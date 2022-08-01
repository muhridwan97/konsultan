<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Gate Check Point</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('gate/_scanner') ?>
    </div>
</div>

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