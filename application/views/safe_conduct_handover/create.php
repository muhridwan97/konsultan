<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Safe Conduct Handover</h3>
    </div>

    <form action="<?= site_url('safe-conduct-handover/save/' . $safeConduct['id']) ?>" role="form" method="post" class="need-validation">
        <input type="hidden" name="safe_conduct" value="<?= $safeConduct['id'] ?>">

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tep">No Safe Conduct</label>
                        <p class="form-control-static">
                            <a href="<?= site_url('safe-conduct/view/' . $safeConduct['id']) ?>">
                                <?= if_empty($safeConduct['no_safe_conduct_group'], $safeConduct['no_safe_conduct']) ?>
                            </a>
                            <?php if (empty($safeConductGroups)): ?>
                                (<?= $safeConduct['no_reference'] ?>)
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tep">Entry Permit</label>
                        <p class="form-control-static">
                            <?= $safeConduct['tep_code'] ?: '-' ?> <?= if_empty($safeConduct['no_police'], '', '(', ')') ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('received_date') == '' ?: 'has-error'; ?>">
                        <label for="received_date">Received Date</label>
                        <input type="text" class="form-control daterangepicker2" id="received_date" name="received_date"
                               placeholder="Site transit actual date" autocomplete="off" required
                               value="<?= set_value('received_date', format_date($safeConductHandover['received_date'] ?? '', 'd F Y H:i')) ?>">
                        <?= form_error('received_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('driver_handover_date') == '' ?: 'has-error'; ?>">
                        <label for="driver_handover_date">Driver Handover Date</label>
                        <input type="text" class="form-control daterangepicker2" id="driver_handover_date" name="driver_handover_date"
                               placeholder="Site transit actual date" autocomplete="off"
                               value="<?= set_value('driver_handover_date', format_date($safeConductHandover['driver_handover_date'] ?? '', 'd F Y H:i')) ?>">
                        <?= form_error('driver_handover_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Handover description"
                          maxlength="500"><?= set_value('description', $safeConductHandover['driver_handover_date'] ?? '') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <?php if (!empty($safeConductGroups)): ?>
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Safe Conduct Group</h3>
                    </div>
                    <div class="box-body">
                        <div class="alert alert-warning">
                            <strong>Info:</strong> Safe conduct group members will have same handover value.
                        </div>
                        <table class="table table-bordered table-striped no-datatable responsive">
                            <thead>
                            <tr>
                                <th style="width: 50px" class="text-center">No</th>
                                <th>No Safe Conduct</th>
                                <th>No Reference</th>
                                <th>Customer Name</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($safeConductGroups as $index => $safeConductGroup): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <a href="<?= site_url('safe-conduct-handover/view/' . $safeConductGroup['id']) ?>">
                                            <?= $safeConductGroup['no_safe_conduct'] ?>
                                        </a>
                                    </td>
                                    <td><?= $safeConductGroup['no_reference'] ?></td>
                                    <td><?= $safeConductGroup['customer_name'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($safeConductGroups)): ?>
                                <tr>
                                    <td colspan="4">No safe conduct available</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Create Handover
            </button>
        </div>
    </form>
</div>