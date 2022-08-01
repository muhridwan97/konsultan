<div class="row">
    <div class="col-md-6">
        <div class="box ">
            <div class="box-header with-border">
                <h3 class="box-title">Summary Documents</h3>
                <form action="#" method="get" class="pull-right" id="form-summary">
                    <select name="doc_year" id="doc_year" class="select2" style="min-width: 70px">
                        <?php for ($year = date('Y'); $year >= 2018; $year--) : ?>
                            <option value="<?= $year ?>">
                                <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="doc_month" id="doc_month" class="select2" style="min-width: 110px">
                        <?php foreach (get_months() as $index => $month) : ?>
                            <option value="<?= ($index + 1) ?>" <?= (date('m') == ($index + 1)) ? 'selected' : '' ?>>
                                <?= $month ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-primary" id="filter-summary" data-url="<?= site_url('report/summary_report_compliance') ?>">
                        Filter
                    </button>
                </form>
            </div>
            <div class="box-body" id="summary-body">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-body ">
                            <h4 class="mt0">Average Validate Document <span id="tanggal_summary"></span></h4>
                            <h2 class="mt10 text-danger">
                                <span id="average">-</span> / day
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box ">
            <div class="box-header with-border">
                <h3 class="box-title">Summary Draft</h3>
                <form action="#" method="get" class="pull-right" id="form-summary-draft">
                    <select name="doc_year" id="doc_year" class="select2" style="min-width: 70px">
                        <?php for ($year = date('Y'); $year >= 2018; $year--) : ?>
                            <option value="<?= $year ?>">
                                <?= $year ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="doc_month" id="doc_month" class="select2" style="min-width: 110px">
                        <?php foreach (get_months() as $index => $month) : ?>
                            <option value="<?= ($index + 1) ?>" <?= (date('m') == ($index + 1)) ? 'selected' : '' ?>>
                                <?= $month ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-primary" id="filter-summary-draft" data-url="<?= site_url('report/summary_report_compliance') ?>">
                        Filter
                    </button>
                </form>
            </div>
            <div class="box-body" id="summary-body-draft">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-body ">
                            <h4 class="mt0">Average Draft Document <span id="tanggal_summary"></span></h4>
                            <h2 class="mt10 text-danger">
                                <span id="average">-</span> / day
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">PIC Detail</h3>
        <div class="pull-right">
            <a href="#form-filter-work-order-container" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_report_compliance', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-work-order-container" <?= get_url_param('filter_report_compliance', 0) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_report_compliance" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pic">PIC</label>
                                <select class="form-control select2 " data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>" name="pic" id="pic" data-placeholder="Select pic">
                                    <option value="">
                                    </option>   
                                    <option value="all" <?= get_url_param('pic')=='all'?'selected':'' ?>>ALL</option>
                                    <?php
                                    foreach ($pic as $pic) : ?>
                                        <?php if (get_url_param('filter_report_compliance') && get_url_param('pic') == $pic['id'] && get_url_param('pic') != '') { ?>
                                            <option value="<?= $pic['id'] ?>" selected>
                                                <?= $pic['name'] ?>
                                            </option>
                                        <?php } ?>
                                        <option value="<?= $pic['id'] ?>">
                                            <?= $pic['name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customers">Customer</label>
                                <select class="form-control select2" data-url="" data-key-id="id" data-key-label="name" name="customers" id="customers" data-placeholder="Select customer">
                                <option value=""></option>    
                                <option value="all" <?= get_url_param('customers')=='all'?'selected':'' ?>>ALL</option>
                                    <?php
                                    foreach ($customer as $customer) : ?>
                                        <?php if (get_url_param('filter_report_compliance') && get_url_param('customers') == $customer['id'] && get_url_param('customers') != '') { ?>
                                            <option value="<?= $customer['id'] ?>" selected>
                                                <?= $customer['name'] ?>
                                            </option>
                                        <?php } ?>
                                        <option value="<?= $customer['id'] ?>">
                                            <?= $customer['name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="date_from">Date From</label>
                            <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Date from" maxlength="50" value="<?= get_url_param('filter_report_compliance') ? set_value('date_from', get_url_param('date_from')) : '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to">Date To</label>
                            <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Date to" maxlength="50" value="<?= get_url_param('filter_report_compliance') ? set_value('date_to', get_url_param('date_to')) : '' ?>">
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="branch">Branch</label>
                                <select class="form-control select2 " data-key-id="id" data-key-label="name" name="branch" id="branch" data-placeholder="Select branch">
                                    <option value="">
                                    </option>   
                                    <option value="all" <?= get_url_param('branch')=='all'?'selected':'' ?>>ALL</option>
                                    <?php
                                    foreach ($branches as $branch) : ?>
                                        <?php if (get_url_param('filter_report_compliance') && get_url_param('branch') == $branch['id'] && get_url_param('branch') != '') { ?>
                                            <option value="<?= $branch['id'] ?>" selected>
                                                <?= $branch['branch'] ?>
                                            </option>
                                        <?php } ?>
                                        <option value="<?= $branch['id'] ?>">
                                            <?= $branch['branch'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="doc_type">Document Type</label>
                                <select class="form-control select2 " data-key-id="id" data-key-label="name" name="doc_type" id="doc_type" data-placeholder="Select Document Type">
                                    <option value="">
                                    </option>   
                                    <option value="all" <?= get_url_param('doc_type')=='all'?'selected':'' ?>>ALL</option>
                                    <option value="sppb" <?= get_url_param('doc_type')=='sppb'?'selected':'' ?>>SPPB</option>
                                    <option value="draft" <?= get_url_param('doc_type')=='draft'?'selected':'' ?>>DRAFT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <table class="table table-striped table-bordered responsive" id="table-report-compliance">
            <thead>
                <tr>
                    <th>No</th>
                    <th>PIC</th>
                    <th>Customer</th>
                    <th>Upload No</th>
                    <th>Aju</th>
                    <th>Document</th>
                    <th>Document Date (Created)</th>
                    <th>Branch</th>
                    <th>Type</th>
                    <th class="text-center draft-time" data-sorter="draftSorter">Draft Rensponse Time (Hours)</th>
                    <th class="text-center">Confirm Rensponse Time (Hours)</th>
                    <th class="text-center">Total Items</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($compliances as $compliance) : ?>
                    <tr>
                        <th>
                            <?= $i; ?>
                        </th>
                        <th>
                            <?= if_empty($compliance['created_name'], '-') ?>
                        </th>
                        <th>
                            <?= if_empty($compliance['name_customer'], '-') ?>
                        </th>
                        <th>
                            <?= if_empty($compliance['no_upload'], '-') ?>
                        </th>
                        <th>
                            <?= if_empty($compliance['no_document'], '-') ?>
                        </th>
                        <th>
                            <?= if_empty($compliance['document_type'], '-') ?>
                        </th>
                        <th>
                            <?= if_empty($compliance['created_at'], '-') ?>
                        </th>
                        <th>
                            <?= if_empty($compliance['branch_name'], '-') ?>
                        </th>
                        <th>
                            <?= if_empty($compliance['jenis_doc'], '-') ?>
                        </th>
                        <th class="text-center">
                            <?= if_empty($compliance['draft_service_time'], 0) ?>
                        </th>
                        <th class="text-center">
                            <?= if_empty($compliance['confirm_service_time'], 0) ?>
                        </th>
                        <th class="text-center">
                            <?= if_empty($compliance['total_items'], '0') ?>
                        </th>
                    </tr>
                <?php
                    $i++;
                endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="<?= base_url('assets/app/js/report-compliance.js') ?>" defer></script>