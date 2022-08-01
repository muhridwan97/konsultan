<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Ops Detail Week <?= get_url_param('minggu',0) + 1 ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-ops-detail">
            <thead>
            <tr>
                <th style="width: 30px">NO</th>
                <th>DATE</th>
                <th>DAY</th>
                <th>NAME OPS</th>
                <th>COUNT</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            $total_ops = 0;
            foreach ($datas['data'] as $data): ?>
            <?php
            $total_ops+=$data['count_ops'];
             ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= if_empty($data['selected_date'], '-') ?></td>
                    <td><?= if_empty($data['nama_hari'], '-')?></td>
                    <td><?= if_empty($data['name_ops'], '-')?></td>
                    <td><?= if_empty($data['count_ops'], '-')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4">Total</th>
                <th><?= $total_ops ?> OPS</th>
            </tr>
            <tr>
                <th colspan="4">Average</th>
                <th ><?= $total_ops/($no-1) ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>