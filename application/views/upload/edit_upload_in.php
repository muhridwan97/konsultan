<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Upload In</h3>
    </div>
    <form action="<?= site_url('upload/update-upload-in/'.$upload['id']) ?>" role="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">No Upload</label>
                        <p class="form-control-static">
                            <a href="<?= site_url('upload/view/'. $upload['id']) ?>"><?= $upload['no_upload'] ?></a>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Booking Type</label>
                        <p class="form-control-static"><?= $upload['booking_type'] ?></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Upload Description</label>
                        <p class="form-control-static"><?= $upload['description'] ?></p>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    <?php if($bookingType['type'] == BookingTypeModel::TYPE_EXPORT): ?>
                        <div class="form-group <?= form_error('upload_in') == '' ?: 'has-error'; ?>">
                            <label for="upload_in">Upload In</label>
                            <select class="form-control select2" name="upload_in[]" id="upload_in" style="width: 100%"
                                    data-placeholder="Select related upload in" multiple required>
                                <option value=""></option>
                                <?php foreach ($uploadIn as $key => $upload_reference): ?>
                                    <option value="<?= $upload_reference['id'] ?>" <?= set_select('upload_in[]', $upload_reference['id'], array_search($upload_reference['id'], array_column($uploadReferences, 'id_upload_reference')) !== false) ?>><?= $upload_reference['no_upload'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('upload_in', '<span class="help-block">', '</span>'); ?>
                        </div>
                    <?php else: ?>
                        <div class="form-group <?= form_error('upload_in') == '' ?: 'has-error'; ?>">
                            <label for="upload_in">Upload In</label>
                            <select class="form-control select2" name="upload_in" id="upload_in" style="width: 100%"
                                    data-placeholder="Select related upload in" required>
                                <option value=""></option>
                                <?php foreach ($uploadIn as $key => $upload_reference): ?>
                                    <option value="<?= $upload_reference['id'] ?>" <?= set_select('upload_in', $upload_reference['id'], $upload_reference['id'] == $upload['id_upload']) ?>><?= $upload_reference['no_upload'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('upload_in', '<span class="help-block">', '</span>'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-warning pull-right">Update Upload In</button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/upload.js?v=13') ?>" defer></script>