$(function () {
    const btnTryScannerAgain = document.getElementById('btn-try-again');

    const modalTakePhoto = document.getElementById('modal-take-photo');
    const scannerMessage =  modalTakePhoto.querySelector('#camera-message');
    const btnBrowsePhoto = $(modalTakePhoto).find('#btn-browse-photo');
    const btnCapturePhoto = $(modalTakePhoto).find('#btn-capture-photo');
    const btnToggleFlashlight = $(modalTakePhoto).find('#btn-toggle-flashlight');
    const btnConfirmPhoto = $(modalTakePhoto).find('#btn-confirm-photo');
    const btnRetry = $(modalTakePhoto).find('#btn-retry');

    let track;
    let deviceCapabilities;
    let imageCapture;
    let isTorchOn = false; // some device cannot get current torch status, we create variable helper
    let capturedPhotoFile;

    const video = document.createElement("video");
    const cameraPreview = modalTakePhoto.querySelector('#camera-preview');
    const cameraWrapper = modalTakePhoto.querySelector('#camera-wrapper');
    const canvas = cameraPreview.getContext("2d");

    btnTryScannerAgain.addEventListener('click', function () {
        location.reload();
    });

    let onPhotoTaken = () => {
        console.log('On photo taken');
    }
    let onPhotoBrowsed = () => {
        console.log('On photo browsed');
    }

    window.openModalTakePhoto = function (_onPhotoTaken, _onPhotoBrowsed) {
        $(modalTakePhoto).modal({
            backdrop: 'static',
            keyboard: false
        });
        capturedPhotoFile = null;
        preCapturePhoto();

        initializeMedia();

        if (_onPhotoTaken) {
            onPhotoTaken = _onPhotoTaken;
        }

        if (_onPhotoBrowsed) {
            onPhotoBrowsed = _onPhotoBrowsed;
        }
    }

    $(modalTakePhoto).on('hide.bs.modal', function () {
        if (video.srcObject) {
            video.srcObject.getVideoTracks().forEach(function (track) {
                track.stop();
            });
        }
    });

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

                // get the active track of the stream
                track = stream.getVideoTracks()[0];
                video.addEventListener('loadedmetadata', (e) => {
                    window.setTimeout(() => {
                        if (track.getCapabilities) {
                            deviceCapabilities = track.getCapabilities();
                            if (ImageCapture) {
                                imageCapture = new ImageCapture(track);
                                console.log(imageCapture.track.getSettings());
                                imageCapture.getPhotoCapabilities().then(console.log);
                            }
                        } else {
                            btnToggleFlashlight.prop('disabled', true);
                        }
                    }, 500);
                });
            })
            .catch(function () {
                cameraPreview.style.display = 'none';
                scannerMessage.style.display = 'block';
                btnCapturePhoto.hide();
                btnToggleFlashlight.hide();
            });
    }

    function tick() {
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
        }
        requestAnimationFrame(tick);
    }

    btnCapturePhoto.on('click', function () {
        if (imageCapture && false) {
            // take photo from ImageCapture API
            imageCapture.takePhoto({
                fillLightMode: 'off', //flash option is buggy
                imageHeight: 1500,
                imageWidth: 2000,
            }).then(capturedPhoto => {
                video.pause();
                //turnOffFlashLight();

                capturedPhotoFile = capturedPhoto

                const img = new Image();
                img.onload = function () {
                    canvas.drawImage(img, 0, 0, cameraPreview.width || 1, cameraPreview.height || 1);
                }
                img.src = URL.createObjectURL(capturedPhoto);
                //onPhotoTaken(capturedPhoto, URL.createObjectURL(capturedPhoto), modalTakePhoto);
            }).catch(console.log);
        } else {
            video.pause();
            // take frame (capture from canvas)
            turnOffFlashLight();

            const jpegImage = cameraPreview.toDataURL("image/jpeg"); //.split(';base64,')[1];
            //const jpegBlob = cameraPreview.toBlob();

            const blobBin = atob(jpegImage.split(',')[1]);
            const arrayData = [];
            for (let i = 0; i < blobBin.length; i++) {
                arrayData.push(blobBin.charCodeAt(i));
            }
            const file = new Blob([new Uint8Array(arrayData)], {type: 'image/jpeg'});

            capturedPhotoFile = file;
            //onPhotoTaken(file, jpegImage, modalTakePhoto);
        }
        postCapturePhoto();

        /* submit image to server
        const formData = new FormData();
        formData.append("file", file, 'image.jpg');
        $.ajax({
            url: baseUrl + 'upload-document-file/upload',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
        }).done(function(respond){
            video.play();
        });
        */
    });

    btnRetry.on('click', function () {
        video.play();
        preCapturePhoto();
    });

    btnConfirmPhoto.on('click', function () {
        if (capturedPhotoFile) {
            onPhotoTaken(capturedPhotoFile, URL.createObjectURL(capturedPhotoFile), modalTakePhoto);
        }
    });

    btnBrowsePhoto.on('click', function () {
        turnOffFlashLight();
        onPhotoBrowsed(modalTakePhoto);
    });

    btnToggleFlashlight.on('click', function () {
        if (track && imageCapture && deviceCapabilities.torch) {
            track.applyConstraints({
                advanced: [{
                    torch: /*!imageCapture.track.getSettings().torch*/ !isTorchOn
                }]
            }).catch(e => console.log(e));

            if (/*imageCapture.track.getSettings().torch*/ isTorchOn) {
                $(this).find('.flashlight-label').text('On');
                isTorchOn = false;
            } else {
                $(this).find('.flashlight-label').text('Off');
                isTorchOn = true;
            }
        } else {
            alert("Your device does not support ImageCapture API")
        }
    });

    function preCapturePhoto() {
        btnCapturePhoto.show();
        btnToggleFlashlight.show();
        btnConfirmPhoto.hide();
        btnRetry.hide();
    }

    function postCapturePhoto() {
        btnCapturePhoto.hide();
        btnToggleFlashlight.hide();
        btnConfirmPhoto.show();
        btnRetry.show();
    }

    function turnOffFlashLight() {
        $(modalTakePhoto).find('.flashlight-label').text('On');
        if (track && imageCapture && deviceCapabilities.torch) {
            if (/*imageCapture.track.getSettings().torch*/ isTorchOn) {
                track.applyConstraints({
                    advanced: [{
                        torch: false
                    }]
                })
                    .catch(e => console.log(e));
            }
            isTorchOn = false;
        }
    }
});