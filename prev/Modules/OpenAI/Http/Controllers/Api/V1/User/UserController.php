<?php

namespace Modules\OpenAI\Http\Controllers\Api\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Subscription\Services\PackageSubscriptionService;
use App\Models\{
    User
};
use Db;

class UserController extends Controller
{

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return [type]
     */
    public function update(Request $request)
    {
        $id = auth()->guard('api')->user()->id;
        $response = $this->checkExistence($id, 'users');

        if ($response['status'] === true) {

            $validator = User::siteUpdateValidation($request->only('name', 'image'), $id);

            if ($validator->fails()) {
                return $this->unprocessableResponse($validator->messages());
            }

            try {
                DB::beginTransaction();

                if ((new User)->updateUser($request->only('name', 'image'), $id)) {
                    DB::commit();
                    return $this->okResponse([], __('The :x has been successfully saved.', ['x' => __('User Info')]));
                } else {
                    return $this->okResponse([], __('No changes found.'));
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->errorResponse([], 500,  $e->getMessage());
            }
        }

        return $this->response([], 204, $response['message']);
    }

    /**
     * Return subscription's feature limits.
     *
     * @param PackageSubscriptionService $packageSubscriptionService
     * @return array
     */
    public function index(PackageSubscriptionService $packageSubscriptionService)
    {
        if (subscription('getUserSubscription', auth()->guard('api')->user()->id)) {
            
            $activeSubscription = $packageSubscriptionService->getUserSubscription();
            $activeFeatureLimits = $packageSubscriptionService->getActiveFeature($activeSubscription->id);

            return $this->response($activeFeatureLimits);
            
        } 

        $activeFeatureLimits =  $packageSubscriptionService->getDefaultFeature();
        return $this->response($activeFeatureLimits , 202, __('You don\'t have any subscription. Please subscribe a plan.'));
        
    }

}
