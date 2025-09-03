<x-app-layout>
    
    <style>
        .historique-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
            overflow-x: auto;
        }

        .historique-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #198754;
            font-weight: 600;
        }

        .historique-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .historique-table th,
        .historique-table td {
            text-align: center;
            vertical-align: middle;
            padding: 1rem;
        }

        .historique-table td.break {
            word-break: break-word;
            max-width: 300px;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 768px) {
            .historique-table {
                border: 0;
                font-size: 14px;
            }

            .historique-table thead {
                display: none;
            }

            .historique-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            }

            .historique-table td {
                display: flex;
                justify-content: space-between;
                padding: 0.75rem 1rem;
                border-bottom: 1px solid #e9ecef;
            }

            .historique-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #198754;
                flex-basis: 45%;
                text-align: left;
            }

            .historique-table td.break {
                word-break: break-word;
                max-width: 100%;
            }
        }
    </style>
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
        <main class="container-fluid">
                <div class="historique-card">
                    <h3 class="historique-header">🕘 Historique de Connexion</h3>

                    <table class="table table-striped table-bordered historique-table">
                        <thead class="table-success">
                            <tr>
                                <th>Date & Heure</th>
                                <th>Adresse IP</th>
                                <th>Navigateur</th>
                                <th>User-Agent Complet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Date & Heure">
                                    {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y à H:i') : 'Jamais connecté' }}
                                </td>
                                <td data-label="Adresse IP">
                                    {{ Auth::user()->last_ip ?? 'N/A' }}
                                </td>
                                <td data-label="Navigateur">
                                    {{ Auth::user()->browser ?? 'N/A' }}
                                </td>
                                <td data-label="User-Agent" class="break">
                                    {{ Auth::user()->user_agent ?? 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
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