<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Delivery Order</h3>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Category</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $booking['category'] ?> : <?= $booking['booking_type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $booking['customer_name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $booking['no_booking'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($booking['no_reference'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Upload</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (empty($booking['no_upload'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= site_url('upload/view/' . $booking['id_upload']) ?>">
                                        <?= $booking['no_upload'] ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Booking Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($booking['booking_date'], false) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">ETA</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= readable_date($booking['eta'], false) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">ETA Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(difference_date(date('Y-m-d'), $booking['eta']) == 0): ?>
                                    <span class="label label-primary">Berthing</span>
                                <?php elseif (difference_date(date('Y-m-d'), $booking['eta']) > 0): ?>
                                    <span class="label label-success">Onboard</span>
                                <?php else: ?>
                                    <span class="label label-danger">Overdue</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($booking['description'], 'No description', '', '', true) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($booking['created_at']) ?> by <?= if_empty($booking['creator_name'], 'Unknown') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-default">
            <div class="box-header">
                <?php if(empty($do)): ?>
                    <h3 class="box-title">DO</h3>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_ORDER_CREATE)): ?>
                        <a href="<?= site_url('upload/response/' . $booking['id_upload'] . '?id_document_type=' . $doDocId . '&redirect=' . base_url(uri_string())) ?>"
                           class="btn btn-sm btn-primary pull-right">
                            Upload DO
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <h3 class="box-title">DO : <?= $do['no_document'] ?></h3>
                    -
                    <?php if($do['is_valid'] == 0): ?>
                        ON REVIEW
                    <?php elseif($do['is_valid'] == 1): ?>
                        <span class="text-success">VALIDATED</span>
                    <?php else: ?>
                        <span class="text-danger">REJECTED</span>
                    <?php endif; ?>
                    <?= if_empty($do['validator'], '', 'By ') ?>
                    <div class="pull-right row-upload-document"
                         data-id="<?= $do['id'] ?>"
                         data-label="<?= $do['document_type'] ?>">
                        <?php if($do['is_valid'] != 1): ?>
                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
                                <a class="btn btn-primary btn-sm btn-validate-document"
                                   href="<?= site_url('upload_document/validate/' . $do['id'] . '?redirect=' . base_url(uri_string())) ?>">
                                    Validate
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-default" disabled>Validated</button>
                        <?php endif; ?>
                        <a href="<?= site_url('upload_document/edit/' . $do['id'] . '?redirect=' . base_url(uri_string())) ?>"
                           class="btn btn-sm btn-success">
                            Edit
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <table class="table table-condensed no-datatable">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Uploader</th>
                        <th>No Document</th>
                        <th>Doc Date</th>
                        <th>Uploaded At</th>
                        <th>Files</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1 ?>
                    <?php foreach (get_if_exist($do, 'files', []) as $file): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $file['uploader'] ?></td>
                            <td><?= $file['no_document'] ?></td>
                            <td><?= readable_date($file['document_date'], false) ?></td>
                            <td><?= readable_date($file['created_at']) ?></td>
                            <td>
                                <a href="<?= asset_url($file['directory'] . '/' . $file['source']) ?>" target="_blank">
                                    <?= $file['source'] ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($do['files'])): ?>
                        <tr>
                            <td colspan="6">No document uploaded</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-default">
            <div class="box-header">
                <?php if(empty($ata)): ?>
                    <h3 class="box-title">ATA</h3>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_ORDER_CREATE)): ?>
                        <a href="<?= site_url('upload/response/' . $booking['id_upload'] . '?id_document_type=' . $ataDocId . '&redirect=' . base_url(uri_string())) ?>"
                           class="btn btn-sm btn-primary pull-right">
                            Upload ATA
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <h3 class="box-title">ATA : <?= $ata['no_document'] ?>
                        <span class="text-danger">
                            (<?= readable_date($ata['document_date'], false) ?>)
                        </span>
                    </h3>
                    -
                    <?php if($ata['is_valid'] == 0): ?>
                        ON REVIEW
                    <?php elseif($ata['is_valid'] == 1): ?>
                        <span class="text-success">VALIDATED</span>
                    <?php else: ?>
                        <span class="text-danger">REJECTED</span>
                    <?php endif; ?>
                    <?= if_empty($ata['validator'], '', 'By ') ?>
                    <div class="pull-right row-upload-document"
                         data-id="<?= $ata['id'] ?>"
                         data-label="<?= $ata['document_type'] ?>">
                        <?php if($ata['is_valid'] != 1): ?>
                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
                                <a class="btn btn-primary btn-sm btn-validate-document"
                                   href="<?= site_url('upload_document/validate/' . $ata['id'] . '?redirect=' . base_url(uri_string())) ?>">
                                    Validate
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-default" disabled>Validated</button>
                        <?php endif; ?>
                        <a href="<?= site_url('upload_document/edit/' . $ata['id'] . '?redirect=' . base_url(uri_string())) ?>"
                           class="btn btn-sm btn-success">
                            Edit
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <table class="table table-condensed no-datatable">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Uploader</th>
                        <th>No Document</th>
                        <th>Doc Date</th>
                        <th>Uploaded At</th>
                        <th>Files</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1 ?>
                    <?php foreach (get_if_exist($ata, 'files', []) as $file): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $file['uploader'] ?></td>
                            <td><?= $file['no_document'] ?></td>
                            <td><?= readable_date($file['document_date'], false) ?></td>
                            <td><?= readable_date($file['created_at']) ?></td>
                            <td>
                                <a href="<?= asset_url($file['directory'] . '/' . $file['source']) ?>" target="_blank">
                                    <?= $file['source'] ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($ata['files'])): ?>
                        <tr>
                            <td colspan="6">No document uploaded</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-default">
            <div class="box-header">
                <?php if(empty($sppb)): ?>
                    <h3 class="box-title">SPPB</h3>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_ORDER_CREATE)): ?>
                        <a href="<?= site_url('upload/response/' . $booking['id_upload'] . '?id_document_type=' . $sppbDocId . '&redirect=' . base_url(uri_string())) ?>"
                           class="btn btn-sm btn-primary pull-right">
                            Upload SPPB
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <h3 class="box-title">SPPB : <?= $sppb['no_document'] ?></h3>
                    -
                    <?php if($sppb['is_valid'] == 0): ?>
                        ON REVIEW
                    <?php elseif($sppb['is_valid'] == 1): ?>
                        <span class="text-success">VALIDATED</span>
                    <?php else: ?>
                        <span class="text-danger">REJECTED</span>
                    <?php endif; ?>
                    <?= if_empty($sppb['validator'], '', 'By ') ?>
                    <div class="pull-right row-upload-document"
                         data-id="<?= $sppb['id'] ?>"
                         data-label="<?= $sppb['document_type'] ?>">
                        <?php if($sppb['is_valid'] != 1): ?>
                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
                                <a class="btn btn-primary btn-sm btn-validate-document"
                                   href="<?= site_url('upload_document/validate/' . $sppb['id'] . '?redirect=' . base_url(uri_string())) ?>">
                                    Validate
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-default" disabled>Validated</button>
                        <?php endif; ?>
                        <a href="<?= site_url('upload_document/edit/' . $sppb['id'] . '?redirect=' . base_url(uri_string())) ?>"
                           class="btn btn-sm btn-success">
                            Edit
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <table class="table table-condensed no-datatable">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Uploader</th>
                        <th>No Document</th>
                        <th>Doc Date</th>
                        <th>Uploaded At</th>
                        <th>Files</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1 ?>
                    <?php foreach (get_if_exist($sppb, 'files', []) as $file): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $file['uploader'] ?></td>
                            <td><?= $file['no_document'] ?></td>
                            <td><?= readable_date($file['document_date'], false) ?></td>
                            <td><?= readable_date($file['created_at']) ?></td>
                            <td>
                                <a href="<?= asset_url($file['directory'] . '/' . $file['source']) ?>" target="_blank">
                                    <?= $file['source'] ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($sppb['files'])): ?>
                        <tr>
                            <td colspan="6">No document uploaded</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if(!empty($do) && $do['is_valid'] && !empty($sppb) && !empty($ata)): ?>
            <div class="box box-success">
                <div class="box-header">
                    <?php if(empty($tila)): ?>
                        <h3 class="box-title">TILA</h3>
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_ORDER_CREATE)): ?>
                            <a href="<?= site_url('upload/response/' . $booking['id_upload'] . '?id_document_type=' . $tilaDocId . '&redirect=' . base_url(uri_string())) ?>"
                               class="btn btn-sm btn-primary pull-right">
                                Upload Tila
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <h3 class="box-title">Tila : <?= $tila['no_document'] ?></h3>
                        <div class="pull-right row-upload-document"
                             data-id="<?= $tila['id'] ?>"
                             data-label="<?= $tila['document_type'] ?>">
                            <a href="<?= site_url('upload_document/edit/' . $tila['id'] . '?redirect=' . base_url(uri_string())) ?>"
                               class="btn btn-sm btn-success">
                                Edit
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box-body">
                    <table class="table table-condensed no-datatable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Uploader</th>
                            <th>No Document</th>
                            <th>Doc Date</th>
                            <th>Uploaded At</th>
                            <th>Files</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1 ?>
                        <?php foreach (get_if_exist($tila, 'files', []) as $file): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $file['uploader'] ?></td>
                                <td><?= $file['no_document'] ?></td>
                                <td><?= readable_date($file['document_date'], false) ?></td>
                                <td><?= readable_date($file['created_at']) ?></td>
                                <td>
                                    <a href="<?= asset_url($file['directory'] . '/' . $file['source']) ?>" target="_blank">
                                        <?= $file['source'] ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($tila['files'])): ?>
                            <tr>
                                <td colspan="6">No document uploaded</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Activity Intervals Summary</h3>
            </div>
            <div class="box-body">
                <table class="table table-condensed table-striped responsive no-datatable">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>Doc Number</th>
                        <th>Author</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>Booking Created</td>
                        <td><?= readable_date($booking['created_at']) ?></td>
                        <td><?= if_empty($booking['no_reference'], '-') ?></td>
                        <td><?= if_empty($booking['creator_name'], '-') ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>ETA</td>
                        <td><?= readable_date($booking['eta'], false) ?></td>
                        <td><?= if_empty($booking['no_reference'], '-') ?></td>
                        <td><?= if_empty($booking['creator_name'], '-') ?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>DO Uploaded</td>
                        <td><?= readable_date($do['created_at']) ?></td>
                        <td><?= get_if_exist($do, 'no_document', '-') ?></td>
                        <td><?= get_if_exist($do, 'uploader', '-') ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>DO Validated</td>
                        <td><?= readable_date($do['validated_at']) ?></td>
                        <td><?= get_if_exist($do, 'no_document', '-') ?></td>
                        <td><?= get_if_exist($do, 'validator', '-') ?></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>ATA</td>
                        <td><?= readable_date($ata['created_at']) ?></td>
                        <td><?= get_if_exist($ata, 'no_document', '-') ?></td>
                        <td><?= get_if_exist($ata, 'uploader', '-') ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>SPPB Uploaded</td>
                        <td><?= readable_date($sppb['created_at']) ?></td>
                        <td><?= get_if_exist($sppb, 'no_document', '-') ?></td>
                        <td><?= get_if_exist($sppb, 'uploader', '-') ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>TILA Uploaded</td>
                        <td><?= readable_date($tila['created_at']) ?></td>
                        <td><?= get_if_exist($tila, 'no_document', '-') ?></td>
                        <td><?= get_if_exist($tila, 'uploader', '-') ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary">
            Back
        </a>
        <?php if(!empty($tila) && $tila['is_valid']): ?>
            <a href="<?= site_url('safe-conduct/create?id_booking=' . $booking['id']) ?>" class="btn btn-success pull-right">
                Create Safe Conduct
            </a>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('upload/_modal_validate_document') ?>

<script src="<?= base_url('assets/app/js/upload_document.js') ?>" defer></script>