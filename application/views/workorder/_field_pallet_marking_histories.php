<?php if(isset($palletHistories) && !empty($palletHistories)): ?>
<table class="table table-bordered table-striped no-datatable responsive">
    <thead>
        <th style="width: 30px">No</th>
        <th>No Work Order</th>
        <th>Created At</th>
        <th>Created By</th>
        <th>Status</th>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php foreach ($palletHistories as $pallet): ?>
            <?php $work_order = $this->workOrder->getWorkOrderById($pallet['id_reference']); ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $work_order['no_work_order'] ?></td>
            <td><?= date('d F Y H:i', strtotime($pallet['created_at'])) ?></td>
            <td><?= $pallet['creator_name'] ?></td>
            <td><?= $pallet['status'] ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php else: ?>
    <table class="table table-bordered table-striped no-datatable responsive">
        <tr colspan="4" class="text-center">No Data Available</tr>
    </table>
<?php endif; ?>