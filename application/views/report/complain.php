<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Complains</h3>
        <div class="pull-right">
            <a href="#filter_complain" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_complain', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=GOODS" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_complain', [
            'filter_complain' => 'filter_complain',
            'hidden' => isset($_GET['filter_complain']) ? false : true
        ]) ?>
        <div class="table-responsive">
        <table class="table table-bordered table-striped no-datatable responsive no-wrap">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>No Complain</th>
                <th>Complain Date</th>
                <th>Customer</th>
                <th>Department</th>
                <th>Major/Minor</th>
                <th>Complain</th>
                <th>Investigation</th>
                <th>Conclusion</th>
                <th>Close Date</th>
                <th>FTKP</th>
            </tr>
            </thead>
            <tbody>
                <?php $number = 0; ?>
                <?php foreach ($complains as $complain): ?>
                <tr>
                    <td><?= $number = $number+1; ?></td>
                    <td><?= $complain['no_complain'] ?></td>
                    <td><?= $complain['complain_date'] ?></td>
                    <td><?= $complain['customer'] ?></td>
                    <td><?= $complain['department'] ?></td>
                    <td><?= $complain['category'] ?> (<?= $complain['value_type'] ?>)</td>
                    <td><?= $complain['complain'] ?></td>
                    <td><?= $complain['investigation_result'] ?></td>
                    <td><?= $complain['conclusion'] ?></td>
                    <td><?= $complain['close_date'] ?></td>
                    <td><?= $complain['ftkp'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
