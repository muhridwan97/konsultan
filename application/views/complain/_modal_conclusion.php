<div class="modal fade" tabindex="-1" role="dialog" id="modal-conclusion">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?=base_url('complain/conclusion')?>" method="post" enctype="multipart/form-data" id="conclusion-form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Conclusion <span class="conclusion-title"></span></h4>
                </div>
                <input type="hidden" id="id_complain" name="id_complain">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="conclusion">Conclusion</label>
                        <textarea class="form-control" id="conclusion" required name="conclusion" placeholder="Enter conclusion"><?= set_value('conclusion') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="email_to">Email To</label>
                        <textarea id="email_to" name="email_to" class="form-control" rows="2" placeholder="Email To" data-bv-emailaddress-multiple="true" data-bv-emailaddress="true" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="email_cc">Email CC</label>
                        <textarea id="email_cc" name="email_cc" class="form-control" rows="2" placeholder="Email CC" data-bv-emailaddress-multiple="true" data-bv-emailaddress="true"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="attachment">Attachment</label>
                        <input type="file" id="attachment" name="attachment" class="file-upload-default" data-max-size="3000000" >
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
                    <button type="submit" data-toggle="one-touch" data-touch-message="Submit..." class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js" defer></script>
<script>
    $(document).ready(function() {
    $('#conclusion-form').bootstrapValidator({
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    email: {
                        validators: {
                            notEmpty: {
                                message: 'Email is required and cannot be empty'
                            },
                            emailAddress: {
                                message: 'The value is not a valid email address'
                            }
                        }
                    }
                }
            });
});
</script>