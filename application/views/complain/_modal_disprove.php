<div class="modal fade" tabindex="-1" role="dialog" id="modal-disprove">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?=base_url('complain/disprove')?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><span class="disprove-title"></span></h4>
                </div>
                <input type="hidden" id="id_complain" name="id_complain">
                <input type="hidden" id="disprove" name="disprove">
                <div class="modal-body">
                    <div class="form-group" id="note-wrapper">
                        <label for="note" id="label-note">Note</label>
                        <textarea class="form-control" id="note" name="note" placeholder="Enter note" maxlength="500"><?= set_value('note') ?></textarea>
                    </div>
                    <div style="display: none;" id="rating-wrapper">
                        <div class="form-group">
                            <label for="rating"> Rating :</label>
                            <div>
                                <div class="checkbox-inline mt-2 mb-0">
                                    <label class="form-check-label mb-0">
                                        <input type="radio" class="form-check-input" name="rating" id="rate_poor" value="1"> Poor
                                    </label>
                                </div>
                                <div class="checkbox-inline mt-2 mb-0">
                                    <label class="form-check-label mb-0">
                                        <input type="radio" class="form-check-input" name="rating" id="rate_bad" value="2"> Bad
                                    </label>
                                </div>
                                <div class="checkbox-inline mt-2 mb-0">
                                    <label class="form-check-label mb-0">
                                        <input type="radio" class="form-check-input" name="rating" id="rate_fair" value="3"> Fair
                                    </label>
                                </div>
                                <div class="checkbox-inline mt-2 mb-0">
                                    <label class="form-check-label mb-0">
                                        <input type="radio" class="form-check-input" name="rating" id="rate_good" value="4"> Good
                                    </label>
                                </div>
                                <div class="checkbox-inline mt-2 mb-0">
                                    <label class="form-check-label mb-0">
                                        <input type="radio" class="form-check-input" name="rating" id="rate_very_good" value="5"> Very Good
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reason">Rating Reason</label>
                            <textarea class="form-control" id="reason" name="reason" placeholder="Enter reason" maxlength="500"><?= set_value('reason') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="file-upload-default" data-max-size="3000000">
                        <div class="input-group col-xs-12">
                            <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" readonly placeholder="Upload attachment">
                            <span class="input-group-btn">
                                <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">Upload</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" data-toggle="one-touch" data-touch-message="Submit..." class="btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>