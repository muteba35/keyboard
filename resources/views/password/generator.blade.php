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
            <main class="container-fluid px-4 py-6">
                <div class="password-generator">
                     <h2 style="text-align:center; font-weight:400; margin-bottom: 1rem; font-size: 1.5rem;">Générateur de mot de passe</h2>

                    <div class="password-input-wrapper mb-4 text-center">
                        <input type="text" id="generated-password" readonly class="generated-input" value="Votre mot de passe">
                    </div>

                     <!-- ✅ Badge force du mot de passe -->
                    <div id="password-strength-badge" class="mb-3 text-start" style="font-weight: 600; font-size: 14px;"></div>

                        <div class="space-y-4 mb-6">
                             <div class="option-item flex-col w-full">
                            <label for="length" class="option-label mb-2">
                                Longueur : <span id="length-display" class="font-bold text-blue-600">20</span>
                           <input type="range" min="8" max="64" value="20" id="length" class="range-slider w-full mt-2">
                            </label>
        
                        </div>

                            <div class="option-item">
                                <label for="uppercase" class="option-label">Utilisiez des lettres Majuscules (A-Z)</label>
                                <label class="switch">
                                    <input type="checkbox" id="uppercase" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="option-item">
                                <label for="numbers" class="option-label">Utilisiez des chiffres (0-9)</label>
                                <label class="switch">
                                    <input type="checkbox" id="numbers" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="option-item">
                                <label for="symbols" class="option-label">Utillisez des symboles (@!&*)</label>
                                <label class="switch">
                                    <input type="checkbox" id="symbols" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <hr>
