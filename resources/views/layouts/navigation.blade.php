<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
          <a class="navbar-brand ps-3 d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="fas fa-user-shield fa-lg me-2" style="color: #4dabf7;"></i>
                <span class="fw-bold text-white">KeyManage</span>
            </a>
                        <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                
     </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                        <li><a class="dropdown-item" href="{{ route('logs.index') }}">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                              <form method="POST" action="{{ route('logout') }}">
                                @csrf
                        <li><a class="dropdown-item" :href="route('logout')"onclick="event.preventDefault();this.closest('form').submit();">Logout</a></li>
                         </form>
                    </ul>
                </li>
<li class="nav-item">
    <a class="nav-link position-relative" href="{{ route('messages_create') }}">
        <i class="fas fa-bell fa-lg text-secondary" style="position: relative;"></i>
        @if(session('notifications_non_lues', 0) > 0)
            <span class="position-absolute translate-middle badge rounded-pill bg-danger"
                  style="
                    top: 10px;
                    right: -8px;
                    font-size: 0.55rem;
                    width: 16px;
                    height: 16px;
                    padding: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;">
                {{ session('notifications_non_lues') }}
            </span>
        @endif
    </a>
</li>
            </ul>
            </nav>

           
        