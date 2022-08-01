<div id="rating-wrapper">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Rating</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
                <div>
                    <div class="checkbox-inline mt-2 mb-0">
                        <label class="form-check-label mb-0">
                            <input type="radio" class="form-check-input" name="rating" id="rate_poor" value="1" <?= set_radio('rating', 1, ($complain['rating'] ?? 0) == 1) ?>> Poor
                        </label>
                    </div>
                    <div class="checkbox-inline mt-2 mb-0">
                        <label class="form-check-label mb-0">
                            <input type="radio" class="form-check-input" name="rating" id="rate_bad" value="2" <?= set_radio('rating', 2, ($complain['rating'] ?? 0) == 2) ?>> Bad
                        </label>
                    </div>
                    <div class="checkbox-inline mt-2 mb-0">
                        <label class="form-check-label mb-0">
                            <input type="radio" class="form-check-input" name="rating" id="rate_fair" value="3" <?= set_radio('rating', 3, ($complain['rating'] ?? 0) == 3) ?>> Fair
                        </label>
                    </div>
                    <div class="checkbox-inline mt-2 mb-0">
                        <label class="form-check-label mb-0">
                            <input type="radio" class="form-check-input" name="rating" id="rate_good" value="4" <?= set_radio('rating', 4, ($complain['rating'] ?? 0) == 4) ?>> Good
                        </label>
                    </div>
                    <div class="checkbox-inline mt-2 mb-0">
                        <label class="form-check-label mb-0">
                            <input type="radio" class="form-check-input" name="rating" id="rate_very_good" value="5" <?= set_radio('rating', 5, ($complain['rating'] ?? 0) == 5) ?>> Very Good
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="reason">Rating Reason</label>
                <textarea class="form-control" id="reason" name="reason" placeholder="Enter reason" maxlength="500"><?= set_value('reason', ($complain['rating_reason'] ?? '')) ?></textarea>
            </div>
        </div>
    </div>
</div>