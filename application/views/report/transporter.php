<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Transporter Report</h3>
        <div class="pull-right">
            <a href="#filter_transporter" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_transporter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
        </div>
    </div>
    <?php if(isset($target_all)): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-6">Target : <?= values($target_all[0]['target'],'-') ?></label>
            </div>
        </div>
    </div>
    <?php if(!empty($target_all[0]['id_branch'])) : ?>
    <?php foreach ($target_all as $target_branch) : ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-6">Target <?= values($target_branch['branch_name'],'-') ?> : <?= values($target_branch['target_branch'],'-') ?></label>
            </div>
        </div>
    </div>
    <?php endforeach ?>
    <?php endif;?>
    <?php else: ?>
        <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-6">There is no target set</label>
            </div>
        </div>
    </div>
    <?php endif;?>
    <div class="box-body">
        <?php $this->load->view('report/_filter_transporter', [
            'hidden' => isset($_GET['filter_transporter']) ? false : true
        ]) ?>
        <div class="table-responsive">
        <table class="table table-bordered table-striped no-datatable responsive" data-page-length="10" id="table-transporter">
            <thead>
            <tr>
                <th class="text-center" rowspan="2" style="max-width: 3px">Week</th>
                <th class="text-center" colspan="2" >INTERNAL</th>
                <th class="text-center" rowspan="2" style="max-width: 3px">EX TER NAL</th>
                <?php if(!empty($vehicles)): ?>
                <?php foreach ($vehicles as $vehicle): ?>
                    <th class="text-center" colspan="3">
                        <?= strtoupper($vehicle['vehicle_name']); ?>
                        <br>
                        (<?= strtoupper(str_replace(' ', '', $vehicle['no_plate'])); ?>)
                    </th>
                <?php endforeach; ?>
                <?php endif; ?>
            </tr>
            <tr>
            <th class="text-center" style="max-width: 3px">Comp</th>
            <th class="text-center" style="max-width: 20px" >Cont %</th>
            <?php if(!empty($vehicles)): ?>
                <?php foreach ($vehicles as $vehicle): ?>
                    <th style="max-width: 2px">Trip</th>
                    <th style="max-width: 20px">Use %</th>
                    <th style="max-width: 20px">Targ %</th>
                <?php endforeach; ?>
            <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php 
                $param = "";
                $paramType = get_url_param('activity_type',0);
                $year = get_url_param('year',0);
                if(isset($_GET['branch'])){
                    $branch = get_url_param('branch',0);
                    foreach ($branch as $key => $branch) {
                            $param.="&branch%5B%5D=".$branch;
                    }
                }
                  ?>
            <?php foreach ($periods as $period): ?>
                <tr>
                    <td class="text-center">
                        <?= $period ?>
                    </td>
                    <?php if (key_exists($period, $reports)): ?>
                        <td class="text-center" style="width: 10px;"><?= $reports[$period]['total'] ?> / <?= $reports[$period]['total_external'] + $reports[$period]['total'] ?></td>
                        <td class="text-center" style="width: 10px;"><?= numerical($reports[$period]['total']/($reports[$period]['total_external'] + $reports[$period]['total']) * 100, 1,true) ?> %</td>
                        <td class="text-center">
                            <a href="<?= site_url("report/transporter-external-detail?year={$year}&week={$period}&activity_type={$paramType}{$param}") ?>" target="_blank">
                                <?= $reports[$period]['total_external'] ?>
                            </a>
                        </td>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <?php $isFound = false; ?>
                            <?php foreach ($reports[$period]['vehicles'] as $report): ?>
                                <?php if ($report['no_plate'] == $vehicle['no_plate'] && $report['week'] == $period): ?>
                                    <td class="text-center">
                                        <a href="<?= site_url("report/transporter-detail?year={$year}&week={$period}&vehicle={$report['no_plate']}&branch={$report['id_branch']}&activity_type={$paramType}&branch_data=" . get_url_param('branch_data', 'VEHICLE')) ?>" target="_blank">
                                            <?= $report['total_safe_conduct'] ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <?= numerical($report['percentage'], 1, true) ?> %
                                    </td>
                                    <td class="text-center">
                                        <?php if($report['prod']<100): ?>
                                            <span style="color:red"><?= numerical($report['prod'], 1, true) ?> %</span>
                                        <?php else : ?>
                                            <?= numerical($report['prod'], 1, true) ?> %
                                        <?php endif; ?>
                                    </td>
                                    <?php $isFound = true; break; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (!$isFound): ?>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script defer>
    $('#table-transporter'). dataTable({ "order": [[ 0, 'desc' ]] });
</script>