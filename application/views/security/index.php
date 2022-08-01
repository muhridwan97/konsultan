<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Security Check Point</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('security/_scanner') ?>
    </div>
</div>

<?php if ($this->session->flashdata('status') != NULL) : ?>
    <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" name="testing" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p><?= $this->session->flashdata('message'); ?></p>
    </div>
<?php endif; ?>
<div class="box box-primary">

    <div class="box-header">
        Scan History
    </div>
    <div class="box-body">
        <div class="list-group" id="code-history">
            <a href="#" class="list-group-item disabled">
                BARCODE SCAN CODE
            </a>
        </div>
    </div>
</div>