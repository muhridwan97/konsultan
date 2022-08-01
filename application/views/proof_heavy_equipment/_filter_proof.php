<form role="form" method="get" class="form-filter" id="filter_proof" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_proof" value="1">
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
                                <option value="<?= $heavyEquipmentName['id'] ?>" <?= get_url_param('heavy_equipment') == $heavyEquipmentName['id'] ? 'selected' : '' ?>> <?= $heavyEquipmentName['no_requisition']." - ".substr($heavyEquipmentName['request_title'], 0, 20) ?></option>
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
                <div class="col-md-12">
                    <label for="date">Date</label>
                    <input type="text" class="form-control datepicker" id="date" name="date"
                            placeholder="Date" autocomplete="off" required
                            maxlength="50" value="<?= set_value('date', get_url_param('date')) ?>">
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-6 text-left">
                    <!-- <button type="button" class="btn btn-success" id="proof-print">Print</button> -->
                    <button type="button" class="btn btn-success" id="edit-print">Print</button>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="reset" class="btn btn-default btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">View History</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?= base_url('assets/app/js/proof_heavy_equipment.js') ?>" defer></script>
