<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['pre_controller'][] = [
    'class'    => 'MaintenanceMode',
    'function' => 'checkMaintenanceMode',
    'filename' => 'MaintenanceMode.php',
    'filepath' => 'hooks',
    'params'   => array()
];

$hook['post_controller_constructor'] = [
    [
        'class' => 'MustAuthenticated',
        'function' => 'checkAuth',
        'filename' => 'middleware/MustAuthenticated.php',
        'filepath' => 'hooks',
    ],
    [
        'class'    => 'BranchChecker',
        'function' => 'checkCustomerBranch',
        'filename' => 'middleware/BranchChecker.php',
        'filepath' => 'hooks',
        'params'   => array()
    ],
    [
        'class'    => 'BranchChecker',
        'function' => 'checkCustomerBranchGetUrl',
        'filename' => 'middleware/BranchChecker.php',
        'filepath' => 'hooks',
        'params'   => array()
    ],
    [
        'class' => 'RequestFilter',
        'function' => 'filterRequestMethod',
        'filename' => 'middleware/RequestFilter.php',
        'filepath' => 'hooks',
    ],
    [
        'class' => 'Logging',
        'function' => 'logAccess',
        'filename' => 'middleware/Logging.php',
        'filepath' => 'hooks',
    ]
];