<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Transporter External Detail Report Week - <?= get_url_param('week',0) ?></h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped responsive no-datatable">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Safe Conduct Date</th>
                <th>Customer</th>
                <th>Branch</th>
                <th>No Safe Conduct</th>
                <th>No Work Order</th>
                <th>No Reference</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 0 ?>
            <?php $lastReference = '' ?>
            <?php foreach ($details as $detail): ?>
                <tr>
                    <?php if (empty($detail['id_transporter_entry_permit']) || $detail['id_transporter_entry_permit'] != $lastReference) : ?>
                        <?php $no++ ?>
                        <td rowspan="<?= $detail['total_row'] ?>">
                            <?= $no ?>
                        </td>
                    <?php endif; ?>
                    <td><?= format_date($detail['created_at'], 'd F Y') ?></td>
                    <td><?= strtoupper($detail['customer_name']) ?></td>
                    <td><?= strtoupper($detail['branch']) ?></td>
                    <td>
                        <a href="<?= site_url('p/' . $detail['id_branch'] . '/safe-conduct/view/' . $detail['id'], false) ?>" target="_blank">
                            <?= $detail['no_safe_conduct'] ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= site_url('p/' . $detail['id_branch'] . '/work-order/view/' . $detail['id_work_order'], false) ?>" target="_blank">
                            <?= $detail['no_work_order'] ?>
                        </a>
                    </td>
                    <td><?= $detail['no_reference'] ?></td>
                    <?php if ($detail['id_transporter_entry_permit'] != $lastReference) : ?>
                        <?php $lastReference = $detail['id_transporter_entry_permit'] ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
