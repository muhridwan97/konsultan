<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_notifications')) {
    /**
     * Helper get single value of settings.
     * @return array
     */
    function get_notifications()
    {
        $CI = get_instance();
        $CI->load->model("UploadModel", "uploadNotification");
        $loggedUser = UserModel::authenticatedUserData('id_person');
        return $CI->uploadNotification->getUploadsByUser($loggedUser, null, false, false);
    }
}