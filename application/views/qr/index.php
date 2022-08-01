<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">QR Scan Code</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <form action="<?= site_url('scan-qr') ?>" method="get">
            <div class="form-group">
                <label for="input-code">QR Code Scan Input</label>
                <div class="input-group">
                    <input type="text" class="form-control input-lg" name="code" id="input-code"
                           placeholder="Scan or put code number" required autofocus
                           value="<?= get_url_param('code') ?>" readonly>
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-lg" type="button" id="btn-scanner" data-target-scanner="#input-code">
                            <span class="hidden-xs">SCAN </span><i class="fa fa-qrcode"></i>
                        </button>
                    </span>
                </div>
                <span class="help-block">Scan printable qr such as TEP code, Pallet Number or Safe Conduct</span>
            </div>
        </form>
    </div>
</div>

<?php $this->load->view('qr/_modal_scanner') ?>

<script src="<?= base_url('assets/plugins/jsQR/jsQR.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/qr-scanner.js') ?>" defer></script>