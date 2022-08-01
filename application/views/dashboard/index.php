<?php $this->load->view('template/_alert') ?>

<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="ion ion-ios-list-outline"></i></span>

            <div class="info-box-content">
                <h4 class="mt10 mb0 text-danger">ORDER</h4>
                <h2 class="mt0">4 <small class="hidden-lg">pending</small></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="ion ion-ios-gear-outline"></i></span>

            <div class="info-box-content">
                <h4 class="mt10 mb0 text-danger">PROCCESS</h4>
                <h2 class="mt0">5 <small class="hidden-lg">pending</small></h2>
            </div>
        </div>
    </div>

    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

            <div class="info-box-content">
                <h4 class="mt10 mb0 text-danger">CUSTOMER</h4>
                <h2 class="mt0">10 <small class="hidden-lg">customer</small></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="ion ion-ios-albums-outline"></i></span>
            <div class="info-box-content">
                <h4 class="mt10 mb0 text-danger">OUTSTANDING</h4>
                <h2 class="mt0">2<small class="hidden-lg">items</small></h2>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-danger marquee">
    <h4 class="mb0"></h4>
    <div class="marque-infinite">
        <p class="mb0">
            <strong><i class="icon fa fa-warning"></i> Flash News!</strong>
            <?php $totalSticky = 0 ?>
            <?php foreach ($news as $post): ?>
                <?php if($post['is_sticky'] && (strtotime(date('Y-m-d H:i:s')) <= strtotime($post['expired_date'])) ): ?>
                    <?php $totalSticky++ ?> &nbsp; &nbsp; - &nbsp; &nbsp;
                    <a href="<?= site_url('news/view/' . $post['id']) ?>">
                        <?= $post['title'] ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if($totalSticky <= 0): ?>
                No any flash news today, have a nice day. Contact our support if you need help.
            <?php endif ?>
        </p>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="modal-outstanding-rating">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Rating Outstanding</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">
                        <strong>We are gathering information for research</strong>
                        <span class="fa ion-ios-star-outline"></span>
                        <span class="fa ion-ios-star-outline"></span>
                        <span class="fa ion-ios-star-outline"></span>
                        <span class="fa ion-ios-star-outline"></span>
                        <span class="fa ion-ios-star-outline"></span>
                        <br>
                        You have unrated booking <strong class="text-primary"><?= count(BookingModel::getOutstandingRating()) ?> bookings</strong>.
                    </p>
                    <span>Help us to improve our services, by giving rating completed booking.</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Remind Me Later</button>
                    <a href="<?= site_url('booking-rating') ?>" class="btn btn-danger">Rate Now</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<?php 
    $result = 0; 
?>
<?php foreach ($news as $popUp): ?>
    <?php if (($popUp['is_popup']) && (strtotime(date('Y-m-d H:i:s')) <= strtotime($popUp['expired_date']))): ?>
        <?php $result = $result + 1; ?>
        <div class="modal fade popup" id="myModal<?= $popUp['id'] ?>" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">ANNOUNCEMENT</h4>
                    </div>
                    <div class="modal-body">

                        <?php if (!empty($popUp['featured'])): ?>
                            <a href="<?= base_url('uploads/news/' . $popUp['featured']) ?>"
                               style="height: 200px; display: block; background: url('<?= base_url('uploads/news/' . $popUp['featured']) ?>') center center / cover">
                            </a>
                        <?php endif; ?>
                        <h3 class="mt10"><?= $popUp['title'] ?></h3>
                        <p class="text-muted">Posted by <?= $popUp['author_name'] ?>
                            At <?= readable_date($popUp['created_at']) ?></p>
                        <article class="mb20">
                            <?= $popUp['content'] ?>

                            <?php if (!empty($popUp['description'])): ?>
                                <blockquote class="mt20">
                                    <?= $popUp['description'] ?>
                                </blockquote>
                            <?php endif; ?>
                        </article>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-prev">Prev</button>
                        <button type="button" class="btn btn-default btn-next">Next</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
<input type="hidden" id="index" value="<?= $result ?>">

<script>
    $("div[id^='myModal']").each(function () {

        var currentModal = $(this);

        if ($('#index').val() == 1) {
            currentModal.modal('show');
            currentModal.closest("div[id^='myModal']").prevAll("div[id^='myModal']").first().modal('show');
            currentModal.find('.btn-prev').attr('hidden', true);
        } else {
            currentModal.modal('hide');
            currentModal.closest("div[id^='myModal']").prevAll("div[id^='myModal']").first().modal('show');
        }

        //click next
        currentModal.find('.btn-next').click(function () {
            if (currentModal.closest("div[id^='myModal']").nextAll("div[id^='myModal']").first().length == 0) {
                currentModal.modal('show');
                currentModal.closest("div[id^='myModal']").nextAll("div[id^='myModal']").first().modal('show');
            } else {
                currentModal.modal('hide');
                currentModal.closest("div[id^='myModal']").nextAll("div[id^='myModal']").first().modal('show');
            }
        });

        //click prev
        currentModal.find('.btn-prev').click(function () {
            if (currentModal.closest("div[id^='myModal']").prevAll("div[id^='myModal']").first().length == 0) {
                currentModal.modal('show');
                currentModal.closest("div[id^='myModal']").prevAll("div[id^='myModal']").first().modal('show');
            } else {
                currentModal.modal('hide');
                currentModal.closest("div[id^='myModal']").prevAll("div[id^='myModal']").first().modal('show');
            }
        });

    });
</script>

<script>
    var chartBookingLabel = <?= json_encode($chartBookingLabel) ?>;
    var chartBookingInbound = <?= json_encode(array_column($bookingSummaries, 'inbound_booking')) ?>;
    var chartBookingOutbound = <?= json_encode(array_column($bookingSummaries, 'outbound_booking')) ?>;

    const totalOutstandingBooking = '<?= count(BookingModel::getOutstandingRating()) ?>';
    const isExternal = '<?= UserModel::authenticatedUserData('user_type') == 'EXTERNAL' ? 1 : 0 ?>';
    if(totalOutstandingBooking > 0 && isExternal === '1') {
        $('#modal-outstanding-rating').modal('show');
    }
</script>

<script src="<?= base_url('assets/plugins/chartjs/Chart.min.js') ?>"></script>
<script src="<?= base_url('assets/app/js/dashboard.js?v=2') ?>" defer></script>