<div class="box box-primary" id="table-safe-conduct">
    <div class="box-header with-border">
        <h3 class="box-title">View Safe Conduct</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_EDIT) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT)): ?>
            <?php if(empty($safeConduct['security_in_date'])): ?>
				<a href="<?= site_url('safe-conduct/edit/' . $safeConduct['id']) ?>" class="btn btn-primary pull-right">
					Edit Safe Conduct
				</a>
			<?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('safe_conduct/_data_header') ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Safe Conduct Handover</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-handover">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Attachment</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($safeConductAttachments as $index => $safeConductAttachment): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <a href="<?= asset_url($safeConductAttachment['src']) ?>">
                                <?= basename($safeConductAttachment['src']) ?>
                            </a>
                        </td>
                        <td><?= $safeConductAttachment['created_at'] ?></td>
                        <td><?= $safeConductAttachment['creator_name'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($safeConductAttachments)): ?>
                        <tr>
                            <td colspan="4">No attachment</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php $this->load->view('safe_conduct/_data_photos') ?>
        <?php $this->load->view('safe_conduct/_data_detail') ?>
        
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Safe Conduct Job</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-work-order">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>No Job</th>
                        <th>No Handling</th>
                        <th>Customer</th>
                        <th>Queue</th>
                        <th>Handling</th>
                        <th>Gate In</th>
                        <th>Gate Out</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($workOrders as $index => $workOrder): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
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
                            <td><?= $workOrder['customer_name'] ?></td>
                            <td><?= $workOrder['queue'] ?></td>
                            <td><?= $workOrder['handling_type'] ?></td>
                            <td>
                                <?= is_null($workOrder['gate_in_date']) ? '-' : (new DateTime($workOrder['gate_in_date']))->format('d M Y H:i') ?>
                            </td>
                            <td>
                                <?= is_null($workOrder['gate_out_date']) ? '-' : (new DateTime($workOrder['gate_out_date']))->format('d M Y H:i') ?>
                            </td>
                            <td>
                                <?php
                                $dataLabel = [
                                    WorkOrderModel::STATUS_QUEUED => 'danger',
                                    WorkOrderModel::STATUS_TAKEN => 'warning',
                                    WorkOrderModel::STATUS_COMPLETED => 'success',
                                ];
                                ?>
                                <span class="label label-<?= $dataLabel[$workOrder['status']] ?>">
                                    <?= $workOrder['status'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($workOrders)): ?>
                        <tr>
                            <td colspan="9">No any jobs available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if(!empty($relatedSafeConducts)): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Related Safe Conduct</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable" id="table-work-order">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>No Safe Conduct</th>
                            <th>No Booking</th>
                            <th>Customer</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($relatedSafeConducts as $index => $relatedSafeConduct): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <a href="<?= site_url('safe-conduct/view/' . $relatedSafeConduct['id']) ?>">
                                        <?= $relatedSafeConduct['no_safe_conduct'] ?>
                                    </a>
                                    <?= $relatedSafeConduct['id'] == $safeConduct['id'] ? '(current)' : '' ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('booking/view/' . $relatedSafeConduct['id_booking']) ?>">
                                        <?= $relatedSafeConduct['no_booking'] ?>
                                    </a>
                                    <small class="text-muted"><?= $relatedSafeConduct['no_reference'] ?></small>
                                </td>
                                <td><?= $relatedSafeConduct['customer_name'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($safeConductHistories)): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit History</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable responsive">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Edit At</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($safeConductHistories as $history): ?>
                            <tr>
                                <td class="responsive-hide"><?= $no++ ?></td>
                                <td class="responsive-title">
                                    <?= values($history['author_name'], '-') ?>
                                </td>
                                <td><?= values($history['description'], '-') ?></td>
                                <td><?= readable_date($history['created_at']) ?></td>
                            </tr>
                        <?php endforeach ?>
                        <?php if(empty($safeConductHistories)): ?>
                            <tr>
                                <td colspan="5">No history available</td>
                            </tr>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="box-footer clearfix row-safe-conduct"
         data-id="<?= $safeConduct['id'] ?>"
         data-no="<?= $safeConduct['no_safe_conduct'] ?>"
         data-print-total="<?= $safeConduct['print_total'] ?>"
         data-print-max="<?= $safeConduct['print_max'] ?>">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_PRINT) || AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_OUT_PRINT)): ?>
    <?php $this->load->view('safe_conduct/_modal_confirm_print') ?>
<?php endif; ?>