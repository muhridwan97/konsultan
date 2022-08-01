<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Synchronize List</h3>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-synchronize">
            <thead>
            <tr>
                <th>No</th>
                <th>No BC 1.5</th>
                <th>BC 1.5 Date</th>
                <th>Customer</th>
                <th>No BC 1.1</th>
                <th>BC 1.1 Date</th>
                <th>Vessel</th>
                <th>Voyage</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($bookings as $booking): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td><?= $booking['no_reference'] ?></td>
                    <td><?= $booking['reference_date'] ?></td>
                    <td><?= $booking['customer_name'] ?></td>
                    <td><?= $booking['no_bc11'] ?></td>
                    <td><?= $booking['bc11_date'] ?></td>
                    <td><?= $booking['vessel'] ?></td>
                    <td><?= $booking['voyage'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <li>
                                    <a href="<?= site_url('synchronize/upstream/' . $booking['id']) ?>"
                                       class="btn-synchronize"
                                       data-id="<?= $booking['id'] ?>"
                                       data-label="<?= $booking['no_reference'] ?>">
                                        <i class="fa fa-share"></i> Synchronize
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-synchronize">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <input type="hidden" name="id" id="id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Synchronize</h4>
                </div>
                <div class="modal-body">
                    <p class="lead" style="margin-bottom: 0">Are you sure want to synchronize data
                        <strong id="synchronize-title"></strong>?
                    </p>
                    <p class="text-danger">
                        Please check your existing data before synchronize
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Synchronize</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/app/js/synchronize.js') ?>" defer></script>