<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?= $title ?> | Warehouse</title>
		<link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>">
		<link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">
        <style>
            @page { margin: 20px; }
            body { margin: 20px; }
        </style>
    </head>
	<body>
    <?php $this->load->view('template/_header_transcon') ?>

    <div class="text-center mb20">
        <p style="font-size: 18px; margin-bottom: 0">WAREHOUSE RECEIPT</p>
        <p style="font-size: 16px; text-decoration: underline">NON-TRANSFERABLE</p>
    </div>

    <table style="width: 100%; font-size: 12px; margin-bottom: 20px">
        <tr>
            <td style="width: 140px">WR ISSUANCE DATE</td>
            <td style="width: 10px">:</td>
            <td style="width: 300px"><?= strtoupper(readable_date($warehouseReceipt['issuance_date'], false)) ?></td>
            <td style="width: 100px">WR NO</td>
            <td style="width: 10px">:</td>
            <td><?= $warehouseReceipt['no_warehouse_receipt'] ?></td>
        </tr>
        <tr>
            <td>TO THE ORDER OF</td>
            <td>:</td>
            <td><?= strtoupper($warehouseReceipt['order_of']) ?></td>
            <td>OUR REF NO</td>
            <td>:</td>
            <td><?= $warehouseReceipt['no_batch'] ?></td>
        </tr>
        <tr>
            <td>FOR THE A/C OF</td>
            <td>:</td>
            <td colspan="4"><?= strtoupper($warehouseReceipt['customer_name']) ?></td>
        </tr>
    </table>

    <table class="table table-condensed" style="font-size: 11px; margin-bottom: 20px">
        <tr>
            <th>Marking</th>
            <th>Number of Packages/Units</th>
            <th>Description<br>(Said to contain)</th>
            <th>Gross/Nett Weight<br>(Said to weight)</th>
        </tr>
        <?php foreach ($warehouseReceiptDetails as $item): ?>
            <tr>
                <td></td>
                <td><?= numerical($item['quantity'], 2) ?> <?= $item['unit'] ?></td>
                <td><?= $item['type_goods'] ?></td>
                <td>
                    GR WT : <?= numerical($item['tonnage_gross'] / 1000, 2) ?> <br>
                    NT WT : <?= numerical($item['tonnage'] / 1000, 2) ?>
                </td>
            </tr>
            <tr>
                <td>
                    REMARKS<br>
                    <small style="font-size: 9px">(TOTAL: <?= strtoupper(number_spelled($item['quantity'])) ?> <?= strtoupper($item['unit']) ?> ONLY)</small><br>
                    DATE CARGO IN<br>
                    LOCATION<br>
                    DURATION<br>
                </td>
                <td colspan="3">
                    : &nbsp; <?= strtoupper($item['goods_name']) ?><br><br>
                    : &nbsp;
                        <?php $isFirst = true; foreach (explode(',', $item['inbound_dates']) as $date): ?>
                            <?= !$isFirst ? ',' : '' ?> <?= strtoupper(readable_date($date, false)) ?>
                        <?php $isFirst = false; endforeach; ?>
                        <br>
                    : &nbsp; <?= strtoupper($item['address']) ?><br>
                    : &nbsp; <?= $item['duration'] ?><br>
                <td>
            </tr>
        <?php endforeach; ?>
    </table>

    <article style="font-size: 12px">
        <p>All parties mentioned in this Warehouse Receipt are deemed to have accepted all terms and conditions stated
            in this Warehouse Receipt:-</p>

        <p>
            The governing law shall be the laws of Indonesia and dispute resolution shall be subject to the applicable
            rules of Singapore International Arbitration Centre Rules (SIAC) with hearings in Jakarta, Indonesia in the
            English Language.
        </p>

        <p>
            This Warehouse Receipt is issued in one original only and is only valid when duly signed by the authorized
            signatory of Utami Prasetiawati as a Director PT Transcon Indonesia.
        </p>

        <p>
            The ORIGINAL Warehouse Receipt must be returned for any (full or partial) release of the goods. A photocopy
            or scanned or unauthorized copy of this Warehouse Receipt does not have any legal force or binding effect
            with regard to the entitlement to the goods under this Warehouse Receipt. Any amendment, erasure and/of any
            form of tampering with this Warehouse Receipt renders this Warehouse Receipt null and void. PT Transcon
            Indonesia is not responsible for any consequences arising out of any unauthorized use of this Warehouse
            Receipt.
        </p>
        <p>
            Should there be any request for partial release(s), this original Warehouse Receipt must be presented to PT
            Transcon Indonesia for cancellation. A new Warehouse Receipt will be issued for the remaining goods, upon
            written request thereto, on the condition that such request is acceptable to PT Transcon Indonesia.
        </p>
        <p>
            PT Transcon Indonesia is not responsible to purchase any insurance in respect of the goods stated in this
            Warehouse Receipt.
        </p>

        <p>This Warehouse Receipt and any right in connection with it are subject to PT Transcon Indonesia rates and
            service fees.</p>
        <p>An Indonesian translation of this Warehouse Receipt is provided on the reverse side of this Warehouse Receipt
            to comply with Law No. 24 of 2009. In the event of conflict between the English and Indonesian version, the
            English version shall prevail.</p>

        <br>

        <p><strong>THIS WAREHOUSE RECEIPT IS NON-TRANSFERABLE</strong></p>
        <br>

        <p><strong>PT TRANSCON INDONESIA</strong></p>
        <br><br>
        <p><strong>UTAMI PRASETIAWATI<br>DIRECTOR</strong></p>
    </article>

    <div style="page-break-after: always;"></div>

    <?php $this->load->view('template/_header_transcon') ?>

    <div class="text-center mb20">
        <p style="font-size: 18px; margin-bottom: 0">RESI GUDANG</p>
        <p style="font-size: 16px; text-decoration: underline">TIDAK DAPAT DIALIHKAN</p>
    </div>

    <table style="width: 100%; font-size: 12px; margin-bottom: 20px">
        <tr>
            <td style="width: 180px">TANGGAL PENERBITAN RESI</td>
            <td style="width: 15px">:</td>
            <td style="width: 250px"><?= strtoupper(readable_date_id($warehouseReceipt['issuance_date'], false)) ?></td>
            <td style="width: 90px">NO RESI</td>
            <td style="width: 15px">:</td>
            <td><?= $warehouseReceipt['no_warehouse_receipt'] ?></td>
        </tr>
        <tr>
            <td>ATAS PERINTAH DARI</td>
            <td>:</td>
            <td><?= strtoupper($warehouseReceipt['order_of']) ?></td>
            <td>NO RESI KAMI</td>
            <td>:</td>
            <td><?= $warehouseReceipt['no_batch'] ?></td>
        </tr>
        <tr>
            <td>UNTUK KEPENTINGAN</td>
            <td>:</td>
            <td colspan="4"><?= strtoupper($warehouseReceipt['customer_name']) ?></td>
        </tr>
    </table>

    <table class="table table-condensed" style="font-size: 11px; margin-bottom: 20px">
        <tr>
            <th>Tanda</th>
            <th>Jumlah Kemasan/Unit</th>
            <th>Deskripsi<br>(Isi muatan yang dinyatakan)</th>
            <th>Berat Kotor/Bersih<br>(Berat yang dinyatakan)</th>
        </tr>
        <?php foreach ($warehouseReceiptDetails as $item): ?>
            <tr>
                <td></td>
                <td><?= numerical($item['quantity'], 2) ?> <?= $item['unit'] ?></td>
                <td><?= $item['type_goods'] ?></td>
                <td>
                    GR WT : <?= numerical($item['tonnage_gross'] / 1000, 2) ?> <br>
                    NT WT : <?= numerical($item['tonnage'] / 1000, 2) ?>
                </td>
            </tr>
            <tr>
                <td>
                    CATATAN<br>
                    <small style="font-size: 10px">(TOTAL: <?= strtoupper(number_spelled_id($item['quantity'])) ?> <?= strtoupper($item['unit']) ?> SAJA)</small><br>
                    TANGGAL KARGO MASUK<br>
                    LOKASI<br>
                    DURASI<br>
                </td>
                <td colspan="3">
                    : &nbsp; <?= strtoupper($item['goods_name']) ?><br><br>
                    : &nbsp;
                        <?php $isFirst = true; foreach (explode(',', $item['inbound_dates']) as $date): ?>
                            <?= !$isFirst ? ',' : '' ?> <?= strtoupper(readable_date_id($date, false)) ?>
                        <?php $isFirst = false; endforeach; ?><br>
                    : &nbsp; <?= strtoupper($item['address']) ?><br>
                    : &nbsp; <?= array_search ($item['duration'], WarehouseReceiptModel::DURATIONS) ?><br>
                <td>
            </tr>
        <?php endforeach; ?>
    </table>

    <article style="font-size: 12px">
        <p>Semua pihak yang disebutkan dalam Resi Gudang ini dianggap telah menerima seluruh syarat dan ketentuan yang
            tercantum dalam Resi Gudang ini:-</p>

        <p>Hukum yang berlaku adalah Hukum Indonesia dan penyelesaian perselisihan harus tunduk pada peraturan yang
            berlaku dari Singapore International Arbitration Centre Rules (Aturan Pusat Arbitrase Internasional
            Singapura) (SIAC) dengan persidangan yang dilaksanakan di Jakarta, Indonesia dalam Bahasa Inggris.</p>

        <p>
            Resi Gudang ini hanya dikeluarkan satu dokumen asli dan hanya berlaku bila ditandatangani oleh yang
            berwenang Utami Prasetiawati selaku Direktur PT Transcon Indonesia.
        </p>

        <p>
            Resi Gudang ASLI harus dikembalikan untuk pelepasan barang (penuh atau sebagian). Salinan fotokopi atau
            pindaian atau salinan yang tidak sah dari Resi Gudang ini tidak memiliki kekuatan hukum atau mengikat
            berkenaan dengan hak atas barang-barang berdasarkan Resi Gudang ini.
        </p>

        <p>
            Setiap perubahan, penghapusan dan/atau segala bentuk perubahan dari Resi Gudang ini menyebabkan Resi Gudang
            ini batal dan tidak berlaku. PT Transcon Indonesia tidak bertanggung jawab atas setiap konsekuensi yang
            timbul dari penggunan yang tidak sah atas Resi Gudang ini.
        </p>
        <p>
            Jika ada permintaan untuk pelepasan sebagian, Resi Gudang asli ini harus diajukan ke PT Transcon Indonesia
            untuk pembatalan. Resi Gudang yang baru akan dikeluarkan untuk barang yang tersisa, atas permintaan tersebut
            dapat diterima oleh PT Transcon Indonesia.
        </p>
        <p>
            Resi Gudang ini dan setiap hak sehubungan dengannya terdapat tarif dan biaya pelayanan berdasarkan ketetapan
            PT Transcon Indonesia.
        </p>

        <p>
            Terjemahan Bahasa Indonesia dari Resi Gudang ini tersedia pada bagian belakang Resi Gudang ini untuk
            memenuhi Undang-Undang No. 24 Tahun 2009. Dalam hal terjadi pertentangan antara versi Bahasa Inggris dan
            Bahasa Indonesia, versi Bahasa Inggris akan berlaku.
        </p>
        <br>
        <p><strong>RESI GUDANG INI TIDAK DAPAT DIALIHKAN</strong></p>
    </article>

    </body>
</html>