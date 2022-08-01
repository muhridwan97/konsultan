<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit News</h3>
    </div>
    <form action="<?= site_url('news/update/' . $news['id']) ?>" role="form" method="post" enctype="multipart/form-data">
        <?= _method('put') ?>

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('title') == '' ?: 'has-error'; ?>">
                <label for="position">Title</label>
                <input type="text" class="form-control" id="title" name="title"
                       placeholder="News title"
                       required maxlength="200" value="<?= set_value('title', $news['title']) ?>">
                <?= form_error('title', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('content') == '' ?: 'has-error'; ?>">
                <label for="content">Content</label>
                <textarea class="form-control wysiwyg" id="content" name="content" placeholder="Content of the news"
                          required rows="10"><?= set_value('content', $news['content']) ?></textarea>
                <?= form_error('content', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                        <label for="type">Privacy Type</label>
                        <select class="form-control select2" name="type" id="type" data-placeholder="Privacy type" required>
                            <option value=""></option>
                            <option value="PUBLIC" <?= set_select('type', 'PUBLIC', $news['type'] == 'PUBLIC') ?>>PUBLIC (Public client area could access)</option>
                            <option value="EXTERNAL" <?= set_select('type', 'EXTERNAL', $news['type'] == 'EXTERNAL') ?>>EXTERNAL (Registered external only)</option>
                            <option value="INTERNAL" <?= set_select('type', 'INTERNAL', $news['type'] == 'INTERNAL') ?>>INTERNAL (Employee of transcon only)</option>
                        </select>
                        <?= form_error('type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group <?= form_error('is_popup') == '' ?: 'has-error'; ?>">
                        <label for="is_popup" class="control-label">Is Popup</label>
                        <div class="row">
                            <div class="col-sm-5">
                                <label class="radio">
                                    <input type="radio" class="form-control" id="popup_yes" name="is_popup" value="1"
                                        <?= set_radio('is_popup', '1', $news['is_popup'] == 1) ?>> <span>POPUP</span>
                                </label>
                            </div>
                            <div class="col-sm-5">
                                <label class="radio">
                                    <input type="radio" class="form-control" id="popup_no" name="is_popup" value="0"
                                        <?= set_radio('is_popup', '0', $news['is_popup'] == 0) ?>> <span>NORMAL</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                        <label for="sticky_yes" class="control-label">Is Sticky</label>
                        <div class="row">
                            <div class="col-sm-5">
                                <label class="radio">
                                    <input type="radio" class="form-control" id="sticky_yes" name="is_sticky" value="1"
                                        <?= set_radio('is_sticky', '1', $news['is_sticky'] == 1) ?>> <span>STICKY</span>
                                </label>
                            </div>
                            <div class="col-sm-5">
                                <label class="radio">
                                    <input type="radio" class="form-control" id="sticky_no" name="is_sticky" value="0"
                                        <?= set_radio('is_sticky', '0', $news['is_sticky'] == '0') ?>> <span>NORMAL</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group <?= form_error('featured') == '' ?: 'has-error'; ?>">
                        <label for="featured">Featured</label>
                        <p class="form-control-static">
                            <?php if (empty($news['featured'])): ?>
                                No featured
                            <?php else: ?>
                                <a href="<?= base_url('uploads/news/' . $news['featured']) ?>">
                                    <?= $news['featured'] ?>
                                </a>
                            <?php endif; ?>
                        </p>
                        <input type="file" name="featured" id="featured"
                               accept="application/msword, application/vnd.ms-excel, application/pdf, image/*, application/zip, .rar"
                               placeholder="News featured document">
                        <?= form_error('featured', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('expired_date') == '' ?: 'has-error'; ?>">
                <label for="expired_date">Expired Date</label>
                <input class="form-control daterangepicker2" id="expired_date" name="expired_date"
                       placeholder="Expired date" required
                       value="<?= set_value('expired_date', (new DateTime($news['expired_date']))->format('d F Y H:i')) ?>">
                <?= form_error('expired_date', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Additional information" maxlength="500"><?= set_value('description', $news['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-success pull-right">
                Update News
            </button>
        </div>
    </form>
</div>

<!-- Bootstrap WYSIHTML5 -->
<link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') ?>">
<script src="<?= base_url('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') ?>"></script>
<script>
    if ($('.wysiwyg').length) {
        $('.wysiwyg').wysihtml5();
    }
</script>