<div style="padding-left: 250px; margin-bottom: 15px; border-bottom: 1px solid #777777">
<table>
    <tr>
        <td><img src="<?= FCPATH . 'assets/app/img/layout/transcon_logo.png' ?>" style="margin-right: 45px; margin-bottom: 10px"></td>
        <td style="padding-left: 10px">
            <p style="font-size: 18px; margin-bottom: 0; letter-spacing: 2px; line-height: 1">
                <strong>PT. Transcon Indonesia</strong>
            </p>
            <p style="line-height: 1; margin-bottom: 10px">
                Jl. Denpasar Blok II No. 1 dan 16, KBN Marunda, Cilincing, Jakarta Utara<br>
                Telp. : 021-448 505 78 Fax. : 021-448 504 03<br>
                email : mgr@transcon-indonesia.com atau cso1@transcon-indonesia.com
            </p>
        </td>
    </tr>
</table>
</div>

<?php
$dataFormat = new DateTime($bookingNews['booking_news_date']);
$day = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jum\'at',
    'Saturday' => 'Sabtu',
];
$dayOrg = $dataFormat->format('l');
$dayName = key_exists($dayOrg, $day) ? $day[$dayOrg] : $dayOrg;
?>
<p class="text-center" style="line-height: 1">
    <strong>
        <?php if ($bookingNews['type'] == 'CANCELING'): ?>
            BERITA ACARA TIDAK DAPAT DIPINDAHKAN TERHADAP BARANG YANG DINYATAKAN TIDAK DIKUASAI
        <?php else: ?>
            BERITA ACARA PEMINDAHAN BARANG YANG DINYATAKAN TIDAK DIKUASAI
        <?php endif; ?><br>
        Nomor : BA-<?= $bookingNews['no_booking_news'] ?>/TI/MKT/<?= roman_number($dataFormat->format('m')) ?>
        /<?= $dataFormat->format('Y') ?>
    </strong>
</p>
<p style="line-height: 1">
    Pada hari <?= $dayName ?> Tanggal <?= $dataFormat->format('d F Y') ?> ,

    <?php if ($bookingNews['type'] == 'CANCELING'): ?>
        Kami Sampaikan Pembatalan Realisasi Pemindahan Barang Yang Dinyatakan Tidak Dikuasai Yang berasal dari Tempat Penimbunan Sementara Lap.
        <?= $bookingNews['tps'] ?> ke Tempat Penimbunan Pabean PT. Transcon Indonesia Yang Beralamat Di Jl. Denpasar Blok II no 1 dan 16 KBN Marunda, Cilincing, Jakarta Utara.
        Dengan Data Sebagai Berikut:
    <?php else: ?>
        dilaksanakan Penarikan Barang yang tidak dikuasai dari lokasi Tempat Penimbunan Sementara Lap. TPS <?= $bookingNews['tps'] ?> ke Tempat Penimbunan Pabean PT.
        Transcon Indonesia yang beralamat di Jln. Denpasar Blok II No 1 dan 16 KBN Marunda, Cilincing, Jakarta Utara. Sesuai Surat Pemberitahuan Kepala Seksi Tempat Penimbunan Nomor :
        <?= $bookingNews['no_sprint'] ?> tanggal <?= (new DateTime($bookingNews['sprint_date']))->format('d F Y') ?>, tentang Perintah Pemindahan Barang yang dinyatakan tidak Dikuasai, sebelum pemindahan akan dilakukan pengecekan
        dengan data sebagai berikut:
    <?php endif; ?>
