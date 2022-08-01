<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Customer</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="row">
                <!-- photo -->
                <div class="col-lg-2 col-xs-12">
                    <img class="img-responsive"
                         src="<?= base_url('assets/app/img/avatar/' . if_empty($customer['photo'], 'no-avatar.jpg')) ?>"
                         alt="User Avatar">
                </div>
                <!-- end of photo -->
                <!-- details -->
                <div class="col-lg-10">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-4">Identity Number</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $customer['identity_number'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $customer['name'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Gender</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $customer['gender'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Birthday</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= readable_date($customer['birthday'], false) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Address</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($customer['address'], 'No address') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-4">Contact</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($customer['contact'], 'No contact') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Email</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($customer['email'], 'No email') ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Tax Number</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($customer['tax_number'], 'No tax number') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Created At</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= readable_date($customer['created_at']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Updated At</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= readable_date($customer['updated_at']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </form>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>