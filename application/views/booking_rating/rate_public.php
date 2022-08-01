<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Rate Us - Warehouse</title>
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
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/iCheck/square/blue.css') ?>">
    <!-- bootstrap daterangepicker -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/daterangepicker/daterangepicker.css') ?>">
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
                    <a href="<?= site_url('/', false) ?>" class="navbar-brand"><?= $this->config->item('app_name') ?></a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <div class="content-wrapper">
        <div class="container">
            <section class="content">
                <?php $this->load->view('template/_alert') ?>

                <div class="box box-primary">
                    <form action="<?= site_url('booking-rating-public/rate/' . $token, false) ?>?id_booking=<?= $booking['id'] ?>" method="post">
                        <div class="box-header">
                            <h3 class="box-title">Rate Us</h3>
                        </div>
                        <div class="box-body">
                            <p class="lead" style="margin-bottom: 15px">
                                Give booking <strong><?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>)</strong>
                                rating as follow?
                            </p>
                            <div class="form-group">
                                <label for="rate_3" class="control-label mr10" style="vertical-align: bottom">Rate</label>

                                <div class="rating-wrapper">
                                    <i class="rating-star fa fa-star-o" data-star="1"></i>
                                    <i class="rating-star fa fa-star-o" data-star="2"></i>
                                    <i class="rating-star fa fa-star-o" data-star="3"></i>
                                    <i class="rating-star fa fa-star-o" data-star="4"></i>
                                    <i class="rating-star fa fa-star-o" data-star="5"></i>
                                    <div class="rating-input">
                                        <label class="mr20">
                                            <input type="radio" name="rating" id="rate_1" value="1"<?= $booking['rating'] == '1' ? ' checked' : '' ?>>
                                        </label>
                                        <label class="mr20">
                                            <input type="radio" name="rating" id="rate_2" value="2"<?= $booking['rating'] == '2' ? ' checked' : '' ?>>
                                        </label>
                                        <label class="mr20">
                                            <input type="radio" name="rating" id="rate_3" value="3"<?= $booking['rating'] == '3' ? ' checked' : '' ?>>
                                        </label>
                                        <label class="mr20">
                                            <input type="radio" name="rating" id="rate_4" value="4"<?= $booking['rating'] == '4' ? ' checked' : '' ?>>
                                        </label>
                                        <label>
                                            <input type="radio" name="rating" id="rate_5" value="5"<?= $booking['rating'] == '5' ? ' checked' : '' ?>>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description" class="control-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="2" required placeholder="Rate message"><?= $booking['rating_description'] ?></textarea>
                            </div>
                        </div>
                        <div class="box-footer clearfix">
                            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right" id="btn-save-booking">
                                Give Rating
                            </button>
                        </div>
                    </form>
                </div>

                <?php $this->load->view('booking/_view_detail') ?>
            </section>
        </div>
    </div>
</div>


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

<script>
    $('.rating-star').on('click', function () {
        const star = $(this).data('star') || 0;
        $(this).closest('.rating-wrapper').find('.rating-star')
            .removeClass('fa-star')
            .removeClass('fa-star-o');
        for (var i = 1; i <= 5; i++) {
            if(i <= star) {
                $(this).closest('.rating-wrapper').find('.rating-star[data-star="' + i + '"]').addClass('fa-star');
            } else {
                $(this).closest('.rating-wrapper').find('.rating-star[data-star="' + i + '"]').addClass('fa-star-o');
            }
        }

        $(this).closest('.rating-wrapper').find('[name="rating"]').iCheck('uncheck');
        $(this).closest('.rating-wrapper').find('input[value="' + star + '"]').iCheck('check');
    });
</script>
</body>
</html>
