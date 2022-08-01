<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Report Schedule</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-report-schedule">
            <thead>
            <tr>
                <th style="width: 30px" class="text-center">No</th>
                <th>Report Name</th>
                <th>Description</th>
                <th>Recurring</th>
                <th>Triggered At</th>
                <th>Status</th>
                <th>Updated By</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $taskStatuses = [
                ReportScheduleModel::STATUS_ACTIVE => 'success',
                ReportScheduleModel::STATUS_INACTIVE => 'danger',
            ];
            ?>
            <?php foreach ($reportSchedules as $index => $reportSchedule): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?= str_replace(['-', '_'], ' ', $reportSchedule['report_name']) ?></td>
                    <td><?= if_empty($reportSchedule['description'], '-') ?></td>
                    <td class="no-wrap"><?= if_empty($reportSchedule['recurring_period'], '-') ?></td>
                    <td><?= if_empty($reportSchedule['schedule_label'], '-') ?></td>
                    <td>
                        <span class="label label-<?= get_if_exist($taskStatuses, $reportSchedule['status'], 'primary') ?>">
                            <?= $reportSchedule['status'] ?>
                        </span>
                    </td>
                    <td><?= if_empty($reportSchedule['updater_name'], '-') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_REPORT_SCHEDULE_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('report-schedule/view/' . $reportSchedule['report_name']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_REPORT_SCHEDULE_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('report-schedule/edit/' . $reportSchedule['report_name']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>