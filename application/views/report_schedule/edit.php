<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Report Schedule</h3>
    </div>
    <form action="<?= site_url('report-schedule/update/' . $reportSchedule['report_name']) ?>" class="form" method="post" id="form-report-schedule">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group">
                <label for="report_name">Report Name</label>
                <input type="text" class="form-control" id="report_name" name="report_name" readonly
                       value="<?= ucwords(str_replace(['-', '_'], ' ', $reportSchedule['report_name'])) ?>">
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Report description"
                          maxlength="500"><?= set_value('description', $reportSchedule['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('send_to_type') == '' ?: 'has-error'; ?>">
                        <label for="send_to_type">Send To Type</label>
                        <select class="form-control select2" name="send_to_type" id="send_to_type" data-placeholder="Select to type" style="width: 100%">
                            <option value="DEFAULT"<?= set_select('send_to_type', 'DEFAULT', $reportSchedule['send_to_type'] == 'DEFAULT') ?>>DEFAULT</option>
                            <option value="PROFILE"<?= set_select('send_to_type', 'PROFILE', $reportSchedule['send_to_type'] == 'PROFILE') ?>>PROFILE</option>
                            <option value="PROFILE CONTACT"<?= set_select('send_to_type', 'PROFILE CONTACT', $reportSchedule['send_to_type'] == 'PROFILE CONTACT') ?>>PROFILE CONTACT</option>
                            <option value="USER"<?= set_select('send_to_type', 'USER', $reportSchedule['send_to_type'] == 'USER') ?>>USER</option>
                            <option value="BRANCH PLB OR TPP SUPPORT"<?= set_select('send_to_type', 'BRANCH PLB OR TPP SUPPORT', $reportSchedule['send_to_type'] == 'BRANCH PLB OR TPP SUPPORT') ?>>BRANCH PLB OR TPP SUPPORT</option>
                        </select>
                        <span class="help-block">Primary email address by data</span>
                        <?= form_error('send_to_type') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="send_to">Send To (Additional)</label>
                        <input type="email" class="form-control" id="send_to" name="send_to" placeholder="Send to emails" multiple
                               value="<?= set_value('send_to', $reportSchedule['send_to']) ?>">
                        <span class="help-block">Separate email by comma</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('send_cc_type') == '' ?: 'has-error'; ?>">
                        <label for="send_cc_type">Send CC Type</label>
                        <select class="form-control select2" name="send_cc_type" id="send_cc_type" data-placeholder="Select cc type" style="width: 100%">
                            <option value="DEFAULT"<?= set_select('send_cc_type', 'DEFAULT', $reportSchedule['send_cc_type'] == 'DEFAULT') ?>>DEFAULT</option>
                            <option value="PROFILE"<?= set_select('send_cc_type', 'PROFILE', $reportSchedule['send_cc_type'] == 'PROFILE') ?>>PROFILE</option>
                            <option value="PROFILE CONTACT"<?= set_select('send_cc_type', 'PROFILE CONTACT', $reportSchedule['send_cc_type'] == 'PROFILE CONTACT') ?>>PROFILE CONTACT</option>
                            <option value="USER"<?= set_select('send_cc_type', 'USER', $reportSchedule['send_cc_type'] == 'USER') ?>>USER</option>
                            <option value="BRANCH PLB OR TPP SUPPORT"<?= set_select('send_cc_type', 'BRANCH PLB OR TPP SUPPORT', $reportSchedule['send_cc_type'] == 'BRANCH PLB OR TPP SUPPORT') ?>>BRANCH PLB OR TPP SUPPORT</option>
                        </select>
                        <span class="help-block">CC email address by data</span>
                        <?= form_error('send_cc_type') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="send_cc">Send CC (Additional)</label>
                        <input type="email" class="form-control" id="send_cc" name="send_cc" placeholder="Send cc emails" multiple
                               value="<?= set_value('send_cc', $reportSchedule['send_cc']) ?>">
                        <span class="help-block">Separate email by comma</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('send_bcc_type') == '' ?: 'has-error'; ?>">
                        <label for="send_bcc_type">Send BCC Type</label>
                        <select class="form-control select2" name="send_bcc_type" id="send_bcc_type" data-placeholder="Select bcc type" style="width: 100%">
                            <option value="DEFAULT"<?= set_select('send_bcc_type', 'DEFAULT', $reportSchedule['send_bcc_type'] == 'DEFAULT') ?>>DEFAULT</option>
                            <option value="PROFILE"<?= set_select('send_bcc_type', 'PROFILE', $reportSchedule['send_bcc_type'] == 'PROFILE') ?>>PROFILE</option>
                            <option value="PROFILE CONTACT"<?= set_select('send_bcc_type', 'PROFILE CONTACT', $reportSchedule['send_bcc_type'] == 'PROFILE CONTACT') ?>>PROFILE CONTACT</option>
                            <option value="USER"<?= set_select('send_bcc_type', 'USER', $reportSchedule['send_bcc_type'] == 'USER') ?>>USER</option>
                            <option value="BRANCH PLB OR TPP SUPPORT"<?= set_select('send_bcc_type', 'BRANCH PLB OR TPP SUPPORT', $reportSchedule['send_bcc_type'] == 'BRANCH PLB OR TPP SUPPORT') ?>>BRANCH PLB OR TPP SUPPORT</option>
                        </select>
                        <span class="help-block">BCC email address by data</span>
                        <?= form_error('send_bcc_type') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="send_bcc">Send BCC</label>
                        <input type="email" class="form-control" id="send_bcc" name="send_bcc" placeholder="Send bcc emails" multiple
                               value="<?= set_value('send_bcc', $reportSchedule['send_bcc']) ?>">
                        <span class="help-block">Separate email by comma</span>
                    </div>
                </div>
            </div>

            <div class="box box-primary mt20">
                <div class="box-header with-border">
                    <h3 class="box-title">Scheduler</h3>
                </div>
                <div class="box-body">
                    <div class="form-group <?= form_error('recurring_period') == '' ?: 'has-error'; ?>">
                        <label for="recurring-period">Recurring Period</label>
                        <select class="form-control select2" name="recurring_period" id="recurring-period" data-placeholder="Select period" required style="width: 100%">
                            <option value=""></option>
                            <option value="<?= ReportScheduleModel::PERIOD_ONE_TIME ?>"<?= set_select('recurring_period', ReportScheduleModel::PERIOD_ONE_TIME, $reportSchedule['recurring_period'] == ReportScheduleModel::PERIOD_ONE_TIME) ?>>
                                <?= ReportScheduleModel::PERIOD_ONE_TIME ?>
                            </option>
                            <option value="<?= ReportScheduleModel::PERIOD_DAILY ?>"<?= set_select('recurring_period', ReportScheduleModel::PERIOD_DAILY, $reportSchedule['recurring_period'] == ReportScheduleModel::PERIOD_DAILY) ?>>
                                <?= ReportScheduleModel::PERIOD_DAILY ?>
                            </option>
                            <option value="<?= ReportScheduleModel::PERIOD_WEEKLY ?>"<?= set_select('recurring_period', ReportScheduleModel::PERIOD_WEEKLY, $reportSchedule['recurring_period'] == ReportScheduleModel::PERIOD_WEEKLY) ?>>
                                <?= ReportScheduleModel::PERIOD_WEEKLY ?>
                            </option>
                            <option value="<?= ReportScheduleModel::PERIOD_MONTHLY ?>"<?= set_select('recurring_period', ReportScheduleModel::PERIOD_MONTHLY, $reportSchedule['recurring_period'] == ReportScheduleModel::PERIOD_MONTHLY) ?>>
                                <?= ReportScheduleModel::PERIOD_MONTHLY ?>
                            </option>
                            <option value="<?= ReportScheduleModel::PERIOD_ANNUAL ?>"<?= set_select('recurring_period', ReportScheduleModel::PERIOD_ANNUAL, $reportSchedule['recurring_period'] == ReportScheduleModel::PERIOD_ANNUAL) ?>>
                                <?= ReportScheduleModel::PERIOD_ANNUAL ?>
                            </option>
                        </select>
                        <?= form_error('recurring_period') ?>
                    </div>
                    <div class="row form-row">
                        <div class="col-md-3">
                            <div class="form-group <?= form_error('triggered_at') == '' ?: 'has-error'; ?>">
                                <label for="triggered_at">Triggered At</label>
                                <input type="text" class="form-control datepicker" id="triggered_at" name="triggered_at" maxlength="50" required disabled
                                       value="<?= set_value('triggered_at', format_date($reportSchedule['triggered_at'], 'd/m/Y')) ?>" placeholder="Task expected to be done" autocomplete="off">
                                <?= form_error('triggered_at') ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group <?= form_error('triggered_month') == '' ?: 'has-error'; ?>">
                                <label for="triggered_month">Month</label>
                                <select class="form-control select2" id="triggered_month" name="triggered_month" data-placeholder="Select month" style="width: 100%" required disabled>
                                    <option value=""></option>
                                    <?php foreach (get_months() as $index => $month): ?>
                                        <option value="<?= ($index + 1) ?>"<?= set_select('triggered_month', ($index + 1), ($index + 1) == $reportSchedule['triggered_month']) ?>>
                                            <?= $month ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('triggered_month') ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group <?= form_error('triggered_date') == '' ?: 'has-error'; ?>">
                                <label for="triggered_date">Date in Month</label>
                                <select class="form-control select2" name="triggered_date" id="triggered_date" data-placeholder="Select date" style="width: 100%" required disabled>
                                    <option value=""></option>
                                    <?php for ($i = 1; $i <= 31; $i++): ?>
                                        <option value="<?= $i ?>"<?= set_select('triggered_date', $i, $i == $reportSchedule['triggered_date']) ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <?= form_error('triggered_date') ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group <?= form_error('triggered_day') == '' ?: 'has-error'; ?>">
                                <label for="triggered_day">Day of Week</label>
                                <select class="form-control select2" name="triggered_day" id="triggered_day" data-placeholder="Select day" style="width: 100%" required disabled>
                                    <option value=""></option>
                                    <?php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] ?>
                                    <?php foreach ($days as $index => $day): ?>
                                        <option value="<?= $index ?>"<?= set_select('triggered_day', $index, $index == $reportSchedule['triggered_day'] && $reportSchedule['recurring_period'] == ReportScheduleModel::PERIOD_WEEKLY) ?>>
                                            <?= $day ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('triggered_day') ?>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group <?= form_error('triggered_time') == '' ?: 'has-error'; ?>">
                                <label for="triggered_time">Time</label>
                                <select class="form-control select2" name="triggered_time" id="triggered_time" data-placeholder="Select time" style="width: 100%" required disabled>
                                    <option value=""></option>
                                    <?php for ($i = 0; $i <= 23; $i++): ?>
                                        <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>:00"
                                            <?= set_select('triggered_time', str_pad($i, 2, '0', STR_PAD_LEFT) . ':00', (str_pad($i, 2, '0', STR_PAD_LEFT) . ':00' == format_date($reportSchedule['triggered_time'], 'H:i'))) ?>>
                                            <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>:00
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <?= form_error('triggered_time') ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group <?= form_error('status') == '' ?: 'has-error'; ?>">
                                <label for="status">Status Schedule</label>
                                <select class="form-control select2" name="status" id="status">
                                    <option value="<?= ReportScheduleModel::STATUS_ACTIVE ?>"<?= set_select('status', ReportScheduleModel::STATUS_ACTIVE, ReportScheduleModel::STATUS_ACTIVE == $reportSchedule['status']) ?>>
                                        <?= ReportScheduleModel::STATUS_ACTIVE ?>
                                    </option>
                                    <option value="<?= ReportScheduleModel::STATUS_INACTIVE ?>"<?= set_select('status', ReportScheduleModel::STATUS_INACTIVE, ReportScheduleModel::STATUS_INACTIVE == $reportSchedule['status']) ?>>
                                        <?= ReportScheduleModel::STATUS_INACTIVE ?>
                                    </option>
                                </select>
                                <?= form_error('status') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Update Report Schedule
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/report-schedule.js?v=1') ?>" defer></script>