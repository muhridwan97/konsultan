<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


// Account setting permission
defined('PERMISSION_ACCOUNT_EDIT') OR define('PERMISSION_ACCOUNT_EDIT', 'account-edit');
defined('PERMISSION_SETTING_EDIT') OR define('PERMISSION_SETTING_EDIT', 'setting-edit');
defined('PERMISSION_REPORT_SCHEDULE_VIEW') OR define('PERMISSION_REPORT_SCHEDULE_VIEW', 'report-schedule-view');
defined('PERMISSION_REPORT_SCHEDULE_EDIT') OR define('PERMISSION_REPORT_SCHEDULE_EDIT', 'report-schedule-edit');

// Utility setting permission
defined('PERMISSION_WHATSAPP_DIALOG') OR define('PERMISSION_WHATSAPP_DIALOG', 'whatsapp-dialog');

// User privileges permission
defined('PERMISSION_PERMISSION_VIEW') OR define('PERMISSION_PERMISSION_VIEW', 'permission-view');
defined('PERMISSION_PERMISSION_CREATE') OR define('PERMISSION_PERMISSION_CREATE', 'permission-create');
defined('PERMISSION_PERMISSION_EDIT') OR define('PERMISSION_PERMISSION_EDIT', 'permission-edit');
defined('PERMISSION_PERMISSION_DELETE') OR define('PERMISSION_PERMISSION_DELETE', 'permission-delete');
defined('PERMISSION_PERMISSION_PRINT') OR define('PERMISSION_PERMISSION_PRINT', 'permission-print');

defined('PERMISSION_ROLE_VIEW') OR define('PERMISSION_ROLE_VIEW', 'role-view');
defined('PERMISSION_ROLE_CREATE') OR define('PERMISSION_ROLE_CREATE', 'role-create');
defined('PERMISSION_ROLE_EDIT') OR define('PERMISSION_ROLE_EDIT', 'role-edit');
defined('PERMISSION_ROLE_DELETE') OR define('PERMISSION_ROLE_DELETE', 'role-delete');
defined('PERMISSION_ROLE_PRINT') OR define('PERMISSION_ROLE_PRINT', 'role-print');

defined('PERMISSION_USER_VIEW') OR define('PERMISSION_USER_VIEW', 'user-view');
defined('PERMISSION_USER_CREATE') OR define('PERMISSION_USER_CREATE', 'user-create');
defined('PERMISSION_USER_EDIT') OR define('PERMISSION_USER_EDIT', 'user-edit');
defined('PERMISSION_USER_DELETE') OR define('PERMISSION_USER_DELETE', 'user-delete');
defined('PERMISSION_USER_PRINT') OR define('PERMISSION_USER_PRINT', 'user-print');

// Master data permission
defined('PERMISSION_BRANCH_VIEW') OR define('PERMISSION_BRANCH_VIEW', 'branch-view');
defined('PERMISSION_BRANCH_CREATE') OR define('PERMISSION_BRANCH_CREATE', 'branch-create');
defined('PERMISSION_BRANCH_EDIT') OR define('PERMISSION_BRANCH_EDIT', 'branch-edit');
defined('PERMISSION_BRANCH_DELETE') OR define('PERMISSION_BRANCH_DELETE', 'branch-delete');
defined('PERMISSION_BRANCH_PRINT') OR define('PERMISSION_BRANCH_PRINT', 'branch-print');

defined('PERMISSION_WAREHOUSE_VIEW') OR define('PERMISSION_WAREHOUSE_VIEW', 'warehouse-view');
defined('PERMISSION_WAREHOUSE_CREATE') OR define('PERMISSION_WAREHOUSE_CREATE', 'warehouse-create');
defined('PERMISSION_WAREHOUSE_EDIT') OR define('PERMISSION_WAREHOUSE_EDIT', 'warehouse-edit');
defined('PERMISSION_WAREHOUSE_DELETE') OR define('PERMISSION_WAREHOUSE_DELETE', 'warehouse-delete');
defined('PERMISSION_WAREHOUSE_PRINT') OR define('PERMISSION_WAREHOUSE_PRINT', 'warehouse-print');

defined('PERMISSION_CONTAINER_VIEW') OR define('PERMISSION_CONTAINER_VIEW', 'container-view');
defined('PERMISSION_CONTAINER_CREATE') OR define('PERMISSION_CONTAINER_CREATE', 'container-create');
defined('PERMISSION_CONTAINER_EDIT') OR define('PERMISSION_CONTAINER_EDIT', 'container-edit');
defined('PERMISSION_CONTAINER_DELETE') OR define('PERMISSION_CONTAINER_DELETE', 'container-delete');
defined('PERMISSION_CONTAINER_PRINT') OR define('PERMISSION_CONTAINER_PRINT', 'container-print');

defined('PERMISSION_COMPLAIN_CATEGORY_VIEW') OR define('PERMISSION_COMPLAIN_CATEGORY_VIEW', 'complain-category-view');
defined('PERMISSION_COMPLAIN_CATEGORY_CREATE') OR define('PERMISSION_COMPLAIN_CATEGORY_CREATE', 'complain-category-create');
defined('PERMISSION_COMPLAIN_CATEGORY_EDIT') OR define('PERMISSION_COMPLAIN_CATEGORY_EDIT', 'complain-category-edit');
defined('PERMISSION_COMPLAIN_CATEGORY_DELETE') OR define('PERMISSION_COMPLAIN_CATEGORY_DELETE', 'complain-category-delete');
defined('PERMISSION_COMPLAIN_CATEGORY_PRINT') OR define('PERMISSION_COMPLAIN_CATEGORY_PRINT', 'complain-category-print');

defined('PERMISSION_COMPLAIN_VIEW') OR define('PERMISSION_COMPLAIN_VIEW', 'complain-view');
defined('PERMISSION_COMPLAIN_CREATE') OR define('PERMISSION_COMPLAIN_CREATE', 'complain-create');
defined('PERMISSION_COMPLAIN_EDIT') OR define('PERMISSION_COMPLAIN_EDIT', 'complain-edit');
defined('PERMISSION_COMPLAIN_DELETE') OR define('PERMISSION_COMPLAIN_DELETE', 'complain-delete');
defined('PERMISSION_COMPLAIN_PRINT') OR define('PERMISSION_COMPLAIN_PRINT', 'complain-print');
defined('PERMISSION_COMPLAIN_VALIDATE') OR define('PERMISSION_COMPLAIN_VALIDATE', 'complain-validate');
defined('PERMISSION_COMPLAIN_RESULT') OR define('PERMISSION_COMPLAIN_RESULT', 'complain-result');
defined('PERMISSION_COMPLAIN_UPLOAD') OR define('PERMISSION_COMPLAIN_UPLOAD', 'complain-upload');

defined('PERMISSION_COMPLAIN_INVESTIGATION_VIEW') OR define('PERMISSION_COMPLAIN_INVESTIGATION_VIEW', 'complain-investigation-view');
defined('PERMISSION_COMPLAIN_INVESTIGATION_CREATE') OR define('PERMISSION_COMPLAIN_INVESTIGATION_CREATE', 'complain-investigation-create');
defined('PERMISSION_COMPLAIN_INVESTIGATION_EDIT') OR define('PERMISSION_COMPLAIN_INVESTIGATION_EDIT', 'complain-investigation-edit');
defined('PERMISSION_COMPLAIN_INVESTIGATION_DELETE') OR define('PERMISSION_COMPLAIN_INVESTIGATION_DELETE', 'complain-investigation-delete');
defined('PERMISSION_COMPLAIN_INVESTIGATION_PRINT') OR define('PERMISSION_COMPLAIN_INVESTIGATION_PRINT', 'complain-investigation-print');
defined('PERMISSION_COMPLAIN_INVESTIGATION_ADMIN') OR define('PERMISSION_COMPLAIN_INVESTIGATION_ADMIN', 'complain-investigation-admin');

defined('PERMISSION_GOODS_VIEW') OR define('PERMISSION_GOODS_VIEW', 'goods-view');
defined('PERMISSION_GOODS_CREATE') OR define('PERMISSION_GOODS_CREATE', 'goods-create');
defined('PERMISSION_GOODS_EDIT') OR define('PERMISSION_GOODS_EDIT', 'goods-edit');
defined('PERMISSION_GOODS_DELETE') OR define('PERMISSION_GOODS_DELETE', 'goods-delete');
defined('PERMISSION_GOODS_PRINT') OR define('PERMISSION_GOODS_PRINT', 'goods-print');

defined('PERMISSION_ASSEMBLY_VIEW') OR define('PERMISSION_ASSEMBLY_VIEW', 'assembly-view');
defined('PERMISSION_ASSEMBLY_CREATE') OR define('PERMISSION_ASSEMBLY_CREATE', 'assembly-create');
defined('PERMISSION_ASSEMBLY_EDIT') OR define('PERMISSION_ASSEMBLY_EDIT', 'assembly-edit');
defined('PERMISSION_ASSEMBLY_DELETE') OR define('PERMISSION_ASSEMBLY_DELETE', 'assembly-delete');
defined('PERMISSION_ASSEMBLY_PRINT') OR define('PERMISSION_ASSEMBLY_PRINT', 'assembly-print');

defined('PERMISSION_ASSEMBLY_GOODS_VIEW') OR define('PERMISSION_ASSEMBLY_GOODS_VIEW', 'assembly-goods-view');
defined('PERMISSION_ASSEMBLY_GOODS_CREATE') OR define('PERMISSION_ASSEMBLY_GOODS_CREATE', 'assembly-goods-create');
defined('PERMISSION_ASSEMBLY_GOODS_EDIT') OR define('PERMISSION_ASSEMBLY_GOODS_EDIT', 'assembly-goods-edit');
defined('PERMISSION_ASSEMBLY_GOODS_DELETE') OR define('PERMISSION_ASSEMBLY_GOODS_DELETE', 'assembly-goods-delete');
defined('PERMISSION_ASSEMBLY_GOODS_PRINT') OR define('PERMISSION_ASSEMBLY_GOODS_PRINT', 'assembly-goods-print');

