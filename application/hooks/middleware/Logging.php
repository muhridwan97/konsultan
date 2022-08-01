<?php
/**
 * Log history.
 */

class Logging
{
    /**
     * Capture request and put into log access.
     */
    public function logAccess()
    {
        $CI = get_instance();       
        if (UserModel::isLoggedIn() && !$CI->input->is_ajax_request() && strtolower(get_class($CI)) != 'logs') {
            $CI->load->library('user_agent');

            $userData = UserModel::authenticatedUserData();
            $branch = get_active_branch();

            if(!empty($branch) & !is_null($branch)){
                $CI->db->insert('logs', [
                    'id_branch' => $branch['id'],
                    'type' => str_replace(['-', '_'], ' ', strtoupper(get_class($CI))),
                    'data' => json_encode([
                        'host' => site_url('/', false),
                        'path' => uri_string(),
                        'query' => $_SERVER['QUERY_STRING'],
                        'ip' => $CI->input->ip_address(),
                        'platform' => $CI->agent->platform(),
                        'browser' => $CI->agent->browser(),
                        'is_mobile' => $CI->agent->is_mobile(),
                        'user_id' => $userData['id'],
                        'name' => $userData['name'],
                        'username' => $userData['username'],
                        'access' => $branch['branch'],
                    ]),
                    'created_by' => UserModel::authenticatedUserData('id'),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}