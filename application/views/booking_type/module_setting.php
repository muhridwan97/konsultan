<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Set Import Booking Type Rules</h3>
    </div>
    <form action="<?= site_url('booking_type/save_module_import_setting/' . $bookingType['id']) ?>"
          id="form-synchronize" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <input type="hidden" id="id_booking_type" name="id_booking_type" value="<?= $bookingType['id'] ?>">

            <div class="form-group <?= form_error('module') == '' ?: 'has-error'; ?>">
                <label for="module">Booking Type</label>
                <p class="form-control-static"><?= $bookingType['booking_type'] ?> (<?= $bookingType['category'] ?>)</p>
            </div>

            <div class="form-group <?= form_error('module') == '' ?: 'has-error'; ?>">
                <label for="module">Module</label>
                <select class="form-control select2" name="module" id="module" data-placeholder="Select module"
                        required>
                    <option value=""></option>
                    <?php $moduleId = empty($bookingTypeModules) ? 0 : $bookingTypeModules[0]['id_module']; ?>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?= $module['id'] ?>" <?= set_select('module', $module['id'], $moduleId == $module['id']) ?>>
                            <?= $module['module_name'] ?> (<?= $module['type'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('module', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">Select available module import</span>
            </div>

            <div id="module-synchronize-wrapper" <?= empty($moduleId) ? 'style="display: none"' : '' ?>>
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Booking</h3>
                    </div>
                    <div class="box-body">
                        <table class="table no-datatable">
                            <thead>
                            <tr>
                                <th style="width: 300px">Booking Field</th>
                                <th>Module Target Field</th>
                                <th>Result</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('no_reference', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>No Reference</td>
                                <td>
                                    <input type="hidden" name="headers[no_reference][table]" value="bookings">
                                    <input type="hidden" name="headers[no_reference][field]" value="no_reference">
                                    <input type="hidden" name="headers[no_reference][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="headers[no_reference][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('reference_date', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Reference Date</td>
                                <td>
                                    <input type="hidden" name="headers[reference_date][table]" value="bookings">
                                    <input type="hidden" name="headers[reference_date][field]" value="reference_date">
                                    <input type="hidden" name="headers[reference_date][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="headers[reference_date][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('supplier', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Supplier</td>
                                <td>
                                    <input type="hidden" name="headers[supplier][is_reference]" value="true">
                                    <input type="hidden" name="headers[supplier][table]" value="ref_people">
                                    <input type="hidden" name="headers[supplier][field]" value="name">
                                    <input type="hidden" name="headers[supplier][option]" value="bookings">
                                    <input type="hidden" name="headers[supplier][value]" value="id_supplier">
                                    <input type="hidden" name="headers[supplier][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="headers[supplier][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('customer', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Customer</td>
                                <td>
                                    <input type="hidden" name="headers[customer][is_reference]" value="true">
                                    <input type="hidden" name="headers[customer][table]" value="ref_people">
                                    <input type="hidden" name="headers[customer][field]" value="name">
                                    <input type="hidden" name="headers[customer][option]" value="bookings">
                                    <input type="hidden" name="headers[customer][value]" value="id_supplier">
                                    <input type="hidden" name="headers[customer][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="headers[customer][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('vessel', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Vessel</td>
                                <td>
                                    <input type="hidden" name="headers[vessel][table]" value="bookings">
                                    <input type="hidden" name="headers[vessel][field]" value="vessel">
                                    <input type="hidden" name="headers[vessel][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="headers[vessel][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('voyage', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Voyage</td>
                                <td>
                                    <input type="hidden" name="headers[voyage][table]" value="bookings">
                                    <input type="hidden" name="headers[voyage][field]" value="voyage">
                                    <input type="hidden" name="headers[voyage][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="headers[voyage][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('booking_description', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Description</td>
                                <td>
                                    <input type="hidden" name="headers[booking_description][table]" value="bookings">
                                    <input type="hidden" name="headers[booking_description][field]" value="description">
                                    <input type="hidden" name="headers[booking_description][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="headers[booking_description][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Booking Extension</h3>
                    </div>
                    <div class="box-body">
                        <table class="table no-datatable">
                            <thead>
                            <tr>
                                <th style="width: 300px">Booking Extension Field</th>
                                <th>Module Target Field</th>
                                <th>Result</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($extensionFields as $extensionField): ?>
                                <tr>
                                    <?php
                                    $table = '';
                                    $field = '';
                                    if (!empty($bookingTypeModules)) {
                                        $key = array_search($extensionField['field_name'], array_column($bookingTypeModules, 'category'));
                                        if($key !== false){
                                            $table = $bookingTypeModules[$key]['target_table'];
                                            $field = $bookingTypeModules[$key]['target_field'];
                                        }
                                    }
                                    ?>
                                    <td><?= $extensionField['field_title'] ?></td>
                                    <td>
                                        <input type="hidden"
                                               name="extensions[<?= $extensionField['field_name'] ?>][table]"
                                               value="booking_extensions">
                                        <input type="hidden"
                                               name="extensions[<?= $extensionField['field_name'] ?>][field]"
                                               value="value">
                                        <input type="hidden"
                                               name="extensions[<?= $extensionField['field_name'] ?>][table_target]"
                                               value="<?= $table ?>">
                                        <input type="hidden"
                                               name="extensions[<?= $extensionField['field_name'] ?>][field_target]"
                                               value="<?= $field ?>">
                                        <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                    </td>
                                    <td class="target-result">
                                        <?php
                                        if (!empty($table) && !empty($field)) {
                                            echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($extensionFields)): ?>
                            <tr>
                                <td colspan="3">No extension field</td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Container</h3>
                    </div>
                    <div class="box-body">
                        <table class="table no-datatable">
                            <thead>
                            <tr>
                                <th style="width: 300px">Container Field</th>
                                <th>Module Target Field</th>
                                <th>Result</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('no_container', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>No Container (References)</td>
                                <td>
                                    <input type="hidden" name="containers[no_container][is_reference]" value="true">
                                    <input type="hidden" name="containers[no_container][table]" value="ref_containers">
                                    <input type="hidden" name="containers[no_container][field]" value="no_container">
                                    <input type="hidden" name="containers[no_container][option]" value="booking_containers">
                                    <input type="hidden" name="containers[no_container][value]" value="id_container">
                                    <input type="hidden" name="containers[no_container][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="containers[no_container][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('type', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Type</td>
                                <td>
                                    <input type="hidden" name="containers[type][table]" value="ref_containers">
                                    <input type="hidden" name="containers[type][field]" value="type">
                                    <input type="hidden" name="containers[type][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="containers[type][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('size', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Size</td>
                                <td>
                                    <input type="hidden" name="containers[size][table]" value="ref_containers">
                                    <input type="hidden" name="containers[size][field]" value="size">
                                    <input type="hidden" name="containers[size][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="containers[size][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('container_description', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Description</td>
                                <td>
                                    <input type="hidden" name="containers[container_description][table]" value="booking_containers">
                                    <input type="hidden" name="containers[container_description][field]" value="description">
                                    <input type="hidden" name="containers[container_description][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="containers[container_description][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Goods</h3>
                    </div>
                    <div class="box-body">
                        <table class="table no-datatable">
                            <thead>
                            <tr>
                                <th style="width: 300px">Goods Field</th>
                                <th>Module Target Field</th>
                                <th>Result</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('no_goods', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>No Goods (References)</td>
                                <td>
                                    <input type="hidden" name="goods[no_goods][is_reference]" value="true">
                                    <input type="hidden" name="goods[no_goods][table]" value="ref_goods">
                                    <input type="hidden" name="goods[no_goods][field]" value="no_goods">
                                    <input type="hidden" name="goods[no_goods][option]" value="booking_goods">
                                    <input type="hidden" name="goods[no_goods][value]" value="id_goods">
                                    <input type="hidden" name="goods[no_goods][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="goods[no_goods][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('no_hs', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>No HS</td>
                                <td>
                                    <input type="hidden" name="goods[no_hs][table]" value="ref_goods">
                                    <input type="hidden" name="goods[no_hs][field]" value="no_hs">
                                    <input type="hidden" name="goods[no_hs][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="goods[no_hs][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('name', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Goods Desc</td>
                                <td>
                                    <input type="hidden" name="goods[name][table]" value="ref_goods">
                                    <input type="hidden" name="goods[name][field]" value="name">
                                    <input type="hidden" name="goods[name][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="goods[name][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('unit', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Unit</td>
                                <td>
                                    <input type="hidden" name="goods[unit][is_reference]" value="true">
                                    <input type="hidden" name="goods[unit][table]" value="ref_units">
                                    <input type="hidden" name="goods[unit][field]" value="unit">
                                    <input type="hidden" name="goods[unit][option]" value="booking_goods">
                                    <input type="hidden" name="goods[unit][value]" value="id_unit">
                                    <input type="hidden" name="goods[unit][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="goods[unit][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('quantity', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Quantity</td>
                                <td>
                                    <input type="hidden" name="goods[quantity][table]" value="booking_goods">
                                    <input type="hidden" name="goods[quantity][field]" value="quantity">
                                    <input type="hidden" name="goods[quantity][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="goods[quantity][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('volume', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Volume</td>
                                <td>
                                    <input type="hidden" name="goods[volume][table]" value="booking_goods">
                                    <input type="hidden" name="goods[volume][field]" value="volume">
                                    <input type="hidden" name="goods[volume][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="goods[volume][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('tonnage', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Tonnage (Kg)</td>
                                <td>
                                    <input type="hidden" name="goods[tonnage][table]" value="booking_goods">
                                    <input type="hidden" name="goods[tonnage][field]" value="tonnage">
                                    <input type="hidden" name="goods[tonnage][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="goods[tonnage][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $table = '';
                                $field = '';
                                if (!empty($bookingTypeModules)) {
                                    $key = array_search('goods_description', array_column($bookingTypeModules, 'category'));
                                    if($key !== false){
                                        $table = $bookingTypeModules[$key]['target_table'];
                                        $field = $bookingTypeModules[$key]['target_field'];
                                    }
                                }
                                ?>
                                <td>Description</td>
                                <td>
                                    <input type="hidden" name="goods[goods_description][table]" value="booking_goods">
                                    <input type="hidden" name="goods[goods_description][field]" value="description">
                                    <input type="hidden" name="goods[goods_description][table_target]" value="<?= $table ?>">
                                    <input type="hidden" name="goods[goods_description][field_target]" value="<?= $field ?>">
                                    <button class="btn btn-sm btn-primary btn-select-target">Select Target</button>
                                </td>
                                <td class="target-result">
                                    <?php
                                    if (!empty($table) && !empty($field)) {
                                        echo "Table <strong>{$table}</strong> field <strong>{$field}</strong>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-primary pull-right">Set Module Import Fields</button>
        </div>

    </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-browse-module">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Select Module Field</h4>
            </div>
            <div class="modal-body">
                <p class="lead" style="margin-bottom: 0">
                    Select table and field of table <strong id="module-name"></strong>.
                </p>
                <div id="module-content-wrapper" style="max-height: 400px; overflow-y: auto">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-left btn-table-list" style="display: none">Table
                    List
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning btn-set-blank-field">Set Blank Field</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?= base_url('assets/app/js/booking_type.js') ?>" defer></script>