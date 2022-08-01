<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Document Production</h3>
        <div class="pull-right">
            <!-- <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <a href="#filter_over_capacity" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_over_capacity', 0) ? 'Hide' : 'Show' ?> Filter
            </a> -->
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="col-sm-6">Week Target 30</label>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
            <table class="table table-bordered table-striped no-wrap no-datatable" id="production">
                <thead>
                <tr>
                    <th style="width: 25px" class="sorting_desc">Week</th>
                    <th>Jumlah Draft</th>
                    <th>Jumlah SPPB</th>
                    <th>Target %</th>
                    <th>AVG SPPB</th>
                    <th>Jumlah Document</th>
                    <th>Head Count</th>
                    <th>Overtime</th>
                </tr>
                </thead>
                <tbody>
                    <?php $number = 0; 
                    if(!empty($reportProductions)): ?>
                    <?php foreach ($reportProductions as $reportProduction): ?>
                    <tr>
                        <td><?= $reportProduction['minggu']; ?></td>
                        <td><a href="<?= site_url() ?>report/document-production-detail-draft?tahun=<?= $reportProduction['tahun'] ?>&minggu=<?=$reportProduction['minggu'] ?>" target="_blank"><?= if_empty($reportProduction['jumlah_draft'],0) ?></a></td>
                        <td><a href="<?= site_url() ?>report/document-production-detail-sppb?tahun=<?= $reportProduction['tahun'] ?>&minggu=<?=$reportProduction['minggu'] ?>" target="_blank"><?= if_empty($reportProduction['jumlah_sppb'],0) ?></a></td>
                        <?php if(!empty($reportProduction['avg_count_admin'])): ?>
                            <td><?= round(($reportProduction['jumlah_sppb']/$reportProduction['avg_count_admin'])/30 * 100,0) ?> %</td>
                        <?php else:?>
                            <td>0 %</td>
                        <?php endif; ?>
                        <td><?= @(round(@(if_empty($reportProduction['jumlah_sppb'],0)/$reportProduction['count_day'])/if_empty($reportProduction['avg_count_admin'],0),2)) ?></td>
                        <td><?= if_empty($reportProduction['jumlah_draft'],0)+if_empty($reportProduction['jumlah_sppb'],0) ?></td>
                        <td><a href="<?= site_url() ?>report/document-production-detail-comp?tahun=<?= $reportProduction['tahun'] ?>&minggu=<?=$reportProduction['minggu'] ?>" target="_blank"><?= if_empty($reportProduction['avg_count_admin'],0) ?></a></td>
                        <td><a href="<?= site_url() ?>report/document-production-detail-overtime?tahun=<?= $reportProduction['tahun'] ?>&minggu=<?=$reportProduction['minggu'] ?>" target="_blank"><?= if_empty($reportProduction['lembur'],0) ?></a> minute (<?= round(if_empty($reportProduction['lembur'],0)/60,2) ?> hour)</td>
                        
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5"> No Data Available </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
    $('#production').DataTable( {
        "order": [[ 0, "desc" ]]
    } );
} );
</script>