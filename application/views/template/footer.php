<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        Version <?= get_setting('app_version') ?>
    </div>
    <!-- Default to the left -->
    <strong>
        Copyright &copy; <?= date('Y') ?>
        <a href="<?= get_setting('meta_url') ?>"><?= $this->config->item('app_name') ?></a>.
    </strong> All rights reserved.
</footer>
<span id="loader"><img id="loader" src="<?= base_url('assets/app/img/layout/loading.gif') ?>" alt="Loading..."><p id="loader">This process will take a few minutes, you can do other things but make sure this page remains open and the handheld is still connected to the internet until the process is complete.</p></span>
<style type="text/css" media="screen">
    span#loader{
        background-color: rgba(255, 255, 255, 0.9);
        position: fixed;
        height: 100%;
        width: 100%;    
        left: 0;
        top: 0;
        z-index: 10000;
        display: none;
    }    
    img#loader{
        text-align: center;
        position: fixed;
        height: 100px;
        left: 50%;
        transform: translateX(-50%);
        top: 30%;
    }
    p#loader{
        text-align: center;
        font-weight: bold;
        position: fixed;
        height: 100px;
        left: 1%;
        right: 1%;
        top: 50%;
        padding: 0 20px;
    }
</style>