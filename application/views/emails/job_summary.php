<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

    <style type="text/css" rel="stylesheet" media="all">
        /* Media Queries */
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<?php

$style = [
    /* Layout ------------------------------ */

    'body' => 'margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;',
    'email-wrapper' => 'width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;',

    /* Masthead ----------------------- */

    'email-masthead' => 'padding: 25px 0; text-align: center;',
    'email-masthead_name' => 'font-size: 16px; font-weight: bold; color: #2F3133; text-decoration: none; text-shadow: 0 1px 0 white;',

    'email-body' => 'width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;',
    'email-body_inner' => 'width: auto; max-width: 650px; margin: 0 auto; padding: 0;',
    'email-body_cell' => 'padding: 35px;',

    'email-footer' => 'width: auto; max-width: 650px; margin: 0 auto; padding: 0; text-align: center;',
    'email-footer_cell' => 'color: #AEAEAE; padding: 35px; text-align: center;',

    /* Body ------------------------------ */

    'body_action' => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
    'body_sub' => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',

    /* Type ------------------------------ */

    'anchor' => 'color: #3869D4;',
    'header-1' => 'margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;',
    'header-2' => 'margin-top: 0; color: #2F3133; font-size: 17px; font-weight: bold; text-align: left;',
    'paragraph' => 'margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;',
    'paragraph-sub' => 'margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;',
    'paragraph-center' => 'text-align: center;',

    /* Buttons ------------------------------ */

    'button' => 'display: block; display: inline-block; width: 200px; min-height: 20px; padding: 10px;
                 background-color: #3869D4; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                 text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',

    'button--green' => 'background-color: #22BC66;',
    'button--red' => 'background-color: #dc4d2f;',
    'button--blue' => 'background-color: #3869D4;',
];
?>

<?php $fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;'; ?>

