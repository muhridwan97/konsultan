<form action="<?= site_url("upload_document/download_file/" . $upload['id']) ?>" role="form" method="post" id="form-role">
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Uploaded Documents</h3>
        <div class="pull-right">
            <?php if ($upload['is_hold']): ?>
                <a href="<?= site_url("upload/release/{$upload['id']}") ?>"
                   class="btn btn-success btn-validate"
                   data-validate="release" data-label="<?= $upload['no_upload'] ?>">
                    Release
                </a>
            <?php else: ?>
                <?php if (AuthorizationModel::isAuthorized([PERMISSION_UPLOAD_CREATE, PERMISSION_UPLOAD_VALIDATE]) && in_array($upload['status'], [UploadModel::STATUS_NEW, UploadModel::STATUS_ON_PROCESS])): ?>
                    <a href="<?= site_url("upload/hold/{$upload['id']}") ?>"
                       class="btn btn-danger btn-validate"
                       data-validate="hold" data-label="<?= $upload['no_upload'] ?>">
                        Hold
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE) && ($upload['status'] == UploadModel::STATUS_PAID || $allowSetAP)): ?>
                <a href="<?= site_url("upload/set-analyzing-point/{$upload['id']}") ?>"
                   class="btn btn-success btn-validate"
                   data-validate="Set Analyzing Point" data-label="<?= $upload['no_upload'] ?>">
                    <i class="fa fa-check-square"></i> Set Analyzing Point
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <div class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Upload</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $upload['no_upload'] ?></p>
                            <input type="hidden" name="no_upload" value="<?= $upload['no_upload']?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $upload['name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Category</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $upload['category'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Booking Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $upload['booking_type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if ($upload['is_valid']): ?>
                                    <?php
                                    if (empty($upload['id_booking'])) {
                                        $linkBooking = site_url('booking/create/' . $upload['id']);
                                        $labelActionBooking = 'Create Booking';
                                    } else {
                                        $linkBooking = site_url('booking/view/' . $upload['id_booking']);
                                        $labelActionBooking = $upload['no_booking'];
                                    }
                                    ?>
                                    <a href="<?= $linkBooking ?>"><?= $labelActionBooking ?></a>
                                <?php else: ?>
                                    Validate Upload First
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Upload Ref</label>
                        <div class="col-sm-9">
                            <?php if($upload['type'] == BookingTypeModel::TYPE_IMPORT): ?>
                                <p class="form-control-static">
                                    <?php if(empty($uploadIn)): ?>
                                        -
                                    <?php else: ?>
                                        <a href="<?= site_url('upload/view/' . $uploadIn['id']) ?>">
                                            <?= $uploadIn['no_upload'] ?>
                                        </a>
                                    <?php endif; ?>
                                </p>
                            <?php else: ?>
                                <?php foreach ($uploadReferences as $uploadReference): ?>
                                    <p class="form-control-static">
                                        <a href="<?= site_url('upload/view/' . $uploadReference['id_upload_reference']) ?>">
                                            <?= $uploadReference['no_upload_reference'] ?>
                                        </a>
                                    </p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($upload['description'], 'No description') ?>
                                (<a href="<?= site_url('upload/edit/' . $upload['id']) ?>"> Edit</a>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $upload['type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $uploadStatuses = [
                                    UploadModel::STATUS_REJECTED => 'danger',
                                    UploadModel::STATUS_NEW => 'default',
                                    UploadModel::STATUS_ON_PROCESS => 'info',
                                    UploadModel::STATUS_HOLD => 'danger',
                                    UploadModel::STATUS_RELEASED => 'success',
                                    UploadModel::STATUS_BILLING => 'primary',
                                    UploadModel::STATUS_PAID => 'success',
                                    UploadModel::STATUS_AP => 'warning',
                                    UploadModel::STATUS_CLEARANCE => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($uploadStatuses, $upload['status'], 'default') ?>">
                                    <?= $upload['status'] ?>
                                </span>
                                <?php if($upload['is_hold']): ?>
                                    <span class="label label-danger ml5">
                                        HOLD
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Is Valid</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <span class="label label-<?= $upload['is_valid'] ? 'success' : 'danger' ?>">
                                    <?= $upload['is_valid'] ? 'Yes' : 'No' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($upload['created_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($upload['updated_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header clearfix">
        <h3 class="box-title">Upload Documents</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_CREATE)): ?>
            <div class="pull-right">
                <button type="submit" class="btn btn-success">
                    <i class="fa ion-android-download" ></i> Download
                </button>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
                    <a href="<?= site_url('upload/response/' . $upload['id']) ?>" class="btn btn-primary">
                        <i class="fa ion-reply-all"></i> Response
                    </a>
                <?php endif ?>
                <a href="<?= site_url("upload_document/create/$id") ?>" class="btn btn-primary">
                    <i class="fa ion-plus-round"></i> Add Docs
                </a>
            </div>
        <?php endif ?>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped responsive" id="table-document">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Check</th>
                    <th>Type Document</th>
                    <th>No Document</th>
                    <th>Document Date</th>
                    <th>Description</th>
                    <th>Total Files</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Validated At</th>
                    <th>Validated</th>
                    <th>Type</th>
                    <th style="width: 60px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1;
                foreach ($documents as $document): ?>
                    <tr class="<?= $document['is_response'] ? 'success' : '' ?>">
                        <td><?= $no++ ?></td>
                        <td class="text-center">
                            <input type="checkbox" class="checked-download" id="icheck" name="id_doc[]" value="<?= $document['id']?>">
                        </td>
                        <td>
                            <a href="<?= site_url('upload_document/download/' . $document['id']) ?>">
                                <?= $document['document_type'] ?>
                            </a>
                        </td>
                        <td><?= if_empty($document['no_document'],'-') ?></td>
                        <td><?= readable_date($document['document_date'], false) ?></td>
                        <td><?= if_empty($document['description'], 'No description') ?></td>
                        <td>
                            <?php if ($document['total_file'] == 0): ?>
                                No file available
                            <?php else: ?>
                                <a href="<?= site_url('upload_document/view/' . $document['id']) ?>">
                                    <?= numerical($document['total_file'], 3, true) ?> files
                                </a>
                            <?php endif ?>
                        </td>
                        <td><?= empty($document['created_name']) ? '-' : $document['created_name'] ?></td>
                        <td><?= format_date($document['created_at'], 'd F Y H:i') ?> </td>
                        <td><?= if_empty(format_date($document['validated_at'], 'd F Y H:i'),'-') ?> </td>
                        <td>
                            <?php
                            $statusLabel = [
                                0  => 'label-warning',
                                1  => 'label-success',
                                -1 => 'label-danger',
                            ];

                            $explodeName = explode(' ',$document['document_type']);
                            $lastNameDocType = array_pop($explodeName);
                            ?>
                            <?php if($document['is_valid'] == 0 && $document['is_check'] == 0): ?>
                                <span class="label label-default ?>"> Pending </span>
                            <?php else: ?>
                                <span class="label <?= $statusLabel[$document['is_valid']] ?> ?>">
                                <?php if($document['is_valid'] == 0): ?>
                                    On Review
                                <?php elseif($document['is_valid'] == 1 && $lastNameDocType != DocumentTypeModel::DOC_CONF): ?>
                                    Valid
                                <?php elseif($document['is_valid'] == 1 && $lastNameDocType == DocumentTypeModel::DOC_CONF): ?>
                                    Confirmed
                                <?php else: ?>
                                    Rejected
                                <?php endif ?>
                            </span>
                            <?php endif ?>
                        </td>
                        <td >
                            <?php
                            $statusLabel = [
                                0 => 'label-warning',
                                1 => 'label-primary',
                            ];
                            ?>
                            <span class="label <?= $statusLabel[$document['is_response']] ?>">
                            <?= $document['is_response'] ? 'Response' : 'Request' ?>
                        </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right row-upload-document"
                                    data-id="<?= $document['id'] ?>"
                                    data-id-upload="<?= $upload['id'] ?>"
                                    data-label="<?= $document['document_type'] ?>">

                                    <li class="dropdown-header">ACTION</li>

                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VIEW)): ?>
                                        <li>
                                            <a href="<?= site_url('upload_document/view/' . $document['id']) ?>">
                                                <i class="fa ion-document"></i>View Files
                                            </a>
                                        </li>
                                    <?php endif ?>

                                    <?php if($document['is_valid'] == 0 && $document['is_check'] == 0): ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_CHECK)): ?>
                                            <li>
                                                <a class="btn-check-document"
                                                   href="<?= site_url('upload_document/check/' . $document['id']) ?>">
                                                    <i class="fa fa-check-circle-o"></i>Check Document
                                                </a>
                                            </li>
                                        <?php endif ?>
                                    <?php else: ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
                                            <li>
                                                <a class="btn-validate-document"
                                                   href="<?= site_url('upload_document/validate/' . $document['id']) ?>">
                                                    <i class="fa ion-checkmark"></i>Validate Document
                                                </a>
                                            </li>
                                        <?php endif ?>
                                    <?php endif ?>

                                    <?php if ((UserModel::authenticatedUserData('user_type') != "INTERNAL" && $document['is_valid'] != 1) || ((UserModel::authenticatedUserData('user_type') == "INTERNAL"))): ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_EDIT)): ?>
                                            <li>
                                                <a href="<?= site_url('upload_document/edit/' . $document['id']) ?>">
                                                    <i class="fa ion-compose"></i>Edit / Re-upload
                                                </a>
                                            </li>
                                        <?php endif ?>

                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_DELETE)): ?>
                                            <li role="separator" class="divider"></li>
                                            <li>
                                                <a href="<?= site_url('upload_document/delete/' . $document['id']) ?>"
                                                   class="btn-delete-document">
                                                    <i class="fa ion-trash-a"></i> Delete
                                                </a>
                                            </li>
                                        <?php endif ?>
                                    <?php endif ?>
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
</form>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Uploaded Photos</h3>
    </div>
    <div class="box-body">
        <div class="box box-primary">
            <div class="box-header">
                <h4 class="box-title">Photo Item</h4>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
                <div class="pull-right">
                    <a href="<?= site_url("upload_item_photo/add") ?>" class="btn btn-success btn-add-item" data-id-upload="<?= $id ?>" data-id-person="<?= $upload['id_person'] ?>">
                        <i class="fa ion-plus-round"></i> Add Item
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-photo">
                    <thead>
                    <tr>
                        <th style="width: 20px">No</th>
                        <th>Item Name</th>
                        <th>No HS</th>
                        <th>Link</th>
                        <th>Reason</th>
                        <th style="width: 60px">Validated</th>
                        <th style="width: 60px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;
                    foreach ($photos as $photo): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $photo['item_name'] ?></td>
                            <td><?php if(!empty($photo['id_item']) && $photo['status'] == UploadItemPhotoModel::STATUS_VALIDATED): ?> 
                                <?= $photo['no_hs_master'] ?>
                                <?php else: ?> 
                                <?= $photo['no_hs'] ?>
                            <?php endif; ?></td>
                            <td><?php if(!empty($photo['id_item'])): ?> 
                                 <a href="<?= site_url().'item-compliance/view/'.$photo['id_item']  ?>" target="_blank"><?=$photo['item_name_master']?></a> 
                                <?php else: ?> 
                                    Not Yet Set
                                <?php endif; ?>
                            </td>
                            <td><?= if_empty($photo['description_validated'], 'No reason') ?></td>
                            <td><?php if($photo['status'] == UploadItemPhotoModel::STATUS_ON_REVIEW) : ?>
                                <span class="label label-warning">
                                    On Review
                                </span>
                            <?php elseif($photo['status'] == UploadItemPhotoModel::STATUS_REJECTED) : ?>
                                <span class="label label-danger">
                                    Reject
                                </span>
                            <?php else : ?>
                                <span class="label label-success">
                                    Valid
                                </span>
                            <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right row-upload-photo"
                                        data-id="<?= $photo['id'] ?>"
                                        data-id-upload="<?= $photo['id_upload'] ?>"
                                        data-id-person="<?= $photo['id_customer'] ?>"
                                        data-id-item="<?= $photo['id_item'] ?>"
                                        data-item-name="<?= $photo['item_name_master'] ?>"
                                        data-total-file="<?= $photo['total_file'] ?>"
                                        data-label="<?= $photo['item_name'] ?>">                                        

                                        <li class="dropdown-header">ACTION</li>
                                        <li>
                                            <a class="btn-view-photo"
                                            href="">
                                                <i class="fa ion-search"></i>View Photo
                                            </a>
                                        </li>
                                        <?php if($photo['status'] == UploadItemPhotoModel::STATUS_ON_REVIEW): ?>
                                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_CHECK)): ?>
                                                <li>
                                                    <a class="btn-validate-photo"
                                                    href="<?= site_url('upload_item_photo/validated/' . $photo['id']) ?>">
                                                        <i class="fa ion-checkmark"></i>Validate Photo
                                                    </a>
                                                </li>
                                            <?php endif ?>
                                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_DELETE)): ?>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a href="<?= site_url('upload_item_photo/delete/' . $photo['id']) ?>"
                                                    class="btn-delete-photo">
                                                        <i class="fa ion-trash-a"></i> Delete
                                                    </a>
                                                </li>
                                            <?php endif ?>
                                        <?php endif ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($photos) <= 0): ?>
                        <tr>
                            <td colspan="8">No documents available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if(!empty($itemCompliances)): ?>
        <form id="form-photo" action="<?= site_url('upload-item-photo/update') ?>" role="form" method="post" enctype="multipart/form-data">
        <div class="box box-danger">
            <div class="box-header">
                <h4 class="box-title">Required Photos</h4>
            </div>
            <div class="box-body" id="photo-wrapper">
            <?php foreach($itemCompliances as $index => $itemCompliance): ?>
                    <div class="panel panel-danger card-photo required-photo">
                        <div class="panel-heading">
                            <?= $itemCompliance['item_name'] ?>
                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_CHECK)) :?>
                            <a href="<?= site_url('upload_item_photo/delete/') ?>"
                                class="btn-delete-photo pull-right"
                                data-id="<?= $itemCompliance['id'] ?>"
                                data-label="<?= $itemCompliance['item_name'] ?>"
                                data-id-upload="<?= $itemCompliance['id_upload'] ?>">
                                <i class="fa fa-remove"></i>
                            </a>
                            <?php endif; ?>
                            <span class="pull-right">Required&nbsp</span>
                        </div>
                        <div class="panel-body">
                            <input type="hidden" name="id_upload" value="<?= $id ?>">
                            <input type="hidden" name="photos[<?= $index ?>][id_item_photo]" value="<?= $itemCompliance['id'] ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="item_name_<?= $index ?>" class="control-label">
                                            Item Name
                                        </label>
                                        <input type="text" class="form-control"
                                            placeholder="Enter Item Name"
                                            id="item_name_<?= $index ?>" name="photos[<?= $index ?>][item_name]" value="<?= $itemCompliance['item_name'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="no_hs_<?= $index ?>" class="control-label">
                                            No HS
                                        </label>
                                        <input type="text" class="form-control" minlength="8" maxlength="8"
                                            placeholder="Enter HS Number"
                                            id="no_hs_<?= $index ?>" name="photos[<?= $index ?>][no_hs]" value="<?= $itemCompliance['no_hs'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="file_photo_<?= $index ?>">
                                            File Photos <span style="color:#a9a9a9">(Upload max 3 MB)</span>
                                        </label>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="btn btn-primary btn-block fileinput-button">
                                                    <span class="button-file">Select Photo</span>
                                                    <input class="upload-photo" id="file_photo_<?= $index ?>" type="file" accept="image/*" name="file_photo_<?= $index ?>">
                                                </div>
                                                <div class="upload-input-wrapper"></div>
                                            </div>
                                            <div class="col-sm-9">
                                                <div id="progress" class="progress progress-upload">
                                                    <div class="progress-bar progress-bar-success"></div>
                                                </div>
                                                <div class="uploaded-file"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach; ?>
            </div>
            <div class="box-footer">
                <i class="fa fa-info-circle"></i> &nbsp;
                    You need to upload all the required photos.
                <button type="submit" class="btn btn-primary pull-right btn-upload">Save Upload</button>
            </div>
        </div>
        </form>
        <?php endif; ?>
    </div>
