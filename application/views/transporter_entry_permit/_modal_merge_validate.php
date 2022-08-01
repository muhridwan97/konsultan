<div class="modal fade" tabindex="-1" role="dialog" id="modal-merge-validate">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add Merge</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 2px">Are you sure want to merge this request with TEP code <span id="label-tep-code"></span> ?</p>
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_tep" id="id_tep">
                    <input type="hidden" name="id_customer" id="id_customer">
                    <input type="hidden" name="id_upload" id="id_upload">
                    <input type="hidden" name="slot" id="slot">
                    <input type="hidden" name="slot_created" id="slot_created">
                    <input type="hidden" name="armada" id="armada">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-merge-validate" data-toggle="one-touch">Merge Request</button>
                </div>
            </form>
        </div>
    </div>
</div>