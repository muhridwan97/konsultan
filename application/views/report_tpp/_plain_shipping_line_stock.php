<table style="width: 100%" border="1">
    <thead>
    <tr>
        <th style="width: 25px">No</th>
        <th>No Container</th>
        <th>Container Size</th>
        <th>Container Type</th>
        <th>Vessel</th>
        <th>Voyage</th>
        <th>Consignee</th>
        <th>Position</th>
        <th>Gate In Date</th>
        <th>Gate In Time</th>
        <th>Seal</th>
        <th>BC 1.1</th>
        <th>BC 1.1 Date</th>
        <th>BC 1.1 Pos</th>
        <th>No BL</th>
        <th>Goods</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Status</th>
        <th>Status Date</th>
        <th>Shipping Line</th>
        <th>TPS</th>
        <th>NHP No</th>
        <th>NHP Date</th>
        <th>Dok Kep</th>
        <th>Kep Date</th>
        <th>Doc Out</th>
        <th>Out Date</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($shippingLineStock as $report): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= if_empty($report['no_container'], '-') ?></td>
            <td><?= if_empty($report['container_size'], '-') ?></td>
            <td><?= if_empty($report['container_type'], '-') ?></td>
            <td><?= if_empty($report['vessel'], '-') ?></td>
            <td><?= if_empty($report['voyage'], '-') ?></td>
            <td><?= if_empty($report['owner_name'], '-') ?></td>
            <td><?= if_empty($report['position'], '-') ?></td>
            <td><?= format_date($report['completed_at'], 'd F Y') ?></td>
            <td><?= format_date($report['completed_at'], 'H:i:s') ?></td>
            <td><?= if_empty($report['seal'], '-') ?></td>
            <td><?= if_empty($report['no_bc11'], '-') ?></td>
            <td><?= if_empty($report['bc11_date'], '-') ?></td>
            <td><?= if_empty($report['pos'], '-') ?></td>
            <td><?= if_empty($report['no_bl'], '-') ?></td>
            <td><?= if_empty($report['goods_name'], '-') ?></td>
            <td><?= if_empty($report['quantity'], '-') ?></td>
            <td><?= if_empty($report['unit'], '-') ?></td>
            <td><?= if_empty($report['document_status'], '-') ?></td>
            <td><?= if_empty($report['document_status_date'], '-') ?></td>
            <td><?= if_empty($report['shipping_line_name'], '-') ?></td>
            <td><?= if_empty($report['tps_name'], '-') ?></td>
            <td><?= if_empty($report['no_nhp'], '-') ?></td>
            <td><?= if_empty($report['nhp_date'], '-') ?></td>
            <td><?= if_empty($report['no_doc_kep'], '-') ?></td>
            <td><?= if_empty($report['doc_kep_date'], '-') ?></td>
            <td><?= if_empty($report['no_reference_out'], '-') ?></td>
            <td><?= if_empty($report['reference_out_date'], '-') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>