<div class="action-buttons">
                    <button onclick="copyPassword()" class="btn btn-copy"><i class="fas fa-copy"></i> Copier</button>
                    <button onclick="generatePassword()" class="btn btn-generate"><i class="fas fa-sync-alt"></i> Générer</button>
                    <button onclick="savePassword()" class="btn btn-save"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
                </div>
                 <form id="save-password-form" method="POST" action="{{ route('mot_de_passe.genere') }}">
                    @csrf
                    <input type="hidden" id="hidden-password" name="mot_de_passe_genere">
                </form>
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
<style>
        .password-generator {
            width: 100%;
            margin: 3rem auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            padding: 2rem;
        }

        .generated-input {
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            width: 100%;
            font-family: 'Fira Code', monospace;
            font-size: 1.2rem;
            border-radius: 8px;
            background: #f1f5f9;
            color: #1f2937;
            text-align: center;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 26px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e2e8f0;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #10b981;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .range-slider {
            width: 100%;
            height: 8px;
            border-radius: 4px;
            background: #e2e8f0;
            outline: none;
        }

        .range-slider::-webkit-slider-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #10b981;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 15px;
        }

        .btn-copy {
            background-color: #3b82f6;
            color: white;
        }

        .btn-copy:hover {
            background-color: #2563eb;
        }

        .btn-generate {
            background-color: #10b981;
            color: white;
        }

        .btn-generate:hover {
            background-color: #059669;
        }

        .btn-save {
            background-color: #64748b;
            color: white;
        }

        .btn-save:hover {
            background-color: #475569;
        }

        .option-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: background 0.2s;
            margin-bottom: 1.25rem;
        }

        .option-label {
            font-weight: 500;
            color: #334155;
        }

        .action-buttons {
            margin-top: 2.5rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .progress-bar-container {
            width: 100%;
            height: 12px;
            background-color: #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: #3b82f6;
            transition: width 0.3s ease-in-out;
        }

        
           .badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        .badge.bg-success { background-color: #10b981; color: white; }
        .badge.bg-warning { background-color: #facc15; color: #1f2937; }
        .badge.bg-danger  { background-color: #ef4444; color: white; }
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
    
    <script>
      const passwordDisplay = document.getElementById('generated-password');
      const lengthInput     = document.getElementById('length');
      const lengthDisplay   = document.getElementById('length-display');
      const badgeContainer  = document.getElementById('password-strength-badge');

      lengthInput.addEventListener('input', () => {
        lengthDisplay.textContent = lengthInput.value;
      });

      function generatePassword() {
        const length          = +lengthInput.value;
        const inclUppercase   = document.getElementById('uppercase').checked;
        const inclNumbers     = document.getElementById('numbers').checked;
        const inclSymbols     = document.getElementById('symbols').checked;
        const lowercase       = 'abcdefghijklmnopqrstuvwxyz';
        const uppercase       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const numbers         = '0123456789';
        const symbols         = '@!&*%$#';
        let   charset         = lowercase
                                + (inclUppercase ? uppercase : '')
                                + (inclNumbers   ? numbers   : '')
                                + (inclSymbols   ? symbols   : '');

        if (!charset) {
          passwordDisplay.value = 'Sélectionnez un critère';
          updateBadge('', '', '');
          return;
        }

        let pwd = '';
        for (let i = 0; i < length; i++) {
          pwd += charset[Math.floor(Math.random() * charset.length)];
        }
        passwordDisplay.value = pwd;

         // Mise à jour de la force après génération
  updateStrength();
    }
    // Fonction d’analyse de la force du mot de passe
function updateStrength() {
  const length        = +lengthInput.value;
  const inclUppercase = document.getElementById('uppercase').checked;
  const inclNumbers   = document.getElementById('numbers').checked;
  const inclSymbols   = document.getElementById('symbols').checked;

  const activeConditions = [inclUppercase, inclNumbers, inclSymbols].filter(Boolean).length;

if (
    length >= 20 || // Longueur seule suffit pour être fort
    (length >= 16 && activeConditions === 3) || // Tous les toggles actives + longueur >= 16
    (length >= 20 && activeConditions >= 1) || // Longueur >= 20 + au moins 1 toggle
    (length >= 19 && activeConditions >= 2) ||// ou longueur =19 + au moins 2 toggles
    (length >= 11 && length <= 19) &&
    (activeConditions ===2  || activeConditions === 3)//// ou longueur <= 19 + au moins 2 toggles
) {
    text = 'Mot de passe fort';
    cls = 'badge bg-success';
    icon = '✅';
}
// Cas MOYEN
else if (
    (length >= 11 && length <= 19) &&
    (activeConditions === 0 || activeConditions === 1)
) {
    text = 'Mot de passe moyen';
    cls = 'badge bg-warning';
    icon = '⚠️';
}


// Cas FAIBLE
else {
    text = 'Mot de passe faible';
    cls = 'badge bg-danger';
    icon = '❌';
}

updateBadge(text, cls, icon);
      }

   // Mise à jour du badge visuel
function updateBadge(text, className, icon) {
  badgeContainer.innerHTML = '';  // clear
  if (text) {
    const span = document.createElement('span');
    span.className   = className;
    span.textContent = icon + ' ' + text;
    badgeContainer.appendChild(span);
  }
}

      function copyPassword() {
        const txt = passwordDisplay.value;
        if (txt && txt !== 'Votre mot de passe') {
          navigator.clipboard.writeText(txt).then(() => {
            const orig = passwordDisplay.value;
            passwordDisplay.value = 'Copié !';
            setTimeout(() => passwordDisplay.value = orig, 1500);
          });
        }
      }

    
function savePassword() {
    const passwordDisplay = document.getElementById('generated-password');
    const password = passwordDisplay.value;

    if (password === '' || password === 'Votre mot de passe') {
        alert("Générez d'abord un mot de passe.");
        return;
    }

    // ✅ Feedback visuel "Enregistré !"
    const originalPassword = passwordDisplay.value;
    passwordDisplay.value = 'Enregistré !';
    setTimeout(() => passwordDisplay.value = originalPassword, 1500);

    // ✅ Injecte dans le champ caché du formulaire
    document.getElementById('hidden-password').value = password;

    // ✅ Soumet le formulaire caché
    document.getElementById('save-password-form').submit();
}



      // auto-génération au chargement
      window.onload = generatePassword;

      // ✅ Mise à jour dynamique en fonction des changements
lengthInput.addEventListener('input', updateStrength);
document.getElementById('uppercase').addEventListener('change', updateStrength);
document.getElementById('numbers').addEventListener('change', updateStrength);
document.getElementById('symbols').addEventListener('change', updateStrength);
    </script>
</x-app-layout>