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
	</head>
	<body onload="window.print();">
    <div class="row">
        <div class="col-md-10 col-md-push-1">
            <div class="wrapper">
                <section class="invoice">
                    <div class="page-header mt0">
                        <?php if(isset($invoice)): ?>
                            <div class="<?= $invoice['status'] == 'PUBLISHED' ? 'text-success' : '' ?> pull-right text-center" style="line-height: 0.7; padding: 7px 10px; margin-top: 10px; border: 2px solid <?= $invoice['status'] == 'PUBLISHED' ? '#3c763d' : '#333333' ?>">
                                <?= $invoice['status'] ?><br>
                                <small style="font-size: 14px">
                                    <?php if($invoice['status'] == 'DRAFT'): ?>
                                        DRAFT-
                                    <?php endif; ?>
                                    <?= $invoice['no_invoice'] ?>
                                </small>
                            </div>
                        <?php endif; ?>
                        <img class="pull-left" style="margin-top: 5px" src="<?= base_url('assets/app/img/layout/transcon_logo.png') ?>" alt="Transcon Logo">
                        <div style="margin-left: 120px">
                            <p class="lead mb0" style="font-weight: bold">
                                PT. TRANSCON INDONESIA
                            </p>
                            <p class="small text-muted" style="font-size: 16px">
                                Jl. Denpasar Blok 1 No. 1 dan 16 KBN Marunda, Cilincing, Jakarta Utara 14120<br>
                                Telp: 44850578, Fax: 44850403
                            </p>
                        </div>
                    </div>
                    <?php $this->load->view($page); ?>
                </section>
            </div>
        </div>
    </div>
	</body>
</html>