@extends('layouts.login')
@section('auth')
<div class="login-card-wrap">
    <div class="login-logo text-center">
        <img src="{{ url('assets/img/Alkindiherbal.png') }}" alt="Alkindi Logo">
        <a href="{{ url('/') }}">Absensi Online PT. Alkindi</a>
    </div>

    <div class="card login-panel">
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session()->has('loginError'))
            <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
                {{ session('loginError') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card-body login-card-body">
            <h2 class="login-title">Masuk Ke Akun</h2>
            <p class="login-caption">Akses dashboard absensi karyawan secara real-time.</p>

            <form action="{{ url('/login-proses') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Username" name="username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-4">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <div class="col-6">
                        <a href="https://wa.me/+6281283080018?text=Hallo Admin, Saya kesulitan untuk Login." class="btn btn-outline-danger btn-block">Bantuan</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="login-note">Pastikan lokasi dan kamera aktif sebelum melakukan absensi.</p>
</div>

<div class="modal fade" id="modal-info" tabindex="-1" role="dialog" aria-labelledby="modalInfoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-danger" id="modalInfoLabel">Perhatian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="mb-3">
                    <li>Pastikan selalu absen saat masuk dan pulang kerja.</li>
                    <li>Wajah terlihat jelas saat absen (tanpa masker).</li>
                    <li>Perhatikan radius lokasi sebelum melakukan absensi.</li>
                </ul>
                <p class="mb-0 text-muted">Jangan abaikan absen, karena setiap detik berharga.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        $("#modal-info").modal('show');
    });
</script>
@endpush
@endsection
