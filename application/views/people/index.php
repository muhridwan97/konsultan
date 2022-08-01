<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">People</h3>

        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_CREATE)
            || AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_CREATE)
            || AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_CREATE)): ?>
                <a href="<?= site_url('people/create') ?>" class="btn btn-primary">
                    Create Person
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-people">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Type</th>
                <th>No Person</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>WhatsApp Group</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-people-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_VIEW)): ?>
                <li>
                    <a href="<?= site_url('people/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $editPerson = AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_EDIT);
            $editCustomer = AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_EDIT);
            $editSupplier = AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_EDIT);
            $allowEdit = $editPerson || $editCustomer || $editSupplier;
            ?>
            <?php if ($allowEdit): ?>
                <li class="action-edit" data-edit-person="<?= $editPerson ?>" data-edit-customer="<?= $editCustomer ?>" data-edit-supplier="<?= $editSupplier ?>">
                    <a href="<?= site_url('people/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($allowEdit): ?>
                <li>
                    <a href="<?= site_url('people_contact/create/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Add contact
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_EDIT_NOTIFICATION)): ?>
                <li>
                    <a href="<?= site_url('people/edit-notification/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Setup notification
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $deletePerson = AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_DELETE);
            $deleteCustomer = AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_DELETE);
            $deleteSupplier = AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_DELETE);
            $allowDelete = $deletePerson || $deleteCustomer || $deleteSupplier;
            ?>
            <?php if ($allowDelete): ?>
                <li role="separator" class="divider action-delete-divider"></li>
                <li class="action-delete" data-delete-person="<?= $deletePerson ?>" data-delete-customer="<?= $deleteCustomer ?>" data-delete-supplier="<?= $deleteSupplier ?>">
                    <a href="<?= site_url('people/delete/{{id}}') ?>"
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/people.js?v=3') ?>" defer></script>