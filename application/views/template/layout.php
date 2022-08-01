<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title ?> | APP</title>
    <link rel="shortcut icon" href="<?= base_url('assets/app/img/layout/konsultan.png') ?>">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Google Font -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/font-googleapis/css/font-googleapis.css') ?>">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css') ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/ionicons-2.0.1/css/ionicons.min.css') ?>">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datatables/dataTables.bootstrap.css') ?>">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/select2/select2.min.css') ?>">
    <!-- Upload Files -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/jQueryUpload/jquery.fileupload.css') ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/iCheck/square/blue.css') ?>">
    <!-- bootstrap daterangepicker -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/daterangepicker/daterangepicker.css') ?>">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/datepicker/datepicker3.css') ?>">
    <!-- bootstrap timepicker -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/timepicker/bootstrap-timepicker.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/timepicker/bootstrap-timepicker.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/AdminLTE.min.css') ?>">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
    apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/skins/skin-blue.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">
    <script type="text/javascript">
        window.baseUrl = <?php echo json_encode(site_url()); ?>;
        window.assetUrl = <?php echo json_encode(base_url()); ?>;
        window.assetUrlS3 = <?php echo json_encode(rtrim(env('S3_ENDPOINT'), '/') . '/' . env('S3_BUCKET') . '/'); ?>;
    </script>


    <!-- jQuery 3.1.1 -->
    <script src="<?= base_url('assets/plugins/jQuery/jquery-3.1.1.min.js') ?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.min.js') ?>"></script>
    <!-- Select2 -->
    <script src="<?= base_url('assets/plugins/select2/select2.full.min.js') ?>"></script>
    <!-- iCheck 1.0.1 -->
    <script src="<?= base_url('assets/plugins/iCheck/icheck.min.js') ?>"></script>
    <!-- jquery validation -->
    <script src="<?= base_url('assets/plugins/jquery-validation/jquery.validate.min.js') ?>"></script>
    <!-- DataTables -->
    <script src="<?= base_url('assets/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/datatables/dataTables.bootstrap.min.js') ?>"></script>
    <!-- iCheck -->
    <script src="<?= base_url('assets/plugins/iCheck/icheck.min.js') ?>"></script>
    <!-- Upload Files -->
    <script src="<?= base_url('assets/plugins/jQueryUpload/jquery.ui.widget.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/jQueryUpload/jquery.iframe-transport.js') ?>"></script>
    <script src="<?= base_url('assets/plugins/jQueryUpload/jquery.fileupload.js') ?>"></script>
    <!-- Moment js -->
    <script src="<?= base_url('assets/plugins/daterangepicker/moment.js') ?>"></script>
    <!-- bootstrap daterangepicker -->
    <script src="<?= base_url('assets/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    <!-- bootstrap datepicker -->
    <script src="<?= base_url('assets/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>
    <!-- bootstrap timepicker -->
    <script src="<?= base_url('assets/plugins/timepicker/bootstrap-timepicker.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets/template/js/adminlte.min.js') ?>"></script>
</head>

<body id="main-body" class="hold-transition skin-blue <?= isset($collapse) && $collapse ? 'sidebar-collapse' : '' ?> sidebar-mini">
<?php if ($this->config->item('is_demo')): ?>
    <div class="demo-sticky">You're currently accessing demo mode</div>
<?php endif; ?>
<?php if (!empty(env('STICKY_MESSAGE'))): ?>
    <div class="demo-sticky danger"><?= env('STICKY_MESSAGE') ?></div>
<?php endif; ?>
<?php if (get_setting('lock_opname', 0)): ?>
    <div class="demo-sticky danger">Stock Opname in progress, handling and tally feature is temporarily locked</div>
<?php endif; ?>
<div class="wrapper">

    <?php $this->load->view('template/header'); ?>
    <?php $this->load->view('template/sidebar'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $title ?>
                <small><?php echo isset($subtitle) ? $subtitle : '' ?></small>
            </h1>
            <ol class="breadcrumb">
                <?php
                $segment1 = $this->uri->segment(1);
                $segment2 = $this->uri->segment(2);
                if ($segment1 == 'p') {
                    $segment1 = $this->uri->segment(3);
                    $segment2 = $this->uri->segment(4);
                }
                ?>
                <li><a href="<?= site_url('dashboard') ?>">Home</a></li>
                <?php if (!is_null($segment1)): ?>
                    <li><a href="<?= site_url($segment1) ?>"><?= ucwords(str_replace(['_', '-'], ' ', $segment1)) ?></a></li>
                <?php endif; ?>
                <?php if (!is_null($segment2)): ?>
                    <li class="active"><?= ucwords(str_replace(['_', '-'], ' ', $segment2)) ?></li>
                <?php endif; ?>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

            <?php $this->load->view($page, isset($data) ? $data : null); ?>

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <?php $this->load->view('template/footer'); ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->
<script type="text/javascript">
    if ($(window).width() > 960) {
        var bodyTag = document.getElementsByTagName("BODY")[0];
        if (localStorage.getItem('tci-sidebar') === '0') {
            bodyTag.className += " sidebar-collapse";
        }
    }
</script>

<!-- App js -->
<script src="<?= base_url('assets/app/js/app.js?v=5') ?>"></script>
<script src="<?= base_url('assets/app/js/report.js?v=6') ?>"></script>

</body>
</html>