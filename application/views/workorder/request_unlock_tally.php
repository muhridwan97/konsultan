<div class="box box-primary">
    <!-- /.box-header -->

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-request">
            <thead>
            <tr>
                <th style="width: 5px">No</th>
                <th style="width: 10%">No Work Order</th>
                <th>Description</th>
                <th style="width: 10%">Date Open</th>
                <th style="width: 10%">Date Close</th>
                <th style="width: 60px">Status</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            // print_debug($requests);
            foreach ($requests as $data): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><a href="<?=site_url()?>work-order/view/<?= $data['id_work_order'] ?>" ><?= $data['no_work_order'] ?></a></td>
                    <td class="responsive-title"><?= $data['description'] ?></td>
                    <td ><?= date("d F Y", strtotime($data['date_from'])) ?></td>
                    <td ><?= date("d F Y", strtotime($data['date_to']))?></td>
                    <td >
                        <?php
                            $dataLabel = [
                                'REJECT' => 'danger',
                                'REQUEST' => 'warning',
                                'APPROVE' => 'success',
                            ];
                            ?>
                            <span class="label label-<?= $dataLabel[$data['status']] ?> mr10">
                            <?= $data['status'] ?>
                        </span>
                        <?php if ($data['is_locked']==1): ?>
                            <span class="label label-default mr10">
                                Lock
                            </span>
                        <?php else : ?>
                            <span class="label label-default mr10">
                                Unlock
                            </span>
                        <?php endif ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right row-request"
                                data-id="<?= $data['id'] ?>"
                                data-id-work-order="<?= $data['id_work_order'] ?>"
                                data-no-work-order="<?= $data['no_work_order'] ?>"
                                data-date-from="<?= date("d F Y", strtotime($data['date_from'])) ?>"
                                data-date-to="<?= date("d F Y", strtotime($data['date_to']))?>"
                                data-description="<?= $data['description'] ?>"
                                data-name="<?= $data['name'] ?>"
                                >
                                <li class="dropdown-header">ACTION</li>
                                    <li>
                                        <a href="<?= site_url()?>/work-order/view/<?= $data['id_work_order'] ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                    
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_LOCK)): ?>
                                        <?php if ($data['status']=='REQUEST'): ?>
                                        <?php if ($data['is_locked']==1): ?>
                                        <li>
                                            <a href="<?=site_url()?>work-order/unlock_by_work_order_id/<?=$data['id_work_order']?>?redirect=work-order/request-unlock-tally" class="unlock-now-request-tally">
                                                <i class="fa fa-unlock"></i>Unlock Now
                                            </a>
                                        </li>
                                        <?php else : ?>
                                        <li>
                                            <a href="<?=site_url()?>work-order/locked_by_work_order_id/<?=$data['id_work_order']?>?redirect=work-order/request-unlock-tally" class="unlock-now-request-tally">
                                                <i class="fa fa-lock"></i>Lock Now
                                            </a>
                                        </li>
                                        <?php endif;?>
                                        <li>
                                            <a href="#" class="approve-request-tally">
                                                <i class="fa fa-check"></i>Approve
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="reject-request-tally">
                                                <i class="fa fa-close"></i>Reject
                                            </a>
                                        </li>
                                        <?php endif;?>
                                    <?php endif;?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/work-order.js?v=5') ?>" defer></script>
<?php $this->load->view('workorder/_modal_approve_unlock_tally') ?>
<?php $this->load->view('workorder/_modal_reject_unlock_tally') ?>