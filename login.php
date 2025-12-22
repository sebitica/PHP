<?php
require_once 'config/database.php';
require_once 'config/csrf.php';

session_start();
$database = new Database();
$db = $database->getConnection();
$message = "";

$siteKey = "########################################"; 
$secretKey = "########################################";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf(); 

    $recaptcha_response = $_POST['g-recaptcha-response'];
    $verify_url = "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptcha_response}";
    $resp = json_decode(file_get_contents($verify_url));

    if (!$resp->success) {
        $message = "<p style='color:red'>❌ Verificare robot eșuată.</p>";
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $query = "SELECT id_user, username, parola, rol FROM Users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $row['parola'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_id'] = $row['id_user'];
                
                $_SESSION['role'] = $row['rol'];        
                $_SESSION['rol'] = $row['rol'];

                header("Location: admin/dashboard.php");
                exit;
            } else {
                $message = "<p style='color: red;'>Parolă incorectă.</p>";
            }
        } else {
            $message = "<p style='color: red;'>Utilizatorul nu există.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body style="text-align: center; margin-top: 50px; font-family: sans-serif; background-color: #fcba03">
    <h2>Autentificare</h2>
    <?php echo $message; ?>
    
    <form action="login.php" method="POST" style="display: inline-block; padding: 20px; border: 1px solid #ccc; background-color: #ffffff; border-radius: 10px; min-width: 300px;">
        <?php csrf_field(); ?>
        
        <label>Username:</label><br>
        <input type="text" name="username" required style="width: 90%; padding: 8px; margin-bottom: 10px;"><br>
        
        <label>Parolă:</label><br>
        <div style="position: relative; width: 90%; margin: 0 auto; margin-bottom: 15px;">
            <input type="password" name="password" id="password" required 
                   style="width: 100%; padding: 8px; padding-right: 35px; box-sizing: border-box;">
            
            <span id="togglePassword" 
                  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none;">
                👁️
            </span>
        </div>
        
        <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>" style="display: inline-block;"></div>
        <br><br>
        
        <input type="submit" value="Intră în Cont" style="padding: 10px 20px; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 5px;">
        
        <p><a href="register.php">Nu ai cont? Înregistrează-te</a></p>
        <p><a href="index.html">Înapoi la Meniu</a></p>
    </form>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
</body>
</html>
