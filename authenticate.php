<?php
session_start();

require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id_user, parola FROM Users WHERE username = :username LIMIT 1";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['parola'])) {

        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id_utilizator'];
        
        header('Location: admin/dashboard.php');
        exit();
    } else {
        header('Location: login.php?error=1');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
