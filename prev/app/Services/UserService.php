<?php

/**
 * @package UserService
 * @author TechVillage <support@techvill.org>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @created 06-03-2023
 */

 namespace App\Services;

use App\Models\User;
use App\Services\Mail\UserSetPasswordMailService;
use App\Traits\MessageResponseTrait;

 class UserService
 {
    use MessageResponseTrait;

    /**
     * Service
     */
    public string|null $service;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($service = null)
    {
        $this->service = $service;

        if (is_null($service)) {
            $this->service = __('User');
        }
    }

    /**
     * Update user password
     *
     * @param array $data
     * @return array
     */
    public function updatePassword(array $data, int $id): array
    {
        $user = User::find($id);

        if (!$user) {
            $this->notFoundResponse();
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['raw_password'] = trim($data['password']);
        $data['password'] = \Hash::make(trim($data['password']));

        if (!$user->update($data)) {
            return $this->saveFailResponse();
        }

        if (isset($data['send_mail'])) {
            $user['user_name'] = $user['name'];
            $user['raw_password'] = $data['raw_password'];
            (new UserSetPasswordMailService)->send($user);
        }

        return $this->updateSuccessResponse();
    }

    /**
     * Delete User
     *
     * @param int $id
     * @return array
     */
    public function delete(int $id): array
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFoundResponse();
        }

        $isAdmin = $user->roles()->where('slug', 'super-admin')->first();

        if ($isAdmin) {
            return ['status' => 'fail', 'message' => __("Admin account can't be deleted.")];
        }

        if ($user->delete()) {
            $user->deleteFiles(['thumbnail' => true]);

            return $this->deleteSuccessResponse();
        }

        return $this->deleteFailResponse();
    }
 }
