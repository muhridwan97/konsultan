<table class="table table-stripped no-datatable">
    <thead>
    <tr>
        <th>No</th>
        <th>Table</th>
        <th style="width: 100px">Browse</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($tables as $table): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $table['table_name'] ?></td>
            <td>
                <button class="btn btn-sm btn-primary btn-browse-table"
                        data-module="<?= $moduleId ?>"
                        data-table="<?= $table['table_name'] ?>">
                    Browse
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>