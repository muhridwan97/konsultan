<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $this->config->item('app_name') ?> | Plan Realization</title>
    <style>
        @page { margin: 30px; }

        * {
            margin: 0;
            padding: 0;
        }

        body { font-size: 10px; font-family: sans-serif; margin: 20px; }

        p {
            line-height: 1.4;
        }

        table td, table th {
            padding: 5px 7px;
        }

        .table {
            border: 1px solid #777;
        }
        .table td, .table th {
            border-top: 1px solid #777;
            border-bottom: 1px solid #777;
        }
        .table.table-border-all th,
        .table.table-border-all td {
            border: 1px solid #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: #212529;
        }
        table table {
            font-size: 10px;
            margin: -1px 5px 10px;
        }
        .pt-md-0 {
            padding-top: 0;
        }
        .table-warning {
            background-color: #ffe9b8;
        }
        .table-danger {
            background-color: #f8cfce;
        }
        .table-error,
        .table-error td {
            background-color: #ffb3b2 !important;
        }
        .table-success {
            background-color: #daf9b8;
        }
        .table-secondary {
            background-color: #f8f8f8;
        }
        .label {
            display: inline-block;
            padding: .2em .6em .3em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25em;
        }
        .label-danger {
            background-color: #dd4b39 !important;
        }
        .label-success {
            background-color: #00a65a !important;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .pull-left {
            float: left;
        }
        .pull-right {
            float: right;
        }
        .clearfix {
            clear: both;
        }
        hr {
            height:0;
            border: 0;
            border-top: 1px solid #dddddd;
            -webkit-box-sizing:content-box;
            -moz-box-sizing:content-box;
            box-sizing:content-box;
        }
    </style>
</head>
<body>
<div style="margin-bottom: 15px; margin-left: 80px">
    <img src="<?= FCPATH . 'assets/app/img/layout/header-tci-iso-aeo.png' ?>" style="width: 570px">
</div>

<div class="row">
    <div class="col-md-6" style="float: right;">
        <table style="width: 40%" >
            <tbody>
            <tr>
                <th style="width: 80px">OPS</th>
                <td><?= (count($attendances)+count($overtimeFix)) ?> orang</td>
            </tr>
            <tr>
                <th>Buruh</th>
                <td><?= count($attendanceLabors) ?> orang</td>
            </tr>
            <tr>
                <th>HE</th>
                <td><?= implode(", ", $heavyName) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <table style="width: 40%">
            <tbody>
            <tr>
                <th style="width: 80px">Date From</th>
                <td><?= format_date($dateFrom, 'd F Y H:i') ?></td>
            </tr>
            <tr>
                <th>Date To</th>
                <td><?= format_date($dateTo, 'd F Y H:i') ?></td>
            </tr>
            <tr>
                <th>Branch</th>
                <td><?= $branch ?> - <?= strtoupper($shift) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<br>
<table class="table table-border-all">
    <thead>
    <tr class="text-center">
        <th rowspan="2">No</th>
        <th rowspan="2">Customer</th>
        <th colspan="2">Inbound</th>
        <th rowspan="2">Outbound<br>(FRT)</th>
        <th colspan="2">Inbound</th>
        <th rowspan="2">Outbound<br>(Fleet)</th>
        <th rowspan="2">Description Type</th>
    </tr>
    <tr class="text-center">
        <th>FCL<br>(FRT)</th>
        <th>LCL<br>(FRT)</th>
        <th>FCL<br>(Box)</th>
        <th>LCL<br>(Fleet)</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($reports as $index => $report): ?>
        <tr>
            <td class="text-center"><?= $index + 1 ?></td>
            <td><?= str_replace(',', '<br>', $report['customer_name']) ?></td>
            <td class="text-center"><?= $report['inbound_fcl_box_frt'] ?></td>
            <td class="text-center"><?= $report['inbound_lcl_vehicle_frt'] ?></td>
            <td class="text-center"><?= $report['outbound_lcl_vehicle_frt'] ?></td>
            <td class="text-center"><?= $report['inbound_fcl_box'] ?></td>
            <td class="text-center"><?= $report['inbound_lcl_vehicle'] ?></td>
            <td class="text-center"><?= $report['outbound_lcl_vehicle'] ?></td>
            <td><?= $report['description'] ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <th colspan="2" rowspan="2">Total Production</th>
        <th class="text-center"><?= array_sum(array_column($reports, 'inbound_fcl_box_frt')) ?></th>
        <th class="text-center"><?= array_sum(array_column($reports, 'inbound_lcl_vehicle_frt')) ?></th>
        <th class="text-center"><?= array_sum(array_column($reports, 'outbound_lcl_vehicle_frt')) ?></th>
        <th class="text-center"><?= array_sum(array_column($reports, 'inbound_fcl_box')) ?></th>
        <th class="text-center"><?= array_sum(array_column($reports, 'inbound_lcl_vehicle')) ?></th>
        <th class="text-center"><?= array_sum(array_column($reports, 'outbound_lcl_vehicle')) ?></th>
        <th class="text-center" rowspan="2"></th>
    </tr>
    <tr>
        <th class="text-center" colspan="3"><?= array_sum(array_column($reports, 'total_frt')) ?></th>
        <th class="text-center" colspan="3"><?= array_sum(array_column($reports, 'total')) ?></th>
    </tr>
    </tbody>
