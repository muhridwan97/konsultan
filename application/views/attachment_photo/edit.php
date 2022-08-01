<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Attachment Photo</h3>
    </div>
    <form action="<?= site_url('attachment-photo/update/' . $attachmentPhoto['id']) ?>" class="need-validation" role="form" method="post"
          id="form-attachment-photo">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('photo_name') == '' ?: 'has-error'; ?>">
                <label for="photo_name">Photo Name</label>
                <input type="text" class="form-control" id="photo_name" name="photo_name"
                        placeholder="Enter Photo name"
                        required maxlength="100" value="<?= set_value('photo_name', $attachmentPhoto['photo_name']) ?>">
                <?= form_error('photo_name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Attachment Photo Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Attachment Photo description"
                          required maxlength="500"><?= set_value('description', $attachmentPhoto['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Update Attachment Photo
            </button>
        </div>
    </form>
</div>
