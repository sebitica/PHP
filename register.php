<?php
require_once 'config/database.php';
require_once 'config/csrf.php'; 

$database = new Database();
$db = $database->getConnection();
$message = "";

$siteKey = "########################################"; 
$secretKey = "########################################";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    verify_csrf(); 

    $recaptcha_response = $_POST['g-recaptcha-response'];
    $verify_url = "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptcha_response}";
    $response_data = json_decode(file_get_contents($verify_url));

    if (!$response_data->success) {
        $message = "<p style='color:red'>❌ Te rog bifează că nu ești robot!</p>";
    } else {
        $username = htmlspecialchars(strip_tags($_POST['username']));
        $password = $_POST['password'];
        
        $check_query = "SELECT id_user FROM Users WHERE username = :username";
        $stmt_check = $db->prepare($check_query);
        $stmt_check->bindParam(':username', $username);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            $message = "<p style='color:red'>Acest username există deja.</p>";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $rol = 'user'; 

            $query = "INSERT INTO Users (username, parola, rol) VALUES (:user, :pass, :rol)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user', $username);
            $stmt->bindParam(':pass', $password_hash);
            $stmt->bindParam(':rol', $rol);

            if ($stmt->execute()) {
                $message = "<p style='color:green'>Cont creat! <a href='login.php'>Autentifică-te aici</a>.</p>";
            } else {
                $message = "<p style='color:red'>Eroare la creare cont.</p>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head> 
    <meta charset="UTF-8"> 
    <title>Înregistrare</title> 
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> 
</head>
<body style="text-align: center; margin-top: 50px; font-family: sans-serif; background-color: #fcba03;">
    <h2>Înregistrare Utilizator</h2>
    <?php echo $message; ?>
    
    <form action="register.php" method="POST" style="display: inline-block; padding: 20px; border: 1px solid #ccc; background-color: #ffffff; border-radius: 10px; min-width: 300px;">
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
        
        <input type="submit" value="Creează Cont" style="padding: 10px 20px; cursor: pointer; background-color: #28a745; color: white; border: none; border-radius: 5px;">
        
        <p><a href="login.php">Ai deja cont? Login</a></p>
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
