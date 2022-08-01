<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Photo <?= $category ?></h3>
    </div>
    <form action="<?= site_url('security-photo-type/update/' . $category) ?>" role="form" method="post" id="form-security-photo-type">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Start Photo</h3>
                </div>
                <div class="box-body">
                    <table class="table no-datatable table-photo" id="table-photo-start">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th>Photo Title</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $securityPhotoStarts = set_value('photo_starts[]', $securityPhotoStarts) ?>
                        <?php foreach ($securityPhotoStarts as $index => $securityPhotoStart): ?>
                            <tr class="row-photo">
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <input type="text" class="form-control input-photo-title" name="photo_starts[]" id="photo_start_<?= $index ?>"
                                           value="<?= $securityPhotoStart['photo_title'] ?? $securityPhotoStart ?>"
                                           placeholder="Type photo title" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-photo">
                                        <i class="ion-trash-b"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-center row-placeholder"<?= !empty($securityPhotoStarts) ? ' style="display: none"' : '' ?>>
                                Click <strong>Add New Photo</strong> to insert new record
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-block btn-primary mt10 btn-add-photo" data-target="#table-photo-start" data-name="photo_starts">
                        ADD PHOTO
                    </button>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Stop Photo</h3>
                </div>
                <div class="box-body">
                    <table class="table no-datatable table-photo" id="table-photo-stop">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th>Photo Title</th>
                            <th style="width: 50px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $securityPhotoStops = set_value('photo_stops[]', $securityPhotoStops) ?>
                        <?php foreach ($securityPhotoStops as $index => $securityPhotoStop): ?>
                            <tr class="row-photo">
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <input type="text" class="form-control input-photo-title" name="photo_stops[]" id="photo_stop_<?= $index ?>"
                                           value="<?= $securityPhotoStop['photo_title'] ?? $securityPhotoStop ?>"
                                           placeholder="Type photo title" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-photo">
                                        <i class="ion-trash-b"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-center row-placeholder"<?= !empty($securityPhotoStops) ? ' style="display: none"' : '' ?>>
                                Click <strong>Add New Photo</strong> to insert new record
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-block btn-primary mt10 btn-add-photo" data-target="#table-photo-stop" data-name="photo_stops">
                        ADD PHOTO
                    </button>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-warning pull-right">
                Update Photo Types
            </button>
        </div>
    </form>
</div>

<script id="row-photo-template" type="text/x-custom-template">
    <tr class="row-photo">
        <td></td>
        <td>
            <input type="text" class="form-control input-photo-title" placeholder="Type photo title" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove-photo">
                <i class="ion-trash-b"></i>
            </button>
        </td>
    </tr>
</script>

<script src="<?= base_url('assets/app/js/security_photo_type.js') ?>" defer></script>