<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Overtime</h3>
    </div>
    <form action="<?= site_url('overtime/update/'.$overtime['id']) ?>" role="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('name_of_day') == '' ?: 'has-error'; ?>">
                <label for="name_of_day">Name Of Day</label>
                 <select class="form-control select2" disabled name="name_of_day" id="name_of_day"
                        data-placeholder="Select Day" style="width: 100%">
                    <option value=""></option>
                    <option value="SUNDAY" <?= set_select('name_of_day', 'SUNDAY', $overtime['name_of_day'] == 'SUNDAY') ?>>SUNDAY</option>
                    <option value="MONDAY"  <?= set_select('name_of_day', 'MONDAY', $overtime['name_of_day'] == 'MONDAY') ?>>MONDAY</option>
                    <option value="TUESDAY"  <?= set_select('name_of_day', 'TUESDAY', $overtime['name_of_day'] == 'TUESDAY') ?>>TUESDAY</option>
                    <option value="WEDNESDAY" <?= set_select('name_of_day', 'WEDNESDAY', $overtime['name_of_day'] == 'WEDNESDAY') ?>>WEDNESDAY</option>
                    <option value="THURSDAY"  <?= set_select('name_of_day', 'THURSDAY', $overtime['name_of_day'] == 'THURSDAY') ?>>THURSDAY</option>
                    <option value="FRIDAY"  <?= set_select('name_of_day', 'FRIDAY', $overtime['name_of_day'] == 'FRIDAY') ?>>FRIDAY</option>
                    <option value="SATURDAY"  <?= set_select('name_of_day', 'SATURDAY', $overtime['name_of_day'] == 'SATURDAY') ?>>SATURDAY</option>
                </select>
                <?= form_error('name_of_day', '<span class="help-block">', '</span>'); ?>
            </div>

             <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('first_overtime') == '' ?: 'has-error'; ?>">
                        <label for="first_overtime">First Overtime</label>
                        <input type="time" class="form-control" id="first_overtime" name="first_overtime"
                               placeholder="Enter First Overtime"
                               required maxlength="50" value="<?= set_value('first_overtime', $overtime['first_overtime']) ?>">
                        <input type="hidden" name="overtimeDay" value="<?= set_value('overtimeDay', $overtime['name_of_day']) ?>" >
                        <?= form_error('first_overtime', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('second_overtime') == '' ?: 'has-error'; ?>">
                        <label for="second_overtime">Second Overtime</label>
                        <input type="time" class="form-control" id="second_overtime" name="second_overtime"
                                   placeholder="Enter Second Overtime"
                                   required maxlength="50" value="<?= set_value('second_overtime', $overtime['second_overtime']) ?>">
                        <?= form_error('second_overtime', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
             <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Overtime description"
                          required maxlength="500"><?= set_value('description', $overtime['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Overtime</button>
        </div>
    </form>
</div>