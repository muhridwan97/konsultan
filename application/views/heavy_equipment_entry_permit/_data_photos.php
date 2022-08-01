<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Photo Start</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Link Photo</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <a href="<?= base_url("uploads/security_heep/".$heep['photo_in']) ?>" target="_blank">
                                <?= strtoupper($heep['photo_in'])?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Photo Stop</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Link Photo</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <a href="<?= base_url("uploads/security_heep/".$heep['photo_out']) ?>" target="_blank">
                                <?= strtoupper($heep['photo_out'])?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>