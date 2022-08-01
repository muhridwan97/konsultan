<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Job Document</h3>
    </div>

    <form action="<?= site_url('work-order-document/save') ?>" role="form" method="post" id="form-work-order-document">
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
                            <option value="<?= $branch['id'] ?>"<?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('auction_date') == '' ?: 'has-error'; ?>">
                <label for="date">Document Date</label>
                <input type="text" class="form-control datepicker" id="date" name="date"
                       placeholder="Document date" required
                       value="<?= set_value('date') ?>">
                <?= form_error('date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Job Data</h3>
                </div>
                <div class="box-body" id="job-list-wrapper">
                    Select job date above
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Upload Files</h3>
                </div>
                <div class="box-body">
                    <div id="document-uploader">

                        <div class="btn btn-primary btn-lg fileinput-button mb20">
                            <span class="button-file"><i class="ion-upload mr10"></i>Select file</span>
                            <input id="input-file" type="file" multiple name="input_files">
                        </div>

                        <div id="uploaded-input-wrapper">
                            <?php $files = set_value('input_files_uploaded', []); ?>
                            <?php $files = is_array($files) ? $files : [] ?>
                            <?php foreach ($files as $file): ?>
                                <input type="hidden" name="input_files_uploaded[]" value="<?= $file ?>">
                            <?php endforeach; ?>
                        </div>

                        <div id="uploaded-file">
                            <?php foreach ($files as $file): ?>
                                <div class="uploaded-item">
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div id="progress" class="progress progress-upload">
                                                <div class="progress-bar progress-bar-success" style="width: 100%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="#" data-file="<?= $file ?>" class="btn btn-danger btn-sm btn-delete-file mr10">
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
                </div>

                <div class="box-footer">
                    <i class="ion-information-circled mr10"></i>Each individual file should not be greater than 2MB
                </div>
            </div>

        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Save Documents
            </button>
        </div>
    </form>
</div>

<script id="upload-item-template" type="text/x-custom-template">
    <div class="uploaded-item">
        <div class="row">
            <div class="col-md-11">
                <div id="progress" class="progress progress-upload">
                    <div class="progress-bar progress-bar-success"></div>
                </div>
            </div>
            <div class="col-md-1">
                <a href="#" data-file="" class="btn btn-danger btn-sm btn-delete-file mr10">
                    DELETE
                </a>
            </div>
        </div>
        <p class="text-ellipsis mb0 upload-file-name">
            Uploading...
        </p>
    </div>
</script>

<script src="<?= base_url('assets/app/js/work-order-document.js') ?>" defer></script>