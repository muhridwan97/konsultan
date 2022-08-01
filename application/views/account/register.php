<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $this->config->item('app_name') ?> | Registration Page</title>
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
<body class="hold-transition register-page" style="height: auto">
<?php if ($this->config->item('is_demo') || (ENVIRONMENT == 'development' && preg_match('/transcon-indonesia.com/', base_url()))): ?>
    <div class="demo-sticky">You're currently accessing demo mode</div>
<?php endif; ?>
<div class="register-box" style="margin: 40px auto">
    <div class="register-logo">
        <a href="<?= site_url('/') ?>"><?= $this->config->item('app_name') ?></a>
    </div>

    <div class="register-box-body">
        <p class="login-box-msg">Register a new membership</p>

        <!-- alert -->
        <?php if ($this->session->flashdata('status') != NULL): ?>
            <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p><?= $this->session->flashdata('message'); ?></p>
            </div>
        <?php endif; ?>
        <!-- end of alert -->

        <form action="<?= site_url('account/register') ?>" method="post">
            <div class="form-group has-feedback <?= form_error('name') == '' ?: 'has-error'; ?>">
                <input type="text" class="form-control" placeholder="Full name" name="name" id="name" value="<?= set_value('name') ?>">
                <span class="ion-person-stalker form-control-feedback"></span>
                <?= form_error('name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group has-feedback <?= form_error('username') == '' ?: 'has-error'; ?>">
                <input type="text" class="form-control" placeholder="Username" name="username" id="username" value="<?= set_value('username') ?>">
                <span class="ion-person form-control-feedback"></span>
                <?= form_error('username', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group has-feedback <?= form_error('email') == '' ?: 'has-error'; ?>">
                <input type="email" class="form-control" placeholder="Email" name="email" id="email" value="<?= set_value('email') ?>">
                <span class="ion-email form-control-feedback"></span>
                <?= form_error('email', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group has-feedback <?= form_error('password') == '' ?: 'has-error'; ?>">
                <input type="password" class="form-control" placeholder="Password" name="password" id="password">
                <span class="ion-locked form-control-feedback"></span>
                <?= form_error('password', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group has-feedback <?= form_error('confirm_password') == '' ?: 'has-error'; ?>">
                <input type="password" class="form-control" placeholder="Retype password" name="confirm_password" id="confirm_password">
                <span class="ion-unlocked form-control-feedback"></span>
                <?= form_error('confirm_password', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group has-feedback <?= form_error('agree') == '' ?: 'has-error'; ?>">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="agree" id="agree">
                        I agree to the <a href="<?= site_url('help/terms') ?>" target="_blank">terms and conditions</a>
                    </label>
                </div>
                <?= form_error('agree', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group has-feedback">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
            </div>
        </form>

        <div class="text-center">
            <a href="<?= site_url('welcome') ?>" class="text-center">I already have a membership</a>
        </div>
    </div>
    <!-- /.form-box -->
</div>
<!-- /.register-box -->

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
