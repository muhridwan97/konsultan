<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Transcon Indonesia - <?= $title ?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/app/img/layout/favicon.png') ?>">
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
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/AdminLTE.min.css') ?>">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
    apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/skins/skin-blue.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">
    <script type="text/javascript">
        window.baseUrl = <?php echo json_encode(site_url()); ?>;
    </script>


    <!-- jQuery 3.1.1 -->
    <script src="<?= base_url('assets/plugins/jQuery/jquery-3.1.1.min.js') ?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.min.js') ?>"></script>
    <!-- Select2 -->
    <script src="<?= base_url('assets/plugins/select2/select2.full.min.js') ?>"></script>
    <!-- iCheck 1.0.1 -->
    <script src="<?= base_url('assets/plugins/iCheck/icheck.min.js') ?>"></script>
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
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets/template/js/adminlte.min.js') ?>"></script>
</head>

<body id="main-body" class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="<?= site_url('/') ?>" class="navbar-brand"><?= get_setting('app_name') ?></a>
                </div>
            </div>
        </nav>
    </header>
    <!-- Full Width Column -->
    <div class="content-wrapper">
        <div class="container">
            <!-- Main content -->
            <section class="content">
                <?php $this->load->view($page, isset($data) ? $data : null); ?>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="container">
            <div class="pull-right hidden-xs">
                Version <?= get_setting('app_version') ?>
            </div>
            <strong>
                Copyright &copy; <?= date('Y') ?>
                <a href="<?= get_setting('meta_url') ?>"><?= $this->config->item('app_name') ?></a>.
            </strong> All rights reserved.
        </div>
        <!-- /.container -->
    </footer>
</div>

<!-- App js -->
<script src="<?= base_url('assets/app/js/app.js?v=3') ?>"></script>
</body>
</html>
