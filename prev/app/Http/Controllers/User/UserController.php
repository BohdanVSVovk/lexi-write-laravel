<?php

/**
 * @package UserController
 * @author TechVillage <support@techvill.org>
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @contributor Soumik Datta <[soumik.techvill@gmail.com]>
 * @created 22-11-2021
 */

namespace App\Http\Controllers\User;

use Auth, Hash, DB, Crypt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Services\UserAgentParserService;
use Illuminate\Support\Str;
use App\Services\Mail\{
    UserVerificationMail
};
use Modules\Subscription\Entities\SubscriptionDetails;

class UserController extends Controller
{
    /**
     * User profile
     */
    public function profile(): View
    {
        $id = Auth::guard('user')->user()->id;
        $data['user'] = User::with('avatarFile')->where('id', $id)->first();

        return view('user.profile', $data);
    }

    /**
     * User subscription
     */
    public function subscription(): View
    {
        return view('user.subscription');
    }

    /**
     * Edit user
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(): View
    {
        $data['user'] = User::getAll()->where('id', Auth::user()->id)->first();

        return view('site.user.edit', $data);
    }

    /**
     * Update user
     *
     * @param  Request  $request
     */
    public function update(Request $request): mixed
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];

        if ($request->isMethod('post')) {
            $id = Auth::user()->id;
            $result = $this->checkExistence($id, 'users');

            if ($result['status'] === true) {
                $validator = User::siteUpdateValidation($request->only('name', 'image'), $id);
                if ($validator->fails()) {
                    $response['message'] = $validator->errors()->first();
                    return $response;
                }

                try {

                    DB::beginTransaction();

                    if ((new user)->updateUser($request->only('name', 'image'), $id)) {
                        DB::commit();
                        $response = ['status' => 'success', 'message' => __('Your information has been successfully saved.')];
                    }

                } catch (\Exception $e) {
                    DB::rollBack();
                    $response['message'] = $e->getMessage();
                }

            } else {
                $response['message'] = $result['message'];
            }
        }

        return $response;
    }

    /**
     * Update user password
     *
     * @param  Request  $request
     */
    public function updatePassword(Request $request): array
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $user = Auth::user();

        $data = $this->checkExistence($user->id, 'users', ['getData' => true]);

        if ($data['status'] === false) {
            $response['message'] = $data['message'];

            return $response;
        }

        if (empty($data['data']->sso_service) && !Hash::check($request->old_password, $data['data']->password)) {
            $response['message'] = __('Current password is wrong.');

            return $response;
        }

        $validator = User::siteUpdatePasswordValidation($request->all());

        if ($validator->fails()) {
            $response['message'] = $validator->errors()->first();

            return $response;
        }

        $user->password = Hash::make(trim($request->new_password));

        if ($user->save()) {
            $response = [
                'status' => 'success',
                'message' => __('The :x has been successfully saved.', ['x' => __('Password')])
            ];
        } else {
            $response['message'] = __('Nothing is updated.');
        }

        return $response;
    }

    /**
     * Verify Email
     *
     * @param  Request  $request
     */
    public function verifyEmailByAjax(Request $request): mixed
    {

        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $id = Auth::user()->id;
        $email = Auth::user()->email;

        if ($email != $request->email) {
            return $response;
        }

        try {
            DB::beginTransaction();

            $id = Auth::User()->id;

            $request['activation_code'] = Str::random(10);
            $request['activation_otp'] = random_int(1111, 9999);

            $id = (new User)->updateUser(['activation_code' => $request->activation_code, 'activation_otp' => $request->activation_otp], $id);

            if (!empty($id)) {

                $request['name'] = Auth::user()->name;
                $request['raw_password'] = Auth::user()->password;

                // Send Mail to the customer
                $emailResponse = (new UserVerificationMail)->send($request);

                if ($emailResponse['status'] == false) {
                    DB::rollBack();
                    $response['message'] = $emailResponse['message'];

                    return $response;
                }

                DB::commit();

                $response['status'] = 'success';
                $response['preference'] = preference('email');

                if (preference('email') == 'both' ) {
                    $response['message'] = __('Verification link & code has been sent to your mail.');
                } else if (preference('email') == 'token') {
                    $response['message'] =  __('Verification link has been sent to your mail.');
                } else {
                    $response['message'] = __('Verification code has been sent to your mail.');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response['status'] = 'fail';
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    /**
     * Verify Email By Ajax
     *
     * @param  Request  $request
     */
    public function verifyOtpByAjax(Request $request): mixed
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];

        $otp = (new User)->tokenExist($request->otp);

        if ($otp) {
            User::where('activation_otp', $request->otp)->update(['activation_code' => NULL, 'activation_otp' => NULL, 'email_verified_at' => now()]);
            $response = ['status' => 'success', 'message' => __('Email Verified')];
        } else {
            $response = ['status' => 'fail', 'message' => __('OTP didn\'t match')];
        }

        return $response;
    }

    /**
     * Update Email By Ajax
     *
     * @param  Request  $request
     */
    public function updateEmailByAjax(Request $request): mixed
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $validator = User::userEmailValidation(['email' => $request->new_email]);

        if ($validator->fails()) {
            $response = ['status' => 'fail', 'message' => $validator->errors()->first()];

            return $response;
        }

        try {
            DB::beginTransaction();

            $id = Auth::User()->id;
            $id = (new User)->updateUser(['email' => $request->new_email], $id);

            if (!empty($id)) {
                DB::commit();
                $response = ['status' => 'success', 'message' => __('Email has been updated')];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response['status'] = 'fail';
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    /**
     * Edit Email
     *
     * @param  Request  $request
     */
    public function editEmail(Request $request): View
    {
        $data['id'] = $request->id;

        return view('user.update_email', $data);
    }

    /**
     * Update Email
     *
     * @param  Request  $request
     */
    public function updateEmail(Request $request)
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $validator = User::userEmailValidation(['email' => $request->email]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $id = $request->id;
            $id = (new User)->updateUser(['email' => $request->email], $id);

            if (!empty($id)) {
                DB::commit();
                $response = ['status' => 'success', 'message' => __('Email has been updated')];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response['status'] = 'fail';
            $response['message'] = $e->getMessage();
        }

        $this->setSessionValue($response);
        return redirect()->route('user.profile');
    }

    /**
     * Verification
     * @param int $id
     * @return \Illuminate\Routing\Redirector
     */
    public function verification($code)
    {
        $user = User::where('activation_code', $code)->first();

        if (empty($user)) {
            $this->setSessionValue(['status' => 'fail', 'message' => __('Invalid Request')]);

            return redirect()->route('user.profile');
        }

        User::where('activation_code', $code)->update(['activation_otp' => NULL, 'activation_code' => NULL]);

        return redirect()->route('userEditEmail', ['id' => Crypt::encrypt($user->id)]);
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        if (auth()->user()->role()->slug == 'super-admin') {
            return back()->withFail(__("Admin account can't be deleted."));
        }

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withFail(__('Password does not match'));
        }

        if (User::where('id', \Auth::user()->id)->update(['status' => 'Deleted'])) {
            Auth::guard('user')->logout();
            return redirect()->route('frontend.index')->withSuccess(__('The :x has been successfully deleted.', ['x' => __('Account')]));
        }

        return back()->withFail(__('Failed to delete user, please try again.'));
    }

    /**
     * Delete Image
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeImage()
    {
        if ((new User)->removeProfileImage()) {
            return back()->withSuccess(__('Profile image remove successfully.'));
        }

        return back()->withFail(__('Profile image remove fail.'));
    }

    /**
     * Distinct User Activity
     */
    public function activity(): View
    {
        $idArray = [];
        $lastLogins = \DB::table(config('activitylog.table_name'))
            ->select(\DB::raw('MAX(id) as id'))
            ->where('causer_id', auth()->id())
            ->where('log_name', 'user login')
            ->where('description', 'LIKE', '%successful%')
            ->groupBy(['properties->ip_address', 'properties->browser'])->get();

        foreach ($lastLogins as $lastLogin){
            $idArray[] = $lastLogin->id;
        }

        $userActivities = \DB::table(config('activitylog.table_name'))
            ->select(\DB::raw('id, properties, created_at'))
            ->whereIn('id', $idArray)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('site.user.activity', compact('userActivities'));
    }

    /**
     * Get user subscription history
     *
     * @return View
     */
    public function subscriptionHistory(): View
    {
        $data['payments'] = SubscriptionDetails::with('package', 'user')->where('user_id', auth()->id())->latest()->paginate(preference('row_per_page', 25));
        return view('user.subscription-history', $data);
    }

}
