<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Outstanding Completed</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped no-wrap table-responsive">
            <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>No Reference</th>
                <th>SPPB Date</th>
                <th>Category</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $index => $booking): ?>
                <tr>
                    <td><?= $index + 1?></td>
                    <td><?= $booking['customer_name'] ?></td>
                    <td><?= $booking['no_reference'] ?></td>
                    <td><?= $booking['sppb_date'] ?></td>
                    <td><?= $booking['category'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
