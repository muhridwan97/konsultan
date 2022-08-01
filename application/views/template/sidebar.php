<?php
$segment1 = $this->uri->segment(1);
$segment2 = $this->uri->segment(2);
if ($segment1 == 'p') {
    $segment1 = $this->uri->segment(3);
    $segment2 = $this->uri->segment(4);
}
?>

<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= base_url('assets/app/img/avatar/no-avatar.jpg') ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?= UserModel::authenticatedUserData('name') ?></p>
                <a href="<?= site_url('account') ?>">
                    <?= UserModel::authenticatedUserData('username') ?>
                </a>
            </div>
        </div>

        <form action="<?= site_url('search') ?>" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                    <button type="submit" id="search-btn" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <?php if (!empty(get_active_branch())) : ?>
                <li id="current-branch" class="header bg-light-blue" data-clickable="true" data-url="<?= site_url('gateway', false) ?>" style="font-weight: bold; color: white;">
                    BRANCH : <?= get_active_branch('branch', true) ?>
                </li>
            <?php endif; ?>

            <li class="header">APPLICATION</li>

            <li class="<?= $segment1 != 'dashboard' ?: 'active' ?>">
                <a href="<?= site_url('dashboard') ?>">
                    <i class="fa ion-speedometer"></i> <span>Dashboard</span>
                </a>
            </li>

            <?php
            $masterMenu = [
                'role', 'user', 'branch', 'warehouse', 'container', 'goods', 'customer', 'customer-storage-capacity', 'module', 'eseal', 'vehicle',
                'position-type', 'position', 'unit', 'conversion', 'permission', 'document-type', 'booking-type', 'checklist-type', 'checklist', 'complain-kpi', 'complain-category',
                'handling-type', 'component', 'component_price', 'extension-field', 'overtime', 'complain-category', 'service-hour', 'heavy_equipment',
                'target', 'item-compliance', 'attachment-photo', 'operation-cut-off'
            ];
            $masterMenuAuthorized = [
                'role-view', 'user-view', 'branch-view', 'warehouse-view', 'container-view', 'position-type-view', 'service-hour-view', 'customer-storage-capacity-view',
                'goods-view', 'customer-view', 'checklist-type-view', 'checklist-view', 'customer-view', 'supplier-view', 'position-view', 'unit-view', 'conversion-view', 'permission-view', 'document-type-view', 'booking-type-view', 'extension-field-view', 'overtime-view', 'complain-kpi-view', 'complain-category-view',
                'item-compliance-view', 'attachment-photo-view', PERMISSION_OPERATION_CUT_OFF_VIEW
            ];
            $masterIsVisible = false;
            foreach ($masterMenuAuthorized as $masterAuth) {
                if (AuthorizationModel::isAuthorized($masterAuth)) {
                    $masterIsVisible = true;
                    break;
                }
            }
            ?>
            <?php if ($masterIsVisible) : ?>
                <li class="treeview <?= !in_array($segment1, $masterMenu) ?: 'active' ?>">
                    <a href="#">
                        <i class="fa ion-grid"></i> <span>Master</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_PERMISSION_VIEW)) : ?>
                            <li class="<?= $segment1 != 'permission' ?: 'active' ?>">
                                <a href="<?= site_url('permission') ?>">
                                    <i class="fa fa-circle-o"></i> Permissions
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_ROLE_VIEW)) : ?>
                            <li class="<?= $segment1 != 'role' ?: 'active' ?>">
                                <a href="<?= site_url('role') ?>">
                                    <i class="fa fa-circle-o"></i> Roles
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_USER_VIEW)) : ?>
                            <li class="<?= $segment1 != 'user' ?: 'active' ?>">
                                <a href="<?= site_url('user') ?>">
                                    <i class="fa fa-circle-o"></i> Users
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_VIEW)) : ?>
                            <li class="<?= $segment1 != 'customer' ?: 'active' ?>">
                                <a href="<?= site_url('customer') ?>">
                                    <i class="fa fa-circle-o"></i> Customer
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_TYPE_VIEW)) : ?>
                            <li class="<?= $segment1 != 'handling-type' ?: 'active' ?>">
                                <a href="<?= site_url('handling-type') ?>">
                                    <i class="fa fa-circle-o"></i> Handling Types
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_DOCUMENT_TYPE_VIEW)) : ?>
                            <li class="<?= $segment1 != 'document-type' ?: 'active' ?>">
                                <a href="<?= site_url('document-type') ?>">
                                    <i class="fa fa-circle-o"></i> Document Types
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_EXTENSION_FIELD_VIEW)) : ?>
                            <li class="<?= $segment1 != 'extension-field' ?: 'active' ?>">
                                <a href="<?= site_url('extension-field') ?>">
                                    <i class="fa fa-circle-o"></i> Extension Field
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_TYPE_VIEW)) : ?>
                            <li class="<?= $segment1 != 'booking-type' ?: 'active' ?>">
                                <a href="<?= site_url('booking-type') ?>">
                                    <i class="fa fa-circle-o"></i> Booking Types
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php
            $uploadMenu = ['upload', 'upload_document', 'upload_document_file', 'file_manager'];
            $uploadMenuAuthorized = [PERMISSION_UPLOAD_VIEW, PERMISSION_UPLOAD_VALIDATE];
            $uploadIsVisible = false;
            foreach ($uploadMenuAuthorized as $uploadAuth) {
                if (AuthorizationModel::isAuthorized($uploadAuth)) {
                    $uploadIsVisible = true;
                    break;
                }
            }
            ?>
            <?php if ($uploadIsVisible) : ?>
                <li class="treeview <?= !in_array($segment1, $uploadMenu) ?: 'active' ?>">
                    <a href="#">
                        <i class="fa ion-ios-cloud-upload-outline"></i> <span>Upload</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VIEW)) : ?>
                            <li class="<?= !in_array($segment1, ['upload', 'upload_document', 'upload_document_file']) ?: 'active' ?>">
                                <a href="<?= site_url('upload') ?>">
                                    <i class="fa fa-circle-o"></i> Document
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)) : ?>
                            <li class="<?= $segment1 != 'file_manager' ?: 'active' ?>">
                                <a href="<?= site_url('file_manager') ?>">
                                    <i class="fa fa-circle-o"></i> File Manager
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            
            <?php
            $utilityMenu = ['raw_contact', 'news', 'module_explorer', 'calculator', 'backup', 'synchronize', 'logs', 'system-log', 'token', 'whatsapp_dialog'];
            $utilityMenuAuthorized = [PERMISSION_SETTING_EDIT,PERMISSION_WHATSAPP_DIALOG];
            $utilityIsVisible = false;
            foreach ($utilityMenuAuthorized as $utilityAuth) {
                if (AuthorizationModel::isAuthorized($utilityAuth)) {
                    $utilityIsVisible = true;
                    break;
                }
            }
            ?>
            <?php if ($utilityIsVisible) : ?>
                <li class="treeview <?= !in_array($segment1, $utilityMenu) ?: 'active' ?>">
                    <a href="#">
                        <i class="fa ion-hammer"></i> <span>Utility</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_SETTING_EDIT)) : ?>
                        <li class="<?= $segment1 != 'raw_contact' ?: 'active' ?>">
                            <a href="<?= site_url('raw_contact') ?>">
                                <i class="fa fa-circle-o"></i> Raw Contact
                            </a>
                        </li>
                        <li class="<?= $segment1 != 'module_explorer' ?: 'active' ?>">
                            <a href="<?= site_url('module_explorer') ?>">
                                <i class="fa fa-circle-o"></i> Browse Module Data
                            </a>
                        </li>
                        <li class="<?= $segment1 != 'calculator' ?: 'active' ?>">
                            <a href="<?= site_url('calculator') ?>">
                                <i class="fa fa-circle-o"></i> Converter Calculator
                            </a>
                        </li>
                        <li class="<?= $segment1 != 'backup' ?: 'active' ?>">
                            <a href="<?= site_url('backup') ?>">
                                <i class="fa fa-circle-o"></i> System Backup
                            </a>
                        </li>
                        <li class="<?= $segment1 != 'synchronize' ?: 'active' ?>">
                            <a href="<?= site_url('synchronize') ?>">
                                <i class="fa fa-circle-o"></i> Synchronize
                            </a>
                        </li>
                        <li class="<?= $segment1 != 'logs' ?: 'active' ?>">
                            <a href="<?= site_url('logs') ?>">
                                <i class="fa fa-circle-o"></i> Logs History
                            </a>
                        </li>
                        <li class="<?= $segment1 != 'system-log' ?: 'active' ?>">
                            <a href="<?= site_url('system-log') ?>">
                                <i class="fa fa-circle-o"></i> System Log
                            </a>
                        </li>
                        <li class="<?= $segment1 != 'token' ?: 'active' ?>">
                            <a href="<?= site_url('token') ?>">
                                <i class="fa fa-circle-o"></i> Token & Key
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WHATSAPP_DIALOG)) : ?>
                        <li class="<?= $segment1 != 'whatsapp_dialog' ?: 'active' ?>">
                            <a href="<?= site_url('whatsapp_dialog') ?>">
                                <i class="fa fa-circle-o"></i> WhatsApp Dialog
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>


            <?php
            $settingMenu = ['setting', 'report-schedule', 'security-photo-type'];
            $settingMenuAuthorized = [PERMISSION_SETTING_EDIT, PERMISSION_REPORT_SCHEDULE_VIEW];
            $settingIsVisible = false;
            foreach ($settingMenuAuthorized as $settingAuth) {
                if (AuthorizationModel::isAuthorized($settingAuth)) {
                    $settingIsVisible = true;
                    break;
                }
            }
            ?>
            <?php if ($settingIsVisible) : ?>
            <li class="treeview <?= !in_array($segment1, $settingMenu) ?: 'active' ?>">
                <a href="#">
                    <i class="fa ion-ios-settings-strong"></i> <span>Setting</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_SETTING_EDIT)) : ?>
                        <li class="<?= $segment1 != 'setting' ?: 'active' ?>">
                            <a href="<?= site_url('setting') ?>">
                                <i class="fa fa-circle-o"></i> General Setting
                            </a>
                        </li>
                        <li class="<?= $segment1 != 'security-photo-type' ?: 'active' ?>">
                            <a href="<?= site_url('security-photo-type') ?>">
                                <i class="fa fa-circle-o"></i> Security Photo Type
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_REPORT_SCHEDULE_VIEW)) : ?>
                        <li class="<?= $segment1 != 'report-schedule' ?: 'active' ?>">
                            <a href="<?= site_url('report-schedule') ?>">
                                <i class="fa fa-circle-o"></i> <span>Report Schedule</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized('account-edit')) : ?>
                <?php
                if ($this->config->item('sso_enable')) {
                    $accountUrl = sso_url('account');
                } else {
                    $accountUrl = site_url('account');
                }
                ?>
                <li class="<?= $segment1 != 'account' ?: 'active' ?>">
                    <a href="<?= $accountUrl ?>">
                        <i class="fa ion-person"></i> <span>Account</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="<?= $segment1 != 'client_area' ?: 'active' ?>">
                <a href="<?= site_url('client_area', false) ?>">
                    <i class="fa ion-person-stalker"></i> <span>Public Client Area</span>
                </a>
            </li>

            <li class="<?= $segment1 != 'help' ?: 'active' ?>">
                <a href="<?= site_url('help') ?>">
                    <i class="fa ion-help-circled"></i> <span>Help and Contact</span>
                </a>
            </li>

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
