<p class="lead">Handling result</p>

<?php foreach ($handlings as $handling): ?>
    <a href="<?= site_url('handling/view/' . $handling['id']) ?>" class="search-result-row">
        <div class="panel panel-default">
            <div class="panel-body">
                <p>
                    <strong><?= $handling['no_handling'] ?></strong>
                    <span class="pull-right">
                    <?= (new DateTime($handling['handling_date']))->format('d F Y') ?>
                </span>
                </p>
                <p class="mb0">
                    Handling Type <strong><?= $handling['handling_type'] ?></strong> by
                    Customer <strong><?= $handling['customer_name'] ?></strong>

                    <?php
                    $dataLabel = [
                        HandlingModel::STATUS_PENDING => 'default',
                        HandlingModel::STATUS_APPROVED => 'success',
                        HandlingModel::STATUS_REJECTED => 'danger',
                    ];
                    ?>
                    <span class="pull-right label label-<?= $dataLabel[$handling['status']] ?>">
                    <?= $handling['status'] ?>
                </span>
                </p>
            </div>
        </div>
    </a>
<?php endforeach ?>

<?php if (empty($handlings)): ?>
    <p class="text-muted">No handling result found.</p>
<?php endif ?>

<br>

<p class="lead">Job result</p>

<?php foreach ($workOrders as $workOrder): ?>
    <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>" class="search-result-row">
        <div class="panel panel-default">
            <div class="panel-body">
                <p>
                    <strong><?= $workOrder['no_work_order'] ?></strong>
                    <span class="pull-right">
                    <strong><?= $workOrder['handling_type'] ?></strong> Handling
                </span>
                </p>
                <p class="mb0">
                    Gate In
                    <strong>
                        <?= is_null($workOrder['gate_in_date']) ? '-' : (new DateTime($workOrder['gate_in_date']))->format('d F Y H:i') ?>
                    </strong> &nbsp; &nbsp;
                    Gate Out
                    <strong>
                        <?= is_null($workOrder['gate_out_date']) ? '-' : (new DateTime($workOrder['gate_out_date']))->format('d F Y H:i') ?>
                    </strong>

                    <?php
                    $dataLabel = [
                        WorkOrderModel::STATUS_QUEUED => 'default',
                        WorkOrderModel::STATUS_TAKEN => 'warning',
                        WorkOrderModel::STATUS_COMPLETED => 'success',
                    ];
                    ?>
                    <span class="pull-right label label-<?= $dataLabel[$workOrder['status']] ?>">
                    <?= $workOrder['status'] ?>
                </span>
                </p>
                <p class="mb0">
                    Overtime: <strong><?= $workOrder['overtime'] ? 'Yes' : 'No' ?></strong> &nbsp;
                    Staple: <strong><?= $workOrder['staple'] ? 'Yes' : 'No' ?></strong> &nbsp;
                    Man Power: <strong><?= $workOrder['man_power'] ?></strong> &nbsp;
                    Forklift: <strong><?= $workOrder['forklift'] ?></strong> &nbsp;
                    Tools: <strong><?= is_null($workOrder['tools']) ? '-' : $workOrder['tools'] ?></strong> &nbsp;
                    Materials: <strong><?= is_null($workOrder['materials']) ? '-' : $workOrder['materials'] ?></strong>
                </p>
            </div>
        </div>
    </a>
<?php endforeach ?>

<?php if (empty($workOrders)): ?>
    <p class="text-muted">No job result found.</p>
<?php endif ?>
