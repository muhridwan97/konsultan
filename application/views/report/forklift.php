<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Forklift Usage</h3>
        <div class="pull-right">
            <a href="#filter_forklift" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_forklift', 0) ? 'Hide' : 'Show' ?> Filter
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
        <?php $this->load->view('report/_filter_forklift', [
            'hidden' => isset($_GET['filter_forklift']) ? false : true
        ]) ?>
        
        <table class="table table-bordered table-striped no-wrap table-ajax" id="table-forklift">
            <thead>
            <tr>
                <th style="width: 25px" rowspan="2">No</th>
                <th rowspan="2" class="minggu">Minggu</th>
                <th colspan="3">Jakarta</th>
                <th colspan="3">Medan</th>
                <th colspan="3">Surabaya</th>
                <th rowspan="2">Total AVG of UTL</th>
                <th rowspan="2">Total AVG of TOTAL</th>
            </tr>
            <tr>
                <th >AVG of UTL</th>
                <th >AVG of TOTAL</th>
                <th >AVG TARG (%)</th>
                <th >AVG of UTL</th>
                <th >AVG of TOTAL</th>
                <th >AVG TARG (%)</th>
                <th >AVG of UTL</th>
                <th >AVG of TOTAL</th>
                <th >AVG TARG (%)</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
