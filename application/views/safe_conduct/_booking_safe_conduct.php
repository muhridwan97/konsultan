<table class="table no-datatable">
    <thead>
    <tr>
        <th style="width: 30px">No</th>
        <th>No Safe Conduct</th>
        <th>No Police</th>
        <th>Driver</th>
        <th>Expedition</th>
        <th>Loading</th>
        <th>Check In</th>
        <th>Check Out</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($safeConducts as $safeConduct): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>
                <a href="<?= site_url('safe-conduct/view/' . $safeConduct['id']) ?>"
                   target="_blank">
                    <?= $safeConduct['no_safe_conduct'] ?>
                </a>
            </td>
            <td><?= $safeConduct['no_police'] ?></td>
            <td><?= if_empty($safeConduct['driver'], '-') ?></td>
            <td><?= if_empty($safeConduct['expedition'], 'No expedition') ?></td>
            <td>
                <strong><?= $safeConduct['containers_load'] ?></strong>
                <?php if(!empty($safeConduct['goods_load'])): ?>
                    (<?= $safeConduct['goods_load'] ?>)
                <?php endif; ?>
            </td>
            <td><?= empty($safeConduct['security_in_date']) ? '-' : (new DateTime($safeConduct['security_in_date']))->format('d M Y H:i') ?></td>
            <td><?= empty($safeConduct['security_out_date']) ? '-' : (new DateTime($safeConduct['security_out_date']))->format('d M Y H:i') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>