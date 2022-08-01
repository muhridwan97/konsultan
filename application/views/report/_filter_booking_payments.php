<form role="form" method="get" class="form-filter" id="form-filter" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_booking_payments" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        name="customers[]" id="customer"
                        data-placeholder="Select customer" multiple>
                    <option value=""></option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer['id'] ?>" selected>
                            <?= $customer['no_person'] ?> - <?= $customer['name'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="status">Status Payment</label>
                        <select class="form-control select2" name="status" id="status" data-placeholder="Payment status">
                            <?php
                            $filterStatuses = [
                                PaymentModel::STATUS_DRAFT,
                                PaymentModel::STATUS_APPROVED,
                                PaymentModel::STATUS_REALIZED,
                            ]
                            ?>
                            <option value="0">ALL STATUS</option>
                            <?php foreach ($filterStatuses as $filterStatus): ?>
                                <option value="<?= $filterStatus ?>"<?= set_select('status', $filterStatus, get_url_param('status') == $filterStatus) ?>>
                                    <?= $filterStatus ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="status_checks">Status Check</label>
                        <select class="form-control select2" name="status_checks" id="status_checks" data-placeholder="Check status">
                            <?php
                            $filterStatuses = [
                                PaymentModel::STATUS_PENDING,
                                PaymentModel::STATUS_ASK_APPROVAL,
                                PaymentModel::STATUS_REJECTED,
                                PaymentModel::STATUS_APPROVED,
                            ]
                            ?>
                            <option value="0">ALL STATUS</option>
                            <?php foreach ($filterStatuses as $filterStatus): ?>
                                <option value="<?= $filterStatus ?>"<?= set_select('status_checks', $filterStatus, get_url_param('status_checks', $isChecker ? PaymentModel::STATUS_ASK_APPROVAL : '') == $filterStatus) ?>>
                                    <?= $filterStatus ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="payment_category">Payment Category</label>
                        <select class="form-control select2" name="payment_category" id="payment_category" data-placeholder="Category">
                            <?php
                            $filterCategories = [
                                PaymentModel::PAYMENT_BILLING,
                                PaymentModel::PAYMENT_NON_BILLING,
                            ]
                            ?>
                            <option value="0">ALL CATEGORY</option>
                            <?php foreach ($filterCategories as $filterCategory): ?>
                                <option value="<?= $filterCategory ?>"<?= set_select('payment_category', $filterCategory, get_url_param('payment_category') == $filterCategory) ?>>
                                    <?= $filterCategory ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="payment_type">Payment Type</label>
                        <select class="form-control select2" name="payment_type" id="payment_type" data-placeholder="Payment type">
                            <?php
                            $filterTypes = [
                                PaymentModel::TYPE_OB_TPS,
                                PaymentModel::TYPE_OB_TPS_PERFORMA,
                                PaymentModel::TYPE_DISCOUNT,
                                PaymentModel::TYPE_DO,
                                PaymentModel::TYPE_EMPTY_CONTAINER_REPAIR,
                                PaymentModel::TYPE_DRIVER,
                                PaymentModel::TYPE_DISPOSITION_AND_TPS_OPERATIONAL,
                                PaymentModel::TYPE_AS_PER_BILL,
                                PaymentModel::TYPE_TOOLS_AND_EQUIPMENTS,
                                PaymentModel::TYPE_MISC,
                            ]
                            ?>
                            <option value="0">ALL TYPE</option>
                            <?php foreach ($filterTypes as $filterType): ?>
                                <option value="<?= $filterType ?>"<?= set_select('payment_type', $filterType, get_url_param('payment_type') == $filterType) ?>>
                                    <?= $filterType ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6 col-md-3">
                        <label for="date_from">Date Request From</label>
                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                               placeholder="Date from" autocomplete="off"
                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label for="date_to">Date Request To</label>
                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                               placeholder="Date to" autocomplete="off"
                               maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label for="tps_payment">TPS Payment (Performa)</label>
                            <select class="form-control select2" name="tps_payment" id="tps_payment" data-placeholder="TPS Payment">
                                <?php
                                $filterTPSPayments = [
                                    'Paid By Customer',
                                    'Outstanding',
                                    'Realized',
                                ]
                                ?>
                                <option value="0">ALL PAYMENT</option>
                                <?php foreach ($filterTPSPayments as $filterTPSPayment): ?>
                                    <option value="<?= $filterTPSPayment ?>"<?= set_select('payment_category', $filterTPSPayment, get_url_param('tps_payment') == $filterTPSPayment) ?>>
                                        <?= $filterTPSPayment ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <select class="form-control select2" name="branch[]" id="branch" data-placeholder="Select branch" multiple>
                                <option value=""></option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= in_array($branch['id'], get_url_param('branch', [])) ? 'selected' : '' ?>>
                                        <?= $branch['branch'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
