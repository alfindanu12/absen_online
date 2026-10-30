@extends('layouts.dashboard')
@section('isi')
   <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <form action="{{ url('/rekap-data') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-row mb-2">
                        <div class="col-4">
                            <select name="lokasi" id="lokasi" class='form-control'>
                                <option value="" selected>Pilih Cabang</option>
                                @foreach ($cabang as $caba => $cab)
                                <option value="{{$cab->id}}">{{ $cab->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <select name="jabatan_id" id="jabatan_id" class="form-control">
                                <option value="">Pilih Divisi</option>
                                @foreach($golongan as $j)
                                    <option value="{{ $j->id }}">{{ $j->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">Pilih Pegawai</option>
                            </select>
                        </div>
                    </div>    
                    <div class="form-row">
                        <div class="col-4">
                            <input type="datetime" class="form-control" name="mulai" placeholder="Tanggal Mulai" id="mulai" value="{{ request('mulai') }}">
                        </div>
                        <div class="col-4">
                            <input type="datetime" class="form-control" name="akhir" placeholder="Tanggal Akhir" id="akhir" value="{{ request('akhir') }}">
                        </div>
                        <div>
                            <input type="hidden" name="submit" value="submitted">
                            <button type="submit" id="search" class="form-control btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="tableprintrekap" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama Pegawai</th>
                                <th>Total Cuti Dadakan</th>
                                <th>Total Cuti Bersama</th>
                                <th>Total Cuti Menikah</th>
                                <th>Total Cuti Diluar Tanggungan</th>
                                <th>Total Cuti Khusus</th>
                                <th>Total Cuti Melahirkan</th>
                                <th>Total Izin Telat</th>
                                <th>Total Izin Pulang Cepat</th>
                                <th>Total Hadir</th>
                                <th>Total Alfa</th>
                                <th>Total Libur</th>
                                <th>Total Telat</th>
                                <th>Total Pulang Cepat</th>
                                <th>Total Lembur</th>
                                <th>Pot Telat (Rp)</th>
                                <th>Bonus Lembur (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_user as $du)
                            <tr>
                                <td>
                                    {{ $du->name }}
                                </td>

                                <td>
                                    @php
                                        echo $du->Cuti->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('nama_cuti', 'Cuti Dadakan')->where('status_cuti', 'Diterima')->count() . " x";
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        echo $du->Cuti->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('nama_cuti', 'Cuti Bersama')->where('status_cuti', 'Diterima')->count() . " x";
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        echo $du->Cuti->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('nama_cuti', 'Cuti Menikah')->where('status_cuti', 'Diterima')->count() . " x";
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        echo $du->Cuti->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('nama_cuti', 'Cuti Diluar Tanggungan')->where('status_cuti', 'Diterima')->count() . " x";
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        echo $du->Cuti->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('nama_cuti', 'Cuti Khusus')->where('status_cuti', 'Diterima')->count() . " x";
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        echo $du->Cuti->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('nama_cuti', 'Cuti Melahirkan')->where('status_cuti', 'Diterima')->count() . " x";
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        $jumlah_izin_telat = $du->MappingShift->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('status_absen', 'Izin Telat')->count()
                                    @endphp
                                    {{ $jumlah_izin_telat . " x" }}
                                </td>
                                <td>
                                    @php
                                        $jumlah_izin_pulang_cepat = $du->MappingShift->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('status_absen', 'Izin Pulang Cepat')->count()
                                    @endphp
                                    {{ $jumlah_izin_pulang_cepat . " x" }}
                                </td>
                                <td>
                                    @php
                                        $jumlah_hadir = $du->MappingShift->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('status_absen', '=', 'Masuk')->count();
                                    @endphp
                                    {{ $jumlah_hadir + $jumlah_izin_telat + $jumlah_izin_pulang_cepat. " x" }}
                                </td>
                                <td>
                                    @php
                                        $jumlah_tidak_hadir = $du->MappingShift->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('status_absen', 'Tidak Masuk')->count();
                                        echo $jumlah_tidak_hadir . " x";
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        echo $du->MappingShift->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('status_absen', 'Libur')->count() . " x";
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        
                                        $total_denda = 0;
                                        $totalMenitTerlambat = 0;
                                         if ($jumlah_tidak_hadir > 0) {
                                            $total_denda = $jumlah_tidak_hadir * 0;
                                         }
                                        
                                        $qq = $du->MappingShift->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->where('status_absen','Masuk');
                                        
                                        foreach ($qq as $data) {
    if (!empty($data->jam_absen)) {
        $jamBatas = $data->Shift->jam_masuk;
        $menitBatas = (int) date('H', strtotime($jamBatas)) * 60 + (int) date('i', strtotime($jamBatas));
        $menitAbsen = (int) date('H', strtotime($data->jam_absen)) * 60 + (int) date('i', strtotime($data->jam_absen));
        
        

        $selisihMenit = $menitAbsen - $menitBatas;
        
        if ($selisihMenit > 0) {
            // Hitung denda
            $jumlahBlok = ceil($selisihMenit / 15);
            $denda = $jumlahBlok * 10000;
            
            // Batasi maksimal 50rb
            //if ($denda > 50000) {
              //  $denda = 50000;
            //}
            
            $total_denda += $denda;
            $totalMenitTerlambat += $selisihMenit;
            
            // Tampilkan detail per hari
            //echo "Tanggal: {$data->tanggal} | Jam: {$data->jam_absen} | ";
            //echo "Telat: " . konversiMenitKeJamMenit($selisihMenit) . " | Denda: Rp " . number_format($denda, 0, ',', '.') . "<br>";
        }
    }
}

// Konversi total menit ke jam dan menit
$jam = floor($totalMenitTerlambat / 60);
$menit = $totalMenitTerlambat % 60;
                                        
                                        //$jam   = floor($total_telat / (60 * 60));
                                        //$menit = $total_telat - ( $jam * (60 * 60) );
                                        //$menit2 = floor($menit / 60);
                                    @endphp

                                    @if($jam <= 0 && $menit <= 0)
                                        <span class="badge badge-success">Tidak Pernah Telat</span>
                                    @else
                                        <span class="badge badge-danger">{{ $jam." Jam ".$menit." Menit" }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $total_pulang_cepat = $du->MappingShift->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->sum('pulang_cepat');
                                        $jam_cepat   = floor($total_pulang_cepat / (60 * 60));
                                        $menit_cepat = $total_pulang_cepat - ( $jam_cepat * (60 * 60) );
                                        $menit_cepat2 = floor($menit_cepat / 60);
                                    @endphp

                                    @if($jam_cepat <= 0 && $menit_cepat2 <= 0)
                                        <span class="badge badge-success">Tidak Pernah Pulang Cepat</span>
                                    @else
                                        <span class="badge badge-danger">{{ $jam_cepat." Jam ".$menit_cepat2." Menit" }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $total_lembur = $du->Lembur->where('status', 'Approved')->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])->sum('total_lembur');
                                        $jam   = floor($total_lembur / (60 * 60));
                                        $menit = $total_lembur - ( $jam * (60 * 60) );
                                        $menit2 = floor($menit / 60);
                                        $detik = $total_lembur % 60;
                                    @endphp

                                    <span class="badge badge-success">{{ $jam." Jam ".$menit2." Menit" }}</span>
                                </td>
                                
                                <td>
                                    @php
                                        /* 
                                        $menit_telat = floor($total_telat /(60 * 15) );
                                        $total_denda = $menit_telat * 10000;
                                        echo number_format($total_denda, 0, ',','.'); 
                                        */
                                        
                                        
                                        // $menit_telat = ceil($total_telat / 60);
                                        // $kelipatan_telat = ceil($menit_telat / 15);
                                        // $total_denda = $kelipatan_telat * 10000;
                                        
                                        // if ($total_denda > 50000) {
                                          //  $total_denda = 50000;
                                        // }
                                        echo number_format($total_denda, 0, ',','.');
                                        
                                    @endphp
                                </td>
                                <td>
                                    @php
                                        /*
                                        $menit_lembur = floor($total_lembur / (60 * 30 ));
                                        $total_bonus = $menit_lembur * 5000;
                                        echo number_format($total_bonus, 0, ',','.'); 
                                        */
                                        $menit_lembur = ceil($total_lembur / 60);
                                        $kelipatan_lembur = ceil($menit_lembur / 30);
                                        $total_bonus = $kelipatan_lembur * 5000;
                                        echo number_format($total_bonus, 0, ',','.');
                                        
                                        
                                    @endphp
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<br>
@push('script')
    <script type='text/javascript'>
    
    $(document).ready(function(){
        $("#lokasi").change(function(){
            var lokasiId = $(this).val();
            $.ajax({
                url: '{{ route("get-divisi-by-lokasi") }}',
                type: 'GET',
                data: {
                    lokasi_id: lokasiId
                },
                success: function(response){
                    $('#jabatan_id').empty();
                    $('#user_id').empty();
                    $('#user_id').append('<option value=0>Pilih Pegawai</option>');
                    // if(lokasiId == 0){
                        $('#jabatan_id').append('<option value="0" selected>Pilih Divisi</option>');
                        $.each(response.data, function(index, divisi){
                            $('#jabatan_id').append('<option value = "'+ divisi.id + '">'+divisi.name + '</option>');
                        });
                    // }
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
            $.ajax({
                url: '{{ route("get-karyawan-by-lokasi") }}',
                type: 'GET',
                data: {
                    lokasi_id: lokasiId
                },
                success: function(response){
                    $('#user_id').empty();
                    $('#user_id').append('<option value="0">Pilih Pegawai</option>');
                    $.each(response.allkaryawan, function(index, karyawan){
                        $('#user_id').append('<option value = "'+ karyawan.id + '">'+karyawan.name + '</option>');
                    });
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
        });
        $("#jabatan_id").change(function(){
            var jabatanId = $(this).val();
            var lokasiId = $("#lokasi").val();
            $.ajax({
                url: '{{ route("get-karyawan-by-jabatan-lokasi") }}',
                type: 'GET',
                data: {
                    jabatan_id: jabatanId,
                    lokasi_id: lokasiId
                    },
                success: function(response){
                    $('#user_id').empty();
                    if(jabatanId === "0"){
                        $('#user_id').append('<option value="0">Pilih Pegawai</option>');
                        $.each(response.allkaryawan, function(index, karyawan){
                            $('#user_id').append('<option value = "'+ karyawan.id + '">'+karyawan.name + '</option>');
                        });
                        $('.selectpicker').selectpicker('refresh');
                    }else{
                        $('#user_id').append('<option value="0">Pilih Pegawai</option>');
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
        
        var lokasi_id = "<?php echo $lokasi_id; ?>";
        var jabatan_id = "<?php echo $jabatan_id; ?>";
        var pegawai_id = "<?php echo $pegawai_id; ?>";
        $("#lokasi").val(lokasi_id);
        $("#jabatan_id").val(jabatan_id);
        $("#user_id").val(pegawai_id);
    })
</script>
@endpush
@endsection
