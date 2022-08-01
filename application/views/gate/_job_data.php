<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Job Handling List</h3>
    </div>
    <div class="box-body">

        <?php if ($this->session->flashdata('status') != NULL): ?>
            <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p><?= $this->session->flashdata('message'); ?></p>
            </div>
        <?php endif; ?>

        <table class="table no-datatable" id="table-jobs">
            <thead>
            <tr>
                <th>No</th>
                <th>Type</th>
                <th>No Job</th>
                <th>Queue</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th style="width: 80px">Print</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($workOrders) <= 0): ?>
                <tr>
                    <td colspan="7" class="text-center">No work order available, create one.</td>
                </tr>
            <?php endif; ?>

            <?php $no = 1; ?>
            <?php foreach ($workOrders as $workOrder): ?>
                <tr class="row-workorder"
                    data-id="<?= $workOrder['id'] ?>"
                    data-no="<?= $workOrder['no_work_order'] ?>"
                    data-customer="<?= $workOrder['customer_name'] ?>"
                    data-email="<?= $workOrder['customer_email'] ?>"
                    data-print-total="<?= $workOrder['print_total'] ?>"
                    data-print-max="<?= $workOrder['print_max'] ?>">
                    <td><?= $no++ ?></td>
                    <td><?= $workOrder['handling_type'] ?></td>
                    <td>
                        <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                            <?= $workOrder['no_work_order'] ?>
                        </a>
                    </td>
                    <td><?= $workOrder['queue'] ?></td>
                    <td>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_GATE_CHECK_IN)): ?>
                            <?php if (is_null($workOrder['gate_in_date'])): ?>
                                <a href="<?= site_url('work-order/check_in/' . $workOrder['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>"
                                   class="btn btn-success btn-check-in">Check In</a>
                            <?php else: ?>
                                <?= (new DateTime($workOrder['gate_in_date']))->format('d F Y H:i') ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_GATE_CHECK_OUT)): ?>
                            <?php if (is_null($workOrder['gate_in_date'])): ?>
                                Check In First
                            <?php elseif ($workOrder['status'] != WorkOrderModel::STATUS_COMPLETED && ($workOrder['handling_category'] != HandlingTypeModel::CATEGORY_NON_WAREHOUSE)): ?>
                                <?= $workOrder['status'] ?>
                            <?php elseif (is_null($workOrder['gate_out_date'])): ?>
                                <a href="<?= site_url('work-order/check_out/' . $workOrder['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>"
                                   class="btn btn-danger btn-check-out">Check Out</a>
                            <?php else: ?>
                                <?= (new DateTime($workOrder['gate_out_date']))->format('d F Y H:i') ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_PRINT)): ?>
                            <?php if ($workOrder['print_total'] < $workOrder['print_max']): ?>
                                <a href="<?= site_url('work-order/print-work-order/' . $workOrder['id'] . '?redirect=' . base_url(uri_string()) . '?' . $_SERVER['QUERY_STRING']) ?>"
                                   class="btn btn-primary btn-print-job-sheet">
                                    Print Job
                                </a>
                            <?php else: ?>
                                <button class="btn btn-primary disabled">Max Print</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        If you reach limit of print number, please contact your tally or administrator.
    </div>
</div>