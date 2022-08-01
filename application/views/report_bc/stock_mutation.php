<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Mutation</h3>
        <div class="pull-right">
            <a href="#form-filter-mutation-goods" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report_bc/_filter_mutation', ['hidden' => false]) ?>

        <?php if(!empty($reportMutations)): ?>
            <div class="table-responsive">
                <table class="table table-solid table-bordered table-condensed no-wrap no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 25px" rowspan="2">No</th>
                        <th rowspan="2">Branch</th>
                        <th rowspan="2">No Registration</th>
                        <th rowspan="2">Registration Date</th>
                        <th colspan="4" class="text-center">REF DOCUMENT</th>
                        <th rowspan="2">Owner</th>
                        <th colspan="3" class="text-center">DOCUMENT</th>
                        <th colspan="6" class="text-center">ACTIVITY</th>
                        <th colspan="7" class="text-center">ITEM DETAIL</th>
                        <th colspan="4" class="text-center">MUTATION</th>
                    </tr>
                    <tr>
                        <th>BC Doc No Ref</th>
                        <th>BC Doc Date Ref</th>
                        <th>BC Doc Type Ref</th>
                        <th>DO No</th>
                        <th>BC Doc No</th>
                        <th>BC Doc Date</th>
                        <th>BC Doc Type</th>
                        <th>Handling Type</th>
                        <th>Job No</th>
                        <th>Transaction Date</th>
                        <th>Admin</th>
                        <th>Tally</th>
                        <th>Item Type</th>
                        <th>Item No</th>
                        <th>Item Name</th>
                        <th>Warehouse</th>
                        <th>Position</th>
                        <th>Tonnage (Kg)</th>
                        <th>Volume</th>
                        <th>Unit</th>
                        <th class="text-right">Beginning Balance</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Final Balance</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $no = 1 ?>
                    <?php foreach ($reportMutations as $references): ?>
                        <?php foreach ($references as $dos): ?>
                            <?php foreach ($dos as $items): ?>
                                <?php
                                $stripClass = isset($stripClass) ? $stripClass : '';
                                $border = 'style="border-top: 2px solid #0c0c0c"';
                                ?>
                                <?php foreach ($items as $item): ?>
                                    <tr <?= $border ?> class="<?= $stripClass ?>">
                                        <td><?= $no++ ?></td>
                                        <td><?= if_empty($item['branch'], '-') ?></td>
                                        <td><?= if_empty($item['nopen'], '-') ?></td>
                                        <td><?= if_empty(format_date($item['tapen'], 'd F Y'), '-') ?></td>
                                        <td><?= if_empty($item['bc_doc_/_reference_no_in'], '-') ?></td>
                                        <td><?= readable_date($item['bc_doc_/_reference_date_in'], false) ?></td>
                                        <td><?= if_empty($item['bc_doc_/_booking_type_in'], '-') ?></td>
                                        <td><?= if_empty($item['do_no'], '-', '', '', true) ?></td>
                                        <td><?= if_empty($item['owner'], '-') ?></td>
                                        <td><?= if_empty($item['bc_doc_/_reference_no'], '-') ?></td>
                                        <td><?= readable_date($item['bc_doc_/_reference_date'], false) ?></td>
                                        <td><?= if_empty($item['bc_doc_/_booking_type'], '-') ?></td>
                                        <td><?= if_empty($item['handling_type'], '-') ?></td>
                                        <td><?= if_empty($item['job_no'], '-') ?></td>
                                        <td><?= readable_date($item['transaction_date'], false) ?></td>
                                        <td><?= if_empty($item['admin_name'], '-') ?></td>
                                        <td><?= if_empty($item['tally_name'], '-') ?></td>
                                        <td><?= if_empty($item['item_type'], '-') ?></td>
                                        <td><?= if_empty($item['item_no'], '-') ?></td>
                                        <td><?= if_empty($item['item_name'], '-') ?></td>
                                        <td><?= if_empty($item['warehouse'], '-') ?></td>
                                        <td><?= if_empty($item['position'], '-') ?></td>
                                        <td><?= numerical($item['tonnage']) ?></td>
                                        <td><?= numerical($item['volume']) ?></td>
                                        <td><?= if_empty($item['unit'], '-') ?></td>
                                        <td class="text-right"><?= numerical($item['beginning_balance']) ?></td>
                                        <td class="text-right"><?= if_empty(numerical($item['quantity_debit']), '') ?></td>
                                        <td class="text-right"><?= if_empty(numerical($item['quantity_credit']), '') ?></td>
                                        <td class="text-right"><?= numerical($item['final_balance']) ?></td>
                                    </tr>
                                    <?php $border = '' ?>
                                <?php endforeach ?>
                                <?php $stripClass = ($stripClass == 'active') ? '' : 'active' ?>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if(get_url_param('filter_mutation') && empty($reportMutations)): ?>
    <div class="panel">
        <div class="panel-body">
            <p class="lead mb0">
                No data mutation available
            </p>
        </div>
    </div>
<?php endif; ?>
