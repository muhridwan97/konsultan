<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Notification</h3>
    </div>
    <form action="<?= site_url('people/update_notification/' . $person['id']) ?>" class="form" method="post" id="form-edit-notification">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group">
                <label>Name</label>
                <div>
                    <p class="form-control-static"><?= $person['name'] ?></p>
                </div>
            </div>
            <div class="form-group <?= form_error('whatsapp_group') == '' ?: 'has-error'; ?>">
                <label for="whatsapp_group">Whatsapp Group</label>
                <input type="text" class="form-control" id="whatsapp_group" name="whatsapp_group" placeholder="whatsapp group"
                       value="<?= set_value('whatsapp_group', $person['whatsapp_group']) ?>">
                <?= form_error('whatsapp_group', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('compliance') == '' ?: 'has-error'; ?>">
                <label for="compliance">Compliance</label>
                <select class="form-control select2" multiple name="compliances[]" id="compliance" data-placeholder ="select whatsapp">
                    <?php foreach ($participants as $participant): ?>
                        <option value="<?= $participant['id'] ?>" <?= set_select('compliances[]', $participant['id'], array_search($participant['id'], array_column($complianceMentions, 'whatsapp')) !== false) ?>>
                            <?= invert_chat_id($participant['id']) ?> (<?= $participant['name'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('compliance', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('operational') == '' ?: 'has-error'; ?>">
                <label for="operational">Operational</label>
                <select class="form-control select2" multiple name="operationals[]" id="operational" data-placeholder ="select whatsapp">
                    <?php foreach ($participants as $participant): ?>
                        <option value="<?= $participant['id'] ?>" <?= set_select('operationals[]', $participant['id'], array_search($participant['id'], array_column($operationalMentions, 'whatsapp')) !== false) ?>>
                            <?= invert_chat_id($participant['id']) ?> (<?= $participant['name'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('operational', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('external') == '' ?: 'has-error'; ?>">
                <label for="external">External</label>
                <select class="form-control select2" multiple name="externals[]" id="external" data-placeholder ="select whatsapp"> 
                    <?php foreach ($participants as $participant): ?>
                        <option value="<?= $participant['id'] ?>" <?= set_select('externals[]', $participant['id'], array_search($participant['id'], array_column($externalMentions, 'whatsapp')) !== false) ?>>
                            <?= invert_chat_id($participant['id']) ?> (<?= $participant['name'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('external', '<span class="help-block">', '</span>'); ?>
            </div>
            
        </div>
        <div class="box-footer">
            <a href="<?= site_url('people/view/' . $person['id']) ?>" class="btn btn-primary">Back to detail view</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Update Notification</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/people.js?v=3') ?>" defer></script>