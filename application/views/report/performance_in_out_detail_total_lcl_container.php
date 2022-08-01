<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">LCL Container Detail Week <?= substr(get_url_param('year_week',0),-2) ?></h3>
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
                <th>NO POLICE</th>
                <th>NO CONTAINER</th>
                <th>SIZE</th>
                <th>NO JOB</th>
                <th>COMPLETE AT</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            $total_day = 0;
            foreach ($reportPerformances as $data): ?>
            <?php
             ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= if_empty($data['no_reference'], '-') ?></td>
                    <td><?= if_empty($data['customer_name'], '-')?></td>
                    <td><?= if_empty($data['no_police'], '-')?></td>
                    <td><?= if_empty($data['no_container'], '-')?></td>
                    <td><?= if_empty($data['container_size'], '-')?></td>
                    <td><?= if_empty($data['no_work_order'], '-')?></td>
                    <td><?= format_date($data['completed_at'], 'd F Y H:i:s')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="7">Total</th>
                <?php if(empty($reportPerformances)): ?>
                    <th>0</th>
                <?php else: ?>
                    <th><?= count($reportPerformances); ?></th>
                <?php endif; ?>
            </tr>
            </tfoot>
        </table>
    </div>
</div>