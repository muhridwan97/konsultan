<div class="modal fade modal-scanner" id="modal-take-photo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="text-center pt10" id="camera-wrapper">
                    <div id="camera-message" style="border: 1px solid #ddd; margin: 20px 0; padding: 25px; border-radius: 5px">
                        <h1 class="fa ion-videocamera" style="font-size: 36px"></h1>
                        <p class="small">No cameras or insufficient permission.</p>
                        <button type="button" class="btn btn-sm" id="btn-try-again">Refresh the Page</button>
                    </div>
                    <canvas id="camera-preview" class="mt20" style="display:none; width: auto; height: auto; border-radius: 5px"></canvas>
                </div>
                <div class="text-center mt20">
                    <button class="btn btn-primary" id="btn-browse-photo">
                        <i class="fa fa-folder"></i>
                    </button>
                    <button class="btn btn-danger" id="btn-toggle-flashlight">
                        <i class="fa fa-flash mr10"></i><span class="flashlight-label">On</span>
                    </button>
                    <button class="btn btn-success" id="btn-capture-photo">
                        <i class="fa fa-camera mr10"></i>Capture
                    </button>
                    <button class="btn btn-danger" id="btn-retry" style="display: none">
                        <i class="fa fa-repeat"></i>
                    </button>
                    <button class="btn btn-success" id="btn-confirm-photo" style="display: none">
                        <i class="fa fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>