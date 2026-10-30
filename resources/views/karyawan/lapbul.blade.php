@extends('layouts.dashboard')
@section('isi')
    <div class="container-fluid">
        <div class="card" style="border-radius: 20px">
            <div class="card-header">
                <div class='row'>
                    <div class='col-3'>
                        <div class='form-group'>
                            <input type="button" class='btn btn-primary form-control' value='Bulanan' id='bulanan'>
                        </div>
                    </div>
                    <div class='col-3'>
                        <div class='form-group'>
                            <input type="button" class='btn btn-default form-control' value='Mingguan' id='mingguan'>
                        </div>
                    </div>
                </div>
                <div class='row' id='bulanan-type'>
                    <div class='col-5'>
                        <div class='form-group'>
                            <label for="startFromMonth">From</label>
                            <input type="date" name="startFromMonth" id="startFromMonth" class='form-control'>
                        </div>
                    </div>
                    <div class='col-5'>
                        <div class='form-group'>
                            <label for="toEndMonth">To</label>
                            <input type="date" class='form-control' name="toEndMonth" id="toEndMonth">
                        </div>
                    </div>
                    <div class='col-2'>
                        <div class='form-group'>
                            <label for="print">&nbsp;</label>
                            <input class='btn btn-success form-control' type="button" name="print" id="printBulanan" value="Cetak">
                        </div>
                    </div>
                </div>
                <div class='row' id='mingguan-type' style='display:none;'>
                    <div class='row col-12'>
                        <div class='col-4'>
                            <div class='form-group'>
                                <label for="lokasi">Cabang</label>
                                <select name="lokasi" id="lokasi" class='form-control'>
                                    <option value="0" selected>Semua</option>
                                    @foreach ($cabang as $caba => $cab)
                                    <option value="{{$cab->id}}">{{ $cab->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='col-4'>
                            <div class='form-group'>
                                <label for="jabatan">Divisi</label>
                                <select name="jabatan" id="jabatan" class='form-control'>
                                    <option value="0" selected>Semua</option>
                                    @foreach ($jabatan as $jabatan => $jab)
                                    <option value="{{$jab->id}}">{{ $jab->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='col-4'>
                            <div class='form-group'>
                                <label for="karyawan">Karyawan</label>
                                <select name="karyawan" id="karyawan" class='form-control'>
                                    <option value="" selected>Semua</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class='row col-12'>
                        <div class='col-4'>
                            <div class='form-group'>
                                <label for="startFrom">From</label>
                                <input type="date" id='startFrom' class='form-control'/>
                            </div>
                        </div>
                        <div class='col-4'>
                            <div class='form-group'>
                                <label for="endTo">To</label>
                                <input class='form-control' type='date' id='endTo' />
                            </div>
                        </div>
                        <div class='col-4'>
                            <div class='form-group'>
                                <label for="printMingguan">&nbsp;</label>
                                <input class='btn btn-success form-control' type='button' id='printMingguan' value='Cetak'/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" id='bulanan-tabel'>
                <table class="table table-sm table-bordered table-hover" id='table-bulanan'>
                    <thead class='text-center'>
                        <tr>
                            <th width="10px">No</th>
                            <th>Divisi</th>
                            <th width='150px'>Jumlah Karyawan</th>
                            <th>Total Lembur</th>
                            <th>Sub Total Gaji</th>
                        </tr>
                    </thead>
                    <tbody id='data-gaji'>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total Gaji</th>
                            <th id="total-gaji" class="text-left">Rp. -</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class='card-body' id='mingguan-tabel'  style='display:none'>
                <table class="table table-sm table-bordered table-hover" id='table-mingguan' width='100%'>
                    <thead class='text-center'>
                        <tr>
                            <th width="10px">No</th>
                            <th>Nama Karyawan</th>
                            <th>Upah Normal</th>
                            <th>Jumlah Hadir</th>
                            <th>Upah Target</th>
                            <th>Upah Lembur</th>
                            <th>Masuk Minggu</th>
                            <th>Kasbon</th>
                            <th>Denda</th>
                            <th>Total Terima Gaji</th>
                            <th>Print Slip Gaji</th>
                        </tr>
                    </thead>
                    <tbody id='data-gaji-karyawan'>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="9" class="text-right">Total Gaji</th>
                            <th id="total-gaji-karyawan" class="text-left">Rp. -</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
          </div>
        
    <br>

    @push('script')
        <script type="text/javascript">
            $('#bulanan').on('click', function(){
                $('#bulanan-type').show();
                $('#bulanan-tabel').show();
                $('#mingguan-type').hide();
                $('#mingguan-tabel').hide();
                // Mengubah style tombol
                $('#bulanan').removeClass('btn-default').addClass('btn-primary');
                $('#mingguan').removeClass('btn-primary').addClass('btn-default');
            });
            $('#mingguan').on('click', function(){
                $('#bulanan-type').hide();
                $('#bulanan-tabel').hide();
                $('#mingguan-type').show();
                $('#mingguan-tabel').show();
                $('#mingguan').removeClass('btn-default').addClass('btn-primary');
                $('#bulanan').removeClass('btn-primary').addClass('btn-default');
            });
            $("#lokasi").change(function(){
                var lokasiId = $(this).val();
                $.ajax({
                    url: '{{ route("get-divisi-by-lokasi") }}',
                    type: 'GET',
                    data: {
                        lokasi_id: lokasiId
                    },
                    success: function(response){
                        $('#jabatan').empty();
                        // if(lokasiId == 0){
                            $('#jabatan').append('<option value=0>Pilih Divisi</option>');
                            $.each(response.data, function(index, divisi){
                                $('#jabatan').append('<option value = "'+ divisi.id + '">'+divisi.name + '</option>');
                            });
                        // }
                    },
                    error: function(xhr){
                        console.log(xhr.responseText);
                    }
                });
            });
            $("#jabatan").change(function(){
                var jabatanId = $(this).val();
                $.ajax({
                    url: '{{ route("get-karyawan-by-jabatan") }}',
                    type: 'GET',
                    data: {
                        jabatan_id: jabatanId
                    },
                    success: function(response){
                        $('#karyawan').empty();

                        if(jabatanId == 0){
                            $('#karyawan').append('<option value="0">Pilih Karyawan</option>');
                            $.each(response.allkaryawan, function(index, karyawan){
                                $('#karyawan').append('<option value = "'+ karyawan.id + '">'+karyawan.name + '</option>');
                            });
                        }else{
                            $('#karyawan').append('<option value="0">Pilih Karyawan</option>');
                            $.each(response.karyawan, function(index, karyawan){
                                $('#karyawan').append('<option value = "'+ karyawan.id + '">'+karyawan.name + '</option>');
                            });
                        }
                    },
                    error: function(xhr){
                        console.log(xhr.responseText);
                    }
                });
            });
            $('#jabatan').trigger('change');
            // $('#karyawan').trigger('change');
            var getJabatanUrl = "{{ route('get-jabatan-karyawan', ':id') }}";
            var today = new Date().toISOString().split('T')[0];
            var pastDate = new Date();
            pastDate.setDate(pastDate.getDate() - 7);
            var sevenDaysAgo = pastDate.toISOString().split('T')[0];
            // Set nilai default untuk input startFrom dan toEnd
            $('#startFrom').val(sevenDaysAgo);
            $('#endTo').val(today);

            // set nilai default untuk startFromMonth dan toEndMonth
            var todayMonth = new Date();
            var year = todayMonth.getFullYear();
            var month = todayMonth.getMonth() + 1;
            if(month < 10){
                month = '0' + month;
            }
            var startFromMonth = year + '-' + month + '-01';
            $('#startFromMonth').val(startFromMonth);

            var lastDayOfMonth = new Date(year, month, 0).getDate();
            var endToMonth = year + '-' + month + '-' + lastDayOfMonth;
            $('#toEndMonth').val(endToMonth);
            
            // $('#karyawan').trigger('change');
            
            var table = $("#table-bulanan").DataTable({
                processing: true,
                // serverSide: true,
                ajax: {
                    url: '{{ route('get-gaji-karyawan') }}',
                    data: function(d){
                        d.startFrom = $("#startFromMonth").val();
                        d.endTo = $("#toEndMonth").val();
                    },
                    error: function(xhr){
                        alert('an error occured :', xhr.responseText);
                    }
                },
                columns: [
                    {data: null, name: 'no', className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1; // Menambahkan nomor urut
                        }
                    },
                    {data: 'jabatan', name: 'jabatan'},
                    {data: 'jumlah_karyawan', name: 'jumlah_karyawan', className: 'text-center'},
                    {data: 'total_lembur', name: 'total_lembur', className: 'text-left',
                        render: function(data, type, row){
                            return data === null || data === 0 ? 'Rp. -' : 'Rp. ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0});
                        }
                    },
                    {
                        data: null, // Calculate dynamically
                        name: 'total',
                        className: 'text-left',
                        render: function(data, type, row) {
                            // Parse values as integers and calculate the total
                            let total = 
                                parseInt(row.subtotal || 0) + 
                                parseInt(row.total_lembur || 0) + 
                                parseInt(row.total_masuk_minggu || 0) + 
                                parseInt(row.total_upah_target || 0) - 
                                parseInt(row.total_kasbon || 0) - 
                                parseInt(row.total_denda || 0);

                            // Format the result as Indonesian Rupiah or display 'Rp. -' if 0
                            return total === 0 
                                ? 'Rp. -' 
                                : 'Rp. ' + total.toLocaleString('id-ID', { minimumFractionDigits: 0 });
                        }
                    },
                ],
                 footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Helper function to parse currency format to numbers
                    var parseCurrency = function(value) {
                        if (typeof value === 'string') {
                            return parseFloat(value.replace(/[^\d.-]/g, '')) || 0;
                        }
                        return value || 0;
                    };

                    // Calculate total sum of all 'total' columns in the current page
                    var totalSum = data.reduce(function(acc, row) {
                        let rowTotal = 
                            parseInt(row.subtotal || 0) + 
                            parseInt(row.total_lembur || 0) + 
                            parseInt(row.total_masuk_minggu || 0) + 
                            parseInt(row.total_upah_target || 0) - 
                            parseInt(row.total_kasbon || 0) - 
                            parseInt(row.total_denda || 0);
                        return acc + rowTotal;
                    }, 0);

                    // Display the total sum in the footer
                    $(api.column(4).footer()).html(
                        'Rp. ' + totalSum.toLocaleString('id-ID', { minimumFractionDigits: 0 })
                    );
                }
            });
            
            $("#startFromMonth, #toEndMonth").change(function() {
                table.ajax.reload();  // Reload data ketika bulan/tahun diubah
            });

            var weektable = $('#table-mingguan').DataTable({
                lengthMenu: [[100, 200, 300, 1000], [100, 200, 300, 1000]],
                pageLength: 100,
                processing: true,
                // serverSide: true,
                ajax: {
                    url: '{{ route('get-gaji-karyawan-by-id') }}',
                    data: function(d){
                        d.user_id = $("#karyawan").val();
                        d.lokasi_id = $("#lokasi").val();
                        d.jabatan_id = $("#jabatan").val();
                        d.startFrom = $("#startFrom").val();
                        d.endTo = $("#endTo").val();
                    },
                    error: function(xhr){
                        alert('an error occured :', xhr.responseText);
                    }
                },
                columns: [
                    {data: null, name: 'no', className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1; // Menambahkan nomor urut
                        }
                    },
                    {data: 'nama_karyawan', name: 'nama_karyawan'},
                    {data: 'upah_normal', name: 'upah_normal',
                        render: function(data, type, row){
                            return data === null || data === 0 ? 'Rp. -' : 'Rp. ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0});
                        }
                    },
                    {data: 'jumlah_hadir', name: 'jumlah_hadir', className: 'text-center'},
                    {data: 'upah_target', name: 'upah_target',
                        render: function(data, type, row){
                            return data === null || data === 0 ? 'Rp. -' : 'Rp. ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0});
                        }
                    },
                    {data: 'upah_lembur', name: 'upah_lembur',
                        render: function(data, type, row){
                            return data === null || data === 0 ? 'Rp. -' : 'Rp. ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0});
                        }
                    },
                    {data: 'masuk_minggu', name: 'masuk_minggu',
                        render: function(data, type, row){
                            return data === null || data === 0 ? 'Rp. -' : 'Rp. ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0});
                        }
                    },
                    {data: 'kasbon', name: 'kasbon',
                        render: function(data, type, row){
                            return data === null || data === 0 ? 'Rp. -' : 'Rp. ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0});
                        }
                    },
                    {data: 'denda', name: 'denda',
                        render: function(data, type, row){
                            return data === null || data === 0 ? 'Rp. -' : 'Rp. ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0});
                        }
                    },
                    {data: 'total_gaji', name: 'total_gaji',
                        render: function(data, type, row){
                            return data === null || data === 0 ? 'Rp. -' : 'Rp. ' + parseFloat(data).toLocaleString('id-ID', {minimumFractionDigits: 0});
                        }
                    },
                    {data: 'null', name: 'cetak', className: 'text-center',
                        render: function(data, type, row){
                            return `<button class='btn btn-success print-row'><i class='fas fa-print'></i></button>`;
                        }
                    }
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // Fungsi untuk menghapus format "Rp" dan koma untuk perhitungan
                    function parseCurrency(value) {
                        return typeof value === 'string'
                            ? parseFloat(value.replace(/[^\d.-]/g, '')) || 0
                            : value;
                    }

                    // Menghitung total untuk masing-masing kolom
                    function sumColumn(index) {
                        return api
                            .column(index, { page: 'current' })
                            .data()
                            .reduce(function (a, b) {
                                return parseCurrency(a) + parseCurrency(b);
                            }, 0);
                    }

                    // Hitung total masing-masing komponen
                    var totalGaji = sumColumn(9);       // total_gaji
                    var totalUpahTarget = sumColumn(4); // upah_target
                    var totalUpahLembur = sumColumn(5); // upah_lembur
                    // var totalMasukMinggu = sumColumn(6); // masuk_minggu
                    var totalKasbon = sumColumn(7);     // kasbon
                    var totalDenda = sumColumn(8);      // denda

                    // Hitung total keseluruhan
                    var grandTotal = totalGaji 
                        // + totalUpahTarget 
                        // + totalUpahLembur 
                        // // + totalMasukMinggu 
                        // - totalKasbon 
                        // - totalDenda;

                    // Tampilkan hasil di footer
                    $(api.column(9).footer()).html(
                        'Rp. ' + grandTotal.toLocaleString('id-ID', { minimumFractionDigits: 0 })
                    );
                }

            });
            $("#lokasi, #jabatan ,#karyawan, #startFrom, #endTo").change(function() {
                weektable.ajax.reload();  // Reload data ketika bulan/tahun diubah
            });

            $("#printBulanan").click(function() {
                // Ambil data dari tabel yang ingin dicetak
                var startFromMonth = $("#startFromMonth").val(); // Mengambil nama bulan yang dipilih
                var toEndMonth = $("#toEndMonth").val(); // Mengambil tahun yang dipilih

                // Ambil tabel dari halaman
                var tableContent = document.getElementById('table-bulanan').outerHTML; 

                // Buat dokumen baru untuk print layout
                var printWindow = window.open('', '', 'height=1122,width=793');
                
                // Tambahkan konten HTML untuk halaman print
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Print Laporan Gaji Bulanan</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                                padding: 20px;
                            }
                            h3 {
                                text-align: center;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            table, th, td {
                                border: 1px solid black;
                            }
                            th, td {
                                padding: 8px;
                                // text-align: center;
                            }
                            th {
                                background-color: #f2f2f2;
                            }
                        </style>
                    </head>
                    <body>
                        <h3>Laporan Gaji <br>Periode ` + startFromMonth + ` sampai ` + toEndMonth + `</h3>
                        ` + tableContent + `
                    </body>
                    </html>
                `);

                // Tutup dokumen baru dan panggil fungsi print
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            });
            $("#printMingguan").click(function() {
                // Ambil data dari tabel yang ingin dicetak
                var nama_karyawan = $("#karyawan option:selected").text();
                var nama_jabatan = $("#jabatan option:selected").text();
                var key_karyawan = $("#karyawan option:selected").val();
                var key_jabatan = $("#jabatan option:selected").val();
                var periodStart = $("#startFrom").val();
                var periodEnd = $("#endTo").val();
                // Ambil tabel dari halaman
                var tableContent = document.getElementById('table-mingguan').outerHTML; 

                // Buat dokumen baru untuk print layout
                var printWindow = window.open('', '', 'height=1122,width=793');
                
                // Tambahkan konten HTML untuk halaman print
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Print Laporan Gaji Mingguan</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                                padding: 20px;
                            }
                            h3 {
                                text-align: center;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            table, th, td {
                                border: 1px solid black;
                            }
                            th, td {
                                padding: 8px;
                                // text-align: center;
                            }
                            th {
                                background-color: #f2f2f2;
                            }
                                @media print {
                                table tr th:last-child, /* Kolom header terakhir */
                                table tr td:last-child { /* Kolom terakhir */
                                    display: none;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <h3>Laporan Gaji Karyawan <br>Periode ${periodStart} sampai ${periodEnd} </h3>
                        ${(key_karyawan == 0 ? '':                 '<p>Nama    :' +nama_karyawan + '</p>')}
                        ${(key_karyawan != 0 && key_jabatan != 0 ? '<p>Divisi  :' +nama_jabatan +'</p>':'')}
                        ${(key_jabatan != 0 && key_karyawan == 0 ? '<p>Divisi  :' +nama_jabatan +'</p>':'')}
                        <br>
                        ${tableContent}
                    </body>
                    </html>
                `);

                // Tutup dokumen baru dan panggil fungsi print
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            });

            $('#table-mingguan').on('click', '.print-row', function() {
                var rowData = weektable.row($(this).parents('tr')).data(); // Get the row data
                var startFrom = $("#startFrom").val();
                var endTo = $("#endTo").val();
                function formatDate(dateStr) {
                    var parts = dateStr.split("-"); // Memecah tanggal berdasarkan "-"
                    return parts[2] + "-" + parts[1] + "-" + parts[0]; // Menggabungkan kembali dalam format "dd-mm-yyyy"
                }

    // Menggunakan fungsi untuk memformat tanggal
    var formattedStartFrom = formatDate(startFrom); // Hasil: "10-10-2024"
    var formattedEndTo = formatDate(endTo); // Hasil: "15-10-2024"
    // Template for print
    var printContent = `
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Slip Gaji Karyawan</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }

            .slip-container {
                width: 210mm; /* Untuk kertas A5 */
                padding: 20px;
                border: 1px solid #000;
                margin: 0 auto;
                box-sizing: border-box;
            }

            .header {
                text-align: left;
                margin-bottom: 20px;
            }

            .header img {
                max-width: 100px;
            }

            .company-info, .employee-info {
                margin-bottom: 20px;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                border: none;
            }

            .table th, .table td {
                border: none;
                padding: 8px;
                text-align: left;
            }

            .table th {
                background-color: #f2f2f2;
            }

            .summary {
                text-align: right;
            }

            .right-align {
                text-align: right;
            }

            .footer {
                text-align: right;
            }

            .footer strong {
                display: inline-block;
                width: 200px;
            }
        </style>
    </head>
    <body>
        <div class="slip-container">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="vertical-align: top;" width=60%>
                        <p>
                            <span style="font-size: 24px; font-weight: bold;">PT. SOMARINDO FACTORY</span> <br>
                            <span style="font-size: 18px; font-weight: bold;">SLIP GAJI</span> <br>
                            Periode <br>
                            Nama Karyawan
                        </p>
                    </td>
                    <td width="60%">
                        <span style="font-size: 24px; font-weight: bold;"></span> <br>
                        <span style="font-size: 18px; font-weight: bold;"></span> <br>
                        : ${formattedStartFrom} s/d ${formattedEndTo} <br>
                        : ${rowData.nama_karyawan}
                    </td>
                    <td style="text-align: left; vertical-align: top;">
                        <img src="/assets/img/logo-somar-removebg.png" alt="PT. SOMARINDO FACTORY logo" width="200px">
                    </td>
                </tr>
            </table>
            <hr/>
            <table class="table" border=0px>
                <thead>
                    <tr>
                        <th colspan=2>PENDAPATAN</th>
                        <th colspan=2>POTONGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="right-align">${parseFloat(rowData.upah_normal).toLocaleString('id-ID', {minimumFractionDigits: 0})}</td>
                        <td>Kasbon</td>
                        <td class="right-align" style="text-align: right;">${parseFloat(rowData.kasbon).toLocaleString('id-ID', {minimumFractionDigits: 0})}</td>
                    </tr>
                    <tr>
                        <td>Jumlah Hari Kerja</td>
                        <td class="right-align">${rowData.jumlah_hadir}</td>
                        <td>Denda</td>
                        <td class="right-align" style="text-align: right;">${parseFloat(rowData.denda).toLocaleString('id-ID', {minimumFractionDigits: 0})}</td>
                    </tr>
                    <tr>
                        <td>Upah Target</td>
                        <td class="right-align">${parseFloat(rowData.upah_target).toLocaleString('id-ID', {minimumFractionDigits: 0})}</td>
                        <td></td>
                        <td class="right-align"></td>
                    </tr>
                    <tr>
                        <td>Upah Lembur</td>
                        <td class="right-align">${parseFloat(rowData.upah_lembur).toLocaleString('id-ID', {minimumFractionDigits: 0})}</td>
                        <td></td>
                        <td class="right-align"></td>
                    </tr>
                    <tr>
                        <td>Masuk Minggu</td>
                        <td class="right-align">${parseFloat(rowData.masuk_minggu).toLocaleString('id-ID', {minimumFractionDigits: 0})}</td>
                        <td></td>
                        <td class="right-align"></td>
                    </tr>
                </tbody
                
                <tfoot>
                    <tr>
                         <td><strong>TOTAL PENDAPATAN</strong></td>
                        <td class="right-align"><strong> 
                        ${parseFloat((parseFloat(rowData.upah_normal) * parseFloat(rowData.jumlah_hadir)) + parseFloat(rowData.upah_target) + parseFloat(rowData.masuk_minggu) + parseFloat(rowData.upah_lembur)).toLocaleString('id-ID', {minimumFractionDigits: 0})} </strong></td>
                        
                        <td><strong>TOTAL POTONGAN</strong></td>
                        <td class="right-align" style="text-align: right;"><strong> ${parseFloat(parseFloat(rowData.kasbon) + parseFloat(rowData.denda)).toLocaleString('id-ID', {minimumFractionDigits: 0})} </strong></td>
                    </tr>
                    
                    
                    <tr>
                        <td><strong>Sisa Gaji</strong></td>
                        <td class="right-align" > <strong>${parseFloat(rowData.total_gaji).toLocaleString('id-ID', {minimumFractionDigits: 0})} </strong></td>
                        <td></td>
                        <td class="right-align"></td>
                    </tr>
                    <tr>
                        <td colspan="4"><hr></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: center;">Tanda Tangan</td>
                    </tr>
                    <tr>
                        <td><br><br><br></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: center;">(_______________)</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
    </html>  
    `;
    // Open new window for printing
    var printWindow = window.open('', '_blank', 'width=600,height=600');
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    printWindow.document.onload = function () {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    };
});
        </script>
    @endpush
@endsection




