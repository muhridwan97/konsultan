<div class="box box-primary" id="table-safe-conduct">
    <div class="box-header with-border">
        <h3 class="box-title">View Safe Conduct Group</h3>
    </div>

    <div class="box-body"><form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Safe Conduct</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConductGroup['no_safe_conduct_group'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Total Safe Conduct</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConductGroup['total_safe_conduct'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConductGroup['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($safeConductGroup['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Safe Conduct Group</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-work-order">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>No Safe Conduct</th>
                        <th>No Booking</th>
                        <th>Customer</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($safeConducts as $index => $relatedSafeConduct): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <a href="<?= site_url('safe-conduct/view/' . $relatedSafeConduct['id']) ?>">
                                    <?= $relatedSafeConduct['no_safe_conduct'] ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= site_url('booking/view/' . $relatedSafeConduct['id_booking']) ?>">
                                    <?= $relatedSafeConduct['no_booking'] ?>
                                </a>
                                <small class="text-muted"><?= $relatedSafeConduct['no_reference'] ?></small>
                            </td>
                            <td><?= $relatedSafeConduct['customer_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary">
            Back
        </a>
    </div>
</div>