<?php

/**
 * @package SubscriptionController
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Mostafijur Rahman <mostafijur.techvill@gmail.com>
 * @created 28-03-2023
 */

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\{
    Auth,
    DB
};
use Illuminate\Support\Facades\Session;
use Modules\Gateway\Redirect\GatewayRedirect;
use Modules\Subscription\Services\PackageSubscriptionService;
use Modules\Subscription\Entities\{
    Package,
    SubscriptionDetails
};

class SubscriptionController extends Controller
{
    /**
     * Subscription service
     *
     * @var object
     */
    protected $subscriptionService;

    /**
     * Package Subscription Service
     *
     * @var object
     */
    protected $packageSubscriptionService;

    /**
     * Constructor for Subscription controller
     *
     * @param SubscriptionService $subscriptionService
     * @return void
     */
    public function __construct(SubscriptionService $subscriptionService, PackageSubscriptionService $packageSubscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->packageSubscriptionService = $packageSubscriptionService;
    }

    /**
     * Get packages
     *
     * @param Request $request
     * @return view
     */
    public function package(Request $request)
    {
        $data['activeSubscription'] = $this->packageSubscriptionService->getUserSubscription();

        $data['activeFeatureLimits'] = $this->packageSubscriptionService->getActiveFeature($data['activeSubscription']?->id ?? 1);
        $data['packages'] = Package::orderBy('sort_order')->get();
        $data['activeSubscriptionPackage'] = Package::find($data['activeSubscription']?->package_id);
        $data['activePackage'] = $this->subscriptionService->activePackage();

        if ($data['activePackage']) {
            $data['activePackageDescription'] = $this->subscriptionService->planDescription($data['activePackage']->id);
        }

        if (!isset($request->page) && subscription('getUserSubscription', auth()->user()->id)) {
            return view('user.subscription-details', $data);
        }
        return view('user.subscription-details', $data);
    }

    /**
     * Get plan description
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function planDescription(string $id)
    {
        $data['activeSubscription'] = $this->packageSubscriptionService->getUserSubscription();
        $data['activeSubscriptionPackage'] = Package::find($data['activeSubscription']?->package_id);
        $data['packages'] = Package::orderBy('sort_order')->get();
        $data['activePackage'] = $this->subscriptionService->activePackage();
        $data['activePackageDescription'] = $this->subscriptionService->planDescription($id);

        return view('user.renderable.plans', $data)->render();
    }

    /**
     * Store subscription data
     *
     * @param Request $request
     * @return redirect
     */
    public function storeSubscription(Request $request)
    {
        if ($this->c_p_c()) {
            \Session::flush();
            return view('errors.installer-error', ['message' => __("This product is facing license violation issue.<br>Please contact admin to fix the issue.")]);
        }
        Package::where(['status' => 'Active', 'id' => $request->package_id])->firstOrFail();
        $subscription = subscription('getUserSubscription', auth()->user()->id);
        
        if ($subscription && $subscription->status == 'Active' && str_contains(strtolower($subscription->activeDetail()?->payment_method), 'recurring')) {
            return redirect()->route('user.package')->withErrors(__('Please cancel you current subscription to activate other plan.'));
        }

        if ($subscription) {
            return $this->updateSubscription($request);
        }

        try {
            $paymentType = ['automate' => 'recurring', 'manual' => 'single', 'customer_choice' => 'all'];
            DB::beginTransaction();
            $response = $this->packageSubscriptionService->storePackage($request->package_id, Auth::user()?->id);

            if ($response['status'] != 'success') {
                throw new \Exception(__('Subscription fail.'));
            }
            $packageSubscriptionDetails = $this->packageSubscriptionService->storeSubscriptionDetails();

            if ($packageSubscriptionDetails->is_trial || $packageSubscriptionDetails->billing_price == 0) {
                $this->packageSubscriptionService->activatedSubscription($packageSubscriptionDetails->id);
                DB::commit();

                return redirect()->route('user.package')->withSuccess($response['message']);
            }

            request()->query->add(['payer' => 'user', 'to' => techEncrypt('subscription-paid')]);

            $route = GatewayRedirect::paymentRoute($packageSubscriptionDetails, $packageSubscriptionDetails->amount_billed, $packageSubscriptionDetails->currency, $packageSubscriptionDetails->unique_code, $request, $paymentType[preference('subscription_renewal')]);

            DB::commit();
            return redirect($route);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.package')->withErrors($response['message'] ?? $e->getMessage());
        }
    }

    /**
     * Cancel Subscription
     *
     * @param $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription($user_id)
    {
        $response = (new PackageSubscriptionService)->cancel($user_id);
        $this->setSessionValue($response);

        return back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscriptionPaid(Request $request)
    {
        if (!checkRequestIntegrity()) {
            return redirect(GatewayRedirect::failedRedirect('integrity'));
        }

        try {

            $response =  $this->subscriptionService->subscriptionPaid($request);
            Session::flash('success', __('You have successfully subscribed to your desired plan.'));
            return redirect()->route('user.package');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->route('user.package');
        }
    }

    /**
     * Show billing details
     *
     * @param string|integer $id
     * @return view
     */
    public function billDetails(string|int $id)
    {
        $data['subscription'] = SubscriptionDetails::find($id);

        return view('user.bill-details', $data);
    }

