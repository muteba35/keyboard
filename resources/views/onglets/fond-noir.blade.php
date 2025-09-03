<style>
    .service-card {
        border-radius: 10px;
        background: #f7f7f7;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        transition: 0.3s ease-in-out;
        padding: 20px;
        text-align: center;
        height: 100%;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .service-logo {
        width: 60px;
        height: 60px;
        margin-bottom: 15px;
    }

    .service-name {
        font-size: 18px;
        font-weight: bold;
        color: #2d3748;
    }

    .service-username {
        font-size: 14px;
        color: #718096;
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
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 bg-primary text-white">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Identifiants</div>
                <div class="h5 mb-0 font-weight-bold">{{ $totalIdentifiants }}</div>
            </div>
            <div class="card-footer text-white d-flex justify-content-between align-items-center">
                <span>Voir tous</span>
                <i class="fas fa-key"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 bg-warning text-white">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-uppercase mb-1">Mots de passe générés</div>
                <div class="h5 mb-0 font-weight-bold">{{ $totalPasswords }}</div>
            </div>
            <div class="card-footer text-white d-flex justify-content-between align-items-center">
                <span>Voir tous</span>
                <i class="fas fa-lock"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 bg-success text-white">
        <div class="card-body">
            <div class="text-xs font-weight-bold text-uppercase mb-1">Mots de passe forts</div>
            <div class="h5 mb-0 font-weight-bold">{{ $identifiantsForts }}</div>

            @php
                $pourcentageForts = $totalIdentifiants > 0 
                    ? ($identifiantsForts / $totalIdentifiants) * 100 
                    : 0;
            @endphp

            {{ intval($pourcentageForts) }}% du total

            <div class="progress mt-2">
                <div class="progress-bar bg-light" role="progressbar"
                    style="width: {{ intval($pourcentageForts) }}%;"aria-valuenow="{{ intval($pourcentageForts) }}"
                    aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
        <div class="card-footer text-white d-flex justify-content-between align-items-center">
            <span>Voir stats</span>
            <i class="fas fa-shield-alt"></i>
        </div>
    </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2 bg-danger text-white">
            <div class="card-body">
            <div class="text-xs font-weight-bold text-uppercase mb-1">Mots de passe faibles</div>
            <div class="h5 mb-0 font-weight-bold">{{ $identifiantsFaibles }}</div>

            @php
                $pourcentageFaibles = $totalIdentifiants > 0 
                    ? ($identifiantsFaibles / $totalIdentifiants) * 100 
                    : 0;
            @endphp

            {{ intval($pourcentageFaibles) }}% du total

            <div class="progress mt-2">
                <div class="progress-bar bg-light" role="progressbar"
                    style="width: {{ intval($pourcentageFaibles) }}%;"
                    aria-valuenow="{{ intval($pourcentageFaibles) }}"
                    aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
            <div class="card-footer text-white d-flex justify-content-between align-items-center">
                <span>Voir stats</span>
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-area me-1"></i>
                                        Area Chart Example
                                    </div>
                                    <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Bar Chart Example
                                    </div>
                                    <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                                </div>
                            </div>
                        </div>
        
<div class="row mt-4">
    <!-- Facebook -->
    <div class="col-md-3 mb-4">
        <div class="service-card">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook" class="service-logo">
            <div class="service-name">Facebook</div>
            <div class="service-username">mutebajunior@fb.com</div>
        </div>
    </div>

    <!-- Twitter -->
    <div class="col-md-3 mb-4">
        <div class="service-card">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter" class="service-logo">
            <div class="service-name">Twitter</div>
            <div class="service-username">@junior_tweets</div>
        </div>
    </div>

    <!-- Instagram -->
    <div class="col-md-3 mb-4">
        <div class="service-card">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733558.png" alt="Instagram" class="service-logo">
            <div class="service-name">Instagram</div>
            <div class="service-username">@junior_ig</div>
        </div>
    </div>

    <!-- Gmail -->
    <div class="col-md-3 mb-4">
        <div class="service-card">
            <img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" alt="Gmail" class="service-logo">
            <div class="service-name">Gmail</div>
            <div class="service-username">junior.muteba@gmail.com</div>
        </div>
    </div>

    <!-- LinkedIn -->
    <div class="col-md-3 mb-4">
        <div class="service-card">
            <img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" alt="LinkedIn" class="service-logo">
            <div class="service-name">LinkedIn</div>
            <div class="service-username">junior.muteba</div>
        </div>
    </div>

    <!-- GitHub -->
    <div class="col-md-3 mb-4">
        <div class="service-card">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733553.png" alt="GitHub" class="service-logo">
            <div class="service-name">GitHub</div>
            <div class="service-username">mutebajunior</div>
        </div>
    </div>

    <!-- TikTok -->
    <div class="col-md-3 mb-4">
        <div class="service-card">
            <img src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" alt="TikTok" class="service-logo">
            <div class="service-name">TikTok</div>
            <div class="service-username">@junior.tiktok</div>
        </div>
    </div>

    <!-- Outlook -->
    <div class="col-md-3 mb-4">
        <div class="service-card">
            <img src="https://cdn-icons-png.flaticon.com/512/732/732223.png" alt="Outlook" class="service-logo">
            <div class="service-name">Outlook</div>
            <div class="service-username">junior@outlook.com</div>
        </div>
    </div>
</div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2025</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

             <script>
    // Injecter les données PHP depuis le contrôleur dans JS
    const chartData = {
        areaLabels: {!! json_encode($areaLabels) !!},
        areaData: {!! json_encode($areaData) !!},
        barLabels: {!! json_encode($barLabels) !!},
        barData: {!! json_encode($barData) !!},
        pieLabels: {!! json_encode($pieLabels) !!},
        pieData: {!! json_encode($pieData) !!}
    };

    // 📈 Area Chart
    var ctx = document.getElementById("myAreaChart");
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.areaLabels,
            datasets: [{
                label: "Création d'identifiants",
                data: chartData.areaData,
                lineTension: 0.3,
                backgroundColor: "rgba(2,117,216,0.2)",
                borderColor: "rgba(2,117,216,1)",
                pointRadius: 5,
                pointBackgroundColor: "rgba(2,117,216,1)",
                pointBorderColor: "rgba(255,255,255,0.8)",
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(2,117,216,1)",
                pointHitRadius: 50,
                pointBorderWidth: 2
            }]
        },
        options: {
            scales: {
                xAxes: [{
                    gridLines: { display: false },
                    ticks: { maxTicksLimit: 7 }
                }],
                yAxes: [{
                    ticks: { beginAtZero: true, maxTicksLimit: 5 },
                    gridLines: { color: "rgba(0, 0, 0, .125)" }
                }]
            },
            legend: { display: false }
        }
    });

    // 📊 Bar Chart
    var ctx2 = document.getElementById("myBarChart");
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: chartData.barLabels,
            datasets: [{
                label: "Nombre d'identifiants",
                backgroundColor: "rgba(2,117,216,1)",
                borderColor: "rgba(2,117,216,1)",
                data: chartData.barData
            }]
        },
        options: {
            scales: {
                xAxes: [{
                    gridLines: { display: false },
                    ticks: { maxTicksLimit: 6 }
                }],
                yAxes: [{
                    ticks: { beginAtZero: true },
                    gridLines: { display: true }
                }]
            },
            legend: { display: false }
        }
    });

      // 🥧 Pie Chart
    var ctx3 = document.getElementById("myPieChart");
    new Chart(ctx3, {
        type: 'pie',
        data: {
            labels: chartData.pieLabels,
            datasets: [{
                data: chartData.pieData,
                backgroundColor: ['#28a745', '#dc3545']
            }]
        }
    });
</script>
      </div>
        
</x-app-layout>
   