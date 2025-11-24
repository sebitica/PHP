<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: admin/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator</title>
    <style> 
        body { 
        font-family: Arial, sans-serif;
        display: flex;
        justify-content:center;
        align-items: center; 
        min-height: 100vh; 
        background-color: #B3ABFF; 
        margin: 0; 
        }

        .login-container {
        background: white;
        padding: 25px 35px; 
        border-radius: 8px; 
        box-shadow: 0 0 15px rgba(0,0,0,0.1); 
        width: 100%; 
        max-width: 350px;
        }

        h2 {
        text-align: center; 
        color: #343a40; 
        margin-bottom: 20px; 
        }

        input[type="text"], input[type="password"] { 
            width: 100%;
            padding: 10px; 
            margin: 8px 0 15px 0; 
            display: inline-block; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
        }

        input[type="submit"] { 
            background-color: #007bff; 
            color: white; 
            padding: 14px 20px; 
            margin: 10px 0 0 0; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 16px;
        }

        input[type="submit"]:hover { background-color: #0056b3; }

        .error { 
        color: red;
        text-align: center; 
        margin-bottom: 15px; 
        border: 1px solid #f8d7da; 
        background-color: #f8d7da; 
        padding: 8px; 
        border-radius: 4px; 
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Autentificare Administrator</h2>
    
    <?php 
    if (isset($_GET['error'])): 
    ?>
        <p class="error">Nume de utilizator sau parolă incorecte. Încearcă din nou.</p>
    <?php endif; ?>

    <form action="authenticate.php" method="POST">
        <label for="username">Nume Utilizator:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Parolă:</label>
        <input type="password" id="password" name="password" required>
        
        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>
