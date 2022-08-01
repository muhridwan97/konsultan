<div class="modal fade" tabindex="-1" role="dialog" id="modal-tep-check">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post" id="form-modal-tep-check-in" class="need-validation">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">TEP Check In : <strong><?= $tep['tep_category'] ?></strong></h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to check in
                        <strong id="check-in-title"></strong>?
                    </p>
                    <p class="text-warning">
                        Security check in automatic set to present time when the form is submitted.
                    </p>
                    <div class="form-group">
                        <label for="receiver_name" class="control-label">Receiver Name</label>
                        <input type="text" class="form-control" name="receiver_name" id="receiver_name"
                               placeholder="Carrier name" required maxlength="50">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_vehicle" class="control-label">Vehicle</label>
                                <select class="form-control select2" name="receiver_vehicle" id="receiver_vehicle"
                                        data-placeholder="Vehicle type" style="width: 100%" required>
                                    <option value=""></option>
                                    <option value="Highbed" <?= set_select('receiver_vehicle', 'Highbed', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Highbed') ?>>Highbed</option>
                                    <option value="Lowbed" <?= set_select('receiver_vehicle', 'Lowbed', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Lowbed') ?>>Lowbed</option>
                                    <option value="Wingbox" <?= set_select('receiver_vehicle', 'Wingbox', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Wingbox') ?>>Wingbox</option>
                                    <option value="Fuso" <?= set_select('receiver_vehicle', 'Fuso', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Fuso') ?>>Fuso</option>
                                    <option value="CDD" <?= set_select('receiver_vehicle', 'CDD', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'CDD') ?>>CDD</option>
                                    <option value="CDE" <?= set_select('receiver_vehicle', 'CDE', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'CDE') ?>>CDE</option>
                                    <option value="Pickup" <?= set_select('receiver_vehicle', 'Pickup', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Pickup') ?>>Pickup</option>
                                    <option value="Minibus (Van, Xenia, Avanza dll )" <?= set_select('receiver_vehicle', 'Minibus (Van, Xenia, Avanza dll )', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Minibus (Van, Xenia, Avanza dll )') ?>>Minibus (Van, Xenia, Avanza dll )</option>
                                    <option value="Motor" <?= set_select('receiver_vehicle', 'Motor', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Motor') ?>>Motor</option>
                                    <option value="SLIDING BED" <?= set_select('receiver_vehicle', 'SLIDING BED', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'SLIDING BED') ?>>SLIDING BED</option>
                                    <option value="Container 40" <?= set_select('receiver_vehicle', 'Container 40', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Container 40') ?>>Container 40</option>
                                    <option value="Container 20" <?= set_select('receiver_vehicle', 'Container 20', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Container 20') ?>>Container 20</option>
                                    <option value="Multiaxle" <?= set_select('receiver_vehicle', 'Multiaxle', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Multiaxle') ?>>Multiaxle</option>
                                    <option value="Truck" <?= set_select('receiver_vehicle', 'Truck', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Truck') ?>>Truck</option>
                                    <option value="Trailer" <?= set_select('receiver_vehicle', 'Trailer', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Trailer') ?>>Trailer</option>
                                    <option value="Fletrek 40" <?= set_select('receiver_vehicle', 'Fletrek 40', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Fletrek 40') ?>>Fletrek 40</option>
                                    <option value="Fletrek 20" <?= set_select('receiver_vehicle', 'Fletrek 20', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Fletrek 20') ?>>Fletrek 20</option>
                                    <option value="Truck box" <?= set_select('receiver_vehicle', 'Truck box', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Truck box') ?>>Truck box</option>
                                    <option value="Truck engkle" <?= set_select('receiver_vehicle', 'Truck engkle', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Truck engkle') ?>>Truck engkle</option>
                                    <option value="Mobil box" <?= set_select('receiver_vehicle', 'Mobil box', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Mobil box') ?>>Mobil box</option>
                                    <option value="Lain-lain" <?= set_select('receiver_vehicle', 'Lain-lain', (isset($_GET['receiver_vehicle']) ? $_GET['receiver_vehicle'] : '') == 'Lain-lain') ?>>Lain-lain</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_no_police" class="control-label">No Police</label>
                                <input type="text" class="form-control" name="receiver_no_police"
                                       placeholder="No police" id="receiver_no_police" required maxlength="50" pattern="[A-Za-z]{1,2}[0-9]{1,4}[A-Za-z]{0,3}" title="Input Plate Number Format Without Space">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_contact" class="control-label">Receiver Contact</label>
                                <input type="text" class="form-control" name="receiver_contact"
                                       placeholder="Carrier contact" id="receiver_contact" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receiver_email" class="control-label">Receiver Email</label>
                                <input type="email" class="form-control" name="receiver_email"
                                       placeholder="Carrier email" id="receiver_email" maxlength="50">
                            </div>
                        </div>
                    </div>
                    <?php if ($tep['tep_category'] == 'OUTBOUND' && $tep['armada_owner'] == 'TCI'): ?>
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Chassis Handling</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="chassis_handling_type" class="control-label">Drop Chassis?</label>
                                    <select class="form-control select2" name="chassis_handling_type" id="chassis_handling_type"
                                            data-placeholder="Chassis handling type" style="width: 100%" required>
                                        <option value=""></option>
                                        <option value="delivery">No, Deliver / Pickup Existing Goods / Chassis</option>
                                        <option value="drop-chassis">Yes, Drop Chassis</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="no_chassis" class="control-label">Chassis Number</label>
                                    <input type="text" class="form-control" name="no_chassis" required disabled
                                           placeholder="Type the chassis number" id="no_chassis" maxlength="100">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Additional Guest</h3>
                        </div>
                        <div class="box-body">
                            <table class="table no-datatable" id="table-detail-additional-guest">
                                <thead>
                                <tr>
                                    <th style="width: 20px">No</th>
                                    <th style="width: 250px">Name</th>
                                    <th style="width: 50px">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Click <strong>Add New Additional Guest</strong> to insert new record
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-block btn-primary mt10" id="btn-add-additional-guest">
                                ADD NEW ADDITIONAL GUEST
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label">Check In Description</label>
                        <textarea name="description" id="description" cols="30" rows="2" maxlength="500"
                                  class="form-control" placeholder="Check in remark"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" data-toggle="one-touch">Check In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script id="row-additional-guest-template" type="text/x-custom-template">
    <tr class="row-additional-guest-template">
        <td></td>
        <td>
            <input type="text" class="form-control" required name="additional_guest_name[]" id="additional_guest_name"
                   placeholder="Additional Guest Name">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-guest">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>
<script src="<?= base_url('assets/app/js/tep_check.js?v=2') ?>" defer></script>