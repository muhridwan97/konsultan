<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Sppb Request Detail Week <?= substr(get_url_param('year_week',0),-2) ?></h3>
        <div class="pull-right">
			<a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
				<i class="fa fa-file-excel-o"></i>
			</a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-sppb-detail">
            <thead>
            <tr>
                <th style="width: 30px">NO</th>
                <th>NO AJU</th>
                <th>CUSTOMER</th>
                <th>GOODS</th>
                <th>SPPB DATE</th>
                <th>NO REQUEST</th>
                <th>REQUEST AT</th>
                <th>COMPLETE AT</th>
                <th>DATE DIFF</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            $total_day = 0;
            foreach ($reportPerformances as $data): ?>
            <?php
            $total_day+= $data['diff_date'];
             ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= if_empty($data['no_reference'], '-') ?></td>
                    <td><?= if_empty($data['customer_name'], '-')?></td>
                    <td><?= if_empty($data['goods_name'], '-')?><br>
                    <small class="text-muted no-wrap"><?= if_empty($data['no_goods'], '-')?></small>
                    </td>
                    <td><?= format_date($data['sppb_date'], 'd F Y')?></td>
                    <td><?= if_empty($data['no_request'], '-')?></td>
                    <td><?= format_date($data['request_date'], 'd F Y')?></td>
                    <td><?= format_date($data['completed_at'], 'd F Y H:i')?></td>
                    <td><?= if_empty($data['diff_date'], '-')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="7">Average</th>
                <?php if(empty($reportPerformances)): ?>
                    <th>0 day</th>
                <?php else: ?>
                    <th><?= round($total_day/count($reportPerformances),2); ?> day</th>
                <?php endif; ?>
            </tr>
            </tfoot>
        </table>
    </div>
</div>