defined('PERMISSION_VEHICLE_VIEW') OR define('PERMISSION_VEHICLE_VIEW', 'vehicle-view');
defined('PERMISSION_VEHICLE_CREATE') OR define('PERMISSION_VEHICLE_CREATE', 'vehicle-create');
defined('PERMISSION_VEHICLE_EDIT') OR define('PERMISSION_VEHICLE_EDIT', 'vehicle-edit');
defined('PERMISSION_VEHICLE_DELETE') OR define('PERMISSION_VEHICLE_DELETE', 'vehicle-delete');
defined('PERMISSION_VEHICLE_PRINT') OR define('PERMISSION_VEHICLE_PRINT', 'vehicle-print');

defined('PERMISSION_ESEAL_VIEW') OR define('PERMISSION_ESEAL_VIEW', 'eseal-view');
defined('PERMISSION_ESEAL_CREATE') OR define('PERMISSION_ESEAL_CREATE', 'eseal-create');
defined('PERMISSION_ESEAL_EDIT') OR define('PERMISSION_ESEAL_EDIT', 'eseal-edit');
defined('PERMISSION_ESEAL_DELETE') OR define('PERMISSION_ESEAL_DELETE', 'eseal-delete');
defined('PERMISSION_ESEAL_PRINT') OR define('PERMISSION_ESEAL_PRINT', 'eseal-print');
defined('PERMISSION_ESEAL_VALIDATE') OR define('PERMISSION_ESEAL_VALIDATE', 'eseal-validate');

defined('PERMISSION_PEOPLE_VIEW') OR define('PERMISSION_PEOPLE_VIEW', 'people-view');
defined('PERMISSION_PEOPLE_CREATE') OR define('PERMISSION_PEOPLE_CREATE', 'people-create');
defined('PERMISSION_PEOPLE_EDIT') OR define('PERMISSION_PEOPLE_EDIT', 'people-edit');
defined('PERMISSION_PEOPLE_DELETE') OR define('PERMISSION_PEOPLE_DELETE', 'people-delete');
defined('PERMISSION_PEOPLE_PRINT') OR define('PERMISSION_PEOPLE_PRINT', 'people-print');
defined('PERMISSION_PEOPLE_EDIT_NOTIFICATION') OR define('PERMISSION_PEOPLE_EDIT_NOTIFICATION', 'people-edit-notification');

defined('PERMISSION_CUSTOMER_VIEW') OR define('PERMISSION_CUSTOMER_VIEW', 'customer-view');
defined('PERMISSION_CUSTOMER_CREATE') OR define('PERMISSION_CUSTOMER_CREATE', 'customer-create');
defined('PERMISSION_CUSTOMER_EDIT') OR define('PERMISSION_CUSTOMER_EDIT', 'customer-edit');
defined('PERMISSION_CUSTOMER_DELETE') OR define('PERMISSION_CUSTOMER_DELETE', 'customer-delete');
defined('PERMISSION_CUSTOMER_PRINT') OR define('PERMISSION_CUSTOMER_PRINT', 'customer-print');

defined('PERMISSION_OPERATION_CUT_OFF_VIEW') OR define('PERMISSION_OPERATION_CUT_OFF_VIEW', 'operation-cut-off-view');
defined('PERMISSION_OPERATION_CUT_OFF_CREATE') OR define('PERMISSION_OPERATION_CUT_OFF_CREATE', 'operation-cut-off-create');
defined('PERMISSION_OPERATION_CUT_OFF_EDIT') OR define('PERMISSION_OPERATION_CUT_OFF_EDIT', 'operation-cut-off-edit');
defined('PERMISSION_OPERATION_CUT_OFF_DELETE') OR define('PERMISSION_OPERATION_CUT_OFF_DELETE', 'operation-cut-off-delete');

defined('PERMISSION_CUSTOMER_STORAGE_CAPACITY_VIEW') OR define('PERMISSION_CUSTOMER_STORAGE_CAPACITY_VIEW', 'customer-storage-capacity-view');
defined('PERMISSION_CUSTOMER_STORAGE_CAPACITY_CREATE') OR define('PERMISSION_CUSTOMER_STORAGE_CAPACITY_CREATE', 'customer-storage-capacity-create');
defined('PERMISSION_CUSTOMER_STORAGE_CAPACITY_EDIT') OR define('PERMISSION_CUSTOMER_STORAGE_CAPACITY_EDIT', 'customer-storage-capacity-edit');
defined('PERMISSION_CUSTOMER_STORAGE_CAPACITY_DELETE') OR define('PERMISSION_CUSTOMER_STORAGE_CAPACITY_DELETE', 'customer-storage-capacity-delete');

defined('PERMISSION_STORAGE_USAGE_VIEW') OR define('PERMISSION_STORAGE_USAGE_VIEW', 'storage-usage-view');
defined('PERMISSION_STORAGE_USAGE_CREATE') OR define('PERMISSION_STORAGE_USAGE_CREATE', 'storage-usage-create');
defined('PERMISSION_STORAGE_USAGE_EDIT') OR define('PERMISSION_STORAGE_USAGE_EDIT', 'storage-usage-edit');
defined('PERMISSION_STORAGE_USAGE_DELETE') OR define('PERMISSION_STORAGE_USAGE_DELETE', 'storage-usage-delete');
defined('PERMISSION_STORAGE_USAGE_VALIDATE') OR define('PERMISSION_STORAGE_USAGE_VALIDATE', 'storage-usage-validate');

defined('PERMISSION_SUPPLIER_VIEW') OR define('PERMISSION_SUPPLIER_VIEW', 'supplier-view');
defined('PERMISSION_SUPPLIER_CREATE') OR define('PERMISSION_SUPPLIER_CREATE', 'supplier-create');
defined('PERMISSION_SUPPLIER_EDIT') OR define('PERMISSION_SUPPLIER_EDIT', 'supplier-edit');
defined('PERMISSION_SUPPLIER_DELETE') OR define('PERMISSION_SUPPLIER_DELETE', 'supplier-delete');
defined('PERMISSION_SUPPLIER_PRINT') OR define('PERMISSION_SUPPLIER_PRINT', 'supplier-print');

defined('PERMISSION_POSITION_TYPE_VIEW') OR define('PERMISSION_POSITION_TYPE_VIEW', 'position-type-view');
defined('PERMISSION_POSITION_TYPE_CREATE') OR define('PERMISSION_POSITION_TYPE_CREATE', 'position-type-create');
defined('PERMISSION_POSITION_TYPE_EDIT') OR define('PERMISSION_POSITION_TYPE_EDIT', 'position-type-edit');
defined('PERMISSION_POSITION_TYPE_DELETE') OR define('PERMISSION_POSITION_TYPE_DELETE', 'position-type-delete');
defined('PERMISSION_POSITION_TYPE_PRINT') OR define('PERMISSION_POSITION_TYPE_PRINT', 'position-type-print');

defined('PERMISSION_POSITION_VIEW') OR define('PERMISSION_POSITION_VIEW', 'position-view');
defined('PERMISSION_POSITION_CREATE') OR define('PERMISSION_POSITION_CREATE', 'position-create');
defined('PERMISSION_POSITION_EDIT') OR define('PERMISSION_POSITION_EDIT', 'position-edit');
defined('PERMISSION_POSITION_DELETE') OR define('PERMISSION_POSITION_DELETE', 'position-delete');
defined('PERMISSION_POSITION_PRINT') OR define('PERMISSION_POSITION_PRINT', 'position-print');

defined('PERMISSION_UNIT_VIEW') OR define('PERMISSION_UNIT_VIEW', 'unit-view');
defined('PERMISSION_UNIT_CREATE') OR define('PERMISSION_UNIT_CREATE', 'unit-create');
defined('PERMISSION_UNIT_EDIT') OR define('PERMISSION_UNIT_EDIT', 'unit-edit');
defined('PERMISSION_UNIT_DELETE') OR define('PERMISSION_UNIT_DELETE', 'unit-delete');
defined('PERMISSION_UNIT_PRINT') OR define('PERMISSION_UNIT_PRINT', 'unit-print');

defined('PERMISSION_CONVERSION_VIEW') OR define('PERMISSION_CONVERSION_VIEW', 'conversion-view');
defined('PERMISSION_CONVERSION_CREATE') OR define('PERMISSION_CONVERSION_CREATE', 'conversion-create');
defined('PERMISSION_CONVERSION_EDIT') OR define('PERMISSION_CONVERSION_EDIT', 'conversion-edit');
defined('PERMISSION_CONVERSION_DELETE') OR define('PERMISSION_CONVERSION_DELETE', 'conversion-delete');
defined('PERMISSION_CONVERSION_PRINT') OR define('PERMISSION_CONVERSION_PRINT', 'conversion-print');

defined('PERMISSION_DOCUMENT_TYPE_VIEW') OR define('PERMISSION_DOCUMENT_TYPE_VIEW', 'document-type-view');
defined('PERMISSION_DOCUMENT_TYPE_CREATE') OR define('PERMISSION_DOCUMENT_TYPE_CREATE', 'document-type-create');
defined('PERMISSION_DOCUMENT_TYPE_EDIT') OR define('PERMISSION_DOCUMENT_TYPE_EDIT', 'document-type-edit');
defined('PERMISSION_DOCUMENT_TYPE_DELETE') OR define('PERMISSION_DOCUMENT_TYPE_DELETE', 'document-type-delete');
defined('PERMISSION_DOCUMENT_TYPE_PRINT') OR define('PERMISSION_DOCUMENT_TYPE_PRINT', 'document-type-print');

