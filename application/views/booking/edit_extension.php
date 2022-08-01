<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Booking Extension</h3>
    </div>
    <form action="<?= site_url('booking/update_extension/'.$booking['id']) ?>" role="form" method="post">
        <?= _csrf() ?>
        <?= _method('put') ?>

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">No Booking</label>
                        <p class="form-control-static">
                            <?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>)
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Booking Date</label>
                        <p class="form-control-static">
                            <?= readable_date($booking['booking_date']) ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Category</label>
                        <p class="form-control-static">
                            <?= $booking['booking_type'] ?> (<?= $booking['category'] ?>)
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Customer</label>
                        <p class="form-control-static">
                            <?= $booking['customer_name'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Booking Status</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group <?= form_error('status') == '' ?: 'has-error'; ?>">
                                <label for="booking_status">Booking Status</label>
                                <select class="form-control select2" name="booking_status" id="booking_status" data-placeholder="Booking status" required>
                                    <option value=""></option>
                                    <option value="<?= $booking['status'] ?>"><?= $booking['status'] ?> (Last Status)</option>
                                    <option value="UPDATE DESCRIPTION">UPDATE DESCRIPTION</option>
                                </select>
                                <?= form_error('booking_status', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group <?= form_error('document_status') == '' ?: 'has-error'; ?>">
                                <label for="document_status">Document Status</label>
                                <select class="form-control select2" name="document_status" id="document_status" data-placeholder="Booking document status" required>
                                    <option value=""></option>
                                    <?php foreach (BookingModel::DOCUMENT_STATUSES as $status): ?>
                                        <option value="<?= $status ?>" <?= set_select('document_status', $status, $bookingStatus['document_status'] == $status) ?>>
                                            <?= $status ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('document_status', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group <?= form_error('no_doc') == '' ?: 'has-error'; ?>">
                                <label for="no_doc">No Document</label>
                                <input type="text" class="form-control" id="no_doc" name="no_doc"
                                       placeholder="Booking doc number" required
                                       value="<?= set_value('no_doc', $bookingStatus['no_doc']) ?>">
                                <?= form_error('no_doc', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group <?= form_error('doc_date') == '' ?: 'has-error'; ?>">
                                <label for="doc_date">Document Date</label>
                                <input type="text" class="form-control datepicker" id="doc_date" name="doc_date"
                                       placeholder="Reference document date" required
                                       value="<?= set_value('doc_date', readable_date($bookingStatus['doc_date'], false)) ?>">
                                <?= form_error('doc_date', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Status Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Change status description"
                                  maxlength="500"></textarea>
                        <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Extension data</h3>
                </div>
                <div class="box-body">
                    <?php if (empty($extensionFields)): ?>
                        <p class="text-muted">Extension booking fields lies here</p>
                    <?php else: ?>
                        <?= $extensions ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="<?= site_url('booking') ?>" class="btn btn-primary pull-left">
                Back to Booking List
            </a>
            <button type="submit" class="btn btn-warning pull-right" id="btn-save-booking">
                Update Booking Extension
            </button>
        </div>
    </form>
</div>