<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Upload</h3>
    </div>
    <form action="<?= site_url('upload/update/'.$upload['id']) ?>" role="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">No Booking</label>
                        <p class="form-control-static">
                            <?php if ($upload['is_valid']): ?>
                                <?php
                                if (empty($upload['id_booking'])) {
                                    $linkBooking = site_url('booking/create/' . $upload['id']);
                                    $labelActionBooking = 'Create Booking';
                                } else {
                                    $linkBooking = site_url('booking/view/' . $upload['id_booking']);
                                    $labelActionBooking = $upload['no_booking'];
                                }
                                ?>
                                <a href="<?= $linkBooking ?>"><?= $labelActionBooking ?></a>
                            <?php else: ?>
                                Validate First
                            <?php endif ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Booking Type</label>
                        <p class="form-control-static"><?= $upload['booking_type'] ?></p>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Upload Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Warehouse description"
                          required maxlength="500"><?= set_value('description', $upload['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-warning pull-right">Update Upload Description</button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/upload.js?v=13') ?>" defer></script>