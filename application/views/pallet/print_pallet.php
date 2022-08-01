
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title ?> | Warehouse</title>
    <link rel="shortcut icon" href="<?= base_url('assets/app/img/layout/favicon.png') ?>">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/AdminLTE.min.css') ?>">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
    apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/skins/skin-blue.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <script type="text/javascript">
        window.baseUrl = <?php echo json_encode(base_url()); ?>;
    </script>
</head>
<body onload="window.print();">
<div class="row">
    <div class="col-md-8 col-md-push-2">
        <div class="wrapper">
            <section class="invoice">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div class="page-header" style="margin-top: 0">
                            <img class="pull-left" src="<?= base_url('assets/app/img/layout/transcon_logo.png') ?>" alt="Transcon logo"
                                 style="max-width: 80px; margin-top: 5px">
                            <p class="pull-right text-right" style="font-size: 14px; margin-top: 10px">Print Date: <br><strong><?= date('d F Y H:i') ?></strong></p>
                            <h2 style="margin: 0">Pallet Marking</h2>
                            <p class="lead" style="margin-bottom: 10px">No: <?= $pallet['no_pallet'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <?php $palletQr = $barcode->getBarcodePNG($pallet['no_pallet'], "QRCODE", 8, 8); ?>
                        <img src="data:image/png;base64,<?= $palletQr ?>" alt="<?= $pallet['no_pallet'] ?>">
                    </div>
                    <div class="col-sm-9">
                        <table class="table" style="font-size: 120%">
                            <tr>
                                <th>No Booking</th>
                                <td><?= if_empty($pallet['no_booking'], 'No booking') ?></td>
                            </tr>
                            <tr>
                                <th>No Reference</th>
                                <td><?= if_empty($pallet['no_reference'], 'No reference') ?></td>
                            </tr>
                            <tr>
                                <th>Owner</th>
                                <td><?= if_empty($pallet['customer_name'], 'No customer') ?></td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td><?= readable_date($pallet['created_at']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
</body>
</html>

