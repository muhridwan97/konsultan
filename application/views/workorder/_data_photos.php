<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Photo Taken</h3>
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
                <?php $no = 1; ?>
                <?php foreach ($workOrderPhotos as $workOrderPhoto): ?>
                    <?php if (substr($workOrderPhoto['photo'],0,6)!="tally_"):?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <a href="<?= asset_url($workOrderPhoto['photo']) ?>" target="_blank">
                                <?= strtoupper($workOrderPhoto['description'])?>
                            </a>
                        </td>
                    </tr>
                    <?php endif ?>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Photo Completed</h3>
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
                <?php $no = 1; ?>
                <?php foreach ($workOrderPhotos as $workOrderPhoto): ?>
                    <?php if (substr($workOrderPhoto['photo'],0,6)=="tally_"):?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <a href="<?= asset_url($workOrderPhoto['photo']) ?>" target="_blank">
                                <?= strtoupper($workOrderPhoto['description'])?>
                            </a>
                        </td>
                    </tr>
                    <?php endif ?>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>