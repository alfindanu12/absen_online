<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }}</title>

  {{-- logo --}}
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
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ url('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ url('adminlte/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/css/modern-ui.css') }}?v={{ filemtime(public_path('assets/css/modern-ui.css')) }}">
  {{-- select picker --}}
  <link rel="stylesheet" href="{{ url('https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css') }}">

  {{-- timepicker --}}
  <link rel="stylesheet" href="{{ url('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css') }}">
  @stack('style')

</head>
<body class="hold-transition login-page modern-login-page">
    <button type="button" class="theme-toggle-btn login-theme-toggle" id="themeToggle" aria-label="Toggle dark mode" aria-pressed="false" title="Ubah ke mode gelap">
        <span class="theme-toggle-icon theme-icon-sun">
            <i class="fas fa-sun"></i>
        </span>
        <span class="theme-toggle-icon theme-icon-moon">
            <i class="fas fa-moon"></i>
        </span>
    </button>

    @yield('auth')
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ url('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ url('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ url('adminlte/dist/js/adminlte.min.js') }}"></script>
    {{-- selectpicker --}}
    <script src="{{ url('https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ url('https://cdn.jsdelivr.net/npm/flatpickr') }}"></script>
    
    <script>
      config = {
          enableTime: true,
          noCalendar: true,
          dateFormat: "H:i",
          time_24hr: true,
      }

      flatpickr("input[type=datetime-local]", config)
      flatpickr("input[type=datetime]", {})
  </script>
    <script>
        $(function(){
          $('form').on('submit', function(){
            $(':input[type="submit"]').prop('disabled', true);
          })
        })
    </script>
    <script>
      (function () {
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

        if (!themeToggle) {
          return;
        }

        syncThemeToggle();
        themeToggle.addEventListener('click', function () {
          const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
          applyTheme(nextTheme, true);
        });
      })();
    </script>

  @stack('script')
</body>
</html>
