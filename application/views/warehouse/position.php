<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Positions <strong><?= $warehouse['warehouse'] ?></strong></h3>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped" id="table-position">
            <thead>
            <tr>
                <th>No</th>
                <th>Warehouse</th>
                <th>Customer</th>
                <th>Position</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($positions as $position): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <a href="<?= site_url('warehouse/view/' . $position['id_warehouse']) ?>">
                            <?= $position['warehouse'] ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= site_url('people/view/' . $position['id_customer']) ?>">
                            <?= $position['name'] ?>
                        </a>
                    </td>
                    <td><?= $position['position'] ?></td>
                    <td><?= if_empty($position['description'], 'No description') ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>