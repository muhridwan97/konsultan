<form role="form" class="form-horizontal form-view">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3">No Auction</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $auction['no_auction'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">No Doc</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $auction['no_doc'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Doc Date</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($auction['doc_date'], false) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Auction Date</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($auction['auction_date'], false) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3">Status</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php
                        $statuses = [
                            'PENDING' => 'default',
                            'APPROVED' => 'success',
                            'REJECTED' => 'danger',
                        ]
                        ?>
                        <span class="label label-<?= $statuses[$auction['status']] ?>">
                                    <?= $auction['status'] ?>
                                </span>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($auction['description'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($auction['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($auction['updated_at']) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Booking Auction Detail</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped no-datatable">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>No Booking</th>
                <th>No Reference</th>
                <th>Booking Customer</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($auctionDetails as $auctionDetail): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $auctionDetail['no_booking'] ?></td>
                    <td><?= $auctionDetail['no_reference'] ?></td>
                    <td><?= $auctionDetail['customer_name'] ?></td>
                    <td><?= if_empty($auctionDetail['description'], '-') ?></td>
                </tr>
                <?php
                $containerGoodsExist = key_exists('goods', $auctionDetail) && !empty($auctionDetail['goods']);
                $containerContainersExist = key_exists('containers', $auctionDetail) && !empty($auctionDetail['containers']);
                ?>
                <?php if ($containerGoodsExist || $containerContainersExist): ?>
                    <tr>
                        <td></td>
                        <td colspan="4">
                            <?php if ($containerContainersExist): ?>
                                <table class="table table-condensed table-striped no-datatable">
                                    <thead>
                                    <tr>
                                        <th style="width: 25px">No</th>
                                        <th>No Container</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Seal</th>
                                        <th>Inbound Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $innerNo = 1; ?>
                                    <?php foreach ($auctionDetail['containers'] as $containerItem): ?>
                                        <tr>
                                            <td><?= $innerNo++ ?></td>
                                            <td><?= $containerItem['no_container'] ?></td>
                                            <td><?= $containerItem['type'] ?></td>
                                            <td><?= $containerItem['size'] ?></td>
                                            <td><?= $containerItem['seal'] ?></td>
                                            <td><?= format_date($containerItem['completed_at'], 'd F Y H:i') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                            <?php if ($containerGoodsExist): ?>
                                <table class="table table-condensed table-striped no-datatable">
                                    <thead>
                                    <tr>
                                        <th style="width: 25px">No</th>
                                        <th>Goods</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Weight (Kg)</th>
                                        <th>Gross Weight (Kg)</th>
                                        <th>Volume (M<sup>3</sup>)</th>
                                        <th>Inbound Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $innerNo = 1; ?>
                                    <?php foreach ($auctionDetail['goods'] as $item): ?>
                                        <tr>
                                            <td><?= $innerNo++ ?></td>
                                            <td><?= $item['goods_name'] ?></td>
                                            <td><?= numerical($item['quantity'], 3, true) ?></td>
                                            <td><?= $item['unit'] ?></td>
                                            <td><?= numerical($item['total_weight'], 3, true) ?></td>
                                            <td><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                                            <td><?= numerical($item['total_volume']) ?></td>
                                            <td><?= readable_date($item['completed_at']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>