<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Stock Location</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter', 1) ? 'Hide' : 'Show' ?> Filter
            </a>
        </div>
    </div>

    <div class="box-body">
        <form role="form" method="get" class="form-filter need-validation" id="form-filter" <?= get_url_param('filter', 1) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="warehouse">Warehouse</label>
                        <select class="form-control select2" id="warehouse" name="warehouse" data-placeholder="Select warehouse" required style="width: 100%">
                            <option value="">ALL WAREHOUSES</option>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?= $wh['id'] ?>" <?= get_url_param('warehouse') == $wh['id'] ? 'selected' : '' ?>>
                                    <?= $wh['warehouse'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner">Owner</label>
                                <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                                    <select class="form-control select2 select2-ajax" style="width: 100%"
                                            data-url="<?= site_url('people/ajax_get_people') ?>"
                                            data-key-id="id" data-key-label="name" data-key-sublabel="no_person" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                            name="owner[]" id="owner"
                                            data-placeholder="Select owner" multiple>
                                        <option value=""></option>
                                        <?php foreach ($owners as $owner): ?>
                                            <option value="<?= $owner['id'] ?>" selected>
                                                <?= $owner['name'] ?> - <?= $owner['no_person'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= UserModel::authenticatedUserData('name') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="booking">Booking</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=INBOUND') ?>"
                                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                        name="booking[]" id="booking" multiple
                                        data-placeholder="Select booking inbound">
                                    <option value=""></option>
                                    <?php foreach($bookings as $booking): ?>
                                        <option value="<?= $booking['id'] ?>" selected>
                                            <?= $booking['no_reference'] ?> - <?= $booking['customer_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="search" value="<?= get_url_param('search') ?>" class="form-control" id="search" name="search" placeholder="Type query search">
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <div class="map-wrapper" id="map-wrapper">
            <div id="map-container" style="transform: scale(1); transform-origin: top left;">
                <?php
                function checkBorder($block, $map) {
                    static $blockMappingCollections = [];

                    $blockX = $block['position_x'];
                    $blockY = $block['position_y'];

                    $rowKey = $block['id_position'] . '-ROW-Y' . $blockY;
                    $colKey = $block['id_position'] . '-COL-X' . $blockX;

                    // cache result of same row
                    if (key_exists($rowKey, $blockMappingCollections)) {
                        $rowX = $blockMappingCollections[$rowKey];
                    } else {
                        $rowX = array_column(array_filter($map, function ($blockMap) use ($blockY) {
                            return $blockMap['position_y'] == $blockY;
                        }), 'position_x');
                        $blockMappingCollections[$rowKey] = $rowX;
                    }

                    // cache result of same column
                    if (key_exists($colKey, $blockMappingCollections)) {
                        $colY = $blockMappingCollections[$colKey];
                    } else {
                        $colY = array_column(array_filter($map, function ($blockMap) use ($blockX) {
                            return $blockMap['position_x'] == $blockX;
                        }), 'position_y');
                        $blockMappingCollections[$colKey] = $colY;
                    }

                    // check smallest x
                    $smallestX = min($rowX);

                    // check smallest y
                    $smallestY = min($colY);

                    // check largest x
                    $largestX = max($rowX);

                    // check largest y
                    $largestY = max($colY);

                    // check if coordinate in the edge
                    $isOuterOfShape = false;
                    $borders = [];

                    $leftSide = $blockX == $smallestX;
                    $topSide = $blockY == $smallestY;
                    $rightSide = $blockX == $largestX;
                    $bottomSide = $blockY == $largestY;

                    if ($leftSide || $topSide || $rightSide || $bottomSide) {
                        $isOuterOfShape = true;
                    }

                    if ($leftSide) $borders[] = 'left';
                    if ($topSide) $borders[] = 'top';
                    if ($rightSide) $borders[] = 'right';
                    if ($bottomSide) $borders[] = 'bottom';

                    return [
                        'is_outer_side' => $isOuterOfShape,
                        'borders' => $borders
                    ];
                }

                if (!empty($warehouse) && $warehouse['total_column'] > 0 && $warehouse['total_row'] > 0) {
                    $mapView = "<label class='block guide locked' id='block_guide_xy'>&nbsp;</label>";
                    for ($x = 1; $x <= $warehouse['total_column']; $x++) {
                        $mapView .= "<label class='block guide locked' id='guide_x{$x}'>{$x}</label>";
                    }
                    $mapView .= "<br>";
                    for ($y = 1; $y <= $warehouse['total_row']; $y++) {
                        $mapView .= "<label class='block guide locked' id='guide_y{$y}'>{$y}</label>";
                        for ($x = 1; $x <= $warehouse['total_column']; $x++) {
                            $style = '';
                            $border = '';
                            $color = '';
                            $tooltip = "data-toggle='tooltip' data-original-title='X{$x}-Y{$y}'";
                            $positionName = '';
                            $blockClass = 'block';
                            $positionLabelContent = '';
                            $stockContent = '';
                            foreach ($positionBlocks as $index => $block) {
                                if ($block['position_x'] == $x && $block['position_y'] == $y && $block['is_usable']) {
                                    $result = checkBorder($block, $positionMaps[$block['id_position']]);
                                    $color = "background-color: " . ($block['is_usable'] ? '#DFDFDF' : $block['color']) . ";";
                                    $blockClass = 'block active locked';
                                    $tooltip = "data-toggle='tooltip' data-original-title='{$block['position']} - X{$x}Y{$y}'";
                                    $positionName = $block['position'];

                                    if ($result['is_outer_side']) {
                                        foreach ($result['borders'] as $borderSide) {
                                            $border .= "border-{$borderSide}: 2px solid #FF8000;";
                                        }
                                    }
                                    $style = $color . ' ' . $border;

                                    if (key_exists($block['id_position'], $stockPositions)) {
                                        $stockQuantity = $stockPositions[$block['id_position']]['stock_quantity'];
                                        $blockClass .= ' block-relative';
                                        $params = set_url_param(['filter_summary_goods' => 1]) . '&position=' . $block['id_position'];
                                        $positionLabelContent = '<span class="block-quantity">' . $positionName . '</span>';
                                        $stockContent = '<a href="' . site_url('report/stock-summary?' . $params) . '" target="_blank" class="block-quantity" style="margin-top: 30px">STOCK: ' . numerical($stockQuantity, 2, true) . '</a>';

                                        unset($stockPositions[$block['id_position']]);
                                    }

                                    unset($positionBlocks[$index]);
                                }
                                if (!$block['is_usable']) {
                                    unset($positionBlocks[$index]);
                                }
                            }
                            $mapView .= "<label for='block_{$x}_{$y}' class='{$blockClass}' data-x='{$x}' data-y='{$y}' data-xy='{$x}-{$y}' data-position='{$positionName}' style='{$style}' {$tooltip}>&nbsp;<span class='block-information-wrapper' style='display: inline-block; transform: scale(1); transform-origin: top left;'>{$positionLabelContent}{$stockContent}<div></div></label>";
                        }
                        $mapView .= "<br>";
                    }
                    echo $mapView;
                } else {
                    echo "<span class='help-block text-center'>No data available</span>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($otherResults)): ?>
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title text-warning"><i class="fa fa-info-circle mr10"></i>Another data also found in other locations:</h3>
        </div>
        <div class="box-body">
            <div class="list-group" id="code-history">
                <a href="#" class="list-group-item disabled">
                    LOCATION
                </a>
                <?php foreach ($otherResults as $otherResult): ?>
                    <a href="<?= site_url('report-stock/stock-location?' . set_url_param(['warehouse' => $otherResult['id_warehouse']])) ?>" class="list-group-item list-history">
                        <strong><?= $otherResult['warehouse'] ?></strong> with total quantity <strong><?= numerical($otherResult['stock_quantity'], 3, true) ?></strong>
                        <i class="fa fa-arrow-right pull-right"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div style="position: fixed; right: 20px; bottom: 20px; z-index: 2">
    <div class="input-group">
        <span class="input-group-btn">
            <button type="button" class="btn btn-danger" id="btn-zoom-out" data-toggle="tooltip" data-title="Zoom Out">
                <i class="fa fa-search-minus"></i>
            </button>
        </span>
        <input type="text" class="form-control text-center" id="zoom-scale" value="1" aria-label="Zoom"
               data-toggle="tooltip" data-title="Click to reset 1x" readonly style="width: 50px; cursor: pointer">
        <span class="input-group-btn">
            <button type="button" class="btn btn-danger" id="btn-zoom-in" data-toggle="tooltip" data-title="Zoom In">
                <i class="fa fa-search-plus"></i>
            </button>
        </span>
    </div>
</div>

<script>
    const mapContainer = $('#map-container');
    const blockInformationWrapper = $('.block-information-wrapper');
    const blockActiveTiles = $('.block.active');
    const btnZoomIn = $('#btn-zoom-in');
    const btnZoomOut = $('#btn-zoom-out');
    const zoomScale = $('#zoom-scale');
    const scaleStep = 0.2;
    const scaleMax = 2;
    const scaleMin = 0.2;

    btnZoomIn.on('click', function () {
        let currentScale = parseFloat(zoomScale.val());
        if (currentScale < scaleMax) {
            currentScale += scaleStep;
        } else {
            currentScale = scaleMax;
        }
        setMapScale(currentScale);
    });

    btnZoomOut.on('click', function () {
        let currentScale = parseFloat(zoomScale.val());
        if (currentScale > scaleMin) {
            currentScale -= scaleStep;
        } else {
            currentScale = scaleMin;
        }
        setMapScale(currentScale);
    });

    zoomScale.on('click', function() {
        setMapScale(1);
    });

    function setMapScale(scaleValue) {
        zoomScale.val(parseFloat(Number(scaleValue).toFixed(1)));
        mapContainer.css('transform', 'scale(' + scaleValue.toFixed(1) + ')');
        if (scaleValue < 1) {
            // auto scale: blockInformationWrapper.css('transform', 'scale(' + (1 + (1.2 - scaleValue)).toFixed(1) + ')');
            let infoScale, borderScale = 2;
            if (scaleValue.toFixed(1) >= 0.8) {
                infoScale = 1.2;
            } else if (scaleValue.toFixed(1) >= 0.6) {
                infoScale = 1.6;
            } else if (scaleValue.toFixed(1) >= 0.4) {
                infoScale = 1.8;
                borderScale = 4;
            } else {
                infoScale = 2.5;
                borderScale = 6;
            }
            blockInformationWrapper.css('transform', 'scale(' + infoScale + ')');
            blockActiveTiles.css('border-width', borderScale);
        } else {
            blockInformationWrapper.css('transform', 'scale(1)');
            blockActiveTiles.css('border-width', 2);
        }
    }
</script>
