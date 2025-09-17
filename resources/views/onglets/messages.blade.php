<x-app-layout>
    <div id="layoutSidenav" class="d-flex">
        <!-- Sidebar -->
        <div id="layoutSidenav_nav" class="shadow-lg">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading text-uppercase">Core</div>
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-gauge"></i></div>
                            Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading text-uppercase">Interface</div>
                        <a class="nav-link" href="{{ route('identifiants') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                            Identifiants
                        </a>

                        <a class="nav-link" href="{{ route('password.generator') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-wrench"></i></div>
                            Générateur
                        </a>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-palette"></i></div>
                            Fonds
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="{{ route('fond-noir') }}">Fond noir</a>
                                <a class="nav-link" href="{{ route('fond-blanc') }}">Fond blanc</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                            Historiques & Logs
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages">
                            <nav class="sb-sidenav-menu-nested nav accordion">
                                <a class="nav-link" href="{{ route('historique.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-clock"></i></div>
                                    Historiques
                                </a>
                                <a class="nav-link" href="{{ route('logs.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-bug"></i></div>
                                    Logs
                                </a>
                            </nav>
                        </div>

                        <div class="sb-sidenav-menu-heading text-uppercase">Addons</div>
                        <a class="nav-link" href="{{ route('messages_create') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-comment-dots"></i></div>
                            Messages
                        </a>

                        <a class="nav-link" href="{{ route('securite_test') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-shield-alt"></i></div>
                            Analyseur
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer bg-dark text-white text-center small">
                    <strong>Connecté : {{ Auth::user()->name }}</strong>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="layoutSidenav_content" class="w-100">
            <main class="container py-5">
                <div class="card border-0 shadow-lg rounded-4 animate__animated animate__fadeInUp" style="overflow:hidden;">
                    <!-- Header modernisé -->
                    <div class="card-header text-white d-flex justify-content-between align-items-center" 
                        style="background: linear-gradient(135deg, #4e73df, #224abe); box-shadow: inset 0 -2px 8px rgba(0,0,0,0.15);">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-bell me-2"></i> Notifications liées aux identifiants
                        </h5>
                        @if(!$notifications->isEmpty())
                            <form method="POST" action="{{ route('notifications.destroyAll') }}" 
                                  onsubmit="return confirm('Voulez-vous vraiment supprimer toutes les notifications ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm rounded-pill shadow-sm hover-scale">
                                    <i class="fas fa-broom me-1"></i> Tout supprimer
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Body modernisé -->
                    <div class="card-body bg-light">
                        @if($notifications->isEmpty())
                            <div class="alert alert-info text-center p-4 rounded-3 shadow-sm animate__animated animate__fadeIn">
                                <i class="fas fa-inbox fa-3x mb-3 text-primary"></i>
                                <h5 class="fw-bold mb-2">Aucune notification</h5>
                                <p class="mb-0 text-muted">Vous n’avez pas encore de notifications liées aux identifiants.</p>
                            </div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($notifications as $notification)
                                    <li class="list-group-item d-flex justify-content-between align-items-start hover-card rounded-3 mb-3 p-3 shadow-sm animate__animated animate__fadeInUp">
                                        <div class="flex-grow-1 pe-3">
                                            <h6 class="fw-bold text-dark mb-1">
                                                <i class="fas fa-tag me-1 text-primary"></i> {{ ucfirst($notification->type) }}
                                            </h6>
                                            <p class="mb-2">{{ $notification->message }}</p>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-clock me-1"></i> Reçu le {{ $notification->created_at->format('d/m/Y à H:i') }}
                                            </small>
                                            @if(!$notification->est_lu)
                                                <span class="badge rounded-pill bg-danger mt-2 px-3 py-1">Non lu</span>
                                            @else
                                                <span class="badge rounded-pill bg-success mt-2 px-3 py-1">Lu</span>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm hover-scale" 
                                                onclick="return confirm('Supprimer cette notification ?')" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Styles et animations -->
    <style>
        .hover-card {
            transition: all 0.35s ease;
            background: white;
            border-left: 4px solid transparent;
        }
        .hover-card:hover {
            background: #f8faff;
            transform: translateY(-6px) scale(1.01);
            border-left: 4px solid #4e73df;
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        .hover-scale {
            transition: all 0.25s ease-in-out;
        }
        .hover-scale:hover {
            transform: scale(1.12);
        }
    </style>

    <!-- Animation CSS (Animate.css) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</x-app-layout>
