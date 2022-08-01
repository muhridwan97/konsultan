<p>
    We would inform you about Warehouse and Field stock data until
    <b><?= $dateStock ?></b>.
</p>

<h4 style="margin-bottom: 5px !important;">Container Stock</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px">
    <tr style="border-bottom: 2px solid #74787E; text-align: left">
        <th style="width: 120px; padding: 7px 3px">Container</th>
        <th style="padding: 7px 3px">20</th>
        <th style="padding: 7px 3px">40</th>
        <th style="padding: 7px 3px">45</th>
        <th style="padding: 7px 3px">Total</th>
    </tr>
    <tr style="border-bottom: 1px solid #92969c;">
        <td style="padding: 7px 3px;">Stock</td>
        <td style="padding: 7px 3px"><?= numerical($reportContainer20) ?></td>
        <td style="padding: 7px 3px"><?= numerical($reportContainer40) ?></td>
        <td style="padding: 7px 3px"><?= numerical($reportContainer45) ?></td>
        <td style="padding: 7px 3px"><?= numerical($reportContainer20 + $reportContainer40 + $reportContainer45) ?></td>
    </tr>
</table>

<h4 style="margin-bottom: 5px !important;">Goods Stock (mix units)</h4>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px">
    <tr style="border-bottom: 2px solid #74787E; text-align: left">
        <th style="width: 120px; padding: 7px 3px">Goods</th>
        <th style="padding: 7px 3px">Total Item</th>
        <th style="padding: 7px 3px">Quantity</th>
        <th style="padding: 7px 3px">Tonnages (Kg)</th>
        <th style="padding: 7px 3px">Volumes</th>
    </tr>
    <tr style="border-bottom: 1px solid #92969c;">
        <td style="padding: 7px 3px;">Stock</td>
        <td style="padding: 7px 3px"><?= numerical($reportGoodsTotals) ?></td>
        <td style="padding: 7px 3px"><?= numerical($reportGoodsQuantity) ?></td>
        <td style="padding: 7px 3px"><?= numerical($reportGoodsTonnage) ?></td>
        <td style="padding: 7px 3px"><?= numerical($reportGoodsVolume) ?></td>
    </tr>
</table>

<b>Note:</b> this email may contains attachment