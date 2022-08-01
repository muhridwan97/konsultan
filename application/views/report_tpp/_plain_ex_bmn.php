<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Ex BMN</title>
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
        <th colspan="2" class="text-center">KEP BMN</th>
        <th colspan="2" class="text-center">Container</th>
        <th rowspan="2">Consignee</th>
        <th rowspan="2">Uraian Barang</th>
        <th colspan="2" class="text-center">Surat Perintah Tarik</th>
        <th rowspan="2">Tanggal Masuk</th>
        <th rowspan="2">Tanggal Pencacahan</th>
        <th rowspan="2">Tanggal Keluar</th>
        <th colspan="2">Dokumen Pengeluaran</th>
        <th colspan="2">Persetujuan DJKN</th>
        <th rowspan="2">Tanggal Lelang 1</th>
        <th rowspan="2">Tanggal Lelang 2</th>
        <th rowspan="2">Keterangan</th>
    </tr>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Ukuran</th>
        <th>No Container</th>
        <th>No</th>
        <th>Tanggal</th>
        <th>Dokumen</th>
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
            <td><?= if_empty($report['no_bmn'], '-') ?></td>
            <td><?= readable_date($report['date_bmn'], '-') ?></td>
            <td><?= if_empty($report['container_size'], '-') ?></td>
            <td><?= if_empty($report['no_container'], '-') ?></td>
            <td><?= if_empty($report['owner_name'], '-') ?></td>
            <td><?= if_empty($report['goods_name'], '-') ?></td>
            <td><?= if_empty($report['no_sprint'], '-') ?></td>
            <td><?= readable_date($report['sprint_date'], false) ?></td>
            <td><?= readable_date($report['inbound_date']) ?></td>
            <td><?= readable_date($report['pencacahan_date'], false) ?></td>
            <td><?= readable_date($report['outbound_date'], false) ?></td>
            <td><?= if_empty($report['no_reference_out'], '-') ?></td>
            <td><?= readable_date($report['reference_out_date'], false) ?></td>
            <td><?= if_empty($report['no_djkn'], '-') ?></td>
            <td><?= readable_date($report['date_djkn'], false) ?></td>
            <td><?= readable_date($report['tanggal_lelang_1'], false) ?></td>
            <td><?= readable_date($report['tanggal_lelang_2'], false) ?></td>
            <td><?= if_empty($report['description'], '-') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>