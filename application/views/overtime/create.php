<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Overtime</h3>
    </div>
    <form action="<?= site_url('overtime/save') ?>" role="form" method="POST">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('name_of_day') == '' ?: 'has-error'; ?>">
                <label for="name_of_day">Name Of Day</label>
                 <select class="form-control select2" required name="name_of_day" id="name_of_day" data-placeholder="Select Day" style="width: 100%">
                    <option value=""></option>
                    <option value="SUNDAY">SUNDAY</option>
                    <option value="MONDAY">MONDAY</option>
                    <option value="TUESDAY">TUESDAY</option>
                    <option value="WEDNESDAY">WEDNESDAY</option>
                    <option value="THURSDAY">THURSDAY</option>
                    <option value="FRIDAY">FRIDAY</option>
                    <option value="SATURDAY">SATURDAY</option>
                </select>
                <?= form_error('name_of_day', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('first_overtime') == '' ?: 'has-error'; ?>">
                        <label for="first_overtime">First Overtime</label>
                        <input type="time" class="form-control" id="first_overtime" name="first_overtime"placeholder="Enter First Overtime" required maxlength="50" value="<?= set_value('first_overtime') ?>">
                        <?= form_error('first_overtime', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('second_overtime') == '' ?: 'has-error'; ?>">
                        <label for="second_overtime">Second Overtime</label>
                         <input type="time" class="form-control" id="second_overtime" name="second_overtime"placeholder="Enter Second Overtime" required maxlength="50" value="<?= set_value('first_overtime') ?>">
                        <?= form_error('second_overtime', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Overtime Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Overtime description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="box-footer clearfix">
                <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
                <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Overtime</button>
            </div>
        </div>
    </form>
</div>