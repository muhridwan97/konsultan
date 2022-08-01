<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Request Priority Items</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit-request-priority/update') ?>?<?= $_SERVER['QUERY_STRING'] ?>" role="form" method="post" class="need-validation">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <?php foreach ($tepRequestUploadGroups as $index => $tepRequestUploads): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Set Priority <?= $index + 1 ?></h3>
                </div>
                <div class="box-body">
                    <div role="form" class="form-horizontal form-view mb0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4">No Reference</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <?= $tepRequestUploads[0]['no_reference'] ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">Goods Name</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static" title="<?= $tepRequestUploads[0]['no_goods'] ?>">
                                            <?= $tepRequestUploads[0]['goods_name'] ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4">Unit</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <?= $tepRequestUploads[0]['unit'] ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">Ex No Container</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <?= if_empty($tepRequestUploads[0]['ex_no_container'], '-') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php foreach ($tepRequestUploads as $tepRequestUpload): ?>
                        <input type="hidden" name="tep_request_unloads[<?= $index ?>][<?= $tepRequestUpload['id'] ?>]" value="<?= $tepRequestUpload['id'] ?>">
                    <?php endforeach; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('unload_location') == '' ?: 'has-error'; ?>">
                                <label for="unload_location">Unload Location</label>
                                <input type="text" class="form-control input-uppercase" id="unload_location" name="unload_location[<?= $index ?>]" placeholder="Unloading location"
                                          required maxlength="200" value="<?= set_value('unload_location', $tepRequestUploads[0]['unload_location']) ?>"/>
                                <?= form_error('unload_location', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('priority') == '' ?: 'has-error'; ?>">
                                <label for="priority">Priority</label>
                                <select class="form-control select2" name="priority[<?= $index ?>]" id="priority" data-placeholder="Select priority" required style="width: 100%">
                                    <option value=""></option>
                                    <option value="Top Urgent"<?= set_select('priority', 'Top Urgent', $tepRequestUploads[0]['priority'] == 'Top Urgent') ?>>Top Urgent</option>
                                    <option value="1st"<?= set_select('priority', '1st', $tepRequestUploads[0]['priority'] == '1st') ?>>1st</option>
                                    <option value="2nd"<?= set_select('priority', '2nd', $tepRequestUploads[0]['priority'] == '2nd') ?>>2nd</option>
                                    <option value="3rd"<?= set_select('priority', '3rd', $tepRequestUploads[0]['priority'] == '3rd') ?>>3rd</option>
                                </select>
                                <?= form_error('priority', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description[<?= $index ?>]" placeholder="Priority description" required
                                  maxlength="500"><?= set_value('description', $tepRequestUploads[0]['priority_description']) ?></textarea>
                        <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                    </div>

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Affected Request</h3>
                        </div>
                        <div class="box-body">
                            <?php $this->load->view('transporter_entry_permit_request_priority/_table_request_item', compact('tepRequestUploads')) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Update
            </button>
        </div>
    </form>
</div>