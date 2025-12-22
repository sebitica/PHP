<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../login.php"); 
    exit;
}

function isAdmin() {
    return (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');
}
?>
