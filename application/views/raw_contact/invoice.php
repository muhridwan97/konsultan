<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Raw Contact</h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Company</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $rawContact['company'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">PIC</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $rawContact['pic'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Address</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $rawContact['address'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Contact</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $rawContact['contact'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Email</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $rawContact['email'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($rawContact['created_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Invoices</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered no-datatable" id="table-invoice">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>No Invoice</th>
                        <th>Customer Name</th>
                        <th>No Ref</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $statuses = [
                        InvoiceModel::STATUS_DRAFT => 'warning',
                        InvoiceModel::STATUS_PUBLISHED => 'success',
                        InvoiceModel::STATUS_CANCELED => 'danger',
                    ]
                    ?>
                    <?php $no = 1 ?>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr class="row-invoice"
                            data-id="<?= $invoice['id'] ?>"
                            data-label="<?= $invoice['no_invoice'] ?>">
                            <td><?= $no++ ?></td>
                            <td><?= $invoice['no_invoice'] ?></td>
                            <td><?= $invoice['customer_name'] ?></td>
                            <td><?= $invoice['no_reference'] ?></td>
                            <td><?= readable_date($invoice['invoice_date'], false) ?></td>
                            <td>
                                <span class="label label-<?= $statuses[$invoice['status']] ?>">
                                    <?= $invoice['status'] ?>
                                </span>
                            </td>
                            <td>Rp. <?= numerical($invoice['total_price'], 0) ?></td>
                            <td>
                                <a href="<?= site_url('invoice/print_invoice_pdf/' . $invoice['id']) ?>" class="btn btn-primary btn-print-invoice">
                                    Print PDF
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($invoices)): ?>
                    <tr>
                        <td colspan="8">No invoice has been created</td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>

<?php $this->load->view('invoice/_modal_print_invoice') ?>