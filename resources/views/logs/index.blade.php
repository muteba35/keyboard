<style>
  /* ✅ Style général du container */
.container {
    margin-top: 30px;
}

/* ✅ Carte (box contenant les logs) */
.card {
    border-radius: 10px;
    border: none;
}

/* ✅ En-tête de la carte */
.card-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    font-weight: bold;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    box-shadow: 0 3px 5px rgba(0,0,0,0.1);
}

/* ✅ Tableau responsive */
.table {
    border-radius: 8px;
    overflow: hidden;
}

/* ✅ Titres du tableau */
.table thead th {
    background-color: #f8f9fa;
    text-align: center;
    font-weight: 600;
}

/* ✅ Cellules du tableau */
.table td, .table th {
    vertical-align: middle;
    text-align: center;
    padding: 15px;
    font-size: 0.95rem;
}

/* ✅ Alternance des lignes */
.table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* ✅ Badge pour action */
.badge {
    font-size: 0.85rem;
    padding: 6px 10px;
    border-radius: 20px;
}

/* ✅ Badge pour type */
.badge.bg-secondary {
    background-color: #6c757d;
}

/* ✅ Petit effet hover sur les lignes */
.table-hover tbody tr:hover {
    background-color: #eaf3ff;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

/* ✅ Responsive: adapte les cellules */
@media (max-width: 768px) {
    .table td, .table th {
        font-size: 0.85rem;
        padding: 10px;
    }

    .card-header h4 {
        font-size: 1.1rem;
    }

    .badge {
        font-size: 0.75rem;
    }
}
</style>
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
                        Analyse de robustesse
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <strong><div class="big">Connecté : {{ Auth::user()->name }}</div></strong>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
          <main>
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-history me-2"></i>Historique des Logs</h4>
                <span class="badge bg-light text-dark">{{ $logs->count() }} enregistrements</span>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th scope="col">Utilisateur</th>
                            <th scope="col">Action</th>
                            <th scope="col">Description</th>
                            <th scope="col">Type</th>
                            <th scope="col">IP</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="text-center">
                                <i class="fas fa-user me-1"></i> {{ $log->user->name ?? 'Système' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">{{ ucfirst($log->action) }}</span>
                            </td>
                            <td>{{ $log->description }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $log->related_type ?? '-' }}</span>
                            </td>
                            <td class="text-center text-muted">{{ $log->ip_address }}</td>
                            <td class="text-center text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aucun log trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
           <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2025</div>
                        <div>
                            <a href="#">Privacy Policy</a> &middot;
                            <a href="#">Terms & Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</x-app-layout>