<?php
require_once 'stagiaire.php';
require_once 'encadrant.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['login'];
    $password = $_POST['password'];
    
    // Get the referring page to determine login type
    $referer = $_SERVER['HTTP_REFERER'];
    
    if (strpos($referer, 'encadrant.html') !== false) {
        // Encadrant login
        $encadrant = new Encadrant();
        $user = $encadrant->login($email, $password);
        
        if ($user) {
            header('Location: dashboard.php');
            exit;
        } else {
            header('Location: ../html/encadrant.html?error=1');
            exit;
        }
    } else {
        // Stagiaire login
        $stagiaire = new Stagiaire();
        $user = $stagiaire->login($email, $password);
        
        if ($user) {
            header('Location: dashboard.php');
            exit;
        } else {
            header('Location: ../html/index.html?error=1');
            exit;
        }
    }
}
?>
