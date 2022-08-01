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
    'email-body_inner' => 'width: auto; margin: 0 auto; padding: 0;',
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
                                    <h1 style="<?php echo $style['header-1'] ?>">Used Space Summary</h1>

                                    <!-- Info -->
                                    <h3 style="<?php echo $style['paragraph'] ?>">
                                        Hello, Admin
                                    </h3>
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        Here we would inform you about current storage usage summary for branch <?= $branch['branch'] ?>:
                                    </p>

                                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; color: #2D2F31; font-size: 13px">
                                        <thead>
                                        <tr style="border-bottom: 1px solid #74787E; border-top: 1px solid #74787E;">
                                            <th rowspan="2" style="padding: 5px 4px; width: 30px">No</th>
                                            <th rowspan="2" style="padding: 5px 4px">Customer</th>
                                            <th colspan="3" style="padding: 5px 4px; text-align: center; border-right: 1px solid #74787E;">Leased Storage (M<sup>2</sup>)</th>
                                            <th colspan="3" style="padding: 5px 4px; text-align: center">Available Storage (Teus)</th>
                                        </tr>
                                        <tr style="border-bottom: 2px solid #74787E;">
                                            <th style="padding: 5px 4px">Warehouse</th>
                                            <th style="padding: 5px 4px">Yard</th>
                                            <th style="padding: 5px 4px; border-right: 1px solid #74787E;">Covered Yard</th>
                                            <th style="padding: 5px 4px">Warehouse Left</th>
                                            <th style="padding: 5px 4px">Yard Left</th>
                                            <th style="padding: 5px 4px">Covered Yard Left</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($customerStorages as $index => $customerStorage): ?>
                                            <tr style="border-bottom: 1px solid #74787E;">
                                                <td style="padding: 5px 4px"><?= $index + 1 ?></td>
                                                <td style="padding: 5px 4px"><?= $customerStorage['name'] ?></td>
                                                <td style="padding: 5px 4px"><?= numerical($customerStorage['warehouse_capacity'], 2, true) ?> / <?= numerical($customerStorage['warehouse_capacity_teus'], 2, true) ?> Teus</td>
                                                <td style="padding: 5px 4px"><?= numerical($customerStorage['yard_capacity'], 2, true) ?> / <?= numerical($customerStorage['yard_capacity_teus'], 2, true) ?> Teus</td>
                                                <td style="padding: 5px 4px; border-right: 1px solid #74787E;"><?= numerical($customerStorage['covered_yard_capacity'], 2, true) ?> / <?= numerical($customerStorage['covered_yard_capacity_teus'], 2, true) ?> Teus</td>
                                                <td style="padding: 5px 4px">
                                                    <?= numerical($customerStorage['warehouse_capacity_teus_left'], 2, true) ?>
                                                    (<?= numerical($customerStorage['warehouse_capacity_teus_left_percent'], 1, true) ?>%)
                                                </td>
                                                <td style="padding: 5px 4px">
                                                    <?= numerical($customerStorage['yard_capacity_teus_left'], 2, true) ?>
                                                    (<?= numerical($customerStorage['yard_capacity_teus_left_percent'], 1, true) ?>%)
                                                </td>
                                                <td style="padding: 5px 4px">
                                                    <?= numerical($customerStorage['covered_yard_capacity_teus_left'], 2, true) ?> 
                                                    (<?= numerical($customerStorage['covered_yard_capacity_teus_left_percent'], 1, true) ?>%)
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <!-- Action Button -->
                                    <table style="<?php echo $style['body_action'] ?>" align="center" width="100%"
                                           cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <a href="<?= site_url('/', false) ?>"
                                                   style="<?php echo $fontFamily ?> <?php echo $style['button'] ?> <?php echo $style['button--blue'] ?>" class="button" target="_blank">
                                                    OPEN DASHBOARD
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
                                                    This email was intended for <?= $email ?>.<br>
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