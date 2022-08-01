<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Sppb Detail Week <?= get_url_param('minggu',0) ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-sppb-detail">
            <thead>
            <tr>
                <th style="width: 30px">NO</th>
                <th>DATE</th>
                <th>NO UPLOAD</th>
                <th>NO AJU</th>
                <th>CUSTOMER</th>
                <th>DOCUMENT TYPE</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            $total_sppb = 0;
            foreach ($datas['data'] as $data): ?>
            <?php
            $total_sppb+= isset($data['count_sppb'])? 1 : 0;
             ?>
            <?php if (!empty($data['id'])): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= if_empty($data['sppb_upload_date'], '-') ?></td>
                    <td><a href="<?= base_url() ?>p/<?= $data['id_branch']?>/upload/view/<?= $data['id'] ?>" target="_blank"><?= if_empty($data['no_upload'], '-')?></a></td>
                    <td><?= if_empty($data['aju_desc'], '-') ?></td>
                    <td><?= if_empty($data['customer_name'], '-')?></td>
                    <td><?= if_empty(str_replace('Draft','',$data['document_type']), '-')?></td>
                </tr>
            <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="5">Total</th>
                <th><?= $total_sppb ?> sppb</th>
            </tr>
            <!-- <tr>
                <th colspan="5">Average</th>
                <th ><?= $total_sppb/($no-1) ?></th>
            </tr> -->
            </tfoot>
        </table>
    </div>
</div>