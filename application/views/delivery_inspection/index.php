<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Delivery Inspection Data</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('delivery_inspection/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-delivery-inspection">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Date</th>
                <th>Location</th>
                <th>PIC TCI</th>
                <th>PIC Khaisan</th>
                <th>PIC SMGP</th>
                <th class="type-total-vehicle">Total Vehicle</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_INSPECTION_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-delivery-inspection-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_INSPECTION_VIEW)): ?>
                <li>
                    <a href="<?= site_url('delivery-inspection/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_INSPECTION_EDIT)): ?>
                <li class="edit">
                    <a href="<?= site_url('delivery-inspection/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_INSPECTION_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('delivery-inspection/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Delivery Inspection"
                       data-label="{{date}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/delivery-inspection.js') ?>" defer></script>