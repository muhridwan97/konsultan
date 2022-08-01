<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Tally Check</h3>
    </div>
    <form action="<?= site_url('tally/update/' . $workOrder['id']) ?>" role="form" method="post" id="form-tally-check" data-handheld-status="<?= $workOrder['status_unlock_handheld'] ?>">
        <input type="hidden" id="timer">
        <input type="hidden" id="datetime">
        <input type="hidden" id="timer-date" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="id" id="work_order_id" value="<?= $workOrder['id'] ?>">
        <input type="hidden" name="mode" id="mode" value="<?= $workOrder['mode'] ?>">
        <input type="hidden" name="status_page" id="status_page" value="<?= 'EDIT_JOB' ?>">

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <?php if (!empty($discrepancy) && get_url_param('discrepancy', false) && AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_DISCREPANCY_EDIT)): ?>
                <input type="hidden" name="discrepancy_edit" id="discrepancy_edit" value="1">
                <div class="alert alert-warning">
                    Edit with discrepancy mode, referenced by handover number <?= $discrepancy['no_discrepancy'] ?>, proceed with careful!
                </div>
            <?php endif; ?>

            <?php $this->load->view('tally/_tally_check_header') ?>

            <?php $this->load->view('tally/_tally_component') ?>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Job Dates</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_EDIT)): ?>
                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT)): ?>
                                <div class="col-md-3">
                                    <div class="form-group <?= form_error('gate_in_date') == '' ?: 'has-error'; ?>">
                                        <label for="gate_in_date">Gate Check In</label>
                                        <input type="text" class="form-control daterangepicker2" id="gate_in_date" name="gate_in_date"
                                               placeholder="Job published at gate" required 
                                               value="<?= set_value('gate_in_date', readable_date($workOrder['gate_in_date'], true, '')) ?>">

                                        <?= form_error('gate_in_date', '<span class="help-block">', '</span>'); ?>
                                        <span class="help-block">Date the job is created</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group <?= form_error('taken_at') == '' ?: 'has-error'; ?>">
                                        <label for="taken_at">Tally Start</label>
                                        <input type="text" class="form-control daterangepicker2" id="taken_at" name="taken_at"
                                               placeholder="Job is taken by tally" required
                                               value="<?= set_value('taken_at', readable_date($workOrder['taken_at'], true, '')) ?>">
                                        <?= form_error('taken_at', '<span class="help-block">', '</span>'); ?>
                                        <span class="help-block">Date the job is taken by tally</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group <?= form_error('completed_at') == '' ?: 'has-error'; ?>">
                                        <label for="completed_at">Tally Complete</label>
                                        <input type="text" class="form-control daterangepicker2" disabled id="completed_at" name="completed_at"
                                               placeholder="Completing tally check" required
                                               value="<?= set_value('completed_at', readable_date($workOrder['completed_at'], true, '')) ?>">
                                        <?= form_error('completed_at', '<span class="help-block">', '</span>'); ?>
                                        <span class="help-block">Date you completing the tally</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group <?= form_error('gate_out_date') == '' ?: 'has-error'; ?>">
                                        <label for="gate_out_date">Gate Check Out</label>
                                        <?php if(empty($workOrder['gate_out_date'])): ?>
                                        <input type="text" class="form-control daterangepicker2" id="gate_out_date" name="gate_out_date"
                                               placeholder="Completing activity" disabled
                                               value="<?= set_value('gate_out_date', readable_date($workOrder['gate_out_date'], true, '')) ?>">
                                        <?php else: ?>
                                        <input type="text" class="form-control daterangepicker2" id="gate_out_date" name="gate_out_date"
                                               placeholder="Completing activity"  required
                                               value="<?= set_value('gate_out_date', readable_date($workOrder['gate_out_date'], true, '')) ?>">
                                        <?php endif; ?>
                                        <?= form_error('gate_out_date', '<span class="help-block">', '</span>'); ?>
                                        <span class="help-block">Date activity is getting done</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="col-md-3">
                                    <div class="form-group <?= form_error('gate_in_date') == '' ?: 'has-error'; ?>">
                                        <label for="gate_in_date">Gate Check In</label>
                                        <input type="text" class="form-control daterangepicker2" disabled id="gate_in_date" name="gate_in_date"
                                               placeholder="Job published at gate" required 
                                               value="<?= set_value('gate_in_date', readable_date($workOrder['gate_in_date'], true, '')) ?>">

                                        <?= form_error('gate_in_date', '<span class="help-block">', '</span>'); ?>
                                        <span class="help-block">Date the job is created</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group <?= form_error('taken_at') == '' ?: 'has-error'; ?>">
                                        <label for="taken_at">Tally Start</label>
                                        <input type="text" class="form-control daterangepicker2" disabled id="taken_at" name="taken_at"
                                               placeholder="Job is taken by tally" required
                                               value="<?= set_value('taken_at', readable_date($workOrder['taken_at'], true, '')) ?>">
                                        <?= form_error('taken_at', '<span class="help-block">', '</span>'); ?>
                                        <span class="help-block">Date the job is taken by tally</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group <?= form_error('completed_at') == '' ?: 'has-error'; ?>">
                                        <label for="completed_at">Tally Complete</label>
                                        <input type="text" class="form-control daterangepicker2" disabled id="completed_at" name="completed_at"
                                               placeholder="Completing tally check" required
                                               value="<?= set_value('completed_at', readable_date($workOrder['completed_at'], true, '')) ?>">
                                        <?= form_error('completed_at', '<span class="help-block">', '</span>'); ?>
                                        <span class="help-block">Date you completing the tally</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group <?= form_error('gate_out_date') == '' ?: 'has-error'; ?>">
                                        <label for="gate_out_date">Gate Check Out</label>
                                        <input type="text" class="form-control daterangepicker2" disabled id="gate_out_date" name="gate_out_date"
                                               placeholder="Completing activity" required
                                               value="<?= set_value('gate_out_date', readable_date($workOrder['gate_out_date'], true, '')) ?>">
                                        <?= form_error('gate_out_date', '<span class="help-block">', '</span>'); ?>
                                        <span class="help-block">Date activity is getting done</span>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <?php $this->load->view('tally/_tally_editor', [
                'bookingId' => if_empty($workOrder['id_booking_in'], $workOrder['id_booking']),
                'customer' => $workOrder['customer_name'],
                'noReference' => $workOrder['no_reference'],
                'workOrderId' => $workOrder['id'],
                'inputSource' => ($workOrder['handling_type'] == 'LOAD' ? 'STOCK' : 'BOTH'),
                'Overtime' => $Overtime,
                'JobPage' => 'edit_job',
                'JobComplete' => $workOrder['completed_at'],
            ]) ?>

        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
            <button type="submit" data-no="<?= $workOrder['no_work_order'] ?>" class="btn btn-primary pull-right" id="btn-save-tally">
                Update Tally
            </button>
        </div>
    </form>
