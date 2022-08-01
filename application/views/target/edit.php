<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Target</h3>
    </div>
    <form action="<?= site_url('target/update/' . $target['id']) ?>" class="form" method="post" id="form-target-branch">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('target_name') == '' ?: 'has-error'; ?>">
                <label for="target_name">Target Name</label>
                <input type="text" class="form-control" id="target_name" name="target_name"
                       placeholder="Enter target name"
                       value="<?= set_value('target_name', $target['target_name']) ?>">
                <?= form_error('target_name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <input type="text" class="form-control" id="description" name="description"
                       placeholder="Enter description"
                       value="<?= set_value('description', $target['description']) ?>">
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('target') == '' ?: 'has-error'; ?>">
                <label for="target">Target all branch</label>
                <input type="number" class="form-control" id="target" name="target"
                       placeholder="Enter target"
                       value="<?= set_value('target', $target['target']) ?>">
                <?= form_error('target', '<span class="help-block">', '</span>'); ?>
            </div>
            <!-- <div class="form-group <?= form_error('target_type') == '' ?: 'has-error'; ?>">
                <label for="target_type">Target Type</label>
                <select class="form-control select2" name="target_type" id="target_type" data-placeholder="Select target type" required>
                    <option value=""></option>
                    <?php foreach ($target_types as $target_type): ?>
                        <option value="<?= $target_type['id'] ?>" <?= set_value('target_type', $target['id_target_type']) == $target_type['id'] ? 'selected' : '' ?>>
                            <?= $target_type['target_type'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('target_type', '<span class="help-block">', '</span>'); ?>
            </div> -->
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
                        <?php $no = 1;
                        foreach ($targetBranches as $targetBranch): ?>
                            <tr class="row-target-branch">
                                <td><?= $no++ ?></td>
                                <td>
                                    <select class="form-control select2" name="branches[]" id="branch" data-placeholder="Select branch" required>
                                        <option value=""></option>
                                        <?php foreach ($branchVmses as $branch): ?>
                                            <option value="<?= $branch['id'] ?>" <?= $branch['id'] == $targetBranch['id_branch'] ? 'selected' : '' ?>>
                                                <?= $branch['branch'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" min="0" step="any" class="form-control" name="target_branches[]" id="target_branch"
                                           value="<?= $targetBranch['target'] ?>"
                                           placeholder="Target Branch">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="descriptions[]" id="detail_description"
                                           value="<?= $targetBranch['description'] ?>"
                                           placeholder="Description">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-target-branch">
                                        <i class="ion-trash-b"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!count($targetBranches)): ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    Click <strong>Add New Branch Target</strong> to insert new record
                                </td>
                            </tr>
                        <?php endif; ?>
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
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update target</button>
        </div>
    </form>
</div>

<?php $this->load->view('target/_target_branch') ?>
<script src="<?= base_url('assets/app/js/target_branch.js') ?>" defer></script>