<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Confirm Site Transit</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit-tracking/confirm-site-transit/' . $transporterEntryPermitTracking['id']) ?>" role="form" method="post" class="need-validation">
        <?= _method('put') ?>

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tep">Linked TEP</label>
                        <p class="form-control-static">
                            <?= $transporterEntryPermitTracking['tep_code'] ?> - <?= $transporterEntryPermitTracking['phbid_no_vehicle'] ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('site_transit_actual_date') == '' ?: 'has-error'; ?>">
                        <label for="site_transit_actual_date">Site Transit</label>
                        <input type="text" class="form-control daterangepicker2" id="site_transit_actual_date" name="site_transit_actual_date"
                               placeholder="Site transit actual date" required
                               value="<?= set_value('site_transit_actual_date', format_date($transporterEntryPermitTracking['site_transit_actual_date'], 'd F Y H:i')) ?>">
                        <?= form_error('site_transit_actual_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Confirm Message</label>
                <textarea class="form-control" id="description" name="description" placeholder="Additional description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Confirm
            </button>
        </div>
    </form>
</div>