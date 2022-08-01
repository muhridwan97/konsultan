<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View User</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $user['name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Username</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $user['username'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Email</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="mailto:<?= $user['email'] ?>"><?= $user['email'] ?></a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">User Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $user['user_type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $statusLabel = [
                                    'PENDING' => 'label-warning',
                                    'ACTIVATED' => 'label-success',
                                    'SUSPENDED' => 'label-danger',
                                ];
                                $classLabel = 'label-default';
                                if (key_exists($user['status'], $statusLabel)) {
                                    $classLabel = $statusLabel[$user['status']];
                                }
                                ?>
                                <span class="label <?= $classLabel ?>"><?= $user['status'] ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Roles</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if ($user['total_role'] == 0): ?>
                                    No role available
                                <?php else: ?>
                                    <a href="<?= site_url('user/role/' . $user['id']) ?>">
                                        <?= number_format($user['total_role'], 0, ',', '.') ?> roles
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($user['type'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Profile</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(!empty($user['person_name'])): ?>
                                    <a href="<?= site_url('people/view/' . $user['id_person']) ?>">
                                        <?= $user['person_name'] ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Profile Email</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($user['person_email'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Contact</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($user['contact'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Address</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($user['address'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Gender</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($user['gender'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Birthday</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($user['birthday'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Photo</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (empty($user['photo'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= base_url('uploads/profile/' . $user['photo']) ?>">
                                        Download
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Website</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($user['website'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($user['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($user['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
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