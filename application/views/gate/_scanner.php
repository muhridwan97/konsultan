<?php if ($this->session->flashdata('status_check') != NULL): ?>
    <div class="alert alert-<?= $this->session->flashdata('status_check') ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p><?= $this->session->flashdata('message_check'); ?></p>
    </div>
<?php endif; ?>

<form action="<?= site_url('gate/check') ?>" method="get">
    <div class="form-group">
        <label for="code">Barcode Scan Input</label>
        <div class="input-group">
            <input type="text" class="form-control input-lg" name="code" id="code"
                   placeholder="Scan or put code number" required autofocus
                   value="<?= isset($_GET['code']) ? $_GET['code'] : '' ?>">
            <span class="input-group-btn">
                <button class="btn btn-primary btn-lg" type="submit" id="btn-scan-code">Scan Code</button>
            </span>
        </div>
        <span class="help-block">Scan or input manually code above</span>
    </div>
</form>

<script src="<?= base_url('assets/app/js/gate.js') ?>" defer></script>