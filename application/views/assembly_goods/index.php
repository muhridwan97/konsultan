<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Assembly Goods</h3>

        <?php if (AuthorizationModel::isAuthorized(PERMISSION_ASSEMBLY_GOODS_CREATE)): ?>
            <a href="<?= site_url('assembly_goods/create') ?>" class="btn btn-primary pull-right">
                Create Item
            </a>
        <?php endif; ?>

    </div>
    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-assembly-goods">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Goods Name</th>
                <th>Quantity Goods</th>
                <th>Assembly Goods Name</th>
                <th>No Assembly Goods</th>
                <th>Quantity Assembly</th>
                <th>Created At</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-assembly-goods-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ASSEMBLY_GOODS_VIEW)): ?>
                <li>
                    <a href="<?= site_url('assembly_goods/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ASSEMBLY_GOODS_EDIT)): ?>
                <li>
                    <a href="<?= site_url('assembly_goods/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ASSEMBLY_GOODS_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('assembly_goods/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-id="{{id}}"
                       data-title="Goods">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_ASSEMBLY_GOODS_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/goods_assembly.js') ?>"></script>
