
<div class="modal fade" tabindex="-1" role="dialog" id="modal-put-away" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" enctype="multipart/form-data" id="form-checklist-container">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Put Away</strong></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                    <label for="payment_date">Put Away Date</label>
                    <input type="text" id="maxYesterday" class="form-control datepicker" name="put_away_date"
                       placeholder="Select Put Away Date"
                       required maxlength="50" >
                    </div>
                     <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger btn-save-container">Save</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(document).ready(function () {
        var today = new Date();
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose:true,
            endDate: new Date(new Date().setDate(new Date().getDate()-1)),
            maxDate: new Date(new Date().setDate(new Date().getDate()-1))
        }).on('changeDate', function (ev) {
                $(this).datepicker('hide');
            });


        $('.datepicker').keyup(function () {
            if (this.value.match(/[^0-9]/g)) {
                this.value = this.value.replace(/[^0-9^-]/g, '');
            }
        });
    });
</script>