defined('PERMISSION_CHECKLIST_TYPE_VIEW') OR define('PERMISSION_CHECKLIST_TYPE_VIEW', 'checklist-type-view');
defined('PERMISSION_CHECKLIST_TYPE_CREATE') OR define('PERMISSION_CHECKLIST_TYPE_CREATE', 'checklist-type-create');
defined('PERMISSION_CHECKLIST_TYPE_EDIT') OR define('PERMISSION_CHECKLIST_TYPE_EDIT', 'checklist-type-edit');
defined('PERMISSION_CHECKLIST_TYPE_DELETE') OR define('PERMISSION_CHECKLIST_TYPE_DELETE', 'checklist-type-delete');
defined('PERMISSION_CHECKLIST_TYPE_PRINT') OR define('PERMISSION_CHECKLIST_TYPE_PRINT', 'checklist-type-print');

defined('PERMISSION_CHECKLIST_VIEW') OR define('PERMISSION_CHECKLIST_VIEW', 'checklist-view');
defined('PERMISSION_CHECKLIST_CREATE') OR define('PERMISSION_CHECKLIST_CREATE', 'checklist-create');
defined('PERMISSION_CHECKLIST_EDIT') OR define('PERMISSION_CHECKLIST_EDIT', 'checklist-edit');
defined('PERMISSION_CHECKLIST_DELETE') OR define('PERMISSION_CHECKLIST_DELETE', 'checklist-delete');
defined('PERMISSION_CHECKLIST_PRINT') OR define('PERMISSION_CHECKLIST_PRINT', 'checklist-print');

defined('PERMISSION_OVERTIME_VIEW') OR define('PERMISSION_OVERTIME_VIEW', 'overtime-view');
defined('PERMISSION_OVERTIME_CREATE') OR define('PERMISSION_OVERTIME_CREATE', 'overtime-create');
defined('PERMISSION_OVERTIME_EDIT') OR define('PERMISSION_OVERTIME_EDIT', 'overtime-edit');
defined('PERMISSION_OVERTIME_DELETE') OR define('PERMISSION_OVERTIME_DELETE', 'overtime-delete');
defined('PERMISSION_OVERTIME_PRINT') OR define('PERMISSION_OVERTIME_PRINT', 'overtime-print');

defined('PERMISSION_SERVICE_HOUR_VIEW') OR define('PERMISSION_SERVICE_HOUR_VIEW', 'service-hour-view');
defined('PERMISSION_SERVICE_HOUR_CREATE') OR define('PERMISSION_SERVICE_HOUR_CREATE', 'service-hour-create');
defined('PERMISSION_SERVICE_HOUR_EDIT') OR define('PERMISSION_SERVICE_HOUR_EDIT', 'service-hour-edit');
defined('PERMISSION_SERVICE_HOUR_DELETE') OR define('PERMISSION_SERVICE_HOUR_DELETE', 'service-hour-delete');
defined('PERMISSION_SERVICE_HOUR_PRINT') OR define('PERMISSION_SERVICE_HOUR_PRINT', 'service-hour-print');

defined('PERMISSION_BOOKING_TYPE_VIEW') OR define('PERMISSION_BOOKING_TYPE_VIEW', 'booking-type-view');
defined('PERMISSION_BOOKING_TYPE_CREATE') OR define('PERMISSION_BOOKING_TYPE_CREATE', 'booking-type-create');
defined('PERMISSION_BOOKING_TYPE_EDIT') OR define('PERMISSION_BOOKING_TYPE_EDIT', 'booking-type-edit');
defined('PERMISSION_BOOKING_TYPE_DELETE') OR define('PERMISSION_BOOKING_TYPE_DELETE', 'booking-type-delete');
defined('PERMISSION_BOOKING_TYPE_PRINT') OR define('PERMISSION_BOOKING_TYPE_PRINT', 'booking-type-print');

defined('PERMISSION_EXTENSION_FIELD_VIEW') OR define('PERMISSION_EXTENSION_FIELD_VIEW', 'extension-field-view');
defined('PERMISSION_EXTENSION_FIELD_CREATE') OR define('PERMISSION_EXTENSION_FIELD_CREATE', 'extension-field-create');
defined('PERMISSION_EXTENSION_FIELD_EDIT') OR define('PERMISSION_EXTENSION_FIELD_EDIT', 'extension-field-edit');
defined('PERMISSION_EXTENSION_FIELD_DELETE') OR define('PERMISSION_EXTENSION_FIELD_DELETE', 'extension-field-delete');
defined('PERMISSION_EXTENSION_FIELD_PRINT') OR define('PERMISSION_EXTENSION_FIELD_PRINT', 'extension-field-print');

defined('PERMISSION_HANDLING_TYPE_VIEW') OR define('PERMISSION_HANDLING_TYPE_VIEW', 'handling-type-view');
defined('PERMISSION_HANDLING_TYPE_CREATE') OR define('PERMISSION_HANDLING_TYPE_CREATE', 'handling-type-create');
defined('PERMISSION_HANDLING_TYPE_EDIT') OR define('PERMISSION_HANDLING_TYPE_EDIT', 'handling-type-edit');
defined('PERMISSION_HANDLING_TYPE_DELETE') OR define('PERMISSION_HANDLING_TYPE_DELETE', 'handling-type-delete');
defined('PERMISSION_HANDLING_TYPE_PRINT') OR define('PERMISSION_HANDLING_TYPE_PRINT', 'handling-type-print');

defined('PERMISSION_HANDLING_COMPONENT_VIEW') OR define('PERMISSION_HANDLING_COMPONENT_VIEW', 'handling-component-view');
defined('PERMISSION_HANDLING_COMPONENT_CREATE') OR define('PERMISSION_HANDLING_COMPONENT_CREATE', 'handling-component-create');
defined('PERMISSION_HANDLING_COMPONENT_EDIT') OR define('PERMISSION_HANDLING_COMPONENT_EDIT', 'handling-component-edit');
defined('PERMISSION_HANDLING_COMPONENT_DELETE') OR define('PERMISSION_HANDLING_COMPONENT_DELETE', 'handling-component-delete');
defined('PERMISSION_HANDLING_COMPONENT_PRINT') OR define('PERMISSION_HANDLING_COMPONENT_PRINT', 'handling-component-print');

defined('PERMISSION_HANDLING_COMPONENT_TRANSACTION_VIEW') OR define('PERMISSION_HANDLING_COMPONENT_TRANSACTION_VIEW', 'handling-component-transaction-view');
defined('PERMISSION_HANDLING_COMPONENT_TRANSACTION_CREATE') OR define('PERMISSION_HANDLING_COMPONENT_TRANSACTION_CREATE', 'handling-component-transaction-create');
defined('PERMISSION_HANDLING_COMPONENT_TRANSACTION_EDIT') OR define('PERMISSION_HANDLING_COMPONENT_TRANSACTION_EDIT', 'handling-component-transaction-edit');
defined('PERMISSION_HANDLING_COMPONENT_TRANSACTION_DELETE') OR define('PERMISSION_HANDLING_COMPONENT_TRANSACTION_DELETE', 'handling-component-transaction-delete');
defined('PERMISSION_HANDLING_COMPONENT_TRANSACTION_PRINT') OR define('PERMISSION_HANDLING_COMPONENT_TRANSACTION_PRINT', 'handling-component-transaction-print');
defined('PERMISSION_HANDLING_COMPONENT_TRANSACTION_VALIDATE') OR define('PERMISSION_HANDLING_COMPONENT_TRANSACTION_VALIDATE', 'handling-component-transaction-validate');

// Shifting
defined('PERMISSION_SHIFTING_VIEW') OR define('PERMISSION_SHIFTING_VIEW', 'shifting-view');
defined('PERMISSION_SHIFTING_CREATE') OR define('PERMISSION_SHIFTING_CREATE', 'shifting-create');
defined('PERMISSION_SHIFTING_EDIT') OR define('PERMISSION_SHIFTING_EDIT', 'shifting-edit');
defined('PERMISSION_SHIFTING_DELETE') OR define('PERMISSION_SHIFTING_DELETE', 'shifting-delete');
defined('PERMISSION_SHIFTING_PRINT') OR define('PERMISSION_SHIFTING_PRINT', 'shifting-print');
defined('PERMISSION_SHIFTING_VALIDATE') OR define('PERMISSION_SHIFTING_VALIDATE', 'shifting-validate');

// Component Price
defined('PERMISSION_COMPONENT_PRICE_VIEW') OR define('PERMISSION_COMPONENT_PRICE_VIEW', 'component-price-view');
defined('PERMISSION_COMPONENT_PRICE_CREATE') OR define('PERMISSION_COMPONENT_PRICE_CREATE', 'component-price-create');
defined('PERMISSION_COMPONENT_PRICE_EDIT') OR define('PERMISSION_COMPONENT_PRICE_EDIT', 'component-price-edit');
defined('PERMISSION_COMPONENT_PRICE_DELETE') OR define('PERMISSION_COMPONENT_PRICE_DELETE', 'component-price-delete');
defined('PERMISSION_COMPONENT_PRICE_PRINT') OR define('PERMISSION_COMPONENT_PRICE_PRINT', 'component-price-print');
defined('PERMISSION_COMPONENT_PRICE_VALIDATE') OR define('PERMISSION_COMPONENT_PRICE_VALIDATE', 'component-price-validate');

