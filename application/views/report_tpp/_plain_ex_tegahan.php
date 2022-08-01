<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Ex Tegahan</title>
    <style>
        table tr td {
            white-space: nowrap;
        }
    </style>
</head>
<body>
<table style="width: 100%" border="1">
    <thead>
    <tr>
        <th rowspan="2" style="width: 25px">No</th>
        <th colspan="3" class="text-center">Dokumen Status Masuk</th>
        <th colspan="2" class="text-center">BA Segel</th>
        <th colspan="2" class="text-center">BA Serah Pemindahan</th>
        <th rowspan="2">Tanggal Masuk</th>
        <th rowspan="2">FCL / LCL</th>
        <th rowspan="2">No Container</th>
        <th rowspan="2">20</th>
        <th rowspan="2">40</th>
        <th rowspan="2">45</th>
        <th rowspan="2">Consignee</th>
        <th rowspan="2">TPS Asal</th>
        <th rowspan="2">Wil</th>
        <th rowspan="2">Uraian Barang</th>
        <th rowspan="2">Jumlah</th>
        <th rowspan="2">Satuan</th>
        <th rowspan="2">Status Akhir</th>
        <th rowspan="2">Tanggal Keluar</th>
        <th rowspan="2">Dok No</th>
        <th rowspan="2">Tanggal</th>
    </tr>
    <tr>
        <th>Awal</th>
        <th>No</th>
        <th>Tanggal</th>
        <th>No</th>
        <th>Tanggal</th>
        <th>No</th>
        <th>Tanggal</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($reports as $report): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= if_empty($report['booking_type'], '-') ?></td>
            <td><?= if_empty($report['no_reference'], '-') ?></td>
            <td><?= readable_date($report['reference_date'], false) ?></td>
            <td><?= if_empty($report['no_ba_serah'], '-') ?></td>
            <td><?= readable_date($report['ba_serah_date'], false) ?></td>
            <td><?= if_empty($report['no_ba_seal'], '-') ?></td>
            <td><?= readable_date($report['ba_seal_date'], false) ?></td>
            <td><?= readable_date($report['inbound_date'], false) ?></td>
            <td><?= if_empty($report['cargo_type']) ?></td>
            <td><?= if_empty($report['no_container']) ?></td>
            <td><?= $report['container_size'] == '20' ? '1' : '0' ?></td>
            <td><?= $report['container_size'] == '40' ? '1' : '0' ?></td>
            <td><?= $report['container_size'] == '45' ? '1' : '0' ?></td>
            <td><?= if_empty($report['owner_name']) ?></td>
            <td><?= if_empty($report['tps_name']) ?></td>
            <td><?= if_empty($report['tps_region']) ?></td>
            <td><?= if_empty($report['goods_name']) ?></td>
            <td><?= numerical($report['quantity']) ?></td>
            <td><?= if_empty($report['unit'], '-') ?></td>
            <td><?= if_empty($report['document_status'], '-') ?></td>
            <td><?= readable_date($report['outbound_date'], false) ?></td>
            <td><?= if_empty($report['no_doc_kep'], '-') ?></td>
            <td><?= readable_date($report['doc_kep_date'], false) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>