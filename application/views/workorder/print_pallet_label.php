<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?> | Warehouse</title>
    <style>
        @page { margin: 5px; }
        body { margin: 5px; }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: bold;
        }
        .pull-left {
            float: left;
        }
        .pull-right {
            float: right;
        }
        .text-right {
            text-align: right;
        }
        .table {
            width: 100%;
        }
        p {
            margin: 0 0 0 0;
            line-height: 1.1;
        }
        
        .table {
            border-collapse: collapse;
        }
        .table tr td , .table tr th {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<?php $counter = 0 ?>
<?php foreach ($goods as $item): ?>
    <?php if($counter > 0 && $counter % 1 == 0): ?>
        <div style="page-break-before: always;"></div>
    <?php endif ?>
    <div class="wrapper" style="margin-bottom: 5px; padding-top: 5px">
        <section class="invoice">
            <div class="page-header clearfix" style="margin-top: 0">
                <img class="pull-left" src="<?= FCPATH .'assets/app/img/layout/transcon_logo.png' ?>" style="width: 75px;margin-right: 5px">
                <p class="pull-right text-right" style="font-size: 14px; margin-top: 5px">Print Date<br><strong><?= date('d F Y') ?></strong></p>
                 <div class="text-center">
                    <h3 style="margin: 0">PALLET <br> MARKING</h3>
                    <p style="margin-bottom: 10px; font-size: 17px">No:<?= $item['no_pallet'] ?></p>
                </div>    
            </div>
            
            <table class="table table-condensed" >
                <tr>
                    <td rowspan="5" colspan="2" class="text-center" style="width: 180px">
                        <?php $palletQr = $barcode->getBarcodePNG($item['no_pallet'], "QRCODE", 7, 7); ?>
                        <img src="data:image/png;base64,<?= $palletQr ?>" alt="<?= $item['no_pallet'] ?>" style="margin-left: 2px">
                        <!-- <p style="font-size: 14px"><?= $item['no_pallet'] ?></p> -->
                    </td>
                    <th><p style="font-size: 13px">Total Weight (Kg)</p></th>
                    <td><p style="font-size: 13px"><?= numerical($item['total_weight'], 2) ?> Kg</p></td>
                </tr>
                <!-- <tr>
                    <th><p style="font-size: 13px">Total Weight (Kg)</p></th>
                    <td><p style="font-size: 13px"><?= numerical($item['total_weight'], 2) ?> Kg</p></td>
                </tr> -->
                <tr>
                    <th><p style="font-size: 13px">Invoice No</p></th>
                    <td><p style="font-size: 13px"><?= if_empty($item['invoice_number'], 'No invoice') ?></p></td>
                </tr>
                <tr>
                    <th><p style="font-size: 13px">Inbound Date</p></th>
                    <td><p style="font-size: 13px"><?= if_empty(format_date($item['completed_at'], 'd F Y H:i'), '-') ?></p></td>
                </tr>
                <tr>
                    <th><p style="font-size: 13px">Position</p></th>
                    <td><p style="font-size: 13px"><?= if_empty($item['position'], 'No position') ?></p></td>
                </tr>
                <tr>
                    <th><p style="font-size: 13px">PIC Tally</p></th>
                    <td><p style="font-size: 13px"><?= if_empty($tally_name, 'No PIC') ?></p></td>
                </tr>
                <tr>
                    <th style=""><p style="font-size: 13px">Booking Type</p></th>
                    <td colspan="3" style=""><p style="font-size: 13px"><?= $item['booking_type'] ?></p></td>
                </tr>
                <tr>
                    <th style=""><p style="font-size: 13px">Booking No</p></th>
                    <td colspan="3"><p style="font-size: 13px"><?= $item['no_booking'] ?> &nbsp; (<?= $item['no_reference'] ?>)</p></td>
                </tr>
                <tr>
                    <th><p style="font-size: 13px">Factory</p></th>
                    <td colspan="3"><p style="font-size: 13px"><?= if_empty($item['owner_name'], 'No factory') ?></p></td>
                </tr>
                <tr>
                    <th><p style="font-size: 13px">Item No</p></th>
                    <td colspan="3"><p style="font-size: 13px"><?= $item['no_goods'] ?> (Ex: <?= $item['ex_no_container'] ?>)</p></td>
                </tr>
                <tr>
                    <th><p style="font-size: 13px">Whey No</p></th>
                    <td colspan="3"><p style="font-size: 13px"><?= if_empty($item['whey_number'], '-') ?></p></td>
                </tr>
                <tr>
                    <th><p style="font-size: 13px">Item Name</p></th>
                    <td colspan="3"><p style="font-size: 13px"><?= $item['goods_name'] ?></p></td>
                </tr>
                
            </table>
        </section>
    </div>
    <?php $counter++; ?>
<?php endforeach; ?>
</body>
</html>

