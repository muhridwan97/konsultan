<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">System Logs</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th class="text-md-center" style="width: 40px">No</th>
                <th>Log File</th>
                <th>File Size</th>
                <th>Last Modified</th>
                <th style="width: 100px" class="text-md-right">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $index => $log): ?>
                <tr>
                    <td class="text-md-center"><?= $index + 1 ?></td>
                    <td><?= if_empty($log['log_file'], '-') ?></td>
                    <td><?= numerical($log['file_size'], 2, true) ?> KB</td>
                    <td><?= format_date($log['last_modified'], 'd F Y H:i') ?></td>
                    <td class="text-md-right">
                        <a href="<?= site_url('system-log/download/' . $log['log_file']) ?>" class="btn btn-primary" type="button">
                            Download
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="2">No log data available</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>