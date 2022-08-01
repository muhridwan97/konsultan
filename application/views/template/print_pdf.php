<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?= $title ?> | Warehouse</title>
        <link rel="shortcut icon" href="<?= base_url('assets/app/img/layout/favicon.png') ?>">
		<!-- Bootstrap 3.3.7 -->
		<link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>">
		<link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">
        <style>
            @page { margin: 20px 10px 15px 10px; }
            body { margin: 20px 10px 15px 10px; }
        </style>
    </head>
	<body>
    <?php $this->load->view($page); ?>
	</body>
</html>