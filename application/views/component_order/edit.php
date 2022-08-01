<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Component Transaction</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('component_order/update/'.$componentTransaction['id']) ?>" role="form" method="post" enctype="multipart/form-data" id="form-component-transaction">
        <input type="hidden" name="id" id="id" value="<?= $componentTransaction['id'] ?>">
        <div class="box-body">
            <?php if ($this->session->flashdata('status') != NULL): ?>
                <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p><?= $this->session->flashdata('message'); ?></p>
                </div>
            <?php endif; ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach (get_customer_branch() as $branch): ?>
                            <option value="<?= $branch['id'] ?>" <?= set_select('branch', $branch['id'], $branch['id'] == $componentTransaction['id_branch']) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>
            <div class="form-group <?= form_error('no_transaction') == '' ?: 'has-error'; ?>">
                <label for="no_transaction">No Transaction</label>
                <input type="text" class="form-control" id="no_transaction" name="no_transaction"
                       placeholder="Put transaction number"
                       required maxlength="50" value="<?= set_value('no_transaction', $componentTransaction['no_transaction']) ?>">
                <?= form_error('no_transaction', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('component') == '' ?: 'has-error'; ?>">
                <label for="component">Handling Component</label>
                <select type="text" class="form-control select2" id="component" name="component"
                        data-placeholder="Select related component">
                    <option value=""></option>
                    <?php foreach ($components as $component): ?>
                        <option value="<?= $component['id'] ?>" <?= set_select('component', $component['id'], $component['id'] == $componentTransaction['id_component']) ?>>
                            <?= $component['handling_component'] . ' (' . if_empty($component['component_category'], '-') .')' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('component', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('transaction_date') == '' ?: 'has-error'; ?>">
                <label for="transaction_date">Transaction Date</label>
                <input type="text" class="form-control daterangepicker2" id="transaction_date" name="transaction_date"
                       placeholder="Select transaction date"
                       required maxlength="50" value="<?= set_value('transaction_date', (new DateTime($componentTransaction['transaction_date']))->format('d F Y H:i')) ?>">
                <?= form_error('transaction_date', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('quantity') == '' ?: 'has-error'; ?>">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                               placeholder="Quantity of item"
                               required value="<?= set_value('quantity', $componentTransaction['quantity']) ?>">
                        <?= form_error('quantity', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('amount') == '' ?: 'has-error'; ?>">
                        <label for="amount">Amount</label>
                        <input type="text" class="form-control currency" id="amount" name="amount"
                               placeholder="Amount of transaction"
                               required maxlength="50" value="<?= set_value('amount', 'Rp. ' . numerical($componentTransaction['amount'], 0, true)) ?>">
                        <?= form_error('amount', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('option') == '' ?: 'has-error'; ?>">
                <label for="option">Option</label>
                <input type="text" class="form-control" id="option" name="option"
                       placeholder="Option of item"
                       maxlength="50" value="<?= set_value('option', $componentTransaction['option']) ?>">
                <?= form_error('option', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('attachment') == '' ?: 'has-error'; ?>">
                <label for="attachment">Attachment</label>
                <p class="form-control-static">
                    <?php if (empty($componentTransaction['attachment'])): ?>
                        No uploaded file
                    <?php else: ?>
                        <a href="<?= base_url('uploads/component_orders/' . $componentTransaction['attachment']) ?>" target="_blank">
                            <?= $componentTransaction['attachment'] ?>
                        </a>
                    <?php endif; ?>
                </p>
                <input type="file" id="attachment" name="attachment"
                       placeholder="Select transaction attachment">
                <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Order description"
                          required maxlength="500"><?= set_value('description', $componentTransaction['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="<?= site_url('component_order') ?>" class="btn btn-primary pull-left">Back to Component Order List</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-warning pull-right">Update Component Order</button>
        </div>
    </form>
</div>
<!-- /.box -->