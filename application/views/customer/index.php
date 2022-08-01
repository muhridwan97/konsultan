<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Customer</h3>

        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if ( AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_CREATE)): ?>
                <a href="<?= site_url('customer/create') ?>" class="btn btn-primary">
                    Create Customer
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-customer">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Identity Number</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-customer-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_VIEW)): ?>
                <li>
                    <a href="<?= site_url('customer/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $editCustomer = AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_EDIT);
            $allowEdit =  $editCustomer ;
            ?>
            <?php if ($allowEdit): ?>
                <li class="action-edit" data-edit-customer="<?= $editCustomer ?>">
                    <a href="<?= site_url('customer/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $deleteCustomer = AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_DELETE);
            $allowDelete = $deleteCustomer;
            ?>
            <?php if ($allowDelete): ?>
                <li role="separator" class="divider action-delete-divider"></li>
                <li class="action-delete" data-delete-customer="<?= $deleteCustomer ?>">
                    <a href="<?= site_url('customer/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-id="{{id}}"
                       data-title="Person"
                       data-label="{{person}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/customer.js?v=3') ?>" defer></script>