<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Request Priority</h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Goods</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $tepRequestUploads[0]['goods_name'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Unit</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $tepRequestUploads[0]['unit'] ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Request Items</h3>
            </div>
            <div class="box-body">
                <?php $this->load->view('transporter_entry_permit_request_priority/_table_request_item') ?>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>