<nav class="main-header navbar navbar-expand navbar-light">
    <ul class="navbar-nav align-items-center">
      <li class="nav-item">
        <a class="nav-link menu-trigger" data-widget="pushmenu" href="#" role="button" aria-label="Toggle Sidebar">
          <i class="fas fa-bars"></i>
        </a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-branding">PT. Alkindi</span>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item mr-2">
        <button type="button" class="theme-toggle-btn" id="themeToggle" aria-label="Toggle dark mode" aria-pressed="false" title="Ubah ke mode gelap">
          <span class="theme-toggle-icon theme-icon-sun">
            <i class="fas fa-sun"></i>
          </span>
          <span class="theme-toggle-icon theme-icon-moon">
            <i class="fas fa-moon"></i>
          </span>
        </button>
      </li>
      <li class="nav-item dropdown user-menu-wrap">
        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle user-pill">
          <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
          <span class="d-none d-sm-inline-block">{{ auth()->user()->name }}</span>
        </a>
        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu dropdown-menu-right border-0 shadow">
          <li>
            <a href="{{ url('/my-profile/edit-password') }}" class="dropdown-item">
              <i class="fa fa-solid fa-key fa-sm fa-fw mr-2"></i> Edit Password
            </a>
          </li>
          <li>
            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Logout
            </a>
          </li>
        </ul>
      </li>
    </ul>
</nav>
