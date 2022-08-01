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
    'email-body_inner' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0;',
    'email-body_cell' => 'padding: 35px;',

    'email-footer' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;',
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

    'table' => 'margin-top: 0; color: #64686E; font-size: 12px; line-height: 1.5em; text-aligh:left; border-collapse: collapse; border:1px solid #aaaaaa',

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
                            TCI Warehouse
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td style="<?php echo $style['email-body'] ?>" width="100%">
                        <table style="<?php echo $style['email-body_inner'] ?>" align="center" width="570"
                               cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="<?php echo $fontFamily ?> <?php echo $style['email-body_cell'] ?>">
                                    <!-- Greeting -->
                                    <h1 style="<?php echo $style['header-1'] ?>">Booking Complete And Rating</h1>

                                    <!-- Info -->
                                    <h3 style="<?php echo $style['paragraph'] ?>">
                                        Hello, <?= $booking['customer_name'] ?>
                                    </h3>
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        We recently completing booking <b><?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>)</b>
                                        by customer <b><?= $booking['customer_name'] ?></b>. Help us to improve our services, by giving rating completed booking,
                                        we are gathering information for research, you can also give us feedback by visiting our Warehouse system and
                                        access menu [Booking] - [Outstanding Rating].
                                    </p>

                                    <?php if(!empty($bookingContainers)): ?>
                                        <table style="<?php echo $style['table'] ?>" width="100%">
                                            <tr style='border-bottom: 1px solid #aaaaaa;'>
                                                <td style="padding: 4px">No</td>
                                                <td style="padding: 4px">Container</td>
                                                <td style="padding: 4px">Size</td>
                                                <td style="padding: 4px">Type</td>
                                                <td style="padding: 4px">Seal</td>
                                            </tr>
                                            <?php foreach ($bookingContainers as $index => $container): ?>
                                                <tr>
                                                    <td style="padding: 4px"><?= $index + 1 ?></td>
                                                    <td style="padding: 4px"><?= $container['no_container'] ?></td>
                                                    <td style="padding: 4px"><?= $container['size'] ?></td>
                                                    <td style="padding: 4px"><?= $container['type'] ?></td>
                                                    <td style="padding: 4px"><?= $container['seal'] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        </table>

                                        <br>
                                    <?php endif ?>

                                    <?php if(!empty($bookingGoods)): ?>
                                        <table style="<?php echo $style['table'] ?>" width="100%">
                                            <tr style='border-bottom: 1px solid #aaaaaa;'>
                                                <td style="padding: 4px">No</td>
                                                <td style="padding: 4px">Goods</td>
                                                <td style="padding: 4px">Quantity</td>
                                                <td style="padding: 4px">Unit</td>
                                                <td style="padding: 4px">Weight (KG)</td>
                                                <td style="padding: 4px">Gross (KG)</td>
                                                <td style="padding: 4px">Volume</td>
                                            </tr>
                                            <?php foreach ($bookingGoods as $index => $goods): ?>
                                                <tr style='border-bottom: 1px solid #aaaaaa;'>
                                                    <td style="padding: 4px"><?= $index + 1 ?></td>
                                                    <td style="padding: 4px"><?= $goods['goods_name'] ?></td>
                                                    <td style="padding: 4px"><?= numerical($goods['quantity'], 3, true) ?></td>
                                                    <td style="padding: 4px"><?= $goods['unit'] ?></td>
                                                    <td style="padding: 4px"><?= numerical($goods['total_weight'], 3, true) ?></td>
                                                    <td style="padding: 4px"><?= numerical($goods['total_gross_weight'], 3, true) ?></td>
                                                    <td style="padding: 4px"><?= numerical($goods['total_volume']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    <?php endif ?>

                                    <!-- Action Button -->
                                    <table style="<?php echo $style['body_action'] ?>" align="center" width="100%"
                                           cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <a href="<?php echo site_url('booking-rating-public/rate/' . $token, false) ?>?id_booking=<?= $booking['id'] ?>" style="<?php echo $fontFamily ?> <?php echo $style['button'] ?> <?php echo $style['button--red'] ?>"
                                                   class="button" target="_blank">
                                                    RATE US
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Salutation -->
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        Regards,<br>
                                        <b>Warehouse</b>
                                    </p>

                                    <!-- Sub Copy -->
                                    <table style="<?php echo $style['body_sub'] ?>">
                                        <tr>
                                            <td style="<?php echo $fontFamily ?>">
                                                <p style="<?php echo $style['paragraph-sub'] ?>">
                                                    If you're having trouble clicking the "RATE US" button,
                                                    copy and paste the URL below into your web browser:
                                                </p>

                                                <p style="<?php echo $style['paragraph-sub'] ?>">
                                                    <a style="<?php echo $style['anchor'] ?>"
                                                       href="<?php echo site_url('booking-rating-public/rate/' . $token, false) ?>?id_booking=<?= $booking['id'] ?>"
                                                       target="_blank">
                                                        <?php echo site_url('booking-rating-public/rate/' . $token, false) ?>?id_booking=<?= $booking['id'] ?>
                                                    </a>
                                                </p>

                                                <p style="<?php echo $style['paragraph-sub'] ?>">
                                                    This email was intended for
                                                    <a href="mailto:<?php echo $email ?>" style="<?php echo $style['anchor'] ?>">
                                                        <?php echo $email ?>
                                                    </a>
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
                        <table style="<?php echo $style['email-footer'] ?>" align="center" width="570" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="<?php echo $fontFamily ?> <?php echo $style['email-footer_cell'] ?>">
                                    <p style="<?php echo $style['paragraph-sub'] ?>">
                                        &copy; <?php echo date('Y') ?>
                                        <a style="<?php echo $style['anchor'] ?>" href="http://www.transcon-indonesia.com" target="_blank">PLB Warehouse</a>.
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