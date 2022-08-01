<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Component Prices</h3>
        <div class="pull-right">
            <form class="mr5 hidden-xs" action="<?= site_url('component_price/index') ?>"
                  id="form-component-price-filter" style="display: inline-block">
                <?php
                $filterCustomer = get_url_param('customer');
                ?>
                <select class="select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-add-empty-value="ALL CUSTOMER" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customer" id="customer" style="min-width: 200px">
                    <option value="">ALL CUSTOMER</option>
                    <?php if(!empty($customer)): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
            </form>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPONENT_PRICE_CREATE)): ?>
                <a href="<?= site_url('component_price/create') ?>" class="btn btn-primary">
                    Create Prices
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive table-ajax" id="table-component-price">
            <thead>
            <tr>
                <th style="width: 30px">ID</th>
                <th>Customer</th>
                <th>Type</th>
                <th>Subtype</th>
                <th>Handling</th>
                <th>Component</th>
                <th>Rules</th>
                <th>Price</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPONENT_PRICE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPONENT_PRICE_VALIDATE)): ?>
    <?php $this->load->view('template/_modal_validate'); ?>
<?php endif; ?>

<script id="control-invoice-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPONENT_PRICE_VIEW)): ?>
                <li>
                    <a href="<?= site_url('component_price/view/{{id}}') ?>">
                        <i class="fa ion-ios-search"></i>View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPONENT_PRICE_EDIT)): ?>
                <li>
                    <a href="<?= site_url('component_price/edit/{{id}}') ?>">
                        <i class="fa ion-ios-compose-outline"></i>Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPONENT_PRICE_VALIDATE)): ?>
                <li>
                    <a href="<?= site_url('component-price/validate-price/approve/{{id}}') ?>"
                       class="btn-validate"
                       data-validate="approve"
                       data-label="{{price_label}}">
                        <i class="fa ion-checkmark"></i> Approve
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('component-price/validate-price/reject/{{id}}') ?>"
                       class="btn-validate"
                       data-validate="reject"
                       data-label="{{price_label}}">
                        <i class="fa ion-close"></i> Reject
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_COMPONENT_PRICE_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('component_price/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Component Price"
                       data-label="{{price_label}}">
                        <i class="fa ion-ios-trash-outline"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/component_price.js?v=1') ?>" defer></script>