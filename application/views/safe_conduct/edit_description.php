<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Safe Conduct - <?= $safeConduct['no_safe_conduct'] ?></h3>
    </div>

    <form action="<?= site_url('safe-conduct/update_description/' . $safeConduct['id']) ?>" role="form" method="post"
          id="form-safe-conduct" class="edit" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <input type="hidden" name="id" id="id" value="<?= $safeConduct['id'] ?>">

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Safe conduct description"
                          rows="1" maxlength="500"><?= set_value('description', $safeConduct['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right" id="btn-save-safe-conduct">
                Update Safe Conduct
            </button>
        </div>
    </form>
</div>
