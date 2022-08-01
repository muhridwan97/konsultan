<?php
/**
 * Created by PhpStorm.
 * User: angga
 * Date: 05/09/17
 * Time: 9:52
 */

class BranchChecker
{
    /**
     * Check if customer try to access branch that not belong to them.
     */
    public function checkCustomerBranch()
    {
        $CI = get_instance();

        if(in_array(get_class($CI), ['Migrate', 'Automate'])) return;

        if (UserModel::isLoggedIn() && $CI->config->item('enable_branch_mode')) {
            $branches = get_customer_branch();
            $segment1 = $CI->uri->segment(1);
            $segment2 = $CI->uri->segment(2);
            if ($segment1 == 'p') {
                $hasBranch = false;
                foreach ($branches as $branch) {
                    if ($branch['id'] == $segment2) {
                        $hasBranch = true;
                    }
                }
                if (!$hasBranch) {
                    redirect('gateway', false);
                }
            }
        }
    }

    /**
     * Check if user try to remove branch id in url (except in whitelist controller).
     * Whitelist controllers are controllers that accessible without prefix branch (/p/1/controller)
     */
    public function checkCustomerBranchGetUrl()
    {
        $CI = get_instance();
        if (UserModel::isLoggedIn() && $CI->config->item('enable_branch_mode')) {
            $segment1 = $CI->uri->segment(1);
            if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                // user allow access /setting rather than /p/1/setting (except branch's settings)
                $whitelistControllers = [
                    'migrate', 'automate', 'gateway', 'file_manager', 'account', 'setting', 'cycle-count', 'opname',
                    'module_explorer', 'calculator', 'tracker', 'backup', 'help', 'client_area', 'response', 'api',
                    'booking-rating-public', 'payment-check', 'payment', 'webhook', 'system-log', 'discrepancy-handover-confirmation'
                    // masters controller if you like
                ];
                if ($segment1 != 'p' && !in_array($segment1, $whitelistControllers)) {
                    flash('danger', 'Please select specific branch!');
                    redirect('gateway', false);
                }
            }
        }
    }
}
