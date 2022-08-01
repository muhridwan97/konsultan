<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Handover Daily Summary</h3>
        <div class="pull-right">
            <a href="#form-filter-handover" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-handover">
            <input type="hidden" name="filter_handover" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="from_date">Date From</label>
                                <input type="text" class="form-control datepicker" id="from_date" name="from_date"
                                       placeholder="Set handover from date"
                                       maxlength="50" value="<?= set_value('from_date', get_url_param('from_date', readable_date('now', false))) ?>">
                            </div>
                            <div class="col-sm-6">
                                <label for="to_date">Date To</label>
                                <input type="text" class="form-control datepicker" id="to_date" name="to_date"
                                       placeholder="Set handover to date"
                                       maxlength="50" value="<?= set_value('to_date', get_url_param('to_date', readable_date('now', false))) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url('report/handover') ?>" class="btn btn-default" id="btn-reset-filter">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered no-wrap no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Customer</th>
                    <th>Handling Type</th>
                    <th>No Job</th>
                    <th>No Police</th>
                    <th>No Invoice</th>
                    <th>No Booking</th>
                    <th>Job Complete Date</th>
                    <th>Taken By</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1 ?>
                <?php foreach ($reportHandovers as $reportHandover): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $reportHandover['customer_name'] ?></td>
                        <td><?= $reportHandover['handling_type'] ?></td>
                        <td>
                            <a href="<?= site_url('work-order/view/' . $reportHandover['id']) ?>">
                                <?= $reportHandover['no_work_order'] ?>
                            </a>
                        </td>
                        <td>
                            <?php if(empty($reportHandover['no_police'])): ?>
                                -
                            <?php else: ?>
                                <a href="<?= site_url('safe-conduct/view/' . $reportHandover['id_safe_conduct']) ?>">
                                    <?= $reportHandover['no_police'] ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td><?= if_empty($reportHandover['invoice_number'], '-') ?></td>
                        <td>
                            <a href="<?= site_url('booking/view/' . $reportHandover['id_booking']) ?>">
                                <?= $reportHandover['no_booking'] ?>
                            </a>
                        </td>
                        <td><?= format_date($reportHandover['completed_at'], 'd F Y H:i') ?></td>
                        <td><?= $reportHandover['tally_name'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($reportHandovers)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No data handover available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="text-right">
                <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&print=1" class="btn btn-primary">
                    Print Handover
                </a>
            </div>
        </div>
    </div>
</div>
