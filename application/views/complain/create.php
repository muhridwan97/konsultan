<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Complain</h3>
    </div>
    <form action="<?= site_url('complain/save') ?>" class="form-complain" method="post" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                <label for="customer">Customer</label>
                <?php if($people_login['type'] == PeopleModel::$TYPE_EMPLOYEE && UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                    <select class="form-control select2 select2-ajax"
                            data-url="<?= site_url('people/ajax_get_people') ?>"
                            data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                            name="customer" id="customer"
                            data-placeholder="Select customer" required style="width: 100%">
                        <option value=""></option>
                        <?php foreach ($owners as $owner): ?>
                            <option value="<?= $owner['id'] ?>" <?= set_value('customer') == $owner['id'] ? 'selected' : '' ?>>
                                <?= $owner['name'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                <?php else: ?>
                    <p class="form-control-static"><?= UserModel::authenticatedUserData('name') ?></p>
                    <input type="hidden" name="customer" id="customer" value="<?= $people_login['id'] ?>">
                <?php endif ?>
                <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
            </div>

            <?php if($people_login['type'] == PeopleModel::$TYPE_EMPLOYEE && UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
            <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                <label for="category">Complain Category</label>
                <select class="form-control select2 category" name="category" id="category"
                        data-placeholder="Select Complain Category" required style="width: 100%">
                    <option value=""></option>
                    <?php foreach($categories AS $category): ?>
                        <option value="<?= $category['id'] ?>" <?= set_value('category') == $category['id'] ? 'selected' : '' ?>> <?= $category['category'] ?> (<?= $category['value_type'] ?>)</option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('category', '<span class="help-block">', '</span>'); ?>
            </div>
            <?php endif; ?>

            <?php if($people_login['type'] == PeopleModel::$TYPE_EMPLOYEE && UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
             <div class="form-group <?= form_error('department') == '' ?: 'has-error'; ?>">
                <label for="department">Department</label>
                <select class="form-control select2 department" name="department" id="department"
                        data-placeholder="Select Department" required style="width: 100%">
                    <option value=""></option>
                    <?php foreach($departments AS $department): ?>
                        <option value="<?= $department['department'] ?>" <?= set_value('department') == $department['department'] ? 'selected' : '' ?>> <?= $department['department'] ?></option>
                    <?php endforeach; ?>
                </select>
                 <p class="help-block">Users that have permission <strong>complain-investigation-create</strong> in selected branch and department will receive the email</p>
                <?= form_error('department', '<span class="help-block">', '</span>'); ?>
            </div>
            <?php endif; ?>

            <?php if($people_login['type'] == PeopleModel::$TYPE_EMPLOYEE && UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
            <!--<div class="form-group email-pic">
                <label for="email-pic">Email PIC</label>
                <input type="text" class="form-control" readonly id="email-pic" name="email_pic" value="<?= set_value('email_pic') ?>"> 
                <p class="text-muted">Setting Email PIC in HR Apps.</p>
            </div>-->
            <?php endif; ?>

            <div class="form-group <?= form_error('via') == '' ?: 'has-error'; ?>">
                <label for="via">Via</label>
                <select class="form-control select2" name="via" id="via"
                        data-placeholder="Select Via" required style="width: 100%">
                    <option value=""></option>
                    <?php foreach($via AS $key => $by): ?>
                        <?php if($people_login['type'] == PeopleModel::$TYPE_EMPLOYEE && UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                            <?php if($by !== "SYSTEM"): ?>
                                <option value="<?= $by ?>" <?= set_value('via') == $by ? 'selected' : '' ?>> <?= $by ?></option>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if($people_login['type'] == PeopleModel::$TYPE_CUSTOMER && UserModel::authenticatedUserData('user_type') == 'EXTERNAL'):?>
                                <?php if($by === "SYSTEM"): ?>
                                    <option value="<?= $by ?>" selected> <?= $by ?></option>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <?= form_error('via', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                <label for="branch">Branch</label>
                <select class="form-control select2 branch" name="branch" id="branch"
                        data-placeholder="Select branch" required style="width: 100%">
                    <option value=""></option>
                    <?php foreach($branches AS $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= set_value('branch') == $branch['id'] ? 'selected' : '' ?>> <?= $branch['branch'] ?></option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('complain') == '' ?: 'has-error'; ?> complain-class">
                <label for="complain">Complain Details</label>
                 <textarea class="form-control" id="complain" required name="complain" placeholder="Enter Complain" maxlength="500"><?= set_value('complain') ?></textarea>
                <?= form_error('complain', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('complain_attachment') == '' ?: 'has-error'; ?>">
                <label for="complain_attachment">Complain Attachment</label>
                <input type="file" name="complain_attachment"
                       accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                       placeholder="Select complain document">
                <?= form_error('complain_attachment', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/complain.js?v=2') ?>" defer></script>