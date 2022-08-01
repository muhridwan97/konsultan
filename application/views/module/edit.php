<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Module</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('module/update/'.$module['id']) ?>" role="form" method="post">

        <input type="hidden" value="<?= $module['id'] ?>" id="id" name="id">

        <div class="box-body">

            <?php if ($this->session->flashdata('status') != NULL): ?>
                <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p><?= $this->session->flashdata('message'); ?></p>
                </div>
            <?php endif ?>

            <div class="form-group <?= form_error('module_name') == '' ?: 'has-error'; ?>">
                <label for="module_name">Module Name</label>
                <input type="text" class="form-control" id="module_name" name="module_name"
                       placeholder="Enter module name"
                       required maxlength="100" value="<?= set_value('module_name', $module['module_name']) ?>">
                <?= form_error('module_name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('module_description') == '' ?: 'has-error'; ?>">
                <label for="module_description">Module Description</label>
                <textarea class="form-control" id="module_description" name="module_description"
                          placeholder="Module description"
                          required maxlength="500"
                          rows="2"><?= set_value('module_description', $module['module_description']) ?></textarea>
                <?= form_error('module_description', '<span class="help-block">', '</span>'); ?>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                        <label for="type">Module Type</label>
                        <select class="form-control select2" id="type" name="type" required
                                data-placeholder="Select module type">
                            <option value=""></option>
                            <option value="INBOUND" <?= set_select('type', 'INBOUND', $module['type'] == 'INBOUND'); ?>>
                                INBOUND
                            </option>
                            <option value="OUTBOUND" <?= set_select('type', 'OUTBOUND', $module['type'] == 'OUTBOUND'); ?>>
                                OUTBOUND
                            </option>
                        </select>
                        <?= form_error('type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('database') == '' ?: 'has-error'; ?>">
                        <label for="database">Database Name</label>
                        <input type="text" class="form-control" id="database" name="database"
                               placeholder="Enter database name"
                               required maxlength="50" value="<?= set_value('database', $module['database']) ?>">
                        <?= form_error('database', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('hostname') == '' ?: 'has-error'; ?>">
                        <label for="hostname">Hostname</label>
                        <input type="text" class="form-control" id="hostname" name="hostname"
                               placeholder="Put hostname or server address"
                               required maxlength="50" value="<?= set_value('hostname', $module['hostname']) ?>">
                        <?= form_error('hostname', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('port') == '' ?: 'has-error'; ?>">
                        <label for="port">Port</label>
                        <input type="text" class="form-control" id="port" name="port" placeholder="Enter module name"
                               maxlength="50" value="<?= set_value('port', $module['port']) ?>">
                        <?= form_error('port', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('username') == '' ?: 'has-error'; ?>">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Database username"
                               required maxlength="50" value="<?= set_value('username', $module['username']) ?>">
                        <?= form_error('username', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('password') == '' ?: 'has-error'; ?>">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Database password"
                               required maxlength="50" value="<?= set_value('password', $module['password']) ?>">
                        <?= form_error('password', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Table and master fields</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('table_header') == '' ?: 'has-error'; ?>">
                                <label for="table_header">Table Header</label>
                                <input type="text" class="form-control" id="table_header" name="table_header"
                                       placeholder="Header table name"
                                       required maxlength="50" value="<?= set_value('table_header', $module['table_header']) ?>">
                                <?= form_error('table_header', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('table_header_id') == '' ?: 'has-error'; ?>">
                                <label for="table_header_id">Header Field ID</label>
                                <input type="text" class="form-control" id="table_header_id" name="table_header_id"
                                       placeholder="Header field title ID"
                                       required maxlength="50" value="<?= set_value('table_header_id', $module['table_header_id']) ?>">
                                <?= form_error('table_header_id', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('table_header_title') == '' ?: 'has-error'; ?>">
                                <label for="table_header_title">Header Title Field</label>
                                <input type="text" class="form-control" id="table_header_title" name="table_header_title"
                                       placeholder="Header title name field"
                                       required maxlength="50" value="<?= set_value('table_header_title', $module['table_header_title']) ?>">
                                <?= form_error('table_header_title', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('table_header_subtitle') == '' ?: 'has-error'; ?>">
                                <label for="table_header_subtitle">Header Subtitle Field</label>
                                <input type="text" class="form-control" id="table_header_subtitle" name="table_header_subtitle"
                                       placeholder="Header subtitle name field"
                                       required maxlength="50" value="<?= set_value('table_header_subtitle', $module['table_header_subtitle']) ?>">
                                <?= form_error('table_header_subtitle', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('table_container') == '' ?: 'has-error'; ?>">
                                <label for="table_container">Table Container</label>
                                <input type="text" class="form-control" id="table_container" name="table_container"
                                       placeholder="Child container table name"
                                       required maxlength="50" value="<?= set_value('table_container', $module['table_container']) ?>">
                                <?= form_error('table_container', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('table_container_ref') == '' ?: 'has-error'; ?>">
                                <label for="table_container_ref">Container Header Ref</label>
                                <input type="text" class="form-control" id="table_container_ref" name="table_container_ref"
                                       placeholder="Header container field reference"
                                       required maxlength="50" value="<?= set_value('table_container_ref', $module['table_container_ref']) ?>">
                                <?= form_error('table_container_ref', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('table_goods') == '' ?: 'has-error'; ?>">
                                <label for="table_goods">Table Goods</label>
                                <input type="text" class="form-control" id="table_goods" name="table_goods"
                                       placeholder="Child goods table name"
                                       required maxlength="50" value="<?= set_value('table_goods', $module['table_goods']) ?>">
                                <?= form_error('table_goods', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group <?= form_error('table_container_ref') == '' ?: 'has-error'; ?>">
                                <label for="table_goods_ref">Goods Header Ref</label>
                                <input type="text" class="form-control" id="table_goods_ref" name="table_goods_ref"
                                       placeholder="Header goods field reference"
                                       required maxlength="50" value="<?= set_value('table_goods_ref', $module['table_goods_ref']) ?>">
                                <?= form_error('table_goods_ref', '<span class="help-block">', '</span>'); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="<?= site_url('module') ?>" class="btn btn-primary">Back to Module List</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-warning pull-right">Update Module</button>
        </div>
    </form>
</div>
<!-- /.box -->