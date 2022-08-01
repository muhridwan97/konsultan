<div class="modal fade" tabindex="-1" role="dialog" id="modal-resend-notification">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <h4 class="modal-title">Resend Notification</h4>
                </div>
                <div class="modal-body">
                    <h4 class="text-danger" id="countdown"></h4>
                    <p class="lead warning-resend" style="margin-bottom: 0">
                        Are you sure want to 
                        <strong>resend</strong> notification?
                    </p>
                    <p class="text-danger text-notif">
                       <span>This notif may be included to the email, chat or histories.</span>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-submit-notif" data-toggle="one-touch">Resend</button>
                </div>
            </form>
        </div>
    </div>
</div>
