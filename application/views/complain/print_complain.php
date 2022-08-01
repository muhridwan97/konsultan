<div class="box">
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered no-datatable">
                <tr>
                    <th style="background-color: #E5CCC9; font-weight: bold;" colspan="4" class="text-center">BAGIAN 1 - PETUGAS</th>
                </tr>
                <tr>
                    <th style="font-weight: bold;" width="50%" colspan="2" class="text-left">3. PENGGAGAS :</th>
                    <th style="font-weight: bold;" width="50%" colspan="2" class="text-left">4. DEPARTEMEN :</th>
                </tr>
                <tr>
                    <td width="50%" colspan="2" class="text-left"><?= $complain['customer_name']; ?></td>
                    <td width="50%" colspan="2" class="text-left"><?= $complain['department']; ?></td>
                </tr>
            </table>
            <table class="table table-bordered no-datatable">
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left">5.</td>
                    <td style="font-weight: bold;" width="4%" class="text-center"><input type="checkbox"></td>
                    <td style="font-weight: bold;" width="95%" class="text-left" colspan="2">Tindakan Korektif (Jika Timbul Tidak Kesesuaian)</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left"></td>
                    <td style="font-weight: bold;" width="4%" class="text-center"><input type="checkbox"></td>
                    <td style="font-weight: bold;" width="95%" class="text-left" colspan="2">Tindakan Korektif (Jika Timbul Tidak Kesesuaian)</td>
                </tr>
            </table>

            <table class="table table-bordered no-datatable">
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left">6.</td>
                    <td style="font-weight: bold;" class="text-left" colspan="3">JENIS KETIDAKSESUAIAN/POTENSIAL KETIDAKSESUAIAN</td>
                </tr>
                <table class="table">
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left"></td>
                    <td style="font-weight: bold;" width="4%" class="text-center"><input type="checkbox"></td>
                    <td style="font-weight: bold;" width="95%" class="text-left" colspan="2">Keluhan Pelanggan - Laporan No. <?= $complain['no_complain']; ?>
                        
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left"></td>
                    <td style="font-weight: bold;" width="4%" align="center"><center><input type="checkbox"></center></td>
                    <td style="font-weight: bold;" width="95%" class="text-left" colspan="2">Laporan Audit No. _ _ _</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left"></td>
                    <td style="font-weight: bold;" width="4%" class="text-center"><input type="checkbox"></td>
                    <td style="font-weight: bold;" width="95%" class="text-left" colspan="2">Laporan Ketidaksesuaian No. _ _ _ </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left"></td>
                    <td style="font-weight: bold;" width="4%" class="text-center"><input type="checkbox"></td>
                    <td style="font-weight: bold;" width="95%" class="text-left" colspan="2">Lain-lain, Sebutkan _ _ _ </td>
                </tr>
                </table>
            </table>

            <table class="table table-bordered no-datatable">
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left">7.</td>
                    <td style="font-weight: bold;" class="text-left" colspan="3">PENJELASAN KETIDAKSESUAIAN (TIMBUL/POTENSIAL)</td>
                </tr>
                <tr height="100px">
                    <td style="font-weight: bold;" width="1%" class="text-left"></td>
                    <td class="text-left" colspan="3" align=justify><?= $complain['complain']; ?></td>
                </tr>
            </table>

            <table class="table table-bordered no-datatable">
                <tr>
                    <th style="background-color: #E5CCC9; font-weight: bold;" colspan="2" class="text-center">BAGIAN 2 - TINDAKAN</th>
                </tr>
                <tr>
                    <td style="font-weight: bold; width: 50%;" class="text-left">8A. DEPARTEMEN : <?= $complain['department']; ?></td>
                    <td style="font-weight: bold; width: 50%;" class="text-left">9. PERKIRAAN TANGGAL PENYELESAIAN : <?= date('d F Y', strtotime($complain['investigation_date'])) ?></td>
                </tr>  
                <tr>
                    <td style="font-weight: bold; width: 50%;" class="text-left">8B. KEPALA BAGIAN : <?= if_empty($kabag['name'], '-'); ?></td>
                    <td style="font-weight: bold; width: 50%;" class="text-left"></td>
                </tr>  
            </table>

            <table class="table table-bordered no-datatable">
                <tr>
                    <td style="font-weight: bold;" class="text-left">10. AKAR MASALAH (ANALISA TINDAKAN UNTUK PENCEGAHAN)</td>
                </tr>
                 <tr height="100px">
                    <td align=justify><?= $complain['investigation_result']; ?></td>
                </tr>
            </table>

            <table class="table table-bordered no-datatable">
                <tr>
                    <td style="font-weight: bold;" class="text-left">11. TINDAKAN KOREKTIF YANG DIAJUKAN</td>
                </tr>
                 <tr height="100px">
                    <td align=justify><?= $complain['corrective']; ?></td>
                </tr>
            </table>

            <table class="table table-bordered no-datatable">
                <tr>
                    <td style="font-weight: bold;" class="text-left">12. TINDAKAN PENGAJUAN YANG DIAJUKAN (JIKA DIBUTUHKAN)</td>
                </tr>
                 <tr height="100px">
                    <td align=justify><?= $complain['prevention']; ?></td>
                </tr>
            </table>

            <?php if(!empty($complainHistories) || count($complainHistories)>1): ?>
            <table class="table table-bordered no-datatable">
                <tr>
                    <td style="font-weight: bold;" class="text-left" colspan="3">RIWAYAT SANGGAH</td>
                </tr>
            </table>
            
            <table class="table">
                <?php foreach ($complainHistories as $key => $complainHistory) :?>
                <tr>
                    <td style="font-weight: bold;" width="1%" class="text-left"></td>
                    <td style="" width="95%" class="text-left" colspan="2"><?= $complainHistory['description']; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>

            <table class="table table-bordered no-datatable">
                <tr>
                    <th style="background-color: #E5CCC9; font-weight: bold;" colspan="3" class="text-center">BAGIAN 3 - VERIFIKASI</th>
                </tr>
                <tr>
                    <td style="font-weight: bold;" width="30%" class="text-left">13. TINDAKAN EFEKTIF</td>
                    <td style="font-weight: bold;" width="30%" class="text-left">14. ALASAN</td>
                    <td style="font-weight: bold;" width="40%" class="text-left">15. FTKP NO </td>
                </tr>
                <tr height="100px">
                    <td width="30%" class="text-left"> &nbsp; <input type="checkbox" <?= $complain['effective'] == "YA" ? 'checked' : '' ?>> YA <input type="checkbox" <?= $complain['effective'] == "TIDAK" ? 'checked' : '' ?>> TIDAK</td>
                    <td style="font-weight: bold;" width="30%" class="text-left"><?= $complain['conclusion']; ?></td>
                    <td style="font-weight: bold;" width="40%" class="text-left"><?= $complain['ftkp']; ?></td>
                </tr>  
            </table>

            <table class="table table-bordered no-datatable">
                <tr>
                    <td style="font-weight: bold;" width="50%" class="text-left" colspan="2">16. TANDA TANGAN YANG MEMVERISFIKASI</td>
                    <td style="font-weight: bold;" width="50%" class="text-left">17. TANGGAL</td>
                </tr>
                <tr height="250px">
                    <td style="font-weight: bold;" class="text-left" colspan="2">...</td>
                    <td style="font-weight: bold;" class="text-left"><?= date('d F Y', strtotime($complain['close_date'])) ?></td>
                </tr>  
            </table>
        </div>
    </div>
</div>