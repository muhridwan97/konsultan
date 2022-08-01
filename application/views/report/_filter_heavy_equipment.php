<form role="form" method="get" class="form-filter" id="filter_heavy_equipment" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_heavy_equipment" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <label for="type">Type</label>
                    <select class="form-control select2" id="type" name="type" data-placeholder="Select type filter">
                        <option value="INTERNAL" <?= get_url_param('type') == 'INTERNAL' ? 'selected' : '' ?>>INTERNAL</option>
                        <option value="EXTERNAL" <?= get_url_param('type') == 'EXTERNAL' ? 'selected' : '' ?>>EXTERNAL</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="heavy_equipment">Heavy Equipement</label>
                    <select class="form-control select2" id="heavy_equipment" name="heavy_equipment" data-placeholder="Select heavy Equipement filter">
                        <?php if (get_url_param('type') == 'EXTERNAL') : ?>
                            <?php foreach ($heavyEquipmentNames as $key => $heavyEquipmentName) : ?>
                                <option value="<?= $heavyEquipmentName['id'] ?>" <?= get_url_param('heavy_equipment') == $heavyEquipmentName['id'] ? 'selected' : '' ?>> <?= $heavyEquipmentName['name']." - ".$heavyEquipmentName['no_requisition']." - ".date("d F Y",strtotime($heavyEquipmentName['created_at'])) ?></option>
                            <?php endforeach; ?>
                        <?php else :?>
                            <?php foreach ($heavyEquipmentNames as $key => $heavyEquipmentName) : ?>
                                <option value="<?= $heavyEquipmentName['id'] ?>" <?= get_url_param('heavy_equipment') == $heavyEquipmentName['id'] ? 'selected' : '' ?>> <?= $heavyEquipmentName['name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif ?>
                    </select>
                </div>
            </div>    
            <div class="form-group">
                <label for="customer">Customer</label>
                <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                    <select class="form-control select2 select2-ajax" 
                            data-url="<?= site_url('people/ajax_get_people_all_branch') ?>"
                            data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                            data-key-sublabel="no_person"
                            name="customer[]" id="customer"
                            data-placeholder="Select customer" multiple>
                        <option value=""></option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>" selected>
                                <?= $customer['name'].' - '.$customer['no_person'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <p class="form-control-static">
                        <?= UserModel::authenticatedUserData('name') ?>
                    </p>
                <?php endif; ?>
            </div>           
            <div class="row">
                <div class="col-md-6">
                    <label for="date_from">Date From</label>
                    <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                            placeholder="Date from" autocomplete="off"
                            maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                </div>
                <div class="col-md-6">
                    <label for="date_to">Date To</label>
                    <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                            placeholder="Date to" autocomplete="off"
                            maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="reset" class="btn btn-default btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?= base_url('assets/app/js/heavy_equipment.js') ?>" defer></script>
