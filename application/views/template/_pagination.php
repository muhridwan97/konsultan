<?php if (isset($pagination)): ?>
    <div class="mt20 clearfix">
        <p class="text-muted mb10 pull-left">Total result <?= $pagination['total_data'] ?> items</p>

        <nav aria-label="Page navigation" class="pull-right">
            <ul class="pagination pagination-sm pagination-flat mb0 mt0">
                <li class="page-item<?= $pagination['current_page'] == 1 ? ' disabled' : '' ?>">
                    <a class="page-link"
                       href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => $pagination['current_page'] - 1]) ?>"
                       aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>

                <?php for ($i = 1; $i <= ($pagination['current_page'] >= 8 ? 1 : ($pagination['total_page'] > 8 ? 8 : $pagination['total_page'])); $i++): ?>
                    <li class="page-item<?= $i == $pagination['current_page'] ? ' active' : '' ?>">
                        <a class="page-link"
                           href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => $i]) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] >= 8 && $pagination['current_page'] <= $pagination['total_page'] - 8): ?>
                    <li class="page-item disabled">
                        <a class="page-link disabled" href="#">
                            ...
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link"
                           href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => $pagination['current_page'] - 1]) ?>">
                            <?= $pagination['current_page'] - 1 ?>
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link"
                           href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => $pagination['current_page']]) ?>">
                            <?= $pagination['current_page'] ?>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link"
                           href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => $pagination['current_page'] + 1]) ?>">
                            <?= $pagination['current_page'] + 1 ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($pagination['total_page'] >= 8): ?>
                    <?php $lastOrderPage = $pagination['total_page'] - 8; ?>
                    <?php if ($pagination['current_page'] > $lastOrderPage): ?>

                        <?php if ($pagination['current_page'] == 8): ?>
                            <li class="page-item disabled">
                                <a class="page-link disabled" href="#">
                                    ...
                                </a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link"
                                   href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => 8]) ?>">
                                    8
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link disabled" href="#">
                                    ...
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = ($lastOrderPage < 8 ? 9 : $lastOrderPage); $i <= $pagination['total_page']; $i++): ?>
                            <li class="page-item<?= $pagination['current_page'] == $i ? ' active' : '' ?>">
                                <a class="page-link"
                                   href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => $i]) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link disabled" href="#">
                                ...
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link"
                               href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => $pagination['total_page']]) ?>">
                                <?= $pagination['total_page'] ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <li class="page-item<?= $pagination['current_page'] >= $pagination['total_page'] ? ' disabled' : '' ?>">
                    <a class="page-link"
                       href="<?= site_url(uri_string(), false) . '?' . set_url_param(['page' => $pagination['current_page'] + 1]) ?>"
                       aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>