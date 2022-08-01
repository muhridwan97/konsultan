<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

    <style type="text/css" rel="stylesheet" media="all">
        /* Media Queries */
        @media only screen and (max-width: 500px) {
            .button {
                box-sizing: border-box;
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
    'email-body_inner' => 'max-width: 600px; width: auto; margin: 0 auto; padding: 0;',
    'email-body_cell' => 'padding: 25px;',

    'email-footer' => 'width: auto; margin: 0 auto; padding: 0; text-align: center;',
    'email-footer_cell' => 'color: #AEAEAE; padding: 25px; text-align: center;',

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

    'table' => 'margin-top: 0; color: #64686E; font-size: 12px; line-height: 1.5em; text-aligh:left; border-collapse: collapse;',

    /* Buttons ------------------------------ */

    'button' => 'display: block; display: inline-block; width: 200px; min-height: 20px; padding: 10px;
                 background-color: #3869D4; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                 text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',

    'button--green' => 'background-color: #22BC66;',
    'button--red' => 'background-color: #dc4d2f;',
    'button--blue' => 'background-color: #3869D4;',
    'button--small' => 'padding: 7px 10px; line-height: 16px; min-height: auto; width: auto; font-size: 14px;',
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
                           href="<?= site_url() ?>" target="_blank">
                            <?= get_setting('app_name', $this->config->item('app_name')) ?>
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td style="<?php echo $style['email-body'] ?>" width="100%">
                        <table style="<?php echo $style['email-body_inner'] ?>" align="center" width="auto" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="<?php echo $fontFamily ?> <?php echo $style['email-body_cell'] ?>">
                                    <!-- Greeting -->
                                    <h1 style="<?php echo $style['header-1'] ?>"><?= isset($title) ? $title : 'Notification' ?></h1>

                                    <!-- Info -->
                                    <h3 style="<?php echo $style['paragraph'] ?>">
                                        Dear, Management
                                    </h3>
                                    <div style="<?php echo $style['paragraph'] ?>">
                                        <?php if(count($payments) > 1): ?>
                                            <?= count($payments) ?> booking payment requested approval by
                                            <?= UserModel::authenticatedUserData('name') ?> as follow:
                                        <?php else: ?>
                                            <?php $payment =$payments[0] ?>
                                            Booking <?= $payment['no_reference'] ?> by customer <?= $payment['customer_name'] ?> requested payment <?= $payment['no_payment'] ?> (<?= $payment['payment_type'] ?>)
                                            to bank account <?= $payment['bank'] ?> - <?= $payment['account_number'] ?>
                                            with amount Rp. <?= numerical(if_empty($payment['amount'], $payment['amount_request']), 2, true) ?> (Payment bank updated by <?= UserModel::authenticatedUserData('name') ?>)
                                            <br>
                                        <?php endif; ?>
                                    </div>

                                    <h3 style="<?php echo $style['paragraph'] ?>">
                                        Payment request transaction
                                    </h3>

                                    <hr style="height: 0; border: 0; border-top: 1px solid #ddd; margin-bottom: 0">

                                    <?php foreach ($payments as $payment): ?>

                                        <table style="<?php echo $style['table'] ?>" width="100%">
                                            <tr>
                                                <td style="padding-top: 10px; padding-right: 20px; width: 110px">No Payment</td>
                                                <td style="padding-top: 10px"><?= $payment['no_payment'] ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 20px">Customer</td>
                                                <td><?= $payment['customer_name'] ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 20px">No Reference</td>
                                                <td><?= if_empty($payment['no_reference'], $payment['upload_description']) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 20px">Request Date</td>
                                                <td><?= format_date(if_empty($payment['payment_date'], $payment['created_at']), 'd F Y H:i') ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 20px">Description</td>
                                                <td><?= if_empty($payment['invoice_description'], $payment['description']) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 20px">From Bank</td>
                                                <td><?= if_empty($payment['bank'], '-') ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold; padding-right: 20px">Total Amount</td>
                                                <td style="font-weight: bold">Rp. <?= numerical(if_empty($payment['amount'], $payment['amount_request']), 2, true) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold; padding-bottom: 10px; border-bottom: 1px solid #ddd; padding-right: 20px;">Action</td>
                                                <td style="padding-bottom: 10px; border-bottom: 1px solid #ddd;">
                                                    <a href="<?= site_url('payment-check/validate-payment/' . $payment['id'] . '/' . $payment['token'] . '?email=' . base64_encode($email), false) ?>"
                                                       style="<?php echo $fontFamily ?> <?php echo $style['button'] ?> <?php echo $style['button--green'] ?> <?php echo $style['button--small'] ?>" class="button" target="_blank">
                                                        CLICK TO VALIDATE
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php endforeach; ?>

                                    <br>

                                    <!-- Salutation -->
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        Regards,<br>
                                        <b><?= $this->config->item('app_name') ?></b>
                                    </p>

                                    <!-- Sub Copy -->
                                    <table width="100%" style="<?php echo $style['body_sub'] ?>">
                                        <tr>
                                            <td style="<?php echo $fontFamily ?>">
                                                <p style="<?php echo $style['paragraph-sub'] ?>">
                                                    This email was intended for
                                                    <a href="mailto:<?= $email ?>" style="<?php echo $style['anchor'] ?>">
                                                        <?= $email ?>
                                                    </a> and all emails that included in carbon.
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
                        <table style="<?php echo $style['email-footer'] ?>" align="center" width="auto" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="<?php echo $fontFamily ?> <?php echo $style['email-footer_cell'] ?>">
                                    <p style="<?php echo $style['paragraph-sub'] ?>">
                                        &copy; <?php echo date('Y') ?>
                                        <a style="<?php echo $style['anchor'] ?>" href="<?= site_url() ?>" target="_blank">
                                            <?= get_setting('app_name', $this->config->item('app_name')) ?>
                                        </a>.
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
