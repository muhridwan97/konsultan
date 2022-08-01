<?php

if (!function_exists('flash')) {

    /**
     * Translate and replace by placeholder value
     *
     * @param $status
     * @param $message
     * @param null $redirectTo
     * @param string $fallback
     */
    function flash($status, $message, $redirectTo = null, $fallback = 'dashboard')
    {
        $dataStatus = explode('::', $status);
        $dataMessage = explode('::', $message);

        $statusKey = is_array($dataStatus) && count($dataStatus) > 1 ? $dataStatus[0] : 'status';
        $messageKey = is_array($dataMessage) && count($dataMessage) > 1 ? $dataMessage[0] : 'message';
        $status = $dataStatus[1] ?? $status;
        $message = $dataMessage[1] ?? $message;

        // session_start();
        // get_instance()->session->set_flashdata([
        //     $statusKey => $status, $messageKey => $message,
        // ]);
        // session_write_close();

        if (!empty($redirectTo)) {
            // do not support redirect with multiple query params /booking?status=ACTIVE&customer=1
            // use auto redirect instead, keep $redirectTo to null
            if ($redirectTo == '_redirect') {
                $redirect = get_instance()->input->get('redirect');
                redirect(if_empty($redirect, if_empty($fallback, site_url())), false);
            }
            elseif($redirectTo == '_back') {
                redirect(if_empty(get_instance()->agent->referrer(), $fallback), false);
            } else {
                redirect($redirectTo);
            }
        } else {
            $redirect = get_url_param('redirect');
            if (!empty($redirect)) {
                $target = str_replace('redirect=', '', get_if_exist($_SERVER, 'REDIRECT_QUERY_STRING', ''));
                if (!empty($target)) {
                    redirect($target, false);
                }
            }
        }
    }
}
