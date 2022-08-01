<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="<?= site_url() ?>" class="logo">
        <span class="logo-mini">WH</span>
        <span class="logo-lg"><?= $this->config->item('app_name') ?></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= base_url('assets/app/img/avatar/no-avatar.jpg') ?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?= UserModel::authenticatedUserData('name') ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="<?= base_url('assets/app/img/avatar/no-avatar.jpg') ?>" class="img-circle" alt="User Image">

                            <p>
                                <?= UserModel::authenticatedUserData('name') ?>
                                <small><?= UserModel::authenticatedUserData('email') ?></small>
                            </p>
                        </li>
                        <?php if(AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CHECK)): ?>
                            <li class="user-body" id="list-switch-branch">
                                <a href="<?= site_url('gateway', false) ?>" class="text-center" style="display: block; color: #a94442 !important;">
                                    <strong>SWITCH BRANCH</strong>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php $userBranches = get_user_branch(); ?>
                        <?php if (!empty($userBranches) && $this->config->item('enable_branch_mode')) : ?>
                            <?php $no = 1; foreach ($userBranches as $userBranch) : ?>
                                <li class="user-body" id="list-branch-<?= $no++ ?>">
                                    <div class="row">
                                        <div class="col-xs-12 text-center">
                                            <?php $uriString = preg_replace("/p\/[0-9]+\//", '', uri_string()) ?>
                                            <a href="<?= site_url('p/' . $userBranch['id'] . '/', false) . $uriString . '?' . $_SERVER['QUERY_STRING'] ?>"
                                               style="display: block">
                                                <?= $userBranch['branch'] ?>
                                                <?= $userBranch['id'] == get_active_branch('id') ? '(current)' : '' ?>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li class="user-footer">
                            <?php if($this->config->item('sso_enable')): ?>
                                <div class="pull-left">
                                    <a href="<?= sso_url('app') ?>" class="btn btn-default btn-flat">Switch App</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?= sso_url('auth/logout') ?>" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            <?php else: ?>
                                <div class="pull-left">
                                    <a href="<?= site_url('account') ?>" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?= site_url('account/logout', false) ?>" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            <?php endif; ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
