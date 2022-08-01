<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Validate Work Order Overtime</h3>
    </div>

    <form action="<?= site_url('work-order-overtime/save/' . $workOrderOvertime['id_work_order']) ?>" class="need-validation" method="post" enctype="multipart/form-data" id="form-work-order-overtime">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div role="form" class="form-horizontal form-view">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3">No Work Order</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <a href="<?= site_url('work-order/view/' . $workOrderOvertime['id_work_order']) ?>" target="_blank">
                                        <?= $workOrderOvertime['no_work_order'] ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">No Reference</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <?= $workOrderOvertime['no_reference'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Customer</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <?= $workOrderOvertime['customer_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Completed At</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <?= format_date($workOrderOvertime['completed_at'], 'd F Y H:i:s') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3">Service Day</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <?= $workOrderOvertime['service_day'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Service End</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <?= format_date($workOrderOvertime['service_time_end'], 'H:i') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Total Overtime</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <?= $workOrderOvertime['total_overtime'] ?> /
                                    <?= $workOrderOvertime['total_overtime_hour'] ?> Hour(s)
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Effective Date</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <?= format_date($workOrderOvertime['effective_date'], 'd F Y') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Overtime Charge</h3>
                </div>
                <div class="box-body">
                    <div class="form-group <?= form_error('overtime_charged_to') == '' ?: 'has-error'; ?>">
                        <label for="overtime_charged_to">Charged To</label>
                        <select class="form-control select2" name="overtime_charged_to" id="overtime_charged_to" data-placeholder="Charged to" required>
                            <option value=""></option>
                            <option value="OPERATIONAL"<?= set_select('overtime_charged_to', 'OPERATIONAL', $workOrderOvertime['overtime_charged_to'] == 'OPERATIONAL') ?>>
                                OPERATIONAL
                            </option>
                            <option value="CUSTOMER"<?= set_select('overtime_charged_to', 'CUSTOMER', $workOrderOvertime['overtime_charged_to'] == 'CUSTOMER') ?>>
                                CUSTOMER
                            </option>
                        </select>
                        <?= form_error('overtime_charged_to', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="overtime_attachment">Attachment</label>
                        <input type="file" id="overtime_attachment" name="overtime_attachment" class="file-upload-default" data-max-size="3000000"<?= $workOrderOvertime['overtime_charged_to'] == 'CUSTOMER' && empty($workOrderOvertime['overtime_attachment']) ? 'required' : '' ?>>
                        <div class="input-group col-xs-12">
                            <input type="text" name="attachment_info" value="<?= set_value('attachment_info', basename($workOrderOvertime['overtime_attachment'])) ?>"
                                <?= $workOrderOvertime['overtime_charged_to'] == 'CUSTOMER' ? 'required' : '' ?>
                                   id="attachment_info" class="form-control file-upload-info" readonly placeholder="Upload attachment">
                            <div class="input-group-btn">
                                <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">Upload</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group <?= form_error('reason') == '' ?: 'has-error'; ?>">
                        <label for="reason">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" placeholder="Overtime charging reason"
                                  required maxlength="500" minlength="25" rows="3"><?= set_value('reason', $workOrderOvertime['reason']) ?></textarea>
                        <?= form_error('reason', '<span class="help-block">', '</span>'); ?>
                    </div>

                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Overtime additional info"
                                  maxlength="500"><?= set_value('description', $workOrderOvertime['description']) ?></textarea>
                        <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Save Overtime
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/work-order-overtime.js') ?>" defer></script>