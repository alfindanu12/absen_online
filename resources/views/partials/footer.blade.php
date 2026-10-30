<footer class="main-footer app-footer">
    <div class="footer-desktop d-none d-md-flex">
        <span class="footer-brand">Absensi Online PT. Alkindi</span>
        <span class="footer-meta">Sistem Presensi Karyawan</span>
    </div>

    <div class="footer-mobile d-flex d-md-none">
        <a class="footer-link {{ Request::is('dashboard*') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a class="footer-link {{ Request::is('cuti*') ? 'active' : '' }}" href="{{ url('/cuti') }}">
            <i class="fas fa-hourglass-half"></i>
            <span>Cuti</span>
        </a>
        <a class="footer-link {{ Request::is('absen*') ? 'active' : '' }}" href="{{ url('/absen') }}">
            <i class="fas fa-camera"></i>
            <span>Absen</span>
        </a>
        <a class="footer-link {{ Request::is('my-absen*') ? 'active' : '' }}" href="{{ url('/my-absen') }}">
            <i class="fas fa-user-clock"></i>
            <span>History</span>
        </a>
    </div>
</footer>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <form action="{{ url('/logout') }}" method="post">
                    @csrf
                    <button class="btn btn-primary" type="submit">logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
