<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Target</h3>
    </div>
    <form action="<?= site_url('target/save/') ?>" class="form" method="post" id="form-target-branch">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('target_name') == '' ?: 'has-error'; ?>">
                <label for="target_name">Target Name</label>
                <input type="text" class="form-control" id="target_name" name="target_name"
                       placeholder="Enter target name"
                       value="<?= set_value('target_name') ?>">
                <?= form_error('target_name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <input type="text" class="form-control" id="description" name="description"
                       placeholder="Enter description"
                       value="<?= set_value('description') ?>">
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('target') == '' ?: 'has-error'; ?>">
                <label for="target">Target all branch</label>
                <input type="number" class="form-control" id="target" name="target"
                       placeholder="Enter target"
                       value="<?= set_value('target') ?>">
                <?= form_error('target', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Target Per Branch</h3>
                </div>
                <div class="box-body">
                    <table class="table no-datatable" id="table-detail-target-branch">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th style="width: 250px">Branch</th>
                            <th style="width: 150px">Target</th>
                            <th>Description</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center">
                                    Click <strong>Add New Branch Target</strong> to insert new record
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-block btn-primary mt10" id="btn-add-target-branch">
                        ADD NEW BRANCH TARGET
                    </button>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save target</button>
        </div>
    </form>
</div>

<?php $this->load->view('target/_target_branch') ?>
<script src="<?= base_url('assets/app/js/target_branch.js') ?>" defer></script>