// Payment permission
defined('PERMISSION_PAYMENT_VIEW') OR define('PERMISSION_PAYMENT_VIEW', 'payment-view');
defined('PERMISSION_PAYMENT_CREATE') OR define('PERMISSION_PAYMENT_CREATE', 'payment-create');
defined('PERMISSION_PAYMENT_EDIT') OR define('PERMISSION_PAYMENT_EDIT', 'payment-edit');
defined('PERMISSION_PAYMENT_DELETE') OR define('PERMISSION_PAYMENT_DELETE', 'payment-delete');
defined('PERMISSION_PAYMENT_PRINT') OR define('PERMISSION_PAYMENT_PRINT', 'payment-print');
defined('PERMISSION_PAYMENT_VALIDATE') OR define('PERMISSION_PAYMENT_VALIDATE', 'payment-validate');
defined('PERMISSION_PAYMENT_REALIZE') OR define('PERMISSION_PAYMENT_REALIZE', 'payment-realize');
defined('PERMISSION_PAYMENT_CHECK') OR define('PERMISSION_PAYMENT_CHECK', 'payment-check');
defined('PERMISSION_PAYMENT_PIC') OR define('PERMISSION_PAYMENT_PIC', 'payment-pic');

// Payment permission
defined('PERMISSION_INVOICE_VIEW') OR define('PERMISSION_INVOICE_VIEW', 'invoice-view');
defined('PERMISSION_INVOICE_CREATE') OR define('PERMISSION_INVOICE_CREATE', 'invoice-create');
defined('PERMISSION_INVOICE_EDIT') OR define('PERMISSION_INVOICE_EDIT', 'invoice-edit');
defined('PERMISSION_INVOICE_DELETE') OR define('PERMISSION_INVOICE_DELETE', 'invoice-delete');
defined('PERMISSION_INVOICE_PRINT') OR define('PERMISSION_INVOICE_PRINT', 'invoice-print');
defined('PERMISSION_INVOICE_VALIDATE') OR define('PERMISSION_INVOICE_VALIDATE', 'invoice-validate');

// Booking and upload permission
defined('PERMISSION_UPLOAD_VIEW') OR define('PERMISSION_UPLOAD_VIEW', 'upload-view');
defined('PERMISSION_UPLOAD_CREATE') OR define('PERMISSION_UPLOAD_CREATE', 'upload-create');
defined('PERMISSION_UPLOAD_EDIT') OR define('PERMISSION_UPLOAD_EDIT', 'upload-edit');
defined('PERMISSION_UPLOAD_DELETE') OR define('PERMISSION_UPLOAD_DELETE', 'upload-delete');
defined('PERMISSION_UPLOAD_PRINT') OR define('PERMISSION_UPLOAD_PRINT', 'upload-print');
defined('PERMISSION_UPLOAD_VALIDATE') OR define('PERMISSION_UPLOAD_VALIDATE', 'upload-validate');
defined('PERMISSION_UPLOAD_CHECK') OR define('PERMISSION_UPLOAD_CHECK', 'upload-check');
defined('PERMISSION_UPLOAD_EDIT_UPLOAD_IN') OR define('PERMISSION_UPLOAD_EDIT_UPLOAD_IN', 'upload-edit-upload-in');

defined('PERMISSION_BOOKING_IN_VIEW') OR define('PERMISSION_BOOKING_IN_VIEW', 'booking-in-view');
defined('PERMISSION_BOOKING_IN_CREATE') OR define('PERMISSION_BOOKING_IN_CREATE', 'booking-in-create');
defined('PERMISSION_BOOKING_IN_EDIT') OR define('PERMISSION_BOOKING_IN_EDIT', 'booking-in-edit');
defined('PERMISSION_BOOKING_IN_DELETE') OR define('PERMISSION_BOOKING_IN_DELETE', 'booking-in-delete');
defined('PERMISSION_BOOKING_IN_PRINT') OR define('PERMISSION_BOOKING_IN_PRINT', 'booking-in-print');
defined('PERMISSION_BOOKING_IN_VALIDATE') OR define('PERMISSION_BOOKING_IN_VALIDATE', 'booking-in-validate');
defined('PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS') OR define('PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS', 'booking-in-edit-payment-status');
defined('PERMISSION_BOOKING_IN_EDIT_BCF_STATUS') OR define('PERMISSION_BOOKING_IN_EDIT_BCF_STATUS', 'booking-in-edit-bcf-status');

defined('PERMISSION_BOOKING_OUT_VIEW') OR define('PERMISSION_BOOKING_OUT_VIEW', 'booking-out-view');
defined('PERMISSION_BOOKING_OUT_CREATE') OR define('PERMISSION_BOOKING_OUT_CREATE', 'booking-out-create');
defined('PERMISSION_BOOKING_OUT_EDIT') OR define('PERMISSION_BOOKING_OUT_EDIT', 'booking-out-edit');
defined('PERMISSION_BOOKING_OUT_DELETE') OR define('PERMISSION_BOOKING_OUT_DELETE', 'booking-out-delete');
defined('PERMISSION_BOOKING_OUT_PRINT') OR define('PERMISSION_BOOKING_OUT_PRINT', 'booking-out-print');
defined('PERMISSION_BOOKING_OUT_VALIDATE') OR define('PERMISSION_BOOKING_OUT_VALIDATE', 'booking-out-validate');
defined('PERMISSION_BOOKING_OUT_EDIT_PAYMENT_STATUS') OR define('PERMISSION_BOOKING_OUT_EDIT_PAYMENT_STATUS', 'booking-out-edit-payment-status');
defined('PERMISSION_BOOKING_OUT_EDIT_BCF_STATUS') OR define('PERMISSION_BOOKING_OUT_EDIT_BCF_STATUS', 'booking-out-edit-bcf-status');

defined('PERMISSION_BOOKING_CONTROL_VIEW') OR define('PERMISSION_BOOKING_CONTROL_VIEW', 'booking-control-view');
defined('PERMISSION_BOOKING_CONTROL_MANAGE') OR define('PERMISSION_BOOKING_CONTROL_MANAGE', 'booking-control-manage');
defined('PERMISSION_BOOKING_CONTROL_REVERT') OR define('PERMISSION_BOOKING_CONTROL_REVERT', 'booking-control-revert');
defined('PERMISSION_BOOKING_RATE') OR define('PERMISSION_BOOKING_RATE', 'booking-rate');
defined('PERMISSION_BOOKING_STATUS_REVERT') OR define('PERMISSION_BOOKING_STATUS_REVERT', 'booking-status-revert');

defined('PERMISSION_BOOKING_CIF_INVOICE_VIEW') OR define('PERMISSION_BOOKING_CIF_INVOICE_VIEW', 'booking-cif-invoice-view');
defined('PERMISSION_BOOKING_CIF_INVOICE_CREATE') OR define('PERMISSION_BOOKING_CIF_INVOICE_CREATE', 'booking-cif-invoice-create');
defined('PERMISSION_BOOKING_CIF_INVOICE_EDIT') OR define('PERMISSION_BOOKING_CIF_INVOICE_EDIT', 'booking-cif-invoice-edit');
defined('PERMISSION_BOOKING_CIF_INVOICE_DELETE') OR define('PERMISSION_BOOKING_CIF_INVOICE_DELETE', 'booking-cif-invoice-delete');
defined('PERMISSION_BOOKING_CIF_INVOICE_PRINT') OR define('PERMISSION_BOOKING_CIF_INVOICE_PRINT', 'booking-cif-invoice-print');

// Booking news permission
defined('PERMISSION_BOOKING_NEWS_VIEW') OR define('PERMISSION_BOOKING_NEWS_VIEW', 'booking-news-view');
defined('PERMISSION_BOOKING_NEWS_CREATE') OR define('PERMISSION_BOOKING_NEWS_CREATE', 'booking-news-create');
defined('PERMISSION_BOOKING_NEWS_EDIT') OR define('PERMISSION_BOOKING_NEWS_EDIT', 'booking-news-edit');
defined('PERMISSION_BOOKING_NEWS_DELETE') OR define('PERMISSION_BOOKING_NEWS_DELETE', 'booking-news-delete');
defined('PERMISSION_BOOKING_NEWS_PRINT') OR define('PERMISSION_BOOKING_NEWS_PRINT', 'booking-news-print');

// Discrepancy news permission
defined('PERMISSION_DISCREPANCY_HANDOVER_VIEW') OR define('PERMISSION_DISCREPANCY_HANDOVER_VIEW', 'discrepancy-handover-view');
defined('PERMISSION_DISCREPANCY_HANDOVER_CREATE') OR define('PERMISSION_DISCREPANCY_HANDOVER_CREATE', 'discrepancy-handover-create');
defined('PERMISSION_DISCREPANCY_HANDOVER_EDIT') OR define('PERMISSION_DISCREPANCY_HANDOVER_EDIT', 'discrepancy-handover-edit');
defined('PERMISSION_DISCREPANCY_HANDOVER_VALIDATE') OR define('PERMISSION_DISCREPANCY_HANDOVER_VALIDATE', 'discrepancy-handover-validate');
defined('PERMISSION_DISCREPANCY_HANDOVER_PROCEED') OR define('PERMISSION_DISCREPANCY_HANDOVER_PROCEED', 'discrepancy-handover-proceed');
defined('PERMISSION_DISCREPANCY_HANDOVER_DELETE') OR define('PERMISSION_DISCREPANCY_HANDOVER_DELETE', 'discrepancy-handover-delete');

// Delivery permission
defined('PERMISSION_DELIVERY_ORDER_VIEW') OR define('PERMISSION_DELIVERY_ORDER_VIEW', 'delivery-order-view');
defined('PERMISSION_DELIVERY_ORDER_CREATE') OR define('PERMISSION_DELIVERY_ORDER_CREATE', 'delivery-order-create');
defined('PERMISSION_DELIVERY_ORDER_EDIT') OR define('PERMISSION_DELIVERY_ORDER_EDIT', 'delivery-order-edit');
defined('PERMISSION_DELIVERY_ORDER_DELETE') OR define('PERMISSION_DELIVERY_ORDER_DELETE', 'delivery-order-delete');
defined('PERMISSION_DELIVERY_ORDER_PRINT') OR define('PERMISSION_DELIVERY_ORDER_PRINT', 'delivery-order-print');

