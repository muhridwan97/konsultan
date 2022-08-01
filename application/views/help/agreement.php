<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PLB Warehouse System | Agreement</title>
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


<div class="container">
    <div class="panel panel-default mt20 mb20">
        <div class="panel-body">
            <h3>Terms and Conditions</h3>
            <p class="text-muted">Last updated: September 18, 2017</p>
            <p>Please read these Terms and Conditions ("Terms", "Terms and Conditions") carefully before using the TCI Warehouse
                website (the "Service") operated by TCI Warehouse ("us", "we", or "our").</p>

            <p>Your access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These
                Terms apply to all visitors, users and others who access or use the Service.</p>

            <p>By accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms
                then you may not access the Service. Terms & Conditions created by
                <a href="https://termsfeed.com" rel="nofollow">TermsFeed</a> for TCI Warehouse.</p>


            <h4>Accounts</h4>

            <p>When you create an account with us, you must provide us information that is accurate, complete, and current at all
                times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account
                on our Service.</p>

            <p>You are responsible for safeguarding the password that you use to access the Service and for any activities or
                actions under your password, whether your password is with our Service or a third-party service.</p>

            <p>You agree not to disclose your password to any third party. You must notify us immediately upon becoming aware of
                any breach of security or unauthorized use of your account.</p>


            <h4>Links To Other Web Sites</h4>

            <p>Our Service may contain links to third-party web sites or services that are not owned or controlled by
                TCI Warehouse.</p>

            <p>TCI Warehouse has no control over, and assumes no responsibility for, the content, privacy policies, or practices of
                any third party web sites or services. You further acknowledge and agree that TCI Warehouse shall not be responsible or
                liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use
                of or reliance on any such content, goods or services available on or through any such web sites or services.</p>

            <p>We strongly advise you to read the terms and conditions and privacy policies of any third-party web sites or
                services that you visit.</p>


            <h4>Termination</h4>

            <p>We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason
                whatsoever, including without limitation if you breach the Terms.</p>

            <p>All provisions of the Terms which by their nature should survive termination shall survive termination, including,
                without limitation, ownership provisions, warranty disclaimers, indemnity and limitations of liability.</p>

            <p>We may terminate or suspend your account immediately, without prior notice or liability, for any reason whatsoever,
                including without limitation if you breach the Terms.</p>

            <p>Upon termination, your right to use the Service will immediately cease. If you wish to terminate your account, you
                may simply discontinue using the Service.</p>

            <p>All provisions of the Terms which by their nature should survive termination shall survive termination, including,
                without limitation, ownership provisions, warranty disclaimers, indemnity and limitations of liability.</p>


            <h4>Governing Law</h4>

            <p>These Terms shall be governed and construed in accordance with the laws of Indonesia, without regard to its
                conflict of law provisions.</p>

            <p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If
                any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these
                Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and
                supersede and replace any prior agreements we might have between us regarding the Service.</p>


            <h4>Changes</h4>

            <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is
                material we will try to provide at least 30 days notice prior to any new terms taking effect. What constitutes a
                material change will be determined at our sole discretion.</p>

            <p>By continuing to access or use our Service after those revisions become effective, you agree to be bound by the
                revised terms. If you do not agree to the new terms, please stop using the Service.</p>


            <h4>Contact Us</h4>

            <p>If you have any questions about these Terms, please contact us.</p>
        </div>
    </div>
</div>

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