<table class="table table-bordered table-striped table-condensed no-datatable">
    <thead>
    <tr>
        <th style="width: 20px">No</th>
        <th>Customer</th>
        <th>No Job</th>
        <th>Handling</th>
        <th>Gate In</th>
        <th>Complete At</th>
        <th>Status</th>
        <th>Valid</th>
        <th>Validated By</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1 ?>
    <?php foreach ($workOrders as $workOrder): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $workOrder['customer_name'] ?></td>
            <td>
                <a href="<?= site_url("work-order/view/{$workOrder['id']}") ?>" target="_blank">
                    <?= $workOrder['no_work_order'] ?>
                </a>
            </td>
            <td><?= $workOrder['no_handling'] ?></td>
            <td><?= $workOrder['gate_in_date'] ?></td>
            <td><?= $workOrder['completed_at'] ?></td>
            <td>
                <?php
                $dataLabel = [
                    WorkOrderModel::STATUS_QUEUED => 'danger',
                    WorkOrderModel::STATUS_TAKEN => 'warning',
                    WorkOrderModel::STATUS_COMPLETED => 'success',
                    WorkOrderModel::STATUS_VALIDATION_PENDING => 'default',
                    WorkOrderModel::STATUS_VALIDATION_ON_REVIEW => 'warning',
                    WorkOrderModel::STATUS_VALIDATION_VALIDATED => 'success',
                    WorkOrderModel::STATUS_VALIDATION_REJECT => 'danger',
                    WorkOrderModel::STATUS_VALIDATION_FIXED => 'warning'
                ];
                ?>
                <span class="label label-<?= get_if_exist($dataLabel, $workOrder['status'], 'default') ?> mr10">
                    <?php if(empty($workOrder['gate_in_date'])): ?>
                        NEED GATE IN
                    <?php else: ?>
                        <?= $workOrder['status'] ?>
                    <?php endif; ?>
                </span>
            </td>
            <td>
                <span class="label label-<?= get_if_exist($dataLabel, $workOrder['status_validation'], 'default') ?>">
                    <?= if_empty($workOrder['status_validation'], 'PENDING') ?>
                </span>
            </td>
            <td><?= if_empty($workOrder['validator_name'], '-') ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(empty($workOrders)): ?>
        <tr>
            <td colspan="9">No job available</td>
        </tr>
    <?php endif ?>
    </tbody>
</table>