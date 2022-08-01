<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Delivery Inspection <?= $deliveryInspection['date'] ?></h3>
    </div>

    <form action="<?= site_url('delivery-inspection/update/' . $deliveryInspection['id']) ?>" role="form" method="post" class="need-validation" id="form-delivery-inspection">
        <?= _method('put') ?>

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('document_type') == '' ?: 'has-error'; ?>">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location"
                       placeholder="Enter location" required value="<?= set_value('location', $deliveryInspection['location']) ?>">
                <?= form_error('location', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('pic_tci') == '' ?: 'has-error'; ?>">
                        <label for="pic_tci">PIC TCI</label>
                        <input type="text" class="form-control" id="pic_tci" name="pic_tci"
                               placeholder="Enter PIC TCI name" required value="<?= set_value('pic_tci', $deliveryInspection['pic_tci']) ?>">
                        <?= form_error('pic_tci', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('pic_khaisan') == '' ?: 'has-error'; ?>">
                        <label for="pic_khaisan">PIC Khaisan</label>
                        <input type="text" class="form-control" id="pic_khaisan" name="pic_khaisan"
                               placeholder="Enter PIC Khaisan name" required value="<?= set_value('pic_khaisan', $deliveryInspection['pic_khaisan']) ?>">
                        <?= form_error('pic_khaisan', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('pic_smgp') == '' ?: 'has-error'; ?>">
                        <label for="pic_smgp">PIC SMGP</label>
                        <input type="text" class="form-control" id="pic_smgp" name="pic_smgp"
                               placeholder="Enter PIC SMGP name" required value="<?= set_value('pic_smgp', $deliveryInspection['pic_smgp']) ?>">
                        <?= form_error('pic_smgp', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group <?= form_error('total_vehicle') == '' ?: 'has-error'; ?>">
                        <label for="total_vehicle">Total Vehicle (<?= $deliveryInspection['date'] ?>)</label>
                        <input type="text" class="form-control" id="total_vehicle" name="total_vehicle" readonly
                               placeholder="Total vehicle" required value="<?= $deliveryInspection['total_vehicle'] ?>">
                        <?= form_error('total_vehicle', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group <?= form_error('total_match') == '' ?: 'has-error'; ?>">
                        <label for="total_match">Total Match</label>
                        <input type="text" class="form-control" id="total_match" name="total_match"
                               placeholder="Total actual vehicle" required value="<?= set_value('total_match', if_empty($deliveryInspection['total_match'])) ?>">
                        <?= form_error('total_match', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Delivery description"
                          maxlength="500"><?= set_value('description', $deliveryInspection['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Delivery Inspection Details</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable responsive">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>TEP Code</th>
                            <th>No Vehicle</th>
                            <th>Vehicle Type</th>
                            <th>No Order</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($deliveryInspectionDetails as $deliveryInspectionDetail): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $deliveryInspectionDetail['tep_code'] ?></td>
                                <td><?= $deliveryInspectionDetail['no_vehicle'] ?></td>
                                <td><?= $deliveryInspectionDetail['vehicle_type'] ?></td>
                                <td><?= $deliveryInspectionDetail['no_order'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($deliveryInspectionDetails)): ?>
                            <tr>
                                <td colspan="5">No delivery inspection detail</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">
                Update Inspection
            </button>
        </div>
    </form>
</div>
