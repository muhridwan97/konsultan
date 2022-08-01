<div class="modal fade" tabindex="-1" role="dialog" id="modal-confirm-open-pallet">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url()?>work_order/set_pallet_status" method="post">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="pallet_locked" value="<?= PalletMarkingHistoryModel::STATUS_UNLOCKED ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Request Open Pallet</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to <strong> open </strong> pallet marking access
                        <strong id="print-title"></strong>? <br>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Yes</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->