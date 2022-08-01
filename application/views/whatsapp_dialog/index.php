<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">WhatsApp Dialog</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-whatsapp-dialog">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>ID</th>
                <th>NAME</th>
                <th>IMAGE</th>
                <th>IS GROUP</th>
                <th>PARTICIPANTS</th>
                <th>GROUP INVITE LINK</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($datas['data'] as $data): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="responsive-title"><?= $data['id'] ?></td>
                    <td><?= $data['name'] ?></td>
                    <td>
                        <?php if (!empty($data['image'])): ?>
                            <a href="<?= $data['image'] ?>">
                                <img src="<?= $data['image'] ?>" class="img-circle" alt="User Image" width="40" height="40">
                            </a>
                        <?php else: ?>
                            <img src="<?= base_url('assets/app/img/avatar/no-avatar.jpg') ?>" class="img-circle" alt="User Image" width="40" height="40">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= empty($data['isGroup']) ? 'Personal' : 'Group' ?>
                    </td>
                    <td>
                        <?php if (empty($data['participants'])): ?>
                            No participants
                        <?php else: ?>
                            <ul style="padding-left: 10px">
                                <?php foreach ($data['participants'] as $participant): ?>
                                    <li><?= $participant ?></li>
                                <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                    </td>
                    <td><?= if_empty($data['groupInviteLink'], 'No link') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>