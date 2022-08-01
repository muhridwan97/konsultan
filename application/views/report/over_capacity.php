<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Over Capacity Report</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <a href="#filter_over_capacity" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_over_capacity', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_over_capacity', [
            'filter_over_capacity' => 'filter_over_capacity',
            'hidden' => false
        ]) ?>
        <?php if(get_url_param('filter_over_capacity') == true && !empty($multiOverCapacities)): ?>
        <?php foreach ($multiOverCapacities as $key => $multiOverCapacity) : ?>
        <div class="box box-primary">
        <div class="box-header">
            <h4 class="box-title"><?= $customers[$key]['name'] ?></h4>
        </div>  
        <div class="box-body">
            <div class="table-responsive">
            <?php if(get_url_param('filter_over_capacity') == true): ?>
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
            <table class="table table-bordered table-striped no-datatable responsive no-wrap">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Reference</th>
                    <th>Total Space</th>
                    <th>Ex No Container</th>
                    <th>No Goods</th>
                    <th>Goods Name</th>
                </tr>
                </thead>
                <tbody>
                    <?php $number = 0; 
                    if(get_url_param('filter_over_capacity') == true): ?>
                    <?php foreach ($multiOverCapacity as $overCapacity): ?>
                    <?php
                        $countData = count($overCapacity['detail_goods']) ;
                        $countGoods = !empty($overCapacity['detail_goods']) && $countData >= 1 ? $countData = $countData+1 : '';
                    ?>
                    <tr>
                        <td rowspan="<?= $countGoods ?>"><?= $number = $number+1; ?></td>
                        <td rowspan="<?= $countGoods ?>"><?= $overCapacity['no_reference_inbound'] ?></td>
                        <td rowspan="<?= $countGoods ?>">
                        <a href="<?= site_url('')?>report/over-capacity-detail?no_reference=<?= $overCapacity['no_reference_inbound'] ?>&&bookingId=<?= $overCapacity['id_booking'] ?>" target="_blank"> 
                        <?= (!empty($overCapacity['sum_space'])||!empty($overCapacity['sum_space_cal']))?if_empty($overCapacity['sum_space_cal'], $overCapacity['sum_space']):'-' ?> </a> m<sup>2</sup></td>
                        <?php if (!empty($countData)): ?>
                            <?php foreach($overCapacity['detail_goods'] AS $detail): ?>
                            <tr>
                                <td><?= if_empty($detail['ex_no_container'], '-') ?></td>
                                <td><?= if_empty($detail['no_goods'], '-') ?></td>
                                <td><?= if_empty($detail['goods_name'], '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <td> - </td>
                            <td> - </td>
                            <td> - </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2"> Total </td>
                        <td><?= array_sum($multiSpace[$key]); ?> m<sup>2</sup></td>
                        <td colspan="3"> </td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="7"> No Data Available </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
        </div> 
        <?php endforeach; ?>
        <?php else: ?>
            <table class="table table-bordered table-striped responsive no-wrap">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Reference</th>
                    <th>Total Space</th>
                    <th>Ex No Container</th>
                    <th>No Goods</th>
                    <th>Goods Name</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
