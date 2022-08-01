<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Booking</h3>
    </div>
    <form action="<?= site_url('booking/save') ?>" role="form" method="post" id="form-booking">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                <label for="category">Category</label>
                <select class="form-control select2" name="category" id="category" data-placeholder="Select category"
                        required style="width: 100%">
                    <option value=""></option>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_CREATE)): ?>
                        <option value="INBOUND" <?= set_select('category', 'INBOUND', 'INBOUND' == ($selectedUpload['category'] ?? '')) ?>>INBOUND</option>
                    <?php endif ?>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_CREATE)): ?>
                        <option value="OUTBOUND" <?= set_select('category', 'OUTBOUND', 'OUTBOUND' == ($selectedUpload['category'] ?? '')) ?>>OUTBOUND</option>
                    <?php endif ?>
                </select>
                <?= form_error('category', '<span class="help-block">', '</span>'); ?>
            </div>

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
                        <?php endforeach ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                        <label for="booking-type">Booking Type</label>
                        <select class="form-control select2" name="type" id="booking-type"
                                data-placeholder="Select booking type" required style="width: 100%">
                            <option value=""></option>
                            <?php foreach ($selectedBookingTypes as $type): ?>
                                <option value="<?= $type['id'] ?>" data-type="<?= $type['type'] ?>" <?= set_select('type', $type['id'], $type['id'] == ($selectedUpload['id_booking_type'] ?? '')) ?>>
                                    <?= $type['booking_type'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <?= form_error('type', '<span class="help-block">', '</span>'); ?>
                        <span class="help-block">Booking type would change document list (document <strong>inbound</strong> only)</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('booking_date') == '' ?: 'has-error'; ?>">
                        <label for="booking_date">Booking Date</label>
                        <input type="text" class="form-control daterangepicker2" id="booking_date" name="booking_date"
                               placeholder="Booking date" required
                               value="<?= set_value('booking_date', date('d F Y H:i')) ?>">
                        <?= form_error('booking_date', '<span class="help-block">', '</span>'); ?>
                        <span class="help-block">Date you booking, by default should be today</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                        <label for="customer">Customer</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                name="customer" id="customer"
                                data-placeholder="Select customer" required style="width: 100%">
                            <option value=""></option>
                            <?php if (!empty($selectedCustomer)): ?>
                                <option value="<?= $selectedCustomer['id'] ?>" selected>
                                    <?= $selectedCustomer['name'] ?>
                                </option>
                            <?php endif; ?>
                        </select>
                        <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                        <span class="help-block">
                            If you don't find any customer
                            <a href="<?= site_url('people') ?>" target="_blank">Click here</a> to create or add one.
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('supplier') == '' ?: 'has-error'; ?>">
                        <label for="supplier">Supplier</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_SUPPLIER ?>"
                                name="supplier" id="supplier"
                                data-placeholder="Select supplier" style="width: 100%">
                            <option value=""></option>
                        </select>
                        <?= form_error('supplier', '<span class="help-block">', '</span>'); ?>
                        <span class="help-block">
                            If you don't find any supplier
                            <a href="<?= site_url('people') ?>" target="_blank">Click here</a> to create or add one.
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('document') == '' ?: 'has-error'; ?>">
                <label for="booking-document">Related Document</label>
                <select class="form-control select2" name="document" id="booking-document"
                        data-placeholder="Select uploaded document" required style="width: 100%">
                    <option value=""></option>
                    <?php foreach ($uploads as $upload): ?>
                        <option value="<?= $upload['id'] ?>"
                                data-supplier="<?= $upload['id_person'] ?>" <?= set_select('document', $upload['id'], $upload['id'] == (key_exists('id', $selectedUpload) ? $selectedUpload['id'] : '')) ?>>
                            <?= $upload['description'] ?>
                            - <?= (new DateTime($upload['created_at']))->format('d F Y H:i') ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?= form_error('document', '<span class="help-block">', '</span>'); ?>
                <ul class="list-group" id="document-info-wrapper"
                    style="margin-top: 10px; <?= count($selectedDocuments) ?: 'display: none' ?>">
                    <li class="list-group-item disabled">Document Details</li>
                    <?php foreach ($selectedDocuments as $document): ?>
                        <li class="list-group-item data">
                            <strong><?= $document['document_type'] ?></strong> : <?= $document['no_document'] ?>
                            <span class="pull-right"><?= $document['total_file'] ?> files</span>
                        </li>
                    <?php endforeach ?>
                </ul>
                <span class="help-block">
                    Take a moment to retrieving detail document from server when selecting document.
                    <a href="<?= site_url('upload') ?>" target="_blank">Click here</a> to upload document.
                </span>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group <?= form_error('no_reference') == '' ?: 'has-error'; ?>">
                        <label for="no_reference">No Reference</label>
                        <input type="text" class="form-control" id="no_reference" name="no_reference"
                               placeholder="Booking no reference" required
                               value="<?= set_value('no_reference') ?>">
                        <?= form_error('no_reference', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('reference_date') == '' ?: 'has-error'; ?>">
                        <label for="reference_date">Reference Date</label>
                        <input type="text" class="form-control datepicker" id="reference_date" name="reference_date"
                               placeholder="Reference document date" required
                               value="<?= set_value('reference_date') ?>">
                        <?= form_error('reference_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('vessel') == '' ?: 'has-error'; ?>">
                        <label for="vessel">Vessel</label>
                        <input type="text" class="form-control" id="vessel" name="vessel"
                               placeholder="Vessel" required
                               value="<?= set_value('vessel') ?>">
                        <?= form_error('vessel', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('voyage') == '' ?: 'has-error'; ?>">
                        <label for="voyage">Voyage</label>
                        <input type="text" class="form-control" id="voyage" name="voyage"
                               placeholder="Voyage"
                               value="<?= set_value('voyage') ?>">
                        <?= form_error('voyage', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('document_status') == '' ?: 'has-error'; ?>">
                        <label for="document_status">Document Status</label>
                        <select class="form-control select2" name="document_status" id="document_status"
                                data-placeholder="Booking document status" required style="width: 100%">
                            <option value=""></option>
                            <?php foreach (BookingModel::DOCUMENT_STATUSES as $status): ?>
                                <option value="<?= $status ?>" <?= set_select('document_status', $status) ?>>
                                    <?= $status ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <?= form_error('document_status', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Booking Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Booking description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-default">
                <div class="box-header">
                    <h3 class="box-title">Extension data</h3>
                </div>
                <div class="box-body" id="extension-wrapper">
                    <p class="text-muted">Extension fields booking lies here</p>
                </div>
            </div>

            <div id="booking-reference-wrapper" style="display: none">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Booking Reference</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group <?= form_error('booking_in') == '' ?: 'has-error'; ?>">
                            <label for="booking_in">Booking In</label>
                            <select class="form-control select2" name="booking_in" id="booking_in" style="width: 100%"
                                    data-placeholder="Select related booking in" required disabled>
                                <option value=""></option>
                            </select>
                            <?= form_error('booking_in', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header">
                    <h3 class="box-title">Total Weight</h3>
                </div>
                <div class="box-body" id="weight-wrapper">
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('netto') == '' ?: 'has-error'; ?>">
                            <label for="netto">Total Weight</label>
                            <input type="text" class="form-control numeric" id="netto" name="netto"
                                placeholder="netto"
                                value="0">
                            <?= form_error('netto', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group <?= form_error('bruto') == '' ?: 'has-error'; ?>">
                            <label for="bruto">Total Gross Weight</label>
                            <input type="text" class="form-control numeric" id="bruto" name="bruto"
                                placeholder="bruto"
                                value="0">
                            <?= form_error('bruto', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="input-detail-wrapper" style="display: none">
                <?php $this->load->view('tally/_tally_editor') ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right" id="btn-save-booking">
                Save Booking
            </button>
        </div>
    </form>
</div>

<?php $this->load->view('tally/_modal_container_input') ?>
<?php $this->load->view('tally/_modal_goods_input') ?>
<?php $this->load->view('tally/_modal_select_position') ?>

<script src="<?= base_url('assets/app/js/booking-form.js?v=7') ?>" defer></script>
