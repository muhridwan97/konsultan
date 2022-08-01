<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Entry Permit Request</h3>
    </div>

    <form action="<?= site_url('transporter-entry-permit/save_tep_request') ?>" role="form" method="post" id="form-create-tep-request">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group" style="display: none">
                <label for="booking">Transporter Entry Permit Category</label>
            </div>

            <div class="form-group <?= form_error('aju') == '' ?: 'has-error'; ?>">
                <label for="tep_request">TEP Request Reference </label>
                <select class="form-control select2" name="tep_request[]" id="tep_request" multiple
                        data-placeholder="Select tep request reference" style="width: 100%" required>
                    <option value=""></option>
                    <?php foreach ($listRequests as $listRequest): ?>
                        <option value="<?= $listRequest['id'] ?>" <?= set_select('tep_request[]', $listRequest['id'], $listRequest['id'] == $request['id']) ?>>
                            <?= $listRequest['no_request'] ?> ( <?= $listRequest['customer_name'] ?> ) 
                        </option>
                    <?php endforeach ?>
                </select>
                <?= form_error('tep_request', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><span class="hidden-xs">List </span>Goods</h3>
                </div>
                <div class="box-body">
                    <div class="table-editor-wrapper">
                        <div class="table-responsive">
                            <table class="table no-datatable table-striped table-bordered responsive no-wrap" id="table-goods" >
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Reference</th>
                                    <th>No Invoice</th>
                                    <th>No BL</th>
                                    <th>No Goods</th>
                                    <th>Goods Name</th>
                                    <th>Whey Number</th>
                                    <th>Unit</th>
                                    <th>Stock Quantity</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($listGoods)): ?>
                                    <?php foreach ($listGoods as $key => $goods) :?>
                                    <tr>
                                        <td>
                                            <?=$key+1 ?>
                                            <input type="hidden" name="goods[<?=$key?>][id_goods]" id="id_goods" value="<?=$goods['id_goods']?>">
                                            <input type="hidden" name="goods[<?=$key?>][quantity]" id="quantity" value="<?=$goods['quantity']?>">
                                            <input type="hidden" name="goods[<?=$key?>][work_order_quantity]" id="work_order_quantity" value="<?=$goods['work_order_quantity']?>">
                                            <input type="hidden" name="goods[<?=$key?>][id_unit]" id="id_unit" value="<?=$goods['id_unit']?>">
                                            <input type="hidden" name="goods[<?=$key?>][id_upload]" id="id_upload" value="<?=$goods['id_upload']?>">
                                            <input type="hidden" name="goods[<?=$key?>][goods_name]" id="goods_name" value="<?=$goods['goods_name']?>">
                                            <input type="hidden" name="goods[<?=$key?>][no_invoice]" id="no_invoice" value="<?=$goods['no_invoice']?>">
                                            <input type="hidden" name="goods[<?=$key?>][no_bl]" id="no_bl" value="<?=$goods['no_bl']?>">
                                            <input type="hidden" name="goods[<?=$key?>][no_goods]" id="no_goods" value="<?=$goods['no_goods']?>">
                                            <input type="hidden" name="goods[<?=$key?>][unit]" id="unit" value="<?=$goods['unit']?>">
                                            <input type="hidden" name="goods[<?=$key?>][whey_number]" id="whey_number" value="<?=$goods['whey_number']?>">
                                            <input type="hidden" name="goods[<?=$key?>][ex_no_container]" id="ex_no_container" value="<?=$goods['ex_no_container']?>">
                                            <input type="hidden" name="goods[<?=$key?>][hold_status]" id="hold_status" value="<?=$goods['hold_status']?>">
                                            <input type="hidden" name="goods[<?=$key?>][unload_location]" id="unload_location" value="<?=$goods['unload_location']?>">
                                            <input type="hidden" name="goods[<?=$key?>][priority]" id="priority" value="<?=$goods['priority']?>">
                                            <input type="hidden" name="goods[<?=$key?>][priority_description]" id="priority_description" value="<?=$goods['priority_description']?>">
                                            <input type="hidden" name="goods[<?=$key?>][no_reference]" id="no_reference" value="<?=$goods['no_reference']?>">
                                        </td>
                                        <td>
                                            <?= if_empty($goods['no_reference'], '-') ?>
                                        </td>
                                        <td>
                                            <?= if_empty($goods['no_invoice'], '-') ?>
                                        </td>
                                        <td>
                                            <?= if_empty($goods['no_bl'], '-') ?>
                                        </td>
                                        <td>
                                            <?= if_empty($goods['no_goods'], '-') ?>
                                        </td>
                                        <td>
                                            <?= if_empty($goods['goods_name'], '-') ?>
                                        </td>
                                        <td>
                                            <?= if_empty($goods['whey_number'], '-')?>
                                        </td>
                                        <td>
                                            <?= if_empty($goods['unit'], '-') ?>
                                        </td>
                                        <td>
                                            <?= numerical($goods['quantity'], 3, true) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                <tr class="row-placeholder">
                                    <td colspan="9">No goods data</td>
                                </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('tep_date') == '' ?: 'has-error'; ?>">
                        <label for="tep_date">TEP Date</label>
                        <input type="text" class="form-control requestTciDatepicker" id="tep_date" name="tep_date" data-holiday-date="<?= $holidayDate?>"
                               placeholder="TEP Date" required
                               value="<?= set_value('tep_date') ?>">
                        <?= form_error('tep_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bootstrap-timepicker form-group <?= form_error('tep_time') == '' ?: 'has-error'; ?>">
                        <label for="tep_time">TEP Queue Time</label>
                        <div class="input-group">
                            <input type="time" class="form-control timepicker" id="tep_time" name="tep_time" min="08:00"
                               placeholder="TEP Queue Time">
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                        <?= form_error('tep_time', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('total_code') == '' ?: 'has-error'; ?>">
                        <label for="total_code">Total Code<span style="color: gray">(<span id="slot_remain"></span> Slot remaining for <span id="date_remain"></span>)</span></label>
                        <input type="number" class="form-control" id="total_code" name="total_code"
                               placeholder="Total generated item" required min="1" max="1"
                               value="<?= set_value('total_code', 1) ?>">
                        <?= form_error('total_code', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-8" id="email-type-field">
                    <div class="form-group <?= form_error('email_type') == '' ?: 'has-error'; ?>">
                        <label for="email_type">Send Code To</label>
                        <select class="form-control select2" name="email_type" id="email_type" style="width: 100%">
                            <option value="CUSTOMER">SEND TO CUSTOMER</option>
                            <option value="INPUT">INPUT EMAIL MANUAL</option>
                        </select>
                        <?= form_error('email_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4" id="email-input-field" style="display: none">
                    <div class="form-group <?= form_error('input_email') == '' ?: 'has-error'; ?>">
                        <label for="input_email">Input Email</label>
                        <input type="text" class="form-control" id="input_email" name="input_email"
                               placeholder="Input email, separate by comma"
                               value="<?= set_value('input_email') ?>">
                        <?= form_error('input_email', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row" id="tep-before-wrapper" >
                <div class="col-md-12" id="tep-before-field">
                    <div class="form-group <?= form_error('tep_before') == '' ?: 'has-error'; ?>">
                        <label for="tep_before">Has TEP Before?</label>
                        <select class="form-control select2" name="tep_before" id="tep_before" style="width: 100%">
                            <option value="no">NO</option>
                            <option value="yes">YES</option>
                        </select>
                        <?= form_error('tep_before', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6" id="tep-reference-field" style="display: none">
                    <div class="form-group <?= form_error('tep_reference') == '' ?: 'has-error'; ?>">
                        <label for="tep_reference">TEP Reference</label>
                        <select class="form-control select2" name="tep_reference" id="tep_reference" style="width: 100%">
                            <option value=""></option>
                        </select>
                        <?= form_error('tep_reference', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="TEP description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Generate TEP
            </button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/tep.js?v=7') ?>" defer></script>
<script src="<?= base_url('assets/app/js/tep_request.js?v=1') ?>" defer></script>