</p>
<table class="table table-condensed table-bordered" style="margin-bottom: 5px">
    <thead>
    
    <tr>
        <th style="width: 25px">No</th>
        <?php if ($bookingNews['type'] == 'CANCELING'): ?>
            <th>S-Print Pemindahan / Tgl</th>
        <?php endif; ?>
        <th class="text-center">BCF 1.5</th>
        <th class="text-center">No Container</th>
        <th>Size</th>
        <th>Ex. Vessel</th>
        <?php if ($bookingNews['type'] == 'WITHDRAWAL'): ?>
            <th>Seal</th>
        <?php endif; ?>
        <th>DOG</th>
        <th>Condition</th>
        <th>Consignee</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php $lastNoBooking = ''; ?>
    <?php $lastRowSpan = 1; ?>
    <?php $counterRow = 1; ?>
    <?php $counterAllRow = 1; ?>
    <?php $fieldMiddle = 1; ?>
    <?php foreach ($bookingNewsDetails as $bookingNewsDetail): ?>
        <tr>
            <?php if ($bookingNewsDetail['no_reference'] != $lastNoBooking): ?>
                <?php $lastRowSpan = $detailColumns[$bookingNewsDetail['no_reference']]; ?>
                <?php $counterRow = 1 ?>
                <?php $fieldMiddle = ceil($lastRowSpan / 2) ?>
            <?php endif; ?>

            <?php if ($counterRow == $fieldMiddle): ?>
                <td class="text-center" style="line-height: 0.9; <?= $lastRowSpan != $fieldMiddle ? 'border-bottom-color: transparent' : '' ?>">
                    <?= $no++ ?>
                </td>
            <?php else: ?>
                <td style="<?= ($counterRow != $lastRowSpan) ? 'border-bottom-color: transparent' : '' ?>"></td>
            <?php endif; ?>

            <?php if($bookingNews['type'] == 'CANCELING'): ?>
                <?php if ($counterAllRow == ceil(count($bookingNewsDetails) / 2)): ?>
                    <th class="text-center" 
                        style="line-height: 0.9; <?= $lastRowSpan != $fieldMiddle ? 'border-bottom-color: transparent' : '' ?>">
                        <?= $bookingNews['no_sprint'] ?><br>
                        <?= readable_date($bookingNews['sprint_date'], false) ?>
                    </th>
                <?php else: ?>
                    <td style="<?= ($counterAllRow != count($bookingNewsDetails)) ? 'border-bottom-color: transparent' : '' ?>"></td>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($counterRow == $fieldMiddle): ?>
                <td class="text-center"
                    style="line-height: 0.9; <?= $lastRowSpan != $fieldMiddle ? 'border-bottom-color: transparent' : '' ?>">
                    <?= $bookingNewsDetail['no_reference'] ?><br>
                    <?= readable_date($bookingNewsDetail['reference_date'], false) ?>
                </td>
            <?php else: ?>
                <td style="<?= ($counterRow != $lastRowSpan) ? 'border-bottom-color: transparent' : '' ?>"></td>
            <?php endif; ?>

            <td class="text-center" style="line-height: 0.9;">
                <?= $bookingNewsDetail['no_container'] ?>
            </td>
            <td style="line-height: 0.9;"  width="3%"><?= $bookingNewsDetail['size'] ?></td>
            <td style="line-height: 0.9;"><?= if_empty($bookingNewsDetail['vessel'], '-') ?></td>
            <?php if ($bookingNews['type'] == 'WITHDRAWAL'): ?>
                <td style="line-height: 0.9;"  width="3%"><?= if_empty($bookingNewsDetail['seal'], '-') ?></td>
            <?php endif; ?>
            <td style="line-height: 0.9;"><?= if_empty($bookingNewsDetail['dog'], 'No DOG') ?></td>

            <?php if ($counterRow == $fieldMiddle): ?>
                <td class="text-center" width="3%" style="line-height: 0.9; <?= $lastRowSpan != $fieldMiddle ? 'border-bottom-color: transparent' : '' ?>">
                    <?= if_empty($bookingNewsDetail['condition'], '-') ?>
                </td>
                <td class="text-center" style="line-height: 0.9; <?= $lastRowSpan != $fieldMiddle ? 'border-bottom-color: transparent' : '' ?>">
                    <?= $bookingNewsDetail['customer_name'] ?>
                </td>
                <td class="text-center" width="3%"style="line-height: 0.9; <?= $lastRowSpan != $fieldMiddle ? 'border-bottom-color: transparent' : '' ?>">
                    <?= if_empty($bookingNewsDetail['description'], '-') ?>
                </td>
            <?php else: ?>
                <td style="<?= ($counterRow != $lastRowSpan) ? 'border-bottom-color: transparent' : '' ?>"></td>
                <td style="<?= ($counterRow != $lastRowSpan) ? 'border-bottom-color: transparent' : '' ?>"></td>
                <td style="<?= ($counterRow != $lastRowSpan) ? 'border-bottom-color: transparent' : '' ?>"></td>
            <?php endif; ?>

            <?php if ($bookingNewsDetail['no_reference'] != $lastNoBooking): ?>
                <?php $lastNoBooking = $bookingNewsDetail['no_reference']; ?>
            <?php endif; ?>

            <?php $counterRow++ ?>
            <?php $counterAllRow++ ?>
        </tr>
    <?php endforeach; ?>

    <?php if (empty($bookingNewsDetails)): ?>
        <tr>
            <td colspan="9" class="text-center">No data available</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<div style="page-break-inside: avoid ;">

<p>Demikian Berita Acara Nomor:
    BA-<?= $bookingNews['no_booking_news'] ?>/TI/MKT/<?= roman_number($dataFormat->format('m')) ?>
    /<?= $dataFormat->format('Y') ?>
    ini dibuat dengan sebenar-benarnya untuk dapat dipertanggung jawabkan di kemudian hari.
</p>

<table style="width: 100%; line-height: 1">
    <tr>
        <td>
            Mengetahui:<br>
            <strong>Kepala Seksi Penimbunan</strong>
        </td>
        <td>
            Disaksikan Oleh:<br>
            <strong>Hanggar TPS</strong>
        </td>
        <td>
            <strong>Hanggar TPP</strong>
        </td>
    </tr>
    <tr>
        <td style="height: 50px"></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>
            <strong><?= $bookingNews['chief_name'] ?></strong><br>
            NIP. <?= $bookingNews['chief_nip'] ?>
        </td>
        <td>
            NIP.
        </td>
        <td>
            NIP.
        </td>
    </tr>
    <tr>
        <td style="height: 8px"></td>
        <td></td>
        <td></td>
    </tr>
    <?php if ($bookingNews['type'] == 'WITHDRAWAL') : ?>
        <tr>
            <td></td>
            <td>
                <strong>Petugas Pintu TPS</strong>
            </td>
            <td>
                <strong>Petugas Pintu TPP</strong>
            </td>
        </tr>
        <tr>
            <td style="height: 50px"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                NIP.
            </td>
            <td>
                NIP.
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td style="height: 8px"></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
            <strong>Pengusaha TPS</strong>
        </td>
        <td>
            <strong>Pengusaha TPP</strong>
        </td>
    </tr>
    <tr>
        <td style="height: 50px"></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>
            NIP.
        </td>
        <td>
            NIP.
        </td>
    </tr>
</table>
</div>
<script type="text/php">
if ( isset($pdf) ) { 
    $pdf->page_script('
        //if ($PAGE_COUNT > 1) {
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $size = 10;
            $pageText = "BA-<?= $bookingNews['no_booking_news'] ?>/TI/MKT/<?= roman_number($dataFormat->format('m')) ?>/<?= $dataFormat->format('Y') ?>  Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
            $y = 570;
            $x = 630;
            $pdf->text($x, $y, $pageText, $font, $size);
        //} 
    ');
}
</script>


