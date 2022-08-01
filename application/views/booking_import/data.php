<p class="lead mb10">Header</p>
<table class="table table-bordered no-datatable">
    <thead>
    <tr>
        <th>Doc</th>
        <th>Status</th>
        <th>Apply Date</th>
        <th>Reg. Date</th>
        <th>Supplier</th>
        <th>Owner</th>
        <th>Vessel</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $header['KODE_DOKUMEN_PABEAN'] ?></td>
        <td><?= $header['KODE_STATUS'] ?></td>
        <td><?= (new DateTime($header['TANGGAL_AJU']))->format('d M Y') ?></td>
        <td><?= (new DateTime($header['TANGGAL_DAFTAR']))->format('d M Y') ?></td>
        <td><?= $header['NAMA_PEMASOK'] ?></td>
        <td><?= $header['NAMA_PEMILIK'] ?></td>
        <td><?= $header['NAMA_PENGANGKUT'] ?></td>
    </tr>
    </tbody>
</table>

<p class="lead mb10">Container</p>
<table class="table table-bordered no-datatable">
    <thead>
    <tr>
        <th style="width: 30px;">No</th>
        <th>No Container</th>
        <th>Type</th>
        <th>Size</th>
        <th>Seal</th>
        <th>No Police</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($containers as $container): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $container['NOMOR_KONTAINER'] ?></td>
            <td><?= $container['KODE_TIPE_KONTAINER'] ?></td>
            <td><?= $container['KODE_UKURAN_KONTAINER'] ?></td>
            <td><?= if_empty($container['NOMOR_SEGEL'], '-') ?></td>
            <td><?= if_empty($container['NO_POLISI'], '-') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p class="lead mb10">Goods</p>
<table class="table table-bordered no-datatable">
    <thead>
    <tr>
        <th style="width: 30px;">No</th>
        <th>No Goods</th>
        <th>Goods Desc</th>
        <th>Qty Unit</th>
        <th>Qty Packing</th>
        <th>Netto</th>
        <th>Unit</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($goods as $good): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $good['KODE_BARANG'] ?></td>
            <td><?= $good['URAIAN'] ?></td>
            <td><?= number_format($good['JUMLAH_SATUAN'], 0, ',', '.') ?></td>
            <td><?= number_format($good['JUMLAH_KEMASAN'], 0, ',', '.') ?></td>
            <td><?= number_format($good['NETTO'], 0, ',', '.') ?></td>
            <td><?= $good['KODE_SATUAN'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>