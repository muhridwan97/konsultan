$(function () {
    const formGoodsScanner = $('#form-goods');
    const buttonOpenCamera = formGoodsScanner.find('#btn-scan-code');
    const goodsWrapper = formGoodsScanner.find('#goods-wrapper');
    const goodsReadWrapper = formGoodsScanner.find('#goods-read-wrapper');

    const modalScanner = document.getElementById('modal-scanner');
    const scannerMessage = document.getElementById('camera-message');

    const video = document.createElement("video");
    const cameraPreview = document.getElementById('camera-preview');
    const cameraWrapper = document.getElementById('camera-wrapper');
    const canvas = cameraPreview.getContext("2d");

    const modalGoodsInput = $('#modal-goods-input');
    let stockChecking = false;

    /**
     * Trigger to open popup, then initialize media device,
     * if camera exist then it will ask permission.
     */
    buttonOpenCamera.on('click', function (e) {
        e.preventDefault();
        $(modalScanner).modal({
            backdrop: 'static',
            keyboard: false
        });
        initializeMedia();
    });

    /**
     * Close scanner popup, and stop video render from camera.
     */
    $(modalScanner).on('hide.bs.modal', function () {
        if (video.srcObject) {
            video.srcObject.getVideoTracks().forEach(function (track) {
                track.stop();
            });
        }
    });

    /**
     * initialize media device from client hardware,
     * check if the browser support and ask camera permission access,
     * if permission granted then stream image from camera into <video> element and play inline,
     * update frame from stream user device.
     */
    function initializeMedia() {
        if (!('mediaDevices' in navigator)) {
            navigator.mediaDevices = {};
        }
        if (!('getUserMedia' in navigator.mediaDevices)) {
            navigator.mediaDevices.getUserMedia = function (constraints) {
                let getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
                if (!getUserMedia) {
                    return Promise.reject(new Error('getUserMedia is not implemented!'));
                }
                return new Promise(function (resolve, reject) {
                    getUserMedia.call(navigator, constraints, resolve, reject);
                });
            }
        }

        navigator.mediaDevices.getUserMedia({video: {facingMode: "environment"}})
            .then(function (stream) {
                scannerMessage.style.display = 'none';
                cameraPreview.style.display = 'block';

                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(tick);
            })
            .catch(function () {
                cameraPreview.style.display = 'none';
                scannerMessage.style.display = 'block';
            });
    }

    /**
     * Create line in canvas to preview qr code, will called 4 times to make square.
     *
     * @param begin
     * @param end
     * @param color
     */
    function drawMarkerLine(begin, end, color) {
        canvas.beginPath();
        canvas.moveTo(begin.x, begin.y);
        canvas.lineTo(end.x, end.y);
        canvas.lineWidth = 4;
        canvas.strokeStyle = color;
        canvas.stroke();
    }

    /**
     * Update image preview from camera.
     */
    function tick() {
        const modalStockGoods = $('#modal-stock-goods');
        const bookingId = modalStockGoods.data('booking-id');
        const bookingIdOut = modalStockGoods.data('booking-id-out');

        scannerMessage.hidden = false;
        scannerMessage.innerText = "⌛ Loading camera...";

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            scannerMessage.hidden = true;
            cameraPreview.hidden = false;

            const videoHeight = video.videoHeight;
            const videoWidth = video.videoWidth;
            const wrapperWidth = cameraWrapper.offsetWidth;

            cameraPreview.height = videoHeight * wrapperWidth / videoWidth;
            cameraPreview.width = wrapperWidth;

            canvas.drawImage(video, 0, 0, cameraPreview.width || 1, cameraPreview.height || 1);
            const imageData = canvas.getImageData(0, 0, cameraPreview.width || 1, cameraPreview.height || 1);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code) {
                drawMarkerLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                drawMarkerLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                drawMarkerLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                drawMarkerLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
                if (code.data.trim() && !stockChecking) {
                    // goodsWrapper.show();
                    // goodsReadWrapper.hide();
                    goodsReadWrapper.find('#good_read').data("placeholder", "Fetching data...");

                    stockChecking = true;
                    $.ajax({
                        url: baseUrl + 'work_order/ajax_get_stock_by_scanner',
                        type: 'POST',
                        data: {
                            no_pallet: code.data,
                            id_booking : bookingId,
                            id_booking_out : bookingIdOut,
                        },
                        success: function (data) {
                            console.log(data);
                            if (data.status !== 'OK') {
                                goodsReadWrapper.find('#good_read').prop("placeholder", "No data...");
                                goodsWrapper.find('#goods').val('').trigger('change');
                                goodsWrapper.find('#goods').select2();
                                alert(data.status);
                            } else {
                                // goodsWrapper.find('#goods').val(row.id_goods).trigger('change');
                                // goodsWrapper.find('#goods').select2();
                                const row = data.goods;
                                row.name = row.goods_name;
                                goodsReadWrapper.find('#good_read').val(row.goods_name);
                                modalGoodsInput.find('#id_booking_reference').val(row.id_booking_reference || '');
                                modalGoodsInput.find('#position_blocks').val(row.position_blocks);
                                modalGoodsInput.find('#quantity').val();
                                modalGoodsInput.find('#unit').val(row.id_unit).trigger('change');
                                modalGoodsInput.find('#weight').val(setCurrencyValue(Number(row.unit_weight), '', ',', '.'));
                                modalGoodsInput.find('#gross_weight').val(setCurrencyValue(Number(row.unit_gross_weight), '', ',', '.'));
                                modalGoodsInput.find('#length').val(setCurrencyValue(Number(row.unit_length), '', ',', '.'));
                                modalGoodsInput.find('#width').val(setCurrencyValue(Number(row.unit_width), '', ',', '.'));
                                modalGoodsInput.find('#height').val(setCurrencyValue(Number(row.unit_height), '', ',', '.'));
                                modalGoodsInput.find('#volume').val(setCurrencyValue(Number(row.unit_volume), '', ',', '.'));
                                modalGoodsInput.find('#is_hold').val(row.is_hold).trigger('change');
                                modalGoodsInput.find('#status').val(row.status).trigger('change');
                                modalGoodsInput.find('#status_danger').val(row.status_danger).trigger('change');
                                modalGoodsInput.find('#ex_no_container').val(row.ex_no_container);
                                modalGoodsInput.find('#no_pallet').val(row.no_pallet);
                                modalGoodsInput.find('#description').val(row.description);

                                modalGoodsInput.find('#goods').data('data', row);
                                const selectGoods = modalGoodsInput.find('#goods');
                                const newOption = new Option(row.goods_name, row.id_goods, true, true);
                                selectGoods.append(newOption).trigger('change');

                                // set position
                                const dataPosition = {
                                    id: row.id_position,
                                    text: row.position,
                                };
                                const selectPosition = modalGoodsInput.find('#position');
                                if (selectPosition.find("option[value='" + dataPosition.id + "']").length) {
                                    selectPosition.val(dataPosition.id).trigger('change', ['script']);
                                } else {
                                    const newOption = new Option(dataPosition.text, dataPosition.id, true, true);
                                    selectPosition.append(newOption).trigger('change', ['script']);
                                }
                            }                            

                            setTimeout(() => {
                                $(modalScanner).modal('hide');
                            }, 500);

                            stockChecking = false;
                        },
                        error: function(xhr, status, error) {
                            stockChecking = false;
                            alert('Error: ' + xhr.responseText);
                        }  
                    });
                }
            }
        }
        requestAnimationFrame(tick);
    }

});