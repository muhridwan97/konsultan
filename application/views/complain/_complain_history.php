<?php if(isset($complainHistories)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Complain Histories</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable responsive">
                <thead>
                <tr>
                    <th style="width: 30px">No</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Created By</th>
                    <th>Attachment</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($complainHistories as $complainHistory): ?>
                    <tr>
                        <td class="responsive-hide"><?= $no++ ?></td>
                        <td><?= if_empty(nl2br($complainHistory['description']), '-') ?></td>
                        <td>
                            <span class="label label-<?= $dataLabel[$complainHistory['status']] ?>">
                                <?= $complainHistory['status'] ?>
                            </span>
                        </td>
                        <td><?= format_date($complainHistory['created_at'], 'd M Y H:i') ?></td>
                        <td><?= $complainHistory['creator_name'] ?></td>
                        <td>
                            <?php if(!empty($complainHistory['attachment'])): ?>
                                <a href="<?= asset_url(urlencode($complainHistory['attachment'])) ?>" target="_blank">
                                    File
                                </a>
                            <?php else: ?>
                                No File
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if(empty($complainHistories)): ?>
                    <tr>
                        <td colspan="5">No history available</td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>