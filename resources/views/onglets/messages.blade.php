<x-app-layout>
     <div id="layoutSidenav">
             <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-gauge"></i></div>
                            Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading">Interface</div>
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
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne">
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
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link collapsed" href="{{ route('historique.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-clock"></i></div>
                                    Historiques
                                </a>
                                <a class="nav-link collapsed" href="{{ route('logs.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-bug"></i></div>
                                    Logs
                                </a>
                                <div class="collapse" id="pagesCollapseError">
                                    <nav class="sb-sidenav-menu-nested nav">
    
                                    </nav>
                                </div>
                            </nav>
                        </div>

                        <div class="sb-sidenav-menu-heading">Addons</div>
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
                <div class="sb-sidenav-footer">
                    <strong><div class="big">Connecté : {{ Auth::user()->name }}</div></strong>
                </div>
            </nav>
        </div>

             <div id="layoutSidenav_content">
            <main class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Notifications liées aux identifiants</h5>
            @if(!$notifications->isEmpty())
                <form method="POST" action="{{ route('notifications.destroyAll') }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer toutes les notifications ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light" title="Tout supprimer">
                        🧹 Tout supprimer
                    </button>
                </form>
            @endif
        </div>

        <div class="card-body">
            @if($notifications->isEmpty())
                <div class="alert alert-info text-center">
                    Aucune notification liée aux identifiants pour le moment.
                </div>
            @else
                <ul class="list-group">
                    @foreach($notifications as $notification)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="w-100">
                                <strong>{{ ucfirst($notification->type) }} :</strong>
                                <div class="mt-1">{{ $notification->message }}</div>
                                <small class="text-muted d-block mt-2">
                                    Reçu le {{ $notification->created_at->format('d/m/Y à H:i') }}
                                </small>
                                @if(!$notification->est_lu)
                                    <span class="badge bg-danger mt-2">Non lu</span>
                                @else
                                    <span class="badge bg-success mt-2">Lu</span>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"  style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')" title="Supprimer">
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
        </div> ?dashboard?
        

              </div>
    
</x-app-layout>