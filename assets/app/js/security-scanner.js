$(function () {
    const formSecurityScanner = $('#form-security-scanner');
    const inputCode = formSecurityScanner.find('#code');
    const buttonEditNumber = formSecurityScanner.find('#btn-edit-number');
    const buttonOpenCamera = formSecurityScanner.find('#btn-scan-code');

    const modalScanner = formSecurityScanner.find('#modal-scanner');
    const btnCloseScanner = formSecurityScanner.find('.close');
    const scannerMessage = formSecurityScanner.find('#camera-message');
    const video = document.createElement("video");
    const cameraPreview = formSecurityScanner.find('#camera-preview');
    const canvas = cameraPreview.get(0).getContext("2d");

    const body = $('body');

    /**
     * url: /transporter/security-check?code=:no_transporter
     * Toggle input to enable input form when camera does not exist in device.
     */
    buttonEditNumber.on('click', function () {
        if (inputCode.prop('readonly')) {
            inputCode.prop('readonly', false);
            buttonEditNumber.find('i')
                .removeClass('fa-edit')
                .addClass('fa-lock');
        } else {
            inputCode.prop('readonly', true);
            buttonEditNumber.find('i')
                .addClass('fa-edit')
                .removeClass('fa-lock');
        }
    });

    /**
     * Trigger to open popup and set into fullscreen element, after that initialize media device,
     * if camera exist then it will ask permission.
     */
    buttonOpenCamera.on('click', function () {
        modalScanner.show();
        body.css('overflow', 'hidden');
        toggleFullscreen(true);
        initializeMedia();
    });

    /**
     * Close scanner popup, and stop video render from camera,
     * close fullscreen mode as well.
     */
    btnCloseScanner.on('click', function () {
        body.css('overflow', 'auto');
        toggleFullscreen(false);
        modalScanner.hide();
        if (video.srcObject) {
            video.srcObject.getVideoTracks().forEach(function (track) {
                track.stop();
            });
        }
    });

    /**
     * Fullscreen can be exit with ESC key (browser's feature),
     * force to close popup as well because we want to open camera in fullscreen mode (optional),
     * if this event does not exist, scanner popup will keep open but in standard mode.
     */
    document.addEventListener("fullscreenchange", function () {
        if (!isFullscreen()) {
            btnCloseScanner.click();
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

        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                }
            })
            .then(function (stream) {
                scannerMessage.hide();
                cameraPreview.show();

                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(tick);
            })
            .catch(function () {
                cameraPreview.hide();
                scannerMessage.show();
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
        scannerMessage.show();
        scannerMessage.innerText = "⌛ Loading camera...";

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            scannerMessage.hide();
            cameraPreview.show();

            cameraPreview.height = video.videoHeight / 4;
            cameraPreview.width = video.videoWidth;
            canvas.drawImage(video, 0, 0, cameraPreview.width, cameraPreview.height);
            const imageData = canvas.getImageData(0, 0, cameraPreview.width, cameraPreview.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            if (code) {
                drawMarkerLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                drawMarkerLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                drawMarkerLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                drawMarkerLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
                if (code.data.trim()) {
                    inputCode.val(code.data);
                    setTimeout(() => {
                        modalScanner.hide();
                        formSecurityScanner.submit();
                    }, 1000);
                }
            }
        }
        requestAnimationFrame(tick);
    }

    /**
     * Check if there is element in fullscreen mode.
     * @returns {Element | *}
     */
    function isFullscreen() {
        return document.fullscreenElement ||
            document.webkitFullscreenElement ||
            document.mozFullScreenElement ||
            document.msFullscreenElement;
    }

    /**
     * Toggle element scanner in fullscreen mode or normal mode.
     * @param fullscreen
     */
    function toggleFullscreen(fullscreen) {
        const element = modalScanner.get(0);
        if (!fullscreen || isFullscreen()) {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        } else {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        }
    }

});