<p>
    We would inform you about Trucking and Ops service time
    since <b><?= $dateFrom ?></b> until <b><?= date('d F Y') ?> (excluded)</b>.
</p>

<h4 style="margin-bottom: 5px !important;">Service Time Average</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px">
    <tr style="border-bottom: 2px solid #74787E; text-align: left">
        <th style="width: 120px; padding: 7px 3px">Category</th>
        <th style="padding: 7px 3px">Trucking</th>
        <th style="padding: 7px 3px">Queue</th>
        <th style="padding: 7px 3px">Tally</th>
        <th style="padding: 7px 3px">Gate</th>
        <th style="padding: 7px 3px">Ops Total</th>
    </tr>
    <?php foreach ($serviceTimeAvgs as $serviceTime): ?>
        <tr style="border-bottom: 1px solid #92969c;">
            <td style="padding: 7px 3px;"><?= $serviceTime['booking_category'] ?></td>
            <td style="padding: 7px 3px"><?= if_empty($serviceTime['trucking_service_time'], '-') ?></td>
            <td style="padding: 7px 3px"><?= if_empty($serviceTime['queue_duration'], '-') ?></td>
            <td style="padding: 7px 3px"><?= if_empty($serviceTime['tally_service_time'], '-') ?></td>
            <td style="padding: 7px 3px"><?= if_empty($serviceTime['gate_service_time'], '-') ?></td>
            <td style="padding: 7px 3px"><?= if_empty($serviceTime['booking_service_time'], '-') ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(empty($serviceTimeAvgs)): ?>
        <tr style="border-bottom: 1px solid #92969c;">
            <td style="padding: 7px 3px;">Inbound</td>
            <td style="padding: 7px 3px">-</td>
            <td style="padding: 7px 3px">-</td>
            <td style="padding: 7px 3px">-</td>
            <td style="padding: 7px 3px">-</td>
            <td style="padding: 7px 3px">-</td>
        </tr>
        <tr style="border-bottom: 1px solid #92969c;">
            <td style="padding: 7px 3px;">Outbound</td>
            <td style="padding: 7px 3px">-</td>
            <td style="padding: 7px 3px">-</td>
            <td style="padding: 7px 3px">-</td>
            <td style="padding: 7px 3px">-</td>
            <td style="padding: 7px 3px">-</td>
        </tr>
    <?php endif; ?>
</table>

<b>Note:</b> this email may contains attachment