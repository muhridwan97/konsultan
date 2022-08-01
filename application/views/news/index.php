<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">News</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_NEWS_CREATE)): ?>
                <a href="<?= site_url('news/create') ?>" class="btn btn-primary">
                    Create News
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-news">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Title</th>
                <th>Content</th>
                <th>Type</th>
                <th>Is Sticky</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-news-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_NEWS_VIEW)): ?>
                <li>
                    <a href="<?= site_url('news/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_NEWS_EDIT)): ?>
                <li>
                    <a href="<?= site_url('news/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_NEWS_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('news/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Auction"
                       data-label="{{title}}">
                        <i class="fa ion-trash-a"></i>Delete
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_NEWS_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/news.js') ?>" defer></script>