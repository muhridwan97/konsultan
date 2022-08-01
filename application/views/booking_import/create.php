<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Import Booking</h3>
    </div>
    <form action="<?= site_url('booking-import/upload') ?>" role="form" method="post" id="form-booking-import" enctype="multipart/form-data">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required style="width: 100%">
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

            <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                <label for="category">Category</label>
                <select class="form-control select2" name="category" id="category" data-placeholder="Select category"
                        required style="width: 100%">
                    <option value=""></option>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_CREATE)): ?>
                        <option value="INBOUND" <?= set_select('category', 'INBOUND') ?>>INBOUND</option>
                    <?php endif; ?>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_CREATE)): ?>
                        <option value="OUTBOUND" <?= set_select('category', 'OUTBOUND') ?>>OUTBOUND</option>
                    <?php endif; ?>
                </select>
                <?= form_error('category', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('document') == '' ?: 'has-error'; ?>">
                <label for="booking-document">Related Document</label>
                <select class="form-control select2" name="document" id="booking-document" data-placeholder="Select uploaded document" required style="width: 100%">
                    <option value=""></option>
                </select>
                <?= form_error('document', '<span class="help-block">', '</span>'); ?>
                <ul class="list-group" id="document-info-wrapper" style="margin-top: 10px; display: none">
                    <li class="list-group-item disabled">Document Details</li>
                </ul>
                <span class="help-block">
                    Take a moment to retrieving detail document from server when selecting document.
                    <a href="<?= site_url('upload') ?>" target="_blank">Click here</a> to upload document.
                </span>
            </div>

            <div id="booking-reference-wrapper" style="display: none">
                <div class="form-group <?= form_error('booking_in') == '' ?: 'has-error'; ?>">
                    <label for="booking_in">Booking In</label>
                    <select class="form-control select2" name="booking_in" id="booking_in" style="width: 100%" data-placeholder="Select related booking in">
                        <option value=""></option>
                    </select>
                    <?= form_error('booking_in', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>

            <div id="package-wrapper" style="display: none">
                <div class="form-group <?= form_error('xml_document') == '' ?: 'has-error'; ?>">
                    <label for="create_package">Package</label>
                    <div class="checkbox icheck">
                        <label for="create_package">
                            <input type="checkbox" name="create_package" id="create_package" value="1">
                            <strong>Create Goods From Package</strong>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('xml_document') == '' ?: 'has-error'; ?>">
                <label for="xml_document">XML Document</label>
                <input type="file" name="xml_document" id="xml_document"
                       accept="application/xml" required
                       placeholder="Select XML document">
                <?= form_error('xml_document', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-primary pull-right">
                Preview Import
            </button>
        </div>
    </form>
</div>

<?php $this->load->view('template/_modal_confirm') ?>

<script src="<?= base_url('assets/app/js/confirm.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/booking-import-xml.js?v=2') ?>" defer></script>