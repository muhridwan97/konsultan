<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Fleet Production</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter', 1) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <?php $this->session->unset_userdata('status'); ?>
        <?php $this->session->unset_userdata('message'); ?>

        <form role="form" method="get" class="form-filter need-validation" id="form-filter" <?= get_url_param('filter', 1) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="form-group <?= form_error('branches[]') == '' ?: 'has-error'; ?>">
                                <label for="branch">Branches</label>
                                <select class="form-control select2" name="branch[]" id="branch" data-placeholder="Select branch" required multiple>
                                    <option value=""></option>
                                    <?php foreach ($branches as $branch): ?>
                                        <option value="<?= $branch['id'] ?>"<?= in_array($branch['id'], get_url_param('branch', [])) ? ' selected' : '' ?>>
                                            <?= $branch['branch'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label for="date_from">Date From (Cut Off)</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Date cut off from" required autocomplete="off"
                                       maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label for="date_to">Date To (Cut Off)</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Date cut off to" required autocomplete="off"
                                       maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default btn-reset-filter">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Inbounds</h3>
    </div>
    <div class="box-body">
        <?php if (!empty($dataRanges)): ?>
            <div class="alert alert-info">
                <!--<p><strong>Completed Date</strong>: Stripping Job (Completed At), LCL Safe Conduct (Security In/Out depends on Expedition Type):</p>-->
                <p><strong>Completed Date</strong>: Stripping Job & Unload LCL (Completed At):</p>
                <ul class="pl20">
                    <?php foreach ($dataRanges as $dataRange): ?>
                        <li>
                            <strong><?= $dataRange['branch'] ?></strong>:
                            Completed Date <?= format_date($dataRange['start'], 'Y-m-d H:i') ?> - <?= format_date($dataRange['end'], 'Y-m-d H:i') ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <table class="table table-bordered table-striped responsive" data-page-length="10">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Branch</th>
                <th>Inbound Date</th>
                <th>Reference</th>
                <th>Completed Data</th>
                <th>SPPB Date</th>
                <th>Inbound Reference</th>
                <th>Customer</th>
                <th>No Containers</th>
                <th>Party</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($inbounds as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $item['branch'] ?></td>
                    <td><?= $item['inbound_date'] ?></td>
                    <td><?= str_replace(',', '<br>', $item['data_reference']) ?></td>
                    <td><?= str_replace(',', '<br>', $item['completed_date']) ?></td>
                    <td><?= str_replace(',', '<br>', $item['sppb_date']) ?></td>
                    <td><?= str_replace(',', '<br>', $item['no_reference']) ?></td>
                    <td><?= str_replace(',', '<br>', $item['customer_name']) ?></td>
                    <td><?= str_replace(',', '<br>', $item['no_container']) ?></td>
                    <td><?= $item['party'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Outbounds</h3>
    </div>
    <div class="box-body">
        <?php if (!empty($dataRanges)): ?>
            <div class="alert alert-info">
                <p><strong>Outbound Date</strong>: Security Out any of Expedition Type:</p>
                <ul class="pl20">
                    <?php foreach ($dataRanges as $dataRange): ?>
                        <li>
                            <strong><?= $dataRange['branch'] ?></strong>:
                            Outbound Date <?= format_date($dataRange['start'], 'Y-m-d H:i') ?> - <?= format_date($dataRange['end'], 'Y-m-d H:i') ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <table class="table table-bordered table-striped responsive" data-page-length="10">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Branch</th>
                <th>Outbound Date</th>
                <th>No Police</th>
                <th>Outbound Reference</th>
                <th>Inbound Reference</th>
                <th>Vehicle Type</th>
                <th>Customer</th>
                <th>Driver</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($outbounds as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $item['branch'] ?></td>
                    <td><?= $item['outbound_date'] ?></td>
                    <td><?= $item['no_police'] ?></td>
                    <td><?= str_replace(',', '<br>', $item['no_reference']) ?></td>
                    <td><?= str_replace(',', '<br>', $item['no_reference_inbound']) ?></td>
                    <td><?= $item['vehicle_type'] ?></td>
                    <td><?= str_replace(',', '<br>', $item['customer_name']) ?></td>
                    <td><?= $item['driver'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>