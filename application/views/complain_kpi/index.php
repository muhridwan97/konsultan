<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Complain KPI</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
	</div>
	<div class="box-body">
        <?php $this->load->view('template/_alert') ?>

		<table class="table table-bordered table-striped responsive" id="table-unit">
			<thead>
				<tr>
					<th style="width: 30px;">No</th>
					<th>KPI</th>
                    <th>Major</th>
                    <th>Minor</th>
                    <th>Reminder time</th>
                    <th>Reminder day</th>
					<th>Whatsapp group</th>
					<th style="width: 60px">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;
				foreach ($complain_kpi as $complain): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $complain['kpi'] ?></td>
                    <td class="responsive-title"><?= if_empty($complain['major'],"-") ?></td>
                    <td class="responsive-title"><?= if_empty($complain['minor'],"-") ?></td>
                    <td class="responsive-title"><?= $complain['kpi'] == ComplainKpiModel::KPI_RESPONSE_WAITING_TIME ? '-' : $complain['reminder'] ?></td>
                    <td class="responsive-title"><?= $complain['kpi'] == ComplainKpiModel::KPI_RESPONSE_WAITING_TIME ? '-' : "Every {$complain['recur_day']} day" ?></td>
                    <td class="responsive-title"><?= $complain['kpi'] == ComplainKpiModel::KPI_RESPONSE_WAITING_TIME ? '-' : if_empty($complain['whatsapp_groups'],$complain['whatsapp_group']) ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_KPI_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('complain-kpi/view/' . $complain['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_KPI_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('complain-kpi/edit/' . $complain['id']) ?>">
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