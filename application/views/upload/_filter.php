<form role="form" method="get" class="form-filter" id="form-filter" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer">Customer</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                name="customer" id="customer" data-placeholder="Select customer">
                            <option value=""></option>
                            <?php if (!empty($customer)): ?>
                                <option value="<?= $customer['id'] ?>" selected>
                                    <?= $customer['name'] ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="booking_type">Booking Type</label>
                        <select class="form-control select2" name="booking_type" id="booking_type" data-placeholder="Select booking type">
                            <option value="0">ALL BOOKING TYPE</option>
                            <?php foreach ($bookingTypes as $bookingType): ?>
                                <option value="<?= $bookingType['id'] ?>"<?= get_url_param('booking_type') == $bookingType['id'] ? ' selected' : '' ?>>
                                    <?= $bookingType['booking_type'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="document_type">Document Type</label>
                        <select class="form-control select2" name="document_type" id="document_type" data-placeholder="Select document type">
                            <option value="0">ALL DOCUMENT</option>
                            <?php foreach ($documentTypes as $documentType): ?>
                                <option value="<?= $documentType['id'] ?>"<?= get_url_param('document_type') == $documentType['id'] ? ' selected' : '' ?>>
                                    <?= $documentType['document_type'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="document_type_query">Document Query</label>
                        <input type="text" class="form-control" id="document_type_query" name="document_type_query"
                               placeholder="Search query of document desc, no doc or date 2020-01-01"
                               maxlength="50" value="<?= get_url_param('document_type_query') ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="date_from">First Upload Date From</label>
                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                               placeholder="Date from" autocomplete="off"
                               maxlength="50" value="<?= get_url_param('date_from') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="date_to">First Upload Date To</label>
                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                               placeholder="Date to" autocomplete="off"
                               maxlength="50" value="<?= get_url_param('date_to') ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default btn-reset-filter">Reset Filter</a>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>
