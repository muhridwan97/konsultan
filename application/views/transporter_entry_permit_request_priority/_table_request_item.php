<table class="table no-datatable responsive">
    <thead>
    <tr class="no-wrap">
        <th style="width: 50px" class="text-center">No</th>
        <th>No Request</th>
        <th>No Reference</th>
        <th>Goods Name</th>
        <th>Unit</th>
        <th>No Ex Container</th>
        <th>Status</th>
        <th>Location</th>
        <th>Priority</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $statuses = [
        'HOLD' => 'danger',
        'RELEASED' => 'success',
    ]
    ?>
    <?php foreach ($tepRequestUploads as $index => $item): ?>
        <tr>
            <td class="text-center"><?= $index + 1 ?></td>
            <td><?= $item['no_request'] ?></td>
            <td><?= $item['no_reference_upload'] ?></td>
            <td><?= $item['goods_name'] ?></td>
            <td><?= $item['unit'] ?></td>
            <td><?= if_empty($item['ex_no_container'], '-') ?></td>
            <td>
                <span class="label label-<?= get_if_exist($statuses, $item['hold_status'], 'default') ?>">
                    <?= $item['hold_status'] ?: 'HOLD' ?>
                </span>
            </td>
            <td><?= if_empty($item['unload_location'], '-') ?></td>
            <td><?= if_empty($item['priority'], '-') ?></td>
            <td><?= if_empty($item['priority_description'], '-') ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(empty($tepRequestUploads)): ?>
        <tr>
            <td colspan="8">No priority goods available</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>