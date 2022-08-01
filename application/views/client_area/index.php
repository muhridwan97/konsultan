<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4 style="display: inline-block; margin-bottom: 0">Our Tips!</h4> &nbsp;
    <span>Contact our customer support if you have difficulty using this client area or want to know further about our services.</span>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-green">
            <div class="inner" style="position:relative; z-index: 1">
                <h3>TPP Cost Estimation</h3>
                <p>Check your invoice</p>
            </div>
            <div class="icon">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <a href="<?= site_url('client_area/invoice') ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-red">
            <div class="inner" style="position:relative; z-index: 1">
                <h3>Container</h3>
                <p>Track your containers</p>
            </div>
            <div class="icon">
                <i class="fa fa-truck"></i>
            </div>
            <a href="<?= site_url('client_area/container') ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<section class="content-header">
    <h1>Quick Access</h1>
    <p>Find data what you need</p>

    <div class="row mb20">
        <div class="col-md-6 mb20">
            <h4>Cost Estimation</h4>
            <?php $this->load->view('client_area/_form_invoice') ?>
        </div>
        <div class="col-md-6 mb20">
            <h4>Container Tracker</h4>
            <?php $this->load->view('client_area/_form_container') ?>
        </div>
    </div>
</section>

<?php $this->load->view('client_area/_modal_questioner') ?>