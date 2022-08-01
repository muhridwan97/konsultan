<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View <?= $auction['no_auction'] ?></h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_EDIT) && $auction['status'] != AuctionModel::STATUS_APPROVED): ?>
            <a href="<?= site_url('auction/edit/' . $auction['id']) ?>" class="btn btn-primary pull-right">
                Edit Auction
            </a>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <?php $this->load->view('auction/_view') ?>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <a href="<?= site_url('auction/print_auction/' . $auction['id']) ?>" class="btn btn-primary pull-right">
            Print
        </a>
    </div>
</div>