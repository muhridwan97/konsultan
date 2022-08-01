<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?> | Warehouse</title>
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">
</head>
<body>

<div style="padding: 15px" id="viewer-header">
    <form action="<?= site_url('client_area/save_draft_invoice') ?>" method="post">
        <input type="hidden" name="outbound_date" value="<?= get_url_param('outbound_date') ?>">
        <input type="hidden" name="bl" value="<?= get_url_param('bl') ?>">
        <input type="hidden" name="no_container" value="<?= get_url_param('no_container') ?>">
        <input type="hidden" name="contact_id" value="<?= get_url_param('id_contact') ?>">
        <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
        <div class="row">
            <div class="col-xs-6 col-md-7">
                <p style="margin-top: 8px">
                    <strong>
                        Only save this invoice estimation if you intended to making payment or transfer in 1-3 days ahead.
                    </strong>
                </p>
            </div>
            <div class="col-xs-6 col-md-5 text-right">
                <a href="javascript:window.close();" class="btn btn-danger">Close</a>
                <button class="btn btn-success" type="submit">Save as Draft</button>
            </div>
        </div>
    </form>
</div>

<?php if (ENVIRONMENT == 'production'): ?>
    <embed src="https://drive.google.com/viewerng/viewer?embedded=true&url=<?= site_url('client_area/invoice_estimation_print/' . $bookingId) ?>" width="100%" height="450px" id="pdf-viewer">
<?php else: ?>
    <embed src="<?= site_url('client_area/invoice_estimation_print/' . $bookingId) ?>" width="100%" height="450px" id="pdf-viewer">
<?php endif; ?>


<script>

    window.addEventListener("load", adjustViewerHeight);
    window.addEventListener("resize", adjustViewerHeight);

    var viewer = document.getElementById('pdf-viewer');
    function adjustViewerHeight() {
        var headerHeight = document.getElementById('viewer-header').scrollHeight + 5;
        var height = window.innerHeight - headerHeight;
        viewer.setAttribute('height', height);
    }
</script>
</body>
</html>