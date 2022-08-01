<?php if ($this->session->flashdata('status_check') != NULL): ?>
    <div class="alert alert-<?= $this->session->flashdata('status_check') ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" name="ggf" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p><?= $this->session->flashdata('message_check'); ?></p>
    </div>
<?php endif; ?>
<form action="<?= site_url('security/check') ?>" method="get" id="form-security-scanner">
    <div class="form-group">
        <label for="code">Barcode Scan Input</label>
        <div class="input-group">
            <input type="text" class="form-control input-lg" name="code" id="code"
                   placeholder="Scan or put code number" required autofocus
                   value="<?= isset($_GET['code']) ? $_GET['code'] : '' ?>" <?= empty(get_url_param('code')) ? ' readonly' : '' ?>>
            <span class="input-group-btn">
                <button class="btn btn-outline-secondary btn-lg" id="btn-edit-number" type="button">
                    <i class="fa <?= empty(trim(get_url_param('code'))) ? 'fa-edit' : 'fa-lock' ?>"></i>
                </button>
                <button class="btn btn-primary btn-lg" type="button" id="btn-scan-code"><span class="hidden-xs">SCAN </span><i class="fa fa-qrcode"></i></button>
            </span>
        </div>
        <span class="help-block">Scan or input manually code job above</span>
    </div>
    <div id="modal-scanner" style="display: none">
        <button type="button" class="close">
            <span aria-hidden="true">&times;</span>
        </button>
        <div id="camera-message">
            <div class="align-items-center justify-content-center border rounded" id="message-wrapper">
                <div class="text-center">
                    <h1 class="fa fa-eye-slash"></h1>
                    <p class="small">No cameras or insufficient permission.</p>
                    <button type="button" class="btn btn-sm" id="btn-try-again">Refresh the Page</button>
                </div>
            </div>
        </div>
        <canvas id="camera-preview" class="rounded" style="display:none; width: 100%; height: 100%; "></canvas>
    </div>
</form>

<script src="<?= base_url('assets/plugins/jsQR/jsQR.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/security.js?v=8') ?>" defer></script>
<script src="<?= base_url('assets/app/js/security-scanner.js?v=8') ?>" defer></script>
<link rel="stylesheet" href="<?= base_url('assets/app/css/security.css') ?>" defer>