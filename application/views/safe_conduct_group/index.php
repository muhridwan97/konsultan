<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Safe Conduct Group Data</h3>
        <a href="<?= site_url('safe-conduct') ?>" class="btn btn-info pull-right">
            View Safe Conduct
        </a>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-safe-conduct-group">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Safe Conduct Group</th>
                <th>Total Member</th>
                <th class="type-safe-conduct">Safe Conduct</th>
                <th class="type-date-time">Created At</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-safe-conduct-group-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>
            <li>
                <a href="<?= site_url('safe-conduct-group/view/{{id}}') ?>">
                    <i class="fa ion-search"></i> View
                </a>
            </li>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/safe-conduct-group.js') ?>" defer></script>