<div class="modal fade" tabindex="-1" role="dialog" id="modal-view-file">
    <div class="modal-dialog modal-lg" role="file">
        <div class="modal-content">
            <form action="<?= site_url("transporter_entry_permit/download_file/") ?>" role="form" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">View File</h4>
                </div>
                <div class="modal-body">
                    <div class="pull-right">
                        <input type="hidden" name="id_tep_req" id="id_tep_req" value="">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa ion-android-download" ></i> Download
                        </button>
                    </div>
                    <div id="file-viewer" class="text-center">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>