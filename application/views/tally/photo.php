<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Upload Photo</h3>
    </div>

    <form action="<?= site_url('work-order-goods-photo/save/' . $workOrderGoods['id']) ?>" role="form" method="post">
        <div class="box-body">
            <div class="form-horizontal form-view">
                <div class="form-group">
                    <label class="col-sm-3">No Work Order</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?= $workOrderGoods['no_work_order'] ?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3">No Goods</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?= $workOrderGoods['no_goods'] ?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3">Goods Name</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?= $workOrderGoods['goods_name'] ?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3">Description</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?= $workOrderGoods['description'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_EDIT)) : ?>
                <?php if (empty($workOrderStatuses)) : ?>
                    <div class="box box-success" id="document-uploader">
                        <div class="box-header">
                            <h3 class="box-title">Add Files</h3>
                            <div class="btn btn-primary fileinput-button pull-right">
                                <span class="button-file"><i class="ion-upload mr10"></i>Select file</span>
                                <input id="input-file" type="file" multiple name="input_files" accept="image/png,image/jpeg,image/gif">
                            </div>
                        </div>
                        <div class="box-body">
                            <?php $this->load->view('template/_alert') ?>

                            <div class="box box-primary">
                                <div class="box-body">
                                    <div id="uploaded-input-wrapper">
                                        <?php $files = set_value('input_files_uploaded', []); ?>
                                        <?php $files = is_array($files) ? $files : [] ?>
                                        <?php foreach ($files as $file) : ?>
                                            <input type="hidden" name="input_files_uploaded[]" value="<?= $file ?>">
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="row" id="uploaded-file">
                                        <?php foreach ($files as $file) : ?>
                                            <div class="col-md-6 uploaded-item">
                                                <div style="display: flex">
                                                    <div>
                                                        <a href="<?= base_url('uploads/' . $file) ?>" class="upload-file-preview-link" target="_blank">
                                                            <img src="<?= base_url('uploads/' . $file) ?>" class="upload-file-preview img-responsive" style="margin: auto; height: 100px;">
                                                        </a>
                                                    </div>
                                                    <div style="display: flex; align-items: center; flex-grow: 1; margin: 10px;">
                                                        <div id="progress" class="progress progress-upload" style="width: 100%">
                                                            <div class="progress-bar progress-bar-success progress-bar-striped" style="width: 100%"></div>
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; min-height: 100px; align-items: center;">
                                                        <a href="#" data-file="<?= $file ?>" class="btn btn-danger btn-sm btn-delete-file mr20">
                                                            DELETE
                                                        </a>
                                                    </div>
                                                </div>
                                                <p class="text-ellipsis mb0 upload-file-name">
                                                    <?= $file ?>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <i class="ion-information-circled mr10"></i>Each individual image should not be greater than 10MB
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="box box-danger" id="uploaded-old-file">
                <div class="box-header">
                    <h3 class="box-title">Uploaded Files</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <?php foreach ($workOrderGoodsPhotos as $file) : ?>
                            <div class="uploaded-item col-sm-6 col-md-4 text-center" style="padding-bottom: 10px; margin: 10px 0 10px; border-bottom: 1px solid #dfdfdf;">
                                <div class="mb10">
                                    <a href="<?= asset_url($file['src']) ?>" target="_blank">
                                        <!-- <img src="<?= if_empty($file['url'], base_url('uploads/' . $file['src'])) ?>" class="img-responsive" style="margin: auto; height: 100px;"> -->
                                        <img src="<?= asset_url($file['src']) ?>" class="img-responsive" style="margin: auto; height: 100px;">
                                    </a>
                                    <p class="mt10 mb0"><?= basename($file['src']) ?></p>
                                    <p class="text-muted"><?= if_empty($file['description'], 'No description') ?></p>
                                </div>
                                <?php if (empty($workOrderStatuses)) : ?>
                                    <a href="<?= site_url('work-order-goods-photo/delete/' . $file['id']) ?>" data-file="<?= basename($file['src']) ?>" class="btn btn-delete-uploaded-file btn-danger btn-sm">
                                        DELETE
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($workOrderGoodsPhotos)) : ?>
                        <p class="text-muted">No photo available for this work order item.</p>
                    <?php endif; ?>
                </div>
                <?php if (empty($workOrderStatuses)) : ?>
                    <div class="box-footer">
                        <i class="ion-alert-circled mr10"></i>Delete these file will be affecting immediately
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <?php if (empty($workOrderStatuses)) : ?>
                <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                    Save Photos
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php $this->load->view('template/_modal_confirm'); ?>

<script id="upload-item-template" type="text/x-custom-template">
    <div class="col-md-6 uploaded-item">
        <div style="display: flex">
            <div>
                <a href="#" class="upload-file-preview-link" target="_blank">
                    <img src="" class="upload-file-preview img-responsive" style="margin: auto; height: 100px;">
                </a>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; flex-grow: 1; margin: 10px;">
                <div id="progress" class="progress progress-upload" style="width: 100%">
                    <div class="progress-bar progress-bar-danger progress-bar-success progress-bar-striped"></div>
                </div>
                <textarea name="photo_description[]" rows="1" class="form-control" placeholder="Photo description"></textarea>
            </div>
            <div style="display: flex; min-height: 100px; align-items: center;">
                <a href="#" data-file="" class="btn btn-warning btn-sm btn-delete-file mr20">
                    CANCEL
                </a>
            </div>
        </div>
        <p class="text-ellipsis mb0 upload-file-name" style="padding: 5px 0">
            Uploading...
        </p>
    </div>
</script>

<script src="<?= base_url('assets/app/js/upload-photo.js?v=3') ?>" defer></script>