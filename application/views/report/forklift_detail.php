<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Detail Freighton Week <?= get_url_param('minggu',0) + 1 ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-forklift-detail">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>NO WORK ORDER</th>
                <th>CUSTOMER NAME</th>
                <th>FRT JOB</th>
                <th>SATUAN</th>
                <th>HEAVY EQUIPMENT</th>
                <th>FRT</th>
                <th>DATE JOB</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            $total_frt = 0;
            $total_day = count($tanggalKerja);
            $temp_date = isset($datas['data'][0]['completed_at'])? date('Y-m-d',strtotime($datas['data'][0]['completed_at'])) : '';
            foreach ($datas['data'] as $data): ?>
            <?php
            $total_frt+=$data['frt'];
            $date = date('Y-m-d',strtotime($data['completed_at']));
            if(!in_array(date('Y-m-d',strtotime($data['completed_at'])),$tanggalKerja)){
                $tanggalKerja[]=date('Y-m-d',strtotime($data['completed_at']));
                $total_day++;
            }
             ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="responsive-title"><a href="<?= base_url() ?>p/<?=$data['id_branch']?>/work-order/view/<?= $data['id'] ?>"><?= $data['no_work_order'] ?></a></td>
                    <td><?= if_empty($data['customer_name'], '-') ?></td>
                    <td><?= if_empty($data['frt_own'], '-')?></td>
                    <td><?= if_empty(($data['satuan']=='METER KUBIK')? 'M<sup>3</sup>' : $data['satuan'], '-')?></td>
                    <td><?= if_empty($data['forklift_name'], '-')?></td>
                    <td><?= if_empty($data['frt'], '-')?></td>
                    <td><?= if_empty($data['completed_at'], '-')?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="6">Total</th>
                <th><?= $total_frt ?></th>
                <th><?= $total_day ?> Day</th>
            </tr>
            <tr>
                <th colspan="6">Average</th>
                <th colspan="2"><?= $total_frt/$total_day ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>