<body style="<?php echo $style['body'] ?>">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="<?php echo $style['email-wrapper'] ?>" align="center">
            <table width="100%" cellpadding="0" cellspacing="0">
                <!-- Logo -->
                <tr>
                    <td style="<?php echo $style['email-masthead'] ?>">
                        <a style="<?php echo $fontFamily ?> <?php echo $style['email-masthead_name'] ?>"
                           href="http://www.transcon-indonesia.com" target="_blank">
                           <img src="<?=base_url('assets/app/img/layout/header-tci-iso-aeo.png')?>" height="100">
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td style="<?php echo $style['email-body'] ?>" width="100%">
                        <table style="<?php echo $style['email-body_inner'] ?>" align="center" width="650"
                               cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="<?php echo $fontFamily ?> <?php echo $style['email-body_cell'] ?>">
                                    <!-- Greeting -->
                                    <h1 style="<?php echo $style['header-1'] ?>">Job no <?= $workOrder['no_work_order'] ?> is completed</h1>

                                    <!-- Info -->
                                    <h3 style="<?php echo $style['paragraph'] ?>">
                                        Hello, <?php echo $workOrder['customer_name'] ?>
                                    </h3>
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        We recently completing job order handling <strong><?= $handling['handling_type'] ?></strong>,
                                        here we would inform you result of the job and data summary:
                                    </p>

                                    <h4 style="<?php echo $style['paragraph-sub'] ?>">Tracking Data</h4>
                                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px">
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 150px; text-align: left; padding: 2px 0">No Handling</th>
                                            <td style="padding: 2px 0"><?= $handling['no_handling'] ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">No Job</th>
                                            <td style="padding: 2px 0"><?= $workOrder['no_work_order'] ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Handling Created</th>
                                            <td style="padding: 2px 0"><?= (new DateTime($handling['created_at']))->format('d F Y') ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Handling Reserved</th>
                                            <td style="padding: 2px 0"><?= (new DateTime($handling['handling_date']))->format('d F Y') ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Job Gate In</th>
                                            <td style="padding: 2px 0"><?= (new DateTime($workOrder['gate_in_date']))->format('d F Y H:i') ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Job Gate Out</th>
                                            <td style="padding: 2px 0"><?= (new DateTime($workOrder['gate_out_date']))->format('d F Y H:i') ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Is Staple</th>
                                            <td style="padding: 2px 0"><?= $workOrder['staple'] == 1 ? 'Yes' : 'No' ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Is Overtime</th>
                                            <td style="padding: 2px 0"><?= $workOrder['overtime'] == 1 ? 'Yes' : 'No' ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Man Power</th>
                                            <td style="padding: 2px 0"><?= numerical($workOrder['man_power']) ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Forklift</th>
                                            <td style="padding: 2px 0"><?= numerical($workOrder['forklift']) ?> minutes</td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Tools</th>
                                            <td style="padding: 2px 0"><?= if_empty($workOrder['tools'], '-') ?></td>
                                        </tr>
                                        <tr style="margin-bottom: 10px">
                                            <th style="width: 120px; text-align: left; padding: 2px 0">Materials</th>
                                            <td style="padding: 2px 0"><?= if_empty($workOrder['materials'], '-') ?></td>
                                        </tr>
                                    </table>

                                    <?php if($workOrder['handling_category'] == HandlingTypeModel::CATEGORY_WAREHOUSE): ?>
                                        <h4 style="<?php echo $style['paragraph-sub'] ?>">Containers</h4>
                                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px">
                                            <tr style="border-bottom: 2px solid #74787E; text-align: left">
                                                <th style="width: 25px; padding: 4px 2px">No</th>
                                                <th style="padding: 5px 2px">No Container</th>
                                                <th style="padding: 5px 2px">Type</th>
                                                <th style="padding: 5px 2px">Size</th>
                                                <th style="padding: 5px 2px">Item</th>
                                                <th style="padding: 5px 2px">Description</th>
                                            </tr>
                                            <?php $no = 1;
                                            foreach ($containers as $container): ?>
                                                <tr style="border-bottom: 1px solid #74787E;">
                                                    <td style="padding: 4px 2px;"><?= $no++ ?></td>
                                                    <td style="padding: 4px 2px"><?= $container['no_container'] ?></td>
                                                    <td style="padding: 4px 2px"><?= $container['type'] ?></td>
                                                    <td style="padding: 4px 2px"><?= $container['size'] ?></td>
                                                    <td style="padding: 4px 2px"><?= $container['total_item'] ?></td>
                                                    <td style="padding: 4px 2px"><?= $container['description'] == '' ? '-' : $container['description'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>

                                            <?php if(count($containers) <= 0): ?>
                                                <tr>
                                                    <td colspan="6" style="text-align: center; padding: 4px 2px">No data available</td>
                                                </tr>
                                            <?php endif ?>
                                        </table>

                                        <h4 style="<?php echo $style['paragraph-sub'] ?>">Goods</h4>
                                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                                            <tr style="border-bottom: 2px solid #74787E; text-align: left">
                                                <th style="width: 25px; padding: 4px 2px">No</th>
                                                <th style="padding: 5px 2px">Ex Container</th>
                                                <th style="padding: 5px 2px">Goods</th>
                                                <th style="padding: 5px 2px">Quantity</th>
                                                <th style="padding: 5px 2px">Unit</th>
                                                <th style="padding: 5px 2px">Tonnage</th>
                                                <th style="padding: 5px 2px">Volume</th>
                                                <th style="padding: 5px 2px">Position</th>
                                                <th style="padding: 5px 2px">Pallet</th>
                                            </tr>
                                            <?php $no = 1;
                                            foreach ($goods as $good): ?>
                                                <tr style="border-bottom: 1px solid #74787E;">
                                                    <td style="padding: 4px 2px"><?= $no++ ?></td>
                                                    <td style="padding: 4px 2px"><?= $good['no_container'] == '' ? '-' : $good['no_container'] ?></td>
                                                    <td style="padding: 4px 2px"><?= $good['goods_name'] ?></td>
                                                    <td style="padding: 4px 2px"><?= numerical($good['quantity']) ?></td>
                                                    <td style="padding: 4px 2px"><?= $good['unit'] ?></td>
                                                    <td style="padding: 4px 2px"><?= numerical($good['total_weight']) ?></td>
                                                    <td style="padding: 4px 2px"><?= numerical($good['total_volume']) ?></td>
                                                    <td style="padding: 4px 2px"><?= $good['position'] ?></td>
                                                    <td style="padding: 4px 2px"><?= $good['no_pallet'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>

                                            <?php if(count($goods) <= 0): ?>
                                                <tr>
                                                    <td colspan="9" style="text-align: center; padding: 2px">No data available</td>
                                                </tr>
                                            <?php endif ?>
                                        </table>
                                    <?php endif ?>

                                    <p style="<?php echo $style['paragraph-sub'] ?>"><b>Note:</b></p>
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        <?= $description == '' ? '-' : $description ?>
                                    </p>

                                    <!-- Action Button -->
                                    <table style="<?php echo $style['body_action'] ?>" align="center" width="100%"
                                           cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <a href="mailto:cso2@transcon-indonesia.com"
                                                   style="<?php echo $fontFamily ?> <?php echo $style['button'] ?> <?php echo $style['button--blue'] ?>" class="button" target="_blank">
                                                    CONTACT US
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Salutation -->
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        Regards,<br>
                                        <b>Transcon Indonesia</b>
                                    </p>

                                    <!-- Sub Copy -->
                                    <table style="<?php echo $style['body_sub'] ?>">
                                        <tr>
                                            <td style="<?php echo $fontFamily ?>">
                                                <p style="<?php echo $style['paragraph-sub'] ?>">
                                                    This email was intended for <?php echo $handling['customer_email'] ?>.<br>
                                                    &copy; <?=date('Y')?> Transcon Indonesia all rights reserved.<br>
                                                    Jl.Denpasar Blok II No. 1 dan 16 Kbn Marunda Cilincing Jakarta Utara <br>
                                                    Contact: 021-44850578 or Fax: 021-44850403
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td>
                        <table style="<?php echo $style['email-footer'] ?>" align="center" width="650" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="<?php echo $fontFamily ?> <?php echo $style['email-footer_cell'] ?>">
                                    <p style="<?php echo $style['paragraph-sub'] ?>">
                                        &copy; <?php echo date('Y') ?>
                                        <a style="<?php echo $style['anchor'] ?>" href="http://www.transcon-indonesia.com" target="_blank">Transcon Indonesia</a>.
                                        All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>