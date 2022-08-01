<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Work Order Overtime</h3>
        <div class="pull-right">
            <a href="#form-filter-work-order-overtime" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_work_order_overtime', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('workorder_overtime/_filter', ['hidden' => isset($_GET['filter_work_order_overtime']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive no-wrap" id="table-work-order-overtime">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Branch</th>
                <th>No Reference In</th>
                <th>No Reference</th>
                <th>No Work Order</th>
                <th>Customer</th>
                <th class="type-date-time">Taken At</th>
                <th class="type-date-time">Completed At</th>
                <th>Day</th>
                <th class="type-hour">Service Time End</th>
                <th class="type-overtime">Overtime Minute</th>
                <th class="type-status">Charged To</th>
                <th class="type-has-attachment">Attachment</th>
                <th>Reason</th>
                <th>Validated By</th>
                <th>Validated At</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-work-order-overtime.js') ?>" defer></script>