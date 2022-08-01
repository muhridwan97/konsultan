<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Link TEP with Tracking Delivery</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit-tracking/save') ?>" role="form" method="post" id="form-tep-tracking" class="need-validation">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('tep') == '' ?: 'has-error'; ?>">
                        <label for="tep">Outbound TEP</label>
                        <select class="form-control select2"
                                name="tep" id="tep" required
                                data-placeholder="Select unlinked tep">
                            <option value=""></option>
                            <?php foreach ($unlinkedTep as $tep): ?>
                                <option value="<?= $tep['id'] ?>" <?= set_select('tep', $tep['id'], count($unlinkedTep) == 1) ?>>
                                    <?= $tep['tep_code'] ?> - <?= format_date($tep['created_at'], 'Y-m-d') ?> - <?= $tep['receiver_no_police'] ?> - <?= $tep['receiver_name'] ?> - (<?= if_empty(str_replace(',', ', ', $tep['no_safe_conduct']), 'No safe conduct') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('tep', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"<?= form_error('tracked_vehicle') == '' ?: ' has-error'; ?>>
                        <label for="tracked_vehicle">Tracked Vehicle</label>
                        <select class="form-control select2" required
                                name="tracked_vehicle" id="tracked_vehicle"
                                data-placeholder="Select tracked vehicle">
                            <option value=""></option>
                            <?php while ($vehicle = $trackedVehicles->unbuffered_row('array')): ?>
                                <option value="<?= $vehicle['id'] ?>" <?= set_select('tracked_vehicle', $vehicle['id']) ?>>
                                    <?= $vehicle['nomor_order'] ?> - <?= $vehicle['tanggal_order'] ?> - <?= $vehicle['nomor_kontainer'] ?> (<?= $vehicle['vehicle'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?= form_error('tracked_vehicle', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Link description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Link TEP
            </button>
        </div>
    </form>
</div>