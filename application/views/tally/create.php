<?php date_default_timezone_set('Asia/Jakarta'); ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Tally Check</h3>
    </div>
    <form action="<?= site_url('tally/save/' . $workOrder['id']) ?>" role="form" method="post" id="form-tally-check" data-handheld-status="<?= $workOrder['status_unlock_handheld'] ?>">
        <input type="hidden" name="id" id="id" value="<?= $workOrder['id'] ?>">
        <input type="hidden" name="mode" id="mode" value="<?= $workOrder['mode'] ?>">
        <input type="hidden" name="status_page" id="status_page" value="<?= 'CREATE_JOB' ?>">
        <input type="hidden" name="first" id="firstTime" value="<?= isset($Overtime['first_overtime']) ? $Overtime['first_overtime'] : null  ?>">
        <input type="hidden" name="second" id="secondTime" value="<?= isset($Overtime['second_overtime']) ? $Overtime['second_overtime'] : null  ?>">
        <input type="hidden" id="timer-date" value="<?= date('Y-m-d') ?>">
        <input type="hidden" id="timer">
        <input type="hidden" id="datetime">
        <div class="box-body">
            <?php if ($workOrder['distributed_calculation'] ?? false): ?>
                <div class="alert alert-warning">
                    <i class="fa fa-info-circle mr5"></i>Weight or volume is recalculated by distribute source value of assembly package.
                </div>
            <?php endif; ?>

            <?php $this->load->view('template/_alert') ?>

            <?php $this->load->view('tally/_tally_check_header') ?>

            <?php $this->load->view('tally/_tally_editor', [
                'bookingId' => (
                        $booking['type'] == 'EXPORT' && $booking['category'] == 'OUTBOUND' ? implode(',', array_column($bookingReferences, 'id_booking_reference'))
                            : if_empty($workOrder['id_booking_in'], $workOrder['id_booking'])),
                'customer' => $workOrder['customer_name'],
                'noReference' => $workOrder['no_reference'],
                'workOrderId' => $workOrder['id'],
                'Overtime' => $Overtime,
                'JobPage' => 'create_job',
                'bookingIdOut' => (
                        $booking['type'] == 'EXPORT' && $booking['category'] == 'OUTBOUND' ? $workOrder['id_booking']
                            : (empty($workOrder['id_booking_in'])? '' : $workOrder['id_booking'])),
                'allowAddContainer' => $booking['type'] == 'EXPORT' ? false : true,
                'stockUrl' => (
                        $booking['type'] == 'EXPORT' ? site_url('booking/ajax-get-booking-detail?id=' . $workOrder['id_booking'])
                            : ($workOrder['handling_type'] == 'STRIPPING' ? site_url('booking/ajax-get-booking-detail?goods_only=1&id=' . $workOrder['id_booking'])
                                : '')
                )
            ]) ?>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
            <a href="<?= site_url('tally/release_job/' . $workOrder['id']) ?>"
               class="btn btn-danger"
               id="btn-release-tally-check"
               data-id="<?= $workOrder['id'] ?>"
               data-no="<?= $workOrder['no_work_order'] ?>">
                Release Job
            </a>
            <div class="pull-right">
             <button type="submit" name="button" value="save" data-toggle="one-touch" class="btn btn-primary">
                Save Tally
            </button>
            </div>
        </div>
    </form>
</div>

<?php $this->load->view('tally/_modal_container_input',[
    'workOrderId' => $workOrder['id'],
    'Overtime' => $Overtime,
    'stockLabel' => $workOrder['handling_type'] == 'STRIPPING' ? 'Stock Booking' : 'Stock',
    'allowAddContainer' => $booking['type'] == 'EXPORT' ? false : true,
]) ?>
<?php if ($workOrder['handling_type'] == "LOAD" && get_active_branch('qr_code_status') == 1) {
    $this->load->view('tally/_modal_goods_input_tally',[
        'workOrderId' => $workOrder['id'],
    ]);
} else {
    $this->load->view('tally/_modal_goods_input',[
        'workOrderId' => $workOrder['id'],
        'stockLabel' => $workOrder['handling_type'] == 'STRIPPING' ? 'Stock Booking' : 'Stock'
    ]);
}
?>
<?php $this->load->view('tally/_modal_select_position') ?>
<?php $this->load->view('tally/_modal_release_job') ?>
<?php $this->load->view('tally/_modal_photo_editor') ?>
<?php $this->load->view('template/_modal_confirm'); ?>
<script src="<?= base_url('assets/app/js/upload-photo.js?v=1') ?>" defer></script>

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
        if (seconds == 60) {
            minutes++;
            seconds = 0;
            if (minutes == 60) {
                hours++;
                minutes = 0;
                if (hours == 24) {
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

    $(document).ready(function(){
        startTime();
        setInterval(startTime, 1000);
    });
</script>
<script src="<?= base_url('assets/app/js/tally.js?v=11') ?>" defer></script>
<script src="<?= base_url('assets/app/js/tally_timer.js') ?>" defer></script>
