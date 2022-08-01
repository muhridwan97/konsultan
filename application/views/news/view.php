<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View News</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->

    <div class="box-body">
        <?php if(!empty($news['featured'])): ?>
            <a href="<?= base_url('uploads/news/' . $news['featured']) ?>"
               style="height: 200px; display: block; background: url('<?= base_url('uploads/news/' . $news['featured']) ?>') center center / cover">
            </a>
        <?php endif; ?>
        <h3 class="mt10"><?= $news['title'] ?></h3>
        <p class="text-muted">Posted by <?= $news['author_name'] ?> At <?= readable_date($news['created_at']) ?></p>
        <article class="mb20">
            <?= $news['content'] ?>

            <?php if(!empty($news['description'])): ?>
                <blockquote class="mt20">
                    <?= $news['description'] ?>
                </blockquote>
            <?php endif; ?>
        </article>
    </div>
</div>
<!-- /.box -->