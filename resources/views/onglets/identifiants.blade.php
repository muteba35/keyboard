<style>
       /* Style du titre du modal */
.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
    color: #343a40;
}

/* Style du contenu */
.modal-body {
    padding: 2rem 1.5rem;
}

/* Style des boutons dans le modal */
.modal-body .btn {
    font-size: 1rem;
    font-weight: 500;
    padding: 0.75rem 1.25rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease-in-out;
}

/* Bouton formulaire */
.modal-body .btn-primary {
    background-color: #007bff;
    border: none;
}

.modal-body .btn-primary:hover {
    background-color: #0069d9;
}

/* Bouton glisser fichier Excel */
.modal-body .btn-outline-success {
    border: 2px solid #198754;
    color: #198754;
}

.modal-body .btn-outline-success:hover {
    background-color: #198754;
    color: #fff;
}
.table td.d-flex {
    flex-wrap: nowrap !important; /* empêche l’empilement */
    overflow-x: auto; /* ajoute un défilement si nécessaire */
    gap: 0.25rem;
    justify-content: start;
    white-space: nowrap; /* empêche les retours à la ligne */
}

.table td.d-flex .btn {
    min-width: 35px;
    padding: 6px 8px;
    font-size: 0.75rem;
}

/* Responsive pour petits écrans */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 1rem auto;
    }
    .modal-body .btn {
        font-size: 0.95rem;
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
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Identifiants</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Identifiants</li>
                    </ol>
                    @if(session('identifiant_supp'))
            <div class="alert alert-success" id="identifiantAlert"
                 style="position: fixed; top: 20px; right: 20px; z-index: 1050;">
                ✅ {{ session('identifiant_supp') }}
            </div>
        @endif
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-list me-1"></i>
                            Liste des identifiants enregistrés
                        </div>
                        <div class="card-body">

                            <div class="d-flex justify-content-between mb-3">
                                <a href="{{ route('identifiants-create') }}" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fas fa-plus-circle"></i> Créer un nouvel identifiant
                                </a>
                            </div>

                            <table id="datatablesSimple" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Service</th>
                                        <th>URL</th>
                                        <th>Mot de passe</th>
                                        <th>Dossier</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($identifiants as $identifiant)
                                    <tr>
                                        <td>{{ $identifiant->nom_utilisateur }}</td>
                                        <td>{{ $identifiant->email }}</td>
                                        <td>{{ $identifiant->service }}</td>
                                        <td>
                                            <a href="{{ $identifiant->url_service }}" target="_blank">
                                                {{ $identifiant->url_service }}
                                            </a>
                                        </td>
                                        <td>{{ $identifiant->mot_de_passe_clair }}</td>
                                        <td>{{ $identifiant->dossier->nom ?? 'Aucun' }}</td>
                                        <td>{{ $identifiant->created_at->format('d/m/Y') }}</td>
                                        <td class="d-flex align-items-center">
                                            <a href="{{ route('identifiants.edit', $identifiant->id) }}" class="btn btn-sm btn-warning" title="Éditer">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('identifiants.destroy', $identifiant->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet identifiant ?')" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
<button class="btn btn-sm btn-dark" title="Copier le mot de passe" onclick="copierMotDePasse('{{ $identifiant->mot_de_passe_clair }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>

                                            <button class="btn btn-sm btn-info" title="Imprimer" onclick="ouvrirModalImpression()">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <!-- Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajouter des identifiants</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body text-center">
        <a href="{{ route('identifiants-create') }}" class="btn btn-primary w-100 mb-3">Ajouter via le formulaire</a>
        <a href="{{ route('identifiants.import.page') }}" class="btn btn-outline-success w-100">Glisser un fichier CSV</a>
      </div>
    </div>
  </div>
</div>

            <!-- Modal Impression -->
            <div id="modalImpression" style="
                display:none;
                position:fixed;
                top:20%;
                left:50%;
                transform:translateX(-50%);
                background:white;
                padding:25px;
                border-radius:15px;
                box-shadow:0 0 30px rgba(0,0,0,0.3);
                z-index:999;
                width:90%;
                max-width:400px;
                box-sizing:border-box;
            ">
                <h3 style="margin-bottom:20px; font-size:18px; text-align:center;">
                    Choisissez le format d’exportation :
                </h3>
                <button onclick="exporter('pdf')" class="btn btn-primary" style="width:100%; margin-bottom:10px;">
                    📄 Format PDF
                </button>
                <button onclick="exporter('word')" class="btn btn-success" style="width:100%; margin-bottom:10px;">
                    📝 Format Word
                </button>
                <button onclick="fermerModalImpression()" class="btn btn-outline-secondary" style="width:100%;">
                    Annuler
                </button>
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
    <script>
        function copierMotDePasse(mot_de_passe_clair) {
            navigator.clipboard.writeText(mot_de_passe_clair)
                .then(() => alert("Mot de passe copié !"))
                .catch(err => alert("Erreur de copie"));
        }

        function ouvrirModalImpression() {
            document.getElementById('modalImpression').style.display = 'block';
        }

        function fermerModalImpression() {
            document.getElementById('modalImpression').style.display = 'none';
        }

        function exporter(format) {
            fermerModalImpression();
            let url = '/exporter-identifiants?format=' + format;
            window.location.href = url;
        }
         document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('identifiantAlert');
            if (toast) {
                setTimeout(() => {
                    toast.classList.add('fade-out');
                    setTimeout(() => toast.remove(), 3000);
                }, 4000);
            }
        });
    </script>
</x-app-layout>