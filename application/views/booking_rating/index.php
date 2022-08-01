<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Booking Rating</h3>
	</div>
	<div class="box-body">
        <?php $this->load->view('template/_alert') ?>

		<table class="table table-bordered table-striped responsive" id="table-booking-rating">
			<thead>
				<tr>
					<th style="width: 30px">ID</th>
					<th>No Booking</th>
					<th>No Reference</th>
					<th>Booking Date</th>
					<th>Rating</th>
					<th>Rated At</th>
					<th style="width: 60px">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;
				foreach ($bookings as $booking): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $booking['no_booking'] ?></td>
                    <td class="responsive-title"><?= $booking['no_reference'] ?></td>
                    <td><?= format_date($booking['booking_date'], 'd F Y') ?></td>
                    <td><?= if_empty($booking['rating'], 'NOT SET') ?> / 5</td>
                    <td><?= if_empty(format_date($booking['rated_at'], 'd F Y H:i'), '-') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
							</button>
                            <ul class="dropdown-menu dropdown-menu-right row-booking"
                                data-no-reference="<?= $booking['no_reference'] ?>"
                                data-rating="<?= $booking['rating'] ?>"
                                data-description="<?= $booking['rating_description'] ?>"
                                data-rated-at="<?= $booking['rated_at'] ?>">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_RATE)): ?>
                                    <li>
                                        <a href="<?= site_url('booking/view/' . $booking['id']) ?>">
                                            <i class="fa ion-ios-search"></i>View
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= site_url('booking-rating/rate/' . $booking['id']) ?>" class="btn-rate">
                                            <i class="fa ion-ios-star-outline"></i>Rate
                                        </a>
                                    </li>
                                <?php endif; ?>
							</ul>
						</div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-rating-booking">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Rate Booking</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 15px">
                        Give booking <strong id="booking-title"></strong>
                        rating as follow?
                    </p>
                    <div class="form-group">
                        <label for="rate_3" class="control-label mr10" style="vertical-align: bottom">Rate</label>

                        <div class="rating-wrapper">
                            <i class="rating-star fa fa-star-o" data-star="1"></i>
                            <i class="rating-star fa fa-star-o" data-star="2"></i>
                            <i class="rating-star fa fa-star-o" data-star="3"></i>
                            <i class="rating-star fa fa-star-o" data-star="4"></i>
                            <i class="rating-star fa fa-star-o" data-star="5"></i>
                            <div class="rating-input">
                                <label class="mr20">
                                    <input type="radio" name="rating" id="rate_1" value="1"<?= $booking['rating'] == '1' ? ' checked' : '' ?>>
                                </label>
                                <label class="mr20">
                                    <input type="radio" name="rating" id="rate_2" value="2"<?= $booking['rating'] == '2' ? ' checked' : '' ?>>
                                </label>
                                <label class="mr20">
                                    <input type="radio" name="rating" id="rate_3" value="3"<?= $booking['rating'] == '3' ? ' checked' : '' ?>>
                                </label>
                                <label class="mr20">
                                    <input type="radio" name="rating" id="rate_4" value="4"<?= $booking['rating'] == '4' ? ' checked' : '' ?>>
                                </label>
                                <label>
                                    <input type="radio" name="rating" id="rate_5" value="5"<?= $booking['rating'] == '5' ? ' checked' : '' ?>>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="2" placeholder="Rate message"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/app/js/booking-rating.js?v=1') ?>" defer></script>
<script defer>
    $('.rating-star').on('click', function () {
        const star = $(this).data('star') || 0;
        $(this).closest('.rating-wrapper').find('.rating-star')
            .removeClass('fa-star')
            .removeClass('fa-star-o');
        for (var i = 1; i <= 5; i++) {
            if(i <= star) {
                $(this).closest('.rating-wrapper').find('.rating-star[data-star="' + i + '"]').addClass('fa-star');
            } else {
                $(this).closest('.rating-wrapper').find('.rating-star[data-star="' + i + '"]').addClass('fa-star-o');
            }
        }

        $(this).closest('.rating-wrapper').find('[name="rating"]').iCheck('uncheck');
        $(this).closest('.rating-wrapper').find('input[value="' + star + '"]').iCheck('check');
    });
</script>