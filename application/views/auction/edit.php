<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit <?= $auction['no_auction'] ?></h3>
    </div>

    <form action="<?= site_url('auction/update/' . $auction['id']) ?>" role="form" method="post" id="form-auction">
        <?= _method('put') ?>

        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach (get_customer_branch() as $branch): ?>
                            <option value="<?= $branch['id'] ?>"<?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('no_doc') == '' ?: 'has-error'; ?>">
                        <label for="no_doc">No Doc</label>
                        <input type="text" name="no_doc" id="no_doc" class="form-control" required
                               placeholder="Auction document number" value="<?= set_value('no_doc', $auction['no_doc']) ?>">
                        <?= form_error('no_doc', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('doc_date') == '' ?: 'has-error'; ?>">
                        <label for="doc_date">Doc Date</label>
                        <input type="text" class="form-control datepicker" id="doc_date" name="doc_date"
                               placeholder="Auction document date" required
                               value="<?= set_value('doc_date', readable_date($auction['doc_date'], false)) ?>">
                        <?= form_error('doc_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('auction_date') == '' ?: 'has-error'; ?>">
                <label for="auction_date">Auction Event Date</label>
                <input type="text" class="form-control datepicker" id="auction_date" name="auction_date"
                       placeholder="Auction event date" required
                       value="<?= set_value('auction_date', readable_date($auction['auction_date'], false)) ?>">
                <?= form_error('auction_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Auction description"
                          maxlength="500"><?= set_value('description', $auction['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Booking Data</h3>
                </div>
                <div class="box-body">
                    <table class="table no-datatable" id="table-detail-booking">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th style="width: 300px">Booking</th>
                            <th>Description</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $bookingData = set_value('bookings[booking]', $auctionDetails); ?>
                        <?php $descriptionData = set_value('bookings[description]', []) ?>
                        <?php if (!empty($bookingData)): ?>
                            <?php $no = 1; ?>
                            <?php for ($i = 0; $i < count($bookingData); $i++): ?>
                                <tr class="row-booking">
                                    <td class="no"><?= $no++ ?></td>
                                    <td>
                                        <select class="form-control select2" name="bookings[booking][]" id="detail_booking"
                                                data-placeholder="Select booking" required style="width: 100%">
                                            <option value=""></option>
                                            <?php foreach ($bookings as $booking): ?>
                                                <option value="<?= $booking['id_booking'] ?>" <?= set_select('bookings[booking]['.$i.']', $booking['id_booking'], $booking['id_booking'] == $bookingData[$i]['id_booking']) ?>>
                                                    <?= $booking['no_reference'] ?> - <?= $booking['owner_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control" name="bookings[description][]" id="detail_description"
                                               placeholder="Description" type="text" value="<?= set_value('bookings[description]['.$i.']', $bookingData[$i]['description']) ?>" maxlength="500">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-booking">
                                            <i class="ion-trash-b"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        <?php else: ?>
                            <tr class="row-placeholder">
                                <td colspan="4" class="text-center">
                                    Click <strong>Add Booking</strong> to insert new record
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
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Update Auction
            </button>
        </div>
    </form>
</div>

<script id="row-booking-template" type="text/x-custom-template">
    <tr class="row-booking">
        <td class="no"></td>
        <td>
            <select class="form-control select2" name="bookings[booking][]" id="detail_booking"
                    data-placeholder="Select booking" required style="width: 100%">
                <option value=""></option>
                <?php foreach ($bookings as $booking): ?>
                    <option value="<?= $booking['id_booking'] ?>">
                        <?= $booking['no_reference'] ?> - <?= $booking['owner_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="text" class="form-control" name="bookings[description][]" id="detail_description"
                   placeholder="Description" maxlength="500">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-booking">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>

<script src="<?= base_url('assets/app/js/auction.js') ?>" defer></script>