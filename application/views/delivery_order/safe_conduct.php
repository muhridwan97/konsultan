<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">DO Safe Conduct <?= $deliveryOrder['no_delivery_order'] ?></h3>
    </div>
    <!-- /.box-header -->

    <div class="box-body">

        <?php $this->load->view('delivery_order/_view_header') ?>

        <?php $this->load->view('delivery_order/_view_safe_conduct') ?>

    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary pull-left">
            Back to Delivery Order List
        </a>
        <a href="<?= site_url('safe-conduct/create?category=INBOUND&booking_id=' . $deliveryOrder['id_booking']) ?>" class="btn btn-success pull-right">
            Create Safe Conduct
        </a>
    </div>
</div>
<!-- /.box -->