</div>

<?php $this->load->view('tally/_modal_container_input') ?>
<?php $this->load->view('tally/_modal_goods_input') ?>
<?php $this->load->view('tally/_modal_select_position') ?>

<?php $this->load->view('tally/_modal_confirm_check') ?>
<?php $this->load->view('tally/_modal_photo_editor') ?>
<?php $this->load->view('template/_modal_confirm'); ?>
<script src="<?= base_url('assets/app/js/upload-photo.js?v=2') ?>" defer></script>

<script type="text/javascript">
    var y = <?= date("Y") ?>;
    var m = <?= date("m") ?>;
    var d = <?= date("d") ?>;
    var h = <?= date("H") ?>;
    var i = <?= date("i") ?>;
    var s = <?= date("s") ?>;
    var datetime = y+"-"+m+"-"+d+" "+h+":"+i+":"+s;

    var startTime2 = function () {
        datetime = moment(datetime).add(1, 'seconds').format("YYYY-MM-DD HH:mm:ss");
        datetime = moment(new Date(datetime));

        document.getElementById("timer").value = datetime.format("HH:mm:ss");
        document.getElementById("datetime").value = datetime.format("YYYY-MM-DD HH:mm:ss");
    };

    function startTime() {  
      seconds++;
      if(seconds == 60) {
        minutes++;
        seconds = 0;
        if(minutes == 60) {
            hours++;
            minutes = 0;
            if(hours == 24) {
                hours = 0;
                if ($('#timer-date').length) {
                    const nextDay = moment($('#timer-date').val()).add(1, 'd').format('YYYY-MM-DD');
                    $('#timer-date').val(nextDay);
                }
            }
        }
      }
      h = checkTime(hours);
      m = checkTime(minutes);
      s = checkTime(seconds);
      document.getElementById("timer").value = h + ":" + m + ":" + s;
      setTimeout(startTime, 1000);
    }
    
    // add zero in front of numbers < 10
    function checkTime(i) {
      if (i < 10) {i = "0" + i};
      return i;
    }
    
    // event listener
    window.addEventListener("load", function(event) {
        startTime();
        setInterval(startTime, 1000);
    });
</script>
<script src="<?= base_url('assets/app/js/tally.js?v=10') ?>" defer></script>