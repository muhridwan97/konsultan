<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">History Lock</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped no-datatable">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Description</th>
                <th style="width: 25px">Status</th>
                <th style="width: 10%">Create At</th>
                <th style="width: 10%">Create By</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($lockHistories as $lockHistory): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= values($lockHistory['description'], 'No description') ?></td>
                    <td>
                        <?php
                        $dataLabel = [
                            'REJECT' => 'danger',
                            'REQUEST' => 'warning',
                            'APPROVE' => 'success',
                            'UNLOCKED' => 'info',
                            'LOCKED' => 'danger',
                        ];
                        ?>
                        <span class="label label-<?= $dataLabel[$lockHistory['status']] ?> mr10">
                        <?= $lockHistory['status'] ?>
                        </span>
                    </td>
                    <td><?= $lockHistory['created_at'] ?></td>
                    <td><?= $lockHistory['name'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>