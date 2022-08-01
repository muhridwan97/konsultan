<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">View Component Price</h3>
	</div>
	<form class="form-horizontal form-view">
		<div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Branch</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $componentPrice['branch_name'] ?></p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-3">Customer</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['customer_name'], 'All Customer') ?></p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-3">Handling Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['handling_type_name'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Component</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['component_name'], '-') ?></p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-3">Effective Date</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= readable_date($componentPrice['effective_date']) ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Price Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $componentPrice['price_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Price Subtype</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $componentPrice['price_subtype'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Rules</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $componentPrice['rule'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Price</label>
                <div class="col-sm-9">
                    <p class="form-control-static">Rp. <?= numerical($componentPrice['price'], 3, true) ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Container Size</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['container_size'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Container Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['container_type'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Goods Unit</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['unit'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Status Danger</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['status_danger'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Status Empty</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['status_empty'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Status Condition</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['status_condition'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Min Value</label>
                <div class="col-sm-9">
                    <p class="form-control-static">&gt; <?= numerical($componentPrice['min_weight'], 3, true) ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Descriptions</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($componentPrice['description'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Status</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php
                        $statusLabel = [
                            'PENDING' => 'label-default',
                            'REJECTED' => 'label-danger',
                            'APPROVED' => 'label-success',
                        ];
                        ?>
                        <span class="label <?= $statusLabel[$componentPrice['status']] ?>">
                            <?= $componentPrice['status'] ?>
                        </span>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Validated By</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($componentPrice['validator_name'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Validated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($componentPrice['validated_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($componentPrice['created_at']) ?>
                        (<?= $componentPrice['creator_name'] ?>)
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($componentPrice['updated_at']) ?>
                    </p>
                </div>
            </div>
		</div>
		<div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
		</div>
	</form>
</div>