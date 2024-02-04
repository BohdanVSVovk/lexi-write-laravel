<?php

/**
 * @package PackageSubscriptionService
 * @author TechVillage <support@techvill.org>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @created 18-02-2023
 */

 namespace Modules\Subscription\Services;

use App\Models\{
    Currency,
    Preference,
    User
};
use Modules\Gateway\Contracts\RecurringCancelInterface;
use Modules\Gateway\Facades\GatewayHandler;
use Modules\Subscription\Entities\{
    Package, PackageMeta, PackageSubscription, PackageSubscriptionMeta,
    SubscriptionDetails
};
use Modules\Subscription\Traits\SubscriptionTrait;

 class PackageSubscriptionService
 {
    use SubscriptionTrait;

    /**
     * service name
     * @var string
     */
    public string|null $service;

    /**
     * Subscription
     *
     * @var object
     */
    private $subscription;

    /**
     * Initialize
     *
     * @return void
     */
    public function __construct($service = null)
    {
        $this->service = $service;

        if (is_null($service)) {
            $this->service = __('Package Subscription');
        }
    }

    /**
     * Store package from frontend
     *
     * @param int $packageId
     * @param int $userId
     * @return array
     */
    public function storePackage($packageId, $userId)
    {
        $package = Package::find($packageId);
        $days = ['weekly' => 7, 'monthly' => 30, 'yearly' => 365, 'days' => $package->duration];
        $billed = $package->discount_price > 0 ? $package->discount_price : $package->sale_price;
        $next_billing = $days[$package->billing_cycle];

        if ($package->trial_day && !$this->isUsedTrial($package->id)) {
            $next_billing = $package->trial_day;
            $billed = 0;
        }

        $data = [
            "package_id" => $package->id,
            "user_id" => $userId,
            "billing_price" => $billed,
            "billing_cycle" => $package->billing_cycle,
            "meta" => [
                [
                    "duration" => $package->duration,
                    'trial' => $this->isUsedTrial($package->id) ? 0 : $package->trial_day
                ],
            ],
            "activation_date" => timeZoneFormatDate(now()),
            "billing_date" => timeZoneFormatDate(now()),
            "next_billing_date" => timeZoneFormatDate(now()->addDays($next_billing)),
            "amount_billed" => $billed,
            "amount_received" => "0",
            "amount_due" => $billed,
            "is_customized" => "0",
            "renewable" => $package->renewable ?? 0,
            "payment_status" => "Unpaid",
            "status" => "Pending"
        ];

        return $this->store($data);
    }

    /**
     * Store Package Subscription
     *
     * @param array $data
     * @return array
     */
    public function store(array $data): array
    {
        $data = $this->validateData($data);
        $userId = $data['user_id'];
        unset($data['user_id']);

        $this->subscription = $this->getUserSubscription($userId);

        if ($this->subscription && in_array($this->subscription->status, ['Active', 'Cancel'])) {
            $this->storeRemainingCreditInHistory($this->subscription);
        }

        if ($renew = $this->isRenew($data, $userId)) {
            if ($renew === 'nonrenewable') {
                return [
                    'status' => 'fail',
                    'message' => __('The package is not renewable.')
                ];
            }

            return $this->saveSuccessResponse() + ['subscription' => $this->subscription];
        }

        if ($subscription = PackageSubscription::updateOrCreate([
                'user_id' => $userId
            ], $data)) {
                $this->storeMeta($subscription->id, $subscription->package_id, $data['meta']);
                return $this->saveSuccessResponse() + ['subscription' => $subscription];
        }

        return $this->saveFailResponse();
    }

    /**
     * Cancel Subscription
     */
    public function cancel(int $userId): array
    {
        try {
            $subscription = $this->getUserSubscription($userId);
            $history = $subscription->activeDetail();
            $gateway = strtolower($history?->payment_method);
            $subscriptionId = $subscription->{str_replace('recurring', '', $gateway) . '_subscription_id'};
            $customerId = User::find($userId)->{str_replace('recurring', '', $gateway) . '_customer_id'};

            $response['status'] = 'failed';
            if (str_contains($gateway, 'recurring')) {
                $response = $this->cancelRecurring($gateway, $subscriptionId, $customerId);
            }

            if (!str_contains($gateway, 'recurring') || $response['status'] == 'success') {
                $this->getUserSubscription($userId)->update(['status' => 'Cancel', 'renewable' => 0]);

                User::find($userId)->subscriptionDescription()->update(['status' => 'Cancel', 'renewable' => 0]);

                return [
                    'status' => 'success',
                    'message' => __('The :x has been successfully canceled.', ['x' => $this->service])
                ];
            }

            return [
                'status' => 'fail',
                'message' => __(':x cancel failed. Please try again.', ['x' => $this->service])
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'fail',
                'message' => $e->getMessage()
            ];
        }
        
    }

    /**
     * Is renew subscription
     *
     * @param array $data
     * @param int $userId
     *
     * @return bool
     */
    private function isRenew($data, $userId)
    {
        $subscription = PackageSubscription::where(['user_id' => $userId, 'package_id' => $data['package_id']])->first();

        if (!$subscription || boolval($subscription->trial)) {
            return false;
        }

        $package = Package::find($data['package_id']);

        if (!$data['renewable'] || !$package || $package->status <> 'Active') {
            return 'nonrenewable';
        }

        //Update subscription
        $diffDays = $this->diffInDays($data['billing_date'], $data['next_billing_date']);

        if ($this->isActive($subscription->id)) {
            $next_billing = \Carbon\Carbon::createFromFormat('Y-m-d', $subscription->next_billing_date)->addDays($diffDays);
        } else {
            $next_billing = now()->addDays($diffDays);
        }

        $subscription->update([
            'renewable' => $data['renewable'],
            'billing_cycle' => $data['billing_cycle'],
            'status' => $data['status'],
            'amount_received' => $data['amount_received'],
            'amount_due' => $data['amount_due'],
            'payment_status' => $data['payment_status'],
            'billing_price' => $data['billing_price'],
            'next_billing_date' => $next_billing
        ]);

        // Update subscription meta
        foreach ($this->getFeatureList() as $value) {
            $feature = PackageMeta::where(['package_id' => $data['package_id'], 'feature' => $value,])->get();

            if ($feature->where('key', 'is_value_fixed')->first()->value) {
                continue;
            }

            $limit = $feature->where('key', 'value')->first()->value;
            $history = SubscriptionDetails::where('package_subscription_id', $subscription->id)->whereIn('status', ['Active', 'Cancel'])->orderBy('id', 'desc')->first();

            if ($history && $limit != - 1) {
                $limit += json_decode($history->features)->{$value};
            }

            PackageSubscriptionMeta::where([
                'package_subscription_id' => $subscription->id,
                'type' => 'feature_' . $value,
                'key' => 'value'])
            ->update(['value' => $limit]);
        }

        $nonFeature = [
            'trial' => $this->isUsedTrial($subscription->package_id) ? 0 : $subscription?->package?->trial_day,
            'duration' => $subscription?->package?->duration
        ];

        foreach ($nonFeature as $key => $value) {
            PackageSubscriptionMeta::where([
                'package_subscription_id' => $subscription->id,
                'key' => $key])
            ->update(['value' => $value]);
        }

        PackageSubscription::forgetCache('subscriptionResource.' . 'package_subscriptions' . $subscription->id);

        return true;
    }

    /**
     * Store remaining Credit in History
     *
     * @param object $subscription
     * @return void
     */
    private function storeRemainingCreditInHistory($subscription)
    {
        if ($subscription && in_array($subscription->status, ['Active', 'Cancel']) && !$this->isExpired($subscription->user_id)) {
            $details = SubscriptionDetails::where('package_subscription_id', $subscription->id)
                ->whereIn('status', ['Active', 'Cancel'])
                ->orderBy('id', 'desc')
                ->first();
            if ($details) {
                $details->update(['features' => json_encode($this->featuresLimitRemaining($subscription->id))]);
            }
        }
    }

    /**
     * Find difference between two days
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return string
     */
    public function diffInDays($start_date, $end_date)
    {
        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $start_date);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $end_date);

        return $to->diffInDays($from);
    }

    /**
     * Update Package Subscription
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update(array $data, int $id): array
    {
        $subscription = PackageSubscription::find($id);

        $data['billing_price'] = validateNumbers($data['billing_price']);
        $data['amount_billed'] = validateNumbers($data['amount_billed']);
        $data['amount_received'] = validateNumbers($data['amount_received']);
        $data['amount_due'] = validateNumbers($data['amount_due']);

        if (is_null($subscription)) {
            return $this->notFoundResponse();
        }

        $data = $this->validateData($data);

        if ($subscription->update($data)) {
            $this->updateMeta($data['meta'], $id);

            PackageSubscription::forgetCache('subscriptionResource.' . 'package_subscriptions' . $id);

            return $this->saveSuccessResponse();
        }

        return $this->saveFailResponse();
    }

    /**
     * Delete Package Subscription
     *
     * @param int $id
     * @return array
     */
    public function delete(int $id): array
    {
        $subscription = PackageSubscription::find($id);

        if (is_null($subscription)) {
            return $this->notFoundResponse();
        }

        if ($subscription->delete()) {
            return $this->deleteSuccessResponse();
        }

        return $this->deleteFailResponse();
    }

    /**
     * Validate Data
     *
     * @param array $data
     * @return array $data
     */
    private function validateData($data)
    {
        if (!$this->getUserSubscription($data['user_id'])) {
            $data['code'] =  strtoupper(\Str::random(10));
        }
        $data['activation_date'] = DbDateFormat($data['activation_date']);
        $data['billing_date'] = DbDateFormat($data['billing_date']);
        $data['next_billing_date'] = DbDateFormat($data['next_billing_date']);

        return $data;
    }

    /**
     * Store meta data
     *
     * @param array $data
     * @param int $package_id
     * @return void
     */
    private function storeMeta($subscription_id, $package_id, $optional): void
    {
        $package = Package::find($package_id);

        $optional[0]['usecaseCategory'] = $package->usecaseCategory;
        $optional[0]['usecaseTemplate'] = $package->usecaseTemplate;
        $optional[0]['trial'] = $this->isUsedTrial($package->id) ? 0 : $package->trial_day;

        $data = $this->mergeOldCredit(PackageService::editFeature($package, false));

        $mergeData = array_merge($data, $optional);

        $meta = [];
        foreach ($mergeData as $key => $metaData) {
            $feature = null;

            if (!is_int($key)) {
                $feature = 'feature_' . $key;
                $metaData['usage'] = 0;
            }

            foreach ($metaData as $k => $value) {
                $meta[] = [
                    'package_subscription_id' => $subscription_id,
                    'type' => $feature,
                    'key' => $k,
                    'value' => $value
                ];
            }
        }

        PackageSubscriptionMeta::upsert($meta, ['package_subscription_id', 'type', 'key']);
    }

    /**
     * Merge Old Credit
     *
     * @param array $data
     * @return array
     */
    private function mergeOldCredit($data)
    {
        if ($this->subscription && in_array($this->subscription->status, ['Active', 'Cancel']) && !$this->isExpired($this->subscription->user_id)) {
            foreach ($this->featuresLimitRemaining($this->subscription->id) as $key => $value) {
                if (isset($data[$key]['is_value_fixed']) && $data[$key]['is_value_fixed'] === '0') {
                    $data[$key]['value'] += $value;
                }
            }
        } else if ($this->subscription) {
            $history = SubscriptionDetails::where('package_subscription_id', $this->subscription->id)->whereIn('status', ['Active', 'Cancel'])->orderBy('id', 'desc')->first();

            if (!$history) {
                return $data;
            }

            foreach (json_decode($history->features) as $key => $value) {
                if (isset($data[$key]['is_value_fixed']) && $data[$key]['is_value_fixed'] === '0') {
                    $data[$key]['value'] += $value;
                }
            }
        }

        return $data;
    }

    /**
     * Get Subscription Features
     *
     * @param PackageSubscription $subscription
     * @return \App\Lib\MiniCollection;
     */
    public static function getFeatures(PackageSubscription $subscription)
    {
        $meta = $subscription->metadata()->whereBeginsWith('type', 'feature_')->get();

        $formatData = [];

        foreach ($meta as $data) {
            $formatData[str_replace('feature_', '', $data->type)][$data->key] = $data->value;
        }

        return miniCollection($formatData, true);
    }

    /**
     * Update meta data
     *
     * @param array $data
     * @param int $subscription_id
     * @return void
     */
    private function updateMeta($data, $subscription_id): void
    {
        $meta = [];
        foreach ($data as $key => $metaData) {
            foreach ($metaData as $k => $value) {
                $value = !is_array($value) ? $value : json_encode($value);

                $meta[] = [
                    'package_subscription_id' => $subscription_id,
                    'type' => is_int($key) ? null : 'feature_' . $key,
                    'key' => $k,
                    'value' => $value == 0 || !empty($value) ? $value : PackageService::features()[$key][$k]
                ];
            }
        }

        PackageSubscriptionMeta::upsert($meta, ['package_subscription_id', 'type', 'key']);
    }

    /**
     * Check user id
     */
    private function checkUserId(int|null $userId): int|null
    {
        if (!is_null($userId)) {
            return $userId;
        }

        if (auth()->user()->id) {
            return auth()->user()->id;
        }

        return $userId;
    }

    /**
     * Get subscription information
     */
    public function getSubscription(int $id, $type = 'id', bool $newInstance = false): object|null
    {
        return PackageSubscription::getInstance($type, $id, $newInstance);
    }

    /**
     * Get user subscription information
     */
    public function getUserSubscription(int|null $userId = null, bool $newInstance = false): object|null
    {
        $userId = $this->checkUserId($userId);

        return $this->getSubscription($userId, 'user_id', $newInstance);
    }

    /**
     * Get user active subscription
     */
    public function getUserActiveSubscription(int|null $userId = null)
    {
        return $this->getUserSubscription($userId)->where('status', 'Active')->first();
    }

    /**
     * Get All primary feature
     */
    public function getFeatureList(): array
    {
        return array_keys(PackageService::features());
    }

    /**
     * Get subscription feature option
     */
    public function getFeatureOption(int $subscription_id, string $feature): array
    {
        return $this->featureSubscriptionMeta($subscription_id, $feature);
    }

    /**
     * All feature remaining limit
     */
    public function featuresLimitRemaining(int $subscriptionId): array
    {
        $data = [];

        foreach ($this->getFeatureList() as $value) {
            $feature = $this->getFeatureOption($subscriptionId, $value);

            if (!$feature) {
                continue;
            }

            if ($feature['value'] == -1) {
                $data[$value] = -1;

                continue;
            }

            $data[$value] = $feature['value'] - $feature['usage'];
        }

        return $data;
    }

    /**
     * Get Active Feature
     *
     * @return array
     */
    public function getActiveFeature(int $subscriptionId)
    {
        $limit =  $this->featuresLimitRemaining($subscriptionId);
        $usage =  $this->featuresUsage($subscriptionId);
        $data = [];

        foreach ($limit as $key => $value) {
            $data[$key]['limit'] = $value == -1 ? -1 : $value + $usage[$key];
            $data[$key]['used'] = $usage[$key];
            $data[$key]['remain'] = $value;
            $data[$key]['percentage'] = ($value + $usage[$key] > 0) ? (100 - round(($usage[$key] * 100) / ($value + $usage[$key]))) : 0;
        }

        return $data;
    }

     /**
     * Get Default Feature
     *
     * @return array
     */
    public function getDefaultFeature()
    {
        return [

            'word' => [
                'limit' => 0,
                'used' => '0',
                'remain' => 0,
                'percentage' => 0,
            ],

            'image' => [
                'limit' => 0,
                'used' => '0',
                'remain' => 0,
                'percentage' => 0,
            ],

            'image-resolution' => [
                'limit' => 0,
                'used' => '0',
                'remain' => 0,
                'percentage' => 0,
            ]
        ];
    }

    /**
     * Specific feature remaining limit
     */
    public function featureLimitRemaining(int $subscriptionId, string $feature): int
    {
        $feature = $this->getFeatureOption($subscriptionId, $feature);

        if (!$feature) {
            return 0;
        }

        if ($feature['value'] == -1) {
            return -1;
        }

        return $feature['value'] - $feature['usage'];
    }

    /**
     * Specific feature check active status
     */
    public function isFeatureActive(int $subscriptionId, string $feature): bool
    {
        $feature = $this->getFeatureOption($subscriptionId, $feature);

        return $feature && $feature['status'] == 'Active';
    }

    /**
     * Check has feature limit or not
     */
    public function isWordLimitOver(int $subscriptionId, string $feature): bool
    {
        $limit = $this->tokenToWord(preference('max_token_length', 500));

        if ($this->isTrialMode($subscriptionId)) {
            $limit = $this->tokenToWord(preference('max_token_demo', 200));
        }

        return $this->featureLimitRemaining($subscriptionId, $feature) > $limit;
    }

    /**
     * Is The feature limited
     */
    public function isFeatureUnlimited(int $subscriptionId, string $feature): bool
    {
        $feature = $this->getFeatureOption($subscriptionId, $feature);

        if (!$feature) {
            return false;
        }

        if ($feature['value'] == -1) {
            return true;
        }

        return false;
    }

    /**
     * All feature usage
     */
    public function featuresUsage(int $subscriptionId): array
    {
        $data = [];

        foreach ($this->getFeatureList() as $value) {
            $feature = $this->getFeatureOption($subscriptionId, $value);

            if (!$feature) {
                return $data;
            }

            $data[$value] = $feature['usage'];
        }

        return $data;
    }

    /**
     * Specific feature usage
     */
    public function featureUsage(int $subscriptionId, string $feature): int
    {
        $features = $this->featuresUsage($subscriptionId);

        if (!count($features) || !in_array($feature, $features)) {
            return 0;
        }

        return $features[$feature];
    }

    /**
     * Time left to expire
     */
    public function timeLeft(int|null $subscriptionId): string
    {
        $subscription = $this->getSubscription($subscriptionId);

        if ($this->isExpired($subscription->user_id)) {
            return '0 day';
        }

        return timeToGo($subscription->next_billing_date, false, '');
    }

    /**
     * Subscription meta object for usage
     */
    private function featureUsageMeta(int $subscriptionId, string $feature): object|null
    {
        return PackageSubscriptionMeta::where([
                'package_subscription_id' => $subscriptionId,
                'type' => 'feature_'. $feature,
                'key' => 'usage'
            ])->first();
    }

    /**
     * Usage value increment
     */
    public function usageIncrement(int $subscriptionId, string $feature, int $value): bool
    {
        $usage = $this->featureUsageMeta($subscriptionId, $feature);

        return $usage && $usage->increment('value', $value);
    }

    /**
     * Usage value decrement
     */
    public function usageDecrement(int $subscriptionId, string $feature, int $value): bool
    {
        $usage = $this->featureUsageMeta($subscriptionId, $feature);

        return $usage && $usage->decrement('value', $value);
    }

    /**
     * Get all subscription status
     */
    public function getStatuses(): array
    {
        return PackageService::getStatuses();
    }

    /**
     * Get specific subscription current status
     */
    public function getCurrentStatus(int $subscriptionId): null|string
    {
        return $this->getSubscription($subscriptionId)?->status;
    }

    /**
     * Get token to word
     */
    public function tokenToWord($token): float
    {
        return ceil($token * 0.75);
    }

    /**
     * Get word to token
     */
    public function wordToToken($word): float
    {
        return ceil($word / 0.75);
    }

    /**
     * Get trial day
     */
    public function isTrialMode(int $subscriptionId): bool
    {
        return (bool) $this->getSubscription($subscriptionId)->trial;
    }

    /**
     * Is user subscribed any package
     */
    public function isSubscribed(int|null $userId = null): bool
    {
        $userId = $this->checkUserId($userId);

        return !is_null($this->getUserSubscription($userId));
    }

    /**
     * Is Subscription expired
     */
    public function isExpired(int|null $userId = null): bool
    {
        $subscription = $this->getUserSubscription($userId);

        return $subscription->next_billing_date < now() || $subscription->status == 'Expired';
    }

    /**
     * Is subscription active yet
     */
    public function isActive(int $subscriptionId): bool
    {
        $subscription = $this->getSubscription($subscriptionId);

        if ($subscription) {
            return in_array($subscription->status, ['Active', 'Cancel']);
        }

        return false;
    }

    /**
     * Subscription payment status
     */
    public function isPaid(int $subscriptionId): bool
    {
        $subscription = $this->getSubscription($subscriptionId);

        if ($subscription) {
            return $subscription->payment_status == 'Paid';
        }

        return false;
    }

    /**
     * The plan is renewable or not
     */
    public function isRenewable(int $subscriptionId): bool
    {
        $subscription = $this->getSubscription($subscriptionId);

        return boolval($subscription?->renewable);
    }

    /**
     * Check resolution is valid or not
     */
    public function isValidResolution(int|null $userId = null, int $resolution): bool
    {
        $subscription = $this->getUserSubscription($userId);

        if (!$subscription) {
            return false;
        }

        $feature = $this->getFeatureOption($subscription->id, 'image-resolution');

        if (!$feature) {
            return false;
        }

        if ($feature['value'] == -1) {
            return true;
        }

        return $resolution <= $feature['value'];
    }

    /**
     * Check subscription validity
     */
    public function isValidSubscription(int|null $userId = null, string|null $feature = null, string|null $useCase = null): array
    {
        $status = 'fail';
        if (!$this->isSubscribed($userId)) {
            return [
                'status' => $status,
                'message' => __('Please subscribe a plan to proceed.')
            ];
        }

        $subscription = $this->getUserSubscription($userId);

        if (!$this->isActive($subscription->id)) {
            return [
                'status' => $status,
                'message' => __('Your subscription is not active.')
            ];
        }

        if ($this->isExpired($userId)) {
            return [
                'status' => $status,
                'message' => __('Your subscription is expired.')
            ];
        }

        if (!is_null($feature) && !$this->isFeatureActive($subscription->id, $feature)) {
            return [
                'status' => $status,
                'message' => __('The feature is not available in your plan.')
            ];
        }

        if (!is_null($feature) && !$this->isFeatureUnlimited($subscription->id, $feature) && $this->featureLimitRemaining($subscription->id, $feature) <= 0) {
            return [
                'status' => $status,
                'message' => __('You have exceeded your subscription limit.')
            ];
        }

        if (!is_null($useCase) && !in_array($useCase, json_decode($subscription->usecaseTemplate) ?? [])) {
            return [
                'status' => $status,
                'message' => __('The feature is not available in your plan.')
            ];
        }

        return [
            'status' => 'success',
            'message' => __('Subscription valid.')
        ];
    }

    /**
     * generate & store pdf
     *
     * @param object $subscription
     * @param string $invoiceName
     * @return bool|void
     */
    public function invoicePdfEmail($subscription , $invoiceName = 'subscription-invoice.pdf')
    {
        if (empty($subscription)) {
            return false;
        }
        $data['subscription'] = $subscription;
        $data['logo'] = Preference::getAll()->where('field', 'company_logo_light')->first()->fileUrl();

        return printPDF($data, public_path() . '/uploads/invoices/' . $invoiceName, 'subscription::invoice_print', view('subscription::invoice_print', $data), null, "email");

    }

    /**
     * Is used trial period
     *
     * @param int $packageId
     * @param int|null $userId
     * @return bool
     */
    public function isUsedTrial($packageId, $userId = null): bool
    {
        if (is_null($userId)) {
            $userId = auth()->user()->id;
        }

        return SubscriptionDetails::where(['package_id' => $packageId, 'user_id' => $userId])
            ->where('is_trial', '!=', '0')->count();
    }

    /**
     * Activate subscription and details
     *
     * @param int $subscriptionDetailId
     * @return void
     */
    public function activatedSubscription($subscriptionDetailsId)
    {
        $details = SubscriptionDetails::find($subscriptionDetailsId);

        $subscription = PackageSubscription::find($details->package_subscription_id);

        $details->update(['payment_status' => 'Paid', 'status' => 'Active']);
        $subscription->update(['payment_status' => 'Paid', 'status' => 'Active']);
    }

    /** Recurring renew
     *
     * @param object|array $request
     * @return boolean
     */
    public function updateRecurring($request)
    {
        if ($request->type != 'invoice.payment_succeeded') {
            return false;
        }

        $packageSubscriptionMeta = PackageSubscriptionMeta::where(['key' => 'stripe_subscription_id', 'value' => $request->data['object']['subscription']])->first();
        $packageSubscription = PackageSubscription::find($packageSubscriptionMeta?->package_subscription_id);

        if (empty($packageSubscriptionMeta) || empty($packageSubscription)) {
            return false;
        }

        $receiveAmount = $request->data['object']['amount_paid'] / 100;
        $data = $this->prepareRenewData($packageSubscription, $receiveAmount);

        if ($this->renew($data, $packageSubscription->user_id)) {

            if ($this->isRecurringSubscriptionDetailUpdate($packageSubscription->code, $receiveAmount, 'StripeRecurring')) {

                return true;
            } else {

                return (bool) $this->storeSubscriptionDetails($packageSubscription->user_id, 'StripeRecurring');
            }
        }

        return false;
    }

    /**
     * Store subscription details
     *
     * @param integer|null $userId
     * @param string|null $paymentMethod
     * @return object
     */
    public function storeSubscriptionDetails(int|null $userId = null, string|null $paymentMethod = null, $uniqCode = null) : object
    {
        $packageSubscription = $this->getUserSubscription($userId, true);

        $features = $this->getFeatureList();

        $data = $this->prepareData($packageSubscription, $features, $paymentMethod, $uniqCode);

        SubscriptionDetails::where('status', 'Active')->update(['status' => 'Expired']);

        $subscriptionDetails = SubscriptionDetails::create($data);

        return $subscriptionDetails;
    }

    /**
     * Update subscription details
     *
     * @param integer|null $userId
     * @return bool
     */
    public function updateSubscriptionDetails(int|null $userId = null) : bool
    {
        $packageSubscription = $this->getUserSubscription($userId, true);

        $features = $this->getFeatureList();

        $data = $this->prepareData($packageSubscription, $features, null);

        if ($data['status'] == 'Active') {
            SubscriptionDetails::where('status', 'Active')->update(['status' => 'Expired']);
        }

        $subscriptionDetails = SubscriptionDetails::where('user_id', $userId)->orderBy('id', 'desc')->first()->update($data);

        return $subscriptionDetails;
    }

    /**
     * Prepare Subscription data
     *
     * @param array|object $data
     * @param array|object $features
     * @param string|null $paymentMethod
     * @return array
     */
    public function prepareData(array|object $data, array|object $features, string|null $paymentMethod = null, $uniqCode = null): array
    {
        return [
            'package_subscription_id' => $data->id,
            'code' => $data->code,
            'unique_code' => $uniqCode ?? uniqid(rand(), true),
            'user_id' => $data->user_id,
            'package_id' => $data->package_id,
            'is_trial' => boolval($data->trial),
            'renewable' => $data['renewable'],
            'activation_date' => $data->activation_date,
            'billing_date' => $data->billing_date,
            'next_billing_date' => $data->next_billing_date,
            'billing_price' => $data->billing_price,
            'billing_cycle' => $data->billing_cycle,
            'amount_billed' => $data->billing_price,
            'amount_received' => $data->amount_received,
            'currency' => Currency::getDefault()?->name,
            'payment_status' => $data->payment_status,
            'status' => $data->status,
            'features' => json_encode($features),
            'payment_method' => $paymentMethod
        ];
    }

    /**
     * Renew subscription
     *
     * @param array $data
     * @param int $userId
     *
     * @return bool
     */
    private function renew($data, $userId)
    {
        $subscription = PackageSubscription::where(['user_id' => $userId, 'package_id' => $data['package_id']])->first();

        if (!$subscription) {
            return false;
        }

        //Update subscription
        $diffDays = differInDays($data['billing_date'], $data['next_billing_date']);

        if ($this->isActive($subscription->id)) {
            $next_billing = \Carbon\Carbon::createFromFormat('Y-m-d', $subscription->next_billing_date)->addDays($diffDays);
        } else {
            $next_billing = now()->addDays($diffDays);
        }

        $subscription->update([
            'renewable' => $data['renewable'],
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'billing_price' => $data['billing_price'],
            'amount_received' => $data['amount_received'],
            'billing_date' => $data['billing_date'],
            'next_billing_date' => $next_billing
        ]);

        // Update subscription meta
        foreach ($this->getFeatureList() as $value) {
            $feature = PackageMeta::where(['package_id' => $data['package_id'], 'feature' => $value,])->get();

            if ($feature->where('key', 'is_value_fixed')->first()->value) {
                continue;
            }

            $limit = $feature->where('key', 'value')->first()->value;

            PackageSubscriptionMeta::where([
                'package_subscription_id' => $subscription->id,
                'type' => 'feature_' . $value,
                'key' => 'value'
            ])
            ->increment('value', $limit);
        }

        return true;
    }



    /**
     * Paypal recurring renew
     *
     * @param object|array $request
     * @return boolean
     */
    public function updatePaypalRecurring($request)
    {

        if ($request->event_type != 'PAYMENT.SALE.COMPLETED') {
            return false;
        }
        $packageSubscriptionMeta = PackageSubscriptionMeta::where(['key' => 'paypal_subscription_id', 'value' => $request->resource['billing_agreement_id']])->first();
        $packageSubscription = PackageSubscription::find($packageSubscriptionMeta?->package_subscription_id);

        if (empty($packageSubscriptionMeta) || empty($packageSubscription)) {
            return false;
        }

        $receiveAmount = $request->resource['amount']['total'];
        $data = $this->prepareRenewData($packageSubscription, $receiveAmount);

        if (!$this->renew($data, $packageSubscription->user_id)) {
            return false;
        }

        if ($this->isRecurringSubscriptionDetailUpdate($packageSubscription->code, $receiveAmount, 'PaypalRecurring')) {
            return true;
        } else {
            return (bool) $this->storeSubscriptionDetails($packageSubscription->user_id, 'PaypalRecurring');
        }
    }

    /**
     * Is Admin subscribe package
     *
     * @return bool
     */
    public function isAdminSubscribed()
    {
        return auth()->user()->role()->type == 'admin' && !boolval($this->getUserSubscription());
    }

    /**
     * Update subscription details data
     *
     * @param string|integer $subscriptionId
     * @param integer|float|string $amount_received
     * @param null|string $paymentMethod
     * @return boolean
     */
    public function isRecurringSubscriptionDetailUpdate(string|int $subscriptionId, int|float|string $amount_received, string|null $paymentMethod = null) : bool
    {
        $subscriptionDetails = SubscriptionDetails::where(['payment_status' => 'Unpaid', 'billing_date' => DbDateFormat(date('Y-m-d')), 'code' => $subscriptionId])->latest()->first();

        if ($subscriptionDetails) {
            $subscriptionDetails->payment_status = "Paid";
            $subscriptionDetails->status = "Active";
            $subscriptionDetails->amount_received = $amount_received;
            $subscriptionDetails->payment_method = $paymentMethod;
            $subscriptionDetails->save();

            return true;
        }

        return false;
    }

    /**
     * Prepare renew data
     *
     * @param array|object $subscription
     * @param integer|float|string $receiveAmount
     * @return array
     */
    public function prepareRenewData(array|object $subscription, int|float|string $receiveAmount) : array
    {
        $days = ['weekly' => 7, 'monthly' => 30, 'yearly' => 365, 'days' => $subscription->duration];
        return [
            "package_id" => $subscription->package_id,
            "user_id" => $subscription->user_id,
            "billing_price" => $receiveAmount,
            "billing_date" => timeZoneFormatDate(now()),
            "next_billing_date" => timeZoneFormatDate(now()->addDays($days[$subscription->billing_cycle])),
            "amount_billed" => $receiveAmount,
            "amount_received" => $receiveAmount,
            "amount_due" => "0",
            "is_customized" => "0",
            "renewable" => $subscription->renewable,
            "payment_status" => "Paid",
            "status" => "Active"
        ];
    }

    /**
     * Cancel recurring
     */
    public function cancelRecurring(string $gatewayName, string $subscriptionId, string $customId): mixed
    {
        try {
            $processor = GatewayHandler::getRecurringCancelProcessor($gatewayName);

            if (!$processor instanceof RecurringCancelInterface) {
                throw new \Exception(__('This gateway does not support recurring.'));
            }

            return $processor->execute($subscriptionId, $customId);
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }
 }

