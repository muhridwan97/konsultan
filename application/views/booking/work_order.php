<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Work Order <?= $booking['no_booking'] ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('booking/_view_header') ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Booking Work Order</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-handling">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>No Work Order</th>
                        <th>No Handling</th>
                        <th>Handling Type</th>
                        <th>Customer</th>
                        <th>Tally Start</th>
                        <th>Tally End</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($workOrders as $workOrder): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                    <?= $workOrder['no_work_order'] ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= site_url('handling/view/' . $workOrder['id_handling']) ?>">
                                    <?= $workOrder['no_handling'] ?>
                                </a>
                            </td>
                            <td><?= $workOrder['handling_type'] ?></td>
                            <td><?= $workOrder['customer_name'] ?></td>
                            <td><?= readable_date($workOrder['taken_at']) ?></td>
                            <td><?= readable_date($workOrder['completed_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($workOrders)): ?>
                        <tr>
                            <td colspan="6">No any job available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>