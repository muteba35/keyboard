<style>
    #dropzone {
        background: #f8f9fa;
        transition: 0.3s ease-in-out;
        border: 3px dashed #6c757d;
    }

    #dropzone:hover {
        background: #e9ecef;
    }

    .drop-icon {
        font-size: 4rem;
        color: #28a745;
    }

    .alert-success-custom {
        background-color: #d4edda;
        border-left: 5px solid #28a745;
        color: #155724;
        padding: 1rem;
        border-radius: 5px;
        text-align: center;
        margin-top: 1rem;
        font-weight: 500;
        display: none;
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

                        <a class="nav-link" href="index.html">
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
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError">
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
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card mt-5">
                        <div class="card-header bg-success text-white text-center">
                            <h4 class="mb-0">📥 Importation des identifiants via CSV</h4>
                        </div>

                        <div class="card-body text-center">
                            <p>Glissez ou sélectionnez un fichier <strong>CSV</strong> contenant vos identifiants.</p>

                            {{-- ✅ Erreur critique (ex: mauvais fichier) --}}
                            @if ($errors->has('import_error'))
                                <div class="alert alert-danger border-start border-danger border-5">
                                    <strong>Erreur :</strong> {{ $errors->first('import_error') }}
                                </div>
                            @endif

                            {{-- ⚠️ Erreurs détaillées (ligne par ligne) --}}
                            @if (session('import_errors'))
                                <div class="alert alert-warning border-start border-warning border-5">
                                    <strong>⚠️ Erreurs détectées :</strong>
                                    <ul class="mt-2 mb-0 text-start">
                                        @foreach (session('import_errors') as $erreur)
                                            <li>{{ $erreur }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- ✅ Message succès --}}
                            @if (session('import_success'))
                                <div class="alert alert-success border-start border-success border-5">
                                    ✅ {{ session('import_success') }}
                                </div>
                            @endif

                            {{-- 🌐 Formulaire d’import --}}
                            <form action="{{ route('identifiants.import.excel') }}" method="POST" enctype="multipart/form-data" id="dropForm">
                                @csrf
                                <label for="excelFile" id="dropzone" class="d-block rounded p-5 text-muted" style="cursor:pointer;">
                                    <div class="drop-icon"><i class="fas fa-file-csv"></i></div>
                                    <p class="mt-3">Cliquez ou glissez un fichier CSV ici<br><small>(.csv uniquement)</small></p>
                                    <div id="fileNameDisplay" class="text-success fw-bold"></div>
                                    <input type="file" id="excelFile" name="excel_file" accept=".csv" style="display: none;" onchange="handleFileUpload()">
                                </label>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

    {{-- Script JS pour nom + soumission auto --}}
    <script>
        const dropForm = document.getElementById('dropForm');
        const fileInput = document.getElementById('excelFile');
        const display = document.getElementById('fileNameDisplay');
        let alreadySubmitted = false;

        function handleFileUpload() {
            const file = fileInput.files[0];
            if (file && display) {
                display.textContent = file.name;
            }

            if (!alreadySubmitted && file) {
                alreadySubmitted = true;
                dropForm.submit();
            }
        }

        // Disparition des alertes après 5 secondes
        window.onload = function () {
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = 0;
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        };
    </script>
</x-app-layout>