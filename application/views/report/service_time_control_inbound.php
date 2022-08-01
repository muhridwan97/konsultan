<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">ST Control Inbound</h3>
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
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="search" value="<?= set_value('search', get_url_param('search')) ?>"
                                       class="form-control" id="search" name="search" placeholder="Type query search">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="sppd_status">SPPD Status</label>
                                <select class="form-control select2" name="sppd_status" id="sppd_status">
                                    <option value="">ALL</option>
                                    <option value="DONE"<?= set_select('sppd_status', 'DONE', get_url_param('sppd_status') == 'DONE') ?>>DONE</option>
                                    <option value="NOT YET"<?= set_select('sppd_status', 'NOT YET', get_url_param('sppd_status') == 'NOT YET') ?>>NOT YET</option>
                                </select>
                            </div>
                        </div>
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
                                <label for="booking">Booking In</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=INBOUND') ?>"
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
                    <th rowspan="2">CUSTOMER NAME</th>
                    <th>CUSTOMER</th>
                    <th colspan="2" class="text-center">COMPLIANCE</th>
                    <th>CUSTOMER</th>
                    <th colspan="2" class="text-center">COMPLIANCE</th>
                    <th colspan="4" class="text-center">OPERATIONAL</th>
                    <th>COMPLIANCE</th>
                    <th colspan="3" class="text-center">SERVICE TIME</th>
                </tr>
                <tr>
                    <th>
                        UPLOAD DATE<br>
                        <small class="text-muted">Upload < ATA</small>
                    </th>
                    <th>
                        DRAFT<br>
                        <small class="text-muted">Upload - Draft (2 batch)</small>
                    </th>
                    <th>
                        ATA<br>
                        <!-- <small class="text-muted">Draft - Confirm (2 batch)</small> -->
                    </th>
                    <th>
                        CONFIRM<br>
                        <small class="text-muted">Draft - Confirm (2 batch)</small>
                    </th>
                    <th>
                        DO<br>
                        <small class="text-muted">ATA + 1 days</small>
                    </th>
                    <th>
                        SPPB<br>
                        <small class="text-muted">DO - SPPB (2 batch)</small>
                    </th>
                    <th>
                        SECURITY IN/PENARIKAN<br>
                        <small class="text-muted">SPPB - Security Out (In TCI) (2 batch)</small>
                    </th>
                    <th>
                        UNLOAD<br>
                        <small class="text-muted">Gate In - Unload Complete (2 batch)</small>
                    </th>
                    <!-- <th>
                        STRIPPING<br>
                        <small class="text-muted">Gate In - Stripping Complete (2 batch)</small>
                    </th> -->
                    <th>
                        BONGKAR<br>
                        <small class="text-muted">Last In TCI* - Booking Complete (2 batch)</small>
                    </th>
                    <th>
                        BOOKING COMPLETE<br>
                        <small class="text-muted">Last Unload / Stripping - Booking In Complete (1 batch)</small>
                    </th>
                    <th>
                        SPPD<br>
                        <small class="text-muted">Booking In Complete - SPPD (2 batch)</small>
                    </th>
                    <th>
                        ST DOC<br>
                        <small class="text-muted">ATA / Upload (Nearest to SPPB) - SPPB (1 Day)</small>
                    </th>
                    <th>
                        ST INBOUND DELIVERY<br>
                        <small class="text-muted">SPPB - Sec Out (In TCI) (2 Batch)</small>
                    </th>
                    <th>
                        ST UNLOADING<br>
                        <small class="text-muted">Sec Out (In TCI) - Booking In Complete (2 Batch)</small>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $no = isset($reports) ? ($reports['current_page'] - 1) * $reports['per_page'] : 0 ?>
                <?php $lastNoUpload = '' ?>
                <?php foreach ($reports['data'] as $report): ?>
                    <tr<?= $no % 2 == 0 ? ' class="active"' : ''?>>
                        <?php if($report['no_upload'] != $lastNoUpload): ?>
                            <td rowspan="<?= $report['total_detail'] ?>" class="text-md-center"><?= ++$no ?></td>
                            <td rowspan="<?= $report['total_detail'] ?>"><?= if_empty($report['no_reference'], '-') ?></td>
                            <td rowspan="<?= $report['total_detail'] ?>"><?= if_empty($report['customer_name'], '-') ?></td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_upload'] == '' ? 'warning' : ($report['is_late_upload'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['upload_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_draft'] == '' ? 'warning' : ($report['is_late_draft'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['draft_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>">
                                <?= if_empty(format_date($report['ata_date'], 'd F Y'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_confirmation'] == '' ? 'warning' : ($report['is_late_confirmation'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['confirmation_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_do'] == '' ? 'warning' : ($report['is_late_do'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['do_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_sppb'] == '' ? 'warning' : ($report['is_late_sppb'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['sppb_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td  rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_security_inbound'] == '' ? 'warning' : ($report['is_late_security_inbound'] >= 1 ? 'danger' : 'success') ?>">
                            <?= if_empty(format_date($report['security_inbound_max'], 'd F Y H:i'), '-') ?>
                        </td>
                        <?php endif; ?>
                        
                        <td class="<?= $report['is_late_unload'] == '' ? 'warning' : ($report['is_late_unload'] >= 1 ? 'danger' : 'success') ?>">
                            <?= if_empty(format_date($report['unload_date'], 'd F Y H:i'), '-') ?>
                        </td>
                        <td style="display:none;" class="<?=$report['is_late_stripping'] == ''? 'warning' : ($report['is_late_stripping'] >= 1 ? 'danger' : 'success') ?>">
                            <?= if_empty(format_date($report['stripping_date'], 'd F Y H:i'), '-') ?>
                        </td>
                        
                        <?php if($report['no_upload'] != $lastNoUpload): ?>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_bongkar'] === '' ? 'warning' : ($report['is_late_bongkar'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['bongkar_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_completed'] == '' ? 'warning' : ($report['is_late_completed'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['completed_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['is_late_sppd'] == '' ? 'warning' : ($report['is_late_sppd'] >= 1 ? 'danger' : 'success') ?>">
                                <?= if_empty(format_date($report['sppd_date'], 'd F Y H:i'), '-') ?>
                            </td>
                            <td rowspan="<?= $report['total_detail'] ?>" class="<?= $report['service_time_doc'] == '' ? 'warning' : ($report['service_time_doc'] > 1 ? 'danger' : 'success') ?>">
                                <?= if_empty($report['service_time_doc'], '-', '', ' days') ?>
                            </td>
                            <?php $lastNoUpload = $report['no_upload'] ?>
                        <?php endif; ?>
                        <td class="<?= $report['is_late_st_inbound_delivery'] == '' ? 'warning' : ($report['is_late_st_inbound_delivery'] ? 'danger' : 'success') ?>">
                            <?= if_empty($report['service_time_inbound_delivery_label'], '-') ?>
                        </td>
                        <td class="<?= $report['is_late_st_unload'] == '' ? 'warning' : ($report['is_late_st_unload'] ? 'danger' : 'success') ?>">
                            <?= if_empty($report['service_time_unload_label'], '-') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($reports['data'])): ?>
                    <tr>
                        <td colspan="17">No data available</td>
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
                            <span style="background-color:#90EE90" class="green" data-type="is_late_draft" data-bound="inbound"><?= $reports['summary']['drafting']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_draft" data-bound="inbound"><?= $reports['summary']['drafting']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_draft" data-bound="inbound"><?= $reports['summary']['drafting']['kuning'];?></span>/<?= ($reports['summary']['drafting']['hijau']+$reports['summary']['drafting']['merah']);?> &nbsp <?= $reports['summary']['drafting']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">CONFIRM</p>
                        <h3 class="mt0">
                            <span style="background-color:#90EE90" class="green" data-type="is_late_confirmation" data-bound="inbound"><?= $reports['summary']['confirm']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_confirmation" data-bound="inbound"><?= $reports['summary']['confirm']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_confirmation" data-bound="inbound"><?= $reports['summary']['confirm']['kuning'];?></span>/<?= ($reports['summary']['confirm']['hijau']+$reports['summary']['confirm']['merah']);?> &nbsp <?= $reports['summary']['confirm']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">DO</p>
                        <h3 class="mt0">
                            <span style="background-color:#90EE90" class="green" data-type="is_late_do" data-bound="inbound"><?= $reports['summary']['do']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_do" data-bound="inbound"><?= $reports['summary']['do']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_do" data-bound="inbound"><?= $reports['summary']['do']['kuning'];?></span>/<?= ($reports['summary']['do']['hijau']+$reports['summary']['do']['merah']);?> &nbsp <?= $reports['summary']['do']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">SPPB</p>
                        <h3 class="mt0">
                        <span style="background-color:#90EE90" class="green" data-type="is_late_sppb" data-bound="inbound"><?= $reports['summary']['sppb']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_sppb" data-bound="inbound"><?= $reports['summary']['sppb']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_sppb" data-bound="inbound"><?= $reports['summary']['sppb']['kuning'];?></span>/<?= ($reports['summary']['sppb']['hijau']+$reports['summary']['sppb']['merah']);?> &nbsp <?= $reports['summary']['sppb']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">PENARIKAN</p>
                        <h3 class="mt0">
                        <span style="background-color:#90EE90" class="green" data-type="is_late_security_inbound" data-bound="inbound"><?= $reports['summary']['penarikan']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_security_inbound" data-bound="inbound"><?= $reports['summary']['penarikan']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_security_inbound" data-bound="inbound"><?= $reports['summary']['penarikan']['kuning'];?></span>/<?= ($reports['summary']['penarikan']['hijau']+$reports['summary']['penarikan']['merah']);?> &nbsp <?= $reports['summary']['penarikan']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="lead mb0">BONGKAR</p>
                        <h3 class="mt0">
                        <span style="background-color:#90EE90" class="green" data-type="is_late_bongkar" data-bound="inbound"><?= $reports['summary']['bongkar']['hijau'];?></span>|<span style="background-color:#F08080" class="red" data-type="is_late_bongkar" data-bound="inbound"><?= $reports['summary']['bongkar']['merah'];?></span>|<span style="background-color:#FFFFE0" class="yellow" data-type="is_late_bongkar" data-bound="inbound"><?= $reports['summary']['bongkar']['kuning'];?></span>/<?= ($reports['summary']['bongkar']['hijau']+$reports['summary']['bongkar']['merah']);?> &nbsp <?= $reports['summary']['bongkar']['persen'];?>%
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('report/_modal_filter_by_color') ?>
<?php $this->load->view('report/_modal_filter_by_color_yellow') ?>