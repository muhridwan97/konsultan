<!-- Horizontal Form -->
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Change Ownership</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('change_ownership/update/' . $changeOwnership['id']) ?>" class="form edit" method="post"
          id="form-changeOwnership">
        <div class="box-body">
            <!-- alert -->
            <?php if ($this->session->flashdata('status') != NULL): ?>
                <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <p><?= $this->session->flashdata('message'); ?></p>
                </div>
            <?php endif; ?>
            <!-- end of alert -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('currentOwner') == '' ?: 'has-error'; ?>">
                        <label for="currentOwner">Current Owner</label>
                        <select class="form-control select2CurrentOwner" name="currentOwner" id="currentOwner">
                            <option value="<?= $currentOwner['id'] ?>"><?= $currentOwner['name'] ?></option>
                        </select>
                        <?= form_error('currentOwner', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('newOwner') == '' ?: 'has-error'; ?>">
                        <label for="newOwner">New Owner</label>
                        <select class="form-control select2NewOwner" name="newOwner" id="newOwner" required>
                            <option value="<?= $newOwner['id'] ?>"><?= $newOwner['name'] ?></option>
                        </select>
                        <?= form_error('newOwner', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('booking') == '' ?: 'has-error'; ?>">
                        <label for="booking">Booking</label>
                        <select class="form-control select2" name="booking" id="booking"
                                data-placeholder="Select booking" required>
                            <?php foreach ($bookingOfCustomer as $item) : ?>
                                <option value="<?= $item['id'] ?>" ><?= $item['no_booking'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('changedate') == '' ?: 'has-error'; ?>">
                        <label for="changedate">Since</label>
                        <input type="text" class="form-control daterangepicker2" id="changedate" name="changedate"
                               placeholder="Enter change date"
                               required maxlength="50"
                               value="<?= set_value('changedate', (new DateTime($changeOwnership['change_date']))->format('d F Y H:i:s')) ?>">
                        <?= form_error('changedate', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Ownership description"
                          required
                          maxlength="500"><?= set_value('description', $changeOwnership['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>


            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Ref stock data</h3>
                </div>
                <div class="box-body" id="stock-data-wrapper">
                    <p class="text-muted">Container or goods of related booking</p>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Handling Loading</h3>
                    <p class="text-muted mb0">Set handling job from stock</p>
                </div>
                <div class="box-body">

                    <p class="lead mb0">Handling Containers</p>
                    <table class="table no-datatable mb20">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>No Container</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Seal</th>
                            <th>Description</th>
                            <th style="width: 100px">Action</th>
                        </tr>
                        </thead>
                        <tbody id="destination-container-wrapper">
                        <tr id="placeholder">
                            <td colspan="7" class="text-center">No loading any container</td>
                        </tr>
                        </tbody>
                    </table>

                    <p class="lead mb0">Handling Items</p>
                    <table class="table no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Tonnage (Kg)</th>
                            <th>Volume (M<sup>3</sup>)</th>
                            <th>Position</th>
                            <th>No Pallet</th>
                            <th>Description</th>
                            <th style="width: 100px">Action</th>
                        </tr>
                        </thead>
                        <tbody id="destination-item-wrapper">
                        <tr id="placeholder">
                            <td colspan="11" class="text-center">No loading any item</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="form-group mt20">
                        <label for="total_items">Total item is handled</label>
                        <input type="number" required readonly value="0" min="1" class="form-control" id="total_items"
                               name="total_items">
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="<?= site_url('change_ownership') ?>" class="btn btn-primary">Back to Ownership List</a>
            <button type="submit" class="btn btn-primary pull-right">Change Ownership</button>
        </div>
        <!-- /.box-footer -->
    </form>
</div>
<script>
    //var dateRangePickerSettings = {
    //    singleDatePicker: true,
    //    timePicker: true,
    //    timePicker24Hour: true,
    //    timePickerSeconds: true,
    //    <?//= !is_null($minDate) ? "minDate:'" . (new DateTime($minDate))->format('d F Y H:i:s') . "'," : "" ?>
    <!--    --><?//= !is_null($maxDate) ? "maxDate:'" . (new DateTime($maxDate))->format('d F Y H:i:s') . "'," : "" ?>
    //    locale: {
    //        format: 'DD MMMM YYYY HH:mm:ss'
    //    }
    //}
</script>

<script src="<?= base_url('assets/app/js/ownership.js') ?>" defer></script>