// Delivery tracking permission
defined('PERMISSION_DELIVERY_TRACKING_VIEW') OR define('PERMISSION_DELIVERY_TRACKING_VIEW', 'delivery-tracking-view');
defined('PERMISSION_DELIVERY_TRACKING_CREATE') OR define('PERMISSION_DELIVERY_TRACKING_CREATE', 'delivery-tracking-create');
defined('PERMISSION_DELIVERY_TRACKING_EDIT') OR define('PERMISSION_DELIVERY_TRACKING_EDIT', 'delivery-tracking-edit');
defined('PERMISSION_DELIVERY_TRACKING_DELETE') OR define('PERMISSION_DELIVERY_TRACKING_DELETE', 'delivery-tracking-delete');
defined('PERMISSION_DELIVERY_TRACKING_PRINT') OR define('PERMISSION_DELIVERY_TRACKING_PRINT', 'delivery-tracking-print');
defined('PERMISSION_DELIVERY_TRACKING_CLOSE') OR define('PERMISSION_DELIVERY_TRACKING_CLOSE', 'delivery-tracking-close');
defined('PERMISSION_DELIVERY_TRACKING_ASSIGNMENT') OR define('PERMISSION_DELIVERY_TRACKING_ASSIGNMENT', 'delivery-tracking-assignment');
defined('PERMISSION_DELIVERY_TRACKING_ADD_STATE') OR define('PERMISSION_DELIVERY_TRACKING_ADD_STATE', 'delivery-tracking-add-state');

defined('PERMISSION_DELIVERY_INSPECTION_VIEW') OR define('PERMISSION_DELIVERY_INSPECTION_VIEW', 'delivery-inspection-view');
defined('PERMISSION_DELIVERY_INSPECTION_CREATE') OR define('PERMISSION_DELIVERY_INSPECTION_CREATE', 'delivery-inspection-create');
defined('PERMISSION_DELIVERY_INSPECTION_EDIT') OR define('PERMISSION_DELIVERY_INSPECTION_EDIT', 'delivery-inspection-edit');
defined('PERMISSION_DELIVERY_INSPECTION_DELETE') OR define('PERMISSION_DELIVERY_INSPECTION_DELETE', 'delivery-inspection-delete');

defined('PERMISSION_SAFE_CONDUCT_IN_VIEW') OR define('PERMISSION_SAFE_CONDUCT_IN_VIEW', 'safe-conduct-in-view');
defined('PERMISSION_SAFE_CONDUCT_IN_CREATE') OR define('PERMISSION_SAFE_CONDUCT_IN_CREATE', 'safe-conduct-in-create');
defined('PERMISSION_SAFE_CONDUCT_IN_EDIT') OR define('PERMISSION_SAFE_CONDUCT_IN_EDIT', 'safe-conduct-in-edit');
defined('PERMISSION_SAFE_CONDUCT_IN_UPDATE_DATA') OR define('PERMISSION_SAFE_CONDUCT_IN_UPDATE_DATA', 'safe-conduct-in-update-data');
defined('PERMISSION_SAFE_CONDUCT_IN_DELETE') OR define('PERMISSION_SAFE_CONDUCT_IN_DELETE', 'safe-conduct-in-delete');
defined('PERMISSION_SAFE_CONDUCT_IN_PRINT') OR define('PERMISSION_SAFE_CONDUCT_IN_PRINT', 'safe-conduct-in-print');
defined('PERMISSION_SAFE_CONDUCT_EDIT') OR define('PERMISSION_SAFE_CONDUCT_EDIT', 'safe-conduct-edit');
defined('PERMISSION_SAFE_CONDUCT_HANDOVER') OR define('PERMISSION_SAFE_CONDUCT_HANDOVER', 'safe-conduct-handover');

defined('PERMISSION_SAFE_CONDUCT_OUT_VIEW') OR define('PERMISSION_SAFE_CONDUCT_OUT_VIEW', 'safe-conduct-out-view');
defined('PERMISSION_SAFE_CONDUCT_OUT_CREATE') OR define('PERMISSION_SAFE_CONDUCT_OUT_CREATE', 'safe-conduct-out-create');
defined('PERMISSION_SAFE_CONDUCT_OUT_EDIT') OR define('PERMISSION_SAFE_CONDUCT_OUT_EDIT', 'safe-conduct-out-edit');
defined('PERMISSION_SAFE_CONDUCT_OUT_DELETE') OR define('PERMISSION_SAFE_CONDUCT_OUT_DELETE', 'safe-conduct-out-delete');
defined('PERMISSION_SAFE_CONDUCT_OUT_PRINT') OR define('PERMISSION_SAFE_CONDUCT_OUT_PRINT', 'safe-conduct-out-print');

// Security and gate permission
defined('PERMISSION_SECURITY_CHECK_IN') OR define('PERMISSION_SECURITY_CHECK_IN', 'security-check-in');
defined('PERMISSION_SECURITY_CHECK_OUT') OR define('PERMISSION_SECURITY_CHECK_OUT', 'security-check-out');
defined('PERMISSION_SECURITY_UPDATE_DATA') OR define('PERMISSION_SECURITY_UPDATE_DATA', 'security-update-data');
defined('PERMISSION_SECURITY_CHECK_PHOTO') OR define('PERMISSION_SECURITY_CHECK_PHOTO', 'security-check-photo');

defined('PERMISSION_GATE_CHECK_IN') OR define('PERMISSION_GATE_CHECK_IN', 'gate-check-in');
defined('PERMISSION_GATE_CHECK_OUT') OR define('PERMISSION_GATE_CHECK_OUT', 'gate-check-out');
defined('PERMISSION_GATE_UPDATE_DATA') OR define('PERMISSION_GATE_UPDATE_DATA', 'gate-update-data');

// Handling permission
defined('PERMISSION_PLAN_REALIZATION_VIEW') OR define('PERMISSION_PLAN_REALIZATION_VIEW', 'plan-realization-view');
defined('PERMISSION_PLAN_REALIZATION_CREATE') OR define('PERMISSION_PLAN_REALIZATION_CREATE', 'plan-realization-create');
defined('PERMISSION_PLAN_REALIZATION_EDIT') OR define('PERMISSION_PLAN_REALIZATION_EDIT', 'plan-realization-edit');
defined('PERMISSION_PLAN_REALIZATION_DELETE') OR define('PERMISSION_PLAN_REALIZATION_DELETE', 'plan-realization-delete');
defined('PERMISSION_PLAN_REALIZATION_PRINT') OR define('PERMISSION_PLAN_REALIZATION_PRINT', 'plan-realization-print');

// Handling permission
defined('PERMISSION_HANDLING_VIEW') OR define('PERMISSION_HANDLING_VIEW', 'handling-view');
defined('PERMISSION_HANDLING_CREATE') OR define('PERMISSION_HANDLING_CREATE', 'handling-create');
defined('PERMISSION_HANDLING_EDIT') OR define('PERMISSION_HANDLING_EDIT', 'handling-edit');
defined('PERMISSION_HANDLING_DELETE') OR define('PERMISSION_HANDLING_DELETE', 'handling-delete');
defined('PERMISSION_HANDLING_PRINT') OR define('PERMISSION_HANDLING_PRINT', 'handling-print');
defined('PERMISSION_HANDLING_VALIDATE') OR define('PERMISSION_HANDLING_VALIDATE', 'handling-validate');

// Adjustment permission
defined('PERMISSION_ADJUSTMENT_VIEW') OR define('PERMISSION_ADJUSTMENT_VIEW', 'adjustment-view');
defined('PERMISSION_ADJUSTMENT_CREATE') OR define('PERMISSION_ADJUSTMENT_CREATE', 'adjustment-create');
defined('PERMISSION_ADJUSTMENT_EDIT') OR define('PERMISSION_ADJUSTMENT_EDIT', 'adjustment-edit');
defined('PERMISSION_ADJUSTMENT_DELETE') OR define('PERMISSION_ADJUSTMENT_DELETE', 'adjustment-delete');
defined('PERMISSION_ADJUSTMENT_PRINT') OR define('PERMISSION_ADJUSTMENT_PRINT', 'adjustment-print');
defined('PERMISSION_ADJUSTMENT_VALIDATE') OR define('PERMISSION_ADJUSTMENT_VALIDATE', 'adjustment-validate');

// Work order permission
defined('PERMISSION_WORKORDER_TAKE_JOB') OR define('PERMISSION_WORKORDER_TAKE_JOB', 'workorder-take-job');
defined('PERMISSION_WORKORDER_VIEW') OR define('PERMISSION_WORKORDER_VIEW', 'workorder-view');
defined('PERMISSION_WORKORDER_CREATE') OR define('PERMISSION_WORKORDER_CREATE', 'workorder-create');
defined('PERMISSION_WORKORDER_EDIT') OR define('PERMISSION_WORKORDER_EDIT', 'workorder-edit');
defined('PERMISSION_WORKORDER_DELETE') OR define('PERMISSION_WORKORDER_DELETE', 'workorder-delete');
defined('PERMISSION_WORKORDER_PRINT') OR define('PERMISSION_WORKORDER_PRINT', 'workorder-print');
defined('PERMISSION_WORKORDER_VALIDATE') OR define('PERMISSION_WORKORDER_VALIDATE', 'workorder-validate');
defined('PERMISSION_WORKORDER_VALIDATED_EDIT') OR define('PERMISSION_WORKORDER_VALIDATED_EDIT', 'workorder-validated-edit');
defined('PERMISSION_WORKORDER_DISCREPANCY_EDIT') OR define('PERMISSION_WORKORDER_DISCREPANCY_EDIT', 'workorder-discrepancy-edit');
defined('PERMISSION_WORKORDER_COMPLETE') OR define('PERMISSION_WORKORDER_COMPLETE', 'workorder-complete');
defined('PERMISSION_WORKORDER_LOCK') OR define('PERMISSION_WORKORDER_LOCK', 'workorder-lock');
defined('PERMISSION_WORKORDER_APPROVED') OR define('PERMISSION_WORKORDER_APPROVED', 'workorder-approved');
defined('PERMISSION_WORKORDER_VALIDATE_OVERTIME') OR define('PERMISSION_WORKORDER_VALIDATE_OVERTIME', 'workorder-validate-overtime');
defined('PERMISSION_WORKORDER_PALLET_APPROVED') OR define('PERMISSION_WORKORDER_PALLET_APPROVED', 'workorder-pallet-approved');
defined('PERMISSION_WORKORDER_UNLOCK_HANDHELD') OR define('PERMISSION_WORKORDER_UNLOCK_HANDHELD', 'workorder-unlock-handheld');
defined('PERMISSION_WORKORDER_VIEW_PHOTO') OR define('PERMISSION_WORKORDER_VIEW_PHOTO', 'workorder-view-photo');

