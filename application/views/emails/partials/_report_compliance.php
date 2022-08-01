<p>
    We would inform you about Report Compliance on <b><?= $date ?></b> 
</p>

<h4 style="margin-bottom: 5px !important;">Report Compliance</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px">
    <tr style="border-bottom: 2px solid #74787E; text-align: center">
        <th style="width: 120px; padding: 7px 3px;">No</th>
        <th style="padding: 7px 3px">PIC Compliance</th>
        <th style="padding: 7px 3px">Draft</th>
        <th style="padding: 7px 3px">SPPB</th>
    </tr>
    <?php 
    $i=0;
    $total=0;
    $total_draft=0;
    foreach ($data_reports as $data_report): ?>
        <tr style="border-bottom: 1px solid #92969c;text-align: center">
            <td style="padding: 7px 3px;"><?= ++$i; ?></td>
            <td style="padding: 7px 3px"><?= if_empty($data_report['name'], '-') ?></td>
            <td style="padding: 7px 3px"><?= if_empty($data_report['count_draft'], '-') ?></td>
            <td style="padding: 7px 3px"><?= if_empty($data_report['count'], '-') ?></td>
        </tr>
    <?php 
    $total+=$data_report['count'];
    $total_draft+=$data_report['count_draft'];
    endforeach; ?>
        <tr style="border-bottom: 1px solid #92969c;text-align: center">
            <td style="padding: 7px 3px;" colspan="2">Total Document</td>
            <td style="padding: 7px 3px"><?= if_empty($total_draft, '-') ?></td>
            <td style="padding: 7px 3px"><?= if_empty($total, '-') ?></td>
        </tr>
</table>
<p>New document handled at <b><?= $date ?> : <?= $total_handle ?> of <?= $total_uploads ?></b> </p>
<!-- <b>Note:</b> this email may contains attachment -->