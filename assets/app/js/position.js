$(function () {
    var tableAuction = $('#table-position.table-ajax');
    var controlTemplate = $('#control-position-template').html();
    tableAuction.DataTable({
        language: {
            processing: "Loading...",
            searchPlaceholder: "Search position"
        },
        pageLength: 25,
        serverSide: true,
        processing: true,
        ajax: baseUrl + 'position/position-data',
        order: [[0, "desc"]],
        columns: [
            {
                class: 'responsive-hide',
                data: 'no'
            },
            {
                class: 'responsive-title',
                data: 'warehouse'
            },
            {data: 'name'},
            {data: 'position'},
            {data: 'description'},
            {data: 'id'}
        ],
        columnDefs: [{
            targets: ['type-action'],
            render: function (data, type, full) {
                return controlTemplate
                    .replace(/{{id}}/g, full.id)
                    .replace(/{{position}}/g, full.position);
            }
        }, {
            targets: '_all',
            render: function (data, type, full, meta) {
                return $.trim(data) == '' ? '-' : data;
            }
        }]
    });

    const formPosition = $('#form-position');
    const selectWarehouse = formPosition.find('#warehouse');
    const selectPositionType = formPosition.find('#position_type');
    const mapWrapper = formPosition.find('#map-wrapper');

    let positionId = formPosition.find('input#id').val();
    let totalColumns = 0;
    let totalRows = 0;
    let colorBlock = '';

    if (formPosition.hasClass('edit')) {
        setTimeout(function () {
            selectWarehouse.trigger('change');
        }, 300);
    }

    selectWarehouse.on('change', function () {
        const warehouseId = $(this).val();
        colorBlock = selectPositionType.find('option:selected').data('color') || '';

        mapWrapper.html('Fetching map block...');
        $.get(`${baseUrl}warehouse/ajax-get-warehouse-map?id_warehouse=${warehouseId}`, function (data) {
            totalColumns = data.warehouse.total_column || 0;
            totalRows = data.warehouse.total_row || 0;

            if (totalColumns > 0 && totalRows > 0) {
                let blocksView = '';

                // create x guide
                blocksView += `<label class="block guide locked" id="block_guide_xy">&nbsp;</label>`;
                for (let x = 1; x <= totalColumns; x++) {
                    blocksView += `<label class="block guide locked" id="guide_x${x}">${x}</label>`;
                }
                blocksView += '<br>';

                // loop through to create rows
                for (let y = 1; y <= totalRows; y++) {
                    // first create y guide
                    blocksView += `<label class="block guide locked" id="guide_y${y}">${y}</label>`;

                    // loop through to create columns per row
                    for (let x = 1; x <= totalColumns; x++) {

                        // check if position block is attached already from positions data
                        let color = '';
                        let blockClass = '';
                        let tooltip = ` data-toggle='tooltip' data-original-title='X${x}-Y${y}'`;
                        let checkboxInput = `<input type="checkbox" name="blocks[]" id="block_${x}_${y}" value="X${x}-Y${y}">`;
                        for (let pos of data.positions) {
                            const blockFound = pos.blocks.find(block => {
                                return block['position_x'] == x && block['position_y'] == y;
                            });
                            if (blockFound) {
                                tooltip = ` data-toggle='tooltip' data-original-title='${pos.position}'`;
                                color = `background-color: ${pos.color}`;
                                blockClass = 'active locked';

                                // unlock position blocks when edit position
                                if (positionId == pos.id) {
                                    tooltip = '';
                                    blockClass = 'active';
                                    checkboxInput = `<input checked type="checkbox" name="blocks[]" id="block_${x}_${y}" value="X${x}-Y${y}">`;
                                }

                                break;
                            }
                        }

                        blocksView += `<label for="block_${x}_${y}" class="block ${blockClass}" data-x="${x}" data-y="${y}" data-xy="${x}-${y}" style="${color}"${tooltip}>&nbsp;${checkboxInput}</label>`;
                    }
                    // move down for the next row
                    blocksView += '<br>';
                }
                mapWrapper.html(blocksView);
                if (formPosition.hasClass('edit')) {
                    //drawGuideLine();
                }
                $('[data-toggle="tooltip"]').tooltip({container: 'body'});
            } else {
                mapWrapper.html('<span class="text-danger">Total block coordinate is not set</span>');
            }
        });
    });

    let activeDrag = false;
    mapWrapper.on('mousedown', function (e) {
        if (!activeDrag) {
            if (selectPositionType.val()) {
                let xStart = e.pageX + mapWrapper.scrollLeft() - mapWrapper.offset().left;
                let yStart = e.pageY + mapWrapper.scrollTop() - mapWrapper.offset().top;
                let width = 0;
                let height = 0;

                if (mapWrapper.find('.selectable-helper').length <= 0) {
                    mapWrapper.append(`<div class="selectable-helper" style="left: ${xStart}px; top: ${yStart}px; width: ${width}px; height: ${height}px"></div>`)
                }

                const selectHelper = mapWrapper.find('.selectable-helper');

                let lastX = xStart;
                let lastY = yStart;

                mapWrapper.on('mousemove', function (e) {
                    if (!activeDrag) {
                        resetBlockSelection();
                        activeDrag = true;
                    }

                    const xEnd = e.pageX + mapWrapper.scrollLeft() - mapWrapper.offset().left;
                    const yEnd = e.pageY + mapWrapper.scrollTop() - mapWrapper.offset().top;
                    width = xEnd - xStart - 5;
                    height = yEnd - yStart;

                    if (width < 0) {
                        xStart = xEnd;
                        width = lastX - xEnd;
                    } else {
                        xStart = lastX;
                    }

                    if (height < 0) {
                        yStart = yEnd;
                        height = lastY - yEnd;
                    } else {
                        yStart = lastY;
                    }

                    selectHelper.css('left', xStart).css('top', yStart).css('width', width).css('height', height);

                    let limitSearchLeft = Math.ceil(xStart / 30) - 5;
                    if (limitSearchLeft < 1) {
                        limitSearchLeft = 1;
                    }

                    let limitSearchRight = Math.ceil(xEnd / 30) + 5;
                    if (limitSearchRight > totalColumns) {
                        limitSearchRight = totalColumns;
                    }

                    let limitSearchBottom = Math.ceil(yEnd / 30) + 5;
                    if (limitSearchBottom > totalRows) {
                        limitSearchBottom = totalRows;
                    }

                    let limitSearchTop = Math.ceil(yStart / 30) - 5;
                    if (limitSearchTop < 1) {
                        limitSearchTop = 1;
                    }

                    for (let y = limitSearchTop; y <= limitSearchBottom; y++) {
                        for (let x = limitSearchLeft; x <= limitSearchRight; x++) {
                            const block = $(`.block[data-xy="${x}-${y}"]`);
                            if (!block.hasClass('locked')) {
                                if (overlaps(block, selectHelper)) {
                                    block.find('input[type="checkbox"]').prop('checked', true);
                                    block.addClass('active');
                                    if (colorBlock) {
                                        block.css('background-color', colorBlock.toString());
                                    }
                                } else {
                                    block.find('input[type="checkbox"]').prop('checked', false);
                                    block.removeAttr('style');
                                    block.removeClass('active');
                                }
                            }
                        }
                    }
                    //drawGuideLine();
                });
            } else {
                alert('Select position type, first');
            }
        }
    });

    mapWrapper.on('mouseup', function () {
        activeDrag = false;
        mapWrapper.off('mousemove');
        mapWrapper.find('.selectable-helper').remove();
    });

    mapWrapper.on('click', '.block', function (e) {
        e.preventDefault();
        if ($(e.target).is('label') && !$(e.target).hasClass('locked') && selectPositionType.val()) {
            colorBlock = selectPositionType.find('option:selected').data('color');

            if (!$(this).hasClass('active')) {
                const x = $(this).data('x');
                const y = $(this).data('y');

                const isFirstBlock = mapWrapper.find('.active').length === 0;
                const hasTop = $(`[data-xy="${x}-${y - 1}"]`).not('.locked').hasClass('active');
                const hasBellow = $(`[data-xy="${x}-${y + 1}"]`).not('.locked').hasClass('active');
                const hasLeft = $(`[data-xy="${x - 1}-${y}"]`).not('.locked').hasClass('active');
                const hasRight = $(`[data-xy="${x + 1}-${y}"]`).not('.locked').hasClass('active');

                if (!isFirstBlock && (!hasTop && !hasBellow && !hasLeft && !hasRight)) {
                    resetBlockSelection();
                }

                $(this).addClass('active');
                $(this).find('input[type="checkbox"]').prop('checked', true);
                if (colorBlock) {
                    $(this).css('background-color', colorBlock.toString());
                }
            } else {
                $(this).removeAttr('style');
                $(this).removeClass('active');
                $(this).find('input[type="checkbox"]').prop('checked', false);
            }
            //drawGuideLine();
        }
    });

    function resetBlockSelection() {
        mapWrapper.find('.guide').removeClass('active');
        mapWrapper.find('.hover').removeClass('hover');
        mapWrapper.find('.block.active').not('.locked').removeAttr('style');
        mapWrapper.find('.block.active').not('.locked').find('input[type="checkbox"]').prop('checked', false);
        mapWrapper.find('.block').not('.locked').removeClass('active');
    }

    function drawGuideLine() {
        mapWrapper.find('.hover').removeClass('hover');
        mapWrapper.find('.guide').removeClass('active');

        const activeBlocks = mapWrapper.find('.block.active').not('.locked');
        Array.from(activeBlocks || []).forEach(block => {
            const x = $(block).data('x');
            const y = $(block).data('y');

            mapWrapper.find('#guide_x' + x).addClass('active');
            mapWrapper.find('#guide_y' + y).addClass('active');

            if (x > 1) {
                for (let i = x; i > 0; i--) {
                    mapWrapper.find(`.block[data-xy=${i}-${y}]`).not('.active').addClass('hover');
                }
            }
            if (y > 1) {
                for (let j = y; j > 0; j--) {
                    mapWrapper.find(`.block[data-xy=${x}-${j}]`).not('.active').addClass('hover');
                }
            }
        });
    }

    selectPositionType.on('change', function () {
        if (selectPositionType.val()) {
            colorBlock = selectPositionType.find('option:selected').data('color');
            mapWrapper.find('.block.active').not('.locked').css('background-color', colorBlock.toString());
        }
    });

    const overlaps = (function () {
        function getPositions(elem) {
            let pos, width, height;
            pos = $(elem).position();
            width = $(elem).width();
            height = $(elem).height();
            return [[pos.left, pos.left + width], [pos.top, pos.top + height]];
        }

        function comparePositions(p1, p2) {
            let r1, r2;
            r1 = p1[0] < p2[0] ? p1 : p2;
            r2 = p1[0] < p2[0] ? p2 : p1;
            return r1[1] > r2[0] || r1[0] === r2[0];
        }

        return function (a, b) {
            let pos1 = getPositions(a),
                pos2 = getPositions(b);
            return comparePositions(pos1[0], pos2[0]) && comparePositions(pos1[1], pos2[1]);
        };
    })();

});