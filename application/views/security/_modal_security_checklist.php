<!-- CONTAINER -->
<?php if(!empty($Checklists)): ?>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-checklist-container" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" id="form-checklist-container">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Security Checklist : <strong id="check-title"></strong></h4>
                </div>
                <div class="modal-body">
                    <?php 
                    $isSeal=false;
                    foreach ($data_checklist_types as $data_checklist_type):
                        if ($data_checklist_type['subtype']=='CONTAINER') {
                            $isSeal=true;
                            $branch_type = get_active_branch('branch_type');
                        } ?>
                    <h5 class="box-title mt0" style="font-size: 16px"><?=  $data_checklist_type['checklist_type'] ?></h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped no-datatable">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">No</th>
                                    <th>Description</th>
                                    <th class="text-center">Is Good</th>
                                    <th class="text-center">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;?>
                                <?php foreach ($Checklists as $Checklist): ?>
                                <?php if ($Checklist['checklist_type'] == $data_checklist_type['checklist_type'] ): ?>
                                <tr>
                                    <td class="responsive-hide"><?= $no++ ?></td>
                                    <td class="responsive-title"><?= $Checklist['description'] ?></td>
                                    <td class="responsive-title text-center">
                                        <input type="checkbox" name="result[]"  class="checkboxes" id="iCheck" value="<?= $Checklist['id'] ?>">
                                    </td>
                                    <td class="text-center">
                                        <textarea name="reason[]" class="form-control reason" placeholder="Description" rows="1" required></textarea>
                                        <input type="hidden" name="id_checklists[]" value="<?= $Checklist['id'] ?>">
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                    <!-- <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="file-upload-default" data-max-size="3000000" accept="image/*" capture="camera" required>
                        <div class="input-group col-xs-12">
                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" readonly placeholder="Upload attachment">
                            <span class="input-group-btn">
                                <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">Upload</button>
                            </span>
                        </div>
                    </div> -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Security Check Photo</h3>
                        </div>
                        <div class="box-body">
                            <?php foreach ($securityCheckPhotoTypes as $securityCheckPhotoType): ?>
                                <div class="form-group">
                                    <div>
                                        <label for="attachment"><?= $securityCheckPhotoType['photo_title'] ?></label>
                                        <input type="file" id="attachment" name="attachment" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera" >
                                        <div class="input-group col-xs-12">
                                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none;color:#AAA;
background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false" placeholder="Capture Photo" required>
                                            <span class="input-group-btn">
                                                <button class="file-upload-browse btn btn-default btn-photo-picker button-file" type="button">Capture</button>
                                            </span>
                                        </div>
                                        <div class="upload-input-wrapper"></div>
                                        <input type="hidden" name="security_check_photos[]" value="<?= $securityCheckPhotoType['photo_title'] ?>">
                                    </div>
                                    <div>
                                        <div id="progress" class="progress progress-upload">
                                            <div class="progress-bar progress-bar-success"></div>
                                        </div>
                                        <div class="uploaded-file"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if(empty($securityCheckPhotoTypes)): ?>
                                <p>No security check photo available, <a href="<?= site_url('security-photo-type') ?>">setup here</a>.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if($isSeal && $branch_type=='TPP' && ( (isset($safeConduct) && $safeConduct['expedition_type'] == 'INTERNAL' && !is_null($safeConduct['security_in_date'])) || (isset($safeConduct) && $safeConduct['expedition_type'] == 'EXTERNAL' && is_null($safeConduct['security_in_date'])) || (isset($tep) && is_null($tep['checked_in_at'])) )): ?>
                        <!-- <div class="form-group">
                            <label for="attachment">Attachment Seal</label>
                            <input type="file" id="attachment_seal" name="attachment_seal" class="file-upload-default" data-max-size="3000000" accept="image/*" capture="camera" required>
                            <div class="input-group col-xs-12">
                                <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" readonly placeholder="Upload attachment seal">
                                <span class="input-group-btn">
                                    <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">Upload</button>
                                </span>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <div>
                                    <label for="attachment_seal">Capture Seal Photo</label>
                                    <input type="file" id="attachment_seal" name="attachment_seal" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera" >
                                    <div class="input-group col-xs-12">
                                        <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none;color:#AAA;
    background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false" placeholder="Capture Seal Photo" required>
                                        <span class="input-group-btn">
                                            <button class="file-upload-browse btn btn-default btn-photo-picker button-file" type="button">Capture</button>
                                        </span>
                                    </div>
                                <div class="upload-input-wrapper"></div>
                            </div>
                            <div>
                                <div id="progress" class="progress progress-upload">
                                    <div class="progress-bar progress-bar-success"></div>
                                </div>
                                <div class="uploaded-file"></div>
                            </div>
                        </div>
                        <div class="form-group">
                        <label for="description">Description Seal</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="Seal description" maxlength="500" required></textarea>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if(isset($safeConduct)): ?>
                    <?php if((is_null($safeConduct['security_in_date']))): ?>
                     <input type="hidden" name="security_type" value="CHECK IN">
                     <?php else: ?>
                        <?php if((!is_null($safeConduct['security_in_date']))):?>
                            <input type="hidden" name="security_type" value="CHECK OUT"> 
                        <?php endif; ?>            
                    <?php endif;?>
                    <input type="hidden" name="id_safe_conduct" id="id_safe_conduct" value="<?= $safeConduct['id'] ?>">
                    <input type="hidden" name="id_safe_conduct_container" id="id_container">
                    <input type="hidden" name="no_container" id="no_container">
                <?php endif;?>

                <?php if(isset($tep)): ?>
                    <?php if((is_null($tep['checked_in_at']))): ?>
                     <input type="hidden" name="security_type" value="CHECK IN">
                     <?php else: ?>
                        <?php if((!is_null($tep['checked_in_at']))):?>
                            <input type="hidden" name="security_type" value="CHECK OUT"> 
                        <?php endif; ?>            
                    <?php endif;?>
                    <input type="hidden" name="id_tep" value="<?= $tep['id'] ?>">
                    <input type="hidden" name="id_tep_container" id="id_container">
                    <input type="hidden" name="no_container" id="no_container">
                <?php endif;?>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-toggle="one-touch" class="btn btn-danger btn-save-container">Save</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php else: ?>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-checklist-container">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Security Checklist</h4>
                </div>
                <div class="modal-body">
                    <h4 class="text-warning" style="text-align: center;">Please Contact Admin !</strong></h4>
                </div>
               
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>


