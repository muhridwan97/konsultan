<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Branch</h3>
    </div>
    <form action="<?= site_url('branch/update/' . $branch['id']) ?>" role="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>


            <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                <label for="branch">Branch Name</label>
                <input type="text" class="form-control" id="branch" name="branch"
                       placeholder="Enter branch name"
                       required maxlength="50" value="<?= set_value('branch', $branch['branch']) ?>">
                <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('address') == '' ?: 'has-error'; ?>">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address"
                       placeholder="Enter address name"
                       required maxlength="300" value="<?= set_value('address', $branch['address']) ?>">
                <?= form_error('address', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('pic') == '' ?: 'has-error'; ?>">
                <label for="pic">PIC Name</label>
                <select class="form-control select2" required name="pic" id="pic"
                    data-placeholder="Select PIC name" style="width: 100%">
                    <option value=""></option>
                    <?php foreach($employees AS $employee): ?>
                        <option value="<?= $employee['id'] ?>" <?= set_select('pic', $employee['id'], $employee['id'] == $branch['pic']) ?>>
                            <?= $employee['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('pic', '<span class="help-block">', '</span>'); ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('contact') == '' ?: 'has-error'; ?>">
                        <label for="contact">Branch Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact"
                               placeholder="Branch contact or phone"
                               required maxlength="50" value="<?= set_value('contact', $branch['contact']) ?>">
                        <?= form_error('contact', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('email') == '' ?: 'has-error'; ?>">
                        <label for="email">Branch Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Branch email address"
                               required maxlength="50" value="<?= set_value('email', $branch['email']) ?>">
                        <?= form_error('email', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group <?= form_error('dashboard_status') == '' ?: 'has-error'; ?>">
                        <label for="with-do">Dashboard Status</label>
                        <div class="row">
                            <div class="col-sm-5">
                                <label for="visible">
                                    <input type="radio" id="visible" name="dashboard_status" value="1" <?= set_radio('dashboard_status', 1, $branch['dashboard_status'] == 1) ?>> Visible
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label for="not-Visible">
                                    <input type="radio" id="not-Visible" name="dashboard_status" value="0" <?= set_radio('dashboard_status', 1, $branch['dashboard_status'] == 0) ?>> Not Visible
                                </label>
                            </div>
                        </div>
                        <?= form_error('dashboard_status', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group <?= form_error('tally_check_approval') == '' ?: 'has-error'; ?>">
                        <label for="tally_check_approval_active">Tally Check Approval</label>
                        <div class="row">
                            <div class="col-sm-5">
                                <label for="tally_check_approval_active">
                                    <input type="radio" id="tally_check_approval_active" name="tally_check_approval" value="1"
                                        <?= set_radio('tally_check_approval', 1, $branch['tally_check_approval'] == 1) ?>> Active
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label for="tally_check_approval_inactive">
                                    <input type="radio" id="tally_check_approval_inactive" name="tally_check_approval" value="0"
                                        <?= set_radio('tally_check_approval', 1, $branch['tally_check_approval'] == 0) ?>> Inactive
                                </label>
                            </div>
                        </div>
                        <?= form_error('tally_check_approval', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('cycle_count_day') == '' ?: 'has-error'; ?>">
                        <label for="cycle_count_day">Branch Cycle Count Day</label>
                         <input type="number" class="form-control" id="cycle_count_day" name="cycle_count_day" min="0" placeholder="Branch  cycle count days" required maxlength="50" value="<?= set_value('cycle_count_day', $branch['cycle_count_day']) ?>">
                        <?= form_error('cycle_count_day', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('cycle_count_goods') == '' ?: 'has-error'; ?>">
                        <label for="cycle_count_goods">Branch Cycle Count Goods</label>
                        <input type="number" class="form-control" id="cycle_count_goods" name="cycle_count_goods" min="0" placeholder="Branch cycle count goods" required maxlength="500" value="<?= set_value('cycle_count_goods', $branch['cycle_count_goods']) ?>">
                        <?= form_error('cycle_count_goods', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('cycle_count_container') == '' ?: 'has-error'; ?>">
                        <label for="cycle_count_container">Branch Cycle Count Container</label>
                        <input type="number" class="form-control" id="cycle_count_container" name="cycle_count_container" min="0" placeholder="Branch cycle count goods" required maxlength="500" value="<?= set_value('cycle_count_container', $branch['cycle_count_container']) ?>">
                        <?= form_error('cycle_count_container', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('opname_day_name') == '' ?: 'has-error'; ?>">
                        <label for="opname_day_name">Names of the Opname Day</label>
                        <select class="form-control select2" required name="opname_day_name" id="opname_day_name"
                                data-placeholder="Select name of the opname day" style="width: 100%">
                            <option value=""></option>
                            <?php foreach($get_days AS $day): ?>
                            <option value="<?= $day ?>" <?= set_select('opname_day_name', $day, $branch['opname_day_name'] == $day) ?>><?= $day ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('opname_day_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('opname_day') == '' ?: 'has-error'; ?>">
                        <label for="opname_day">Branch Opname Day</label>
                        <input type="number" class="form-control" id="opname_day" name="opname_day" min="0" placeholder="Branch opname day" required maxlength="500" value="<?= set_value('opname_day', $branch['opname_day']) ?>">
                        <?= form_error('opname_day', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('opname_pending_day') == '' ?: 'has-error'; ?>">
                        <label for="opname_pending_day">Branch Opname Pending Day</label>
                        <input type="number" class="form-control" id="opname_pending_day" name="opname_pending_day" min="0" placeholder="Branch opname pending day" required maxlength="500" value="<?= set_value('opname_pending_day', $branch['opname_pending_day']) ?>">
                        <?= form_error('opname_pending_day', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('email_compliance') == '' ?: 'has-error'; ?>">
                        <label for="email_compliance">Email Compliance</label>
                        <input type="email" class="form-control" multiple id="email_compliance" name="email_compliance"
                               placeholder="Email compliance" required value="<?= set_value('email_compliance', $branch['email_compliance']) ?>">
                        <?= form_error('email_compliance', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                 <div class="col-md-6">
                    <div class="form-group <?= form_error('email_operational') == '' ?: 'has-error'; ?>">
                        <label for="email_operational">Email Operational</label>
                        <input type="email" class="form-control" multiple id="email_operational" name="email_operational"
                               placeholder="Email operational" required value="<?= set_value('email_operational', $branch['email_operational']) ?>">
                        <?= form_error('email_operational', '<span class="help-block">', '</span>'); ?>  
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('email_support') == '' ?: 'has-error'; ?>">
                        <label for="email_support">Email Support</label>
                        <input type="email" class="form-control" multiple id="email_support" name="email_support"
                               placeholder="Email Support" required value="<?= set_value('email_support', $branch['email_support']) ?>">
                        <?= form_error('email_support', '<span class="help-block">', '</span>'); ?>  
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                     <div class="form-group <?= form_error('branch_type') == '' ?: 'has-error'; ?>">
                        <label for="branch_type">Branch Type</label>
                        <select class="form-control select2" required name="branch_type" id="branch_type"
                                data-placeholder="Select PLB Type" style="width: 100%" >
                            <option value=""></option>
                            <option value="PLB" <?= set_select('branch_type', 'PLB', $branch['branch_type'] == 'PLB') ?>>
                                PLB
                            </option>
                            <option value="TPP" <?= set_select('branch_type', 'TPP', $branch['branch_type'] == 'TPP') ?>>
                                TPP
                            </option>
                            <option value="GU" <?= set_select('branch_type', 'GU', $branch['branch_type'] == 'GU') ?>>
                                GU
                            </option>
                        </select>
                        <?= form_error('branch_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('whatsapp_group') == '' ?: 'has-error'; ?>">
                        <label for="whatsapp_group">Whatsapp Group</label>
                        <input type="text" class="form-control" id="whatsapp_group" name="whatsapp_group"
                               placeholder="Enter whatsapp group number"
                               value="<?= set_value('whatsapp_group', $branch['whatsapp_group']) ?>">
                        <?= form_error('whatsapp_group', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('do_notif') == '' ?: 'has-error'; ?>">
                        <label for="do_notif">Number of days before Expired DO</label>
                        <input type="number" class="form-control" id="do_notif" name="do_notif" min="0" placeholder="Number of days for DO Notification" required maxlength="500" value="<?= set_value('do_notif', $branch['do_notif']) ?>">
                        <?= form_error('do_notif', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('whatsapp_group_security') == '' ?: 'has-error'; ?>">
                        <label for="whatsapp_group_security">WhatsApp Group Security</label>
                        <input type="text" class="form-control" id="whatsapp_group_security" name="whatsapp_group_security"
                               placeholder="Enter whatsapp group security number"
                               value="<?= set_value('whatsapp_group_security', $branch['whatsapp_group_security']) ?>">
                        <?= form_error('whatsapp_group_security', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('max_slot_tep') == '' ?: 'has-error'; ?>">
                        <label for="max_slot_tep">Number of max slot tep</label>
                        <input type="number" class="form-control" id="max_slot_tep" name="max_slot_tep" min="0" placeholder="Number of max slot tep" value="<?= set_value('max_slot_tep', $branch['max_slot_tep']) ?>">
                        <?= form_error('max_slot_tep', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('branch_vms') == '' ?: 'has-error'; ?>">
                        <label for="branch_vms">Relate Branch VMS</label>
                        <select class="form-control select2" name="branch_vms" id="branch_vms"
                                data-placeholder="Select Relate Branch VMS" style="width: 100%">
                            <option value=""></option>
                            <?php foreach($branch_vms AS $branch_vms): ?>
                            <option value="<?= $branch_vms['id'] ?>" <?= set_select('branch_vms', $branch['id'], $branch['id_branch_vms'] == $branch_vms['id']) ?>><?= $branch_vms['branch'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('branch_vms', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('branch_hr') == '' ?: 'has-error'; ?>">
                        <label for="branch_hr">Relate Branch HR</label>
                        <select class="form-control select2" name="branch_hr" id="branch_hr"
                                data-placeholder="Select Relate Branch HR" style="width: 100%">
                            <option value=""></option>
                            <?php foreach($branch_hr AS $branch_hr): ?>
                            <option value="<?= $branch_hr['id'] ?>" <?= set_select('branch_hr', $branch['id'], $branch['id_branch_hr'] == $branch_hr['id']) ?>><?= $branch_hr['location'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('branch_hr', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group <?= form_error('stock_pallet') == '' ?: 'has-error'; ?>">
                        <label for="stock_pallet">Stock Pallet</label>
                        <input type="number" class="form-control" id="stock_pallet" name="stock_pallet" min="0" placeholder="Number of Stock Pallet" value="<?= set_value('stock_pallet', $branch['stock_pallet']) ?>">
                        <?= form_error('stock_pallet', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group <?= form_error('initial_pallet') == '' ?: 'has-error'; ?>">
                        <label for="initial_pallet">Initial Pallet Stock</label>
                        <input type="number" class="form-control" id="initial_pallet" name="initial_pallet" min="0" placeholder="Number of Initial Pallet Stock" value="<?= set_value('initial_pallet', $branch['initial_pallet']) ?>">
                        <?= form_error('initial_pallet', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('qr_code_status') == '' ?: 'has-error'; ?>">
                        <label for="qr_code_status">QR Code Status</label>
                        <div class="row">
                            <div class="col-sm-3">
                                <label for="visible">
                                    <input type="radio" id="visible" name="qr_code_status" value="1" <?= set_radio('qr_code_status', 1, $branch['qr_code_status'] == 1) ?>> Visible
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label for="not-Visible">
                                    <input type="radio" id="not-Visible" name="qr_code_status" value="0" <?= set_radio('qr_code_status', 0, $branch['qr_code_status'] == 0) ?>> Not Visible
                                </label>
                            </div>
                        </div>
                        <?= form_error('qr_code_status', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('qr_code_status') == '' ?: 'has-error'; ?>">
                        <label for="qr_code_status">CSO Name</label>
                        <select class="form-control select2" name="cso" id="cso"
                                data-placeholder="Select CSO" style="width: 100%">
                            <option value=""></option>
                            <?php foreach($dataCSO AS $cso): ?>
                            <option value="<?= $cso['id'] ?>" <?= set_select('cso', $cso['id'], $cso['id'] == $branch['id_cso']) ?>><?= $cso['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('cso', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('max_time_request') == '' ?: 'has-error'; ?>">
                        <label for="max_time_request">Max Time Request</label>
                        <input type="time" class="form-control" id="max_time_request" name="max_time_request" placeholder="Max time request picker" value="<?= set_value('max_time_request', $branch['max_time_request']) ?>">
                        <?= form_error('max_time_request', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
             <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">

                <label for="description">Branch Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Branch description"
                          required maxlength="500"><?= set_value('description', $branch['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">KPI Inbound</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group <?= form_error('kpi_inbound_do') == '' ?: 'has-error'; ?>">
                                <label for="kpi_inbound_do">DO</label>
                                <input type="number" class="form-control" id="kpi_inbound_do" name="kpi_inbound_do" min="0" placeholder="Inbound DO" value="<?= set_value('kpi_inbound_do', $branch['kpi_inbound_do']) ?>" >
                                <?= form_error('kpi_inbound_do', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group <?= form_error('kpi_inbound_sppb') == '' ?: 'has-error'; ?>">
                                <label for="kpi_inbound_sppb">SPPB</label>
                                <input type="number" class="form-control" id="kpi_inbound_sppb" name="kpi_inbound_sppb" min="0" placeholder="Inbound SPPB" value="<?= set_value('kpi_inbound_sppb', $branch['kpi_inbound_sppb']) ?>" >
                                <?= form_error('kpi_inbound_sppb', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group <?= form_error('kpi_inbound_gate_in') == '' ?: 'has-error'; ?>">
                                <label for="kpi_inbound_gate_in">Gate In</label>
                                <input type="number" class="form-control" id="kpi_inbound_gate_in" name="kpi_inbound_gate_in" min="0" placeholder="Inbound Gate In" value="<?= set_value('kpi_inbound_gate_in', $branch['kpi_inbound_gate_in']) ?>" >
                                <?= form_error('kpi_inbound_gate_in', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group <?= form_error('kpi_inbound_stripping') == '' ?: 'has-error'; ?>">
                                <label for="kpi_inbound_stripping">Stripping</label>
                                <input type="number" class="form-control" id="kpi_inbound_stripping" name="kpi_inbound_stripping" min="0" placeholder="Inbound Stripping" value="<?= set_value('kpi_inbound_stripping', $branch['kpi_inbound_stripping']) ?>" >
                                <?= form_error('kpi_inbound_stripping', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Branch</button>
        </div>
    </form>
</div>