<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Visitor</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-security-guest">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>NAME</th>
                <th>no_telepon</th>
                <th>keperluan</th>
                <th>CHECK IN AT</th>
                <th>Name Security</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($datas['data'] as $data): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="responsive-title"><?= $data['nama_visitor'] ?></td>
                    <td><?= if_empty($data['no_telepon'], '-') ?></td>
                    <td><?= if_empty($data['keperluan'], '-')?></td>
                    <td><?= if_empty($data['checkin'], '-')?></td>
                    <td><?= if_empty($data['check_in_name'], '-')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>