<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PLB Warehouse System | Explain</title>
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
<div class="box-header" style="margin: 50px;">
    <div class="register-logo">
        <?= $this->config->item('app_name') ?>
    </div>

    <?php $this->load->view('template/_alert') ?>
    <form action="<?= site_url("discrepancy-handover-confirmation/save-explanation/{$token}?id=" . get_url_param('id') . "&email=" . get_url_param('email')) ?>" method="post" enctype="multipart/form-data">
        <div class="box-body" style="background-color: white;">
            <h4 class="text-center">
                <strong>Explain About Discrepancy</strong>
            </h4>
            <div class="box-body">
                <table class="table mb20">
                    <tr>
                        <td><strong>No Discrepancy</strong></td>
                        <td><?= $discrepancyHandover['no_discrepancy']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Customer Name</strong></td>
                        <td><?= $discrepancyHandover['customer_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>No Reference</strong></td>
                        <td><?= $discrepancyHandover['no_reference']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Explanation</strong></td>
                        <td>
                            <textarea class="form-control" name="explanation" id="explanation" required
                                      placeholder="Explain about discrepancy" rows="2" aria-label="Explanation"><?= set_value('explanation', $discrepancyHandover['explanation']) ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Attachment (If Exist)</strong></td>
                        <td>
                            <div class="form-group">
                                <input type="file" id="attachment" name="attachment" class="file-upload-default upload-photo" >
                                <div class="input-group col-xs-12">
                                    <input type="text" name="attachment_info" id="attachment_info" class="form-control file-upload-info" style="pointer-events: none; color:#AAA; background:#F5F5F5; webkit-touch-callout: none;" onkeydown="return false" placeholder="Upload attachment">
                                    <span class="input-group-btn">
                                        <button class="file-upload-browse btn btn-default btn-simple-upload" type="button">
                                            Select File
                                        </button>
                                    </span>
                                </div>
                            </div>

                            <?php if (!empty($discrepancyHandover['explanation_attachment'])): ?>
                                Existing Attachment: <a href="<?= asset_url($discrepancyHandover['explanation_attachment']) ?>">
                                    <?= basename($discrepancyHandover['explanation_attachment']) ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Discrepancy Goods</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped no-datatable responsive">
                            <thead>
                            <tr>
                                <th style="width: 30px">No</th>
                                <th>Source</th>
                                <th>Type</th>
                                <th>Goods</th>
                                <th>Stock Exist</th>
                                <th>Qty Booking</th>
                                <th>Qty Stock</th>
                                <th>Qty Diff</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($discrepancyHandoverGoods as $discrepancyHandoverItem): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $discrepancyHandoverItem['source'] ?></td>
                                    <td><?= $discrepancyHandoverItem['assembly_type'] ?></td>
                                    <td>
                                        <?= $discrepancyHandoverItem['goods_name'] ?><br>
                                        <small class="text-muted"><?= $discrepancyHandoverItem['no_goods'] ?></small>
                                    </td>
                                    <td><?= $discrepancyHandoverItem['stock_exist'] ? 'YES' : 'NO' ?></td>
                                    <td><?= numerical($discrepancyHandoverItem['quantity_booking'], 2, true) ?></td>
                                    <td><?= numerical($discrepancyHandoverItem['quantity_stock'], 2, true) ?></td>
                                    <td><?= numerical($discrepancyHandoverItem['quantity_difference'], 2, true) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($discrepancyHandoverGoods)): ?>
                                <tr>
                                    <td colspan="8">No discrepancy goods available</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="box-footer clearfix">
                <p class="pull-left"><i class="fa fa-info-circle"></i> If you want to confirm current discrepancy please confirm from you email.</p>
                <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Submit Explanation</button>
            </div>
        </div>
    </form>
    <!-- /.form-box -->
</div>
<!-- /.register-box -->

<!-- jQuery 3.1.1 -->
<script src="<?= base_url() ?>assets/plugins/jQuery/jquery-3.1.1.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?= base_url() ?>assets/plugins/iCheck/icheck.min.js"></script>
<script src="<?= base_url() ?>assets/app/js/app.js?v=5"></script>
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
