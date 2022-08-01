<div class="modal fade" tabindex="-1" role="dialog" id="modal-questioner">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="get">
                <input type="hidden" name="bl">
                <input type="hidden" name="no_container">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Visitor Data Gathering</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">
                        Please fill form below as our client member to proceed the action.
                    </p>
                    <p class="text-warning">
                        We don't share any private information to third party user.
                    </p>
                    <div class="form-group">
                        <label for="company" class="control-label">Company Name</label>
                        <input name="company" id="company" class="form-control"
                               value="<?= get_url_param('company', get_cookie('q_company')) ?>" placeholder="Company name" required>
                    </div>
                    <div class="form-group">
                        <label for="address" class="control-label">Company Address</label>
                        <textarea name="address" id="address" rows="2"
                                  class="form-control" placeholder="Company address"><?= get_url_param('address', get_cookie('q_address')) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="pic" class="control-label">Person in Charge</label>
                        <input name="pic" id="pic" class="form-control"
                               value="<?= get_url_param('pic', get_cookie('q_pic')) ?>" placeholder="Person who we could contact to" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact" class="control-label">Contact</label>
                                <input name="contact" id="contact" class="form-control"
                                       value="<?= get_url_param('contact', get_cookie('q_contact')) ?>" placeholder="Phone number or fax" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="control-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                       value="<?= get_url_param('email', get_cookie('q_email')) ?>" placeholder="Email address" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Proceed</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->