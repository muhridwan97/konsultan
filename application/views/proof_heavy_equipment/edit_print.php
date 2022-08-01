<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Print</h3>
    </div>
    <form action="<?= site_url('proof-heavy-equipment/proof_print') ?>" role="form" method="post" id="form-edit-print">
        <input type="hidden" name='id_reference' id="id_reference" value="<?= $data_print['id_reference'] ?>">
        <input type="hidden" name='type' id="type" value="<?= $data_print['type'] ?>">
        <input type="hidden" name='id_customer' id="id_customer" value="<?= $data_print['id_customer'] ?>">
        <?php $this->load->view('template/_alert') ?>
        <div class="box-body">
            <div class="form-group">
                <label for="alat_berat" class="control-label">Alat Berat</label>
                <input type="text" class="form-control" name="alat_berat" id="alat_berat"
                        placeholder="Heavy Equipment name" required maxlength="50" value="<?= set_value('alat_berat',$data_print['alat_berat']) ?>">
            </div>
            <div class="form-group">
                <label for="hari" class="control-label">Hari</label>
                <input type="text" class="form-control" name="hari" id="hari" readonly
                        placeholder="Day name" required maxlength="50" value="<?= set_value('hari',$data_print['day_name']) ?>">
            </div>
            <div class="form-group">
                <label for="date">Tanggal</label>
                <input type="text" class="form-control datepicker" id="date" name="date"
                        placeholder="Date" autocomplete="off" required
                        maxlength="50" value="<?= set_value('date', $data_print['date']) ?>">
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <label for="start">Start</label>
                    <input type="text" class="form-control" id="start" name="start"
                            placeholder="Date" autocomplete="off" required
                            maxlength="50" value="<?= set_value('start', $data_print['start']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="end">Finish</label>
                    <input type="text" class="form-control" id="end" name="end"
                            placeholder="Date" autocomplete="off" required
                            maxlength="50" value="<?= set_value('end', $data_print['end']) ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Keterangan</label>
                <textarea class="form-control" id="description" name="description"
                        placeholder="Keterangan"
                        maxlength="500"><?= $data_print['description'] ?></textarea>
            </div>
            <div class="form-group">
                <label for="remark" class="control-label">Remark</label>
                <textarea class="form-control" id="remark" name="remark"
                        placeholder="Remark"
                        maxlength="500"><?= $data_print['remark'] ?></textarea>
            </div>
            <div class="form-group">
                <label for="sign_location" class="control-label">Lokasi Ttd</label>
                <input type="text" class="form-control" name="sign_location" id="sign_location"
                        placeholder="City Name" maxlength="50" value="<?= set_value('sign_location',$data_print['sign_location']) ?>">
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="<?= site_url('proof-heavy-equipment') ?>" class="btn btn-primary">Back</a>
            <button type="submit" class="btn btn-primary pull-right" >
                Print
            </button>
        </div>
    </form>
</div>
<script src="<?= base_url('assets/app/js/proof_heavy_equipment.js') ?>" defer></script>