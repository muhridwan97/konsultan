<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Security Check Photo</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Category</th>
                <th>Total Start Photo</th>
                <th>Total Stop Photo</th>
                <th style="width: 50px">Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td><?= $inbound['category'] ?? 'INBOUND' ?></td>
                <td><?= $inbound['total_start'] ?? 0 ?></td>
                <td><?= $inbound['total_stop'] ?? 0 ?></td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-header">ACTION</li>
                            <li>
                                <a href="<?= site_url('security-photo-type/view/INBOUND') ?>">
                                    <i class="fa ion-search"></i>View
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('security-photo-type/edit/INBOUND') ?>">
                                    <i class="fa ion-compose"></i>Edit
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td><?= $outbound['category'] ?? 'OUTBOUND' ?></td>
                <td><?= $outbound['total_start'] ?? 0 ?></td>
                <td><?= $outbound['total_stop'] ?? 0 ?></td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-header">ACTION</li>
                            <li>
                                <a href="<?= site_url('security-photo-type/view/OUTBOUND') ?>">
                                    <i class="fa ion-search"></i>View
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('security-photo-type/edit/OUTBOUND') ?>">
                                    <i class="fa ion-compose"></i>Edit
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td><?= $emptyContainer['category'] ?? 'EMPTY CONTAINER' ?></td>
                <td><?= $emptyContainer['total_start'] ?? 0 ?></td>
                <td><?= $emptyContainer['total_stop'] ?? 0 ?></td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-header">ACTION</li>
                            <li>
                                <a href="<?= site_url('security-photo-type/view/EMPTY CONTAINER') ?>">
                                    <i class="fa ion-search"></i>View
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('security-photo-type/edit/EMPTY CONTAINER') ?>">
                                    <i class="fa ion-compose"></i>Edit
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>