<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Search Data</h3>
        <button data-toggle="modal" data-target="#modal-status-info" class="btn btn-small btn-primary pull-right">Info &nbsp; <i class="fa fa-question-circle"></i></button>
    </div>
    <form role="form" method="get">
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="type">Data Type</label>
                        <select class="form-control select2" id="type" name="type" data-placeholder="Select type" required>
                            <option value=""></option>
                            <option value="BL"<?= get_url_param('type') == 'BL' ? ' selected' : '' ?>>BL</option>
                            <option value="invoice"<?= get_url_param('type') == 'invoice' ? ' selected' : '' ?>>Invoice</option>
                            <option value="no_reference"<?= get_url_param('type') == 'no_reference' ? ' selected' : '' ?>>No Reference (AJU)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="form-group">
                        <label for="reference">Reference (Input and press enter)</label>
                        <select class="form-control select2" id="reference" name="references[]" required multiple data-tags="true" data-placeholder="Type query (multiple input separated by enter)">
                            <option value=""></option>
                            <?php foreach (get_url_param('references', []) as $reference): ?>
                                <option value="<?= trim($reference) ?>" selected>
                                    <?= trim($reference) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-success btn-block">Track Data</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Search Result</h3>
    </div>
    <form role="form" method="get">
        <div class="box-body">
            <table class="table no-datatable">
                <thead>
                <tr>
                    <th style="width: 50px" class="text-center">No</th>
                    <th>Search Type</th>
                    <th>Search Query</th>
                    <th>Current Status</th>
                    <th>Description</th>
                    <th style="width: 100px" class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $trackingStatuses = [
                    'On-Going Vessel' => 'default',
                    'Port of Discharge' => 'primary',
                    'Complete Inbound' => 'success',
                    'Requested' => 'default',
                    'Aw Cust. Complete' => 'info',
                    'Customs Clearance' => 'primary',
                    'Complete Outbound' => 'warning',
                    'Arrive' => 'success',
                    'Not Found' => 'danger',
                ];
                $trackingDescriptions = [
                    'On-Going Vessel' => 'Document received',
                    'Port of Discharge' => 'Awaiting cargo readiness',
                    'Complete Inbound' => 'Awaiting next instruction',
                    'Requested' => 'Outbound request received',
                    'Aw Cust. Complete' => 'LARTAS document needed / Awaiting payment',
                    'Customs Clearance' => 'Goods ready to go out',
                    'Complete Outbound' => 'All goods are out',
                    'Arrive' => 'Receving CIS',
                    'Not Found' => 'Data not found',
                ];
                ?>
                <?php foreach ($data as $index => $datum): ?>
                    <tr <?= ($datum['status'] == 'Not Found') ? 'class="danger"' : 'style="background-color: #f9f9f9"' ?>>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><?= ucwords(str_replace(['_', '-'], ' ', $datum['type'])) ?></td>
                        <td><?= $datum['reference'] ?></td>
                        <td style="text-transform: uppercase">
                            <?php if(empty($datum['outbounds'])): ?>
                                <span class="label label-<?= get_if_exist($trackingStatuses, $datum['status']) ?>">
                                    <?= $datum['status'] ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(empty($datum['outbounds'])): ?>
                                <?= $datum['description'] ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($datum['status'] != 'Not Found'): ?>
                                <a href="<?= site_url('tracking-status-data/search-detail/' . $datum['id']) ?>" class="btn btn-info">
                                    Detail
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if(!empty($datum['outbounds'])): ?>
                        <?php foreach ($datum['outbounds'] as $outbound): ?>
                            <tr class="text-muted">
                                <td></td>
                                <td>&nbsp; - Outbound</td>
                                <td><?= $outbound['no_reference'] ?></td>
                                <td style="text-transform: uppercase">
                                    <span class="label label-<?= get_if_exist($trackingStatuses, $outbound['status']) ?>">
                                        <?= $outbound['status'] ?>
                                    </span>
                                </td>
                                <td><?= $outbound['description'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if(empty($data)): ?>
                    <tr>
                        <td colspan="5">No any data found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-status-info">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tracking Statuses</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 40px">No</th>
                        <th>Status</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $index = 1 ?>
                    <?php foreach ($trackingStatuses as $status => $statusClass): ?>
                        <tr>
                            <td><?= $index++ ?></td>
                            <td style="text-transform: uppercase">
                                <span class="label label-<?= $statusClass ?>">
                                    <?= $status ?>
                                </span>
                            </td>
                            <td><?= $trackingDescriptions[$status] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>