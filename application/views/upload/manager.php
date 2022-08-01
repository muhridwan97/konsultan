<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Upload Manager</h3>
        <a class="btn btn-primary btn-sm pull-right"
           href="<?= site_url('file_manager/download?path=' . $path) ?>">
            Download as Zip
        </a>
    </div>
    <!-- /.box-header -->

    <div class="box-body">

        <?php if ($this->session->flashdata('status') != NULL): ?>
            <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p><?= $this->session->flashdata('message'); ?></p>
            </div>
        <?php endif ?>

        <div class="list-group">
            <div href="#" class="list-group-item disabled">
                <strong>Location</strong> <?= $path ?>
            </div>
            <?php if ($parent != './'): ?>
                <a href="<?= site_url('file_manager/index') ?>" class="list-group-item">
                    ./root/
                </a>
                <a href="<?= site_url('file_manager/index?path=' . $parent) ?>" class="list-group-item">
                    <i class="fa fa-level-up"></i> &nbsp; <?= $parent ?>
                </a>
            <?php endif ?>

            <?php foreach ($files as $index => $file): ?>
                <?php if (is_dir($path . $file)): ?>
                    <a href="<?= is_readable($path . $file) ? site_url('file_manager/index?path=' . $path . $file) : '#' ?>" class="list-group-item">
                        <i class="fa fa-folder-o"></i>
                        &nbsp; <strong><?= $file ?></strong>
                        <span class="pull-right">
                            <?php if(is_readable($path . $file)): ?>
                                <?= count(directory_map($path . $file, 1)) ?> files
                            <?php else: ?>
                                Unauthorized
                            <?php endif ?>
                        </span>
                    </a>
                <?php else: ?>
                    <a href="<?= base_url($path . $file) ?>" class="list-group-item">
                        <?php
                        $file_parts = pathinfo($path . $file);
                        $iconFile = 'fa-file-o';
                        switch(strtolower(key_exists('extension' , $file_parts) ? $file_parts['extension'] : ''))
                        {
                            case "jpg":
                            case "jpeg":
                            case "png":
                            case "gif":
                                $iconFile = 'fa-file-image-o';
                                break;
                            case "pdf":
                                $iconFile = 'fa-file-pdf-o';
                                break;
                            case "zip":
                            case "rar":
                            case "":
                            case NULL:
                                $iconFile = 'fa-file-archive-o';
                                break;
                        }
                        ?>
                        <i class="fa <?= $iconFile ?>"></i>
                        &nbsp; <?= $file ?>
                        <span class="pull-right">
                            <?= round(filesize($path . $file) / 1000) ?> Kb |
                            <?= date("d M Y H:i", filemtime($path . $file)) ?>
                        </span>
                    </a>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
    <!-- /.box -->