<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Status <?= $booking['no_booking'] ?></h3>
    </div>

    <div class="box-body">

        <?php $this->load->view('booking/_view_header') ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Booking Status</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Status Booking</th>
                        <th>Status Doc</th>
                        <th>No Doc</th>
                        <th>Doc Date</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($bookingStatuses as $bookingStatus): ?>
                        <tr>
                            <td class="responsive-hide"><?= $no++ ?></td>
                            <td class="responsive-title"><?= strtoupper($bookingStatus['booking_status']) ?></td>
                            <td><?= strtoupper($bookingStatus['document_status']) ?></td>
                            <td><?= if_empty($bookingStatus['no_doc'], '-') ?></td>
                            <td><?= readable_date($bookingStatus['doc_date'], false) ?></td>
                            <td><?= if_empty($bookingStatus['description'], '-') ?></td>
                            <td><?= readable_date($bookingStatus['created_at']) ?></td>
                            <td><?= $bookingStatus['creator_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($bookingStatuses)): ?>
                        <tr>
                            <td colspan="7">No statuses available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Payment Status</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Payment Status</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($paymentStatuses as $status): ?>
                        <tr>
                            <td class="responsive-hide"><?= $no++ ?></td>
                            <td class="responsive-title"><?= $status['status'] ?></td>
                            <td><?= if_empty($status['description'], '-') ?></td>
                            <td><?= readable_date($status['created_at']) ?></td>
                            <td><?= $status['creator_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($status)): ?>
                        <tr>
                            <td colspan="5">No statuses available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">BCF Status</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>BCF Status</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($bcfStatuses as $status): ?>
                        <tr>
                            <td class="responsive-hide"><?= $no++ ?></td>
                            <td class="responsive-title"><?= $status['status'] ?></td>
                            <td><?= if_empty($status['description'], '-') ?></td>
                            <td><?= readable_date($status['created_at']) ?></td>
                            <td><?= $status['creator_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($status)): ?>
                        <tr>
                            <td colspan="5">No statuses available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back();" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>