@extends('layouts.dashboard')
@section('isi')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                    <span>Filter Nama dan Rentang Tanggal</span><br><br>
                    <div class="form-row">
                        <div class="col-3">
                            <select name="jabatan_id" id="jabatan_id" class="form-control selectpicker" data-live-search="true">
                                <option value="">Pilih Jabatan</option>
                                @foreach($jabatan as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <select name="user_id" id="user_id" class="form-control selectpicker" data-live-search="true">
                                <option value="">Pilih Pegawai</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="datetime" class="form-control" name="mulai" placeholder="Tanggal Mulai" id="mulai">
                        </div>
                        <div class="col-3">
                            <input type="datetime" class="form-control" name="akhir" placeholder="Tanggal Akhir" id="akhir">
                        </div>
                    </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="tableprintabsen" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Pegawai</th>
                            <th>Shift</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Telat</th>
                            <th>Lokasi Masuk</th>
                            <th>Foto Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Pulang Cepat</th>
                            <th>Lokasi Pulang</th>
                            <th>Foto Pulang</th>
                            <th>Status Absen</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <br>
    @push('script')
    <script type='text/javascript'>
        $(document).ready(function(){
            var today = new Date().toISOString().split('T')[0];
            // Set nilai default untuk input startFrom dan toEnd
            $('#mulai').val(today);
            $('#akhir').val(today);
            
            $('#jabatan_id').trigger('change');
            $('#user_id').trigger('change');

            $("#jabatan_id").change(function(){
                var jabatanId = $(this).val();
                $.ajax({
                    url: '{{ route("get-karyawan-by-jabatan") }}',
                    type: 'GET',
                    data: {
                        jabatan_id: jabatanId
                    },
                    success: function(response){
                        $('#user_id').empty();
                        if(jabatanId == 0){
                            $('#user_id').append('<option value="0">Pilih Karyawan</option>');
                            $.each(response.allkaryawan, function(index, karyawan){
                                $('#user_id').append('<option value = "'+ karyawan.id + '">'+karyawan.name + '</option>');
                            });
                            $('.selectpicker').selectpicker('refresh');
                        }else{
                            $('#user_id').append('<option value="0">Pilih Karyawan</option>');
                            $.each(response.karyawan, function(index, karyawan){
                                $('#user_id').append('<option value = "'+ karyawan.id + '">'+karyawan.name + '</option>');
                            });
                            $('.selectpicker').selectpicker('refresh');
                        }
                    },
                    error: function(xhr){
                        console.log(xhr.responseText);
                    }
                });
            });
            
            var tableprintabsen = $('#tableprintabsen').DataTable( {
                "responsive": true, "autoWidth": false, "processing": true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                dom: 'Bflrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        messageTop: function(){
                            var divisi = $('#jabatan_id option:selected').text();
                            var mulai = $("#mulai").val();
                            var akhir = $("#akhir").val();
                            return 'Divisi \t\t\t : ' + divisi + '\nPeriode \t\t : ' + mulai + 's/d'+ akhir;
                        },
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 8, 9, 12 ]
                        }
                    },
                    {
                        extend: 'pdf',
                        messageTop: function () {
                            var divisi = $('#jabatan_id option:selected').text(); // Divisi/Jabatan dari dropdown
                            var mulai = $('#mulai').val(); // Tanggal mulai
                            var akhir = $('#akhir').val(); // Tanggal akhir
                            return 'Divisi \t\t\t : ' + divisi + '\nPeriode \t\t : ' + mulai + ' s/d ' + akhir;
                        },
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 8, 9, 12 ]
                        }
                    }
                ],
                ajax: {
                    url: '{{ route('get-absensi-karyawan') }}',
                    data: function(d){
                        d.jabatan_id = $("#jabatan_id").val();
                        d.user_id = $("#user_id").val(); 
                        d.mulai = $("#mulai").val();
                        d.akhir = $("#akhir").val();
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
                    {data: 'name', name: 'name'},
                    {data: null, name: 'nama_shift',
                        render: function(data, type, row){
                            return `${data.nama_shift} (${data.jam_masuk} - ${data.jam_keluar})`;
                        }
                    },
                    {data: 'tanggal', name: 'tanggal'},
                    {data: null, name: 'jam_absen',
                        render: function(data, type, row){
                            var ket = '';
                            if(data.status_absen == 'Libur'){
                                ket = `<span class="badge badge-info">Libur</span>`;
                            }else if(data.status_absen == 'Cuti'){
                                ket = `<span class="badge badge-warning">Sedang Cuti</span>`;
                            }else if(data.jam_absen == null){
                                ket = `<span class="badge badge-danger">Belum Absen</span>`;
                            }else{
                                ket = `${data.jam_absen}`;
                            }
                            return ket;
                        }
                    },
                    {data: null, name: 'telat',
                        render: function(data, type, row){
                            var ket = '';
                            if(data.status_absen == 'Libur'){
                                ket = `<span class="badge badge-info">Libur</span>`;
                            }else if(data.status_absen == 'Cuti'){
                                ket = `<span class="badge badge-warning">Sedang Cuti</span>`;
                            }else if(data.status_absen == 'Izin Telat'){
                                ket = `<span class="badge badge-warning">Izin Telat</span>`;
                            }else if(data.jam_absen == null){
                                ket = `<span class="badge badge-danger">Belum Absen</span>`;
                            }else{
                                function formatKeterlambatan(telat){
                                    let jam = Math.floor(telat / 3600);

                                    // Hitung menit
                                    let menit = telat % 3600;
                                    let menit2 = Math.floor(menit / 60);

                                    // Hitung detik (opsional jika dibutuhkan)
                                    let detik = telat % 60;

                                    // Tampilkan hasil
                                    if (jam <= 0 && menit2 <= 0) {
                                        return '<span class="badge badge-success">Tepat Waktu</span>';
                                    } else {
                                        return '<span class="badge badge-danger">' + jam + ' Jam ' + menit2 + ' Menit</span>';
                                    }
                                }
                                ket = formatKeterlambatan(data.telat);
                            }
                            return ket;
                        }
                    },
                    {data: null, name: 'lokasi_masuk',
                        render: function(data, type, row){
                            var ket = '';
                            if(data.status_absen == 'Libur'){
                                ket = `<span class="badge badge-info">Libur</span>`;
                            }else if(data.status_absen == 'Cuti'){
                                ket = `<span class="badge badge-warning">Sedang Cuti</span>`;
                            }else if(data.jam_absen == null){
                                ket = `<span class="badge badge-danger">Belum Absen</span>`;
                            }else{
                                var jarak_masuk = data.jarak_masuk ? data.jarak_masuk.toString().split('.') : ['0'];

                                // Buat URL untuk link peta
                                var mapsUrl = `/maps/${data.lat_absen}/${data.long_absen}/${data.user_id}`;

                                ket = `
                                    <a href="${mapsUrl}" class="btn btn-sm btn-secondary" target="_blank">Lihat Peta</a>
                                    <span class="badge badge-warning">${jarak_masuk[0]} Meter</span>
                                `;
                            }
                            return ket;
                        }
                    },
                    {data: null, name: 'foto_jam_absen',render: function(data, type, row){
                        var ket = '';
                            if(data.status_absen == 'Libur'){
                                ket = `<span class="badge badge-info">Libur</span>`;
                            }else if(data.status_absen == 'Cuti'){
                                ket = `<span class="badge badge-warning">Sedang Cuti</span>`;
                            }else if(data.jam_absen == null){
                                ket = `<span class="badge badge-danger">Belum Absen</span>`;
                            }else{
                                ket = `<img src="{{ url('storage') }}/${data.foto_jam_absen}" style="width: 60px">`;
                            }
                            return ket;
                    }},
                    {data: null, name: 'jam_pulang', render: function(data, type, row){
                        var ket = '';
                            if(data.status_absen == 'Libur'){
                                ket = `<span class="badge badge-info">Libur</span>`;
                            }else if(data.status_absen == 'Cuti'){
                                ket = `<span class="badge badge-warning">Sedang Cuti</span>`;
                            }else if(data.jam_absen == null){
                                ket = `<span class="badge badge-danger">Belum Absen</span>`;
                            }else if(data.jam_pulang == null){
                                ket = `<span class="badge badge-warning">Belum Pulang</span>`;
                            }else{
                                ket = `${data.jam_pulang}`;
                            }
                            return ket;
                    }},
                    {data: null, name: 'pulang_cepat', render: function(data, type, row){
                        var ket = '';
                            if(data.status_absen == 'Libur'){
                                ket = `<span class="badge badge-info">Libur</span>`;
                            }else if(data.status_absen == 'Cuti'){
                                ket = `<span class="badge badge-warning">Sedang Cuti</span>`;
                            }else if(data.jam_absen == null){
                                ket = `<span class="badge badge-danger">Belum Absen</span>`;
                            }else{
                                function formatKecepatan(cepat){
                                    let jam = Math.floor(cepat / 3600);

                                    // Hitung menit
                                    let menit = cepat % 3600;
                                    let menit2 = Math.floor(menit / 60);

                                    // Hitung detik (opsional jika dibutuhkan)
                                    let detik = cepat % 60;

                                    // Tampilkan hasil
                                    if (jam <= 0 && menit2 <= 0) {
                                        return '<span class="badge badge-success">Tidak Pulang Cepat</span>';
                                    } else {
                                        return '<span class="badge badge-danger">' + jam + ' Jam ' + menit2 + ' Menit</span>';
                                    }
                                }
                                ket = formatKecepatan(data.pulang_cepat);
                            }
                            return ket;
                    }},
                    {data: null, name: 'lokasi_pulang',
                        render: function(data, type, row){
                            var ket = '';
                            if(data.status_absen == 'Libur'){
                                ket = `<span class="badge badge-info">Libur</span>`;
                            }else if(data.status_absen == 'Cuti'){
                                ket = `<span class="badge badge-warning">Sedang Cuti</span>`;
                            }else if(data.jam_absen == null){
                                ket = `<span class="badge badge-danger">Belum Absen</span>`;
                            }else if(data.jam_pulang == null){
                                ket = `<span class="badge badge-warning">Belum Pulang</span>`;
                            }else{
                                var jarak_pulang = data.jarak_pulang ? data.jarak_pulang.toString().split('.') : ['0'];

                                // Buat URL untuk link peta
                                var mapsUrl = `/maps/${data.lat_pulang}/${data.long_pulang}/${data.user_id}`;

                                ket = `
                                    <a href="${mapsUrl}" class="btn btn-sm btn-secondary" target="_blank">Lihat Peta</a>
                                    <span class="badge badge-warning">${jarak_pulang[0]} Meter</span>
                                `;
                            }
                            return ket;
                        }
                    },
                    {data: null, name: 'foto_jam_pulang', render: function(data, type, row){
                        var ket = '';
                            if(data.status_absen == 'Libur'){
                                ket = `<span class="badge badge-info">Libur</span>`;
                            }else if(data.status_absen == 'Cuti'){
                                ket = `<span class="badge badge-warning">Sedang Cuti</span>`;
                            }else if(data.jam_absen == null){
                                ket = `<span class="badge badge-danger">Belum Absen</span>`;
                            }else if(data.jam_pulang == null){
                                ket = `<span class="badge badge-warning">Belum Pulang</span>`;
                            }else{
                                ket = `<img src="{{ url('storage') }}/${data.foto_jam_pulang}" style="width: 60px">`;
                            }
                            return ket;
                    }},
                    {data: null, name: 'status_absen', render: function(data, type, row){
                        var badge = '';
                        if(data.status_absen == 'Libur'){
                            badge= 'info';
                        }else if(data.status_absen == 'Cuti' || data.status_absen == 'Izin Telat' || data.status_absen == 'Izin Pulang Cepat'){
                            badge= 'warning';
                        }else if(data.status_absen == 'Masuk'){
                            badge= 'success';
                        }else{
                            badge='danger'
                        }
                        return `<span class='badge badge-${badge}'> ${data.status_absen} </span>`;
                    }},
                    {data: null, name: 'action',
                        render: function(data, type, row){
                            var btn_masuk = '';
                            var btn_pulang = '';
                            var btn_delete = '';
                            if(data.status_absen == 'Libur'){
                                btn_masuk = `<span class='badge badge-info'>Libur</span>`;
                                btn_pulang = `<span class='badge badge-info'>Libur</span>`;
                                btn_delete = ``;
                            }else if(data.status_absen == 'Cuti'){
                                btn_masuk = `<span class='badge badge-warning'>Sedang Cuti</span>`;
                                btn_pulang = `<span class='badge badge-warning'>Sedang Cuti</span>`;
                                btn_delete = ``;
                            }else if(data.jam_absen == null){
                                btn_pulang = `<span class='badge badge-danger'>Belum Masuk</span>`;
                            }else{
                                btn_masuk = `<a href="{{ url('/data-absen/${data.id}/edit-masuk') }}" class="btn btn-warning">Edit Masuk</a>`;
                                btn_pulang = `<a href="{{ url('/data-absen/${data.id}/edit-pulang') }}" class="btn btn-warning">Edit Pulang</a>`;
                            }
                            btn_delete = ``;
                            return `${btn_masuk} ${btn_pulang}
                                <form action="{{ url('/data-absen/${data.id}/delete') }}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button class="btn btn-danger" onClick="return confirm('Are You Sure')"><i class="fas fa-trash"></i></button>
                                </form>
                            `;
                        }
                    },
                ],
            });
            
            $("#jabatan_id, #user_id, #mulai, #akhir").change(function(){
                tableprintabsen.ajax.reload();
            });
        })
        
    </script>
    @endpush
@endsection
