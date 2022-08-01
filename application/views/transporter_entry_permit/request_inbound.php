<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Request Entry Permit</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit/save_request_inbound') ?>" role="form" method="post" id="form-tep-request-inbound">
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

            <div class="form-group booking-wrapper">
                <!-- <label for="booking">Booking</label> -->
                <input type="hidden" name="booking" id="booking" value="<?= empty($uploads) ? '':'have' ?>">
                <input type="hidden" name="category" id="category" value="INBOUND">
            </div>
            <div class="form-group <?= form_error('aju') == '' ?: 'has-error'; ?>">
                <label for="aju">Aju Reference </label>
                <select class="form-control select2" name="aju[]" id="aju" multiple
                        data-placeholder="Select Aju Reference" style="width: 100%" required>
                    <option value=""></option>
                    <?php foreach ($uploads as $item): ?>
                        <option value="<?= $item['id'] ?>">
                            <?= $item['description'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?= form_error('aju', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('tep_date') == '' ?: 'has-error'; ?>">
                <label for="tep_date">TEP Date</label>
                <input type="text" class="form-control requestDatepicker" id="tep_date" name="tep_date" data-min-date="<?= $temp_tanggal?>"
                        data-holiday-date = "<?= $holidayDate ?>"
                        placeholder="TEP Date" required
                        value="<?= set_value('tep_date', format_date($temp_tanggal, 'd F Y')) ?>">
                <?= form_error('tep_date', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('slot') == '' ?: 'has-error'; ?>">
                <label for="slot">Number slot request <span style="color: gray">(<span id="slot_remain"><?= $slot_remain ?></span> Slot remaining for <span id="date_remain"><?= date('d F Y',strtotime($temp_tanggal)) ?></span>)</span></label>
                <input type="number" class="form-control" id="slot" name="slot" min="1" max="<?= $slot_remain ?>" placeholder="Number slot request" value="<?= set_value('slot') ?>" required>
                <?= form_error('slot', '<span class="help-block">', '</span>'); ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required-document" id="do-wrapper">
                        <label for="file_do">
                            File DO/Memo <span style="color:#a9a9a9">(Upload max 5 MB)</span>
                        </label>
                        <label class="pull-right">
                            <span class="label label-danger">Required</span>
                        </label>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="btn btn-primary btn-block fileinput-button">
                                    <span class="button-file">Select File</span>
                                    <input class="upload-do" id="file_do" type="file" name="file_do">
                                </div>
                                <div class="upload-input-wrapper"></div>
                            </div>
                            <div class="col-sm-9">
                                <div id="progress" class="progress progress-upload">
                                    <div class="progress-bar progress-bar-success"></div>
                                </div>
                                <div class="uploaded-file"></div>
                            </div>
                        </div>
                    </div>
                </div>
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

<script src="<?= base_url('assets/app/js/tep.js?v=8') ?>" defer></script>
<script src="<?= base_url('assets/app/js/tep_request.js?v=1') ?>" defer></script>
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