defined('PERMISSION_OWNERSHIP_VIEW') OR define('PERMISSION_OWNERSHIP_VIEW', 'change-ownership-view');
defined('PERMISSION_OWNERSHIP_CREATE') OR define('PERMISSION_OWNERSHIP_CREATE', 'change-ownership-create');
defined('PERMISSION_OWNERSHIP_EDIT') OR define('PERMISSION_OWNERSHIP_EDIT', 'change-ownership-edit');
defined('PERMISSION_OWNERSHIP_DELETE') OR define('PERMISSION_OWNERSHIP_DELETE', 'change-ownership-delete');
defined('PERMISSION_OWNERSHIP_PRINT') OR define('PERMISSION_OWNERSHIP_PRINT', 'change-ownership-print');

// News permission
defined('PERMISSION_NEWS_VIEW') OR define('PERMISSION_NEWS_VIEW', 'news-view');
defined('PERMISSION_NEWS_CREATE') OR define('PERMISSION_NEWS_CREATE', 'news-create');
defined('PERMISSION_NEWS_EDIT') OR define('PERMISSION_NEWS_EDIT', 'news-edit');
defined('PERMISSION_NEWS_DELETE') OR define('PERMISSION_NEWS_DELETE', 'news-delete');
defined('PERMISSION_NEWS_PRINT') OR define('PERMISSION_NEWS_PRINT', 'news-print');

// Pallet permission
defined('PERMISSION_PALLET_VIEW') OR define('PERMISSION_PALLET_VIEW', 'pallet-view');
defined('PERMISSION_PALLET_CREATE') OR define('PERMISSION_PALLET_CREATE', 'pallet-create');
defined('PERMISSION_PALLET_EDIT') OR define('PERMISSION_PALLET_EDIT', 'pallet-edit');
defined('PERMISSION_PALLET_DELETE') OR define('PERMISSION_PALLET_DELETE', 'pallet-delete');
defined('PERMISSION_PALLET_PRINT') OR define('PERMISSION_PALLET_PRINT', 'pallet-print');

// Opname permission
defined('PERMISSION_OPNAME_VIEW') OR define('PERMISSION_OPNAME_VIEW', 'opname-view');
defined('PERMISSION_OPNAME_CREATE') OR define('PERMISSION_OPNAME_CREATE', 'opname-create');
defined('PERMISSION_OPNAME_EDIT') OR define('PERMISSION_OPNAME_EDIT', 'opname-edit');
defined('PERMISSION_OPNAME_PROCESS') OR define('PERMISSION_OPNAME_PROCESS', 'opname-process');
defined('PERMISSION_OPNAME_ACCESS') OR define('PERMISSION_OPNAME_ACCESS', 'opname-access'); 
defined('PERMISSION_OPNAME_VIEW_RESULT') OR define('PERMISSION_OPNAME_VIEW_RESULT', 'opname-result');
defined('PERMISSION_OPNAME_DELETE') OR define('PERMISSION_OPNAME_DELETE', 'opname-delete');
defined('PERMISSION_OPNAME_PRINT') OR define('PERMISSION_OPNAME_PRINT', 'opname-print');
defined('PERMISSION_OPNAME_PRINT_RESULT') OR define('PERMISSION_OPNAME_PRINT_RESULT', 'opname-print-result');
defined('PERMISSION_OPNAME_VALIDATE') OR define('PERMISSION_OPNAME_VALIDATE', 'opname-validate');

// Warehouse receipt permission
defined('PERMISSION_WAREHOUSE_RECEIPT_VIEW') OR define('PERMISSION_WAREHOUSE_RECEIPT_VIEW', 'warehouse-receipt-view');
defined('PERMISSION_WAREHOUSE_RECEIPT_CREATE') OR define('PERMISSION_WAREHOUSE_RECEIPT_CREATE', 'warehouse-receipt-create');
defined('PERMISSION_WAREHOUSE_RECEIPT_EDIT') OR define('PERMISSION_WAREHOUSE_RECEIPT_EDIT', 'warehouse-receipt-edit');
defined('PERMISSION_WAREHOUSE_RECEIPT_DELETE') OR define('PERMISSION_WAREHOUSE_RECEIPT_DELETE', 'warehouse-receipt-delete');
defined('PERMISSION_WAREHOUSE_RECEIPT_PRINT') OR define('PERMISSION_WAREHOUSE_RECEIPT_PRINT', 'warehouse-receipt-print');
defined('PERMISSION_WAREHOUSE_RECEIPT_VALIDATE') OR define('PERMISSION_WAREHOUSE_RECEIPT_VALIDATE', 'warehouse-receipt-validate');

// Readdress permission
defined('PERMISSION_READDRESS_VIEW') OR define('PERMISSION_READDRESS_VIEW', 'readdress-view');
defined('PERMISSION_READDRESS_CREATE') OR define('PERMISSION_READDRESS_CREATE', 'readdress-create');
defined('PERMISSION_READDRESS_VALIDATE') OR define('PERMISSION_READDRESS_VALIDATE', 'readdress-validate');

// Auction permission
defined('PERMISSION_AUCTION_VIEW') OR define('PERMISSION_AUCTION_VIEW', 'auction-view');
defined('PERMISSION_AUCTION_CREATE') OR define('PERMISSION_AUCTION_CREATE', 'auction-create');
defined('PERMISSION_AUCTION_EDIT') OR define('PERMISSION_AUCTION_EDIT', 'auction-edit');
defined('PERMISSION_AUCTION_DELETE') OR define('PERMISSION_AUCTION_DELETE', 'auction-delete');
defined('PERMISSION_AUCTION_PRINT') OR define('PERMISSION_AUCTION_PRINT', 'auction-print');
defined('PERMISSION_AUCTION_VALIDATE') OR define('PERMISSION_AUCTION_VALIDATE', 'auction-validate');

// Danger status replace permission
defined('PERMISSION_DANGER_REPLACEMENT_VIEW') OR define('PERMISSION_DANGER_REPLACEMENT_VIEW', 'danger-replacement-view');
defined('PERMISSION_DANGER_REPLACEMENT_CREATE') OR define('PERMISSION_DANGER_REPLACEMENT_CREATE', 'danger-replacement-create');
defined('PERMISSION_DANGER_REPLACEMENT_VALIDATE') OR define('PERMISSION_DANGER_REPLACEMENT_VALIDATE', 'danger-replacement-validate');

// Report permission
defined('PERMISSION_REPORT_GENERAL') OR define('PERMISSION_REPORT_GENERAL', 'report-general');
defined('PERMISSION_REPORT_IN') OR define('PERMISSION_REPORT_IN', 'report-in');
defined('PERMISSION_REPORT_OUT') OR define('PERMISSION_REPORT_OUT', 'report-out');
defined('PERMISSION_REPORT_IN_PROGRESS') OR define('PERMISSION_REPORT_IN_PROGRESS', 'report-in-progress');
defined('PERMISSION_REPORT_OUT_PROGRESS') OR define('PERMISSION_REPORT_OUT_PROGRESS', 'report-out-progress');
defined('PERMISSION_REPORT_STOCK') OR define('PERMISSION_REPORT_STOCK', 'report-stock');
defined('PERMISSION_REPORT_BC') OR define('PERMISSION_REPORT_BC', 'report-bc');
defined('PERMISSION_REPORT_TPP') OR define('PERMISSION_REPORT_TPP', 'report-tpp');
defined('PERMISSION_REPORT_ADMIN_SITE') OR define('PERMISSION_REPORT_ADMIN_SITE', 'report-admin-site');
defined('PERMISSION_REPORT_COMPLAIN') OR define('PERMISSION_REPORT_COMPLAIN', 'report-complain');
defined('PERMISSION_REPORT_ESEAL_TRACKING') or define('PERMISSION_REPORT_ESEAL_TRACKING', 'report-eseal-tracking');
defined('PERMISSION_REPORT_PALLET') OR define('PERMISSION_REPORT_PALLET', 'report-pallet');
defined('PERMISSION_REPORT_FORKLIFT') OR define('PERMISSION_REPORT_FORKLIFT', 'report-forklift');
defined('PERMISSION_REPORT_PERFORMANCE') OR define('PERMISSION_REPORT_PERFORMANCE', 'report-performance');
defined('PERMISSION_REPORT_TRANSPORTER') OR define('PERMISSION_REPORT_TRANSPORTER', 'report-transporter');
defined('PERMISSION_REPORT_OVER_CAPACITY') OR define('PERMISSION_REPORT_OVER_CAPACITY', 'report-over-capacity');
defined('PERMISSION_REPORT_DOCUMENT_PRODUCTION') OR define('PERMISSION_REPORT_DOCUMENT_PRODUCTION', 'report-document-production');
defined('PERMISSION_REPORT_OUTBOUND_TRACKING') OR define('PERMISSION_REPORT_OUTBOUND_TRACKING', 'report-outbound-tracking');
defined('PERMISSION_REPORT_FLEET_PRODUCTION_ACTIVITY') OR define('PERMISSION_REPORT_FLEET_PRODUCTION_ACTIVITY', 'report-fleet-production-activity');
defined('PERMISSION_REPORT_PERFORMANCE_IN_OUT') OR define('PERMISSION_REPORT_PERFORMANCE_IN_OUT', 'report-performance-in-out');

