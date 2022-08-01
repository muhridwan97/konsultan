<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Item</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Item Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $itemCompliance['item_name'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">No HS</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $itemCompliance['no_hs'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Unit</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $itemCompliance['unit'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Customer</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $itemCompliance['customer_name'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($itemCompliance['description'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($itemCompliance['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($itemCompliance['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Photo File</h4>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th>Photo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1;
                        foreach ($photos as $photo): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><a data-toggle="collapse" data-target="#coll_<?= $photo['id'] ?>" target="_blank"><?= $photo['photo'] ?></a>
                                <div id="coll_<?= $photo['id'] ?>" class="collapse">
                                    <a href="<?= $photo['url'] ?>" target="_blank"><img class="img-responsive pad" width="200" height="200" src="<?= $photo['url'] ?>" alt="Photo"></a>
                                </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (count($photos) <= 0): ?>
                            <tr>
                                <td colspan="2">No photo available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Reference Upload</h4>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th>No Upload</th>
                            <th>Customer</th>
                            <th>Branch</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1;
                        foreach ($uploads as $upload): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><a href="<?= base_url(). 'p/' .$upload['id_branch']. '/upload/view/'.$upload['id'] ?>" target="_blank"><?= $upload['no_upload'] ?></a></td>
                                <td><?= $upload['name'] ?></td>
                                <td><?= $upload['branch_name'] ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (count($uploads) <= 0): ?>
                            <tr>
                                <td colspan="4">No photo available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>