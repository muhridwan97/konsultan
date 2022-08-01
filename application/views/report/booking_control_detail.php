<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Report Control</h3>
        <div class="pull-right">
            <a href="#form-filter-control" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-control">
            <input type="hidden" name="filter_control" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="booking">Booking</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=INBOUND') ?>"
                                data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                name="booking" id="booking"
                                data-placeholder="Select booking">
                            <option value=""></option>
                            <?php if (!empty($booking)): ?>
                                <option value="<?= $booking['id'] ?>" selected>
                                    <?= $booking['no_reference'] ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="handling_type">Handling Type</label>
                        <select class="form-control select2" name="handling_type[]" id="handling_type" data-placeholder="Select handling" multiple>
                            <?php foreach ($handlingTypes as $handlingType): ?>
                                <option value="<?= $handlingType['id'] ?>"<?= set_select('handling_type', $handlingType['id'], in_array($handlingType['id'], get_if_exist($_GET, 'handling_type', []))) ?>>
                                    <?= $handlingType['handling_type'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('handling_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <?php if(!empty($booking)): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Bookings</h3>
                </div>
                <div class="box-body">
                    <table class="table table-condensed table-bordered no-datatable responsive mb20">
                        <thead>
                        <tr class="<?= $booking['category'] == 'INBOUND' ? 'success' : 'danger' ?>">
                            <th>#</th>
                            <th>Booking <?= $booking['category'] == 'INBOUND' ? 'In' : 'Out' ?></th>
                            <th>Type</th>
                            <th>Customer</th>
                            <th>Booking Date</th>
                            <th>Status</th>
                            <th>Upload</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>
                                <a href="<?= site_url('booking/view/' . $booking['id']) ?>">
                                    <?= $booking['no_booking'] ?>
                                </a><br>
                                <?= $booking['no_reference'] ?>
                            </td>
                            <td><?= $booking['booking_type'] ?></td>
                            <td><?= $booking['customer_name'] ?></td>
                            <td><?= readable_date($booking['booking_date']) ?></td>
                            <td><?= $booking['status_control'] ?></td>
                            <td>
                                <a href="<?= site_url('upload-document/download-file/' . $booking['id_upload']) ?>">
                                    <?= $booking['no_upload'] ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="6">
                                <table class="table table-condensed table-stripped table-bordered no-datatable">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Job</th>
                                        <th>Completed At</th>
                                        <th>Tally</th>
                                        <th>Safe Conduct</th>
                                        <th>Driver</th>
                                        <th>No Police</th>
                                        <th>Status</th>
                                        <th>Doc</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($booking['work_orders'] as $index => $workOrder): ?>
                                        <tr class="warning">
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                                    <?= $workOrder['no_work_order'] ?>
                                                </a>
                                            </td>
                                            <td><?= readable_date($workOrder['completed_at']) ?></td>
                                            <td><?= $workOrder['tally_name'] ?></td>
                                            <td>
                                                <?php if(empty($workOrder['no_safe_conduct'])): ?>
                                                    -
                                                <?php else: ?>
                                                    <a href="<?= site_url('safe_conduct/view/' . $workOrder['id_safe_conduct']) ?>">
                                                        <?= $workOrder['no_safe_conduct'] ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= if_empty($workOrder['driver'], '-') ?></td>
                                            <td><?= if_empty($workOrder['no_police'], '-') ?></td>
                                            <td><?= if_empty($workOrder['status'], '-') ?></td>
                                            <td>
                                                <?php if (empty($workOrder['attachment'])): ?>
                                                    -
                                                <?php else: ?>
                                                    <a href="<?= asset_url('work_orders/' . $workOrder['attachment']) ?>">
                                                        Download
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan="8">
                                                <?php if(!empty($workOrder['containers'])): ?>
                                                    <?php $this->load->view('booking_control/_data_container', [
                                                        'containers' => $workOrder['containers']
                                                    ]) ?>
                                                <?php endif; ?>

                                                <?php if(!empty($workOrder['goods'])): ?>
                                                    <?php $this->load->view('booking_control/_data_goods', [
                                                        'goods' => $workOrder['goods']
                                                    ]) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($booking['work_orders'])): ?>
                                        <tr>
                                            <td colspan="9">No jobs available</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <?php if(!empty($bookingOuts)): ?>
                        <table class="table table-condensed table-striped table-bordered no-datatable responsive">
                            <thead>
                            <tr class="danger">
                                <th>#</th>
                                <th>Booking Out</th>
                                <th>Type</th>
                                <th>Customer</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                                <th>Upload</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($bookingOuts as $index => $bookingOut): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <a href="<?= site_url('booking/view/' . $bookingOut['id']) ?>">
                                            <?= $bookingOut['no_booking'] ?>
                                        </a><br>
                                        <?= $bookingOut['no_reference'] ?>
                                    </td>
                                    <td><?= $booking['booking_type'] ?></td>
                                    <td><?= $bookingOut['customer_name'] ?></td>
                                    <td><?= readable_date($bookingOut['booking_date']) ?></td>
                                    <td><?= $booking['status_control'] ?></td>
                                    <td>

                                        <a href="<?= site_url('upload-document/download-file/' . $booking['id_upload']) ?>">
                                            <?= $booking['no_upload'] ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan="6">
                                        <table class="table table-condensed table-striped table-bordered no-datatable">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Job</th>
                                                <th>Completed At</th>
                                                <th>Tally</th>
                                                <th>Safe Conduct</th>
                                                <th>Driver</th>
                                                <th>No Police</th>
                                                <th>Status</th>
                                                <th>Doc</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($bookingOut['work_orders'] as $index => $workOrder): ?>
                                                <tr class="warning">
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                                            <?= $workOrder['no_work_order'] ?>
                                                        </a>
                                                    </td>
                                                    <td><?= readable_date($workOrder['completed_at']) ?></td>
                                                    <td><?= $workOrder['tally_name'] ?></td>
                                                    <td>
                                                        <?php if(empty($workOrder['no_safe_conduct'])): ?>
                                                            -
                                                        <?php else: ?>
                                                            <a href="<?= site_url('safe_conduct/view/' . $workOrder['id_safe_conduct']) ?>">
                                                                <?= $workOrder['no_safe_conduct'] ?>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= if_empty($workOrder['driver'], '-') ?></td>
                                                    <td><?= if_empty($workOrder['no_police'], '-') ?></td>
                                                    <td><?= if_empty($workOrder['status'], '-') ?></td>
                                                    <td>
                                                        <?php if (empty($workOrder['attachment'])): ?>
                                                            -
                                                        <?php else: ?>
                                                            <a href="<?= asset_url('work_orders/' . $workOrder['attachment']) ?>">
                                                                Download
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="8">
                                                        <?php if(!empty($workOrder['containers'])): ?>
                                                            <?php $this->load->view('booking_control/_data_container', [
                                                                'containers' => $workOrder['containers']
                                                            ]) ?>
                                                        <?php endif; ?>

                                                        <?php if(!empty($workOrder['goods'])): ?>
                                                            <?php $this->load->view('booking_control/_data_goods', [
                                                                'goods' => $workOrder['goods']
                                                            ]) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if(empty($bookingOut['work_orders'])): ?>
                                                <tr>
                                                    <td colspan="9">No jobs available</td>
                                                </tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($booking) && $booking['category'] == 'INBOUND'): ?>
            <?php $this->load->view('booking_control/_data_comparator') ?>
        <?php endif; ?>

    </div>
</div>