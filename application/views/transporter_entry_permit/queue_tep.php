<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">QUEUE TEP <?= get_url_param('filter_queue_tep', 0) ? $_GET['expired_date'] : date('d F Y',strtotime($expired_date)) ?></h3>
        <div class="pull-right">
            <a href="#form-filter-queue-tep" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_queue_tep', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <?php $this->load->view('transporter_entry_permit/_filter_queue_tep', ['hidden' => isset($_GET['filter_queue_tep']) ? false : true]) ?>
        <table class="table table-bordered table-striped responsive" id="table-queue-tep">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>CUSTOMER NAME</th>
                <th>TEP CODE</th>
                <th>REF</th>
                <th>DESCRIPTION</th>
                <th>QUEUE TIME</th>
                <th style="width: 30px">File</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($datas['data'] as $data): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="responsive-title">
                        <?= if_empty($data['customer_name_out'],'-') ?>
                    </td>
                    <td><a href="<?= site_url('transporter-entry-permit/view/' . $data['id']) ?>"><?= if_empty($data['tep_code'], '-') ?></a></td>
                    <td>
                    <p class="form-control-static">
                                <?php if (!empty($data['id_upload'])) : ?>
                                <a href="<?= site_url('upload/view/' . $data['id_upload']) ?>">
                                <?= if_empty($data['no_aju'], '-') ?></a>
                                <?php else : 
                                    $id_upload_array = explode(",",$data['id_upload_multi']);
                                    $no_aju_array = explode(",",$data['no_aju_multi']);?>
                                    <?php foreach ($id_upload_array as $key => $id_upload) : ?>
                                        <a href="<?= site_url('upload/view/' . $id_upload) ?>">
                                        <?= if_empty($no_aju_array[$key], '-') ?></a> </br>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </p>
                    </td>
                    <td><?= if_empty($data['description_req'], '-') ?></td>
                    <td><?= if_empty($data['queue_time'], 'without request') ?></td>
                    <td><a href="#" data-toggle="tooltip" title="View DO/Memo">
                        <?php foreach ($data['id_tep_req_multi'] as $key => $id_request) : ?>
                            <button class="btn btn-danger btn-view-file" data-id="<?= $id_request ?>">
                                <i class="fa fa-folder"></i>
                            </button></a>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('transporter_entry_permit/_modal_view_file') ?>
<script src="<?= base_url('assets/app/js/tep.js?v=6') ?>" defer></script>