<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $this->config->item('app_name') ?> | Performance Outbound</title>
    <style>
        @page { margin: 30px; }

        * {
            margin: 0;
            padding: 0;
        }

        body { font-size: 11px; font-family: sans-serif; margin: 30px; }

        p {
            line-height: 1.4;
        }

        small {
            font-size: 9px;
        }

        table td, table th {
            padding: 4px 2px;
            text-align: left;
        }

        .table {
            border: 1px solid #dddddd;
        }
        .table td, .table th {
            border-top: 1px solid #dddddd;
            border-bottom: 1px solid #dddddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: #212529;
            font-size: 10px;
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
        .table-secondary {
            background-color: #f8f8f8;
        }
        .text-muted {
            color: #999999;
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
<div style="text-align: center; margin-bottom: 10px">
    <img src="<?= FCPATH . 'assets/app/img/layout/kop_email.jpg' ?>" style="width: 550px; height: 75px">
</div>

<hr>

<br>
<h2 class="font-weight-bold" style="color: #002060; margin-bottom: 10px">COMPLETED OUTBOUND</h2>
<table class="table">
    <thead>
    <tr style="background-color: #002060; color: white">
        <th style="width: 190px; white-space: nowrap">Nomor Ex BC 16</th>
        <th style="width: 190px; white-space: nowrap">Nomor BC 28</th>
        <th style="width: 80px; white-space: nowrap">SPPB Date</th>
        <th style="width: 110px; white-space: nowrap">Invoice Number</th>
        <th style="width: 90px">Service Time (BC28 - OUT PLB)</th>
        <th style="width: 60px; text-align: center">Status</th>
        <th style="width: 70px; white-space: nowrap; text-align: center">On Target</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($reportCompleted as $index => $report): ?>
        <tr<?= ($index + 1) % 2 == 0 ? ' style="background-color: #f7f7f7"' : '' ?>>
            <td><?= $report['no_reference_in'] ?></td>
            <td><?= $report['no_reference_out'] ?></td>
            <td><?= $report['sppb_date'] ?></td>
            <td><?= $report['no_invoice'] ?></td>
            <td><?= $report['st_sppb_gate_out'] ?> Days</td>
            <td style="text-align: center"><?= if_empty($report['outbound_status'], '-')  ?></td>
            <td style="text-align: center"><?= $report['on_target_sppb_gate_out'] ? 'V' : '-'  ?></td>
            <td><?= if_empty($report['description'], '-')  ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr style="background-color: #FFDDDD; color: maroon">
        <th colspan="4">AVERAGE SERVICE TIME</th>
        <th><?= numerical(array_sum(array_column($reportCompleted, 'st_sppb_gate_out')) / if_empty(count($reportCompleted), 1)) ?> Days</th>
        <th colspan="3"></th>
    </tr>
    </tfoot>
</table>

<span style="page-break-after: always"></span>
<h2 class="font-weight-bold" style="color: maroon; margin-bottom: 10px">OUTSTANDING OUTBOUND</h2>
<table class="table">
    <thead>
    <tr style="background-color: maroon; color: white">
        <th style="width: 190px; white-space: nowrap">Nomor Ex BC 16</th>
        <th style="width: 190px; white-space: nowrap">Nomor BC 28</th>
        <th style="width: 80px; white-space: nowrap">SPPB Date</th>
        <th style="width: 110px; white-space: nowrap">Invoice Number</th>
        <th style="width: 90px">Service Time (BC28 - OUT PLB)</th>
        <th style="width: 60px; text-align: center">Status</th>
        <th style="width: 70px; white-space: nowrap; text-align: center"></th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($reportOutstanding as $index => $report): ?>
        <tr<?= ($index + 1) % 2 == 0 ? ' style="background-color: #FFDDDD"' : '' ?>>
            <td><?= $report['no_reference_in'] ?></td>
            <td><?= $report['no_reference_out'] ?></td>
            <td><?= $report['sppb_date'] ?></td>
            <td><?= $report['no_invoice'] ?></td>
            <td>- Days</td>
            <td style="text-align: center"><?= if_empty($report['outbound_status'], '-')  ?></td>
            <td style="text-align: center"></td>
            <td><?= if_empty($report['description'], '-')  ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>