</table>
<br>
<table class="table table-border-all" style="width: 40%">
    <thead>
        <tr class="text-center">
            <th rowspan="2">No</th>
            <th rowspan="2">Production</th>
            <th rowspan="2">FRT</th>
            <th rowspan="2">Fleet</th>
        </tr>
        <tr></tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">1</td>
            <td>Staff</td>
            <td class="text-center"><?= round(@(array_sum(array_column($reports, 'total_frt'))/(count($attendances)+count($overtimeFix))), 2) ?></td>
            <td class="text-center"><?= round(@(array_sum(array_column($reports, 'total'))/(count($attendances)+count($overtimeFix))), 2) ?></td>
        </tr>
        <tr>
            <td class="text-center">2</td>
            <td>MHE</td>
            <td class="text-center"><?= round(@(array_sum(array_column($reports, 'total_frt'))/count($heavyName)), 2) ?></td>
            <td class="text-center"><?= round(@(array_sum(array_column($reports, 'total'))/count($heavyName)), 2) ?></td>
        </tr>
        <tr>
            <td class="text-center">3</td>
            <td>Buruh</td>
            <td class="text-center"><?= round(@(array_sum(array_column($reports, 'total_frt'))/count($attendanceLabors)), 2) ?></td>
            <td class="text-center"><?= round(@(array_sum(array_column($reports, 'total'))/count($attendanceLabors)), 2) ?></td>
        </tr>
    </tbody>
</table>
<br>
<p style="font-size: 8px;">Catatan :</p>
<p style="font-size: 8px;">1. Inbound FCL diambil dari job stripping yang sudah complete.</p>
<p style="font-size: 8px;">2. Inbound LCL diambil dari safeconduct inbound yang sudah security out.</p>
<p style="font-size: 8px;">3. Outbound diambil dari safeconduct outbound yang sudah security out.</p>
<p style="font-size: 8px;">4. Production Staff = Total Production / OPS</p>
<p style="font-size: 8px;">5. Production MHE = Total Production / HE</p>
<p style="font-size: 8px;">6. Production Buruh = Total Production / Buruh</p>
<div style="page-break-after:always"></div>
<h2>Attendance</h2>
<table class="table table-border-all">
    <thead>
        <tr class="text-center">
            <th rowspan="2">No</th>
            <th rowspan="2">Name</th>
            <th rowspan="2">Position</th>
            <th rowspan="2">Start</th>
            <th rowspan="2">End</th>
            <th rowspan="2">Check In</th>
            <th rowspan="2">Check Out</th>
        </tr>
        <tr></tr>
    </thead>
    <tbody>
        <?php foreach ($attendances as $key => $attendance) :?>
        <tr>
            <td class="text-center"><?= $key+1 ?></td>
            <td><?= $attendance['employee_name'] ?></td>
            <td><?= $attendance['position'] ?></td>
            <td><?= format_date($attendance['on_duty'], 'd/m/Y H:i') ?></td>
            <td><?= format_date($attendance['off_duty'], 'd/m/Y H:i') ?></td>
            <td><?= format_date($attendance['check_in'], 'd/m/Y H:i') ?></td>
            <td><?= !empty($attendance['check_out']) ? format_date($attendance['check_out'], 'd/m/Y H:i') : '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <?php if(empty($attendances)): ?>
        <tr>
            <td class="text-center" colspan="7">No data</td>
        </tr>
    <?php endif; ?>
</table>
<br>
<h2>Overtime</h2>
<table class="table table-border-all">
    <thead>
        <tr class="text-center">
            <th rowspan="2">No</th>
            <th rowspan="2">Name</th>
            <th rowspan="2">Position</th>
            <th rowspan="2">Start</th>
            <th rowspan="2">End</th>
            <th rowspan="2">Check In</th>
            <th rowspan="2">Check Out</th>
        </tr>
        <tr></tr>
    </thead>
    <tbody>
        <?php foreach ($overtimeFix as $key => $overtime) :?>
        <tr>
            <td class="text-center"><?= $key+1 ?></td>
            <td><?= $overtime['employee_name'] ?></td>
            <td><?= $overtime['position'] ?></td>
            <td><?= format_date($overtime['overtime_start'], 'd/m/Y H:i') ?></td>
            <td><?= format_date($overtime['overtime_end'], 'd/m/Y H:i') ?></td>
            <td><?= !empty($overtime['check_in']) ? format_date($overtime['check_in'], 'd/m/Y H:i') : '-' ?></td>
            <td><?= !empty($overtime['check_out']) ? format_date($overtime['check_out'], 'd/m/Y H:i') : '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <?php if(empty($overtimeFix)): ?>
        <tr>
            <td class="text-center" colspan="7">No data</td>
        </tr>
    <?php endif; ?>
</table>
<br>
<h2>Attendance Labor</h2>
<table class="table table-border-all">
    <thead>
        <tr class="text-center">
            <th rowspan="2">No</th>
            <th rowspan="2">Name</th>
            <th rowspan="2">Start</th>
            <th rowspan="2">End</th>
            <th rowspan="2">Check In</th>
            <th rowspan="2">Check Out</th>
        </tr>
        <tr></tr>
    </thead>
    <tbody>
        <?php foreach ($attendanceLabors as $key => $attendance) :?>
        <tr>
            <td class="text-center"><?= $key+1 ?></td>
            <td><?= $attendance['non_employee_name'] ?></td>
            <td><?= format_date($attendance['on_duty'], 'd/m/Y H:i') ?></td>
            <td><?= format_date($attendance['off_duty'], 'd/m/Y H:i') ?></td>
            <td><?= format_date($attendance['check_in'], 'd/m/Y H:i') ?></td>
            <td><?= !empty($attendance['check_out']) ? format_date($attendance['check_out'], 'd/m/Y H:i') : '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <?php if(empty($attendanceLabors)): ?>
        <tr>
            <td class="text-center" colspan="6">No data</td>
        </tr>
    <?php endif; ?>
</table>
</body>
</html>

