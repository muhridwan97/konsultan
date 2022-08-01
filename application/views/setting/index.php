<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Settings</h3>
    </div>
    <form action="<?= site_url('setting/update') ?>" role="form" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Application Basic Info
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('app_name') == '' ?: 'has-error'; ?>">
                                <label for="app_name">Application Name</label>
                                <input type="text" class="form-control" id="app_name" name="app_name"
                                       placeholder="Application name" required maxlength="50"
                                       value="<?= set_value('app_name', $settings['app_name']) ?>">
                                <?= form_error('app_name', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('app_version') == '' ?: 'has-error'; ?>">
                                <label for="app_version">Application Name</label>
                                <input type="text" class="form-control" id="app_version" name="app_version"
                                       placeholder="Application version" required maxlength="10"
                                       value="<?= set_value('app_version', $settings['app_version']) ?>">
                                <?= form_error('app_version', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group <?= form_error('meta_url') == '' ?: 'has-error'; ?>">
                        <label for="meta_url">Meta Url</label>
                        <input type="url" class="form-control" id="meta_url" name="meta_url"
                               placeholder="Meta url" required maxlength="100"
                               value="<?= set_value('meta_url', $settings['meta_url']) ?>">
                        <?= form_error('meta_url', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group <?= form_error('meta_keywords') == '' ?: 'has-error'; ?>">
                        <label for="meta_keywords">Meta Keyword</label>
                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                               placeholder="Meta keywords" required maxlength="300"
                               value="<?= set_value('meta_url', $settings['meta_keywords']) ?>">
                        <?= form_error('meta_keywords', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group <?= form_error('meta_description') == '' ?: 'has-error'; ?>">
                        <label for="meta_description">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" placeholder="Description"
                                  required maxlength="500"><?= set_value('meta_description', $settings['meta_description']) ?></textarea>
                        <?= form_error('meta_description', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group <?= form_error('meta_author') == '' ?: 'has-error'; ?>">
                        <label for="meta_author">Meta Author</label>
                        <input type="text" class="form-control" id="meta_author" name="meta_author"
                               placeholder="Meta author" required maxlength="50"
                               value="<?= set_value('meta_url', $settings['meta_author']) ?>">
                        <?= form_error('meta_author', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Emails and Contacts
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('email_bug_report') == '' ?: 'has-error'; ?>">
                                <label for="email_bug_report">Email Bug Report</label>
                                <input type="email" class="form-control" id="email_bug_report" name="email_bug_report"
                                       placeholder="Email bug report" required maxlength="50"
                                       value="<?= set_value('email_bug_report', $settings['email_bug_report']) ?>">
                                <?= form_error('email_bug_report', '<span class="help-block">', '</span>'); ?>
                                <input type="text" class="form-control mt10" id="admin_developer" name="admin_developer"
                                       placeholder="Admin developer" required maxlength="50" aria-label="admin-developer"
                                       value="<?= set_value('admin_developer', $settings['admin_developer']) ?>">
                                <?= form_error('admin_developer', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('email_support') == '' ?: 'has-error'; ?>">
                                <label for="email_support">Email Support</label>
                                <input type="email" multiple class="form-control" id="email_support" name="email_support"
                                       placeholder="Email support" required maxlength="200"
                                       value="<?= set_value('email_support', $settings['email_support']) ?>">
                                <?= form_error('email_support', '<span class="help-block">', '</span>'); ?>
                                <input type="text" class="form-control mt10" id="admin_support" name="admin_support"
                                       placeholder="Admin operational" required maxlength="50" aria-label="admin-support"
                                       value="<?= set_value('admin_operational', $settings['admin_operational']) ?>">
                                <?= form_error('admin_operational', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('operations_administrator_email') == '' ?: 'has-error'; ?>">
                                <label for="operations_administrator_email">Admin & Email Operational</label>
                                <input type="email" multiple class="form-control" id="operations_administrator_email" name="operations_administrator_email"
                                       placeholder="Email operational" required maxlength="200"
                                       value="<?= set_value('operations_administrator_email', $settings['operations_administrator_email']) ?>">
                                <?= form_error('operations_administrator_email', '<span class="help-block">', '</span>'); ?>
                                <input type="text" class="form-control mt10" id="admin_operational" name="admin_operational"
                                       placeholder="Admin operational" required maxlength="50"
                                       value="<?= set_value('admin_operational', $settings['admin_operational']) ?>">
                                <?= form_error('admin_operational', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group <?= form_error('email_finance') == '' ?: 'has-error'; ?>">
                                <label for="email_finance">Admin & Email Finance</label>
                                <input type="email" multiple class="form-control" id="email_finance" name="email_finance"
                                       placeholder="Email finance" required maxlength="200"
                                       value="<?= set_value('email_finance', $settings['email_finance']) ?>">
                                <?= form_error('admin_finance', '<span class="help-block">', '</span>'); ?>
                                <input type="text" class="form-control mt10" id="admin_finance" name="admin_finance"
                                       placeholder="Admin finance" required maxlength="50" aria-label="admin-finance"
                                       value="<?= set_value('admin_finance', $settings['admin_finance']) ?>">
                                <?= form_error('admin_finance', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('email_compliance') == '' ?: 'has-error'; ?>">
                                 <label for="email_compliance">Admin & Email Compliance</label>
                                 <input type="email" class="form-control" multiple id="email_compliance" name="email_compliance"
                                       placeholder="Email compliance" required value="<?= set_value('email_compliance', $settings['email_compliance']) ?>">
                                <?= form_error('email_compliance', '<span class="help-block">', '</span>'); ?>
                                <input type="text" class="form-control mt10" id="admin_compliance" name="admin_compliance"
                                       placeholder="Admin compliance" required maxlength="50" aria-label="admin-compliance"
                                       value="<?= set_value('admin_compliance', $settings['admin_compliance']) ?>">
                                <?= form_error('admin_compliance', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('email_finance2') == '' ?: 'has-error'; ?>">
                                <label for="email_finance2">Admin & Email Finance <span style="color: gray;font-size:12px">(Email Finance For Lock)</span></label>
                                <input type="email" multiple class="form-control" id="email_finance2" name="email_finance2"
                                       placeholder="Email finance For Lock" required maxlength="200"
                                       value="<?= set_value('email_finance2', $settings['email_finance2']) ?>">
                                <?= form_error('admin_finance2', '<span class="help-block">', '</span>'); ?>
                                <input type="text" class="form-control mt10" id="admin_finance2" name="admin_finance2"
                                       placeholder="Admin finance For Lock" required maxlength="50" aria-label="admin-finance2"
                                       value="<?= set_value('admin_finance2', $settings['admin_finance2']) ?>">
                                <?= form_error('admin_finance2', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('email_finance3') == '' ?: 'has-error'; ?>">
                                <label for="email_finance3">Admin & Email Finance <span style="color: gray;font-size:12px">(Email draft customer)</span></label>
                                <input type="email" multiple class="form-control" id="email_finance3" name="email_finance3"
                                       placeholder="Email finance For Lock" required maxlength="200"
                                       value="<?= set_value('email_finance3', $settings['email_finance3']) ?>">
                                <?= form_error('admin_finance3', '<span class="help-block">', '</span>'); ?>
                                <input type="text" class="form-control mt10" id="admin_finance3" name="admin_finance3"
                                       placeholder="Admin finance For Lock" required maxlength="50"
                                       value="<?= set_value('admin_finance3', $settings['admin_finance3']) ?>">
                                <?= form_error('admin_finance3', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('email_finance3') == '' ?: 'has-error'; ?>">
                                <label for="email_finance3">Admin & Email Finance <span style="color: gray;font-size:12px">(Email draft customer)</span></label>
                                <input type="email" multiple class="form-control" id="email_finance3" name="email_finance3"
                                       placeholder="Email finance For Lock" required maxlength="200"
                                       value="<?= set_value('email_finance3', $settings['email_finance3']) ?>">
                                <?= form_error('admin_finance3', '<span class="help-block">', '</span>'); ?>
                                <input type="text" class="form-control mt10" id="admin_finance3" name="admin_finance3"
                                       placeholder="Admin finance For Lock" required maxlength="50"
                                       value="<?= set_value('admin_finance3', $settings['admin_finance3']) ?>">
                                <?= form_error('admin_finance3', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('whatsapp_group_submission') == '' ?: 'has-error'; ?>">
                                <label for="whatsapp_group_submission">WhatsApp Group Submission</label>
                                <input type="text" class="form-control" id="whatsapp_group_submission" name="whatsapp_group_submission" placeholder="Enter whatsapp group number" value="<?= set_value('whatsapp_group_submission',$settings['whatsapp_group_submission']) ?>">
                                <?= form_error('whatsapp_group_submission', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('whatsapp_group_transfer') == '' ?: 'has-error'; ?>">
                                <label for="whatsapp_group_transfer">WhatsApp Group Transfer</label>
                                <input type="text" class="form-control" id="whatsapp_group_transfer" name="whatsapp_group_transfer" placeholder="Enter whatsapp group number" value="<?= set_value('whatsapp_group_transfer',$settings['whatsapp_group_transfer']) ?>">
                                <?= form_error('whatsapp_group_transfer', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('whatsapp_group_admin') == '' ?: 'has-error'; ?>">
                                <label for="whatsapp_group_admin">WhatsApp Group Admin</label>
                                <input type="text" class="form-control" id="whatsapp_group_admin" name="whatsapp_group_admin" placeholder="Enter whatsapp_group number" value="<?= set_value('whatsapp_group_admin',$settings['whatsapp_group_admin']) ?>">
                                <?= form_error('whatsapp_group_admin', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('whatsapp_group_complain') == '' ?: 'has-error'; ?>">
                                <label for="whatsapp_group_complain">WhatsApp Group Complaint</label>
                                <input type="text" class="form-control" id="whatsapp_group_complain" name="whatsapp_group_complain" placeholder="Enter whatsapp group number" value="<?= set_value('whatsapp_group_complain', $settings['whatsapp_group_complain']) ?>">
                                <?= form_error('whatsapp_group_complain', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('whatsapp_group_management') == '' ?: 'has-error'; ?>">
                                <label for="whatsapp_group_management">WhatsApp Group Management</label>
                                <input type="text" class="form-control" id="whatsapp_group_management" name="whatsapp_group_management" placeholder="Enter whatsapp group number" value="<?= set_value('whatsapp_group_management', $settings['whatsapp_group_management']) ?>">
                                <?= form_error('whatsapp_group_management', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Handling Settings
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('default_inbound_handling') == '' ?: 'has-error'; ?>">
                                <label for="default_inbound_handling">Default Inbound Handling</label>
                                <select class="form-control select2" name="default_inbound_handling" id="default_inbound_handling" data-placeholder="Select default inbound handling" required>
                                    <option value=""></option>
                                    <?php foreach ($handlingTypes as $handlingType): ?>
                                        <option value="<?= $handlingType['id'] ?>" <?= set_select('default_inbound_handling', $handlingType['id'], $handlingType['id'] == $settings['default_inbound_handling']) ?>>
                                            <?= $handlingType['handling_type'] ?> (<?= $handlingType['category'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('default_inbound_handling', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('default_outbound_handling') == '' ?: 'has-error'; ?>">
                                <label for="default_outbound_handling">Default Outbound Handling</label>
                                <select class="form-control select2" name="default_outbound_handling" id="default_outbound_handling" data-placeholder="Select default outbound handling" required>
                                    <option value=""></option>
                                    <?php foreach ($handlingTypes as $handlingType): ?>
                                        <option value="<?= $handlingType['id'] ?>" <?= set_select('default_outbound_handling', $handlingType['id'], $handlingType['id'] == $settings['default_outbound_handling']) ?>>
                                            <?= $handlingType['handling_type'] ?> (<?= $handlingType['category'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('default_outbound_handling', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('default_moving_in_handling') == '' ?: 'has-error'; ?>">
                                <label for="default_moving_in_handling">Default Moving In Handling</label>
                                <select class="form-control select2" name="default_moving_in_handling" id="default_moving_in_handling" data-placeholder="Select default order trucking in handling" required>
                                    <option value=""></option>
                                    <?php foreach ($handlingTypes as $handlingType): ?>
                                        <option value="<?= $handlingType['id'] ?>" <?= set_select('default_moving_in_handling', $handlingType['id'], $handlingType['id'] == $settings['default_moving_in_handling']) ?>>
                                            <?= $handlingType['handling_type'] ?> (<?= $handlingType['category'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('default_moving_in_handling', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('default_moving_out_handling') == '' ?: 'has-error'; ?>">
                                <label for="default_moving_out_handling">Default Moving Out Handling</label>
                                <select class="form-control select2" name="default_moving_out_handling" id="default_moving_out_handling" data-placeholder="Select default order trucking out handling" required>
                                    <option value=""></option>
                                    <?php foreach ($handlingTypes as $handlingType): ?>
                                        <option value="<?= $handlingType['id'] ?>" <?= set_select('default_moving_out_handling', $handlingType['id'], $handlingType['id'] == $settings['default_moving_out_handling']) ?>>
                                            <?= $handlingType['handling_type'] ?> (<?= $handlingType['category'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('default_moving_out_handling', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('default_shifting_handling') == '' ?: 'has-error'; ?>">
                                <label for="default_shifting_handling">Default Shifting Handling</label>
                                <select class="form-control select2" name="default_shifting_handling" id="default_shifting_handling" data-placeholder="Select default handling for shifting" required>
                                    <option value=""></option>
                                    <?php foreach ($handlingTypes as $handlingType): ?>
                                        <option value="<?= $handlingType['id'] ?>" <?= set_select('default_shifting_handling', $handlingType['id'], $handlingType['id'] == $settings['default_shifting_handling']) ?>>
                                            <?= $handlingType['handling_type'] ?> (<?= $handlingType['category'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('default_shifting_handling', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('default_warehouse_receipt_handling') == '' ?: 'has-error'; ?>">
                                <label for="default_shifting_handling">Default Warehouse Receipt</label>
                                <select class="form-control select2" name="default_warehouse_receipt_handling" id="default_warehouse_receipt_handling" data-placeholder="Select default handling for warehouse receipt" required>
                                    <option value=""></option>
                                    <?php foreach ($handlingTypes as $handlingType): ?>
                                        <option value="<?= $handlingType['id'] ?>" <?= set_select('default_warehouse_receipt_handling', $handlingType['id'], $handlingType['id'] == $settings['default_warehouse_receipt_handling']) ?>>
                                            <?= $handlingType['handling_type'] ?> (<?= $handlingType['category'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('default_warehouse_receipt_handling', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Strict Mode
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('strict_upload') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="strict_upload" value="1" <?= set_checkbox('strict_upload', 1, $settings['strict_upload'] == 1) ?>>
                                        Strict Upload
                                    </label>
                                    <span class="help-block">Uploaded documents must be attached on every related transaction eg. booking.</span>
                                    <?= form_error('strict_upload', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('strict_booking') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="strict_booking" value="1" <?= set_checkbox('strict_booking', 1, $settings['strict_booking'] == 1) ?>>
                                        Strict Booking
                                    </label>
                                    <span class="help-block">Booking must be validated before proceed in other pages.</span>
                                    <?= form_error('strict_booking', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('strict_delivery_order') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="strict_delivery_order" value="1" <?= set_checkbox('strict_delivery_order', 1, $settings['strict_delivery_order'] == 1) ?>>
                                        Strict Delivery Order
                                    </label>
                                    <span class="help-block">User not allowed to custom booking package amount and units.</span>
                                    <?= form_error('strict_delivery_order', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('strict_safe_conduct') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="strict_safe_conduct" value="1" <?= set_checkbox('strict_safe_conduct', 1, $settings['strict_safe_conduct'] == 1) ?>>
                                        Strict Safe Conduct
                                    </label>
                                    <span class="help-block">Safe conduct not allowed to have duplicate data from booking or job.</span>
                                    <?= form_error('strict_safe_conduct', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('strict_print_limitation') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="strict_print_limitation" value="1" <?= set_checkbox('strict_print_limitation', 1, $settings['strict_print_limitation'] == 1) ?>>
                                        Strict Print Limit
                                    </label>
                                    <span class="help-block">User have once chance of printing document data in all menu.</span>
                                    <?= form_error('strict_print_limitation', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('strict_handling') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="strict_handling" value="1" <?= set_checkbox('strict_handling', 1, $settings['strict_handling'] == 1) ?>>
                                        Strict Handling
                                    </label>
                                    <span class="help-block">Handling data only fetched from stock rather than defined by user.</span>
                                    <?= form_error('strict_handling', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Invoice
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('invoice_handling_auto') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="invoice_handling_auto" value="1" <?= set_checkbox('invoice_handling_auto', 1, $settings['invoice_handling_auto'] == 1) ?>>
                                        Auto Create Handling Invoice
                                    </label>
                                    <span class="help-block">Automatic handling invoice when user request new handling data (check in gate).</span>
                                    <?= form_error('invoice_handling_auto', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('invoice_job_auto') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="invoice_job_auto" value="1" <?= set_checkbox('invoice_job_auto', 1, $settings['invoice_job_auto'] == 1) ?>>
                                        Auto Create Job Invoice
                                    </label>
                                    <span class="help-block">Automatic job invoice when work order has completed (check out gate).</span>
                                    <?= form_error('invoice_job_auto', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Locking Warehouse
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('lock_opname') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="lock_opname" value="1" <?= set_checkbox('lock_opname', 1, $settings['lock_opname'] == 1) ?>>
                                        Lock Opname
                                    </label>
                                    <span class="help-block">Lock tally and handling feature.</span>
                                    <?= form_error('lock_opname', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('lock_document_transaction') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="lock_document_transaction" value="1" <?= set_checkbox('lock_document_transaction', 1, $settings['lock_document_transaction'] == 1) ?>>
                                        Lock Document Transaction
                                    </label>
                                    <span class="help-block">Lock upload feature.</span>
                                    <?= form_error('lock_document_transaction', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('lock_warehouse_transaction') == '' ?: 'has-error'; ?>">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="minimal" name="lock_warehouse_transaction" value="1" <?= set_checkbox('lock_warehouse_transaction', 1, $settings['lock_warehouse_transaction'] == 1) ?>>
                                        Lock Document Transaction
                                    </label>
                                    <span class="help-block">Lock all transaction except master and invoice.</span>
                                    <?= form_error('lock_warehouse_transaction', '<span class="help-block">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Warehouse Receipt
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('warehouse_receipt_weight') == '' ?: 'has-error'; ?>">
                                <label for="warehouse_receipt_weight">Warehouse Receipt Weight (KG)</label>
                                <input type="number" class="form-control" id="warehouse_receipt_weight" name="warehouse_receipt_weight"
                                       placeholder="Value per warehouse receipt" required
                                       value="<?= set_value('warehouse_receipt_weight', $settings['warehouse_receipt_weight']) ?>">
                                <?= form_error('warehouse_receipt_weight', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Core Ops Position
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="position">Position</label>
                        <select class="form-control select2 select2-ajax" required=""
                                data-url="<?= site_url('setting/ajax_get_employee_position') ?>"
                                data-key-id="id" data-key-label="position"
                                name="positions[]" id="position"
                                data-placeholder="Select position" multiple>
                            <option value=""></option>
                            <?php foreach ($positions as $position): ?>
                                <option value="<?= $position['id_position'] ?>" selected>
                                    <?= $position['position'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Document Production Position
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="position">Position</label>
                        <select class="form-control select2 select2-ajax" required=""
                                data-url="<?= site_url('setting/ajax_get_employee_position') ?>"
                                data-key-id="id" data-key-label="position"
                                name="documentPositions[]" id="documentPosition"
                                data-placeholder="Select position" multiple>
                            <option value=""></option>
                            <?php foreach ($documentPositions as $documentPosition): ?>
                                <option value="<?= $documentPosition['id_position'] ?>" selected>
                                    <?= $documentPosition['position'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('password') == '' ?: 'has-error'; ?>">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Current password" required maxlength="50">
                <?= form_error('password', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Update Setting</button>
        </div>
    </form>
</div>