<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $this->config->item('app_name') ?> | Forgot Password</title>
    <link rel="shortcut icon" href="<?= base_url('assets/app/img/layout/favicon.png') ?>">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/template/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/iCheck/square/blue.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/app/css/app.css">

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
<body class="hold-transition login-page" style="height: auto">
<?php if ($this->config->item('is_demo') || (ENVIRONMENT == 'development' && preg_match('/transcon-indonesia.com/', base_url()))): ?>
    <div class="demo-sticky">You're currently accessing demo mode</div>
<?php endif ?>
<div class="login-box" style="margin: 40px auto">
    <div class="login-logo">
        <a href="<?= site_url('/') ?>"><?= $this->config->item('app_name') ?></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Forgot your password</p>

        <!-- alert -->
        <?php if ($this->session->flashdata('status') != NULL): ?>
            <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p><?= $this->session->flashdata('message'); ?></p>
            </div>
        <?php endif ?>
        <!-- end of alert -->

        <form action="<?= site_url('account/forgot') ?>" method="post">
            <div class="form-group has-feedback <?= form_error('email') == '' ?: 'has-error'; ?>">
                <input type="email" class="form-control" name="email" id="email" placeholder="Registered email address"
                    value="<?php echo set_value('email'); ?>">
                <span class="ion-email form-control-feedback"></span>
                <?= form_error('email', '<span class="help-block">', '</span>'); ?>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-flat">Reset Password</button>
        </form>

        <div class="text-center" style="margin-top: 20px">
            <a href="<?= site_url('/') ?>">I remember my password</a>
        </div>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3.1.1 -->
<script src="<?= base_url() ?>assets/plugins/jQuery/jquery-3.1.1.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
