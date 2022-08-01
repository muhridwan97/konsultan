<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New User</h3>
    </div>
    <form action="<?= site_url('user/save') ?>" role="form" class="need-validation" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('name') == '' ?: 'has-error'; ?>">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                       required maxlength="50" value="<?= set_value('name') ?>">
                <?= form_error('name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('username') == '' ?: 'has-error'; ?>">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username"
                               required maxlength="50" value="<?= set_value('username') ?>">
                        <?= form_error('username', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('email') == '' ?: 'has-error'; ?>">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                               required maxlength="50" value="<?= set_value('email') ?>">
                        <?= form_error('email', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('user_type') == '' ?: 'has-error'; ?>">
                        <label for="user_type_internal" class="control-label">User Type</label>
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="radio">
                                    <input type="radio" class="form-control" id="user_type_internal" name="user_type" value="INTERNAL"
                                        <?= set_radio('user_type', 'INTERNAL') ?>> <span style="margin-top: 2px">INTERNAL</span>
                                </label>
                            </div>
                            <div class="col-sm-3">
                                <label class="radio">
                                    <input type="radio" class="form-control" id="user_type_external" name="user_type" value="EXTERNAL"
                                        <?= set_radio('user_type', 'EXTERNAL', true) ?>> <span>EXTERNAL</span>
                                </label>
                            </div>
                        </div>
                        <?= form_error('user_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('status') == '' ?: 'has-error'; ?>">
                        <label for="status">Status</label>
                        <select class="form-control select2" name="status" id="status" data-placeholder="Select status" required>
                            <option value=""></option>
                            <?php foreach ($statuses as $key => $value): ?>
                                <option value="<?= $key ?>" <?= set_select('status', $key) ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('status', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('password') == '' ?: 'has-error'; ?>">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Password" required minlength="5" maxlength="50">
                        <?= form_error('password', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('confirm_password') == '' ?: 'has-error'; ?>">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               placeholder="Confirm password" required minlength="5" maxlength="50">
                        <?= form_error('confirm_password', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('roles[]') == '' ?: 'has-error'; ?>">
                <label for="roles">Roles</label>
                <?= form_error('roles[]', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="box-group" id="accordion">
                <?php foreach ($branches as $branch): ?>
                    <div class="panel box" style="box-shadow: none">
                        <div class="box-header">
                            <h5 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#branch<?= $branch['id'] ?>" aria-expanded="true">
                                    <?= $branch['branch'] ?>
                                </a>
                            </h5>
                        </div>
                        <div id="branch<?= $branch['id'] ?>" class="panel-collapse collapse in" aria-expanded="true">
                            <div class="box-body">
                                <div class="row">
                                    <?php foreach ($roles as $role): ?>
                                        <div class="col-sm-3">
                                            <div class="checkbox icheck" style="margin-top: 0">
                                                <label>
                                                    <input type="checkbox" name="roles[<?= $branch['id'] ?>][]" id="role_<?= $branch['id'] ?>_<?= $role['id'] ?>" value="<?= $role['id'] ?>"
                                                        <?php echo set_checkbox('roles', $role['id']); ?>>
                                                    &nbsp; <?= $role['role'] ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save User</button>
        </div>
    </form>
</div>