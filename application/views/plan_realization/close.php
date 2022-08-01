<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Update Realization</h3>
    </div>

    <form action="<?= site_url('plan-realization/close-realization/' . $planRealization['id']) ?>" role="form" method="post" id="form-plan-realization">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Resources</h3>
                </div>
                <div class="box-body">
                    <table class="table table-sm table-hover table-striped table-bordered no-datatable responsive" data-page-length="10">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>Resources</th>
                            <th>Plan</th>
                            <th>Unit</th>
                            <th class="success text-center">Last Realization</th>
                            <th class="warning text-center">Outstanding</th>
                            <th class="danger text-center">Current Realization</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($resources as $index => $resource): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $resource['resource'] ?></td>
                                <td><?= $resource['plan'] ?></td>
                                <td><?= $resource['unit'] ?></td>
                                <td class="success text-center"><?= $resource['realization'] ?></td>
                                <td class="warning text-center"><?= $resource['outstanding'] ?></td>
                                <td class="danger text-center"><?= $resource['current_realization'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Inbounds</h3>
                    <p class="text-success">Compare booking items with completed work orders</p>
                </div>
                <div class="box-body">
                    <table class="table table-sm table-hover table-striped table-bordered no-datatable responsive">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 30px">No</th>
                            <th rowspan="2" style="width: 50px"><span class="label label-danger">Skip</span></th>
                            <th rowspan="2" style="width: 250px">Customer Name</th>
                            <th rowspan="2">No Reference</th>
                            <td colspan="3" class="text-center"><strong>Party</strong></td>
                            <td colspan="3" class="text-center success"><strong>Last Unload</strong></td>
                            <td colspan="3" class="text-center warning"><strong>Outstanding</strong></td>
                            <td colspan="3" class="text-center danger"><strong>Current Realization</strong></td>
                        </tr>
                        <tr>
                            <th>20'</th>
                            <th>40'</th>
                            <th>LCL</th>
                            <th class="success text-center">20'</th>
                            <th class="success text-center">40'</th>
                            <th class="success text-center">LCL</th>
                            <th class="warning text-center">20'</th>
                            <th class="warning text-center">40'</th>
                            <th class="warning text-center">LCL</th>
                            <th class="danger text-center">20'</th>
                            <th class="danger text-center">40'</th>
                            <th class="danger text-center">LCL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($inbounds as $index => $inbound): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="checkbox icheck mt0">
                                        <label>
                                            <input type="checkbox" name="inbound_skips[<?= $inbound['id_booking'] ?>]" value="<?= $inbound['id_booking'] ?>" class="check-inbound-skip"
                                                <?= set_checkbox('inbound_skips[' . $inbound['id_booking'] . ']', $inbound['id_booking']); ?>>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <?= $inbound['customer_name'] ?><br>
                                    <small class="text-muted"><?= character_limiter($inbound['item_name'], 100) ?></small>
                                </td>
                                <td><?= substr($inbound['no_reference'], -6) ?></td>
                                <td><?= if_empty($inbound['party_20'], '&nbsp;') ?></td>
                                <td><?= if_empty($inbound['party_40'], '&nbsp;') ?></td>
                                <td><?= if_empty(numerical($inbound['party_lcl'], 2, true), '&nbsp;') ?></td>
                                <td class="success text-center"><?= if_empty($inbound['realization_20'], '&nbsp;') ?></td>
                                <td class="success text-center"><?= if_empty($inbound['realization_40'], '&nbsp;') ?></td>
                                <td class="success text-center"><?= if_empty(numerical($inbound['realization_lcl'], 2, true), '&nbsp;') ?></td>
                                <td class="warning text-center"><?= if_empty($inbound['outstanding_20']) ?></td>
                                <td class="warning text-center"><?= if_empty($inbound['outstanding_40']) ?></td>
                                <td class="warning text-center"><?= if_empty(numerical($inbound['outstanding_lcl'], 2, true)) ?></td>
                                <td class="danger text-center"><?= if_empty($inbound['current_realization_20'], '&nbsp;') ?></td>
                                <td class="danger text-center"><?= if_empty($inbound['current_realization_40'], '&nbsp;') ?></td>
                                <td class="danger text-center"><?= if_empty(numerical($inbound['current_realization_lcl'],2, true), '&nbsp;') ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="6"></td>
                                <td colspan="3" style="font-weight: bold">Location</td>
                                <td colspan="3" class="warning text-center"><?= $inbound['unloading_location'] ?></td>
                                <td colspan="3" class="danger text-center" style="max-width: 170px">
                                    <?= str_replace(',', ', ', if_empty($inbound['current_unloading_location'], '-')) ?>
                                </td>
                            </tr>
                            <?php if (!empty($inbound['resources'])): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="6"></td>
                                    <td colspan="9" style="font-weight: bold">Special Equipment</td>
                                </tr>
                                <?php foreach ($inbound['resources'] as $resource): ?>
                                    <tr>
                                        <td></td>
                                        <td colspan="6"></td>
                                        <td colspan="3"><?= $resource['resource'] ?></td>
                                        <td colspan="3" class="warning text-center"><?= if_empty($resource['plan'], 0) ?></td>
                                        <td colspan="3" class="danger text-center"><?= $resource['current_realization'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="16">&nbsp;</td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Outbounds</h3>
                    <p class="text-success">Compare handling (current date by default) items with completed work orders</p>
                </div>
                <div class="box-body">
                    <table class="table table-sm table-hover table-striped table-bordered no-datatable">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 30px">No</th>
                            <th rowspan="2" style="width: 50px"><span class="label label-danger">Skip</span></th>
                            <th rowspan="2" style="width: 250px">Customer Name</th>
                            <th rowspan="2">No Reference / Ex Inbound</th>
                            <td colspan="3" class="text-center"><strong>Party</strong></td>
                            <td colspan="3" class="text-center success"><strong>Last Load</strong></td>
                            <td colspan="3" class="text-center warning"><strong>Outstanding</strong></td>
                            <td colspan="3" class="text-center danger"><strong>Current Realization</strong></td>
                        </tr>
                        <tr>
                            <th>20'</th>
                            <th>40'</th>
                            <th>LCL</th>
                            <th class="success text-center">20'</th>
                            <th class="success text-center">40'</th>
                            <th class="success text-center">LCL</th>
                            <th class="warning text-center">20'</th>
                            <th class="warning text-center">40'</th>
                            <th class="warning text-center">LCL</th>
                            <th class="danger text-center">20'</th>
                            <th class="danger text-center">40'</th>
                            <th class="danger text-center">LCL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($outbounds as $index => $outbound): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="checkbox icheck mt0">
                                        <label>
                                            <input type="checkbox" name="outbound_skips[<?= $outbound['id_booking'] ?>]" value="<?= $outbound['id_booking'] ?>" class="check-outbound-skip"
                                                <?= set_checkbox('outbound_skips[' . $outbound['id_booking'] . ']', $outbound['id_booking']); ?>>
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
                                <td><?= if_empty($outbound['party_20']) ?></td>
                                <td><?= if_empty($outbound['party_40']) ?></td>
                                <td><?= if_empty(numerical($outbound['party_lcl'], 2, true)) ?></td>
                                <td class="success text-center"><?= if_empty($outbound['realization_20'], '&nbsp;') ?></td>
                                <td class="success text-center"><?= if_empty($outbound['realization_40'], '&nbsp;') ?></td>
                                <td class="success text-center"><?= if_empty(numerical($outbound['realization_lcl'], 2, true), '&nbsp;') ?></td>
                                <td class="warning text-center"><?= if_empty($outbound['outstanding_20']) ?></td>
                                <td class="warning text-center"><?= if_empty($outbound['outstanding_40']) ?></td>
                                <td class="warning text-center"><?= if_empty(numerical($outbound['outstanding_lcl'], 2, true)) ?></td>
                                <td class="danger text-center"><?= if_empty($outbound['current_realization_20'], '&nbsp;') ?></td>
                                <td class="danger text-center"><?= if_empty($outbound['current_realization_40'], '&nbsp;') ?></td>
                                <td class="danger text-center"><?= if_empty(numerical($outbound['current_realization_lcl'], 2, true), '&nbsp;') ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="6"></td>
                                <td colspan="3" style="font-weight: bold">Location</td>
                                <td colspan="3" class="warning text-center"><?= $outbound['loading_location'] ?></td>
                                <td colspan="3" class="danger text-center" style="max-width: 170px">
                                    <?= str_replace(',', ', ', if_empty($outbound['current_loading_location'], '-')) ?>
                                </td>
                            </tr>
                            <?php if (!empty($outbound['resources'])): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="6"></td>
                                    <td colspan="9" style="font-weight: bold">Special Equipment</td>
                                </tr>
                                <?php foreach ($outbound['resources'] as $resource): ?>
                                    <tr>
                                        <td></td>
                                        <td colspan="6"></td>
                                        <td colspan="3"><?= $resource['resource'] ?></td>
                                        <td colspan="3" class="warning text-center"><?= if_empty($resource['plan'], 0) ?></td>
                                        <td colspan="3" class="danger text-center"><?= $resource['current_realization'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="16">&nbsp;</td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-group <?= form_error('analysis') == '' ?: 'has-error'; ?>">
                <label for="analysis">Analysis</label>
                <textarea class="form-control" id="analysis" name="analysis" placeholder="Realization analysis" required
                          maxlength="500"><?= set_value('analysis') ?></textarea>
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
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Close Realization
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/plan-realization.js') ?>" defer></script>