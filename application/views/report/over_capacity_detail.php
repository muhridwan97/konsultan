<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Over Capacity Detail Report Aju <?= get_url_param('no_reference',0) ?></h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Date</th>
                <th>No Work Order</th>
                <th>No Reference</th>
                <th>Space</th>
            </tr>
            </thead>
            <tbody>
                <?php $number = 0;  $totalSpace = []; 
                if(!empty($workOrders)): ?>
                <?php
                foreach ($workOrders as $workOrder): 
                    $totalSpace[] = $workOrder['new_space']; ?>
                <tr>
                    <td ><?= $number = $number+1; ?></td>
                    <td ><?= $workOrder['completed_at'] ?></td>
                    <td ><a href="<?= site_url('work-order/view/'.$workOrder['id'])?>"><?= $workOrder['no_work_order'] ?></a></td>
                    <td ><?= $workOrder['no_reference'] ?></td>
                    <td ><?= number_format($workOrder['new_space'],2) ?> m<sup>2</sup></td>
                </tr>
                <?php endforeach; ?>
                
            </tbody>
            <tbody>
                <tr>
                    <?php if(!empty($opnameSpace)): ?>
                        <td  colspan="3"> Total </td>
                        <?php if(array_sum($totalSpace)==0): ?>
                            <td class="pull-right"> <?= array_sum($totalSpace) ?> ( <?= $opnameSpace[0]['space_diff']>0 ? '+' : '' ?><?= numerical($opnameSpace[0]['space_diff'],2) ?> )</td>
                        <?php else: ?>
                            <td class="pull-right"> <?= array_sum($totalSpace) ?> ( <?= $opnameSpace[0]['space_diff']>0 ? '+' : '' ?><?= numerical($opnameSpace[0]['space_diff'],2) ?> ) (<?= $opnameSpace[0]['space_diff']>0 ? '+' : '' ?><?= numerical($opnameSpace[0]['space_diff']/abs(array_sum($totalSpace))*100,0) ?>%)</td>
                        <?php endif ?>
                        <td ><?= numerical((array_sum($totalSpace)+$opnameSpace[0]['space_diff']),2) ?> m<sup>2</sup></td>
                    <?php else: ?>
                        <td  colspan="4"> Total </td>
                        <td ><?= numerical(array_sum($totalSpace),2) ?> m<sup>2</sup></td>
                    <?php endif; ?>    
                </tr>
            </tbody>
                <?php endif; ?>
        </table>
        </div>
    </div>
</div>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Check Histories</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th class="text-center" style="width: 20px">No</th>
                    <th class="text-center">Check Space</th>
                    <th class="text-center">Check Difference</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Date Opname</th>
                    <th class="text-center">Validated By</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($checkHistory as $history): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= empty($history['space_check']) ? "not check" : numerical($history['space_check'],2) ?></td>
                        <td><?= empty($history['space_diff']) ? "not check" : numerical($history['space_diff'],2) ?></td>
                        <td><?= $history['status'] ?></td>
                        <td><?= $history['opname_space_date'] ?></td>
                        <td><?= $history['validate_name'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($checkHistory)): ?>
                    <tr>
                        <td colspan="4" class="text-center"><?= "No history" ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>  
</div>
