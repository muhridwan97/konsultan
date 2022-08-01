<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Deferred TPS Summary</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter need-validation" id="form-filter">
            <input type="hidden" name="filter" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tps">TPS</label>
                                <select class="form-control select2" id="tps" name="tps" data-placeholder="Select tps" required>
                                    <option value=""></option>
                                    <?php foreach ($tps as $tpsWarehouse): ?>
                                        <option value="<?= $tpsWarehouse['id'] ?>" <?= get_url_param('tps') == $tpsWarehouse['id'] ? 'selected' : '' ?>>
                                            <?= $tpsWarehouse['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="help-block">TPS warehouse or origin</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_from">Date From</label>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Date from"
                                               maxlength="50" required autocomplete="off" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                                        <span class="help-block">TPS Out (TPP In) or TPP out date</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_to">Date To</label>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Date to"
                                               maxlength="50" required autocomplete="off" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                                        <span class="help-block">TPS Out (TPP In) or TPP out date</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default" id="btn-reset-filter">Reset Filter</a>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <?php if(!empty(get_url_param('filter'))): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-wrap no-datatable">
                    <thead>
                    <tr>
                        <th rowspan="2" class="text-center">No</th>
                        <th colspan="2" class="text-center">SPRINT</th>
                        <th colspan="4" class="text-center">Status BCF</th>
                        <th rowspan="2" class="text-center">No Container</th>
                        <th colspan="3" class="text-center">Container Size</th>
                        <th colspan="2" class="text-center">BA TPP</th>
                        <th rowspan="2" class="text-center">Gate Out TPS</th>
                        <th rowspan="2" class="text-center">Gate In TPP</th>
                        <th rowspan="2" class="text-center">Out TPP</th>
                        <th rowspan="2" class="text-center">Vessel / Voyage</th>
                        <th rowspan="2" class="text-center">Goods Description</th>
                        <th rowspan="2" class="text-center">Consignee</th>
                        <th class="text-center">Position</th>
                        <th class="text-center">Status</th>
                        <th rowspan="2" class="text-center">Gate Out TPP</th>
                        <th rowspan="2" class="text-center">Description</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>BTD/BCF 1.5</th>
                        <th>No Kep BDN</th>
                        <th class="">No. Kep BMN</th>
                        <th>BTD/BCF Date</th>
                        <th>20</th>
                        <th>40</th>
                        <th>45</th>
                        <th>No</th>
                        <th>Date</th>
                        <th>In/Out</th>
                        <th>Lelang/Musnah/Hibah</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reports as $index => $report): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= if_empty($report['no_sprint'], '-') ?></td>
                            <td><?= if_empty($report['sprint_date'], '-') ?></td>
                            <td><?= $report['no_reference'] ?></td>
                            <td><?= if_empty($report['no_bdn'], '-') ?></td>
                            <td><?= if_empty($report['no_bmn'], '-') ?></td>
                            <td><?= $report['reference_date'] ?></td>
                            <td><?= $report['no_container'] ?></td>
                            <td><?= $report['container_size_20'] ?></td>
                            <td><?= $report['container_size_40'] ?></td>
                            <td><?= $report['container_size_45'] ?></td>
                            <td><?= if_empty($report['no_booking_news'], '-') ?></td>
                            <td><?= if_empty($report['booking_news_date'], '-') ?></td>
                            <td><?= if_empty($report['tps_gate_out_date'], '-') ?></td>
                            <td><?= $report['tpp_gate_in_date'] ?></td>
                            <td><?= if_empty($report['tpp_out_date'], '-') ?></td>
                            <td><?= $report['vessel'] ?> <?= $report['voyage'] ?></td>
                            <td><?= if_empty($report['goods_description'], '-') ?></td>
                            <td><?= $report['customer_name'] ?></td>
                            <td><?= $report['position'] ?></td>
                            <td><?= if_empty($report['status'], '-') ?></td>
                            <td><?= if_empty($report['tpp_gate_out_date'], '-') ?></td>
                            <td><?= if_empty($report['description'], '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(get_url_param('filter') && empty($reports)): ?>
                        <tr>
                            <td colspan="22">No report tps deferred available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if(empty(get_url_param('filter'))): ?>
    <div class="panel">
        <div class="panel-body">
            <p class="lead mb0">
                Adjust filter to fetch report data
            </p>
        </div>
    </div>
<?php endif; ?>