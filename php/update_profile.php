<?php
session_start(); // Start session first
include "connection.php"; // Include DB connection

// Check if user is logged in and the request is POST
if (!isset($_SESSION['id_stg']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // It's better to redirect to login if session is invalid,
    // but redirecting back to profile is also okay if you assume they were just there.
    header('Location: profil.php');
    exit;
}

// --- Input Validation and Sanitization ---
// Keep this section as it was previously corrected
$id_stg = filter_input(INPUT_POST, 'id_stg', FILTER_SANITIZE_NUMBER_INT);
$nom = filter_input(INPUT_POST, 'nom_stg', FILTER_SANITIZE_STRING);
$prenom = filter_input(INPUT_POST, 'prn_stg', FILTER_SANITIZE_STRING);
$tel = filter_input(INPUT_POST, 'tel_stg', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'mail_stg', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

// Basic validation
if (empty($id_stg) || empty($nom) || empty($prenom) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $_SESSION['flash_error'] = "Données invalides ou manquantes.";
     header('Location: profil.php');
     exit;
}

// Check if ID matches the logged-in user
if ($id_stg != $_SESSION['id_stg']) {
    $_SESSION['flash_error'] = "Mise à jour non autorisée.";
    header('Location: profil.php');
    exit;
}

// --- Database Update ---
// Keep this section as it was previously corrected
try {
    $sql = "UPDATE stagiaire SET NOM_STG = ?, PRN_STG = ?, MAIL_STG = ?, TEL_STG = ?";
    $params = [$nom, $prenom, $email, $tel];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", MDP_STG = ?";
        $params[] = $hashed_password;
    }

    $sql .= " WHERE ID_STG = ?";
    $params[] = $id_stg;

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $_SESSION['nom'] = $nom; // Update session
    $_SESSION['prenom'] = $prenom; // Update session

    $_SESSION['flash_message'] = "Profil mis à jour avec succès.";

} catch (PDOException $e) {
     if ($e->getCode() == '23000') {
         $_SESSION['flash_error'] = "L'adresse email ou le numéro de téléphone est déjà utilisé(e).";
     } else {
         $_SESSION['flash_error'] = "Erreur lors de la mise à jour du profil.";
         // error_log("Profile Update Error: " . $e->getMessage()); // Log for debugging
     }
}

// Redirect back to the profile page
header('Location: profil.php');
exit;
?>