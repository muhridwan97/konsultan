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
                <?php foreach ($tepChecklistsIn as $index => $checklistsIn): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <a href="<?= asset_url($checklistsIn['photo']) ?>" target="_blank">
                                <?= strtoupper($checklistsIn['title']) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ($attachmentSealIn as $index => $checklistsIn): ?>
                    <tr>
                        <td><?= count($tepChecklistsIn) + $index + 1 ?></td>
                        <td>
                            <a href="<?= asset_url($checklistsIn['attachment_seal']) ?>" target="_blank">
                                Photo Seal : <?= strtoupper(basename($checklistsIn['attachment_seal'])) ?>
                            </a><br>
                            Description Seal : <?= strtoupper($checklistsIn['description']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tepChecklistsIn) && empty($attachmentSealIn)): ?>
                    <tr>
                        <td colspan="2">No checklist in data</td>
                    </tr>
                <?php endif; ?>
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
                <?php foreach ($tepChecklistsOut as $index => $checklistsOut): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <a href="<?= asset_url($checklistsOut['photo']) ?>" target="_blank">
                                <?= strtoupper($checklistsOut['title']) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ($attachmentSealOut as $index => $checklistsOut): ?>
                    <tr>
                        <td><?= count($tepChecklistsOut) + $index + 1 ?></td>
                        <td>
                            <a href="<?= asset_url($checklistsOut['attachment_seal']) ?>" target="_blank">
                                Photo Seal : <?= strtoupper(basename($checklistsOut['attachment_seal'])) ?>
                            </a><br>
                            Description Seal : <?= strtoupper($checklistsOut['description']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tepChecklistsOut) && empty($attachmentSealOut)): ?>
                    <tr>
                        <td colspan="2">No checklist out data</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>