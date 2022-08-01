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
    'email-body_inner' => 'width: auto; max-width: 600px; margin: 0 auto; padding: 0;',
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
                        <table style="<?php echo $style['email-body_inner'] ?>" align="center" width="600"
                               cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="<?php echo $fontFamily ?> <?php echo $style['email-body_cell'] ?>">
                                    <!-- Greeting -->
                                    <h1 style="<?php echo $style['header-1'] ?>"><?= isset($title) ? $title : 'Notification' ?></h1>

                                    <!-- Info -->
                                    <h3 style="<?php echo $style['paragraph'] ?>">
                                        Hello, <?= isset($user['name']) ? $user['name'] : 'user' ?>
                                    </h3>
                                    <div style="<?php echo $style['paragraph'] ?>" align="justify">
                                        <?= $content ?>
                                        <br>
                                        <br>
                                       If you think this document complete, please approve document by clicking the approve button below. But if you think this document response is not complete or not valid, please revise document by clicking the revise button below.
                                    </div>
                                    <!-- Action Button -->
                                    <table style="<?php echo $style['body_action'] ?>" align="center" width="100%"
                                           cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center">
                                                <a href="<?php echo site_url('response/revise_response/'.$token); ?>?id=<?= $uploadDocumentId ?>"
                                                   style="<?php echo $fontFamily ?> <?php echo $style['button'] ?> <?php echo $style['button--red'] ?>" class="button" target="_blank">
                                                    REVISE
                                                </a>
                                            </td>
                                            <td align="center">
                                                <a href="<?php echo site_url('response/approve_response/'.$token); ?>?id=<?= $uploadDocumentId ?>&email=<?= ($user['email'] ?? '') ?>"
                                                   style="<?php echo $fontFamily ?> <?php echo $style['button'] ?> <?php echo $style['button--blue'] ?>" class="button" target="_blank">
                                                    APPROVE
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Info -->
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        For more further information please contact our
                                        <a href="mailto:cso1@transcon-indonesia.com">customer support</a>
                                    </p>

                                    <!-- Salutation -->
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        Regards,<br>
                                        <b>Transcon Indonesia</b>
                                    </p>

                                    <!-- Sub Copy -->
                                    <table width="100%" style="<?php echo $style['body_sub'] ?>">
                                        <tr>
                                            <td style="<?php echo $fontFamily ?>">
                                                <p style="<?php echo $style['paragraph-sub'] ?>" >
                                                    If you're having trouble clicking the "Approve" button,
                                                    copy and paste the URL below into your web browser:
                                                    <a style="<?php echo $style['anchor'] ?>"
                                                       href="<?= site_url('response/approve_response/' . $token); ?>?id=<?= $uploadDocumentId ?>&email=<?= ($user['email'] ?? '') ?>"
                                                       target="_blank">
                                                        <?= site_url('response/approve_response/' . $token); ?>?id=<?= $uploadDocumentId ?>&email=<?= ($user['email'] ?? '') ?>
                                                    </a>. If you're having trouble clicking the "Revise" button,
                                                    copy and paste the URL below into your web browser:
                                                    <a style="<?php echo $style['anchor'] ?>" href="<?= site_url('response/revise_response/' . $token); ?>?id=<?= $uploadDocumentId ?>" target="_blank">
                                                        <?= site_url('response/revise_response/' . $token); ?>?id=<?= $uploadDocumentId ?>
                                                    </a>
                                                </p>
                                                <p style="<?php echo $style['paragraph-sub'] ?>">
                                                    This email was intended for <?php echo isset($user['email']) ? $user['email'] : 'user' ?>.<br>
                                                    &copy; <?=date('Y')?> Transcon Indonesia all rights reserved.<br>
                                                    Jl.Denpasar Blok II No.1 dan 16 Kbn Marunda Cilincing Jakarta Utara <br>
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
                        <table style="<?php echo $style['email-footer'] ?>" align="center" width="600" cellpadding="0" cellspacing="0">
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