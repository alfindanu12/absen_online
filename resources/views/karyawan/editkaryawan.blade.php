@extends('layouts.dashboard')
@section('isi')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @php
                                $fotoKaryawan = $karyawan->foto_karyawan;
                                if (empty($fotoKaryawan)) {
                                    $fotoPreview = url('assets/img/foto_default.jpg');
                                } elseif (strpos($fotoKaryawan, 'data:image') === 0) {
                                    $fotoPreview = $fotoKaryawan;
                                } else {
                                    $fotoPreview = url('storage/'.$fotoKaryawan);
                                }
                            @endphp
                            <img id="foto-preview" class="profile-user-img img-fluid img-circle" src="{{ $fotoPreview }}" alt="User profile picture">
                        </div>

                        <h3 class="profile-username text-center">{{ $karyawan->name }}</h3>

                        <p class="text-muted text-center">{{ $karyawan->Jabatan->nama_jabatan }}</p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                            <b>Email</b> <a class="float-right">{{ $karyawan->email }}</a>
                            </li>
                            <li class="list-group-item">
                            <b>Username</b> <a class="float-right">{{ $karyawan->username }}</a>
                            </li>
                            <li class="list-group-item">
                            <b>Telepon</b> <a class="float-right">{{ $karyawan->telepon }}</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <!-- <ul class="nav nav-pills">
                          <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul> -->
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <form method="post" action="{{ url('/pegawai/proses-edit/'.$karyawan->id) }}" enctype="multipart/form-data">
                            @method('put')
                            @csrf
                            <div class="form-row">
                                <div class="col">
                                    <label for="name">Nama Pegawai</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" autofocus value="{{ old('name', $karyawan->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="foto_karyawan" class="form-label">Foto Pegawai</label>
                                    <input class="form-control @error('foto_karyawan') is-invalid @enderror" type="file" id="foto_karyawan" name="foto_karyawan" accept="image/*">
                                    <input type="hidden" id="img_enc" name="img_enc" value="">
                                    @error('foto_karyawan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <small class="form-text text-muted">Pilih foto lalu klik Submit. Preview akan tampil langsung.</small>
                                </div>
                                <input type="hidden" name="foto_karyawan_lama" value="{{ $karyawan->foto_karyawan }}">
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $karyawan->email) }}">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="telepon">Nomor Telfon</label>
                                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $karyawan->telepon) }}">
                                    @error('telepon')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $karyawan->username) }}">
                                    @error('username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <input type="hidden" name="password" value="{{ $karyawan->password }}">
                                <div class="col">
                                    <label for="lokasi_id">Lokasi Kantor</label>
                                    <select name="lokasi_id" id="lokasi_id" class="form-control selectpicker" data-live-search="true">
                                        @foreach ($data_lokasi as $dl)
                                            @if(old('lokasi_id', $karyawan->lokasi_id) == $dl->id)
                                            <option value="{{ $dl->id }}" selected>{{ $dl->nama_lokasi }}</option>
                                            @else
                                            <option value="{{ $dl->id }}">{{ $dl->nama_lokasi }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('lokasi_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="tgl_lahir">Tanggal Lahir</label>
                                    <input type="datetime" class="form-control @error('tgl_lahir') is-invalid @enderror" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', $karyawan->tgl_lahir) }}">
                                    @error('tgl_lahir')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <?php $gender = array(
                                    [
                                        "gender" => "Laki-Laki"
                                    ],
                                    [
                                        "gender" => "Perempuan"
                                    ]);
                                    ?>
                                    <label for="gender">Gender</label>
                                    <select name="gender" id="gender" class="form-control selectpicker" data-live-search="true">
                                        @foreach ($gender as $g)
                                            @if(old('gender', $karyawan->gender) == $g["gender"])
                                            <option value="{{ $g["gender"] }}" selected>{{ $g["gender"] }}</option>
                                            @else
                                            <option value="{{ $g["gender"] }}">{{ $g["gender"] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('gender')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="tgl_join">Tanggal Masuk Perusahaan</label>
                                    <input type="datetime" class="form-control @error('tgl_join') is-invalid @enderror" id="tgl_join" name="tgl_join" value="{{ old('tgl_join', $karyawan->tgl_join) }}">
                                    @error('tgl_join')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <?php $sNikah = array(
                                    [
                                        "status" => "Menikah"
                                    ],
                                    [
                                        "status" => "Lajang"
                                    ]);
                                    ?>
                                    <label for="status_nikah">Status</label>
                                    <select name="status_nikah" id="status_nikah" class="form-control selectpicker" data-live-search="true">
                                        @foreach ($sNikah as $s)
                                        @if(old('status_nikah', $karyawan->status_nikah) == $s["status"])
                                        <option value="{{ $s["status"] }}" selected>{{ $s["status"] }}</option>
                                        @else
                                        <option value="{{ $s["status"] }}">{{ $s["status"] }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @error('status_nikah')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="jabatan_id">Jabatan</label>
                                    <select name="jabatan_id" id="jabatan_id" class="form-control selectpicker" data-live-search="true">
                                        @foreach ($data_jabatan as $dj)
                                            @if(old('jabatan_id', $karyawan->jabatan_id) == $dj->id)
                                            <option value="{{ $dj->id }}" selected>{{ $dj->nama_jabatan }}</option>
                                            @else
                                            <option value="{{ $dj->id }}">{{ $dj->nama_jabatan }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('jabatan_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <?php $is_admin = array(
                                    [
                                        "is_admin" => "admin"
                                    ],
                                    [
                                        "is_admin" => "user"
                                    ]);
                                    ?>
                                    <label for="is_admin">Level User</label>
                                    <select name="is_admin" id="is_admin" class="form-control selectpicker" data-live-search="true">
                                        @foreach ($is_admin as $a)
                                            @if(old('is_admin', $karyawan->is_admin) == $a["is_admin"])
                                            <option value="{{ $a["is_admin"] }}" selected>{{ $a["is_admin"] }}</option>
                                            @else
                                            <option value="{{ $a["is_admin"] }}">{{ $a["is_admin"] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('is_admin')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="cuti_dadakan">Cuti Dadakan</label>
                                    <input type="number" class="form-control @error('cuti_dadakan') is-invalid @enderror" id="cuti_dadakan" name="cuti_dadakan" value="{{ old('cuti_dadakan', $karyawan->cuti_dadakan) }}">
                                    @error('cuti_dadakan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="cuti_bersama">Cuti Bersama</label>
                                    <input type="number" class="form-control @error('cuti_bersama') is-invalid @enderror" id="cuti_bersama" name="cuti_bersama" value="{{ old('cuti_bersama', $karyawan->cuti_bersama) }}">
                                    @error('cuti_bersama')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="cuti_menikah">Cuti Menikah</label>
                                    <input type="number" class="form-control @error('cuti_menikah') is-invalid @enderror" id="cuti_menikah" name="cuti_menikah" value="{{ old('cuti_menikah', $karyawan->cuti_menikah) }}">
                                    @error('cuti_menikah')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="cuti_diluar_tanggungan">Cuti Diluar Tanggungan</label>
                                    <input type="number" class="form-control @error('cuti_diluar_tanggungan') is-invalid @enderror" id="cuti_diluar_tanggungan" name="cuti_diluar_tanggungan" value="{{ old('cuti_diluar_tanggungan', $karyawan->cuti_diluar_tanggungan) }}">
                                    @error('cuti_diluar_tanggungan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="cuti_khusus">Cuti Khusus</label>
                                    <input type="number" class="form-control @error('cuti_khusus') is-invalid @enderror" id="cuti_khusus" name="cuti_khusus" value="{{ old('cuti_khusus', $karyawan->cuti_khusus) }}">
                                    @error('cuti_khusus')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="cuti_melahirkan">Cuti Melahirkan</label>
                                    <input type="number" class="form-control @error('cuti_melahirkan') is-invalid @enderror" id="cuti_melahirkan" name="cuti_melahirkan" value="{{ old('cuti_melahirkan', $karyawan->cuti_melahirkan) }}">
                                    @error('cuti_melahirkan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="izin_telat">Izin Telat</label>
                                    <input type="number" class="form-control @error('izin_telat') is-invalid @enderror" id="izin_telat" name="izin_telat" value="{{ old('izin_telat', $karyawan->izin_telat) }}">
                                    @error('izin_telat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="izin_pulang_cepat">Izin Pulang Cepat</label>
                                    <input type="number" class="form-control @error('izin_pulang_cepat') is-invalid @enderror" id="izin_pulang_cepat" name="izin_pulang_cepat" value="{{ old('izin_pulang_cepat', $karyawan->izin_pulang_cepat) }}">
                                    @error('izin_pulang_cepat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="alamat">Alamat</label>
                                    <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" value="{{ old('alamat', $karyawan->alamat) }}">
                                    @error('alamat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="golongan_id">Divisi</label>
                                    <select name="golongan_id" id="golongan_id" class="form-control selectpicker" data-live-search="true">
                                        <option value="">Pilih Divisi</option>
                                        @foreach ($data_golongan as $dg)
                                            @if(old('golongan_id', $karyawan->golongan_id) == $dg->id)
                                                <option value="{{ $dg->id }}" selected>{{ $dg->name }}</option>
                                            @else
                                                <option value="{{ $dg->id }}">{{ $dg->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('golongan_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <!--<div class="form-row">-->
                            <!--    <div class="col">-->
                            <!--        <label for="upah_normal">Upah Normal</label>-->
                            <!--        <input type="text" class="form-control @error('upah_normal') is-invalid @enderror" id="upah_normal" name="upah_normal" value="{{ old('upah_normal', $karyawan->upah_normal) }}">-->
                            <!--        @error('upah_normal')-->
                            <!--        <div class="invalid-feedback">-->
                            <!--            {{ $message }}-->
                            <!--        </div>-->
                            <!--        @enderror-->
                            <!--    </div>-->
                            <!--</div>-->
                            <br>
                            <button type="submit" class="btn btn-primary float-right">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Data Upah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-upah" data-id="">
                        <div class="form-row">
                            <div class="col">
                                <label for="upah_target">Upah Target</label>
                                <input type="text" class="form-control @error('upah_target') is-invalid @enderror" id="upah_target">
                                @error('upah_target')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="masuk_minggu">Masuk Minggu</label>
                                <input type="text" class="form-control @error('minggu_masuk') is-invalid @enderror" id="masuk_minggu" name="masuk_minggu">
                                @error('masuk_minggu')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class='form-row'>
                            
                            <div class="col">
                                <label for="lembur">Lembur</label>
                                <input type="text" class="form-control @error('lembur') is-invalid @enderror" id="lembur" name="lembur">
                                @error('lembur')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="kasbon">Kasbon</label>
                                <input type="text" class="form-control @error('kasbon') is-invalid @enderror" id="kasbon" name="kasbon">
                                @error('kasbon')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class="col">
                                <label for="denda">Denda</label>
                                <input type="text" class="form-control @error('denda') is-invalid @enderror" id="denda" name="denda">
                                @error('denda')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal">
                                @error('tanggal')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-edit">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Konfirmasi Delete -->
    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Penghapusan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Hapus</button>
            </div>
            </div>
        </div>
    </div>

    <br>
    @push('script')
    <script>
        $(document).ready(function() {
            $('#foto_karyawan').on('change', function(event) {
                const [file] = event.target.files;
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#foto-preview').attr('src', e.target.result);
                    $('#img_enc').val(e.target.result);
                };
                reader.readAsDataURL(file);
            });

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            $('#upah_normal').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#upah_target').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#modal-edit #upah_target').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#masuk_minggu').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#modal-edit #masuk_minggu').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#lembur').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#modal-edit #lembur').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#kasbon').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#modal-edit #kasbon').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#denda').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            $('#modal-edit #denda').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // Hapus non-digit
                if (value) {
                    value = formatNumber(value); // Format angka
                }
                $(this).val(value); // Set nilai input
            });
            

        });
    </script>
        <script type="text/javascript">
            
            $("#table-upah-input").DataTable({
                responsive: true
            });
            function formatRupiah(value) {
                let formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
                return formatted;
            }
            var table = $("#table-upah").DataTable({
                processing: true,
                // serverSide: true,
                ajax: {
                    url: '{{ route('get-upah-karyawan') }}',
                    data: function(d){
                        d.user_id = $("#user_id").val();
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
                    {data: 'tanggal', name: 'tanggal'},
                    {data: 'upah_normal', name: 'upah_normal',
                        render: function(data, type, row){
                            return formatRupiah(data);
                        }
                    },
                    {data: 'upah_target', name: 'upah_target',
                        render: function(data, type, row){
                            return formatRupiah(data);
                        }
                    },
                    {data: 'masuk_minggu', name: 'masuk_minggu',
                        render: function(data, type, row){
                            return formatRupiah(data);
                        }
                    },
                    {data: 'lembur', name: 'lembur',
                        render: function(data, type, row){
                            return formatRupiah(data);
                        }
                    },
                    {data: 'kasbon', name: 'kasbon',
                        render: function(data, type, row){
                            return formatRupiah(data);
                        }
                    },
                    {data: 'denda', name: 'denda',
                        render: function(data, type, row){
                            return formatRupiah(data);
                        }
                    },
                    {data: null, name: 'aksi', render: function(data, type, row){
                        return `
                        <a class="btn btn-info edit-data" data-id="${data.id}">
                            <i class="fa fa-edit fa-xs"></i>
                        </a>
                        <a class="btn btn-danger delete-data" data-id="${data.id}">
                            <i class="fa fa-trash fa-xs"></i>
                        </a>`;
                    }}
                    
                ],
            });
            $("#table-upah").on('click', '.edit-data', function(){
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route("get-upah-by-id", ":id") }}'.replace(':id', id), // Ganti :id dengan ID dinamis
                    method: 'GET',
                    success: function(data) {
                        // Isi data ke dalam form modal
                        $('#form-edit-upah').data('id', id); // Simpan ID di form
                        $('#modal-edit #tanggal').val(new Date(data.created_at).toISOString().split('T')[0]);
                        // $('#modal-edit #upah_normal').val(data.upah_normal);
                        $('#modal-edit #upah_target').val(data.upah_target);
                        $('#modal-edit #masuk_minggu').val(data.masuk_minggu);
                        $('#modal-edit #lembur').val(data.lembur);
                        $('#modal-edit #kasbon').val(data.kasbon);
                        $('#modal-edit #denda').val(data.denda);
                        
                        // Tampilkan modal
                        $('#modal-edit').modal('show');
                    },
                    error: function(xhr) {
                        alert('Failed to retrieve data: ' + xhr.responseText);
                    }
                });
            });
            $('#table-upah').on('click', '.delete-data', function(){
                var id = $(this).data('id');
                $('#modal-delete').data('id', id); // Simpan ID ke modal
                $('#modal-delete').modal('show'); // Tampilkan modal konfirmasi
            });
            
            $('#btn-save').on('click', function(e){
               
                e.preventDefault();
                // var upah_normal = $('#upah_normal').val();
                var upah_target = $('#upah_target').val().replace(/\./g, '');
                var masuk_minggu = $('#masuk_minggu').val().replace(/\./g, '');
                var lembur = $('#lembur').val().replace(/\./g, '');
                var kasbon = $('#kasbon').val().replace(/\./g, '');
                var denda = $('#denda').val().replace(/\./g, '');
                var user_id = $('#user_id').val();
                 console.log(upah_target,masuk_minggu,lembur,kasbon,denda,user_id);
             
                $.ajax({
                    url: '{{ route("save-upah-karyawan") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: user_id,
                        // upah_normal: upah_normal,
                        upah_target: upah_target,
                        masuk_minggu: masuk_minggu,
                        lembur: lembur,
                        kasbon: kasbon,
                        denda: denda
                    },
                    success: function(response){
                        if(response.success){
                            // $('#upah_normal').val('');
                            $('#upah_target').val('');
                            $('#masuk_minggu').val('');
                            $('#lembur').val('');
                            $('#kasbon').val('');
                            $('#denda').val('');
                            table.ajax.reload();
                        }else{
                            alert('Error: '+ response.message);
                        }
                        alert('Berhasil Simpan Payroll');
                    },
                    error: function(xhr){
                        alert('An error occured: '+ xhr.responseText);
                    }
                })
            });
            // Event listener untuk tombol "Save changes"
            $('#save-edit').on('click', function() {
                // Ambil data dari form modal
                var id = $('#form-edit-upah').data('id'); // Simpan ID di form atau di suatu tempat
                var data = {
                    tanggal: $('#modal-edit #tanggal').val(),
                    // upah_normal: $('#modal-edit #upah_normal').val(),
                    upah_target: $('#modal-edit #upah_target').val().replace(/\./g, ''),
                    masuk_minggu: $('#modal-edit #masuk_minggu').val().replace(/\./g, ''),
                    lembur: $('#modal-edit #lembur').val().replace(/\./g, ''),
                    kasbon: $('#modal-edit #kasbon').val().replace(/\./g, ''),
                    denda: $('#modal-edit #denda').val().replace(/\./g, ''),
                    _token: '{{ csrf_token() }}' // Sertakan CSRF token untuk keamanan
                };

                // Kirimkan data ke server menggunakan AJAX
                $.ajax({
                    url: '{{ route("update-upah-karyawan", ":id") }}'.replace(':id', id), // Ganti :id dengan ID dinamis
                    method: 'PUT', // Gunakan metode PUT untuk update
                    data: data,
                    success: function(response) {
                        // Tampilkan pesan sukses
                        alert('Data berhasil diupdate!');
                        
                        // Refresh DataTable untuk memuat data terbaru
                        $('#table-upah').DataTable().ajax.reload();
                        
                        // Tutup modal
                        $('#modal-edit').modal('hide');
                    },
                    error: function(xhr) {
                        // Tampilkan pesan error
                        alert('Terjadi kesalahan: ' + xhr.responseText);
                    }
                });
            });
            $('#confirm-delete').on('click', function(){
                var id = $('#modal-delete').data('id'); // Ambil ID dari modal

                // Kirimkan data ke server menggunakan AJAX
                $.ajax({
                    url: '{{ route("delete-upah-karyawan", ":id") }}'.replace(':id', id), // Ganti :id dengan ID dinamis
                    method: 'DELETE', // Gunakan metode PUT untuk update
                    data: {
                        _token: '{{ csrf_token() }}' // Sertakan CSRF token untuk keamanan
                    },
                    success: function(response) {
                        // Tampilkan pesan sukses
                        alert('Data berhasil dihapus!');
                        
                        // Refresh DataTable untuk memuat data terbaru
                        $('#table-upah').DataTable().ajax.reload();
                        
                        // Tutup modal
                        $('#modal-delete').modal('hide');
                    },
                    error: function(xhr) {
                        // Tampilkan pesan error
                        alert('Terjadi kesalahan: ' + xhr.responseText);
                    }
                });
            });
            
        </script>
    @endpush
@endsection
