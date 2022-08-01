<div class="invoice-wrapper-pdf">
    <div class="clearfix">
        <div class="pull-left <?= get_url_param('with_header', 1) ? '' : 'hidden' ?>" style="width: 500px">
        <img src="<?= FCPATH .'assets/app/img/layout/kop_email.jpg' ?>" class="mt10 pull-left" height="82">
            <!-- <img src="<?= FCPATH .'assets/app/img/layout/transcon_logo.png' ?>" class="mt10 pull-left" style="max-width: 60px">
            <div style="margin-left: 75px">
                <p class="title-2 mb0 mt0">
                    PT. TRANSCON INDONESIA
                </p>
                <p class="small mb0 text-muted">
                    Jl. Denpasar Blok 1 No.1 dan 16 KBN Marunda, Cilincing, Jakarta Utara 14120<br>
                    Telp: 44850578, Fax: 44850403
                </p>
            </div> -->
        </div>
        <div class="<?= $invoice['status'] == 'PUBLISHED' ? 'text-success' : 'text-danger' ?> pull-right text-center"
             style="padding: 7px 10px; border: 1px solid <?= $invoice['status'] == 'PUBLISHED' ? '#3c763d' : 'maroon' ?>">
            <span>
                NO INVOICE
                <?php if ($invoice['status'] == 'DRAFT'): ?>
                    - DRAFT
                <?php endif; ?>
            </span><br>
            <strong class="mb0" style="font-size:14px;">
                <?= $invoice['no_invoice'] ?>
            </strong>
        </div>
    </div>

    <hr <?= get_url_param('with_header', 1) ? '' : 'style="border-color: transparent"' ?>>
    <?php
    if (!get_url_param('with_header', 1)) {
        echo '<br>';
    }
    ?>
    <?php if(get_url_param('is_estimation', 0)): ?>
        <p class="text-danger text-center mb0">
            <strong>THIS IS ESTIMATION ONLY, THE PRICE MAY CHANGE AT ANY TIME</strong>
        </p>
    <?php endif; ?>
    <?php if ($invoice['status'] == 'CANCELED'): ?>
        <p class="text-danger text-center mb0">
            <strong>THIS INVOICE IS CANCELED</strong>
        </p>
    <?php endif; ?>
    <p class="title-2 text-center mb10" style="font-size: 18px">
        CALCULATION OF INVOICE COST
    </p>

    <table style="width: 100%" class="mb10 mt0">
        <tr>
            <td width="17%"><strong>CONSIGNEE</strong></td>
            <td width="2%">:</td>
            <td colspan="4"><?= $customer['name'] ?></td>
        </tr>
        <tr>
            <td><strong>ADDRESS</strong></td>
            <td>:</td>
            <td colspan="4"><?= if_empty($customer['address'], '-') ?></td>
        </tr>
        <tr>
            <td><strong>NPWP</strong></td>
            <td>:</td>
            <td colspan="4"><?= empty($customer['tax_number']) ? '-' : mask_tax_number($customer['tax_number']) ?></td>
        </tr>
        <tr>
            <td><strong>NO REFERENCE</strong></td>
            <td>:</td>
            <td colspan="4">
                <?= $invoice['no_reference'] ?> (<?= $invoice['no_reference_booking'] ?>)
            </td>
        </tr>
        <tr>
            <td><strong>NO BL / DO</strong></td>
            <td>:</td>
            <td colspan="4">
                <?php $extensionFound = false; ?>
                <?php foreach ($bookingExtensions as $bookingExtension): ?>
                    <?php if($bookingExtension['field_name'] == 'NO_BL'): ?>
                        <?= if_empty($bookingExtension['value'], '-') ?>
                        <?php $extensionFound = true; ?>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if(!$extensionFound): ?>
                    -
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td><strong>VESSEL / VOYAGE</strong></td>
            <td>:</td>
            <td width="45%">
                <?= if_empty($booking['vessel'], '-') ?> / <?= if_empty($booking['voyage'], '-') ?>
            </td>
            <td width="18%"><strong>DANGEROUS GOODS</strong></td>
            <td width="2%">:</td>
            <td><?= key_exists('danger', $invoice) ? $invoice['danger'] : '-' ?></td>
        </tr>
        <tr>
            <td><strong>PARTY</strong></td>
            <td>:</td>
            <td><?= key_exists('party', $invoice) ? $invoice['party'] : $invoice['item_summary'] ?></td>
            <td><strong>INBOUND DATE</strong></td>
            <td>:</td>
            <td><?= readable_date($invoice['inbound_date'], false) ?></td>
        </tr>
        <tr>
            <td><strong>ARRIVAL DATE (ETA)</strong></td>
            <td>:</td>
            <td>
                <?php $extensionFound = false; ?>
                <?php foreach ($bookingExtensions as $bookingExtension): ?>
                    <?php if($bookingExtension['field_name'] == 'ETA'): ?>
                        <?= if_empty($bookingExtension['value'], '-') ?>
                        <?php $extensionFound = true; ?>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if(!$extensionFound): ?>
                    -
                <?php endif; ?>
            </td>
            <td><strong>OUTBOUND DATE</strong></td>
            <td>:</td>
            <td><?= readable_date($invoice['outbound_date'], false) ?></td>
        </tr>
    </table>

    <table class="table-invoice" style="margin-bottom: 5px">
        <thead>
        <tr>
            <th rowspan="2">TPP COST</th>
            <th colspan="6" class="text-center">QUANTITY</th>
            <th rowspan="2" class="text-right">CHARGES</th>
            <th rowspan="2" class="text-right">TOTAL</th>
        </tr>
        <tr>
            <th>DAYS</th>
            <th>20A</th>
            <th>20B</th>
            <th>40A</th>
            <th>40/45B</th>
            <th>LCL</th>
        </tr>
        </thead>
        <tbody>
        <?php $totalPrice = 0 ?>
        <?php $isTotalCharges = true ?>
        <?php $isTotalAll = true ?>
        <?php foreach ($invoiceDetails as $invoiceDetail): ?>

            <?php if($invoiceDetail['type'] == 'PAYMENT' && $isTotalCharges): ?>
                <?php $isTotalCharges = false; ?>
                <tr>
                    <td colspan="8"><strong>Total Charges</strong></td>
                    <td class="text-right no-wrap" style="border-top: 1px solid #666666">
                        <strong style="font-size: 13px">Rp. <?= numerical($totalPrice, 0) ?></strong>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if($invoiceDetail['type'] == 'OTHER' && $isTotalAll): ?>
                <?php $isTotalAll = false; ?>
                <tr>
                    <td colspan="8"><strong>Total</strong></td>
                    <td class="text-right no-wrap" style="border-top: 1px solid #666666">
                        <strong style="font-size: 13px">Rp. <?= numerical($totalPrice, 0) ?></strong>
                    </td>
                </tr>
            <?php endif; ?>

            <?php $totalPrice += $invoiceDetail['total']; ?>

            <tr>
                <td>
                    <?= $invoiceDetail['item_name'] ?>
                    <?php if (preg_match('/\/day/', $invoiceDetail['unit'])): ?>
                        <br>
                        <small class="text-muted">
                            <?= if_empty($invoiceDetail['description']) ?>
                        </small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (preg_match('/\/day/', $invoiceDetail['unit'])): ?>
                        <?= numerical($invoiceDetail['unit_multiplier'], 0, true) ?>
                    <?php endif; ?>
                </td>
                <td><?= if_empty($invoiceDetail['20A']) ?></td>
                <td><?= if_empty($invoiceDetail['20B']) ?></td>
                <td><?= if_empty($invoiceDetail['40A']) ?></td>
                <td><?= if_empty($invoiceDetail['4045B']) ?></td>
                <td><?= if_empty($invoiceDetail['LCL']) ?></td>
                <td class="text-right">
                    <?php if($invoiceDetail['type'] != 'INVOICE' && $invoiceDetail['type'] != 'OTHER' && $invoiceDetail['type'] != 'PAYMENT'): ?>
                        <?= numerical($invoiceDetail['unit_price'], 0) ?>
                    <?php endif; ?>
                </td>
                <td class="text-right no-wrap <?= $invoiceDetail['total'] < 0 ? 'text-danger' : '' ?>" style="font-size: 13px">
                    <?= numerical($invoiceDetail['total'], 0) ?>
                </td>
            </tr>
        <?php endforeach ?>

        <tfoot>
        <tr>
            <td colspan="8"><strong>Total Price</strong></td>
            <td class="text-right no-wrap">
                <strong style="font-size: 13px">Rp. <?= numerical($totalPrice, 0) ?></strong>
            </td>
        </tr>
        </tfoot>
        </tbody>
    </table>

    <p>Container Type A: STD/HC - B: OH/OW/OL/OT/FR</p>

    <table>
        <tr>
            <td>
                <strong>Item Summary :</strong>
                <p><?= if_empty($invoice['item_summary'], 'No item summary') ?></p>
            </td>
            <td rowspan="2" style="vertical-align: top">
                <div class="ml20 text-center">
                    <p class="mb0"><strong>Jakarta, <?= readable_date($invoice['created_at'], false) ?></strong></p>
                    <p class="mb0"><strong>Administration</strong></p>
                    ( <?= $invoice['admin_name'] ?> )
                    <br><br><br>
                </div>
            </td>
        </tr>
        <tr>
            <td width="75%">
                <div style="border: 1px solid maroon; padding: 5px 10px;" class="mb10 text-danger">
                    In case of mistake in billing data, please submit a correction within 3x24 hours (3 days).
                    In case of overpayment a correction must be submitted along with complete document no later than the 6th of the date in following month.
                    Tax Invoices can be requested via email: <strong style="font-size: 13px">efaktur@transcon-indonesia.com</strong>
                </div>

                <p style="font-size: 13px">
                    TT a.n. <strong style="font-size: 13px">PT. Transcon Indonesia</strong>
                    BANK MANDIRI KC. KBN Marunda ACC No. <strong style="font-size: 13px">1200007010056</strong>; &nbsp;
                    BANK BCA KCU Tanjung Priok ACC No. <strong style="font-size: 13px">0073018126</strong>
                </p>
            </td>
        </tr>
    </table>
</div>