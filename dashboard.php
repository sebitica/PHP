<?php

require_once '../config/check_auth.php';
require_once '../config/database.php';

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
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Casa de Producție</title>
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
    </style>
</head>
<body>

    <div class="header">
        <h1>🎛️ Dashboard Producție</h1>
        <h1>🏠<a href="../index.html" style="color: #ffffff">Inapoi la Meniu</a></h1>
        <div class="user-info">
            Logat ca: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            <span style="opacity: 0.7;">(<?php echo ucfirst($_SESSION['rol']); ?>)</span>
            <a href="../logout.php" class="logout-btn">Deconectare</a>
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
    </div>

    <div class="footer">
        <p>&copy; <?php echo date("Y"); ?> Casa de Producție Muzicală. Sistem securizat.</p>
    </div>

</body>
</html>
