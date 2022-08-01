<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Tracking Delivery</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit-tracking/update/' . $transporterEntryPermitTracking['id']) ?>" role="form" method="post" id="form-tep-tracking" class="need-validation">
        <?= _method('put') ?>

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tep">Linked TEP</label>
                        <p class="form-control-static">
                            <?= $transporterEntryPermitTracking['tep_code'] ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tep">Tracked Vehicle</label>
                        <p class="form-control-static">
                            <?= $transporterEntryPermitTracking['phbid_no_vehicle'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('site_transit_actual_date') == '' ?: 'has-error'; ?>">
                        <label for="site_transit_actual_date">Site Transit</label>
                        <?php if(!empty($transporterEntryPermitTracking['site_transit_actual_date'])): ?>
                            <input type="text" class="form-control daterangepicker2" id="site_transit_actual_date" name="site_transit_actual_date"
                                   placeholder="Site transit actual date" required
                                   value="<?= set_value('site_transit_actual_date', format_date($transporterEntryPermitTracking['site_transit_actual_date'], 'd F Y H:i')) ?>">
                            <?= form_error('site_transit_actual_date', '<span class="help-block">', '</span>'); ?>
                        <?php else: ?>
                            <p class="form-control-static">
                                <?= if_empty($transporterEntryPermitTracking['site_transit_actual_date'], '-') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group <?= form_error('site_transit_description') == '' ?: 'has-error'; ?>">
                        <label for="site_transit_description">Site Transit Description</label>
                        <textarea class="form-control" id="site_transit_description" name="site_transit_description" placeholder="Edit site transit description"
                                  maxlength="500"><?= set_value('site_transit_description', $transporterEntryPermitTracking['site_transit_description']) ?></textarea>
                        <?= form_error('site_transit_description', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('unloading_actual_date') == '' ?: 'has-error'; ?>">
                        <label for="unloading_actual_date">Unloading</label>
                        <?php if(!empty($transporterEntryPermitTracking['unloading_actual_date'])): ?>
                            <input type="text" class="form-control daterangepicker2" id="unloading_actual_date" name="unloading_actual_date"
                                   placeholder="Site transit actual date" required
                                   value="<?= set_value('unloading_actual_date', format_date($transporterEntryPermitTracking['unloading_actual_date'], 'd F Y H:i')) ?>">
                            <?= form_error('unloading_actual_date', '<span class="help-block">', '</span>'); ?>
                        <?php else: ?>
                            <p class="form-control-static">
                                <?= if_empty($transporterEntryPermitTracking['unloading_actual_date'], '-') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group <?= form_error('unloading_description') == '' ?: 'has-error'; ?>">
                        <label for="unloading_description">Unloading Description</label>
                        <?php if(!empty($transporterEntryPermitTracking['unloading_actual_date'])): ?>
                            <textarea class="form-control" id="unloading_description" name="unloading_description" placeholder="Edit unloading description"
                                      maxlength="500"><?= set_value('unloading_description', $transporterEntryPermitTracking['unloading_description']) ?></textarea>
                            <?= form_error('unloading_description', '<span class="help-block">', '</span>'); ?>
                        <?php else: ?>
                            <p class="form-control-static">
                                <?= if_empty($transporterEntryPermitTracking['unloading_description'], '-') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Update
            </button>
        </div>
    </form>
</div>