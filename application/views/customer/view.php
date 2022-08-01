<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Person</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="row">
                <!-- photo -->
                <div class="col-lg-2 col-xs-12">
                    <img class="img-responsive"
                         src="<?= base_url('assets/app/img/avatar/' . if_empty($person['photo'], 'no-avatar.jpg')) ?>"
                         alt="User Avatar">
                </div>
                <!-- end of photo -->
                <!-- details -->
                <div class="col-lg-10">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-4">Type</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $person['type'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">No Person</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $person['no_person'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $person['name'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Parent</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= if_empty($person['parent_name'], '-') ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Gender</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $person['gender'] ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Birthday</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= readable_date($person['birthday'], false) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Address</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($person['address'], 'No address') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">User Account</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?php if(!empty($person['id_user'])): ?>
                                            <?php if(is_array($person['id_user'])): ?>
                                                <?php foreach ($person['id_user'] as $key => $value) : ?>
                                                    <a href="<?= site_url('user/view/' . $value) ?>">
                                                        Related User Account <?=$key+1?>
                                                    </a></br>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <a href="<?= site_url('user/view/' . $person['id_user']) ?>">
                                                    Related User Account
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Region</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($person['region'], '-') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Branches</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?php foreach ($branches as $branch): ?>
                                            <?= $branch['branch'] ?><br>
                                        <?php endforeach; ?>
                                        <?php if (empty($branches)): ?>
                                            -
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Max Time Request</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?php if (empty($person['max_time_request'])): ?>
                                            no set time
                                        <?php else:?>
                                            <?= format_date($branch['max_time_request'], 'H:i') ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-4">Outbound Type</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($person['outbound_type'], '-') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Contact</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($person['contact'], 'No contact') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Email</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($person['email'], 'No email') ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Confirm Email</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($person['confirm_email_source'], 'No email source') ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Website</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($person['website'], 'No website') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Tax Number</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= if_empty($person['tax_number'], 'No tax number') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Created At</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= readable_date($person['created_at']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Updated At</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= readable_date($person['updated_at']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Handling Types</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?php foreach ($handlingTypes as $handlingType): ?>
                                            <?= $handlingType['handling_type'] ?><br>
                                        <?php endforeach; ?>
                                        <?php if (empty($handlingTypes)): ?>
                                            -
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Document Types (Reminder)</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?php foreach ($documentTypes as $documentType): ?>
                                            <?= $documentType['document_type'] ?><br>
                                        <?php endforeach; ?>
                                        <?php if (empty($documentTypes)): ?>
                                            -
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary mt20">
                <div class="box-header">
                    <h3 class="box-title">Contacts</h3>
                </div>
                <div class="box-body">

                    <?php if ($this->session->flashdata('status') != NULL): ?>
                        <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <p><?= $this->session->flashdata('message'); ?></p>
                        </div>
                    <?php endif; ?>

                    <table class="table table-condensed" id="table-people-contact">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>Name</th>
                            <th>Occupation</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($contacts)) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($contacts as $contact) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $contact['name'] ?></td>
                                    <td><?= $contact['occupation'] ?></td>
                                    <td><?= $contact['phone'] ?></td>
                                    <td><?= $contact['email'] ?></td>
                                    <td><?= $contact['address'] ?></td>
                                    <td style="width: 70px">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle"
                                                    data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                Action <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li class="dropdown-header">ACTION</li>

                                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_EDIT)): ?>
                                                    <li>
                                                        <a href="<?= site_url('people_contact/edit/' . $contact['id']) ?>">
                                                            <i class="fa ion-compose"></i>Edit
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_DELETE)): ?>
                                                    <li role="separator" class="divider"></li>
                                                    <li>
                                                        <a href="<?= site_url('people_contact/delete/' . $contact['id']) ?>"
                                                           class="btn-delete"
                                                           data-id="<?= $contact['id'] ?>"
                                                           data-title="Contact Person"
                                                           data-label="<?= $contact['name'] ?>">
                                                            <i class="fa ion-trash-a"></i> Delete
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end of details -->

            <?php if ($person['type'] == PeopleModel::$TYPE_CUSTOMER): ?>
                <div class="box box-primary mt20">
                    <div class="box-header">
                        <h3 class="box-title">Customer Storage</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th style="width: 30px">No</th>
                                <th>Effective Date</th>
                                <th>Expired Date</th>
                                <th>Warehouse M<sup>2</sup></th>
                                <th>Yard M<sup>2</sup></th>
                                <th>Covered Yard M<sup>2</sup></th>
                                <th style="width: 60px">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $statusStorage = [
                                CustomerStorageCapacityModel::STATUS_ACTIVE => 'success',
                                CustomerStorageCapacityModel::STATUS_PENDING => 'warning',
                                CustomerStorageCapacityModel::STATUS_EXPIRED => 'danger',
                            ]
                            ?>
                            <?php foreach ($customerStorageCapacities as $index => $customerStorageCapacity) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $customerStorageCapacity['effective_date'] ?></td>
                                    <td><?= $customerStorageCapacity['expired_date'] ?></td>
                                    <td><?= numerical($customerStorageCapacity['warehouse_capacity'], 2, true) ?></td>
                                    <td><?= numerical($customerStorageCapacity['yard_capacity'], 2, true) ?></td>
                                    <td><?= numerical($customerStorageCapacity['covered_yard_capacity'], 2, true) ?></td>
                                    <td>
                                        <span class="label label-<?= get_if_exist($statusStorage, $customerStorageCapacity['status'], 'default')  ?>">
                                            <?= $customerStorageCapacity['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div class="box box-primary mt20">
                <div class="box-header">
                    <h3 class="box-title"><?= ucwords(strtolower($person['type']))?> Member</h3>
                </div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th style="width: 30px">No</th>
                            <th>Name</th>
                            <th>No Person</th>
                            <th>Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($members as $index => $member) : ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $member['name'] ?></td>
                                <td><?= $member['no_person'] ?></td>
                                <td><?= $member['type'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <a href="<?= site_url('people_contact/create/' . $person['id']) ?>" class="btn btn-primary pull-right">
                Add Contact
            </a>
        </div>
    </form>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_PEOPLE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>