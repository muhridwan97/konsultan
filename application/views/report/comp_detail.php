<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Compliance Detail Week <?= get_url_param('minggu',0) ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-comp-detail">
            <thead>
            <tr>
                <th style="width: 30px">NO</th>
                <th>DATE</th>
                <th>DAY</th>
                <th>NAME COMP</th>
                <th>COUNT</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            $total_comp = 0;
            foreach ($datas['data'] as $data): ?>
            <?php
            $total_comp+=$data['count_comp'];
             ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= if_empty($data['selected_date'], '-') ?></td>
                    <td><?= if_empty($data['nama_hari'], '-')?></td>
                    <td><?= if_empty($data['name_comp'], '-')?></td>
                    <td><?= if_empty($data['count_comp'], '-')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4">Total</th>
                <th><?= $total_comp ?> Comp</th>
            </tr>
            <tr>
                <th colspan="4">Average</th>
                <th ><?= $total_comp/($no-1) ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>