<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Request TEP for <?= get_url_param('filter_queue', 0) ? $_GET['date'] :  date('d F Y',strtotime($temp_tanggal) )?></h3>
        <div class="pull-right">
            <a href="#form-filter-queue" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_queue', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE_OUTBOUND)): ?>
            <button class="btn btn-danger batch-action" style="display: none" id="btn-deleted-point" data-url="<?= site_url('transporter-entry-permit/set-tep/') ?>">MERGE</button>
            <?php if (get_active_branch_id() == 8): ?>
                <a href="<?= site_url('linked-entry-permit/create') ?>" class="btn btn-primary">
                    Create TEP OUTBOUND M2
                </a>
            <?php endif; ?>
            <?php if (get_active_branch('branch_type') == "TPP"): ?>
                <a href="<?= site_url('transporter-entry-permit/create-outbound') ?>" class="btn btn-primary">
                    Create TEP OUTBOUND
                </a>
            <?php endif; ?>
        <?php endif; ?> 

        <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_SLOT)): ?>
            <button data-url="<?= site_url('transporter-entry-permit/add-slot') ?>" class="btn btn-primary btn-add-slot">
                Add Slot
            </button>
        <?php endif; ?>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_REQUEST)): ?>
            <a href="<?= site_url('transporter-entry-permit/request-inbound') ?>" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="BC 3.3, BC 4.0 and BC 2.7">
                Request TEP inbound
            </a>
        <?php endif; ?> 
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_REQUEST)): ?>
            <a href="<?= site_url('transporter-entry-permit/request') ?>" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="BC 2.8, BC 2.7, BC 4.1, BC 3.3 and P3BET">
                Request TEP outbound
            </a>
        <?php endif; ?>  
        </div>
    </div>
        
    <div class="box-body" id="tep-queue-list">

        <?php $this->load->view('template/_alert') ?>
        <?php $this->load->view('transporter_entry_permit/_filter_queue', ['hidden' => isset($_GET['filter_queue']) ? false : true]) ?>
        <div class="list-group" id="list-queue-request">
            <a href="#" class="list-group-item disabled hidden-xs">
                <div class="row">
                    <div class="col-xs-1 text-center">
                        Queue
                    </div>
                    <div class="col-xs-2">
                        No Request
                    </div>
                    <div class="col-xs-3">
                        Customer
                    </div>
                    <div class="col-xs-4">
                        <div class="row">
                            <div class="col-sm-6">Description</div>
                            <div class="col-sm-2">Slot Request</div>
                            <div class="col-sm-4 text-right">Created Request Time</div>
                        </div>
                    </div>
                    <?php if(AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
					<div class="col-xs-2" style="display: none">
						<input type="checkbox" id="check-all">
                    </div>
                    
                    <?php endif; ?>
                    <div class="col-xs-1 text-right">
                        Action
                    </div>
                </div>
            </a>
            <?php if (count($requests) > 0): ?>
                <?php 
                    foreach ($requests as $request): ?>
                    <div data-id="<?= $request['id'] ?>"
                         data-customer="<?= $request['customer_name'] ?>"
                         data-date="<?= format_date($request['tep_date'], 'd F Y') ?>"
                         data-customerid="<?= $request['id_customer'] ?>"
                         data-uploadid="<?= if_empty( $request['id_upload'],$request['id_upload_multi']) ?>"
                         data-reference="<?= if_empty($request['no_reference'],$request['no_reference_multi']) ?>"
                         data-slot="<?= $request['slot'] ?>"
                         data-slot-created="<?= $request['slot_created'] ?>"
                         data-category="<?= $request['category'] ?>"
                         data-armada="<?= $request['armada'] ?>"
                         class="list-group-item queue-list"
                         style="<?= $request['armada'] == 'TCI' ? 'background-color:#ffffcc;' : ''  ?>">
                        <div class="row">
                            <div class="col-xs-3 col-sm-1 text-center">
                                <h2 class="list-group-item-heading">
                                    <?= $request['no_queue'] ?>
                                </h2>
                                <button class="btn btn-sm btn-primary mt10 btn-reveal-items collapsed" data-toggle="collapse" data-target="#request-detail-<?= $request['id'] ?>">
                                    SHOW
                                </button>
                            </div>
                            <div class="col-xs-9 col-sm-2 ">
                                <h4 class="list-group-item-heading">
                                    <?= $request['no_request'] ?>
                                </h4>
                                <div class="row">
                                    <div class="col-sm-12"> 
                                    <small class="text-muted no-wrap">
                                        <?= $request['category']?>
                                    </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-9 col-sm-3">
                                <h4 class="list-group-item-heading">
                                    <strong><?= $request['customer_name'] ?></strong>
                                </h4>
                                <div class="row">
                                    <div class="col-sm-12"> 
                                    <?php if($request['category'] == 'OUTBOUND'): ?>
                                        <small class="text-muted no-wrap">
                                            REF IN: <?= if_empty($request['no_reference_in'],$request['no_reference_in_multi']) ?></small></br>   
                                        <small class="text-muted no-wrap">
                                            REF OUT: <?= if_empty($request['no_reference'],$request['no_reference_multi']) ?></small>                                           
                                    <?php else: ?>
                                        <small class="text-muted no-wrap">
                                            REF: <?= if_empty($request['no_reference'],$request['no_reference_multi']) ?></small>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-9 col-xs-push-3 col-sm-4 col-sm-push-0">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Description:</strong><br>
                                        <?= if_empty($request['description'], 'No description') ?>
                                    </div>
                                    <div class="col-sm-2">
                                        <strong>Slot:</strong><br>
                                        <?= if_empty($request['slot'], 'not set') ?><br>
                                        <strong>TEP:</strong><br>
                                        <?php if(isset($request['tep_code'])): ?>
                                        <?= if_empty($request['tep_code'], '-') ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-sm-4 text-right">
                                        <strong>Time:</strong><br>
                                        <?= date('d F Y H:i:s', strtotime($request['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($request['armada'] == 'CUSTOMER' ) :?>
                                <?php if($request['status'] == 'PENDING' 
                                && date('Y-m-d',strtotime($request['created_at'])) <= date('Y-m-d') 
                                && date('Y-m-d',strtotime($request['tep_date'])) >= date('Y-m-d')
                                && AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
                                <div class="col-xs-9 col-xs-push-3 col-sm-2 col-sm-push-0">
                                    <input
                                    data-customer="<?= $request['customer_name'] ?>"
                                    data-date="<?= format_date($request['tep_date'], 'd F Y') ?>"
                                    data-customerid="<?= $request['id_customer'] ?>"
                                    data-uploadid="<?= if_empty( $request['id_upload'],$request['id_upload_multi']) ?>"
                                    data-slot="<?= $request['slot'] ?>"
                                    data-category="<?= $request['category'] ?>"
                                    type="checkbox" class="queue-check" id="check_<?= $request['id'] ?>" value="<?= $request['id'] ?>">
                                    <a href="#" data-toggle="tooltip" title="Add Merge">
                                    <button class="btn btn-primary btn-add-merge" data-url="<?= site_url('transporter-entry-permit/add-merge/') ?>">
                                            <i class="fa fa-plus"></i>
                                            </button></a>
                                    <!-- <a href="#" data-toggle="tooltip" title="View DO/Memo">
                                    <button class="btn btn-danger btn-view-file" data-id="<?= $request['id'] ?>">
                                        <i class="fa fa-folder"></i>
                                    </button></a> -->
                                </div>
                                <?php endif; ?>
                                <div class="col-xs-9 col-xs-push-3 col-sm-2 col-sm-push-0 text-right" style="padding-top: 5px">
                                    <?php if($request['status'] == 'PENDING'): ?>
                                        <?php if(date('Y-m-d',strtotime($request['created_at'])) <= date('Y-m-d') && date('Y-m-d',strtotime($request['tep_date'])) >= date('Y-m-d')): ?>
                                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
                                            <button class="btn btn-primary btn-set-tep" data-url="<?= site_url('transporter-entry-permit/set-tep/' . $request['id']) ?>">
                                                Set TEP &nbsp; <i class="fa fa-check"></i>
                                            </button>
                                            <button class="btn btn-warning btn-skip" data-url="<?= site_url('transporter-entry-permit/set-skip/' . $request['id']) ?>">
                                                Skip &nbsp; <i class="fa fa-times"></i>
                                            </button>
                                            <?php else: ?>
                                                <strong>Queue pending</strong>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <strong>Request expired</strong>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($request['status'] == 'SKIP'): ?>
                                            <strong>Skip request by <?= $request['set_name'] ?></strong>
                                        <?php else: ?>
                                            <p>
                                                <strong>Set queue by <?= $request['set_name'] ?></strong><br>
                                                at <?= format_date($request['expired_at'], 'd F Y' )." ".format_date($request['queue_time'], 'H:i:s') ?>
                                            </p>
                                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
                                                <button class="btn btn-primary btn-sm btn-edit-set-tep" data-url="<?= site_url('transporter-entry-permit/update-set-tep/' . $request['id']) ?>" data-is-today="<?= format_date($request['expired_at']) == date('Y-m-d') ?>">
                                                    Edit Set TEP &nbsp; <i class="fa fa-edit"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php if($request['status'] == 'PENDING' 
                                && AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
                                <div class="col-xs-9 col-xs-push-3 col-sm-2 col-sm-push-0">
                                    <a href="#" data-toggle="tooltip" title="Add Merge">
                                        <button class="btn btn-primary btn-add-merge" data-url="<?= site_url('transporter-entry-permit/add-merge/') ?>">
                                        <i class="fa fa-plus"></i>
                                        </button></a>
                                </div>
                                <?php endif; ?>
                                <div class="col-xs-9 col-xs-push-3 col-sm-2 col-sm-push-0 text-right" style="padding-top: 5px">
                                    <?php if($request['status'] == 'PENDING'): ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
                                        <a class="btn btn-primary" href="<?= site_url('transporter-entry-permit/create-tep-request/' . $request['id']) ?>">
                                            Set TEP &nbsp; <i class="fa fa-check"></i>
                                        </a>
                                        <button class="btn btn-warning btn-skip" data-url="<?= site_url('transporter-entry-permit/set-skip/' . $request['id']) ?>">
                                            Skip &nbsp; <i class="fa fa-times"></i>
                                        </button>
                                        <?php else: ?>
                                            <strong>Queue pending</strong>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($request['status'] == 'SKIP'): ?>
                                            <strong>Skip request by <?= $request['set_name'] ?></strong>
                                        <?php else: ?>
                                            <p>
                                                <strong>Set queue by <?= $request['set_name'] ?></strong><br>
                                                at <?= format_date($request['expired_at'], 'd F Y' )." ".format_date($request['queue_time'], 'H:i:s') ?>
                                            </p>
                                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
                                                <?php if (isset($request['is_add_tep']) && $request['is_add_tep']): ?>
                                                    <a href="#" data-toggle="tooltip" title="Add Merge">
                                                        <button class="btn btn-primary btn-sm btn-add-merge" data-url="<?= site_url('transporter-entry-permit/add-merge/') ?>">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </a>
                                                    <a class="btn btn-info btn-sm" href="<?= site_url('transporter-entry-permit/create-tep-request/' . $request['id']) ?>">
                                                        Add TEP &nbsp; <i class="fa fa-plus"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button class="btn btn-primary btn-sm btn-edit-set-tep" data-url="<?= site_url('transporter-entry-permit/update-set-tep/' . $request['id']) ?>" data-is-today="<?= format_date($request['expired_at']) == date('Y-m-d') ?>">
                                                    Edit Set TEP &nbsp; <i class="fa fa-edit"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="collapse" id="request-detail-<?= $request['id'] ?>">
                        <div class="list-group-item" style="border-radius: 0">
                            <table class="table table-sm no-datatable responsive ml20">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Reference</th>
                                    <th style="width: 300px">Goods Name</th>
                                    <th>Unit</th>
                                    <th>Ex Container</th>
                                    <th>Hold</th>
                                    <th>Location</th>
                                    <th>Priority</th>
                                    <th>Quantity</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($request['tep_request_uploads'] as $itemIndex => $uploadItem): ?>
                                    <tr>
                                        <td><?= $itemIndex + 1 ?></td>
                                        <td>
                                            <a href="<?= site_url('upload/view/' . $uploadItem['id_upload']) ?>">
                                                <?= $uploadItem['no_reference_upload'] ?>
                                            </a>
                                        </td>
                                        <td style="word-break: break-word"><?= $uploadItem['goods_name'] ?></td>
                                        <td><?= $uploadItem['unit'] ?></td>
                                        <td><?= if_empty($uploadItem['ex_no_container'], '-') ?></td>
                                        <td><?= $uploadItem['hold_status'] ?></td>
                                        <td><?= if_empty($uploadItem['unload_location'], '-') ?></td>
                                        <td><?= if_empty($uploadItem['priority'], '-') ?></td>
                                        <td><?= if_empty(round($uploadItem['quantity'],2), '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php 
                    endforeach; ?>
            <?php else: ?>
                <a href="#" class="list-group-item disabled">
                    No queue request tep
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-set-tep">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" id="form-set-tep">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="id_customer" id="id_customer">
                <input type="hidden" name="id_upload" id="id_upload">
                <input type="hidden" name="tep_date" id="tep_date">
                <input type="hidden" name="category" id="category">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Set Request</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 2px">
                        <span class="set-tep-message">Are you sure want to create tep</span>
                        <strong id="customer-name"></strong> At <strong id="date"></strong>?
                    </p>
                    <div class="row" style="margin-bottom: 2px">
                        <div class="col-sm-6">
                            <div class="bootstrap-timepicker">
                                <div class="form-group">
                                <label>Queue time:</label>

                                <div class="input-group">
                                    <input type="time" class="form-control timepicker" id="queue_time" name="queue_time" min="<?= $min_time['min_time'] ?>" data-min-next="<?= $min_time['min_time'] ?>" data-min-today="<?= $min_time_today['min_time'] ?>">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div>
                                <!-- /.input group -->
                                </div>
                                <!-- /.form group -->
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 2px">
                        <div class="col-sm-6">
                            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" placeholder="TEP description"
                                        maxlength="500"><?= set_value('description') ?></textarea>
                                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    <div id="express-wrapper" style="display:none;">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="express_service_type">Type</label>
                                    <select class="form-control select2" name="express_service_type" id="express_service_type" data-placeholder="Select Type" style="width: 100%;">
                                        <option value=""></option>
                                        <option value="TCI">Transcon Indonesia</option>
                                        <option value="CUSTOMER">Customer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="file_express">
                                        Express Services <span style="color:#a9a9a9">(Upload max 5 MB)</span>
                                    </label>
                                    <label class="pull-right">
                                        <span class="label label-danger">Required</span>
                                    </label>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="btn btn-primary btn-block fileinput-button">
                                                <span class="button-file">Select File</span>
                                                <input class="upload-express" id="file_express" type="file" name="file_express">
                                            </div>
                                            <div class="upload-input-wrapper"></div>
                                        </div>
                                        <div class="col-sm-9">
                                            <div id="progress" class="progress progress-upload">
                                                <div class="progress-bar progress-bar-success"></div>
                                            </div>
                                            <div class="uploaded-file"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success pull-left" id="btn-today">Change today</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-create-tep">Create TEP</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-set-tep-merge">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" >
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="id_customer" id="id_customer">
                <input type="hidden" name="id_upload" id="id_upload">
                <input type="hidden" name="tep_date" id="tep_date">
                <input type="hidden" name="category" id="category">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Set Request</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 2px">Are you sure want to create tep 
                        <strong>MERGE SELECTED CUSTOMER</strong> At <strong id="date"></strong>?</p>
                    <div class="row" style="margin-bottom: 2px">
                        <div class="col-sm-6">
                            <div class="bootstrap-timepicker">
                                <div class="form-group">
                                <label>Queue time:</label>

                                <div class="input-group">
                                    <input type="time" class="form-control timepicker" id="queue_time" name="queue_time" min="<?= $min_time['min_time'] ?>" >

                                    <div class="input-group-addon">
                                    <i class="fa fa-clock-o"></i>
                                    </div>
                                </div>
                                <!-- /.input group -->
                                </div>
                                <!-- /.form group -->
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 2px">
                        <div class="col-sm-6">
                            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" placeholder="TEP description"
                                        maxlength="500"><?= set_value('description') ?></textarea>
                                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Create TEP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('transporter_entry_permit/_modal_add_slot') ?>
<?php $this->load->view('transporter_entry_permit/_modal_skip') ?>
<?php $this->load->view('transporter_entry_permit/_modal_add_merge') ?>
<?php $this->load->view('transporter_entry_permit/_modal_view_file') ?>
<script src="<?= base_url('assets/app/js/tep.js?v=7') ?>" defer></script>

