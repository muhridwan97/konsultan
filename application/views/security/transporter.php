<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Transporter</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped responsive" id="table-security-transporter">
                <thead>
                <tr>
                    <th style="width: 30px">No</th>
                    <th>PLATE NUMBER</th>
                    <th>TEP CODE</th>
                    <th>NO SAFE CONDUCT</th>
                    <th>CUSTOMER NAME</th>
                    <th>NAME</th>
                    <th>VEHICLE</th>
                    <th>CHASSIS</th>
                    <th>CHECK IN AT</th>
                    <th>SECURITY</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1;
                foreach ($datas['data'] as $data): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="responsive-title"><?= $data['receiver_no_police'] ?></td>
                        <td><a href="<?= site_url('transporter-entry-permit/view/' . $data['id']) ?>"><?= if_empty($data['tep_code'], '-') ?></a></td>
                        <td><a href="<?= site_url('safe-conduct/view/' . $data['id_safe_conduct']) ?>"><?= if_empty($data['no_safe_conduct'], '-') ?></a></td>
                        <td><?= if_empty($data['customer_name'], if_empty($data['customer_name_in'], if_empty($data['customer_name_out'], '-'))) ?></td>
                        <td><?= if_empty($data['receiver_name'], '-') ?></td>
                        <td><?= if_empty($data['receiver_vehicle'], '-')?></td>
                        <td><?= if_empty($data['no_chassis'], '-')?></td>
                        <td><?= if_empty($data['checked_in_at'], '-')?></td>
                        <td><?= if_empty($data['check_in_name'], '-')?></td>
                        <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_EDIT_SECURITY) || ($data['can_edit']==1 && $data['checked_in_by']==UserModel::authenticatedUserData('id')) && empty($data['checked_out_by'])): ?>
                                    <li>
                                        <a href="<?= site_url('transporter-entry-permit/edit-tep/'.$data['id']) ?>">
                                            <i class="fa fa-edit"></i> Edit
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
</div>