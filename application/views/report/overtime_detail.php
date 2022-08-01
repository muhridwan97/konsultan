<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Overtime Detail Week <?= get_url_param('minggu',0) ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-overtime-detail">
            <thead>
            <tr>
                <th style="width: 30px">NO</th>
                <th>DATE</th>
                <th>NAME</th>
                <th>OVERTIME IN MINUTE</th>
                <th>OVERTIME IN HOUR</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            $total_minute = 0;
            foreach ($datas['data'] as $data): ?>
            <?php
            $total_minute+=$data['overtime_duration'];
             ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= if_empty($data['selected_date'], '-') ?></td>
                    <td><a href="<?= env('HR_URL') ?>/overtime/view/<?= $data['id'] ?>" target="_blank"><?= if_empty($data['name'], '-')?></a></td>
                    <td><?= if_empty($data['overtime_duration'], '-')?></td>
                    <td><?= if_empty($data['overtime_duration_hour'], '-')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th><?= $total_minute ?> minute</th>
                <th><?= $total_minute!= 0 ? round($total_minute/60,2) : 0 ?> hour</th>
            </tr>
            <!-- <tr>
                <th colspan="5">Average</th>
                <th ><?= $total_minute/($no-1) ?></th>
            </tr> -->
            </tfoot>
        </table>
    </div>
</div>