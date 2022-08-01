<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Handling Type</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('handling_type/save') ?>" role="form" method="post" id="form-handling-type">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('handling_type') == '' ?: 'has-error'; ?>">
                        <label for="handling_type">Handling Type</label>
                        <input type="text" class="form-control" id="handling_type" name="handling_type"
                               placeholder="Enter handling name"
                               required maxlength="50" value="<?= set_value('handling_type') ?>">
                        <span class="help-block">Unique handling name.</span>
                        <?= form_error('handling_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('handling_code') == '' ?: 'has-error'; ?>">
                        <label for="handling_code">Handling Code</label>
                        <input type="text" class="form-control" id="handling_code" name="handling_code"
                               placeholder="Put handling code"
                               required maxlength="2" value="<?= set_value('handling_code') ?>">
                        <span class="help-block">Must be 2 capital alpha code for handling type.</span>
                        <?= form_error('handling_code', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                        <label for="category">Category</label>
                        <select class="form-control select2" name="category" id="category"
                                data-placeholder="Select handling category" required>
                            <option value=""></option>
                            <option value="<?= HandlingTypeModel::CATEGORY_WAREHOUSE ?>"
                                <?= set_select('category', HandlingTypeModel::CATEGORY_WAREHOUSE) ?>>
                                <?= HandlingTypeModel::CATEGORY_WAREHOUSE ?>
                            </option>
                            <option value="<?= HandlingTypeModel::CATEGORY_NON_WAREHOUSE ?>"
                                <?= set_select('category', HandlingTypeModel::CATEGORY_NON_WAREHOUSE) ?>>
                                <?= HandlingTypeModel::CATEGORY_NON_WAREHOUSE ?>
                            </option>
                        </select>
                        <?= form_error('category', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('duration') == '' ?: 'has-error'; ?>">
                        <label for="duration">Duration Minimum (Minutes)</label>
                        <input type="number" name="duration" min="0" class="form-control" required placeholder="Enter duration of handling type"></input>
                        <?= form_error('duration', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
                    
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('container_multiplier') == '' ?: 'has-error'; ?>">
                        <label for="container_multiplier">Container Multiplier</label>
                        <select class="form-control select2" name="container_multiplier" id="container_multiplier"
                                data-placeholder="Select container multiplier" required>
                            <option value=""></option>
                            <option value="-1"
                                <?= set_select('container_multiplier', -1) ?>>
                                SUBTRACTION (-)
                            </option>
                            <option value="0"
                                <?= set_select('container_multiplier', 0, true) ?>>
                                NOT AFFECTING (-/+)
                            </option>
                            <option value="1"
                                <?= set_select('container_multiplier', 1) ?>>
                                ADDERS (+)
                            </option>
                        </select>
                        <?= form_error('container_multiplier', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('goods_multiplier') == '' ?: 'has-error'; ?>">
                        <label for="goods_multiplier">Goods Multiplier</label>
                        <select class="form-control select2" name="goods_multiplier" id="goods_multiplier"
                                data-placeholder="Select goods multiplier" required>
                            <option value=""></option>
                            <option value="-1"
                                <?= set_select('goods_multiplier', -1) ?>>
                                SUBTRACTION (-)
                            </option>
                            <option value="0"
                                <?= set_select('goods_multiplier', 0, true) ?>>
                                NOT AFFECTING (-/+)
                            </option>
                            <option value="1"
                                <?= set_select('goods_multiplier', 1) ?>>
                                ADDERS (+)
                            </option>
                        </select>
                        <?= form_error('goods_multiplier', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Handling Type Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Handling type description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('handling_type') == '' ?: 'has-error'; ?>">
                <label for="withPhoto">With Photo</label>
                <div class="checkbox icheck" style="margin-top: 0">
                    <label>
                    <input type="checkbox" name="photo" id="photo" value="1">
                        With Photo
                    </label>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Attachment Photo</h3>
                </div>
                <div class="box-body">
                    <table class="table no-datatable" id="table-detail-attachment-photo">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th style="width: 250px">Photo Name</th>
                            <th style="width: 150px">Condition</th>
                            <th>Description</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="5" class="text-center">
                                Click <strong>Add Attachment Photo</strong> to insert new record
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-block btn-primary mt10" id="btn-add-photo">
                        ADD NEW ATTACHMENT PHOTO
                    </button>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Handling Component</h3>
                </div>
                <div class="box-body">
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
                    <?php endif; ?>

                    <table class="table no-datatable" id="table-detail-handling-component">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th style="width: 250px">Component</th>
                            <th style="width: 150px">Default</th>
                            <th>Description</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="5" class="text-center">
                                Click <strong>Add New Component</strong> to insert new record
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-block btn-primary mt10" id="btn-add-component">
                        ADD NEW SUB HANDLING COMPONENT
                    </button>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right" id="btn-save-handling-type">
                Save Handling Type
            </button>
        </div>
    </form>
</div>
<!-- /.box -->

<?php $this->load->view('handling_type/_handling_component') ?>
<?php $this->load->view('handling_type/_handling_photo') ?>

<script src="<?= base_url('assets/app/js/handling_type.js') ?>" defer></script>