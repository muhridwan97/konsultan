<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Invoice</h3>
    </div>

    <form action="<?= site_url('invoice/save') ?>" role="form" method="post" enctype="multipart/form-data" id="form-invoice">
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
                            <option value="<?= $branch['id'] ?>" <?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('status') == '' ?: 'has-error'; ?>">
                <label for="status">Invoice Status</label>
                <select class="form-control select2" id="status" name="status" data-placeholder="Invoice status setting">
                    <option value="DRAFT">DRAFT (Estimation Invoice)</option>
                    <option value="PUBLISHED">RELEASE (Published Invoice)</option>
                </select>
                <?= form_error('status', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                        <label for="customer">Customer</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                name="customer" id="customer"
                                data-placeholder="Select customer" required>
                            <option value=""></option>
                        </select>
                        <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                        <span class="help-block">
                            If you don't find any customer
                            <a href="<?= site_url('people') ?>" target="_blank">Click here</a> to create or add one.
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('setting') == '' ?: 'has-error'; ?>">
                        <label for="setting">Price Setting</label>
                        <select class="form-control select2" id="setting" name="setting" data-placeholder="Invoice rule setting">
                            <option value="0">GENERAL (General setting price)</option>
                            <option value="1">CUSTOMER (Customer setting price)</option>
                        </select>
                        <?= form_error('setting', '<span class="help-block">', '</span>'); ?>
                        <span class="help-block">Price invoice setting by customer or non customer.</span>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                <label for="type">Invoice Type</label>
                <select class="form-control select2" id="type" name="type" data-placeholder="Invoice mode">
                    <option value=""></option>
                    <option value="BOOKING DEPO">BOOKING DEPO (INBOUND)</option>
                    <option value="BOOKING FULL">BOOKING FULL (INBOUND)</option>
                    <option value="BOOKING FULL EXTENSION">BOOKING FULL EXTENSION (INBOUND)</option>
                    <option value="HANDLING">HANDLING (ESTIMATION)</option>
                    <option value="WORK ORDER">JOB (REALIZATION)</option>
                    <option value="CUSTOM">CUSTOM</option>
                </select>
                <?= form_error('type', '<span class="help-block">', '</span>'); ?>
            </div>

            <div id="form-invoice-mode">
                <?php $this->load->view('invoice/_type_booking_full') ?>
                <?php $this->load->view('invoice/_type_booking_full_extension') ?>
                <?php $this->load->view('invoice/_type_handling') ?>
                <?php $this->load->view('invoice/_type_work_order') ?>
                <?php $this->load->view('invoice/_type_custom') ?>
            </div>

            <div class="panel panel-primary" style="display: none">
                <div class="panel-heading">Invoice Charge</div>
                <div class="panel-body">
                    <div id="charge-invoice-wrapper"></div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Invoice Note</label>
                <textarea class="form-control" id="description" name="description" placeholder="Invoice description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Create Invoice</button>
        </div>
    </form>
</div>

<script id="row-invoice-item-template" type="text/x-custom-template">
    <tr class="row-invoice-item">
        <td></td>
        <td style="min-width: 200px">
            <input type="text" id="item_name" name="invoice_details[item_name][]"
                   class="form-control" placeholder="Input name" required>
        </td>
        <td>
            <input type="text" id="unit" name="invoice_details[unit][]"
                   class="form-control" placeholder="Unit name" required>
        </td>
        <td>
            <input type="number" step="any" min="0" value="1" id="quantity" name="invoice_details[quantity][]"
                   class="form-control" placeholder="Quantity of item" required>
        </td>
        <td>
            <input type="text" id="unit_price" name="invoice_details[unit_price][]"
                   class="form-control currency" placeholder="Unit price" required>
        </td>
        <td>
            <input type="number" step="any" min="-1" value="1" id="unit_multiplier" name="invoice_details[unit_multiplier][]"
                   class="form-control" placeholder="Unit multiplier" required>
        </td>
        <td>
            <select id="type" name="invoice_details[type][]" class="form-control select2" style="width: 100%" data-placeholder="Item group" required>
                <option value=""></option>
                <option value="STORAGE">STORAGE</option>
                <option value="HANDLING">HANDLING</option>
                <option value="COMPONENT">COMPONENT</option>
                <option value="PAYMENT">PAYMENT</option>
                <option value="INVOICE">INVOICE</option>
                <option value="OTHER">OTHER</option>
            </select>
        </td>
        <td>
            <input type="text" maxlength="50" name="invoice_details[description][]" id="description"
                   class="form-control" placeholder="Description">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-invoice-item">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>

<script id="row-invoice-item-table-template" type="text/x-custom-template">
    <tr>
        <td class="text-center"></td>
        <td>
            <span class="label-item"></span><br>
            <span class="small text-muted label-description"></span>

            <input type="hidden" id="item_name" name="invoice_details[item_name][]" value="">
            <input type="hidden" id="description" name="invoice_details[description][]" value="">
        </td>
        <td>
            <span class="label-unit"></span>
            <input type="hidden" id="unit" name="invoice_details[unit][]" value="">
        </td>
        <td>
            <span class="label-type"></span>
            <input type="hidden" id="type" name="invoice_details[type][]" value="">
        </td>
        <td>
            <span class="label-quantity"></span>
            <input type="hidden" id="quantity" name="invoice_details[quantity][]" value="">
        </td>
        <td class="text-right">
            <span class="label-price"></span>
            <input type="hidden" id="unit_price" name="invoice_details[unit_price][]" value="">
        </td>
        <td>
            <span class="label-multiplier"></span>
            <input type="hidden" id="unit_multiplier" name="invoice_details[unit_multiplier][]" value="">
        </td>
        <td class="text-right no-wrap">
            <span class="label-total"></span>
        </td>
        <td>
            <button class="btn btn-sm btn-primary btn-edit-invoice-table" type="button">
                <i class="fa ion-compose"></i>
            </button>
        </td>
    </tr>
</script>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-invoice-editor">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Invoice Item</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="item" class="control-label">Item Name</label>
                        <input type="text" id="item" name="item"
                               class="form-control" placeholder="Input item name">
                    </div>
                    <div class="form-group">
                        <label for="type" class="control-label">Type</label>
                        <select id="type" name="type" class="form-control select2" style="width: 100%" data-placeholder="Item group">
                            <option value=""></option>
                            <option value="STORAGE">STORAGE</option>
                            <option value="HANDLING">HANDLING</option>
                            <option value="COMPONENT">COMPONENT</option>
                            <option value="PAYMENT">PAYMENT</option>
                            <option value="INVOICE">INVOICE</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit" class="control-label">Unit Name</label>
                                <input type="text" id="unit" name="unit"
                                       class="form-control" placeholder="Unit name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity" class="control-label">Quantity</label>
                                <input type="number" step="any" min="0" value="1" id="quantity" name="quantity"
                                       class="form-control" placeholder="Quantity of item">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price" class="control-label">Unit Price</label>
                                <input type="text" id="price" name="price"
                                       class="form-control currency" placeholder="Unit price">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="multiplier" class="control-label">Multiplier</label>
                                <input type="number" step="any" min="-1" value="1" id="multiplier" name="multiplier"
                                       class="form-control" placeholder="Unit multiplier">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" id="description" cols="30" rows="1"
                                  class="form-control" placeholder="Item description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="item_summary" class="control-label">Item Summary</label>
                        <textarea name="item_summary" id="item_summary" cols="30" rows="2"
                                  class="form-control" placeholder="Item summary"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btn-delete"
                            data-authorized="<?= AuthorizationModel::isAuthorized(PERMISSION_INVOICE_DELETE) ? 'true' : 'false' ?>"
                            data-permission="<?= PERMISSION_INVOICE_DELETE ?>">Delete Item</button>
                    <button type="button" class="btn btn-primary" id="btn-submit">Update Invoice Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('permission/_modal_override_permission') ?>

<script src="<?= base_url('assets/app/js/invoice.js?v=3') ?>" defer></script>
<script src="<?= base_url('assets/app/js/invoice_editor.js?v=3') ?>" defer></script>