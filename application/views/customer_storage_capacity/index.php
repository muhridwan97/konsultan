<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Customer Storages</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_CREATE)): ?>
                <a href="<?= site_url('customer-storage-capacity/create') ?>" class="btn btn-primary">
                    Create
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('customer_storage_capacity/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-customer-storage-capacity">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Customer</th>
                <th>Effective Date</th>
                <th>Expired Date</th>
                <th>Warehouse Storage</th>
                <th>Yard Storage</th>
                <th>Covered Yard Storage</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-customer-storage-capacity-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_VIEW)): ?>
                <li>
                    <a href="<?= site_url('customer-storage-capacity/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_EDIT)): ?>
                <li class="action-edit">
                    <a href="<?= site_url('customer-storage-capacity/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_DELETE)): ?>
                <li role="separator" class="divider action-delete"></li>
                <li>
                    <a href="<?= site_url('customer-storage-capacity/delete/{{id}}') ?>"
                       class="btn-delete action-delete"
                       data-title="Customer Storage Capacity"
                       data-label="{{customer_name}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/customer-storage-capacity.js') ?>" defer></script>