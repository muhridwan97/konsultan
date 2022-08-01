<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Booking Data</h3>
    </div>
    <div class="box-body">
        <table class="table no-datatable" id="table-detail-booking">
            <thead>
            <tr>
                <th style="width: 20px">No</th>
                <th style="width: 230px">Booking</th>
                <th style="width: 150px">Condition</th>
                <th>Description</th>
                <th style="width: 50px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($bookingData) && !empty($bookingData)): ?>
                <?php $no = 1; ?>
                <?php foreach ($bookingData as $bookingDatum): ?>
                    <tr class="row-booking">
                        <td><?= $no++ ?></td>
                        <td>
                            <select class="form-control select2" name="bookings[booking][]" id="detail_booking"
                                    data-placeholder="Select booking" required>
                                <option value=""></option>
                                <?php foreach ($bookings as $booking): ?>
                                    <?php
                                    $isSelected = '';
                                    if ($booking['id'] == $bookingDatum['id_booking']) {
                                        $isSelected = 'selected';
                                    }
                                    ?>
                                    <option value="<?= $booking['id'] ?>" <?= $isSelected ?>>
                                        <?= $booking['no_booking'] ?> - <?= $booking['customer_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control select2" name="bookings[condition][]" id="detail_condition"
                                    data-placeholder="Select condition" required style="width: 100%">
                                <option value=""></option>
                                <option value="GOOD" <?= $bookingDatum['condition'] == 'GOOD' ? 'selected' : '' ?>>GOOD</option>
                                <option value="DAMAGE" <?= $bookingDatum['condition'] == 'DAMAGE' ? 'selected' : '' ?>>DAMAGE</option>
                                <option value="USED" <?= $bookingDatum['condition'] == 'USED' ? 'selected' : '' ?>>USED</option>
                            </select>
                        </td>
                        <td>
                            <input class="form-control" name="bookings[description][]" id="detail_description"
                                   placeholder="Description" type="text" value="<?= $bookingDatum['description'] ?>">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-booking">
                                <i class="ion-trash-b"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">
                        Click <strong>Add New Booking</strong> to insert new record
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        <button type="button" class="btn btn-block btn-primary btn-add-booking">
            ADD BOOKING
        </button>
    </div>
</div>

<script id="row-booking-template" type="text/x-custom-template">
    <tr class="row-booking">
        <td></td>
        <td>
            <select class="form-control select2" name="bookings[booking][]" id="detail_booking"
                    data-placeholder="Select booking" required style="width: 100%">
                <option value=""></option>
                <?php foreach ($bookings as $booking): ?>
                    <option value="<?= $booking['id'] ?>">
                        <?= $booking['no_booking'] ?> - <?= $booking['customer_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select class="form-control select2" name="bookings[condition][]" id="detail_condition"
                    data-placeholder="Select condition" required style="width: 100%">
                <option value=""></option>
                <option value="GOOD">GOOD</option>
                <option value="DAMAGE">DAMAGE</option>
                <option value="USED">USED</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control" name="bookings[description][]" id="detail_description"
                   placeholder="Description">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-booking">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>