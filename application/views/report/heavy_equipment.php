<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Heavy Equipment Report</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <a href="#filter_heavy_equipment" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_heavy_equipment', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_heavy_equipment', [
            'filter_heavy_equipment' => 'filter_heavy_equipment',
            'hidden' => false
        ]) ?>
        <?php if(get_url_param('filter_heavy_equipment') == true ): ?>
        <div class="box box-primary">
        <div class="box-header">
            <h4 class="box-title"><?= $nameHeavy ?></h4>
        </div>  
        <div class="box-body">
            <div class="table-responsive">
            <?php if(get_url_param('filter_heavy_equipment') == true && false): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-6"><ins>Updated <?= date('d-F-Y',strtotime($multiLastUpdate[$key])) ?></ins></label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Total Capacity</label>
                <div class="col-sm-9">
                    <p><?= number_format($customers[$key]['contract'],2) ?> m<sup>2</sup></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Utilitas Spaces</label>
                <div class="col-sm-9">
                    <p><?= array_sum($multiSpace[$key]); ?> m<sup>2</sup></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3"><i>Occupancy Rate (%)</i></label>
                <div class="col-sm-9">
                    <p><i><?= number_format($customers[$key]['contract'],2)==0? number_format($customers[$key]['contract'],2): number_format( array_sum($multiSpace[$key])/$customers[$key]['contract'] * 100,2) ?> % </i></p>
                </div>
            </div>
            <?php endif; ?>
            <table class="table table-bordered table-striped datatable responsive no-wrap">
                <thead>
                <tr>
                    <th rowspan="2" style="width: 25px">No</th>
                    <th rowspan="2">Tanggal</th>
                    <th colspan="2">Jam Pemakaian</th>
                    <th rowspan="2">Keterangan</th>
                    <th rowspan="2">Total Jam</th>
                </tr>
                <tr>
                    <th>Mulai</th>
                    <th>Selesai</th>
                </tr>
                </thead>
                <tbody>
                    <?php $number = 0; 
                    if(get_url_param('filter_heavy_equipment') == true && get_url_param('type') == 'INTERNAL'): ?>
                        <?php foreach ($heavyEquipments as $heavyEquipment): ?>
                        <tr>
                            <td><?= $number = $number+1; ?></td>
                            <td><?= $heavyEquipment['day_name'].", ". date('d-M-Y',strtotime($heavyEquipment['selected_date'])) ?></td>
                            <td><?= $heavyEquipment['name_heavy_equipment']==''? $heavyEquipment['service_time_start'] : $heavyEquipment['start_job'] ?></td>
                            <td><?= $heavyEquipment['name_heavy_equipment']==''? $heavyEquipment['service_time_end'] : $heavyEquipment['finish_job'] ?></td>                        
                            <td><?= $heavyEquipment['name_heavy_equipment']==''? 'Stand By' : $heavyEquipment['teks']?></td>
                            <td><?= $heavyEquipment['jam']>0 ? $heavyEquipment['jam'] : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php elseif(get_url_param('filter_heavy_equipment') == true && get_url_param('type') == 'EXTERNAL'): ?>
                        <?php foreach ($heavyEquipments as $heavyEquipment): ?>
                        <tr>
                            <td><?= $number = $number+1; ?></td>
                            <td><?= $heavyEquipment['day_name'].", ". date('d-M-Y',strtotime($heavyEquipment['selected_date'])) ?></td>
                            <td><?= $heavyEquipment['name_heavy_equipment']==''? $heavyEquipment['service_time_start'] : $heavyEquipment['start_job'] ?></td>
                            <td><?= $heavyEquipment['name_heavy_equipment']==''? $heavyEquipment['service_time_end'] : $heavyEquipment['finish_job'] ?></td>                        
                            <td><?= $heavyEquipment['name_heavy_equipment']==''? ( !empty($heavyEquipment['date_checked_out'])? (($heavyEquipment['date_checked_out']>=$heavyEquipment['selected_date'] && $heavyEquipment['date_checked_in']<=$heavyEquipment['selected_date'])?'Stand By':'-') :(!empty($heavyEquipment['date_checked_in'])?(($heavyEquipment['date_checked_in']<=$heavyEquipment['selected_date']?'Stand By':'-')):'-')) : $heavyEquipment['teks']?></td>
                            <td><?= $heavyEquipment['jam']>0 ? $heavyEquipment['jam'] : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6"> No Data Available </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"> </td>
                        <td>Total Jam </td>
                        <td><?= $totalJam ?></td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td>Total Jam lembur</td>
                        <td><?= $totalJam<=200? 0: $totalJam-200 ?> </td>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
        </div> 
        <?php else: ?>
            <table class="table table-bordered table-striped responsive no-wrap">
                <thead>
                <tr>
                    <th rowspan="2" style="width: 25px">No</th>
                    <th rowspan="2">Tanggal</th>
                    <th colspan="2">Jam Pemakaian</th>
                    <th rowspan="2">Keterangan</th>
                    <th rowspan="2">Total Jam</th>
                </tr>
                <tr>
                    <th>Mulai</th>
                    <th>Selesai</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
