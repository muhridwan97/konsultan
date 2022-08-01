<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">ST Control Outbound</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter" <?= get_url_param('filter', 0) ? '' : 'style="display: none"' ?>>
            <input type="hidden" name="filter" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="search" value="<?= set_value('search', get_url_param('search')) ?>"
                               class="form-control" id="search" name="search" placeholder="Type query search">
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="owner">Owner</label>
                                <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                                    <select class="form-control select2 select2-ajax"
                                            data-url="<?= site_url('people/ajax_get_people') ?>"
                                            data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                            name="owner[]" id="owner"
                                            data-placeholder="Select owner" multiple>
                                        <option value=""></option>
                                        <?php foreach ($owners as $owner): ?>
                                            <option value="<?= $owner['id'] ?>" selected>
                                                <?= $owner['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= UserModel::authenticatedUserData('name') ?>
                                        (<?= UserModel::authenticatedUserData('email') ?>)
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="booking">Booking Out</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=OUTBOUND') ?>"
                                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                        name="booking[]" id="booking"
                                        data-placeholder="Select booking" multiple>
                                    <option value=""></option>
                                    <?php foreach ($bookings as $booking): ?>
                                        <option value="<?= $booking['id'] ?>" selected>
                                            <?= $booking['no_reference'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="date_from">Date From</label>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                               placeholder="Date from"
                                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_to">Date To</label>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                               placeholder="Date to"
                                               maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-condensed no-datatable responsive no-wrap">
                <thead>
                <tr>
                    <th style="width: 60px" rowspan="2">NO</th>
                    <th rowspan="2">NO REFERENCE</th>
                    <th rowspan="2">INBOUND REFERENCE</th>
                    <th rowspan="2">CUSTOMER NAME</th>
                    <th>CUSTOMER</th>
                    <th>COMPLIANCE</th>
                    <th>CUSTOMER</th>
                    <th>COMPLIANCE</th>
                    <th colspan="2" class="text-center">CUSTOMER</th>
                    <th>COMPLIANCE</th>
                    <th colspan="4" class="text-center">OPERATIONAL</th>
                    <th>SERVICE TIME</th>
                </tr>
                <tr>
                    <th>UPLOAD DATE</th>
                    <th>
                        DRAFT<br>
                        <small class="text-muted">Upload - Draft (2 batch)</small>
                    </th>
                    <th>
                        CONFIRM<br>
                        <small class="text-muted">Draft - Confirm (2 batch)</small>
                    </th>
                    <th>BILLING<br>
                        <small class="text-muted">Confirm - Billing (2 batch)</small>
                    </th>
                    <th>
                        PAYMENT<br>
                        <small class="text-muted">Billing - BPN (2 batch)</small>
                    </th>
                    <th>
                        BPN<br>
                        <small class="text-muted">5 Days after Billing</small>
                    </th>
                    <th>
                        SPPB<br>
                        <small class="text-muted">BPN - SPPB (2 batch)</small>
                    </th>
                    <th>SECURITY / TEP CHECK (IN TCI)</th>
                    <th>
                        STUFFING / LOAD<br>
                        <small class="text-muted">TEP Check In - Complete Load (2 batch)</small>
                    </th>
                    <th>
                        GATE OUT<br>
                        <small class="text-muted">Complete Load - Gate Out (2 batch)</small>
                    </th>
                    <th>
                        MUAT<br>
                        <small class="text-muted">SPPB/Sec_start - Booking Complete (2 batch)</small>
                    </th>
                    <th>
                        ST LOADING<br>
                        <small class="text-muted">Security In (Out TCI) - Booking Out Complete (2 batch)</small>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $no = isset($reports) ? ($reports['current_page'] - 1) * $reports['per_page'] : 0 ?>
                <?php $lastNoUpload = '' ?>
                <?php foreach ($reports['data'] as $report): ?>
                    <tr>
                        <?php if($report['no_upload'] != $lastNoUpload): ?>
                            <td rowspan="<?= $report['total_detail'] ?>" class="text-md-center"><?= ++$no ?></td>
                            <td rowspan="<?= $report['total_detail'] ?>"><?= if_empty($report['no_reference'], '-') ?></td>
                            <td rowspan="<?= $report['total_detail'] ?>"><?= if_empty($report['booking_in_reference'], '-') ?></td>
                            <td rowspan="<?= $report['total_detail'] ?>"><?= if_empty($report['customer_name'], '-') ?></td>
                            <td rowspan="<?= $report['total_detail'] ?>">
                                <?= if_empty(format_date($report['upload_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_draft'] == '' ? 'warning' : ($report['is_late_draft'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['draft_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_confirmation'] == '' ? 'warning' : ($report['is_late_confirmation'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['confirmation_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_billing'] == '' ? 'warning' : ($report['is_late_billing'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['billing_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_payment'] == '' ? 'warning' : ($report['is_late_payment'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['payment_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_bpn'] == '' ? 'warning' : ($report['is_late_bpn'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['bpn_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_sppb'] == '' ? 'warning' : ($report['is_late_sppb'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['sppb_date'], 'd F Y H:i'), '-') ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?= if_empty(format_date($report['tep_checked_in_date'], 'd F Y H:i'), '-') ?>
                        </td>
                        <td class="<?= $report['is_late_load'] == '' ? 'warning' : ($report['is_late_load'] >= 1 ? 'danger' : 'success') ?>">
                            <?= if_empty(format_date($report['load_date'], 'd F Y H:i'), '-') ?>
                        </td>
                        <td class="<?= $report['is_late_gate_out'] == '' ? 'warning' : ($report['is_late_gate_out'] >= 1 ? 'danger' : 'success') ?>">
                            <?= if_empty(format_date($report['gate_out_date'], 'd F Y H:i'), '-') ?>
                        </td>
                        <?php if($report['no_upload'] != $lastNoUpload): ?>
                        <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_booking_complete'] == '' ? 'warning' : ($report['is_late_booking_complete'] >= 1 ? 'danger' : 'success') ?>">
                            <?= if_empty(format_date($report['booking_complete'], 'd F Y H:i'), '-') ?>
                        </td>
                        <?php $lastNoUpload = $report['no_upload'] ?>
                        <?php endif; ?>
                        <td class="<?= $report['is_late_st_load'] == '' ? 'warning' : ($report['is_late_st_load'] ? 'danger' : 'success') ?>">
                            <?= if_empty($report['service_time_load_label'], '-') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($reports['data'])): ?>
                    <tr>
                        <td colspan="15">No data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('template/_pagination', ['pagination' => $reports]) ?>
        <div class="row mt20">
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">DRAFTING</p>
                        <h3 class="mt0">
                        <span style="background-color:#90EE90" class="green" data-type="is_late_draft" data-bound="outbound"><?= $reports['summary']['drafting']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_draft" data-bound="outbound"><?= $reports['summary']['drafting']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_draft" data-bound="outbound"><?= $reports['summary']['drafting']['kuning'];?></span>/<?= ($reports['summary']['drafting']['hijau']+$reports['summary']['drafting']['merah']);?> &nbsp <?= $reports['summary']['drafting']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">CONFIRM</p>
                        <h3 class="mt0">
                        <span style="background-color:#90EE90" class="green" data-type="is_late_confirmation" data-bound="outbound"><?= $reports['summary']['confirm']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_confirmation" data-bound="outbound"><?= $reports['summary']['confirm']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_confirmation" data-bound="outbound"><?= $reports['summary']['confirm']['kuning'];?></span>/<?= ($reports['summary']['confirm']['hijau']+$reports['summary']['confirm']['merah']);?> &nbsp <?= $reports['summary']['confirm']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">BILLING</p>
                        <h3 class="mt0">
                            <span style="background-color:#90EE90" class="green" data-type="is_late_billing" data-bound="outbound"><?= $reports['summary']['billing']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_billing" data-bound="outbound"><?= $reports['summary']['billing']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_billing" data-bound="outbound"><?= $reports['summary']['billing']['kuning'];?></span>/<?= ($reports['summary']['billing']['hijau']+$reports['summary']['billing']['merah']);?> &nbsp <?= $reports['summary']['billing']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">PAYMENT</p>
                        <h3 class="mt0">
                            <span style="background-color:#90EE90" class="green" data-type="is_late_payment" data-bound="outbound"><?= $reports['summary']['payment']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_payment" data-bound="outbound"><?= $reports['summary']['payment']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_payment" data-bound="outbound"><?= $reports['summary']['payment']['kuning'];?></span>/<?= ($reports['summary']['payment']['hijau']+$reports['summary']['payment']['merah']);?> &nbsp <?= $reports['summary']['payment']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">SPPB</p>
                        <h3 class="mt0">
                            <span style="background-color:#90EE90" class="green" data-type="is_late_sppb" data-bound="outbound"><?= $reports['summary']['sppb']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_sppb" data-bound="outbound"><?= $reports['summary']['sppb']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_sppb" data-bound="outbound"><?= $reports['summary']['sppb']['kuning'];?></span>/<?= ($reports['summary']['sppb']['hijau']+$reports['summary']['sppb']['merah']);?> &nbsp <?= $reports['summary']['sppb']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">MUAT</p>
                        <h3 class="mt0">
                        <span style="background-color:#90EE90" class="green" data-type="is_late_booking_complete" data-bound="outbound"><?= $reports['summary']['muat']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_booking_complete" data-bound="outbound"><?= $reports['summary']['muat']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_booking_complete" data-bound="outbound"><?= $reports['summary']['muat']['kuning'];?></span>/<?= ($reports['summary']['muat']['hijau']+$reports['summary']['muat']['merah']);?> &nbsp <?= $reports['summary']['muat']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
    </div>
</div>
<?php $this->load->view('report/_modal_filter_by_color') ?>
<?php $this->load->view('report/_modal_filter_by_color_yellow') ?>