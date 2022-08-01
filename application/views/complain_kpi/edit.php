<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit KPI</h3>
    </div>
    <form action="<?= site_url('complain-kpi/update/' . $complain_kpi['id']) ?>" class="form" method="post" id="form-complain-kpi">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="form-group <?= form_error('kpi') == '' ?: 'has-error'; ?>">
                <label for="kpi">KPI</label>
                <p class="form-control-static"><?= $complain_kpi['kpi'] ?></p>
            </div>
            <div class="form-group <?= form_error('major') == '' ?: 'has-error'; ?>">
                <label for="major">Major <span class="text-muted">(hour)</span></label>
                <input type="number" class="form-control" id="major" name="major" min="0"
                       placeholder="Enter Major"
                        value="<?= set_value('major', $complain_kpi['major']) ?>">
                <?= form_error('major', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('minor') == '' ?: 'has-error'; ?>">
                <label for="minor">Minor <span class="text-muted">(hour)</span></label>
                <input type="number" class="form-control" id="minor" name="minor" min="0"
                       placeholder="Enter Minor"
                        value="<?= set_value('minor', $complain_kpi['minor']) ?>">
                <?= form_error('minor', '<span class="help-block">', '</span>'); ?>
            </div>

            <?php if(!in_array($complain_kpi['kpi'], [ComplainKpiModel::KPI_RESPONSE_WAITING_TIME])): ?>
            <div class="form-group <?= form_error('recur_day') == '' ?: 'has-error'; ?>">
                <label for="recur_day">Reminder day <span class="text-muted">(1 for daily, 2 for 2 days once, etc...)</span></label>
                <input type="number" class="form-control" id="recur_day" name="recur_day" min="0"
                       placeholder="Enter Reminder day"
                       required value="<?= set_value('recur_day', $complain_kpi['recur_day']) ?>">
                <?= form_error('recur_day', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('reminder') == '' ?: 'has-error'; ?>">
                <label for="reminder">Reminder time <span class="text-muted">(hour time like 18:00)</span></label>
                <input type="number" class="form-control" id="reminder" name="reminder" min="0" max="23" step='1'
                       placeholder="Enter reminder time"
                    value="<?= set_value('reminder', format_date($complain_kpi['reminder'], 'H')) ?>">
                <?= form_error('reminder', '<span class="help-block">', '</span>'); ?>
            </div>
            <div id="wrapper-reminder">
                <?php foreach ($reminderDetails as $key => $reminderDetail) : ?>
                    <div class="form-group row-reminder <?= form_error('reminders[]') == '' ?: 'has-error'; ?>">
                        <div class="row">
                            <div class="form-group" >
                                <div class="col-md-10">
                                    <label for="reminder">Reminder time <?= $key+2 ?> <span class="text-muted">(hour time like 18 for 18:00)</span></label>
                                    <input type="number" class="form-control" id="reminder_detail" name="reminders[]" min="0" max="23" step='1'
                                        placeholder="Enter reminder time"
                                        value="<?= set_value('reminders[]', format_date($reminderDetail['reminder_time'], 'H')) ?>">
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-reminder">
                                        <i class="ion-trash-b"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <?= form_error('reminders[]', '<span class="help-block">', '</span>'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-sm btn-primary" id="btn-add-reminder">ADD REMINDER</button>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Handling component description"
                          required maxlength="500"><?= set_value('description', $complain_kpi['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <?php if($complain_kpi['id']=='1'): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Whatsapp group</h3>
                </div>
                <div class="box-body">
                    <table class="table no-datatable" id="table-detail-whatsapp">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th style="width: 250px">Group</th>
                            <th style="width: 250px">Branch</th>
                            <th>Whatsapp Group</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1;
                        foreach ($whatsappGroups as $whatsappGroup): ?>
                            <tr class="row-whatsapp">
                                <td><?= $no++ ?></td>
                                <td>
                                    <select class="form-control select2" name="groups[]" id="detail_group"
                                            data-placeholder="Select group" required style="width: 100%">
                                        <?php foreach ($departments as $department): ?>
                                            <option value="<?= $department['department'] ?>" <?= $department['department'] == $whatsappGroup['group'] ? 'selected' : '' ?>>
                                                <?= $department['department'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                
                                <td>
                                    <select class="form-control select2" name="branches[]" id="detail_branch"
                                            data-placeholder="Select group" required style="width: 100%">    
                                        <option value="0">ALL</option>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?= $branch['id'] ?>" <?= $branch['id'] == $whatsappGroup['id_branch_warehouse'] ? 'selected' : '' ?>>
                                                <?= $branch['branch'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2" name="whatsapp_groups[]" id="detail_whatsapp_group"
                                            data-placeholder="Select group" required style="width: 100%">
                                        <?php foreach ($departmentContacts as $departmentContact): ?>
                                            <option value="<?= $departmentContact['id'] ?>" <?= $departmentContact['id'] == $whatsappGroup['id_contact_group'] ? 'selected' : '' ?>>
                                                <?= $departmentContact['department'] ?> - <?= $departmentContact['group_name'] ?>  - <?= $departmentContact['contact_group'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-whatsapp">
                                        <i class="ion-trash-b"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!count($whatsappGroups)): ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    Click <strong>Add New Group</strong> to insert new record
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-block btn-primary mt10" id="btn-add-whatsapp">
                        ADD NEW GROUP
                    </button>
                </div>
            </div>
            <?php else:?>
                <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Whatsapp group</h3>
                </div>
                <div class="box-body">
                    <table class="table no-datatable" id="table-detail-whatsapp">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th style="width: 250px">Group</th>
                            <th>Whatsapp</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1;
                        foreach ($whatsappGroups as $whatsappGroup): ?>
                            <tr class="row-whatsapp">
                                <td><?= $no++ ?></td>
                                <td>
                                    <select class="form-control select2" name="groups[]" id="detail_group"
                                            data-placeholder="Select group" required style="width: 100%">
                                        <option value="<?= $whatsappGroup['group']?>" selected><?=$whatsappGroup['group']?></option>
                                        
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="whatsapp_groups[]" id="detail_whatsapp_group"
                                           value="<?= $whatsappGroup['whatsapp_group'] ?>"
                                           placeholder="whatsapp group">
                                </td>
                                <td class="text-center">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!count($whatsappGroups)): ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    Click <strong>Add New Group</strong> to insert new record
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif;?>
            <?php else:?>
                <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description"
                              placeholder="Complain KPI description"
                              required maxlength="500"><?= set_value('description', $complain_kpi['description']) ?></textarea>
                    <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif;?>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update</button>
        </div>
    </form>
</div>

<?php $this->load->view('complain_kpi/_whatsapp_group') ?>
<?php $this->load->view('complain_kpi/_reminder_time') ?>

<script src="<?= base_url('assets/app/js/complain_kpi.js') ?>" defer></script>