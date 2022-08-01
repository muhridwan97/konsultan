<h3>Master</h3>
<p>
    Master adalah data referensi yang digunakan didalam transaksi program. Setiap perubahan yang dilakukan
    di dalam menu master akan mempengaruhi data transaksi yang mereferensikannya.
</p>
<p>
    Data master harusnya tunggal dengan kriteria tertentu, data master ganda akan membuat
    bingung ketika pemilihan referensi dalam sebuah transaksi.
</p>
<p>
    Data master dapat diakses oleh beberapa user dengan permission yang signifikan seperti yang dimiliki
    role Supervisor atau Manager karena sangat sensitif. Penghapusan data master dapat mempengaruhi kondisi transaksi,
    ada beberapa kasus transaksi yang mereferensi data tersebut ikut hilang atau data transaksi tidak dapat dihapus.
</p>