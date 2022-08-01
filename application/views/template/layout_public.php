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
</head>

<body id="main-body" class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="<?= site_url('client_area') ?>" class="navbar-brand">Client Area</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <?php
                $segment1 = $this->uri->segment(1);
                $segment2 = $this->uri->segment(2);
                ?>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li<?= $segment1 == 'client_area' && ($segment2 == '' || $segment2 == 'index') ? ' class="active"' : '' ?>><a href="<?= site_url('client_area') ?>">Home</a></li>
                        <li<?= $segment2 == 'invoice' ? ' class="active"' : '' ?>><a href="<?= site_url('client_area/invoice') ?>">Invoice</a></li>
                        <li<?= $segment2 == 'container' ? ' class="active"' : '' ?>><a href="<?= site_url('client_area/container') ?>">Container</a></li>
                        <li<?= $segment2 == 'news' ? ' class="active"' : '' ?>><a href="<?= site_url('client_area/news') ?>">News</a></li>
                        <li class="dropdown<?= $segment2 == 'help' ? ' active' : '' ?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">More <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="http://transcon-indonesia.com">Transcon Indonesia</a></li>
                                <li<?= $segment2 == 'help' ? ' class="active"' : '' ?>><a href="<?= site_url('client_area/help') ?>">Help & Support</a></li>
                                <li class="divider"></li>
                                <li><a href="#modal-information" data-toggle="modal">Contact Us</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <?php if(UserModel::isLoggedIn()): ?>
                            <!-- User Account Menu -->
                            <li class="dropdown user user-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <!-- The user image in the navbar-->
                                    <img src="<?= base_url('assets/app/img/avatar/no-avatar.jpg') ?>" class="user-image" alt="User Image">
                                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                    <span class="hidden-xs"><?= UserModel::authenticatedUserData('name') ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">
                                        <img src="<?= base_url('assets/app/img/avatar/no-avatar.jpg') ?>" class="img-circle" alt="User Image">

                                        <p>
                                            <?= UserModel::authenticatedUserData('name') ?>
                                            <small><?= UserModel::authenticatedUserData('email') ?></small>
                                        </p>
                                    </li>
                                    <!-- Menu Body -->
                                    <?php $customerBranches = get_customer_branch(); ?>
                                    <?php if (!empty($customerBranches) && $this->config->item('enable_branch_mode')) : ?>
                                        <?php $no = 1; foreach ($customerBranches as $customerBranch) : ?>
                                            <li class="user-body" id="list-branch-<?= $no++ ?>">
                                                <div class="row">
                                                    <div class="col-xs-12 text-center">
                                                        <?php $uriString = preg_replace("/p\/[0-9]+\//", '', uri_string()) ?>
                                                        <a href="<?= site_url('p/' . $customerBranch['id'] . '/', false) . $uriString . '?' . $_SERVER['QUERY_STRING'] ?>"
                                                           style="display: block">
                                                            <?= $customerBranch['branch'] ?>
                                                            <?= $customerBranch['id'] == get_active_branch('id') ? '(current)' : '' ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!-- /.row -->
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?= site_url('account') ?>" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?= site_url('account/logout', false) ?>" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="<?= site_url('welcome') ?>">Login</a></li>
                            <li><a href="#modal-information" data-toggle="modal">Request Account?</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- /.navbar-custom-menu -->
            </div>
            <!-- /.container-fluid -->
        </nav>
    </header>
    <!-- Full Width Column -->
    <div class="content-wrapper">
        <div class="container">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1><?= $title ?></h1>
                <p><?= $subtitle ?></p>
                <ol class="breadcrumb">
                    <?php
                    $segment1 = $this->uri->segment(1);
                    $segment2 = $this->uri->segment(2);
                    if ($segment1 == 'p') {
                        $segment1 = $this->uri->segment(3);
                        $segment2 = $this->uri->segment(4);
                    }
                    ?>
                    <li><a href="<?= site_url('client_area') ?>">Home</a></li>
                    <?php if (!is_null($segment1)): ?>
                        <li><a href="<?= site_url($segment1) ?>"><?= ucwords(str_replace('_', ' ', $segment1)) ?></a></li>
                    <?php endif; ?>
                    <?php if (!is_null($segment2)): ?>
                        <li class="active"><?= ucwords(str_replace('_', ' ', $segment2)) ?></li>
                    <?php endif; ?>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <?php $this->load->view($page); ?>
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
            </strong> All rights
            reserved.
        </div>
        <!-- /.container -->
    </footer>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="modal-information">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Information</h4>
            </div>
            <div class="modal-body">
                <p class="lead" style="margin-bottom: 0">
                    Further information please contact our support
                    via email <a href="mailto:cso@transcon-indonesia.com">cso@transcon-indonesia.com</a>
                    or by phone <a href="tel:+622144850578">+622144850578</a>
                </p>
                <p class="text-muted">
                    Detail our contacts available on <a href="http://www.transcon-indonesia.com">http://www.transcon-indonesia.com</a>
                </p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


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
<!-- ChartJS 1.0.1 -->
<script src="<?= base_url('assets/plugins/chartjs/Chart.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets/template/js/adminlte.min.js') ?>"></script>
<!-- App js -->
<script src="<?= base_url('assets/app/js/app.js?v=3') ?>"></script>
<script src="<?= base_url('assets/app/js/client_area.js') ?>"></script>
</body>
</html>