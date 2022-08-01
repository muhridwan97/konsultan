<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['app_name'] 	        = env('APP_NAME');
$config['app_author'] 	        = env('APP_AUTHOR');

$config['from_name']            = env('MAIL_FROM_NAME');
$config['from_address']         = env('MAIL_FROM_ADDRESS');
$config['admin_email']          = env('MAIL_ADMIN');

$config['sso_enable']           = env('SSO_ENABLE');

$config['maintenance_mode'] 	= env('MAINTENANCE_MODE');
$config['enable_branch_mode']	= env('BRANCH_MODE');
$config['enable_authorization']	= env('AUTHORIZATION_MODE');
$config['is_demo']	            = env('DEMO_MODE');