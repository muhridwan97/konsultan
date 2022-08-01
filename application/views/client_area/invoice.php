<?php if ($this->session->flashdata('status') != NULL): ?>
    <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p><?= $this->session->flashdata('message'); ?></p>
    </div>
<?php endif; ?>

<?php $this->load->view('client_area/_form_invoice') ?>

<h4 class="mb20">Invoice Result</h4>
<?php if (!empty(get_url_param('no_container') || !empty(get_url_param('bl')))): ?>
    <?php if(empty($bookings)): ?>
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p>No bookings with related BL and container number are found.</p>
        </div>
    <?php else: ?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Invoice Estimation</h3>
                <p class="text-danger mb0">This estimation only predict activities and storage until certain time.</p>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-hover no-datatable">
                    <thead>
                    <tr>
                        <th>No Booking</th>
                        <th>No Reference</th>
                        <th>Booking Date</th>
                        <th style="width: 250px">Estimation</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= $booking['no_booking'] ?></td>
                            <td><?= $booking['no_reference'] ?></td>
                            <td><?= readable_date($booking['booking_date']) ?></td>
                            <td>
                                <form action="<?= site_url('client_area/invoice_estimation_preview/' . $booking['id']) ?>" target="_blank">
                                    <input type="hidden" name="with_header" value="1">
                                    <input type="hidden" name="is_estimation" value="1">
                                    <input type="hidden" name="bl" value="<?= get_url_param('bl') ?>">
                                    <input type="hidden" name="no_container" value="<?= get_url_param('no_container') ?>">
                                    <input type="hidden" name="id_contact" value="<?= $contactId ?>">
                                    <div class="input-group">
                                        <?php if(key_exists('invoice', $booking) && $booking['invoice']): ?>
                                            Invoice was Published
                                        <?php else: ?>
                                            <input class="form-control datepicker-today" type="text" placeholder="Outbound Date" name="outbound_date"
                                                   value="" required style="min-width: 120px">
                                            <span class="input-group-btn">
                                                <button type="submit" class="btn btn-primary btn-flat">Check Cost</button>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="text-muted visible-xs mt10">*Scroll left to right to see hidden content</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($invoices)): ?>
        <div class="alert alert-warning" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p>No published invoices are found.</p>
        </div>
    <?php else: ?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Generated Invoice</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-hover no-datatable">
                    <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>No Reference</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th style="width: 50px">Download</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?= $invoice['no_invoice'] ?></td>
                            <td><?= $invoice['no_reference'] ?></td>
                            <td><?= $invoice['customer_name'] ?></td>
                            <td><?= readable_date($invoice['invoice_date']) ?></td>
                            <td><?= $invoice['type'] ?></td>
                            <td><?= $invoice['status'] ?></td>
                            <td>
                                <a href="<?= site_url('client_area/invoice_print/' . $invoice['id'] . '?with_header=1') ?>" target="_blank" class="btn btn-success">
                                    Print Invoice
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="text-muted visible-xs mt10">*Scroll left to right to see hidden content</p>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    Find by BL reference number or container.
<?php endif; ?>

<?php $this->load->view('client_area/_modal_questioner') ?>