<table class="table table-condensed table-striped no-datatable responsive" <?= isset($borderPlain) && $borderPlain ? 'border="1"' : '' ?>>
    <thead>
    <tr>
        <th style="width: 25px">No</th>
        <th>No Container</th>
        <th>Type</th>
        <th>Size</th>
        <th>Seal</th>
        <th>Position</th>
        <th>Is Empty</th>
        <th>Is Hold</th>
        <th>Status</th>
        <th>Danger</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($containers as $container): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $container['no_container'] ?></td>
            <td><?= $container['type'] ?></td>
            <td><?= $container['size'] ?></td>
            <td><?= if_empty($container['seal'], '-') ?></td>
            <td><?= if_empty($container['position'], '-') ?></td>
            <td class="<?= $container['is_empty'] ? 'bg-red' :'' ?>">
                <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
            </td>
            <td class="<?= $container['is_hold'] ? 'bg-red' :'' ?>">
                <?= $container['is_hold'] ? 'Yes' : 'No' ?>
            </td>
            <td><?= if_empty($container['status'], 'No Status') ?></td>
            <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>">
                <?= $container['status_danger'] ?>
            </td>
        </tr>
        <?php
        $containerGoodsExist = key_exists('goods', $container) && !empty($container['goods']);
        $containerContainersExist = key_exists('containers', $container) && !empty($container['containers']);
        ?>
        <?php if ($containerGoodsExist || $containerContainersExist): ?>
            <tr>
                <td></td>
                <td colspan="9">
                    <?php if ($containerContainersExist): ?>
                        <?php $this->load->view('booking_control/_data_container', ['containers' => $container['containers']]) ?>
                    <?php endif; ?>
                    <?php if ($containerGoodsExist): ?>
                        <?php $this->load->view('booking_control/_data_goods', ['goods' => $container['goods']]) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>