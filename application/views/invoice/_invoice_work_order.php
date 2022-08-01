<?php //$this->load->view('workorder/_data_detail'); ?>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Job Invoice</h3>
    </div>
    <div class="box-body">
        <?php if (!empty($validationMessages)): ?>
            <div class="alert alert-danger" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p><strong>Invoice Alert!</strong></p>
                <ul style="padding-left: 20px">
                    <?php foreach ($validationMessages as $message): ?>
                        <li><?= $message ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <?php if (!empty($infoMessages)): ?>
                <div class="alert alert-warning" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p><strong>Invoice Info!</strong></p>
                    <ul style="padding-left: 20px">
                        <?php foreach ($infoMessages as $infoMessage): ?>
                            <li><?= $infoMessage ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php $this->load->view('invoice/_invoice_editor') ?>
        <?php endif; ?>
    </div>
</div>