</div>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Status Histories</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped no-datatable responsive">
            <thead>
            <tr>
                <th style="width: 50px" class="text-center">No</th>
                <th>Status</th>
                <th>Description</th>
                <th>Data</th>
                <th>Created At</th>
                <th>Created By</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($statusHistories as $index => $statusHistory): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td>
                        <span class="label label-<?= get_if_exist($uploadStatuses, $statusHistory['status'], 'default') ?>">
                            <?= $statusHistory['status'] ?>
                        </span>
                    </td>
                    <td><?= if_empty($statusHistory['description'], '-') ?></td>
                    <td>
                        <?php if(empty($statusHistory['data'])): ?>
                            -
                        <?php else: ?>
                            <a href="<?= site_url('history/view/' . $statusHistory['id']) ?>">
                                View History
                            </a>
                        <?php endif; ?>
                    </td>
                    <td><?= format_date($statusHistory['created_at'], 'd F Y H:i') ?></td>
                    <td><?= if_empty($statusHistory['creator_name'], "No user") ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($statusHistories)): ?>
                <tr>
                    <td colspan="5">No statuses available</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary no-border">
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-document">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_upload" id="id_upload">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Document</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete document
                            <strong id="document-title"></strong>?
                        </p>
                        <p class="text-danger">
                            This action will delete all related files.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if (AuthorizationModel::isAuthorized([PERMISSION_UPLOAD_VALIDATE, PERMISSION_UPLOAD_CREATE])): ?>
    <?php $this->load->view('template/_modal_validate'); ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE)): ?>
    <?php $this->load->view('upload/_modal_validate_document') ?>
    <?php $this->load->view('upload/_modal_validate_photo') ?>
    <?php $this->load->view('item_compliance/_modal_add_item') ?>
<?php endif ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_CHECK)): ?>
    <?php $this->load->view('upload/_modal_check_document') ?>
<?php endif ?>
<?php $this->load->view('upload/_modal_delete_photo') ?>
<?php $this->load->view('upload/_modal_view_photo') ?>

<script src="<?= base_url('assets/app/js/upload_document.js?v=1') ?>" defer></script>
<script src="<?= base_url('assets/app/js/upload-item-photo.js?v=5') ?>" defer></script>