    /**
     * bill pdf
     *
     * @param string|integer $id
     * @return pdf
     */
    public function billPdf(string|int $id)
    {
        $data['subscription'] = SubscriptionDetails::find($id);
        return printPDF($data, 'invoice' . time() . '.pdf', 'user.invoice-print', view('user.invoice-print', $data), 'pdf');
    }

    /**
     * pay for pending subscription
     *
     * @param Request $request
     * @return \Illuminate\Routing\Redirector
     */
    public function payPendingSubscription(Request $request)
    {
        try {
            $paymentType = ['automate' => 'recurring', 'manual' => 'single', 'customer_choice' => 'all'];
            DB::beginTransaction();

            $subscriptionDetails = SubscriptionDetails::where('id', $request->id)->first();
            $subscriptionDetails->update(['unique_code' => uniqid(rand(), true)]);
            $subscriptionDetails = $subscriptionDetails->refresh();
            
            if ($subscriptionDetails->status == 'Active' && str_contains(strtolower($subscriptionDetails->payment_method), 'recurring')) {
                return redirect()->route('user.package')->withErrors(__('Please cancel you current subscription to activate other plan.'));
            }

            $package = Package::find($subscriptionDetails->package_id);

            if (!$package) {
                return redirect()->route('user.package')->withErrors(__('The package is not available.'));
            }

            $price = $package->discount_price > 0 ? $package->discount_price : $package->sale_price;

            request()->query->add(['payer' => 'user', 'to' => techEncrypt('subscription-pending-payment-response')]);

            $route = GatewayRedirect::paymentRoute($subscriptionDetails, $price, $subscriptionDetails->currency, $subscriptionDetails->unique_code, $request, $paymentType[preference('subscription_renewal')]);

            DB::commit();
            return redirect($route);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.package')->withErrors($e->getMessage());
        }
    }

    /**
     * Subscription pending payment response
     *
     * @param Request $request
     */
    public function subscriptionPendingPaymentResponse(Request $request)
    {
        if (!checkRequestIntegrity()) {
            return redirect(GatewayRedirect::failedRedirect('integrity'));
        }

        try {

            $this->subscriptionService->paidPendingSubscription($request);

            return redirect()->route('user.package')->withSuccess(__('Your subscription is update.'));
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->route('user.package');
        }
    }

    /**
     * Update subscription data
     *
     * @param Request $request
     * @return redirect
     */
    public function updateSubscription(Request $request)
    {
        try {
            $paymentType = ['automate' => 'recurring', 'manual' => 'single', 'customer_choice' => 'all'];
            DB::beginTransaction();

            $subscription = subscription('getUserSubscription', auth()->user()->id);
            $package = Package::find($request->package_id);
            $usedTrial = SubscriptionDetails::where(['package_subscription_id' => $subscription->id, 'is_trial' => 1, 'package_id' => $package->id])->first();

            if (($package->trial_day && !$usedTrial) || $package->sale_price == 0) {
                $response = $this->packageSubscriptionService->storePackage($request->package_id, auth()->user()->id);

                if ($response['status'] != 'success') {
                    throw new \Exception(__('Subscription fail.'));
                }

                $packageSubscriptionDetails = $this->packageSubscriptionService->storeSubscriptionDetails();
                $this->packageSubscriptionService->activatedSubscription($packageSubscriptionDetails->id);
                DB::commit();

                return redirect()->route('user.package')->withSuccess($response['message']);
            }

            session(['package_id' => $package->id]);
            $price = $package->discount_price > 0 ? $package->discount_price : $package->sale_price;
            $currency = Currency::find(preference('dflt_currency_id'))->name;

            request()->query->add(['payer' => 'user', 'to' => techEncrypt('subscription-update-paid')]);

            $route = GatewayRedirect::paymentRoute(['package_id' => $request->package_id, 'code' => $subscription->code, 'user_id' => $subscription->user_id], $price, $currency, uniqid(rand(), true), $request, $paymentType[preference('subscription_renewal')]);

            DB::commit();
            return redirect($route);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.package')->withErrors($response['message'] ?? $e->getMessage());
        }
    }

    /**
     * Subscription update paid
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscriptionUpdatePaid(Request $request)
    {
        if (!checkRequestIntegrity()) {
            return redirect(GatewayRedirect::failedRedirect('integrity'));
        }

        try {
            $request['package_id'] = session('package_id');
            $this->subscriptionService->subscriptionUpdatePaid($request);

            Session::flash('success', __('You have successfully subscribed to your desired plan.'));
            return redirect()->route('user.package');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->route('user.package');
        }
    }

    /**
     * Check Verification
     *
     * @return bool
     */
    public function c_p_c()
    {
        if (!g_e_v()) {
            return true;
        }
        if (!g_c_v()) {
            try {
                $d_ = g_d();
                $e_ = g_e_v();
                $e_ = explode('.', $e_);
                $c_ = md5($d_ . $e_[1]);
                if ($e_[0] == $c_) {
                    p_c_v();
                    return false;
                }
                return true;
            } catch (\Exception $e) {
                return true;
            }
        }
        return false;
    }   
}
