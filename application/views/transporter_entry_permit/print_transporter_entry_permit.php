<table style='width:60%; font-size: 14px; text-align: left; border-collapse: collapse; border:1px solid #aaaaaa' cellpadding='10'>
    <tr style='border-bottom: 1px solid #aaaaaa'>
        <th>Code</th>
        <th>Expired At</th>
    </tr>
    <tr style='border-bottom: 1px solid #aaaaaa'>
        <th><?= $tep['tep_code']?></th>
        <td><?= $tep['expired_at']?></td>
    </tr>
    <tr style='border-bottom: 1px solid #aaaaaa'>
        <td align='center' >QR Code</td>
        <td><img src="data:image/png;base64,<?= $tepBarcode ?>"></td>
    </tr>
</table>

