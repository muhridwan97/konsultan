<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_setting')) {
    /**
     * Helper get single value of settings.
     * @param $key
     * @param $default
     * @return string
     */
    function get_setting($key, $default = '')
    {
        $CI = get_instance();
        $CI->load->model("SettingModel", "setting");
        return $CI->setting->getSetting($key, $default);
    }
}

if (!function_exists('get_setting')) {
    /**
     * Get all system preferences.
     * @return mixed
     */
    function get_settings()
    {
        $CI = get_instance();
        $CI->load->model("SettingModel", "setting");
        return $CI->setting->getAllSettings();
    }
}