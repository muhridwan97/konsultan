<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Outstanding Priority & Location
        </h3>
        <div class="pull-right">
            <?php if (!get_url_param('hide_filter', 0)): ?>
                <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                    <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
                </a>
            <?php endif; ?>
            <a href="<?= site_url('payment/ask-approval') ?>" class="btn btn-warning" id="btn-set-priority-batch" style="display: none">
                Edit Priority
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('transporter_entry_permit_request_priority/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>
        <?php $this->load->view('template/_alert') ?>
        <table class="table table-bordered table-striped table-ajax responsive" id="table-tep-request-priority">
            <thead>
            <tr class="text-nowrap">
                <th style="width: 30px" class="type-no">No</th>
                <th>Customer</th>
                <th>Reference</th>
                <th class="type-goods">Goods Name</th>
                <th>Unit</th>
                <th class="type-request">Requests</th>
                <th>Location</th>
                <th>Priority</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-tep-request-priority-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right row-data"
            data-id-upload="{{id_upload}}"
            data-id-booking="{{id_booking}}"
            data-id-goods="{{id_goods}}"
            data-id-unit="{{id_unit}}"
            data-ex-no-container="{{ex_no_container}}">
            <li class="dropdown-header">ACTION</li>

            <li class="action-view">
                <a href="<?= site_url('transporter-entry-permit-request-priority/view?{{id}}') ?>">
                    <i class="fa ion-search"></i> View
                </a>
            </li>
            <li role="separator" class="divider"></li>
            <li class="action-edit">
                <a href="<?= site_url('transporter-entry-permit-request-priority/edit?{{id}}') ?>">
                    <i class="fa ion-compose"></i> Edit Priority
                </a>
            </li>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/tep-request-priority.js?v=1') ?>" defer></script>