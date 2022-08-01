<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $this->config->item('app_name') ?> | Log in</title>
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
<?php endif; ?>
<div class="login-box" style="margin: 40px auto">
    <div class="login-logo">
        <a href="<?= site_url('/') ?>"><?= $this->config->item('app_name') ?></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <!-- alert -->
        <?php if ($this->session->flashdata('status') != NULL): ?>
            <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p><?= $this->session->flashdata('message'); ?></p>
            </div>
        <?php endif; ?>
        <!-- end of alert -->

        <form action="<?= site_url('account/auth') ?>" method="post">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" name="username" id="username" placeholder="Username of email"
                    value="<?php echo set_value('username'); ?>">
                <span class="ion-email form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                <span class="ion-locked form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="checkbox icheck" style="margin-top: 0">
                        <label>
                            <input type="checkbox" name="remember" id="remember" value="1"> Remember Me
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-6 text-right">
                    <a href="<?= site_url('welcome/forgot') ?>" style="margin-top: 10px">
                        I forgot my password
                    </a>
                </div>
                <!-- /.col -->
            </div>
            <div class="form-group has-feedback" style="margin-top: 10px">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div>
        </form>

        <div class="text-center">
            <a href="<?= site_url('client_area') ?>" class="text-center">Visit public client area</a>
            <!-- <a href="<?= site_url('welcome/register') ?>" class="text-center">Register a new membership</a> -->
        </div>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<p class="text-center">
    &copy; <?= date('Y') ?> <a href="<?= site_url('/') ?>"><strong><?= $this->config->item('app_author') ?></strong></a> all rights reserved.
</p>

<!-- jQuery 3.1.1 -->
<script src="<?= base_url() ?>assets/plugins/jQuery/jquery-3.1.1.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?= base_url() ?>assets/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
