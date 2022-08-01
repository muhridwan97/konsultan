<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Booking Invoice</h3>
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
            <?php $this->load->view('invoice/_invoice_booking_header') ?>
            <?php $this->load->view('invoice/_invoice_editor') ?>
        <?php endif; ?>
    </div>
</div>