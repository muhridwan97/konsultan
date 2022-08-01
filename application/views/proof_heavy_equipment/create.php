<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Generate Plan</h3>
    </div>

    <form action="<?= site_url('plan-realization/save') ?>" role="form" class="need-validation" method="post" id="form-plan-realization">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Resources</h3>
                </div>
                <div class="box-body">
                    <?php if($this->config->item('enable_branch_mode')): ?>
                        <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
                    <?php else: ?>
                        <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                            <label for="branch">Branch</label>
                            <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                                <option value=""></option>
                                <?php foreach (get_customer_branch() as $branch): ?>
                                    <option value="<?= $branch['id'] ?>"<?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                        <?= $branch['branch'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                        </div>
                    <?php endif; ?>

                    <table class="table table-sm table-hover table-striped table-bordered no-datatable responsive" data-page-length="10">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>Resources</th>
                            <th>Plan</th>
                            <th>Unit</th>
                            <th>Realization</th>
                            <th>Remark</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($resources as $index => $resource): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?= $resource['resource'] ?>
                                    <?= if_empty($resource['description'], '', '<span class="text-muted">(', ')</span>') ?>
                                </td>
                                <td><?= $resource['plan'] ?></td>
                                <td><?= $resource['unit'] ?></td>
                                <td><?= $resource['realization'] ?></td>
                                <td>
                                    <textarea name="resources[<?= $resource['resource'] ?>][description]" rows="1" class="form-control"
                                              placeholder="Add plan description" maxlength="200"><?= set_value('inbounds[' . $resource['resource'] . '][description]') ?></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Inbounds</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="search-data-inbound">Search Inbound Reference</label>
                        <input type="text" id="search-data-inbound" onkeyup="searchTable(this, 'table-plan-inbound', 2)"
                               class="form-control" placeholder="Search inbound reference data...">
                    </div>
                    <table class="table table-sm table-hover table-striped table-bordered no-datatable responsive" id="table-plan-inbound">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 30px">
                                <div class="checkbox icheck mt0" data-toggle="tooltip" data-title="Toggle select all inbound">
                                    <label>
                                        <input type="checkbox" name="check_all_inbound"  value="1" class="check-all" data-target="#table-plan-inbound"
                                            <?= set_checkbox('check_all_inbound', 1, true); ?>>
                                    </label>
                                </div>
                            </th>
                            <th rowspan="2" style="width: 250px">Customer Name</th>
                            <th rowspan="2">No Reference</th>
                            <th rowspan="2">Entry Permits</th>
                            <td colspan="3" class="text-center"><strong>Party</strong></td>
                            <td colspan="3" class="text-center"><strong>Realization</strong></td>
                            <td rowspan="2" class="hidden-xs"><strong>Remark</strong></td>
                        </tr>
                        <tr>
                            <th>20'</th>
                            <th>40'</th>
                            <th>LCL</th>
                            <th>20'</th>
                            <th>40'</th>
                            <th>LCL</th>
                            <th rowspan="2" class="visible-xs">Remark</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($inbounds as $index => $inbound): ?>
                            <tr data-group="row-<?= $inbound['id_booking'] ?>">
                                <td>
                                    <div class="checkbox icheck mt0">
                                        <label>
                                            <input type="checkbox" name="inbounds[<?= $inbound['id_booking'] ?>][id_booking]" value="<?= $inbound['id_booking'] ?>" class="check-booking"
                                                <?= set_checkbox('inbounds[' . $inbound['id_booking'] . '][id_booking]', $inbound['id_booking'], true); ?>>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <?= $inbound['customer_name'] ?><br>
                                    <small class="text-muted"><?= character_limiter($inbound['item_name'], 100) ?></small>
                                </td>
                                <td>
                                    <a href="<?= site_url('booking/view/' . $inbound['id_booking']) ?>" target="_blank">
                                        <?= substr($inbound['no_reference'], -6) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php foreach ($inbound['transporter_entry_permits'] as $tep): ?>
                                        <input type="hidden" name="inbounds[<?= $inbound['id_booking'] ?>][tep][]" value="<?= $tep['id'] ?>">
                                        <?= $tep['tep_code'] ?><br>
                                    <?php endforeach; ?>
                                    <?php if (empty($inbound['transporter_entry_permits'])): ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= if_empty($inbound['party_20'], '&nbsp;') ?></td>
                                <td><?= if_empty($inbound['party_40'], '&nbsp;') ?></td>
                                <td><?= if_empty(numerical($inbound['party_lcl'], 2, true), '&nbsp;') ?></td>
                                <td><?= if_empty($inbound['realization_20'], '&nbsp;') ?></td>
                                <td><?= if_empty($inbound['realization_40'], '&nbsp;') ?></td>
                                <td><?= if_empty(numerical($inbound['realization_lcl'], 2, true), '&nbsp;') ?></td>
                                <td>
                                    <textarea name="inbounds[<?= $inbound['id_booking'] ?>][description]" rows="1" class="form-control"
                                              placeholder="Plan description" maxlength="200"><?= set_value('inbounds[' . $inbound['id_booking'] . '][description]') ?></textarea>
                                </td>
                            </tr>
                            <tr data-group="row-<?= $inbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="6" style="font-weight: bold">Location</td>
                                <td>
                                    <input type="text" class="form-control" placeholder="Plan location"
                                           name="inbounds[<?= $inbound['id_booking'] ?>][location]" maxlength="100" required>
                                </td>
                            </tr>
                            <tr data-group="row-<?= $inbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="7" style="font-weight: bold">Special Equipment</td>
                            </tr>
                            <tr data-group="row-<?= $inbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="6">Labor</td>
                                <td>
                                    <input type="number" class="form-control" placeholder="Plan labor"
                                           name="inbounds[<?= $inbound['id_booking'] ?>][resources][labor]" min="0" step="1" max="20" required>
                                </td>
                            </tr>
                            <tr data-group="row-<?= $inbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="6">Forklift</td>
                                <td>
                                    <input type="number" class="form-control" placeholder="Plan forklift"
                                           name="inbounds[<?= $inbound['id_booking'] ?>][resources][forklift]" min="0" step="1" max="20" required>
                                </td>
                            </tr>
                            <tr data-group="row-<?= $inbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="6">Crane</td>
                                <td>
                                    <input type="number" class="form-control" placeholder="Plan labor"
                                           name="inbounds[<?= $inbound['id_booking'] ?>][resources][crane]" min="0" step="1" max="20" required>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Outbounds</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="search-data-outbound">Search Inbound Reference</label>
                        <input type="text" id="search-data-outbound" onkeyup="searchTable(this, 'table-plan-outbound', 2)"
                               class="form-control" placeholder="Search outbound reference data...">
                    </div>
                    <table class="table table-sm table-hover table-striped table-bordered no-datatable" id="table-plan-outbound">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 30px">
                                <div class="checkbox icheck mt0" data-toggle="tooltip" data-title="Toggle select all outbound">
                                    <label>
                                        <input type="checkbox" name="check_all_outbound"  value="1" class="check-all" data-target="#table-plan-outbound"
                                            <?= set_checkbox('check_all_outbound', 1, true); ?>>
                                    </label>
                                </div>
                            </th>
                            <th rowspan="2" style="width: 250px">Customer Name</th>
                            <th rowspan="2">No Reference / Ex Inbound</th>
                            <th rowspan="2">Entry Permits</th>
                            <td colspan="3" class="text-center"><strong>Party</strong></td>
                            <td colspan="3" class="text-center"><strong>Realization</strong></td>
                            <td rowspan="2" class="hidden-xs"><strong>Remark</strong></td>
                        </tr>
                        <tr>
                            <th>20'</th>
                            <th>40'</th>
                            <th>LCL</th>
                            <th>20'</th>
                            <th>40'</th>
                            <th>LCL</th>
                            <th rowspan="2" class="visible-xs">Remark</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($outbounds as $index => $outbound): ?>
                            <tr data-group="row-<?= $outbound['id_booking'] ?>">
                                <td>
                                    <div class="checkbox icheck mt0">
                                        <label>
                                            <input type="checkbox" name="outbounds[<?= $outbound['id_booking'] ?>][id_booking]" value="<?= $outbound['id_booking'] ?>" class="check-booking"
                                                <?= set_checkbox('outbounds[' . $outbound['id_booking'] . '][id_booking]', $outbound['id_booking'], true); ?>>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <?= $outbound['customer_name'] ?><br>
                                    <small class="text-muted"><?= character_limiter($outbound['item_name'], 100) ?></small>
                                </td>
                                <td>
                                    <?= substr($outbound['no_reference'], -6) ?> /
                                    <?= substr($outbound['no_reference_inbound'], -6) ?>
                                </td>
                                <td>
                                    <?php foreach ($outbound['transporter_entry_permits'] as $tep): ?>
                                        <input type="hidden" name="outbounds[<?= $outbound['id_booking'] ?>][tep][]" value="<?= $tep['id'] ?>">
                                        <?= $tep['tep_code'] ?><br>
                                    <?php endforeach; ?>
                                    <?php if (empty($outbound['transporter_entry_permits'])): ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= if_empty($outbound['party_20']) ?></td>
                                <td><?= if_empty($outbound['party_40']) ?></td>
                                <td><?= if_empty(numerical($outbound['party_lcl'], 2, true)) ?></td>
                                <td><?= if_empty($outbound['realization_20']) ?></td>
                                <td><?= if_empty($outbound['realization_40']) ?></td>
                                <td><?= if_empty(numerical($outbound['realization_lcl'], 2, true)) ?></td>
                                <td>
                                    <textarea name="outbounds[<?= $outbound['id_booking'] ?>][description]" rows="1" class="form-control"
                                              placeholder="Plan description" maxlength="200"><?= set_value('outbounds[' . $outbound['id_booking'] . '][description]') ?></textarea>
                                </td>
                            </tr>
                            <tr data-group="row-<?= $outbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="7" style="font-weight: bold">Special Equipment</td>
                            </tr>
                            <tr data-group="row-<?= $outbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="6">Labor</td>
                                <td>
                                    <input type="number" class="form-control" placeholder="Plan labor"
                                           name="outbounds[<?= $outbound['id_booking'] ?>][resources][labor]" min="0" step="1" max="20" required>
                                </td>
                            </tr>
                            <tr data-group="row-<?= $outbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="6">Forklift</td>
                                <td>
                                    <input type="number" class="form-control" placeholder="Plan forklift"
                                           name="outbounds[<?= $outbound['id_booking'] ?>][resources][forklift]" min="0" step="1" max="20" required>
                                </td>
                            </tr>
                            <tr data-group="row-<?= $outbound['id_booking'] ?>">
                                <td></td>
                                <td colspan="3"></td>
                                <td colspan="6">Crane</td>
                                <td>
                                    <input type="number" class="form-control" placeholder="Plan labor"
                                           name="outbounds[<?= $outbound['id_booking'] ?>][resources][crane]" min="0" step="1" max="20" required>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Plan description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('send') == '' ?: 'has-error'; ?>">
                <div class="checkbox icheck" style="margin-top: 0">
                    <label>
                        <input type="checkbox" name="send" id="send" value="1" <?php echo set_checkbox('send', 1, true); ?>>
                        &nbsp; Send to group branch immediately
                    </label>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-danger pull-right">
                Save Current Plan
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/plan-realization.js') ?>" defer></script>