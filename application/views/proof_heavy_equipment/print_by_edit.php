<div class="invoice-wrapper-pdf">
    <div class="clearfix">
        <div class="" style="text-align: center">
            <img src="<?= FCPATH . 'assets/app/img/layout/kop_email.jpg' ?>" class="mt10 pull-left" height="82">
        </div>
        
    </div>

    <hr >
    
    <p class="title-2 text-center mb10" style="font-size: 22px;text-align: center">
        <ins><strong>BUKTI PEMAKAIAN</strong><ins>
    </p>

    <table style="width: 100%; line-height: 2;" class="mb10 mt0">
        <tr>
            <td width="17%"><strong>Alat Berat</strong></td>
            <td width="2%">:</td>
            <td colspan="4"><?= $heavyEquipment['alat_berat']?></td>
        </tr>
        <tr>
            <td><strong>Hari</strong></td>
            <td>:</td>
            <td colspan="4"><?= $heavyEquipment['hari']?></td>
        </tr>
        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td colspan="4"><?= date('d F Y',strtotime($heavyEquipment['date']))?></td>
        </tr>
        <tr>
            <td><strong>Jam</strong></td>
            <td>:</td>
            <td colspan="4"><?= $heavyEquipment['start']." - ".$heavyEquipment['end'] ?></td>
        </tr>
        <tr>
            <td><strong>Keterangan</strong></td>
            <td>:</td>
            <td colspan="4"><?= $heavyEquipment['description']?>
            </td>
        </tr>
        <tr>
            <td><strong>Remark</strong></td>
            <td>:</td>
            <td colspan="4"><?= $heavyEquipment['remark']?>
            </td>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <td>
            </td>
            <td rowspan="2" style="vertical-align: top">
                <div class="ml20 text-center">
                    <p class="mb0"><strong><?= $heavyEquipment['sign_location'] ?>, <?= date('d F Y',strtotime($heavyEquipment['date']))?></strong></p>
                    <br>
                    This document data is validated from the system
                    <br><br><br>
                </div>
            </td>
        </tr>
        
    </table>
</div>