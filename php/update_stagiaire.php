<?php
session_start();
require_once 'stagiaire.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Verify user is logged in and has permission
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'encadrant') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    
    $id = $_POST['id'];
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $id_niv = $_POST['id_niv'] ?? '';
    
    if (empty($nom) || empty($prenom) || empty($email) || empty($id_niv)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    $stagiaire = new Stagiaire();
    $result = $stagiaire->update($id, $nom, $prenom, $email, $password, $id_niv);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update stagiaire']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?> 