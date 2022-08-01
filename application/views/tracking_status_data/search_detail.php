<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Inbound Tracking</h3>
    </div>
    <div class="box-body">
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
        <div class="form-horizontal form-view mb0">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No BL</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if(empty($statusResult['documents']['bl'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="#modal-view-document" data-toggle="modal" data-id="<?= $statusResult['documents']['bl']['id'] ?>" class="btn-preview-document">
                                        <?= $statusResult['documents']['bl']['no_document'] ?? '-' ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Invoice</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if(empty($statusResult['documents']['invoice'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="#modal-view-document" data-toggle="modal" data-id="<?= $statusResult['documents']['invoice']['id'] ?>" class="btn-preview-document">
                                        <?= $statusResult['documents']['invoice']['no_document'] ?? '-' ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Reference</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if(empty($statusResult['documents']['main_document'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="#modal-view-document" data-toggle="modal" data-id="<?= $statusResult['documents']['main_document']['id'] ?>" class="btn-preview-document">
                                        <?= $statusResult['documents']['main_document']['no_document'] ?? '-' ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">ETA</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $statusResult['documents']['eta']['document_date'] ?? '-' ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Packing List</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if(empty($statusResult['documents']['packing_list'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="#modal-view-document" data-toggle="modal" data-id="<?= $statusResult['documents']['packing_list']['id'] ?>" class="btn-preview-document">
                                        <?= $statusResult['documents']['packing_list']['no_document'] ?? '-' ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Data</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">Inbound</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <span class="label label-<?= get_if_exist($trackingStatuses, $statusResult['status']) ?>" style="text-transform: uppercase">
                                    <?= $statusResult['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $statusResult['description'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary mt10">
            <div class="box-header with-border">
                <h3 class="box-title">Outbound Trackings</h3>
            </div>
            <div class="box-body">
                <table class="table table-striped no-datatable">
                    <thead>
                    <tr>
                        <th>Outbound</th>
                        <th>Billing</th>
                        <th>SPPB</th>
                        <th>SPPD</th>
                        <th>Status</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($statusResult['outbounds'] as $outbound): ?>
                        <tr>
                            <td><?= $outbound['no_reference'] ?></td>
                            <td>
                                <?php if(empty($outbound['documents']['billing'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="#modal-view-document" data-toggle="modal" data-id="<?= $outbound['documents']['billing']['id'] ?>" class="btn-preview-document">
                                        Billing Document
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(empty($outbound['documents']['sppb'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="#modal-view-document" data-toggle="modal" data-id="<?= $outbound['documents']['sppb']['id'] ?>" class="btn-preview-document">
                                        SPPB Document
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(empty($outbound['documents']['sppd'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="#modal-view-document" data-toggle="modal" data-id="<?= $outbound['documents']['sppd']['id'] ?>" class="btn-preview-document">
                                        SPPD Document
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td style="text-transform: uppercase">
                                <span class="label label-<?= get_if_exist($trackingStatuses, $outbound['status']) ?>">
                                    <?= $outbound['status'] ?>
                                </span>
                            </td>
                            <td><?= $outbound['description'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($statusResult['outbounds'])): ?>
                        <tr>
                            <td colspan="6">No outbound data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-view-document">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">View Document</h4>
            </div>
            <div class="modal-body">
                <div id="document-viewer" class="text-center">
                    <i class="fa fa-spinner"></i> Fetching document...
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/app/js/tracking_status.js') ?>" defer></script>