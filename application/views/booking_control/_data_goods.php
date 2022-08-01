<table class="table table-condensed table-striped no-datatable responsive" <?= isset($borderPlain) && $borderPlain ? 'border="1"' : '' ?>>
    <thead>
    <tr>
        <th style="width: 25px">No</th>
        <th>Goods</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Uni Weight (Kg)</th>
        <th>Total Weight (Kg)</th>
        <th>Unit Gross (Kg)</th>
        <th>Total Gross (Kg)</th>
        <th>LxWxH (M)</th>
        <th>Volume (M<sup>3</sup>)</th>
        <th>Total Volume (M<sup>3</sup>)</th>
        <th>Position</th>
        <th>No Pallet</th>
        <th>Danger</th>
        <th>Ex Container</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($goods as $item): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $item['goods_name'] ?><br>
                <small class="text-muted"><?= $item['no_goods'] ?></small>
            </td>
            <td><?= numerical($item['quantity'], 3, true) ?></td>
            <td><?= $item['unit'] ?></td>
            <td><?= numerical($item['unit_weight'], 3, true) ?></td>
            <td><?= numerical($item['total_weight'], 3, true) ?></td>
            <td><?= numerical($item['unit_gross_weight'], 3, true) ?></td>
            <td><?= numerical($item['total_gross_weight'], 3, true) ?></td>
            <td>
                <?= numerical($item['unit_length'], 3, true) ?>
                x <?= numerical($item['unit_width'], 3, true) ?>
                x <?= numerical($item['unit_height'], 3, true) ?>
            </td>
            <td><?= numerical($item['unit_volume']) ?></td>
            <td><?= numerical($item['total_volume']) ?></td>
            <td><?= if_empty($item['position'], '-') ?></td>
            <td><?= if_empty($item['no_pallet'], '-') ?></td>
            <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>">
                <?= $item['status_danger'] ?>
            </td>
            <td><?= if_empty($item['ex_no_container'], '-') ?></td>
        </tr>
        <?php if (key_exists('goods', $item) && !empty($item['goods'])): ?>
            <tr>
                <td></td>
                <td colspan="14">
                    <?php $this->load->view('booking_control/_data_goods', ['goods' => $item['goods']]) ?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>