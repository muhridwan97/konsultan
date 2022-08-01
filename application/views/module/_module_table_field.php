<p>Table <strong><?= $tableName ?></strong></p>
<table class="table table-stripped no-datatable">
    <thead>
    <tr>
        <th>No</th>
        <th>Field</th>
        <th style="width: 100px">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($fields as $field): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $field ?></td>
            <td>
                <button class="btn btn-sm btn-primary btn-select-field"
                        data-table="<?= $tableName ?>"
                        data-field="<?= $field ?>">
                    Select as Target
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>