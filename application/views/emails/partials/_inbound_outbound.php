<p>
    We would inform you about warehouse activity
    since <b><?= $dateFrom ?></b> until <b><?= date('d F Y') ?> (excluded)</b>.
</p>

<h4 style="margin-bottom: 5px !important;">Container Summary</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px">
    <tr style="border-bottom: 2px solid #74787E; text-align: left">
        <th style="width: 120px; padding: 7px 3px">Container</th>
        <th style="padding: 7px 3px">20</th>
        <th style="padding: 7px 3px">40</th>
        <th style="padding: 7px 3px">45</th>
        <th style="padding: 7px 3px">Total</th>
    </tr>
    <tr style="border-bottom: 1px solid #92969c;">
        <td style="padding: 7px 3px;">Inbound</td>
        <td style="padding: 7px 3px"><?= $inContainer20 ?></td>
        <td style="padding: 7px 3px"><?= $inContainer40 ?></td>
        <td style="padding: 7px 3px"><?= $inContainer45 ?></td>
        <td style="padding: 7px 3px"><?= $inContainer20 + $inContainer40 + $inContainer45 ?></td>
    </tr>
    <tr style="border-bottom: 1px solid #92969C;">
        <td style="padding: 7px 3px;">Outbound</td>
        <td style="padding: 7px 3px"><?= $outContainer20 ?></td>
        <td style="padding: 7px 3px"><?= $outContainer40 ?></td>
        <td style="padding: 7px 3px"><?= $outContainer45 ?></td>
        <td style="padding: 7px 3px"><?= $outContainer20 + $outContainer40 + $outContainer45 ?></td>
    </tr>
</table>

<h4 style="margin-bottom: 5px !important;">Goods Summary</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px">
    <tr style="border-bottom: 2px solid #74787E; text-align: left">
        <th style="width: 120px; padding: 7px 3px">Goods</th>
        <th style="padding: 7px 3px">Total Item</th>
        <th style="padding: 7px 3px">Quantity</th>
        <th style="padding: 7px 3px">Weight (Kg)</th>
        <th style="padding: 7px 3px">Volumes</th>
    </tr>
    <tr style="border-bottom: 1px solid #92969c;">
        <td style="padding: 7px 3px;">Inbound</td>
        <td style="padding: 7px 3px"><?= numerical($inGoodsTotal) ?></td>
        <td style="padding: 7px 3px"><?= numerical($inGoodsQuantity) ?></td>
        <td style="padding: 7px 3px"><?= numerical($inGoodsTonnage) ?></td>
        <td style="padding: 7px 3px"><?= numerical($inGoodsVolume) ?></td>
    </tr>
    <tr style="border-bottom: 1px solid #92969C;">
        <td style="padding: 7px 3px;">Outbound</td>
        <td style="padding: 7px 3px"><?= $outGoodsTotal ?></td>
        <td style="padding: 7px 3px"><?= numerical($outGoodsQuantity) ?></td>
        <td style="padding: 7px 3px"><?= numerical($outGoodsTonnage) ?></td>
        <td style="padding: 7px 3px"><?= numerical($outGoodsVolume) ?></td>
    </tr>
</table>

<b>Note:</b> this email may contains attachment