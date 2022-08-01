<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">News & Updates</h3>
            </div>
            <div class="box-body">
                <ul class="products-list product-list-in-box" style="padding: 10px 20px">
                    <?php foreach ($news as $post): ?>
                        <li class="item">
                            <div class="product-img">
                                <?php $featuredSrc = empty($post['featured']) ? 'assets/app/img/layout/no-image.png' : 'uploads/news/' . $post['featured'] ?>
                                <img src="<?= base_url($featuredSrc) ?>" alt="<?= $post['title'] ?> featured" style="width: 90px; height: 70px">
                            </div>
                            <div class="product-info" style="margin-left: 110px">
                                <h4 class="mt0 mb0">
                                    <a href="<?= site_url('client_area/news_view/' . $post['id']) ?>" class="product-title"><?= $post['title'] ?></a>
                                    <?php if($post['is_sticky']): ?>
                                        <span class="small label label-danger pull-right">STICKY</span>
                                    <?php endif; ?>
                                </h4>
                                <span class="product-description mb0">Published at <?= $post['created_at'] ?> by <?= $post['author_name'] ?></span>
                                <article><?= substr(strip_tags($post['content']), 0, 150) ?>...</article>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php if(empty($news)): ?>
                        <p>No any news was published</p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>