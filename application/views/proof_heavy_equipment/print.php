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
            <td colspan="4"><?= $heavyEquipment['name_heavy_equipment']?></td>
        </tr>
        <tr>
            <td><strong>Hari</strong></td>
            <td>:</td>
            <td colspan="4"><?= $heavyEquipment['day_name']?></td>
        </tr>
        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td colspan="4"><?= date('d F Y',strtotime($heavyEquipment['tgl']))?></td>
        </tr>
        <tr>
            <td><strong>Jam</strong></td>
            <td>:</td>
            <td colspan="4"><?= $heavyEquipment['start_job']." - ".$heavyEquipment['finish_job'] ?></td>
        </tr>
        <tr>
            <td><strong>Keterangan</strong></td>
            <td>:</td>
            <td colspan="4"><?= $heavyEquipment['handling_type']?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td colspan="4">_______________________________________________________________________
            </td>
        </tr>
        <tr>
            <td><strong>Remark</strong></td>
            <td>:</td>
            <td colspan="4">
                _______________________________________________________________________
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
                    <p class="mb0"><strong><?= $branchVms['branch'] ?>, <?= date('d F Y') ?></strong></p>
                    <br><br><br>
                    ___________________
                    <br><br><br>
                </div>
            </td>
        </tr>
        
    </table>
</div>