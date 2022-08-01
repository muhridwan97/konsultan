<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Complain Data</h3>
    </div>
    <form action="<?= site_url('complain/update/' . $complain['id']) ?>" class="form-complain" method="post" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>
            <div class="form-group">
                <label for="customer">Customer</label>
                 <p class="form-control-static"><?= $customer['name'] ?></p>
            </div>

            <?php if($people_login['type'] == PeopleModel::$TYPE_EMPLOYEE && UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
            <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                <label for="category">Complain Category</label>
                <select class="form-control select2" name="category" id="category"
                        data-placeholder="Select Complain Category" required style="width: 100%">
                    <option value=""></option>
                    <?php foreach($categories AS $category): ?>
                        <option value="<?= $category['id'] ?>" <?= set_value('category') == $category['id'] ? 'selected' : '' ?>> <?= $category['category'] ?> (<?= $category['value_type'] ?>)</option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('category', '<span class="help-block">', '</span>'); ?>
            </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('department') == '' ?: 'has-error'; ?>">
                <label for="department">Department</label>
                <select class="form-control select2 department" name="department" id="department"
                        data-placeholder="Select Complain Category" required style="width: 100%">
                    <option value=""></option>
                    <?php foreach($departments AS $department): ?>
                        <option value="<?= $department['department'] ?>" <?= set_select('department', $department['department'], $department['department'] == $complain['department']) ?>> <?= $department['department'] ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="help-block">Users that have permission <strong>complain-investigation-create</strong> in selected branch and department will receive the email</p>
                <?= form_error('department', '<span class="help-block">', '</span>'); ?>
            </div>

            <?php if($people_login['type'] == PeopleModel::$TYPE_EMPLOYEE && UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
            <!--<div class="form-group email-pic">
                <label for="email-pic">Email PIC</label>
                <input type="text" class="form-control" readonly id="email-pic" name="email_pic" value="<?= set_value('email_pic') ?>"> 
                <p class="text-muted">Setting Email PIC in HR Apps.</p>
            </div>-->
            <?php endif; ?>
            
            <div class="form-group <?= form_error('complain_attachment') == '' ?: 'has-error'; ?>">
                <label for="complain_attachment">Complain Attachment</label>
                <input type="file" name="complain_attachment"  id="complain_attachment"
                       accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                       placeholder="Select complain document">
                <?= form_error('complain_attachment', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/complain.js') ?>" defer></script>