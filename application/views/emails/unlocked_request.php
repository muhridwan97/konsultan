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
                            Transcon Indonesia
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
                                    <h1 style="<?php echo $style['header-1'] ?>">Unlocked Tally Request </h1>

                                    <!-- Info -->
                                    <h3 style="<?php echo $style['paragraph'] ?>">
                                        Hello, Admin
                                    </h3>
                                    <p style="<?php echo $style['paragraph'] ?>">
                                        Here we would inform you that <?php echo $customer['name'] ?> request to unlock tally:
                                        <br>No Work Order: <b><?= $workOrder['no_work_order'] ?></b>
                                        <br>Date Unlock From: <b><?= $workOrder['date_from'] ?></b>
                                        <br>Date Unlock To: <b><?= $workOrder['date_to'] ?></b>
                                        <br>Time Request: <b><?= $workOrder['mail_date'] ?></b>
                                        <br>Branch Request: <b><?= get_active_branch('branch') ?></b>
                                        <br>Description: <b><?= $workOrder['description'] ?></b>
                                    </p>

                                    <?php if (!empty($relatedWorkOrders)): ?>
                                        <h3 style="<?php echo $style['paragraph'] ?>">Related Job:</h3>
                                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; color: #2F3133">
                                            <tr style="border-bottom: 2px solid #74787E; text-align: left">
                                                <th style="width: 30px; padding: 4px 2px">No</th>
                                                <th style="padding: 5px 2px">No Work Order</th>
                                                <th style="padding: 5px 2px">No Reference</th>
                                            </tr>
                                            <?php $no = 1 ?>
                                            <?php foreach ($relatedWorkOrders as $index => $relatedWorkOrder): ?>
                                                <tr style="border-bottom: 1px solid #74787E;">
                                                    <td style="padding: 4px 2px;"><?= $no++ ?></td>
                                                    <td style="padding: 4px 2px"><?= $relatedWorkOrder['no_work_order'] ?></td>
                                                    <td style="padding: 4px 2px"><?= $relatedWorkOrder['no_reference'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    <?php endif; ?>

                                    <p style="<?php echo $style['paragraph'] ?>">
                                        Please accept or reject the unlock tally request by our
                                        <a href="<?= base_url('p/'.$workOrder['id_branch'].'/work-order/request-unlock-tally') ?>">system</a>.
                                        If you think this information wrong or irrelevant please contact our
                                        IT support</a>
                                    </p>

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
                                                    This email was intended for <?php echo $operationsAdministratorEmail ?>.<br>
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
                        <table style="<?php echo $style['email-footer'] ?>" align="center" width="570" cellpadding="0" cellspacing="0">
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