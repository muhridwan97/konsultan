<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Account</h3>
    </div>
    <form action="<?= site_url('account/update') ?>" role="form" method="post">
        <div class="box-body">

            <?php if ($this->session->flashdata('status') != NULL): ?>
                <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p><?= $this->session->flashdata('message'); ?></p>
                </div>
            <?php endif ?>

            <?php if (!empty($person)): ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Related with</label>
                            <p class="form-control-static"><?= $person['name'] ?> (<?= $person['type'] ?>)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Address</label>
                            <p class="form-control-static"><?= if_empty($person['address'], '-') ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">No Person</label>
                            <p class="form-control-static"><?= $person['no_person'] ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Email</label>
                            <p class="form-control-static"><?= if_empty($person['email'], '-') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <div class="form-group <?= form_error('name') == '' ?: 'has-error'; ?>">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       placeholder="Enter user name"
                       required maxlength="50" value="<?= set_value('name', $user['name']) ?>">
                <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('username') == '' ?: 'has-error'; ?>">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       placeholder="Unique username"
                       required maxlength="50" value="<?= set_value('username', $user['username']) ?>">
                <?= form_error('username', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('email') == '' ?: 'has-error'; ?>">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       placeholder="Unique email"
                       required maxlength="50" value="<?= set_value('email', $user['email']) ?>">
                <?= form_error('email', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('password') == '' ?: 'has-error'; ?>">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Current password" required maxlength="50">
                <?= form_error('password', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('new_password') == '' ?: 'has-error'; ?>">
                <label for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password"
                       placeholder="New password" maxlength="50">
                <?= form_error('new_password', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('confirm_password') == '' ?: 'has-error'; ?>">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                       placeholder="Confirm new password" maxlength="50">
                <?= form_error('confirm_password', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Update Account</button>
        </div>
    </form>
</div>