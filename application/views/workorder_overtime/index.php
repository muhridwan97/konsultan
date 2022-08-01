<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Work Order Overtime Data</h3>
        <div class="pull-right">
            <a href="#form-filter-work-order-overtime" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter_work_order_overtime', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('workorder_overtime/_filter', ['hidden' => isset($_GET['filter_work_order_overtime']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-work-order-overtime">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Work Order</th>
                <th>Customer</th>
                <th class="type-date-time">Completed At</th>
                <th class="type-hour">Service Time End</th>
                <th class="type-overtime">Overtime</th>
                <th class="type-status">Charged To</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-work-order-overtime-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATE_OVERTIME)): ?>
                <li>
                    <a href="<?= site_url('work-order-overtime/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order-overtime/validate-overtime/{{id}}') ?>">
                        <i class="fa fa-check"></i> Validate
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/work-order-overtime.js') ?>" defer></script>