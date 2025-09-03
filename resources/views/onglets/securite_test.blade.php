<x-app-layout>
    <style>
        .page-wrapper {
            min-height: 100vh;
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 0.6s ease-in-out;
        }

        .title {
            font-size: 28px;
            color: #4a00e0;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
        }

        .password-form {
            position: relative;
        }

        .password-form input {
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .password-form input:focus {
            outline: none;
            border-color: #4a00e0;
            box-shadow: 0 0 10px rgba(74, 0, 224, 0.2);
        }

        .password-form button {
            padding: 12px;
            width: 100%;
            background: linear-gradient(to right, #8e2de2, #4a00e0);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .password-form button:hover {
            background: linear-gradient(to right, #7b1fd1, #3e00cc);
        }

        .toggle-eye {
            position: absolute;
            right: 15px;
            top: 14px;
            cursor: pointer;
            color: #4a00e0;
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .form-container { padding: 25px; }
            .title { font-size: 22px; }
            .password-form button { font-size: 14px; }
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
                <div class="page-wrapper">
                    <div class="form-container">
                        <h2 class="title"><i class="fas fa-lock"></i> Analyse de robustesse</h2>
                        <p class="subtitle">Testez si votre mot de passe est suffisamment sécurisé</p>

                        <form method="POST" action="{{ route('securite.test') }}" class="password-form">
                            @csrf
                            <div style="position: relative;">
                                <input type="password" name="mot_de_passe" id="passwordInput" placeholder="Entrez votre mot de passe" required>
                                <i class="fas fa-eye toggle-eye" onclick="togglePassword()"></i>
                            </div>
                            <button type="submit">Analyser</button>
                        </form>
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

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</x-app-layout>