<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Goods</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_GOODS_CREATE)): ?>
                <a href="<?= site_url('goods/create') ?>" class="btn btn-primary">
                    Create Item
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-goods">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th class="type-customer">Customer</th>
                <th>No Goods</th>
                <th>HS Code</th>
                <th>Whey Number</th>
                <th class="type-goods-name">Goods Name</th>
                <th class="type-assembly-goods">Assembly Goods</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-goods-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_GOODS_VIEW)): ?>
                <li>
                    <a href="<?= site_url('goods/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_GOODS_EDIT)): ?>
                <li>
                    <a href="<?= site_url('goods/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_GOODS_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('goods/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-id="{{id}}"
                       data-title="Goods"
                       data-label="{{goods}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_GOODS_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/goods.js?v=2') ?>" defer></script>
