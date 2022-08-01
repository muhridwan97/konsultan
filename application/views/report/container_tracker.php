<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Container Tracker</h3>
    </div>
    <form action="<?= site_url('report/container-tracker') ?>" role="form" method="get">
        <div class="box-body">
            <div class="form-group">
                <label for="container">Container</label>
                <select class="form-control select2 select2-ajax" style="width: 100%"
                        data-url="<?= site_url('container/ajax_get_container_by_no?owner=' . UserModel::authenticatedUserData('person_type')) ?>"
                        data-key-id="id" data-key-label="no_container"
                        id="container" name="container" data-placeholder="Select container" required>
                    <option value=""></option>
                    <?php if(!empty($container)): ?>
                        <option value="<?= $container['id'] ?>" selected>
                            <?= $container['no_container'] ?>
                        </option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="box-footer clearfix">
            <button type="submit" class="btn btn-primary btn-lg btn-block" data-toggle="one-touch">Track Container</button>
        </div>
    </form>
</div>

<?php if(!empty(get_url_param('container'))): ?>
    <?php $this->load->view('report/_tracker_data'); ?>
<?php endif; ?>