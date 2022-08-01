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
		<style>
        @media screen {
            div.divFooter {
                display: none;
            }
        }
        @media print {
            div.divFooter {
                position: fixed;
                bottom: 0;
                right: 0;
            }
        }	
    	</style>
		<!-- Google Font -->
		<link rel="stylesheet"
		href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <script type="text/javascript">
            window.baseUrl = <?php echo json_encode(base_url()); ?>
		</script>
	</head>
	<body onload="window.print();">
    <div class="row">
        <div class="col-md-8 col-md-push-2">
            <div class="wrapper">
                <!-- Main content -->
                <section class="invoice">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="page-header clearfix" style="padding-bottom: 15px; margin-bottom: 15px; margin-top: 0">
                                <div class="pull-left">
                                    <p class="lead" style="margin-bottom: 0; line-height: .8; font-weight: bold">Transcon Indonesia</p>
                                    <small class="text-muted" style="letter-spacing: 1px">www.transcon-indonesia.com</small>
                                </div>
                                <small class="pull-right">Print Date: <strong><?= date('d F Y H:i') ?></strong></small>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <?php $this->load->view($page, isset($data) ? $data : null); ?>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
        </div>
    </div>
	</body>
</html>