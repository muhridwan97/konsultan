<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Request Entry Permit</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit/save_request') ?>" role="form" method="post" id="form-tep-request">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer / Supplier</label>
                <?php
                $userType = UserModel::authenticatedUserData('user_type');
                $idPerson = UserModel::authenticatedUserData('id_person');
                ?>
                <?php if($userType == 'INTERNAL'): ?>
                    <select class="form-control select2 customer" name="customer" id="customer"
                            data-placeholder="Select customer or supplier" style="width: 100%" required>
                        <option value=""></option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>">
                                <?= $customer['name'] ?> - <?= $customer['no_person'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p class="form-control-static"><?= UserModel::authenticatedUserData('name') ?></p>
                    <input type="hidden" name="customer" id="customer" value="<?= $idPerson ?>">
                <?php endif ?>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="goods-editor" data-id="<?= isset($formId) ? $formId : '1' ?>" data-stock-url="<?= isset($stockUrl) ? $stockUrl : '' ?>">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><span class="hidden-xs">List </span>Goods</h3>
                        <div class="pull-right">
                            <button class="btn btn-sm btn-success" id="btn-stock-goods" type="button">
                                <i class="fa ion-plus"></i> ADD
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-editor-wrapper">
                            <div class="table-editor-scroller">
                                <table class="table no-datatable table-striped table-bordered responsive no-wrap" id="table-goods" data-with-detail="<?= isset($withDetailGoods) ? $withDetailGoods : false ?>">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Reference</th>
                                        <th>No Invoice</th>
                                        <th>No BL</th>
                                        <th>No Goods</th>
                                        <th>Goods Name</th>
                                        <th>Whey Number</th>
                                        <th>Unit</th>
                                        <th>Stock Quantity</th>
                                        <th class="sticky-col-right">
                                            Action
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="row-placeholder">
                                        <td colspan="10">No goods data</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group booking-wrapper">
                <!-- <label for="booking">Booking</label> -->
                <input type="hidden" name="booking" id="booking" value="<?= empty($uploads) ? '':'have' ?>">
            </div>
            <div class="form-group" <?= form_error('armada') == '' ?: 'has-error'; ?>">
                <label for="armada">Armada</label>
                <div class="input-group col-xs-12">
                <select class="form-control select2 " data-key-id="id" data-key-label="name"  name="armada" id="armada" data-placeholder="Select Armada" style="width: 100%;" required>
                    <option value=""></option>   
                    <option value="TCI">Transcon Indonesia</option>   
                    <option value="CUSTOMER">Customer</option>
                </select>
                <?= form_error('armada', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
            <div id="slot-wrapper" style="display: none;">
                <div class="form-group <?= form_error('tep_date') == '' ?: 'has-error'; ?>">
                    <label for="tep_date">TEP Date</label>
                    <input type="text" class="form-control requestDatepicker" id="tep_date" name="tep_date" data-min-date="<?= $temp_tanggal?>"
                            data-holiday-date = "<?= $holidayDate ?>"
                            placeholder="TEP Date"
                            value="<?= set_value('tep_date', format_date($temp_tanggal, 'd F Y')) ?>">
                    <?= form_error('tep_date', '<span class="help-block">', '</span>'); ?>
                </div>
                <div class="form-group <?= form_error('slot') == '' ?: 'has-error'; ?>" >
                    <label for="slot">Number slot request <span style="color: gray">(<span id="slot_remain"><?= $slot_remain ?></span> Slot remaining for <span id="date_remain"><?= date('d F Y',strtotime($temp_tanggal)) ?></span>)</span></label>
                    <input type="number" class="form-control" id="slot" name="slot" min="1" max="<?= $slot_remain ?>" placeholder="Number slot request" value="<?= set_value('slot', '1') ?>">
                    <?= form_error('slot', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>
            <div class="form-group <?= form_error('location') == '' ?: 'has-error'; ?>" id="location-wrapper" style="display: none;">
                <label for="location">Destination </label>
                <input type="text" class="form-control" id="location" name="location" placeholder="Destination request" value="<?= set_value('location') ?>">
                <?= form_error('location', '<span class="help-block">', '</span>'); ?>
            </div>
            
            
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="TEP description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-success pull-right">
                Request TEP
            </button>
        </div>
    </form>
</div>
<?php $this->load->view('transporter_entry_permit/_modal_goods_stock'); ?>
<?php $this->load->view('transporter_entry_permit/_modal_goods_take_stock'); ?>
<script src="<?= base_url('assets/app/js/tep.js?v=8') ?>" defer></script>
<script src="<?= base_url('assets/app/js/tep_request.js?v=3') ?>" defer></script>
<script>
// Get DOM reference
var input = document.getElementById("slot");

// Add event listener
input.addEventListener("input", function(e){

  // Clear any old status
  this.setCustomValidity("");
  
  // Check for invalid state(s)
  if(this.validity.rangeOverflow){
    this.setCustomValidity("Slot is full, please contact operational");
  } else if(this.validity.rangeUnderflow){
    this.setCustomValidity("Please fill slot more than or equal minimal 1 slot");
  }
});
</script>