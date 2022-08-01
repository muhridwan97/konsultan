<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Event Summary</h3>
        <div class="pull-right">
            <a href="#form-filter-container" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report_tpp/_filter_event_summary', ['hidden' => false]) ?>

        <?php foreach ($reportData as $keyMonthYear => $report) : ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= $keyMonthYear ?>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped no-wrap">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Withdrawal Event News No.</th>
                                <th>Customs Order No.</th>
                                <th>BC 1.5</th>
                                <th>BC 1.5 Date</th>
                                <th>TPS</th>
                                <th>BC 1.1</th>
                                <th>BC 1.1 Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; ?>
                            <?php if (key_exists('WITHDRAWAL', $report)) : ?>
                                <?php foreach ($report['WITHDRAWAL'] as $report): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= if_empty($report['no_booking_news'], '-') ?></td>
                                        <td><?= if_empty($report['no_sprint'], '-') ?></td>
                                        <td><?= if_empty($report['no_reference'], '-') ?></td>
                                        <td><?= readable_date($report['reference_date'], false) ?></td>
                                        <td><?= if_empty($report['tps'], '-') ?></td>
                                        <td><?= if_empty($report['no_bc11'], '-') ?></td>
                                        <td><?= readable_date($report['bc11_date'], false) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped no-wrap">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Canceling Event News No.</th>
                                <th>Customs Order No.</th>
                                <th>BC 1.5</th>
                                <th>BC 1.5 Date</th>
                                <th>TPS</th>
                                <th>BC 1.1</th>
                                <th>BC 1.1 Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; ?>
                            <?php if (key_exists('CANCELING', $report)) : ?>
                                <?php foreach ($report['CANCELING'] as $report): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= if_empty($report['no_booking_news'], '-') ?></td>
                                        <td><?= if_empty($report['no_sprint'], '-') ?></td>
                                        <td><?= if_empty($report['no_reference'], '-') ?></td>
                                        <td><?= readable_date($report['reference_date'], false) ?></td>
                                        <td><?= if_empty($report['tps'], '-') ?></td>
                                        <td><?= if_empty($report['no_bc11'], '-') ?></td>
                                        <td><?= readable_date($report['bc11_date'], false) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if(get_url_param('filter_activity') && empty($reportCustomsDaily)): ?>
    <div class="panel">
        <div class="panel-body">
            <p class="lead mb0">
                No data custom daily available
            </p>
        </div>
    </div>
<?php endif; ?>