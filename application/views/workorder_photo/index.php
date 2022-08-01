<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Job Photo</h3>
        <div class="pull-right">
            <a href="#form-filter-work-order-photo" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_work_order_photo', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a class="btn btn-primary reload">
                <i class="fa fa-repeat"></i> Refresh
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('workorder_photo/_filter_workorder_photo', ['hidden' => isset($_GET['filter_work_order_photo']) ? false : true]) ?>
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" data-page-length='50' id="table-jobs">
            <thead>
            <tr>
                <th style="width: 20px">No</th>
                <th>Customer</th>
                <th class="field-job">No Job</th>
                <th class="field-handling no-wrap">Handling</th>
                <th class="field-gate-date">Gate Date</th>
                <th class="field-job-date">Job Complete</th>
                <th class="field-description" style="width: 60px">Description</th>
                <th class="field-status">Status</th>
                <th class="field-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-work-order-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right row-workorder"
            data-id="{{id}}"
            data-no="{{no_work_order}}"
            data-attachment="{{attachment}}"
            data-status="{{status}}"
            data-print-total="{{print_total}}"
            data-print-max="{{print_max}}">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VIEW_PHOTO)): ?>
                <li>
                    <a href="<?= site_url('work-order-photo/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View Job Result
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/work-order-photo.js?v=1') ?>" defer></script>