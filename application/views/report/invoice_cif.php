<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">CIF Invoice</h3>
        <div class="pull-right">
            <a href="#form-filter-invoice" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_invoice', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Data
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true&export_type=monthly" class="btn btn-danger">
                Export Monthly
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-invoice" <?= !get_url_param('filter_invoice') ? 'style="display:none"' : ''  ?>>
            <input type="hidden" name="filter_invoice" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="owner">Owner</label>
                        <select class="form-control select2 select2-ajax" data-url="<?= site_url('people/ajax_get_people') ?>" data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>" name="owner" id="owner" data-placeholder="Select owner" multiple>
                            <option value=""></option>
                            <?php foreach ($owners as $owner) : ?>
                                <option value="<?= $owner['id'] ?>" selected>
                                    <?= $owner['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="booking">Booking</label>
                        <select class="form-control select2 select2-ajax" data-url="<?= site_url('booking/ajax_get_booking_by_keyword') ?>" data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name" name="booking" id="booking" data-placeholder="Select booking">
                            <option value="0">ALL BOOKINGS</option>
                            <?php if (!empty($booking)) : ?>
                                <option value="<?= $booking['id'] ?>" selected>
                                    <?= $booking['no_reference'] ?>
                                </option>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_type">Date Type</label>
                                <select class="form-control select2" name="date_type" id="date_type" data-placeholder="Select type">
                                    <option value="registration_dates.value">TGL NOPEN INBOUND</option>
                                    <option value="registration_date_outbounds.value">TGL NOPEN OUTBOUND</option>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="date_from">Date From</label>
                                    <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Date from" maxlength="50" value="<?= get_url_param('date_from') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="date_to">Date To</label>
                                    <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Date to" maxlength="50" value="<?= get_url_param('date_to') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered no-datatable no-wrap responsive">
                <thead>
                    <tr>
                        <th style="width: 25px">NO</th>
                        <th>JENIS DOK</th>
                        <th>AJU</th>
                        <th>NOPEN</th>
                        <th>TGL NOPEN</th>
                        <th>PARTY</th>
                        <th>CARGO IN</th>
                        <th>NET WEIGHT</th>
                        <th>GROSS WEIGHT</th>
                        <th>NILAI INVOICE</th>
                        <th>NILAI INVOICE AS USD</th>
                        <th>CUSTOMER</th>
                        <th>DOK PENGELUARAN</th>
                        <th>AJU PENGELUARAN</th>
                        <th>NOPEN PENGELUARAN</th>
                        <th>TGL NOPEN PENGELUARAN</th>
                        <th>KONFIRMASI BAYAR</th>
                        <th>SPPB</th>
                        <th>NILAI INVOICE OUT</th>
                        <th>NILAI INVOICE OUT AS USD</th>
                        <th>NDPBM</th>
                        <th>NILAI PABEAN</th>
                        <th>BM</th>
                        <th>PPN</th>
                        <th>PPH</th>
                        <th>CARGO OUT</th>
                        <th>NET WEIGHT</th>
                        <th>GROSS WEIGHT</th>
                        <th>GATE OUT</th>
                        <th>SPPD</th>
                        <th>BALANCE</th>
                        <th>BALANCE AS USD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = isset($reports) ? ($reports['current_page'] - 1) * $reports['per_page'] : 0 ?>
                    <?php $lastReference = '' ?>
                    <?php foreach ($reports['data'] as $report) : ?>
                        <tr>
                            <?php if ($report['no_reference_inbound'] != $lastReference) : ?>
                                <?php $no++ ?>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $no ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $report['booking_type_inbound'] ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>">
                                    <a href="<?= site_url('booking/cif/' . $report['id_booking_in']) ?>">
                                        <?= $report['no_reference_inbound'] ?>
                                    </a>
                                </td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $report['no_registration'] ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $report['registration_date'] ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $report['party'] ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $report['cargo_inbound'] ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= numerical($report['net_weight_inbound'], 2, true) ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= numerical($report['gross_weight_inbound'], 2, true) ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $report['currency_from'] ?> <?= numerical($report['total_cif_inbound_from'], 2, true) ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $report['currency_to'] ?> <?= numerical($report['total_cif_inbound_to'], 2, true) ?></td>
                                <td <?= $no % 2 == 0 ? 'class="active"' : '' ?> rowspan="<?= $report['total_outbound'] ?>"><?= $report['customer_name'] ?></td>
                            <?php endif; ?>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['booking_type_outbound'] ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>>
                                <?php if (empty($report['no_reference_outbound'])) : ?>
                                    -
                                <?php else : ?>
                                    <a href="<?= site_url('booking/cif/' . $report['id_booking_out']) ?>">
                                        <?= $report['no_reference_outbound'] ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['no_registration_outbound'] ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['registration_date_outbound'] ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['payment_confirmation_date'] ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['sppb_date'] ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['currency_from'] ?> <?= numerical($report['total_cif_outbound_from'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['currency_to'] ?> <?= numerical($report['total_cif_outbound_to'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>>Rp. <?= numerical($report['ndpbm'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>>Rp. <?= numerical($report['customs_value'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>>Rp. <?= numerical($report['import_duty'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>>Rp. <?= numerical($report['vat'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>>Rp. <?= numerical($report['income_tax'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['cargo_outbound'] ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= numerical($report['net_weight_outbound'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= numerical($report['gross_weight_outbound'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['gate_out'] ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['sppd_date'] ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['currency_from'] ?> <?= numerical($report['balance_from_currency'], 2, true) ?></td>
                            <td <?= $no % 2 == 0 ? 'class="active"' : '' ?>><?= $report['currency_to'] ?> <?= numerical($report['balance_to_currency'], 2, true) ?></td>
                            <?php if ($report['no_reference_inbound'] != $lastReference) : ?>
                                <?php $lastReference = $report['no_reference_inbound'] ?>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($reports['data'])) : ?>
                        <tr>
                            <td colspan="17">No invoice data</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
        <?php $this->load->view('template/_pagination', ['pagination' => $reports]) ?>
    </div>
</div>