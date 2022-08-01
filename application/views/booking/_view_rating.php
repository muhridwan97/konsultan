<div class="form-horizontal form-view">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Rate & feedback</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Rating</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php $totalRating = round(if_empty($booking['rating'], 0)) ?>
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <?php if ($i < $totalRating): ?>
                                <i class="fa fa-star"></i>
                            <?php else: ?>
                                <i class="fa fa-star-o"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                        &nbsp;
                        (<?= $booking['rating'] ?>)
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Rating Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($booking['rating_description'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Rated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($booking['rated_at'], 'd F Y H:i'), '-') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>