<!-- GOODS -->

<?php if(!empty($Checklists)): ?>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-checklist-goods">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" id="form-checklist-goods">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Security Checklist : <strong id="check-title"></strong></h4>
                </div>
                <div class="modal-body">
                    <?php foreach ($data_checklist_types as $data_checklist_type): ?>
                    <?php if ($data_checklist_type['subtype'] == "GOODS"): ?>
                    <h5 class="box-title mt0" style="font-size: 16px"><?= $data_checklist_type['checklist_type'] ?></h5>
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                            <tr>
                                <th style="width: 30px;">No</th>
                                <th>Description</th>
                                <th class="text-center">Is Good</th>
                                <th class="text-center">Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;?>
                            <?php foreach ($Checklists as $Checklist): ?>
                            <?php if ($Checklist['checklist_type'] ==  $data_checklist_type['checklist_type']): ?>
                            <tr>
                                <td class="responsive-hide"><?= $no++ ?></td>
                                <td class="responsive-title"><?= $Checklist['description'] ?></td>
                                <td class="responsive-title">
                                    <input type="checkbox" name="result[]"  class="checkboxes-goods" id="iCheck" value="<?= $Checklist['id'] ?>">
                                </td>
                                 <td class="text-center">
                                    <textarea name="reason[]" class="form-control reason-goods" placeholder="Description" rows="1" required></textarea>
                                </td>
                                <input type="hidden" name="id_checklists[]" value="<?= $Checklist['id'] ?>">
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <!-- <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera" required>
                        <div class="input-group col-xs-12">
                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" readonly placeholder="Upload attachment">
                            <span class="input-group-btn">
                                <button class="file-upload-browse btn btn-default btn-photo-picker" type="button">Upload</button>
                            </span>
                        </div>
                    </div> -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Security Check Photo</h3>
                        </div>
                        <div class="box-body">
                            <?php foreach ($securityCheckPhotoTypes as $securityCheckPhotoType): ?>
                                <div class="form-group">
                                    <div>
                                        <label for="attachment"><?= $securityCheckPhotoType['photo_title'] ?></label>
                                        <input type="file" id="attachment" name="attachment" class="file-upload-default upload-photo" data-max-size="3000000" accept="image/*" capture="camera" >
                                        <div class="input-group col-xs-12">
                                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none;color:#AAA; background:#F5F5F5;webkit-touch-callout: none;" onkeydown="return false" placeholder="Capture Photo" required>
                                            <span class="input-group-btn">
                                                <button class="file-upload-browse btn btn-default btn-photo-picker button-file" type="button">Capture</button>
                                            </span>
                                        </div>
                                        <div class="upload-input-wrapper"></div>
                                        <input type="hidden" name="security_check_photos[]" value="<?= $securityCheckPhotoType['photo_title'] ?>">
                                    </div>
                                    <div>
                                        <div id="progress" class="progress progress-upload">
                                            <div class="progress-bar progress-bar-success"></div>
                                        </div>
                                        <div class="uploaded-file"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if(empty($securityCheckPhotoTypes)): ?>
                                <p>No security check photo available, <a href="<?= site_url('security-photo-type') ?>">setup here</a>.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if(isset($safeConduct)): ?>
                    <?php if((is_null($safeConduct['security_in_date']))): ?>
                     <input type="hidden" name="security_type" value="CHECK IN">
                     <?php else: ?>
                        <?php if((!is_null($safeConduct['security_in_date']))):?>
                            <input type="hidden" name="security_type" value="CHECK OUT"> 
                        <?php endif; ?>            
                    <?php endif;?>
                    <input type="hidden" name="id_safe_conduct" value="<?= $safeConduct['id'] ?>">
                    <input type="hidden" name="id_safe_conduct_container" id="id_goods">
                    <input type="hidden" name="no_container" id="no_container">
                <?php endif;?>

                <?php if(isset($tep)): ?>
                    <?php if((is_null($tep['checked_in_at']))): ?>
                     <input type="hidden" name="security_type" value="CHECK IN">
                     <?php else: ?>
                        <?php if((!is_null($tep['checked_in_at']))):?>
                            <input type="hidden" name="security_type" value="CHECK OUT"> 
                        <?php endif; ?>            
                    <?php endif;?>
                    <input type="hidden" name="id_tep" value="<?= $tep['id'] ?>">
                    <input type="hidden" name="id_tep_container" id="id_goods">
                    <input type="hidden" name="no_container" id="no_container">
                <?php endif;?>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" data-toggle="one-touch" class="btn btn-danger btn-save-goods">Save</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php else: ?>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-checklist-container">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Security Checklist</h4>
                </div>
                <div class="modal-body">
                    <h4 class="text-warning" style="text-align: center;">Please Contact Admin !</strong></h4>
                </div>
               
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>
