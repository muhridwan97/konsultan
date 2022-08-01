<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Delivery Orders</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-delivery-order">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Booking</th>
                <th>Customer</th>
                <th>Handled By</th>
                <th>ETA</th>
                <th>Status</th>
                <th>Docs</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($bookings as $booking): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title">
                        <p class="mb0">
                            <a href="<?= site_url('booking/view/' . $booking['id']) ?>">
                                <?= $booking['no_booking'] ?>
                            </a>
                        </p>
                        <small class="text-muted"><?= $booking['no_reference'] ?></small>
                    </td>
                    <td>
                        <p class="mb0"><?= $booking['customer_name'] ?></p>
                        <a class="small" href="<?= site_url('upload/view/' . $booking['id_upload']) ?>">
                            <?= $booking['no_upload'] ?>
                        </a>
                    </td>
                    <td>
                        <?php if(empty($booking['assigned_to'])): ?>
                            <a href="<?= site_url('booking-assignment/create?id_booking=' . $booking['id']) ?>">Assign to user</a>
                        <?php else: ?>
                            <?= $booking['assigned_to'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <p class="mb0"><?= readable_date($booking['eta'], false) ?></p>
                        <span class="text-muted"><?= $booking['diff_eta'] ?> Days</span>
                    </td>
                    <td>
                        <?php if($booking['diff_eta'] == 0): ?>
                            <span class="label label-primary">BERTHING</span>
                        <?php elseif ($booking['diff_eta'] > 0): ?>
                            <span class="label label-default">ONBOARD</span>
                        <?php else: ?>
                            <span class="label label-danger">OVERDUE</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($booking['has_tila']): ?>
                            <span class="label label-success">UPLOADED</span>
                        <?php else: ?>
                            <span class="label label-default">IN PROCESS</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right row-delivery-order"
                                data-id="<?= $booking['id'] ?>"
                                data-no="<?= $booking['no_booking'] ?>">

                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_ORDER_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('delivery-order/view/' . $booking['id']) ?>">
                                            <i class="fa ion-search"></i> View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(!$booking['has_do']): ?>
                                    <li>
                                        <a href="<?= site_url('upload/response/' . $booking['id_upload'] . '?id_document_type=' . $doDocId . '&redirect=' . site_url('delivery-order')) ?>">
                                            <i class="fa ion-upload"></i> Upload DO
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if($booking['has_do'] && !$booking['has_ata']): ?>
                                    <li>
                                        <a href="<?= site_url('upload/response/' . $booking['id_upload'] . '?id_document_type=' . $ataDocId . '&redirect=' . site_url('delivery-order')) ?>">
                                            <i class="fa ion-upload"></i> Upload ATA
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(!$booking['has_sppb']): ?>
                                    <li>
                                        <a href="<?= site_url('upload/response/' . $booking['id_upload'] . '?id_document_type=' . $sppbDocId . '&redirect=' . site_url('delivery-order')) ?>">
                                            <i class="fa ion-upload"></i> Upload SPPB
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if($booking['has_do'] && $booking['has_sppb'] && !$booking['has_tila']): ?>
                                    <li>
                                        <a href="<?= site_url('upload/response/' . $booking['id_upload'] . '?id_document_type=' . $tilaDocId . '&redirect=' . site_url('delivery-order')) ?>">
                                            <i class="fa ion-upload"></i> Upload Tila
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/delivery_order.js') ?>" defer></script>