<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Draft Detail Week <?= get_url_param('minggu',0) ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-draft-detail">
            <thead>
            <tr>
                <th style="width: 30px">NO</th>
                <th>DATE</th>
                <th>NO UPLOAD</th>
                <th>NO AJU</th>
                <th>CUSTOMER</th>
                <th>DOCUMENT TYPE</th>
                <th>COUNT</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            $total_draft = 0;
            foreach ($datas['data'] as $data): ?>
            <?php
            $total_draft+=$data['count_draft'];
             ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= if_empty($data['selected_date'], '-') ?></td>
                    <td><a href="<?= base_url() ?>p/<?= $data['id_branch']?>/upload_document/view/<?= $data['id'] ?>" target="_blank"><?= if_empty($data['no_upload'], '-')?></a></td>
                    <td><?= if_empty($data['aju_desc'], '-')?></td>
                    <td><?= if_empty($data['customer_name'], '-')?></td>
                    <td><?= if_empty($data['document_type'], '-')?></td>
                    <td><?= if_empty($data['count_draft'], '-')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="6">Total</th>
                <th><?= $total_draft ?> draft</th>
            </tr>
            <!-- <tr>
                <th colspan="5">Average</th>
                <th ><?= $total_draft/($no-1) ?></th>
            </tr> -->
            </tfoot>
        </table>
    </div>
</div>