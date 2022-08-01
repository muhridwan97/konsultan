<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_customer_branch')) {
    /**
     * Get list of customer branches.
     *
     * @return mixed
     */
    function get_customer_branch()
    {
        $CI = get_instance();
        $CI->load->model("BranchModel", "branch");

        $customerId = UserModel::authenticatedUserData('id_person');
        return $CI->branch->getByCustomer($customerId);
    }
}

if (!function_exists('get_user_branch')) {
    /**
     * Get list of user branches.
     *
     * @return mixed
     */
    function get_user_branch()
    {
        $CI = get_instance();
        $CI->load->model("BranchModel", "branch");

        $userId = UserModel::authenticatedUserData('id');
        return $CI->branch->getByUser($userId);
    }
}

if (!function_exists('get_active_branch')) {
    /**
     * Get branch data.
     *
     * @param null $key
     * @param bool $redirectIfNotFound
     * @param string $redirectPath
     * @return string | array
     */
    function get_active_branch($key = null, $redirectIfNotFound = false, $redirectPath = 'gateway')
    {
        if($key == 'id') {
            return get_active_branch_id();
        }

        $CI = get_instance();
        $CI->load->model("BranchModel", "branch");

        $branchId = $CI->uri->segment(2);
        $branch = $CI->branch->getById($branchId);

        if ($redirectIfNotFound) {
            if (empty($branch)) {
                redirect($redirectPath, false);
            }
        }

        if (!empty($key) && !empty($branch)) {
            if (key_exists($key, $branch)) {
                return $branch[$key];
            }
            return '';
        }

        return $CI->branch->getById($branchId);
    }
}

if (!function_exists('get_active_branch_id')) {
    /**
     * Get branch id by checking is branch active and segment url.
     */
    function get_active_branch_id()
    {
        $CI = get_instance();

        if ($CI->config->item('enable_branch_mode') && $CI->uri->segment(1) == 'p') {
            $branchId = $CI->uri->segment(2);
            if (is_numeric($branchId)) {
                return $branchId;
            }
        }

        return null;
    }
}