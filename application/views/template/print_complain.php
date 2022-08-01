<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

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
	     <div class="row">
	        <div class="col-md-10 col-md-push-1">
	            <div class="wrapper">
	                <section class="invoice">
	                        <table class="table table-bordered no-datatable">
	                             <tr>
	                             	<th rowspan="2"><center><img style="margin-top: 5px;" src="<?php echo base_url('assets/app/img/layout/transcon_logo.png');?>" /></center></th>
	                                <th rowspan="2" class="text-center">
	                                    <p style="font-weight: bold">
	                                        PT. TRANSCON INDONESIA
	                                    </p>
	                                    <p class="lead mb0" class="small text-muted" style="font-size: 11px">
	                                        Jl. Denpasar Blok 1 No. 1 dan 16 <br> KBN Marunda, Cilincing, Jakarta Utara 14120<br>
	                                        Telp: 44850578, Fax: 44850403
	                                    </p>
	                                </th>
	                                <th class="text-center" rowspan="2" style="font-weight: bold;">LAPORAN KELUHAN PELANGGAN</th>
	                                <th class="text-left" style="font-weight: bold;">1. FTKP NO : <?= strtoupper($complain['ftkp']) ?></th>
	                            </tr>
	                            <tr>
	                                <th class="text-left">2. TANGGAL : <?= date('d F Y', strtotime($complain['created_at'])) ?></th>
	                            </tr>
	                        </table>
	                    <?php $this->load->view($page); ?>
	                </section>
	            </div>
	        </div>
	    </div>
	</body>
</html>