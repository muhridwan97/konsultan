<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Complain Conclusion</h3>
    </div>
    <form action="<?= site_url('complain/update-conclusion-category/' . $complain['id']) ?>" class="need-validation" method="post" enctype="multipart/form-data">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="no_complain">No Complain</label>
                        <p class="form-control-static"><?= $complain['no_complain'] ?></p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="customer">Customer</label>
                        <p class="form-control-static"><?= $complain['customer_name'] ?></p>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                <label for="conclusion_category">Conclusion Category</label>
                <select class="form-control select2" name="conclusion_category" id="conclusion_category"
                        data-placeholder="Select Conclusion Category" required style="width: 100%">
                    <option value=""></option>
                    <?php foreach($conclusions AS $conclusion): ?>
                        <option value="<?= $conclusion['id'] ?>"<?= set_select('conclusion_category', $conclusion['id'], $conclusion['id'] == $complain['id_conclusion_category']) ?>>
                            <?= $conclusion['category'] ?> (<?= $conclusion['value_type'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('conclusion_category', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Enter description"><?= set_value('description') ?></textarea>
            </div>
            <div class="form-group">
                <label for="attachment">Attachment</label>
                <input type="file" id="attachment" name="attachment" class="file-upload-default" data-max-size="3000000" >
                <div class="input-group col-xs-12">
                    <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" readonly placeholder="Upload attachment">
                    <span class="input-group-btn">
                        <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">Upload</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Final Conclusion</button>
        </div>
    </form>
</div>