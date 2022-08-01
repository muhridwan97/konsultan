<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Request Release Items</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit-request-release/save') ?>" role="form" method="post" id="form-tep-request-release" class="need-validation">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer</label>
                <?php if (UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                    <select class="form-control select2 select2-ajax"
                            data-url="<?= site_url('people/ajax_get_people') ?>"
                            data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                            name="customer" id="customer" data-placeholder="Select customer" required style="width: 100%">
                        <option value=""></option>
                        <?php if (!empty($customer)): ?>
                            <option value="<?= $customer['id'] ?>" selected>
                                <?= $customer['name'] ?> - <?= $customer['no_person'] ?>
                            </option>
                        <?php endif; ?>
                    </select>
                <?php else: ?>
                    <p class="form-control-static"><?= UserModel::authenticatedUserData('name') ?></p>
                    <input type="hidden" name="customer" id="customer" value="<?= UserModel::authenticatedUserData('id_person') ?>">
                <?php endif ?>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Description about release request"
                          maxlength="500" minlength="10" required><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Requested To Be Released Goods</h3>
                </div>
                <div class="box-body">
                    <table class="table no-datatable table-striped table-bordered" id="table-hold-goods" data-selected-hold="<?= $holdGoodId ?? '' ?>">
                        <thead>
                        <tr>
                            <th style="width: 50px" class="text-center">No</th>
                            <th>Hold Reference</th>
                            <th>No Reference</th>
                            <th>Goods Name</th>
                            <th>Unit</th>
                            <th>Requests</th>
                            <th style="width: 80px">Released</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="row-placeholder">
                            <td colspan="6">No hold goods data</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Release
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/tep-request-release.js?v=1') ?>" defer></script>