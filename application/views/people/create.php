<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Person</h3>
    </div>
    <form action="<?= site_url('people/save') ?>" class="form need-validation" method="post" id="form-people">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <?php $activeBranchId = get_active_branch('id') ?>
            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= $activeBranchId ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach (get_customer_branch() as $branch): ?>
                            <option value="<?= $branch['id'] ?>"<?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="<?= set_value('type') == 'CUSTOMER' ? 'col-md-3' : 'col-md-6' ?>" id="field-type">
                    <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                        <label for="type">Type</label>
                        <select class="form-control select2" name="type" id="type" data-placeholder="Select type" style="width: 100%" required>
                            <option value=""></option>
                            <?php foreach ($types as $key => $value): ?>
                                <?php $preType = isset($_GET['type']) && $key == $_GET['type'] ?>
                                <option value="<?= $key ?>" <?= set_select('type', $key, $preType) ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block">Group or category of person entity</span>
                        <?= form_error('type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-3" id="field-outbound-type" <?= set_value('type') == 'CUSTOMER' ? '' : 'style="display: none"' ?>>
                    <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                        <label for="outbound_type">Outbound Type</label>
                        <select class="form-control select2" name="outbound_type" id="outbound_type" data-placeholder="Select outbound type" style="width: 100%">
                            <option value="NOT SET">NOT SET</option>
                            <option value="CASH AND CARRY"<?= set_select('outbound_type', 'CASH AND CARRY') ?>>CASH AND CARRY</option>
                            <option value="ACCOUNT RECEIVABLE"<?= set_select('outbound_type', 'ACCOUNT RECEIVABLE') ?>>ACCOUNT RECEIVABLE</option>
                        </select>
                        <span class="help-block">How customer handle payment</span>
                        <?= form_error('type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6" id="field-no-person">
                    <div class="form-group <?= form_error('no_person') == '' ?: 'has-error'; ?>">
                        <label for="no_person">No Person</label>
                        <input type="text" class="form-control" id="no_person" name="no_person"
                               placeholder="Enter code person"
                               value="<?= set_value('no_person') ?>" pattern="[A-Z]{3}" required maxlength="3">
                        <span class="help-block">Format person code ABC (3 alphas)</span>
                        <?= form_error('no_person', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="box" id="field-customer-storage" <?= set_value('outbound_type') == 'ACCOUNT RECEIVABLE' ? '' : 'style="display: none"' ?>>
                <div class="box-header">
                    <h3 class="box-title">Initial Storage Capacity</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('effective_date') == '' ?: 'has-error'; ?>">
                                <label for="effective_date">Effective Date</label>
                                <input type="text" class="form-control datepicker" id="effective_date" name="effective_date"
                                       placeholder="Enter effective date" autocomplete="off"
                                       required value="<?= set_value('effective_date') ?>">
                                <?= form_error('effective_date', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('expired_date') == '' ?: 'has-error'; ?>">
                                <label for="expired_date">Expired Date</label>
                                <input type="text" class="form-control datepicker" id="expired_date" name="expired_date"
                                       placeholder="Enter effective date" autocomplete="off"
                                       required value="<?= set_value('expired_date') ?>">
                                <?= form_error('expired_date', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('warehouse_capacity') == '' ?: 'has-error'; ?>">
                                <label for="warehouse_capacity">Warehouse Capacity</label>
                                <input type="number" class="form-control" id="warehouse_capacity" name="warehouse_capacity"
                                       placeholder="Enter warehouse capacity"
                                       required maxlength="15" min="0" max="10000" value="<?= set_value('warehouse_capacity') ?>">
                                <?= form_error('warehouse_capacity', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('yard_capacity') == '' ?: 'has-error'; ?>">
                                <label for="yard_capacity">Yard Capacity</label>
                                <input type="number" class="form-control" id="yard_capacity" name="yard_capacity"
                                       placeholder="Enter yard capacity"
                                       required maxlength="15" min="0" max="10000" value="<?= set_value('yard_capacity') ?>">
                                <?= form_error('yard_capacity', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?= form_error('covered_yard_capacity') == '' ?: 'has-error'; ?>">
                                <label for="covered_yard_capacity">Covered Yard Capacity</label>
                                <input type="number" class="form-control" id="covered_yard_capacity" name="covered_yard_capacity"
                                       placeholder="Enter covered yard capacity"
                                       required maxlength="15" min="0" max="10000" value="<?= set_value('covered_yard_capacity') ?>">
                                <?= form_error('covered_yard_capacity', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>" id="field-related-parent" <?= set_value('type') == 'CUSTOMER' ? '' : 'style="display: none"' ?>>
                <label for="parent">Related Parent</label>
                <select class="form-control select2 select2-ajax" style="width: 100%"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        data-add-empty-value="NO CUSTOMER" data-empty-value="0"
                        name="parent" id="parent" data-placeholder="Select parent entity">
                    <option value=""></option>
                    <?php if (!empty($parent)): ?>
                        <option value="<?= $parent['id'] ?>" selected>
                            <?= $parent['name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
                <?= form_error('parent', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('name') == '' ?: 'has-error'; ?>">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       placeholder="Enter person name"
                       value="<?= set_value('name') ?>" required maxlength="50">
                <?= form_error('name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('address') == '' ?: 'has-error'; ?>">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address"
                       placeholder="Enter address"
                       value="<?= set_value('address') ?>">
                <?= form_error('address', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('region') == '' ?: 'has-error'; ?>">
                <label for="region">Region</label>
                <input type="text" class="form-control" id="region" name="region"
                       placeholder="Enter region"
                       value="<?= set_value('region') ?>">
                <?= form_error('region', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('gender') == '' ?: 'has-error'; ?>">
                        <label for="gender">Gender</label>
                        <select class="form-control select2" name="gender" id="gender"
                                data-placeholder="Select gender" required>
                            <option value=""></option>
                            <?php foreach ($genders as $key => $value): ?>
                                <option value="<?= $key ?>" <?= set_select('gender', $key) ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('gender', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('birthday') == '' ?: 'has-error'; ?>">
                        <label for="birthday">Birthday</label>
                        <input type="text" class="form-control datepicker" id="birthday" name="birthday"
                               placeholder="Enter birthday"
                               value="<?= set_value('birthday') ?>">
                        <?= form_error('birthday', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('contact') == '' ?: 'has-error'; ?>">
                        <label for="contact">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact"
                               placeholder="Enter contact" maxlength="50"
                               value="<?= set_value('contact') ?>">
                        <?= form_error('contact', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('email') == '' ?: 'has-error'; ?>">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Enter email name"
                               value="<?= set_value('email') ?>">
                        <?= form_error('email', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('website') == '' ?: 'has-error'; ?>">
                        <label for="website">Website</label>
                        <input type="text" class="form-control" id="website" name="website"
                               placeholder="Enter website name"
                               value="<?= set_value('website') ?>">
                        <?= form_error('website', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('tax_number') == '' ?: 'has-error'; ?>">
                        <label for="tax_number">Tax Number</label>
                        <input type="text" class="form-control" id="tax_number" name="tax_number"
                               placeholder="Put tax number" minlength="15" maxlength="15"
                               value="<?= set_value('tax_number') ?>">
                        <?= form_error('tax_number', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- <div class="col-md-6" id="field-whatsapp-group">
                    <div class="form-group <?= form_error('whatsapp_group') == '' ?: 'has-error'; ?>">
                        <label for="whatsapp_group">Whatsapp Group</label>
                        <input type="text" class="form-control" id="whatsapp_group" name="whatsapp_group"
                               placeholder="Enter whatsapp_group number"
                               value="<?= set_value('whatsapp_group') ?>">
                        <?= form_error('whatsapp_group', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div> -->
                <div class="col-md-6">
                    <div class="form-group <?= form_error('max_time_request') == '' ?: 'has-error'; ?>">
                        <label for="max_time_request">Max Time Request</label>
                        <input type="time" class="form-control" id="max_time_request" name="max_time_request" placeholder="Max time request picker" value="">
                        <?= form_error('max_time_request', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6" id="field-user-type" <?= set_value('type') == 'CUSTOMER' ? '' : 'style="display: none"' ?>>
                    <div class="form-group <?= form_error('type_user') == '' ?: 'has-error'; ?>">
                        <label for="type">User Type</label>
                        <select class="form-control select2" name="type_user" id="type_user" style="width: 100%" required>
                            <option value="">-- No User Type --</option>
                            <?php foreach ($userTypes as $userType): ?>
                                <option value="<?= $userType ?>" <?= set_select('type_user', $userType) ?>>
                                    <?= $userType ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('type_user', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('user[]') == '' ?: 'has-error'; ?>">
                        <label for="user">User</label>
                        <select class="form-control select2" multiple name="user[]" id="user">
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= set_select('user[]', $user['id']) ?>>
                                    <?= $user['name'] ?> (<?= $user['email'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('user[]', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('branches[]') == '' ?: 'has-error'; ?>" id="field-branch" <?= set_value('type') == 'CUSTOMER' ? '' : 'style="display: none"' ?>>
                <label for="branches">Access Branch</label>
                <span class="help-block">Person at least must has one branch</span>
                <div class="row">
                    <?php foreach ($branches as $branch): ?>
                        <div class="col-sm-3">
                            <div class="checkbox icheck" style="margin-top: 0">
                                <label>
                                    <input type="checkbox" name="branches[]" id="<?= $branch['id'] ?>" value="<?= $branch['id'] ?>"
                                        <?php echo set_checkbox('branch', $branch['id'], $branch['id'] == $activeBranchId); ?>>
                                    &nbsp; <?= ucwords($branch['branch']) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= form_error('branches[]', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('handling_types') == '' ?: 'has-error'; ?>">
                <label for="handling_types" style="margin-bottom: 15px">Access Handling Type</label>
                <div class="row">
                    <?php foreach ($handlingTypes as $handlingType): ?>
                        <div class="col-sm-4">
                            <div class="checkbox icheck" style="margin-top: 0">
                                <label>
                                    <input type="checkbox" name="handling_types[]" id="handling_type_<?= $handlingType['id'] ?>" value="<?= $handlingType['id'] ?>"
                                        <?php echo set_checkbox('handling_types', $handlingType['id']); ?>>
                                    &nbsp; <?= $handlingType['handling_type'] ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= form_error('handling_types', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('booking_types') == '' ?: 'has-error'; ?>">
                <label for="booking_types" style="margin-bottom: 15px">Access Booking Type</label>
                <div class="row">
                    <?php foreach ($bookingTypes as $bookingType): ?>
                        <div class="col-sm-4">
                            <div class="checkbox icheck" style="margin-top: 0">
                                <label>
                                    <input type="checkbox" name="booking_types[]" id="booking_type_<?= $bookingType['id'] ?>" value="<?= $bookingType['id'] ?>"
                                        <?php echo set_checkbox('booking_types', $bookingType['id']); ?>>
                                    &nbsp; <?= $bookingType['booking_type'] ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= form_error('booking_types', '<span class="help-block">', '</span>'); ?>
            </div>
            <?php if(!empty($documentTypes)): ?>
            <div class="form-group <?= form_error('document_types') == '' ?: 'has-error'; ?>">
                <label for="document_types" style="margin-bottom: 15px">Access Document Type (Reminder)</label>
                <div class="row">
                    <?php foreach ($documentTypes as $documentType): ?>
                        <div class="col-sm-4">
                            <div class="checkbox icheck" style="margin-top: 0">
                                <label>
                                    <input type="checkbox" name="document_types[]" id="document_type_<?= $documentType['id'] ?>" value="<?= $documentType['id'] ?>"
                                        <?php echo set_checkbox('document_types', $documentType['id']); ?>>
                                    &nbsp; <?= $documentType['document_type'] ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= form_error('document_types', '<span class="help-block">', '</span>'); ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Person</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/people.js?v=3') ?>" defer></script>