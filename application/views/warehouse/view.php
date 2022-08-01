<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Warehouse</h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Warehouse Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $warehouse['warehouse'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Branch</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $warehouse['branch'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Columns (X)</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= numerical($warehouse['total_column'], 0) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Rows (Y)</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= numerical($warehouse['total_row'], 0) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Tiers (Z)</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">0</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $warehouse['type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($warehouse['description'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Positions</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if ($warehouse['total_position'] == 0): ?>
                                    No position available
                                <?php else: ?>
                                    <a href="<?= site_url('warehouse/position/' . $warehouse['id']) ?>">
                                        <?= numerical($warehouse['total_position'], 0) ?> positions
                                    </a>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($warehouse['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($warehouse['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="box box-primary mt20">
            <div class="box-header with-border">
                <h3 class="box-title">Map Block Positions</h3>
            </div>
            <div class="box-body">
                <div class="map-wrapper" id="map-wrapper">
                    <?php
                    if ($warehouse['total_column'] > 0 && $warehouse['total_row'] > 0) {
                        $mapView = "<label class='block guide locked' id='block_guide_xy'>&nbsp;</label>";
                        for ($x = 1; $x <= $warehouse['total_column']; $x++) {
                            $mapView .= "<label class='block guide locked' id='guide_x{$x}'>{$x}</label>";
                        }
                        $mapView .= "<br>";
                        for ($y = 1; $y <= $warehouse['total_row']; $y++) {
                            $mapView .= "<label class='block guide locked' id='guide_y{$y}'>{$y}</label>";
                            for ($x = 1; $x <= $warehouse['total_column']; $x++) {
                                $color = '';
                                $tooltip = "data-toggle='tooltip' data-original-title='X{$x}-Y{$y}'";
                                $blockClass = 'block';
                                foreach ($positionBlocks as $index => $block) {
                                    if ($block['position_x'] == $x && $block['position_y'] == $y) {
                                        $color = 'background-color: ' . $block['color'];
                                        $blockClass = 'block active locked';
                                        $tooltip = "data-toggle='tooltip' data-original-title='{$block['position']}'";
                                        unset($positionBlocks[$index]);
                                    }
                                }
                                $mapView .= "<label for='block_{$x}_{$y}' class='{$blockClass}' data-x='{$x}' data-y='{$y}' data-xy='{$x}-{$y}' style='{$color}' {$tooltip}>&nbsp;</label>";
                            }
                            $mapView .= "<br>";
                        }
                        echo $mapView;
                    } else {
                        echo "<span class='text-danger'>Total block coordinate is not set</span>";
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
    <div class="box-footer">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
    </div>
</div>