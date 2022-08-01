<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit <?= $deliveryTracking['no_delivery_tracking'] ?></h3>
    </div>

    <form action="<?= site_url('delivery-tracking/update/' . $deliveryTracking['id']) ?>" role="form" method="post" id="form-delivery-tracking">
        <?= _method('put') ?>

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
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

            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer" id="customer" data-placeholder="Select customer" required>
                    <option value=""></option>
                    <?php if(!empty($customer)): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('user') == '' ?: 'has-error'; ?>">
                <label for="user">Assigned To</label>
                <select class="form-control select2" name="user" id="user" data-placeholder="Select user" required>
                    <option value=""></option>
                    <?php foreach($users as $user): ?>
                        <option value="<?= $user['id'] ?>"<?= set_select('user', $user['id'], $user['id'] == $deliveryTracking['id_user']) ?>>
                            <?= $user['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">All users that have permission "delivery tracking add state"</span>
                <?= form_error('user', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('user') == '' ?: 'has-error'; ?>">
                        <label for="reminder_type">Reminder Type</label>
                        <select class="form-control select2" name="reminder_type" id="reminder_type" required data-placeholder="Select reminder type" style="width: 100%">
                            <option value="EMPLOYEE"<?= set_select('reminder_type', 'EMPLOYEE', $deliveryTracking['reminder_type'] == 'EMPLOYEE') ?>>ASSIGNED EMPLOYEE</option>
                            <option value="DEPARTMENT"<?= set_select('reminder_type', 'DEPARTMENT', $deliveryTracking['reminder_type'] == 'DEPARTMENT') ?>>GROUP DEPARTMENT</option>
                        </select>
                        <?= form_error('reminder_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contact_group">Contact Group</label>
                        <select class="form-control select2" name="contact_group" id="contact_group" required
                            <?= set_value('reminder_type', $deliveryTracking['reminder_type']) == 'DEPARTMENT' ? '' : 'disabled' ?> data-placeholder="Select group" style="width: 100%">
                            <option value=""></option>
                            <?php foreach ($contactGroups as $contactGroup): ?>
                                <option value="<?= $contactGroup['id'] ?>"<?= set_select('contact_group', $contactGroup['id'], $contactGroup['id'] == $deliveryTracking['id_department_contact_group']) ?>>
                                    <?= $contactGroup['group_name'] ?> - <?= $contactGroup['contact_group'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('contact_group', '<span class="help-block">', '</span>') ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Delivery description"
                          maxlength="500"><?= set_value('description', $deliveryTracking['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">
                Update Delivery
            </button>
        </div>
    </form>
</div>
