<style>
    /* 🎨 Carte contenant le formulaire */
    .card-glow {
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        padding: 2rem;
        max-width: 500px;
        margin: auto;
        margin-top: 40px;
        animation: fadeIn 0.5s ease-in-out;
    }

    .card-glow h2 {
        text-align: center;
        color: #4a00e0;
        margin-bottom: 1rem;
        font-weight: bold;
        font-size: 25px;
    }

    /* 🧩 Champs */
    input, textarea, select {
        width: 100%;
        padding: 12px 15px;
        margin-top: 6px;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(138, 43, 226, 0.1);
        transition: box-shadow 0.3s;
        background-color: #f9f9f9;
    }

    input:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 12px rgba(138, 43, 226, 0.4);
    }

    button:hover {
        background: linear-gradient(to right, #7a1fd2, #3a00d0);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 🔔 Alertes pour mot de passe généré */
    .alert-box {
        margin: 1.5rem auto;
        background-color: #007BFF;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        max-width: 600px;
        font-size: 16px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: opacity 1s ease-in-out;
    }

    .alert-box.fade-out {
        opacity: 0;
    }

    /* ✅ Nouvelle alerte pour identifiant ajouté */
    .alert {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 16px;
        margin: 1.5rem auto;
        max-width: 600px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: opacity 1s ease-in-out;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert.fade-out {
        opacity: 0;
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
                  @if(session('identifiant_added'))
                        <div class="toast" id="successToast">
                            ✅ {{ session('identifiant_added') }}
                        </div>
                    @endif

         @if($motDePasseGenere)
                    <div id="session-info" class="alert-box">
                        💡 Un mot de passe généré automatiquement a été inséré dans le champ.
                    </div>
                @endif
                 @if(session('identifiant_succes'))
            <div class="alert alert-success" id="identifiantAlert"
                 style="position: fixed; top: 20px; right: 20px; z-index: 1050;">
                ✅ {{ session('identifiant_succes') }}
            </div>
        @endif
                <main class="min-h-screen flex justify-center items-center bg-gradient-to-r from-purple-500 to-indigo-600">
                <div class="card-glow">
                    <h2>Ajouter un Identifiant</h2>
                    <hr>
                    <form method="POST" action="{{ route('identifiants.store') }}">
                        @csrf

                        <!-- Nom d'utilisateur -->
                        <div class="mb-4">
                           
                            <input type="text" name="nom_utilisateur" :value="old('nom_utilisateur')" placeholder="Username" required autofocus >
                             <x-input-error :messages="$errors->get('nom_utilisateur')" class="mt-1 text-xs" />
                        </div>

                        <div class="mb-4">
                           
                            <input type="text" name="email" :value="old('email')" placeholder="email" required autofocus >
                             <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                        </div>

                        <!-- Mot de passe -->
                        <div class="mb-4">
                    
                            <input type="password" type="password" name="mot_de_passe" placeholder="Mot de passe" required   value="{{ old('mot_de_passe', $motDePasseGenere ?? '') }}" >
                            <x-input-error :messages="$errors->get('mot_de_passe')" class="mt-1 text-xs" />
                        </div>

                        <!-- Service -->
                        <div class="mb-4">
                          
                            <input list="services" name="service" id="service"  placeholder="service"/>
                            <datalist id="services">
                            <option value="Google">
                            <option value="Facebook">
                            <option value="GitHub">
                            <option value="LinkedIn">
                            <option value="Twitter">
                            <option value="Gmail">
                            <option value="Outlook">
                            <option value="Instagram">
                            <option value="Dropbox">
                            <option value="TikTok">
                            <option value="Snapchat">
    </datalist>

                               <x-input-error :messages="$errors->get('service')" class="mt-1 text-xs" />
                        </div>

                        <!-- URL Service -->
                        <div class="mb-4">
                          
                            <input type="url"  id="url_service" type="url" name="url_service" :value="old('url_service')" placeholder="https://exemple.com">
                             <x-input-error :messages="$errors->get('url_service')" class="mt-1 text-xs" />
                        </div>

                        <!-- Nom du dossier -->
                        <div class="mb-4">
                           
                            <input type="text" type="text" name="nom_dossier" :value="old('nom_dossier')" placeholder="nom_dossier" >
                            <x-input-error :messages="$errors->get('nom_dossier')" class="mt-1 text-xs" />
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <textarea name="description" rows="3" placeholder="Petite description du compte..."></textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1 text-xs" />
                        </div>

                        <!-- Bouton -->
                        <div class="flex justify-end mt-4"> 
                            <button type="submit" style=" width: 100%;
    background: linear-gradient(to right, #8e2de2, #4a00e0);
    color: white;
    padding: 12px;
    border: none;
    border-radius: 12px;
    font-weight: bold;
    transition: background 0.3s;">Ajouter Identifiant</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
 <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('identifiantAlert');
            if (toast) {
                setTimeout(() => {
                    toast.classList.add('fade-out');
                    setTimeout(() => toast.remove(), 3000);
                }, 4000);
            }

            const infoBox = document.getElementById('session-info');
            if (infoBox) {
                setTimeout(() => {
                    infoBox.classList.add('fade-out');
                    setTimeout(() => infoBox.remove(), 1000);
                }, 5000);
            }
        });
    </script>
</x-app-layout>
