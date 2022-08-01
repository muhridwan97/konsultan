<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Performance</h3>
        <div class="pull-right">
            <a href="#filter_performance" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_performance', 0) ? 'Hide' : 'Show' ?> Filter
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
        <?php $this->load->view('report/_filter_performance', [
            'hidden' => isset($_GET['filter_performance']) ? false : true
        ]) ?>

        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-performance">
            <thead>
            <tr>
                <th style="width: 25px" rowspan="2">No</th>
                <th rowspan="2" class="minggu">Minggu</th>
                <th colspan="7" >Jakarta</th>
                <th colspan="7" >Medan</th>
                <th colspan="7">Surabaya</th>
                <th rowspan="2">Total AVG of FRT</th>
                <th rowspan="2">Total AVG of OPS</th>
            </tr>
            <tr>
                <th class="frt">AVG of FRT</th>
                <th class="ops">AVG of OPS</th>
                <th class="ops">AVG of FRT/OPS</th>
                <th class="ops">TARG (%)</th>
                <th class="ops">AVG of OPS CORE</th>
                <th class="ops">AVG of FRT/OPS CORE</th>
                <th class="ops">TARG CORE(%)</th>
                <th class="frt">AVG of FRT</th>
                <th class="ops">AVG of OPS</th>
                <th class="ops">AVG of FRT/OPS</th>
                <th class="ops">TARG (%)</th>
                <th class="ops">AVG of OPS CORE</th>
                <th class="ops">AVG of FRT/OPS CORE</th>
                <th class="ops">TARG CORE(%)</th>
                <th class="frt">AVG of FRT</th>
                <th class="ops">AVG of OPS</th>
                <th class="ops">AVG of FRT/OPS</th>
                <th class="ops">TARG (%)</th>
                <th class="ops">AVG of OPS CORE</th>
                <th class="ops">AVG of FRT/OPS CORE</th>
                <th class="ops">TARG CORE(%)</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
