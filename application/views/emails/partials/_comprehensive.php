<p>
    We would inform you about warehouse comprehensive report
    since <b><?= $dateFrom ?></b> until <b><?= $dateTo ?></b>.
</p>

<h4 style="margin-bottom: 5px !important;">Container Summary</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 12px;">
    <tr style="border-bottom: 2px solid #74787E; text-align: center">
        <th style="width: 120px; padding: 7px 3px" colspan="4">Previous Stock</th>
        <th style="width: 120px; padding: 7px 3px" colspan="4">IN</th>
        <th style="width: 120px; padding: 7px 3px" colspan="4">OUT</th>
        <th style="width: 120px; padding: 7px 3px" colspan="4">Current Stock</th>
    </tr>
    <tr style="border-bottom: 2px solid #74787E; text-align: center">
        <th style="padding: 7px 3px">20</th>
        <th style="padding: 7px 3px">40</th>
        <th style="padding: 7px 3px">45</th>
        <th style="padding: 7px 3px">Total</th>
        <th style="padding: 7px 3px">20</th>
        <th style="padding: 7px 3px">40</th>
        <th style="padding: 7px 3px">45</th>
        <th style="padding: 7px 3px">Total</th>
        <th style="padding: 7px 3px">20</th>
        <th style="padding: 7px 3px">40</th>
        <th style="padding: 7px 3px">45</th>
        <th style="padding: 7px 3px">Total</th>
        <th style="padding: 7px 3px">20</th>
        <th style="padding: 7px 3px">40</th>
        <th style="padding: 7px 3px">45</th>
        <th style="padding: 7px 3px">Total</th>
    </tr>
    <tr style="border-bottom: 1px solid #92969c; text-align: center">
        <td style="padding: 7px 3px"><?= $previousContainerStock20 ?></td>
        <td style="padding: 7px 3px"><?= $previousContainerStock40 ?></td>
        <td style="padding: 7px 3px"><?= $previousContainerStock45 ?></td>
        <td style="padding: 7px 3px"><?= $previousContainerStock20 + $previousContainerStock40 + $previousContainerStock45 ?></td>
        <td style="padding: 7px 3px"><?= $inContainer20 ?></td>
        <td style="padding: 7px 3px"><?= $inContainer40 ?></td>
        <td style="padding: 7px 3px"><?= $inContainer45 ?></td>
        <td style="padding: 7px 3px"><?= $inContainer20 + $inContainer40 + $inContainer45 ?></td>
        <td style="padding: 7px 3px"><?= $outContainer20 ?></td>
        <td style="padding: 7px 3px"><?= $outContainer40 ?></td>
        <td style="padding: 7px 3px"><?= $outContainer45 ?></td>
        <td style="padding: 7px 3px"><?= $outContainer20 + $outContainer40 + $outContainer45 ?></td>
        <td style="padding: 7px 3px"><?= $currentContainerStock20 ?></td>
        <td style="padding: 7px 3px"><?= $currentContainerStock40 ?></td>
        <td style="padding: 7px 3px"><?= $currentContainerStock45 ?></td>
        <td style="padding: 7px 3px"><?= $currentContainerStock20 + $currentContainerStock40 + $currentContainerStock45 ?></td>
    </tr>
</table>

<h4 style="margin-bottom: 5px !important;">Yard Analysis</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 12px;">
    <tr style="border-bottom: 2px solid #74787E; text-align: center">
        <th colspan="2" style="padding: 7px 3px">Yard Usage Analysis</th>
        <th colspan="3" style="padding: 7px 3px">Stock</th>
        <th colspan="3" style="padding: 7px 3px">Dwelling Time</th>
    </tr>
    <tr style="border-bottom: 2px solid #74787E; text-align: center">
        <th style="padding: 7px 3px">TEUS</th>
        <th style="padding: 7px 3px">YOR</th>
        <th style="padding: 7px 3px">Growth</th>
        <th style="padding: 7px 3px">Slow Growth</th>
        <th style="padding: 7px 3px">No Growth</th>
        <th style="padding: 7px 3px">Growth</th>
        <th style="padding: 7px 3px">Slow Growth</th>
        <th style="padding: 7px 3px">No Growth</th>
    </tr>
    <tr style="border-bottom: 1px solid #92969c; text-align: center">
        <th style="padding: 7px 3px"><?= $teus ?></th>
        <th style="padding: 7px 3px"><?= $yor ?></th>
        <th style="padding: 7px 3px"><?= $stockGrowth ?></th>
        <th style="padding: 7px 3px"><?= $stockSlowGrowth ?></th>
        <th style="padding: 7px 3px"><?= $stockNoGrowth ?></th>
        <th style="padding: 7px 3px"><?= $dwellingGrowth ?></th>
        <th style="padding: 7px 3px"><?= $dwellingSlowGrowth ?></th>
        <th style="padding: 7px 3px"><?= $dwellingNoGrowth ?></th>
    </tr>
</table>

<b>Note:</b> this email may contains attachment