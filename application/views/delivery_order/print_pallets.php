<?php foreach ($pallets as $pallet) : ?>

    <section class="invoice">
        <div class="row">
            <div class="col-xs-12 text-center">
                <div class="page-header" style="margin-top: 0">
                    <img class="pull-left" src="<?= base_url('assets/app/img/layout/cwt-logo.png') ?>" alt="CWT limited logo"
                         style="margin-top: 10px; max-width: 120px">
                    <p class="pull-right text-right" style="font-size: 14px; margin-top: 10px">Print Date: <br><strong><?= date('d F Y H:i') ?></strong></p>
                    <h2 style="margin: 0">Pallet Marking</h2>
                    <p class="lead" style="margin-bottom: 10px">No: <?= $pallet['no_pallet'] ?></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <?php $palletQr = $barcode->getBarcodePNG($pallet['no_pallet'], "QRCODE", 8, 8); ?>
                <img src="data:image/png;base64,<?= $palletQr ?>" alt="<?= $pallet['no_pallet'] ?>">
            </div>
            <div class="col-sm-9">
                <table class="table" style="font-size: 120%">
                    <tr>
                        <th style="width: 170px; border-top: none">DO Number</th>
                        <td style="border-top: none"><?= $pallet['no_delivery_order'] ?></td>
                    </tr>
                    <tr>
                        <th>Booking Number</th>
                        <td><?= $pallet['no_booking'] ?></td>
                    </tr>
                    <tr>
                        <th>Owner</th>
                        <td><?= $pallet['no_person'] ?></td>
                    </tr>
                    <tr>
                        <th>Since</th>
                        <td><?= (new DateTime($pallet['since_date']))->format('d F Y H:i') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </section>

<?php endforeach; ?>