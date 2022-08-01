<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Request Hold Items</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit-request-hold/save') ?>" role="form" method="post" id="form-tep-request-hold" class="need-validation">
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
                <textarea class="form-control" id="description" name="description" placeholder="Description about hold request"
                          maxlength="500" minlength="10" required><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Request Hold Goods</h3>
                    <div class="pull-right">
                        <button class="btn btn-sm btn-success" id="btn-add-goods" type="button">
                            <i class="fa ion-plus"></i> ADD
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-editor-wrapper" id="goods-hold-editor">
                        <div class="table-editor-scroller">
                            <table class="table no-datatable table-striped table-bordered responsive no-wrap" id="table-goods">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Reference</th>
                                    <th>Goods Name</th>
                                    <th>Unit</th>
                                    <th>Description</th>
                                    <th class="sticky-col-right">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($goods as $index => $item): ?>
                                    <tr class="row-goods row-stock" data-id="<?= uniqid() ?>">
                                        <td><?= $index + 1 ?></td>
                                        <td id="no-reference-label">
                                            <?= $item['no_reference_outbound'] ?><br>
                                            <small class="text-muted"><?= $item['no_reference_inbound'] ?></small>
                                        </td>
                                        <td id="goods-label">
                                            <?= $item['goods_name'] ?><br>
                                            <small class="text-muted"><?= $item['no_goods'] ?></small>
                                        </td>
                                        <td id="unit-label"><?= $item['unit'] ?></td>
                                        <td id="description-label"><?= $item['description'] ?></td>
                                        <td class="sticky-col-right" style="min-height: 58px">
                                            <input type="hidden" name="goods[<?= $index ?>][id_upload]" id="id_upload" value="<?= $item['id_upload'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][id_booking]" id="id_booking" value="<?= $item['id_booking'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][id_goods]" id="id_goods" value="<?= $item['id_goods'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][id_unit]" id="id_unit" value="<?= $item['id_unit'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][ex_no_container]" id="ex_no_container" value="<?= $item['ex_no_container'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][quantity]" id="quantity" value="<?= $item['quantity'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][description]" id="description" value="<?= $item['description'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][id_request_uploads]" id="id_request_uploads" value="<?= $item['id_request_uploads'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][id_requests]" id="id_requests" value="<?= $item['id_requests'] ?>">
                                            <input type="hidden" name="goods[<?= $index ?>][no_requests]" id="no_requests" value="<?= $item['no_requests'] ?>">
                                            <button class="btn btn-sm btn-danger btn-remove-goods" type="button">
                                                <i class="ion-trash-b"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($goods)): ?>
                                    <tr class="row-placeholder">
                                        <td colspan="6">No requested goods data</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Hold
            </button>
        </div>
    </form>
</div>

<?php $this->load->view('transporter_entry_permit_request_hold/_modal_goods_list'); ?>
<?php $this->load->view('transporter_entry_permit_request_hold/_modal_goods_take'); ?>

<script src="<?= base_url('assets/app/js/tep-request-hold.js?v=1') ?>" defer></script>