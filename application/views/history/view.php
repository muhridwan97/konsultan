<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Data History</h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <?php foreach ($statusHistory['data'] as $title => $datum): ?>
                <?php if(is_array($datum)): ?>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <?= strtoupper(str_replace(['-', '_'], ' ', $title)) ?>
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <?php foreach ($datum as $key => $value): ?>
                                    <?php if(is_array($value)): ?>
                                        <div class="col-12">
                                            <div class="box box-primary">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">
                                                        <?= strtoupper(str_replace(['-', '_'], ' ', (is_numeric($key) ? 'Data' . ($key + 1) : $key))) ?>
                                                    </h3>
                                                </div>
                                                <div class="box-body">
                                                    <div class="row">
                                                        <?php foreach ($value as $innerKey => $innerValue): ?>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-sm-4">
                                                                        <?= ucwords(str_replace(['-', '_'], ' ', $innerKey)) ?>
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <p class="form-control-static">
                                                                            <?= if_empty(is_numeric($innerValue) ? numerical($innerValue) : $innerValue, '-') ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-sm-4">
                                                    <?= ucwords(str_replace(['-', '_'], ' ', $key)) ?>
                                                </label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-static">
                                                        <?= if_empty(is_numeric($value) ? numerical($value) : $value, '-') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-5">
                                    <?= strtoupper(str_replace(['-', '_'], ' ', $title)) ?>
                                </label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">
                                        <?= $datum ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>