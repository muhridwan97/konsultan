<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PLB Warehouse System | Registration Page</title>
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
        <a href="<?= site_url('/') ?>"><?= $this->config->item('app_name') ?></a>
    </div>

    <?php $this->load->view('template/_alert') ?>
    <form action="<?= site_url('response/revise/'.$tokenEmail.'?id='.get_url_param('id')) ?>" method="post" enctype="multipart/form-data">
        <div class="box-body" style="background-color: white;">
            <p class="lead text-center">Revise this Document <?= $document['document_type']; ?> <?= substr($upload['description'],-3); ?></p>
            <input type="hidden" class="form-control" name="id_upload" value="<?= $document['id_upload'] ?>">
            <input type="hidden" class="form-control" name="document_type" value="<?= $document['document_type'] ?>">
            <div class="box-body">
                <table class="table table-condensed responsive no-datatable">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Document</th>
                        <th>Revise</th>
                        <th>Attachment</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $no = 0; ?>
                        <?php if(!empty($files)): ?>
                            <?php foreach($allFiles as $index => $file): ?>
                                <?php if((!$file['description2']) && (!$file['description_date'])): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <a href="<?= asset_url($file['directory'] . '/' . $file['source']) ?>" target="_blank">
                                            <?= basename($file['source']) ?>
                                        </a>
                                    </td>
                                    <td><textarea class="form-control" name="revise[]" placeholder="Describe what needs to be change" required></textarea></td>
                                    <td> 
                                        <input type="file" name="attachment[]" id="attachment" accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar">
                                    </td>
                                </tr>
                                <input type="hidden" class="form-control" name="id_upload_document_files[]" value="<?= $file['id'] ?>">
                                <input type="hidden" class="form-control" name="id_upload_document[]" value="<?= $file['id_upload_document'] ?>">
                                <?php else: ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <a href="<?= asset_url($file['directory'] . '/' . $file['source']) ?>" target="_blank">
                                                <?= basename($file['source']) ?>
                                            </a>
                                        </td>
                                        <td><?= $file['description2'] ?><br><?= format_date($file['description_date'], 'd F Y H:i') ?></td>
                                        <td>
                                            <?php if(empty($file['description_attachment'])): ?>
                                                -
                                            <?php else: ?>
                                                <a href="<?= asset_url($file['description_attachment']) ?>" target="_blank">
                                                    <?= basename($file['description_attachment']) ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4"><?= "No data available or already revised by other users" ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if(!empty($files)): ?>
                <div class="box-footer clearfix">
                    <p class="pull-left"><i class="fa fa-info-circle"></i> If you want to confirm current document please confirm from you email.</p>
                    <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Revision</button>
                </div>
                <?php endif; ?>
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
