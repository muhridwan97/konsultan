<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Handling Warehouse</title>
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
    <div class="col-md-5 col-md-push-3">
        <div class="wrapper">
            <section class="invoice">
                <div class="text-center">
                    <h3>Transcon Handling Working Pass</h3>
                    <p style="font-size: 16px; letter-spacing: 1px">www.transcon-indonesia.com</p>
                    <hr>
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $handling['no_handling'] ?>">
                    <p class="lead" style="margin-top: 10px">No Handling: <?= $handling['no_handling'] ?></p>
                    <hr>
                </div>
                <form class="form-horizontal form-view">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Branch</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $handling['branch'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Customer</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $handling['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">No Booking</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $handling['no_booking'] ?> (<?= $handling['no_reference'] ?>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Type Handling</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $handling['handling_type'] ?> (<?= $handling['handling_category'] ?>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Handling Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= (new DateTime($handling['handling_date']))->format('d F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($handling['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </form>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
</div>
</body>
</html>