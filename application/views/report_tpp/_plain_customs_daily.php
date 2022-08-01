<?php foreach ($reportCustomsDaily as $keyDate => $reportCustoms) : ?>
    <?php if (key_exists('inbound', $reportCustoms)) : ?>
        <h4>Laporan Pemasukan <?= format_date($keyDate, 'd F Y') ?></h4>
        <table style="width: 100%" border="1">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Container/Cargo</th>
                <th>20</th>
                <th>40</th>
                <th>45</th>
                <th>LCL</th>
                <th>Segel</th>
                <th>BC 15</th>
                <th>Tanggal BC 15</th>
                <th>Jumlah/Jenis Barang</th>
                <th>Consignee</th>
                <th>TPS</th>
                <th>Jam</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reportCustoms['inbound'] as $index => $report): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $report['item_type'] == 'GOODS' ? 'LCL' : if_empty($report['no_container'], '-') ?></td>
                    <td><?= $report['container_size'] == '20' ? '1' : '' ?></td>
                    <td><?= $report['container_size'] == '40' ? '1' : '' ?></td>
                    <td><?= $report['container_size'] == '45' ? '1' : '' ?></td>
                    <td><?= $report['item_type'] == 'GOODS' ? '1' : '' ?></td>
                    <td><?= if_empty($report['seal'], '-') ?></td>
                    <td>
                        <?= if_empty($report['no_reference'], '-') ?>
                        <small class="text-danger"><?= $report['booking_type'] == 'TEGAHAN' ? '(TEGAHAN)' : '' ?></small>
                    </td>
                    <td><?= format_date($report['reference_date'], 'd F Y') ?></td>
                    <td><?= if_empty($report['goods_name'], '-') ?></td>
                    <td><?= if_empty($report['booking_customer_name'], '-') ?></td>
                    <td><?= if_empty($report['tps_name'], '-') ?></td>
                    <td><?= format_date($report['completed_at'], 'H:i:s') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($reportCustoms['inbound'])): ?>
                <tr>
                    <td colspan="13">No inbound data</td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td><?= $reportCustoms['inbound_summary']['total_20'] ?></td>
                <td><?= $reportCustoms['inbound_summary']['total_40'] ?></td>
                <td><?= $reportCustoms['inbound_summary']['total_45'] ?></td>
                <td><?= $reportCustoms['inbound_summary']['total_lcl'] ?></td>
                <td colspan="7"></td>
            </tr>
            </tfoot>
        </table>
        <br>
    <?php endif ?>
    <?php if (key_exists('outbound', $reportCustoms)) : ?>
        <h4>Laporan Pengeluaran <?= format_date($keyDate, 'd F Y') ?></h4>
        <table style="width: 100%" border="1">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Container/Cargo</th>
                <th>20</th>
                <th>40</th>
                <th>45</th>
                <th>LCL</th>
                <th>Segel</th>
                <th>BC 15</th>
                <th>Tanggal BC 15</th>
                <th>Jumlah/Jenis Barang</th>
                <th>Consignee</th>
                <th>TPS</th>
                <th>Jam</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reportCustoms['outbound'] as $index => $report): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $report['item_type'] == 'GOODS' ? 'LCL' : if_empty($report['no_container'], '-') ?></td>
                    <td><?= $report['container_size'] == '20' ? '1' : '' ?></td>
                    <td><?= $report['container_size'] == '40' ? '1' : '' ?></td>
                    <td><?= $report['container_size'] == '45' ? '1' : '' ?></td>
                    <td><?= $report['item_type'] == 'GOODS' ? '1' : '' ?></td>
                    <td><?= if_empty($report['seal'], '-') ?></td>
                    <td><?= if_empty($report['no_reference'], '-') ?></td>
                    <td><?= readable_date($report['reference_date'], false, '-') ?></td>
                    <td><?= if_empty($report['goods_name'], '-') ?></td>
                    <td><?= if_empty($report['booking_customer_name'], '-') ?></td>
                    <td><?= if_empty($report['tps_name'], '-') ?></td>
                    <td><?= format_date($report['completed_at'], 'H:i:s') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($reportCustoms['outbound'])): ?>
                <tr>
                    <td colspan="13">No outbound data</td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td><?= $reportCustoms['outbound_summary']['total_20'] ?></td>
                <td><?= $reportCustoms['outbound_summary']['total_40'] ?></td>
                <td><?= $reportCustoms['outbound_summary']['total_45'] ?></td>
                <td><?= $reportCustoms['outbound_summary']['total_lcl'] ?></td>
                <td colspan="7"></td>
            </tr>
            </tfoot>
        </table>
        <br>
    <?php endif ?>
    <table style="width: 100%" border="1">
        <thead>
        <tr>
            <th>KET</th>
            <th>20'</th>
            <th>40'</th>
            <th>45'</th>
            <th>LCL</th>
            <th>TOTAL CONT</th>
            <th>TOTAL BCF</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>PEMASUKAN</td>
            <td><?= $reportCustoms['inbound_movement_summary']['total_20'] ?></td>
            <td><?= $reportCustoms['inbound_movement_summary']['total_40'] ?></td>
            <td><?= $reportCustoms['inbound_movement_summary']['total_45'] ?></td>
            <td><?= $reportCustoms['inbound_movement_summary']['total_lcl'] ?></td>
            <td><?= $reportCustoms['inbound_movement_summary']['total_container'] ?></td>
            <td><?= $reportCustoms['inbound_movement_summary']['total_bcf'] ?></td>
        </tr>
        <tr>
            <td>PENGELUARAN</td>
            <td><?= $reportCustoms['outbound_movement_summary']['total_20'] ?></td>
            <td><?= $reportCustoms['outbound_movement_summary']['total_40'] ?></td>
            <td><?= $reportCustoms['outbound_movement_summary']['total_45'] ?></td>
            <td><?= $reportCustoms['outbound_movement_summary']['total_lcl'] ?></td>
            <td><?= $reportCustoms['outbound_movement_summary']['total_container'] ?></td>
            <td><?= $reportCustoms['outbound_movement_summary']['total_bcf'] ?></td>
        </tr>
        <tr class="bg-red">
            <td>TEGAHAN IN</td>
            <td><?= $reportCustoms['hold_inbound_movement_summary']['total_20'] ?></td>
            <td><?= $reportCustoms['hold_inbound_movement_summary']['total_40'] ?></td>
            <td><?= $reportCustoms['hold_inbound_movement_summary']['total_45'] ?></td>
            <td><?= $reportCustoms['hold_inbound_movement_summary']['total_lcl'] ?></td>
            <td><?= $reportCustoms['hold_inbound_movement_summary']['total_container'] ?></td>
            <td><?= $reportCustoms['hold_inbound_movement_summary']['total_bcf'] ?></td>
        </tr>
        <tr class="bg-red">
            <td>TEGAHAN OUT</td>
            <td><?= $reportCustoms['hold_outbound_movement_summary']['total_20'] ?></td>
            <td><?= $reportCustoms['hold_outbound_movement_summary']['total_40'] ?></td>
            <td><?= $reportCustoms['hold_outbound_movement_summary']['total_45'] ?></td>
            <td><?= $reportCustoms['hold_outbound_movement_summary']['total_lcl'] ?></td>
            <td><?= $reportCustoms['hold_outbound_movement_summary']['total_container'] ?></td>
            <td><?= $reportCustoms['hold_outbound_movement_summary']['total_bcf'] ?></td>
        </tr>
        </tbody>
    </table>
<?php endforeach; ?>