defined('PERMISSION_REPORT_STOCK_LOCATION') or define('PERMISSION_REPORT_STOCK_LOCATION', 'report-stock-location');
defined('PERMISSION_REPORT_STOCK_STATUS') or define('PERMISSION_REPORT_STOCK_STATUS', 'report-stock-status');
defined('PERMISSION_REPORT_STOCK_COMPARATOR') or define('PERMISSION_REPORT_STOCK_COMPARATOR', 'report-stock-comparator');
defined('PERMISSION_REPORT_STOCK_MOVEMENT') or define('PERMISSION_REPORT_STOCK_MOVEMENT', 'report-stock-movement');
defined('PERMISSION_REPORT_STOCK_AGING') or define('PERMISSION_REPORT_STOCK_AGING', 'report-stock-aging');
defined('PERMISSION_REPORT_STOCK_OUTBOUND') or define('PERMISSION_REPORT_STOCK_OUTBOUND', 'report-stock-outbound');
defined('PERMISSION_REPORT_COMPLIANCE') or define('PERMISSION_REPORT_COMPLIANCE', 'report-compliance');
defined('PERMISSION_REPORT_PLAN_REALIZATION') or define('PERMISSION_REPORT_PLAN_REALIZATION', 'report-plan-realization');
defined('PERMISSION_REPORT_HANDOVER') or define('PERMISSION_REPORT_HANDOVER', 'report-handover');
defined('PERMISSION_REPORT_BOOKING_RATING') or define('PERMISSION_REPORT_BOOKING_RATING', 'report-booking-rating');
defined('PERMISSION_REPORT_BOOKING_SUMMARY') or define('PERMISSION_REPORT_BOOKING_SUMMARY', 'report-booking-summary');
defined('PERMISSION_REPORT_BOOKING_CONTROL') or define('PERMISSION_REPORT_BOOKING_CONTROL', 'report-booking-control');
defined('PERMISSION_REPORT_BOOKING_COMPARATOR') or define('PERMISSION_REPORT_BOOKING_COMPARATOR', 'report-booking-comparator');
defined('PERMISSION_REPORT_BOOKING_TRACKER') or define('PERMISSION_REPORT_BOOKING_TRACKER', 'report-booking-tracker');
defined('PERMISSION_REPORT_CONTAINER_TRACKER') or define('PERMISSION_REPORT_CONTAINER_TRACKER', 'report-container-tracker');
defined('PERMISSION_REPORT_WORK_ORDER_SUMMARY') or define('PERMISSION_REPORT_WORK_ORDER_SUMMARY', 'report-work-order-summary');
defined('PERMISSION_REPORT_SERVICE_TIME') or define('PERMISSION_REPORT_SERVICE_TIME', 'report-service-time');
defined('PERMISSION_REPORT_SERVICE_TIME_CONTROL_IN') or define('PERMISSION_REPORT_SERVICE_TIME_CONTROL_IN', 'report-service-time-control-in');
defined('PERMISSION_REPORT_SERVICE_TIME_CONTROL_OUT') or define('PERMISSION_REPORT_SERVICE_TIME_CONTROL_OUT', 'report-service-time-control-out');
defined('PERMISSION_REPORT_INVOICE') or define('PERMISSION_REPORT_INVOICE', 'report-invoice');
defined('PERMISSION_REPORT_WORK_ORDER_OVERTIME') or define('PERMISSION_REPORT_WORK_ORDER_OVERTIME', 'report-work-order-overtime');
defined('PERMISSION_REPORT_HEAVY_EQUIPMENT_USAGE') or define('PERMISSION_REPORT_HEAVY_EQUIPMENT_USAGE', 'report-heavy-equipment-usage');
defined('PERMISSION_REPORT_BOOKING_PAYMENTS') or define('PERMISSION_REPORT_BOOKING_PAYMENTS', 'report-booking-payments');

// Work order document
defined('PERMISSION_WORK_ORDER_DOCUMENT_VIEW') OR define('PERMISSION_WORK_ORDER_DOCUMENT_VIEW', 'work-order-document-view');
defined('PERMISSION_WORK_ORDER_DOCUMENT_CREATE') OR define('PERMISSION_WORK_ORDER_DOCUMENT_CREATE', 'work-order-document-create');
defined('PERMISSION_WORK_ORDER_DOCUMENT_EDIT') OR define('PERMISSION_WORK_ORDER_DOCUMENT_EDIT', 'work-order-document-edit');
defined('PERMISSION_WORK_ORDER_DOCUMENT_DELETE') OR define('PERMISSION_WORK_ORDER_DOCUMENT_DELETE', 'work-order-document-delete');
defined('PERMISSION_WORK_ORDER_DOCUMENT_PRINT') OR define('PERMISSION_WORK_ORDER_DOCUMENT_PRINT', 'work-order-document-print');
defined('PERMISSION_WORK_ORDER_DOCUMENT_VALIDATE') OR define('PERMISSION_WORK_ORDER_DOCUMENT_VALIDATE', 'work-order-document-validate');

// Cycle Count permission
defined('PERMISSION_CYCLE_COUNT_VIEW') OR define('PERMISSION_CYCLE_COUNT_VIEW', 'cycle-count-view');
defined('PERMISSION_CYCLE_COUNT_CREATE') OR define('PERMISSION_CYCLE_COUNT_CREATE', 'cycle-count-create');
defined('PERMISSION_CYCLE_COUNT_DELETE') OR define('PERMISSION_CYCLE_COUNT_DELETE', 'cycle-count-delete');
defined('PERMISSION_CYCLE_COUNT_PRINT') OR define('PERMISSION_CYCLE_COUNT_PRINT', 'cycle-count-print');
defined('PERMISSION_CYCLE_COUNT_PROCESS') OR define('PERMISSION_CYCLE_COUNT_PROCESS', 'cycle-count-process');  
defined('PERMISSION_CYCLE_COUNT_ACCESS') OR define('PERMISSION_CYCLE_COUNT_ACCESS', 'cycle-count-access'); 
defined('PERMISSION_CYCLE_COUNT_RESULT') OR define('PERMISSION_CYCLE_COUNT_RESULT', 'cycle-count-result'); 
defined('PERMISSION_CYCLE_COUNT_VALIDATE') OR define('PERMISSION_CYCLE_COUNT_VALIDATE', 'cycle-count-validate');
defined('PERMISSION_CYCLE_COUNT_PRINT_RESULT') OR define('PERMISSION_CYCLE_COUNT_PRINT_RESULT', 'cycle-count-print-result');

// Put Away permission
defined('PERMISSION_PUT_AWAY_VIEW') OR define('PERMISSION_PUT_AWAY_VIEW', 'put-away-view');
defined('PERMISSION_PUT_AWAY_CREATE') OR define('PERMISSION_PUT_AWAY_CREATE', 'put-away-create');
defined('PERMISSION_PUT_AWAY_DELETE') OR define('PERMISSION_PUT_AWAY_DELETE', 'put-away-delete');
defined('PERMISSION_PUT_AWAY_PRINT') OR define('PERMISSION_PUT_AWAY_PRINT', 'put-away-print');
defined('PERMISSION_PUT_AWAY_PROCESS') OR define('PERMISSION_PUT_AWAY_PROCESS', 'put-away-process');  
defined('PERMISSION_PUT_AWAY_ACCESS') OR define('PERMISSION_PUT_AWAY_ACCESS', 'put-away-access'); 
defined('PERMISSION_PUT_AWAY_RESULT') OR define('PERMISSION_PUT_AWAY_RESULT', 'put-away-result'); 
defined('PERMISSION_PUT_AWAY_VALIDATE') OR define('PERMISSION_PUT_AWAY_VALIDATE', 'put-away-validate');
defined('PERMISSION_PUT_AWAY_PRINT_RESULT') OR define('PERMISSION_PUT_AWAY_PRINT_RESULT', 'put-away-print-result');

// Booking assignment
defined('PERMISSION_BOOKING_ASSIGNMENT_VIEW') OR define('PERMISSION_BOOKING_ASSIGNMENT_VIEW', 'booking-assignment-view');
defined('PERMISSION_BOOKING_ASSIGNMENT_CREATE') OR define('PERMISSION_BOOKING_ASSIGNMENT_CREATE', 'booking-assignment-create');
defined('PERMISSION_BOOKING_ASSIGNMENT_DELETE') OR define('PERMISSION_BOOKING_ASSIGNMENT_DELETE', 'booking-assignment-delete');

// Transporter entry permits
defined('PERMISSION_TEP_VIEW') OR define('PERMISSION_TEP_VIEW', 'tep-view');
defined('PERMISSION_TEP_CREATE') OR define('PERMISSION_TEP_CREATE', 'tep-create');
defined('PERMISSION_TEP_EDIT') OR define('PERMISSION_TEP_EDIT', 'tep-edit');
defined('PERMISSION_TEP_DELETE') OR define('PERMISSION_TEP_DELETE', 'tep-delete');
defined('PERMISSION_TEP_PRINT') OR define('PERMISSION_TEP_PRINT', 'tep-print');
defined('PERMISSION_TEP_REQUEST') OR define('PERMISSION_TEP_REQUEST', 'tep-request');
defined('PERMISSION_TEP_SLOT') OR define('PERMISSION_TEP_SLOT', 'tep-slot');
defined('PERMISSION_TEP_CREATE_OUTBOUND') OR define('PERMISSION_TEP_CREATE_OUTBOUND', 'tep-create-outbound');
defined('PERMISSION_TEP_QUEUE_VIEW') OR define('PERMISSION_TEP_QUEUE_VIEW', 'tep-queue-view');
defined('PERMISSION_TEP_EDIT_SECURITY') OR define('PERMISSION_TEP_EDIT_SECURITY', 'tep-edit-security');
defined('PERMISSION_REPORT_HEAVY_EQUIPMENT') OR define('PERMISSION_REPORT_HEAVY_EQUIPMENT', 'report-heavy-equipment');
defined('PERMISSION_TEP_REQUEST_VIEW') OR define('PERMISSION_TEP_REQUEST_VIEW', 'tep-request-view');

