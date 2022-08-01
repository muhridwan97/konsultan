<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Warehouses <strong><?= $branch['branch'] ?></strong></h3>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped" id="table-warehouse">
            <thead>
            <tr>
                <th style="width: 50px">No</th>
                <th>Warehouse</th>
                <th>Description</th>
                <th>Total Position</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($warehouses as $warehouse): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $warehouse['warehouse'] ?></td>
                    <td><?= $warehouse['description'] ?></td>
                    <td>
                        <?php if($warehouse['total_position'] == 0): ?>
                            No position available
                        <?php else: ?>
                            <a href="<?= site_url('position/warehouse/' . $warehouse['id']) ?>">
                                <?= number_format($warehouse['total_position'], 0, ',', '.') ?> positions
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>