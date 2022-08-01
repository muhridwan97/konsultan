<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Proof of Heavy Equipment Usage</h3>
        <div class="pull-right">
            <!-- <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a> -->
            <a href="#filter_proof" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_proof', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('proof_heavy_equipment/_filter_proof', [
            'filter_proof' => 'filter_proof',
            'hidden' => false
        ]) ?>
        <?php if(get_url_param('filter_proof') == true ): ?>
        <div class="box box-primary">
        <div class="box-header">
            <h4 class="box-title">History Print</h4>
        </div>  
        <div class="box-body">
            <div class="table-responsive">
            <table class="table table-bordered table-striped datatable responsive" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Date</th>
                    <th style="width: 50%">Customer</th>
                    <th>Usage At</th>
                    <th>Print At</th>
                    <th>Print By</th>
                    <th class="type-action" style="width: 60px">Action</th>
                </tr>
                </thead>
                <tbody>
                    <?php $number = 0; 
                    if(get_url_param('filter_proof') == true): ?>
                        <?php foreach ($historyPrints as $historyPrint): ?>
                        <tr>
                            <td><?= $number = $number+1; ?></td>
                            <td><?= date('d-M-Y',strtotime($historyPrint['date'])) ?></td>
                            <td><?= $historyPrint['customer'] ?></td>
                            <td><?= $historyPrint['start']." - ".$historyPrint['end']?></td>
                            <td><?= $historyPrint['created_at'] ?></td>
                            <td><?= $historyPrint['creator_name']?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li class="dropdown-header">ACTION</li>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_VIEW)): ?>
                                            <li>
                                                <a href="<?= site_url('proof-heavy-equipment/view/' . $historyPrint['id']) ?>">
                                                    <i class="fa ion-search"></i>View Detail
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6"> No Data Available </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
        </div> 
        <?php else: ?>
        <div class="box box-primary">
            <div class="box-header">
                <h4 class="box-title">History Print</h4>
            </div>  
            <table class="table table-bordered table-striped responsive no-wrap">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Usage At</th>
                    <th>Print At</th>
                    <th>Print By</th>
                    <th class="type-action" style="width: 60px">Action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

