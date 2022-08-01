<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Invoice <?= $booking['no_booking'] ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('booking/_view_header') ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Booking Invoice</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>No Invoice</th>
                        <th>Invoice Date</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php $totalInvoice = 0; ?>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('invoice/view/' . $invoice['id']) ?>">
                                    <?= $invoice['no_invoice'] ?>
                                </a>
                            </td>
                            <td><?= readable_date($invoice['invoice_date']) ?></td>
                            <td><?= if_empty($invoice['description'], '-') ?></td>
                            <td><?= if_empty($invoice['type'], '-') ?></td>
                            <td>
                                <?php
                                $statuses = [
                                    'DRAFT' => 'default',
                                    'PUBLISHED' => 'success',
                                    'CANCELED' => 'danger',
                                ]
                                ?>
                                <span class="label label-<?= $statuses[$invoice['status']] ?>">
                                    <?= $invoice['status'] ?>
                                </span>
                            </td>
                            <td class="text-right">Rp. <?= numerical($invoice['total_price'], 0, true) ?></td>
                        </tr>
                        <?php $totalInvoice+= $invoice['total_price'] ?>
                    <?php endforeach; ?>

                    <?php if(empty($invoices)): ?>
                    <tr>
                        <td colspan="7">No any invoice available</td>
                    </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="6"><strong>Total Invoice Price</strong></td>
                            <td class="text-right"><strong>Rp. <?= numerical($totalInvoice, 0, true) ?></strong></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary pull-left">
            Back
        </a>
        <a href="<?= site_url('invoice/create?booking_id=' . $booking['id']) ?>" class="btn btn-success pull-right">
            Add Invoice
        </a>
    </div>
</div>