// TEP tracking
defined('PERMISSION_TEP_TRACKING_LINK') OR define('PERMISSION_TEP_TRACKING_LINK', 'tep-tracking-link');
defined('PERMISSION_TEP_TRACKING_LINK_EDIT') OR define('PERMISSION_TEP_TRACKING_LINK_EDIT', 'tep-tracking-link-edit');
defined('PERMISSION_TEP_TRACKING_VALIDATE') OR define('PERMISSION_TEP_TRACKING_VALIDATE', 'tep-tracking-validate');

// Dashboard Security
defined('PERMISSION_DASHBOARD_SECURITY_VIEW') OR define('PERMISSION_DASHBOARD_SECURITY_VIEW', 'dashboard-security-view');
defined('PERMISSION_DASHBOARD_SECURITY_CREATE') OR define('PERMISSION_DASHBOARD_SECURITY_CREATE', 'dashboard-security-create');
defined('PERMISSION_DASHBOARD_SECURITY_EDIT') OR define('PERMISSION_DASHBOARD_SECURITY_EDIT', 'dashboard-security-edit');
defined('PERMISSION_DASHBOARD_SECURITY_DELETE') OR define('PERMISSION_DASHBOARD_SECURITY_DELETE', 'dashboard-security-delete');

// Master Heavy Equipment
defined('PERMISSION_HEAVY_EQUIPMENT_VIEW') OR define('PERMISSION_HEAVY_EQUIPMENT_VIEW', 'heavy-equipment-view');
defined('PERMISSION_HEAVY_EQUIPMENT_CREATE') OR define('PERMISSION_HEAVY_EQUIPMENT_CREATE', 'heavy-equipment-create');
defined('PERMISSION_HEAVY_EQUIPMENT_EDIT') OR define('PERMISSION_HEAVY_EQUIPMENT_EDIT', 'heavy-equipment-edit');
defined('PERMISSION_HEAVY_EQUIPMENT_DELETE') OR define('PERMISSION_HEAVY_EQUIPMENT_DELETE', 'heavy-equipment-delete');
defined('PERMISSION_HEAVY_EQUIPMENT_PRINT') OR define('PERMISSION_HEAVY_EQUIPMENT_PRINT', 'heavy-equipment-print');

// Heavy Equipment Entry Permit
defined('PERMISSION_HEEP_VIEW') OR define('PERMISSION_HEEP_VIEW', 'heep-view');
defined('PERMISSION_HEEP_CREATE') OR define('PERMISSION_HEEP_CREATE', 'heep-create');
defined('PERMISSION_HEEP_EDIT') OR define('PERMISSION_HEEP_EDIT', 'heep-edit');
defined('PERMISSION_HEEP_DELETE') OR define('PERMISSION_HEEP_DELETE', 'heep-delete');

// Master Target
defined('PERMISSION_TARGET_VIEW') OR define('PERMISSION_TARGET_VIEW', 'target-view');
defined('PERMISSION_TARGET_CREATE') OR define('PERMISSION_TARGET_CREATE', 'target-create');
defined('PERMISSION_TARGET_EDIT') OR define('PERMISSION_TARGET_EDIT', 'target-edit');
defined('PERMISSION_TARGET_DELETE') OR define('PERMISSION_TARGET_DELETE', 'target-delete');

// Master Item Compliance
defined('PERMISSION_ITEM_COMPLIANCE_VIEW') OR define('PERMISSION_ITEM_COMPLIANCE_VIEW', 'item-compliance-view');
defined('PERMISSION_ITEM_COMPLIANCE_CREATE') OR define('PERMISSION_ITEM_COMPLIANCE_CREATE', 'item-compliance-create');
defined('PERMISSION_ITEM_COMPLIANCE_EDIT') OR define('PERMISSION_ITEM_COMPLIANCE_EDIT', 'item-compliance-edit');
defined('PERMISSION_ITEM_COMPLIANCE_DELETE') OR define('PERMISSION_ITEM_COMPLIANCE_DELETE', 'item-compliance-delete');

// Proof Heavy Equipment
defined('PERMISSION_PROOF_HEAVY_EQUIPMENT_VIEW') OR define('PERMISSION_PROOF_HEAVY_EQUIPMENT_VIEW', 'proof-heavy-equipment-view');
defined('PERMISSION_PROOF_HEAVY_EQUIPMENT_PRINT') OR define('PERMISSION_PROOF_HEAVY_EQUIPMENT_PRINT', 'proof-heavy-equipment-print');

// Opname Space permission
defined('PERMISSION_OPNAME_SPACES_VIEW') OR define('PERMISSION_OPNAME_SPACES_VIEW', 'opname-spaces-view');
defined('PERMISSION_OPNAME_SPACES_CREATE') OR define('PERMISSION_OPNAME_SPACES_CREATE', 'opname-spaces-create');
defined('PERMISSION_OPNAME_SPACES_DELETE') OR define('PERMISSION_OPNAME_SPACES_DELETE', 'opname-spaces-delete');
defined('PERMISSION_OPNAME_SPACES_EDIT') OR define('PERMISSION_OPNAME_SPACES_EDIT', 'opname-spaces-edit');
defined('PERMISSION_OPNAME_SPACES_PRINT') OR define('PERMISSION_OPNAME_SPACES_PRINT', 'opname-spaces-print');
defined('PERMISSION_OPNAME_SPACES_PROCESS') OR define('PERMISSION_OPNAME_SPACES_PROCESS', 'opname-spaces-process');  
defined('PERMISSION_OPNAME_SPACES_RESULT') OR define('PERMISSION_OPNAME_SPACES_RESULT', 'opname-spaces-result'); 
defined('PERMISSION_OPNAME_SPACES_VALIDATE') OR define('PERMISSION_OPNAME_SPACES_VALIDATE', 'opname-spaces-validate');
defined('PERMISSION_OPNAME_SPACES_PRINT_RESULT') OR define('PERMISSION_OPNAME_SPACES_PRINT_RESULT', 'opname-spaces-print-result');
defined('PERMISSION_OPNAME_SPACES_ACCESS') OR define('PERMISSION_OPNAME_SPACES_ACCESS', 'opname-spaces-access');

//
defined('PERMISSION_ATTACHMENT_PHOTO_VIEW') OR define('PERMISSION_ATTACHMENT_PHOTO_VIEW', 'attachment-photo-view');
defined('PERMISSION_ATTACHMENT_PHOTO_CREATE') OR define('PERMISSION_ATTACHMENT_PHOTO_CREATE', 'attachment-photo-create');
defined('PERMISSION_ATTACHMENT_PHOTO_EDIT') OR define('PERMISSION_ATTACHMENT_PHOTO_EDIT', 'attachment-photo-edit');
defined('PERMISSION_ATTACHMENT_PHOTO_DELETE') OR define('PERMISSION_ATTACHMENT_PHOTO_DELETE', 'attachment-photo-delete');

//
defined('PERMISSION_ATTACHMENT_PHOTO_VIEW') OR define('PERMISSION_ATTACHMENT_PHOTO_VIEW', 'attachment-photo-view');
defined('PERMISSION_ATTACHMENT_PHOTO_CREATE') OR define('PERMISSION_ATTACHMENT_PHOTO_CREATE', 'attachment-photo-create');
defined('PERMISSION_ATTACHMENT_PHOTO_EDIT') OR define('PERMISSION_ATTACHMENT_PHOTO_EDIT', 'attachment-photo-edit');
defined('PERMISSION_ATTACHMENT_PHOTO_DELETE') OR define('PERMISSION_ATTACHMENT_PHOTO_DELETE', 'attachment-photo-delete');

defined('PERMISSION_SCAN_QR_DATA') OR define('PERMISSION_SCAN_QR_DATA', 'scan-qr-data');
defined('PERMISSION_TRACKING_STATUS_DATA') OR define('PERMISSION_TRACKING_STATUS_DATA', 'tracking-status-data');

// Master Complain KPI
defined('PERMISSION_COMPLAIN_KPI_VIEW') OR define('PERMISSION_COMPLAIN_KPI_VIEW', 'complain-kpi-view');
defined('PERMISSION_COMPLAIN_KPI_EDIT') OR define('PERMISSION_COMPLAIN_KPI_EDIT', 'complain-kpi-edit');

defined('ROLE_ADMINISTRATOR') OR define('ROLE_ADMINISTRATOR', 'Administrator');
defined('ROLE_CUSTOM') OR define('ROLE_CUSTOM', 'Custom');
defined('ROLE_MANAGER') OR define('ROLE_MANAGER', 'Manager');
defined('ROLE_SUPERVISOR') OR define('ROLE_SUPERVISOR', 'Supervisor');
defined('ROLE_OPERATIONAL') OR define('ROLE_OPERATIONAL', 'Operational');
defined('ROLE_SECURITY') OR define('ROLE_SECURITY', 'Security');
defined('ROLE_TALLY') OR define('ROLE_TALLY', 'Tally');
defined('ROLE_SUPPLIER') OR define('ROLE_SUPPLIER', 'Supplier');
defined('ROLE_CUSTOMER') OR define('ROLE_CUSTOMER', 'Customer');
