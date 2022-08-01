<form role="form" class="form-horizontal form-view">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3">Category</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['category'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Booking Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['booking_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Supplier</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['supplier_name'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Customer</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['customer_name'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">No Booking</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['no_booking'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Status</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php
                        $statuses = [
                            BookingModel::STATUS_BOOKED => 'default',
                            BookingModel::STATUS_REJECTED => 'danger',
                            BookingModel::STATUS_APPROVED => 'primary',
                            BookingModel::STATUS_COMPLETED => 'success',
                        ]
                        ?>
                        <span class="label label-<?= get_if_exist($statuses, $booking['status']) ?>"><?= $booking['status'] ?></span>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Status Payout</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['status_payout'], '-') ?>
                        <?= if_empty($booking['payout_until_date'], '-', 'until ') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Booking Date</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($booking['booking_date'], 'd F Y') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">No Reference</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['no_reference'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Reference Date</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= format_date($booking['reference_date'], 'd F Y') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Reference <?= $booking['category'] == 'INBOUND' ? 'Out' : 'In' ?></label>
                <div class="col-sm-9">
                    <?php if ($booking['category'] == 'INBOUND'): ?>
                        <?php foreach ($bookingOut as $bookingOutData): ?>
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $bookingOutData['id']) ?>">
                                    <?= $bookingOutData['no_booking'] ?>
                                </a>
                            </p>
                        <?php endforeach; ?>
                        <?php if (empty($bookingOut)): ?>
                            <p class="form-control-static">No outbound references yet</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if($booking['type'] == BookingTypeModel::TYPE_IMPORT): ?>
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $booking['id_booking_in']) ?>">
                                    <?= if_empty($booking['no_booking_in'], '-') ?>
                                </a>
                            </p>
                        <?php else: ?>
                            <?php foreach ($bookingReferences as $bookingReference): ?>
                                <p class="form-control-static">
                                    <a href="<?= site_url('booking/view/' . $bookingReference['id_booking_reference']) ?>">
                                        <?= $bookingReference['no_booking_reference'] ?>
                                    </a>
                                </p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3">Mode</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['mode'], 'DEFAULT') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Source</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php if(empty($booking['source_file'])): ?>
                            -
                        <?php else: ?>
                            <a href="<?= base_url('uploads/' . $booking['source_file']) ?>">
                                DOWNLOAD
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">No Upload</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <a href="<?= site_url('upload/view/' . $booking['id_upload']) ?>">
                            <?= if_empty($booking['no_upload'], '-') ?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Doc. Status</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['document_status'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Vessel</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['vessel'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Voyage</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['voyage'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Payment Status</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['payment_status'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">BCF Status</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['bcf_status'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($booking['description'], 'No description') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($booking['created_at'], 'd F Y H:i"') ?>
                        by <?= if_empty($booking['creator_name'], 'Unknown') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($booking['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>