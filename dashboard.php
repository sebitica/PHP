<?php
require_once '../config/check_auth.php';
require_once '../config/database.php';

if (!isAdmin()) {
} 

$database = new Database();
$db = $database->getConnection();

try {
    $nr_artisti = $db->query("SELECT COUNT(*) FROM Artisti")->fetchColumn();
    $nr_albume = $db->query("SELECT COUNT(*) FROM Albume")->fetchColumn();
    $nr_piese = $db->query("SELECT COUNT(*) FROM Piese")->fetchColumn();
    $nr_producatori = $db->query("SELECT COUNT(*) FROM Producatori")->fetchColumn();
    $nr_studiouri = $db->query("SELECT COUNT(*) FROM Studiouri")->fetchColumn();
} catch (Exception $e) {
    $nr_artisti = $nr_albume = $nr_piese = $nr_producatori = $nr_studiouri = 0;
}

$nume_artisti = [];
$piese_per_artist = [];

try {
    $query_chart = "SELECT a.nume, COUNT(ap.id_piesa) as total 
                    FROM Artisti a 
                    LEFT JOIN Artisti_Piesa ap ON a.id_artist = ap.id_artist 
                    GROUP BY a.id_artist 
                    LIMIT 10";
    $stmt_chart = $db->prepare($query_chart);
    $stmt_chart->execute();

    while ($row = $stmt_chart->fetch(PDO::FETCH_ASSOC)) {
        $nume_artisti[] = $row['nume'];
        $piese_per_artist[] = $row['total'];
    }
} catch (Exception $e) {
}

$json_labels = json_encode($nume_artisti);
$json_data = json_encode($piese_per_artist);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Casa de Producție</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .header { background-color: #343a40; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .user-info { font-size: 0.9rem; }
        .logout-btn { background-color: #dc3545; color: white; text-decoration: none; padding: 5px 10px; border-radius: 4px; margin-left: 15px; }
        .logout-btn:hover { background-color: #c82333; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .role-banner { padding: 15px; border-radius: 5px; margin-bottom: 30px; border-left: 5px solid; }
        .role-admin { background-color: #fff3cd; border-color: #ffc107; color: #856404; }
        .role-user { background-color: #d1ecf1; border-color: #17a2b8; color: #0c5460; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .card-header { padding: 15px; font-weight: bold; text-transform: uppercase; color: white; }
        .bg-music { background-color: #6f42c1; } 
        .bg-artist { background-color: #007bff; } 
        .bg-album { background-color: #28a745; } 
        .bg-prod { background-color: #fd7e14; } 
        .bg-studio { background-color: #17a2b8; } 
        .card-body { padding: 20px; text-align: center; }
        .count { font-size: 2.5rem; font-weight: bold; color: #333; margin: 0; }
        .label { color: #666; margin-bottom: 15px; display: block; }
        .btn-action { display: inline-block; padding: 8px 16px; background-color: #343a40; color: white; text-decoration: none; border-radius: 4px; font-size: 0.9rem; }
        .btn-action:hover { background-color: #23272b; }
        .footer { text-align: center; margin-top: 50px; color: #aaa; font-size: 0.8rem; }

        .dashboard-columns {
            display: flex;          
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 40px;
            align-items: flex-start; 
        }
        
        .col-left, .col-right {
            flex: 1;                
            min-width: 300px; 
        } 

        .widget-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .widget-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f4f6f9;
            color: #343a40;
        }
        
        .news-scroll {
            
            max-height: 400px;    
            overflow-y: auto;   
            padding-right: 5px; 
        }
        
        .news-scroll::-webkit-scrollbar { width: 6px; }
        .news-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
        .news-scroll::-webkit-scrollbar-thumb { background: #888; border-radius: 3px; }
        .news-scroll::-webkit-scrollbar-thumb:hover { background: #555; }

        .news-item {
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .news-item a { color: #007bff; text-decoration: none; font-weight: bold; }
        .news-item small { color: #888; display: block; margin-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>🎛️ Dashboard Producție</h1>
        <h1>🏠<a href="../../index.html" style="color: #ffffff; font-size: 0.8em; text-decoration: none;">Inapoi la Site</a></h1>
        <div class="user-info">
            Logat ca: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            <span style="opacity: 0.7;">(<?php echo isset($_SESSION['rol']) ? ucfirst($_SESSION['rol']) : 'User'; ?>)</span>
            <a href="../../logout.php" class="logout-btn">Deconectare</a>
        </div>
    </div>

    <div class="container">

        <?php if (isAdmin()): ?>
            <div class="role-banner role-admin">
                <h3>👑 Mod Administrator</h3>
                <p>Aveți acces complet. Puteți <strong>Adăuga</strong>, <strong>Edita</strong> și <strong>Șterge</strong> date din sistem.</p>
            </div>
        <?php else: ?>
            <div class="role-banner role-user">
                <h3>👤 Mod Utilizator Standard</h3>
                <p>Aveți acces Read-Only. Puteți vizualiza catalogul și datele, dar funcțiile de editare sunt dezactivate.</p>
            </div>
        <?php endif; ?>

        <div class="grid">
            
            <div class="card">
                <div class="card-header bg-music">🎵 Piese (Tracks)</div>
                <div class="card-body">
                    <p class="count"><?php echo $nr_piese; ?></p>
                    <span class="label">Piese înregistrate</span>
                    <a href="piese/index.php" class="btn-action">Gestionează Piese →</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-artist">🎤 Artiști</div>
                <div class="card-body">
                    <p class="count"><?php echo $nr_artisti; ?></p>
                    <span class="label">Artiști sub contract</span>
                    <a href="artisti/index.php" class="btn-action">Vezi Artiști →</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-album">💿 Albume</div>
                <div class="card-body">
                    <p class="count"><?php echo $nr_albume; ?></p>
                    <span class="label">Albume lansate</span>
                    <a href="albume/index.php" class="btn-action">Vezi Albume →</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-prod">🎧 Producători</div>
                <div class="card-body">
                    <p class="count"><?php echo $nr_producatori; ?></p>
                    <span class="label">Producători</span>
                    <a href="producatori/index.php" class="btn-action">Gestionează →</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-studio">🎙️ Studiouri</div>
                <div class="card-body">
                    <p class="count"><?php echo $nr_studiouri; ?></p>
                    <span class="label">Studiouri de înregistrare</span>
                    <a href="studiouri/index.php" class="btn-action">Vezi Studiouri →</a>
                </div>
            </div>

        </div> 
        <div class="dashboard-columns">
            
            <div class="col-left">
                <div class="widget-box">
                    <h3 class="widget-title">🌍 Știri Muzicale (Sursă Externă)</h3>
                    <div class="news-scroll">
                        <?php
                        if (file_exists('news_widget.php')) {
                            include 'news_widget.php';
                        } else {
                            echo "<p style='color:orange'>Fișierul news_widget.php lipsește.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-right">
                <div class="widget-box">
                    <h3 class="widget-title">📊 Statistică: Piese per Artist</h3>
                    <canvas id="myChart"></canvas>
                </div>
            </div>

        </div>
        
    </div>

    <div class="footer">
        <p>&copy; Casă de Producție Muzicală</p>
    </div>

    <script>
        const ctx = document.getElementById('myChart');
        
        const labels = <?php echo $json_labels; ?>;
        const dataValues = <?php echo $json_data; ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Număr de Piese',
                    data: dataValues,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 } 
                    }
                }
            }
        });
    </script>

</body>
</html>
