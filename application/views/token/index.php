<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Token</h3>
        <a href="#modal-generate-token" data-toggle="modal" class="btn btn-primary pull-right">
            Generate Token
        </a>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-token">
            <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Token</th>
                <th>Type</th>
                <th>Activation Left</th>
                <th>Expired After</th>
                <th>Created At</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($tokens as $token): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $token['token'] ?></td>
                    <td><?= $token['type'] ?></td>
                    <td><?= $token['max_activation'] ?></td>
                    <td><?= readable_date($token['expired_at'], false) ?></td>
                    <td><?= readable_date($token['created_at']) ?></td>
                    <td>
                        <a href="<?= site_url('token/delete/' . $token['id']) ?>" class="btn btn-danger btn-delete"
                           data-id="<?= $token['id'] ?>"
                           data-title="Token"
                           data-label="<?= $token['type'] ?> - <?= $token['token'] ?>">
                            <i class="fa ion-trash-a"></i> Revoke
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('token/_modal_generate_token') ?>
<?php $this->load->view('template/_modal_delete'); ?>

<script src="<?= base_url('assets/app/js/token.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>