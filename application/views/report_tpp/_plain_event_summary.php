<?php
$month = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];
?>
<?php if (!empty($reportData)) : ?>
    <?php foreach ($reportData as $keyMonthYear => $report) : ?>
        <?php $key = explode(' ', $keyMonthYear) ?>
        <strong>Bulan <?= $month[$key[0]] . ' ' . $key[1] ?></strong>
        <?php if (key_exists('WITHDRAWAL', $report)) : ?>
            <table style="width: 100%" border="1">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No. BA Penarikan</th>
                    <th>No. Sprint</th>
                    <th>BC 1.5</th>
                    <th>Tanggal BC 1.5</th>
                    <th>TPS</th>
                    <th>BC 1.1</th>
                    <th>Tanggal BC 1.1</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($report['WITHDRAWAL'] as $report): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= if_empty($report['no_booking_news'], '-') ?></td>
                        <td><?= if_empty($report['no_sprint'], '-') ?></td>
                        <td style="mso-number-format:'\@'"><?= if_empty($report['no_reference'], '-') ?></td>
                        <td><?= readable_date($report['reference_date'], false) ?></td>
                        <td><?= if_empty($report['tps'], '-') ?></td>
                        <td><?= if_empty($report['no_bc11'], '-') ?></td>
                        <td><?= readable_date($report['bc11_date'], false) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <br>
        <?php if (key_exists('CANCELING', $report)) : ?>
            <table style="width: 100%" border="1">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No. BA Pembatalan</th>
                    <th>No. Sprint</th>
                    <th>BC 1.5</th>
                    <th>Tanggal BC 1.5</th>
                    <th>TPS</th>
                    <th>BC 1.1</th>
                    <th>Tanggal BC 1.1</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($report['CANCELING'] as $report): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= if_empty($report['no_booking_news'], '-') ?></td>
                        <td><?= if_empty($report['no_sprint'], '-') ?></td>
                        <td><?= if_empty($report['no_reference'], '-') ?></td>
                        <td><?= readable_date($report['reference_date'], false) ?></td>
                        <td><?= if_empty($report['tps'], '-') ?></td>
                        <td><?= if_empty($report['no_bc11'], '-') ?></td>
                        <td><?= readable_date($report['bc11_date'], false) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>