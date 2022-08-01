<?php if ($this->session->flashdata('status') != NULL): ?>
    <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p><?= $this->session->flashdata('message'); ?></p>
    </div>
<?php endif; ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Help and Support</h3>
    </div>
    <div class="box-body">

        <div class="row">
            <div class="col-md-3 help-list-menu">
                <ul>
                    <li><a href="<?= site_url('help') ?>">Index Page</a></li>
                    <li>
                        <a href="<?= site_url('help?page=master') ?>">Master</a>
                        <ul>
                            <li><a href="<?= site_url('help?page=master&section=role') ?>">Roles</a></li>
                            <li><a href="<?= site_url('help?page=master&section=user') ?>">Users</a></li>
                            <li><a href="<?= site_url('help?page=master&section=people') ?>">People</a></li>
                            <li><a href="<?= site_url('help?page=master&section=branch') ?>">Branches</a></li>
                            <li><a href="<?= site_url('help?page=master&section=warehouse') ?>">Warehouses</a></li>
                            <li><a href="<?= site_url('help?page=master&section=position') ?>">Positions</a></li>
                            <li><a href="<?= site_url('help?page=master&section=vehicle') ?>">Vehicles</a></li>
                            <li><a href="<?= site_url('help?page=master&section=eseal') ?>">Eseal</a></li>
                            <li><a href="<?= site_url('help?page=master&section=container') ?>">Containers</a></li>
                            <li><a href="<?= site_url('help?page=master&section=goods') ?>">Goods</a></li>
                            <li><a href="<?= site_url('help?page=master&section=unit') ?>">Units</a></li>
                            <li><a href="<?= site_url('help?page=master&section=component') ?>">Components</a></li>
                            <li><a href="<?= site_url('help?page=master&section=handling-type') ?>">Handling Types</a></li>
                            <li><a href="<?= site_url('help?page=master&section=document-type') ?>">Document Types</a></li>
                            <li><a href="<?= site_url('help?page=master&section=extension-field') ?>">Extensions Field</a></li>
                            <li><a href="<?= site_url('help?page=master&section=booking-type') ?>">Booking Type</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= site_url('help?page=inbound') ?>">Inbound</a>
                        <ul>
                            <li><a href="<?= site_url('help?page=inbound&section=upload') ?>">Upload</a></li>
                            <li><a href="<?= site_url('help?page=inbound&section=booking') ?>">Booking</a></li>
                            <li><a href="<?= site_url('help?page=inbound&section=payment') ?>">Payment</a></li>
                            <li><a href="<?= site_url('help?page=inbound&section=safe-conduct') ?>">Safe Conduct</a></li>
                            <li><a href="<?= site_url('help?page=inbound&section=security') ?>">Security</a></li>
                            <li><a href="<?= site_url('help?page=inbound&section=gate-in') ?>">Gate In</a></li>
                            <li><a href="<?= site_url('help?page=inbound&section=tally') ?>">Tally</a></li>
                            <li><a href="<?= site_url('help?page=inbound&section=gate-out') ?>">Gate Out</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= site_url('help?page=handling') ?>">Handling</a>
                        <ul>
                            <li><a href="<?= site_url('help?page=handling&section=handling') ?>">Order Handling</a></li>
                            <li><a href="<?= site_url('help?page=handling&section=shifting') ?>">Shifting</a></li>
                            <li><a href="<?= site_url('help?page=handling&section=readdress') ?>">Readdress</a></li>
                            <li><a href="<?= site_url('help?page=handling&section=danger-replacement') ?>">Danger Replacement</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= site_url('help?page=outbound') ?>">Outbound</a>
                        <ul>
                            <li><a href="<?= site_url('help?page=outbound&section=container') ?>">Full Container</a></li>
                            <li><a href="<?= site_url('help?page=outbound&section=lcl') ?>">LCL</a></li>
                            <li><a href="<?= site_url('help?page=outbound&section=tegahan') ?>">Tegahan</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= site_url('help?page=report') ?>">Report</a>
                        <ul>
                            <li><a href="<?= site_url('help?page=report&section=inbound') ?>">Inbound</a></li>
                            <li><a href="<?= site_url('help?page=report&section=outbound') ?>">Outbound</a></li>
                            <li><a href="<?= site_url('help?page=report&section=plan-realization') ?>">Plan & Realization</a></li>
                            <li><a href="<?= site_url('help?page=report&section=booking-summary') ?>">Booking Summary</a></li>
                            <li><a href="<?= site_url('help?page=report&section=container-tracker') ?>">Container Tracker</a></li>
                            <li><a href="<?= site_url('help?page=report&section=handling-activity') ?>">Handling Activity</a></li>
                            <li><a href="<?= site_url('help?page=report&section=service-time') ?>">Service Time</a></li>
                            <li><a href="<?= site_url('help?page=report&section=invoice-summary') ?>">Invoice Summary</a></li>
                            <li><a href="<?= site_url('help?page=report&section=stock-summary') ?>">Stock Summary</a></li>
                            <li><a href="<?= site_url('help?page=report&section=mutation-container') ?>">Stock Mutation Container</a></li>
                            <li><a href="<?= site_url('help?page=report&section=mutation-goods') ?>">Stock Mutation Goods</a></li>
                            <li><a href="<?= site_url('help?page=report&section=stock-aging') ?>">Stock Aging</a></li>
                        </ul>
                    </li>
                    <li><a href="<?= site_url('faq') ?>">FAQ</a></li>
                    <li><a href="<?= site_url('troubleshooting') ?>">Troubleshooting</a></li>
                    <li><a href="<?= site_url('support') ?>">Support</a></li>
                    <li><a href="<?= site_url('agreement') ?>">Agreement</a></li>
                    <li><a href="<?= site_url('privacy') ?>">Privacy</a></li>
                </ul>
            </div>
            <div class="col-md-9 help-section">
                <?php $this->load->view($help) ?>
            </div>
        </div>
    </div>
</div>
