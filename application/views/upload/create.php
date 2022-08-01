<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Upload</h3>
    </div>
    <form id="form-upload" action="<?= site_url('upload/save') ?>" role="form" method="post" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach (get_customer_branch() as $branch): ?>
                            <option value="<?= $branch['id'] ?>" <?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif ?>

            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer / Supplier</label>
                <?php
                $userType = UserModel::authenticatedUserData('user_type');
                $idPerson = UserModel::authenticatedUserData('id_person');
                ?>
                <?php if($userType == 'INTERNAL'): ?>
                    <select class="form-control select2 customer" name="customer" id="customer"
                            data-placeholder="Select customer or supplier" style="width: 100%" required>
                        <option value=""></option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>">
                                <?= $customer['name'] ?> - <?= $customer['no_person'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p class="form-control-static"><?= UserModel::authenticatedUserData('name') ?></p>
                    <input type="hidden" name="customer" id="customer" value="<?= $idPerson ?>">
                <?php endif ?>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Upload title</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Upload title description" rows="1"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                <label for="type">Upload Type</label>
                <select class="form-control select2 upload-booking-type" id="type" name="type" required style="width: 100%" data-placeholder="Select Upload Type">
                    <option value=""></option>
                    <?php foreach ($types as $type) : ?>
                        <option value="<?= $type['id'] ?>" <?php echo set_select('type', $type['id']); ?> data-category="<?= $type['category'] ?>" data-type="<?= $type['type'] ?>">
                            <?= $type['booking_type'] ?> (<?= $type['category'] ?> - <?= $type['type'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('type', '<span class="help-block">', '</span>'); ?>
            </div>

            <div id="upload-in-wrapper" style="display: none">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="form-group <?= form_error('upload_in') == '' ?: 'has-error'; ?>">
                            <label for="upload_in">Upload In</label>
                            <select class="form-control select2" name="upload_in" id="upload_in" style="width: 100%"
                                    data-placeholder="Select related upload in" required disabled>
                                <option value=""></option>
                            </select>
                            <?= form_error('upload_in', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('is_hold') == '' ?: 'has-error'; ?>">
                <label for="hold">Hold Immediately</label>
                <div class="row">
                    <div class="col-sm-3 col-md-1">
                        <label for="visible">
                            <input type="radio" id="hold" name="is_hold" value="1"<?= set_radio('is_hold', 1) ?>> Yes
                        </label>
                    </div>
                    <div class="col-sm-4 col-md-1">
                        <label for="not-hold">
                            <input type="radio" id="not-hold" name="is_hold" value="0" <?= set_radio('is_hold', 0, true) ?>> No
                        </label>
                    </div>
                </div>
                <?= form_error('is_hold', '<span class="help-block">', '</span>'); ?>
            </div>

            <div id="field-hold-description" class="form-group <?= form_error('hold_description') == '' ?: 'has-error'; ?>" <?= set_value('is_hold') ? '' : 'style="display: none"' ?>>
                <label for="hold_description">Hold Description</label>
                <textarea class="form-control" id="hold_description" name="hold_description"
                          placeholder="Hold description" rows="2"
                          maxlength="500"><?= set_value('hold_description') ?></textarea>
                <?= form_error('hold_description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-warning" style="display: none" id="upload-photo-wrapper"> 
                <div class="box-header">
                    <h4 class="box-title">Photo Item</h4>
                </div>
                <div class="box-body" id="photo-wrapper">
            <?php if(set_value('photos')): ?>
                <?php foreach(set_value('photos', []) as $index => $photo): ?>
                    <div class="panel panel-warning card-photo">
                        <div class="panel-heading">
                            Photo <?= ($index+1) ?>
                            <?php if ($index>0) :?>
                            <a class="btn-remove-photo pull-right">
                                <i class="fa fa-remove"></i>
                            </a>
                            <?php endif; ?>
                            <span class="pull-right">Optional&nbsp</span>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="item_name_<?= $index ?>" class="control-label">
                                            Item Name
                                        </label>
                                        <input type="text" class="form-control"
                                            placeholder="Enter Item Name"
                                            id="item_name_<?= $index ?>" name="photos[<?= $index ?>][item_name]">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="no_hs_<?= $index ?>" class="control-label">
                                            No HS
                                        </label>
                                        <input type="text" class="form-control"
                                            placeholder="Enter HS Number"
                                            id="no_hs_<?= $index ?>" name="photos[<?= $index ?>][no_hs]">
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
            <?php else: ?>
                    <div class="panel panel-warning card-photo">
                        <div class="panel-heading">
                            Photo 1
                            <span class="pull-right">Optional</span>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="item_name_0" class="control-label">
                                            Item Name
                                        </label>
                                        <input type="text" class="form-control"
                                            placeholder="Enter Item Name"
                                            id="item_name_0" name="photos[0][item_name]">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="no_hs_0" class="control-label">
                                            No HS
                                        </label>
                                        <input type="text" class="form-control"
                                            placeholder="Enter HS Number"
                                            id="no_hs_0" name="photos[0][no_hs]">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="file_photo_0">
                                            File Photos <span style="color:#a9a9a9">(Upload max 3 MB)</span>
                                        </label>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="btn btn-primary btn-block fileinput-button">
                                                    <span class="button-file">Select Photo</span>
                                                    <input class="upload-photo" id="file_photo_0" type="file" accept="image/*" name="file_photo_0">
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
                <?php endif; ?> 
                </div>
                <div class="box-footer">
                    <div class="text-right">
                        <button class="btn btn-sm btn-info" id="btn-add-photo" type="button">ADD PHOTO</button>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Document Upload</h4>
                </div>
                <div class="box-body" id="form-upload-wrapper">
                    <p class="text-muted">Select <a href="#type">booking type</a> to upload document.</p>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right btn-upload">Save Upload</button>
        </div>
    </form>
</div>
<!-- /.box -->
<script id="photo-template" type="text/x-custom-template">
    <div class="panel panel-warning card-photo required-document">
        <div class="panel-heading">
            Photo {{no}}
            <a class="btn-remove-photo pull-right">
                <i class="fa fa-remove"></i>
            </a>
            <span class="pull-right">Optional&nbsp</span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="item_name_{{index}}" class="control-label">
                            Item Name
                        </label>
                        <input type="text" class="form-control" required
                            placeholder="Enter Item Name"
                            id="item_name_{{index}}" name="photos[{{index}}][item_name]">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="no_hs_{{index}}" class="control-label">
                            No HS
                        </label>
                        <input type="text" class="form-control" required
                            placeholder="Enter HS Number"
                            id="no_hs_{{index}}" name="photos[{{index}}][no_hs]">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="file_photo_{{index}}">
                            File Photos <span style="color:#a9a9a9">(Upload max 3 MB)</span>
                        </label>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="btn btn-primary btn-block fileinput-button">
                                    <span class="button-file">Select Photo</span>
                                    <input class="upload-photo" id="file_photo_{{index}}" type="file" accept="image/*" name="file_photo_{{index}}">
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
</script>

<script src="<?= base_url('assets/app/js/upload.js?v=13') ?>" defer></script>
<script src="<?= base_url('assets/app/js/upload-item-photo.js?v=1') ?>" defer></script>