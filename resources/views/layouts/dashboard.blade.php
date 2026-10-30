<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $title }}</title>
  {{-- logo --}}
  <link rel="stylesheet" href="{{ url('adminlte/plugins/fullcalendar/main.css') }}">

  <script>
    (function () {
      try {
        document.documentElement.setAttribute('data-theme', localStorage.getItem('ui-theme') || 'light');
      } catch (error) {
        document.documentElement.setAttribute('data-theme', 'light');
      }
    })();
  </script>

  <link rel="shorcut icon" href="{{ url('assets/img/Alkindiherbal.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="{{ url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ url('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css') }}">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/jqvmap/jqvmap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('adminlte/dist/css/adminlte.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/daterangepicker/daterangepicker.css') }}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/summernote/summernote-bs4.min.css') }}">

  {{-- select picker --}}
  <link rel="stylesheet" href="{{ url('https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css') }}">

  <!-- DataTables -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ url('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ url('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

  {{-- timepicker --}}
  <link rel="stylesheet" href="{{ url('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css') }}">

  {{-- maps leaflet js --}}
  <link rel="stylesheet" href="{{ url('https://unpkg.com/leaflet@1.8.0/dist/leaflet.css') }}" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin=""/>

  <script src="{{ url('https://unpkg.com/leaflet@1.8.0/dist/leaflet.js') }}" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>

  <link rel="stylesheet" href="{{ url('assets/css/modern-ui.css') }}?v={{ filemtime(public_path('assets/css/modern-ui.css')) }}">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  @stack('style')
</head>
<body class="hold-transition sidebar-mini layout-fixed modern-dashboard">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img src="{{ url('assets/img/Alkindiherbal.png') }}" alt="Alkindi Logo" height="72" width="72">
  </div>

  @include('partials.topbar')

  @include('partials.sidebar')


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @hasSection('hide-page-header')
    @else
      <div class="content-header">
        <div class="container-fluid">
          <div class="page-title-wrap">
            <div>
              <p class="page-kicker mb-1">Absensi Online</p>
              <h1 class="page-title">{{ $title }}</h1>
            </div>
            <div class="page-meta" data-live-date>{{ now()->translatedFormat('l, d F Y') }}</div>
          </div>
        </div>
      </div>
    @endif
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      @yield('isi')
    </section>
  </div>
  <!-- /.content-wrapper -->

  @include('partials.footer')

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

@include('partials.script')
<script>
  (function () {
    const timeNodes = document.querySelectorAll('[data-live-clock]');
    const dateNodes = document.querySelectorAll('[data-live-date]');
    const themeToggle = document.getElementById('themeToggle');
    const root = document.documentElement;

    const syncThemeToggle = function () {
      if (!themeToggle) {
        return;
      }

      const isDark = root.getAttribute('data-theme') === 'dark';
      themeToggle.setAttribute('aria-pressed', String(isDark));
      themeToggle.setAttribute('title', isDark ? 'Ubah ke mode terang' : 'Ubah ke mode gelap');
    };

    const applyTheme = function (theme, animate) {
      root.setAttribute('data-theme', theme);
      syncThemeToggle();

      try {
        localStorage.setItem('ui-theme', theme);
      } catch (error) {}

      if (animate && document.body) {
        document.body.classList.remove('theme-transition');
        window.requestAnimationFrame(function () {
          document.body.classList.add('theme-transition');
          window.clearTimeout(window.__themeTransitionTimer);
          window.__themeTransitionTimer = window.setTimeout(function () {
            document.body.classList.remove('theme-transition');
          }, 450);
        });
      }
    };

    if (themeToggle) {
      syncThemeToggle();
      themeToggle.addEventListener('click', function () {
        const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme, true);
      });
    }

    if (timeNodes.length || dateNodes.length) {
      const timeFormatter = new Intl.DateTimeFormat('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        timeZone: 'Asia/Jakarta'
      });

      const dateFormatter = new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        timeZone: 'Asia/Jakarta'
      });

      const updateClock = function () {
        const now = new Date();
        const timeText = timeFormatter.format(now).replace(/\./g, ':');
        const dateText = dateFormatter.format(now);

        timeNodes.forEach(function (node) {
          node.textContent = timeText;
        });

        dateNodes.forEach(function (node) {
          node.textContent = dateText;
        });
      };

      updateClock();
      setInterval(updateClock, 1000);
    }
  })();
</script>
@stack('script')
@include('sweetalert::alert')

</body>
</html>
