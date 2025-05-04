<?php
session_start(); // Start session first
include "connection.php"; // Include DB connection

// Check if user is logged in and the request is POST
if (!isset($_SESSION['id_stg']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: demandes.php'); // Redirect back
    exit;
}

// --- Input Validation ---
// Keep this section as it was previously corrected
$type_demande = filter_input(INPUT_POST, 'type_demande', FILTER_SANITIZE_STRING);

if (empty($type_demande) || !in_array($type_demande, ['attestation_stage', 'attestation_reussite'])) {
     $_SESSION['flash_error'] = "Type de demande invalide.";
     header('Location: demandes.php');
     exit;
}

// --- Prepare Notification ---
// Keep this section as it was previously corrected
$stagiaire_id = $_SESSION['id_stg'];
$stagiaire_nom = $_SESSION['nom'] ?? 'Inconnu';
$stagiaire_prenom = $_SESSION['prenom'] ?? '';
$admin_id = 1; // <<< HARDCODED Admin ID

$contenu_notification = "Le stagiaire " . htmlspecialchars($stagiaire_prenom . ' ' . $stagiaire_nom) .
                         " (ID: " . htmlspecialchars($stagiaire_id) . ") a demandé une ";

if ($type_demande === 'attestation_stage') {
    $contenu_notification .= "'Attestation de Stage'.";
} elseif ($type_demande === 'attestation_reussite') {
    $contenu_notification .= "'Attestation de Réussite'.";
}

// --- Database Insertion ---
// Keep this section as it was previously corrected
try {
    // Using DATE_NOTIF as DATE type requires format YYYY-MM-DD
    $current_date = date('Y-m-d');
    $sql = "INSERT INTO notification (CONT_NOTIF, DATE_NOTIF, ID_ADM) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$contenu_notification, $current_date, $admin_id]);

    $_SESSION['flash_message'] = "Votre demande a été envoyée avec succès.";

} catch (PDOException $e) {
    $_SESSION['flash_error'] = "Erreur lors de l'envoi de la demande.";
    // error_log("Demand Submit Error: " . $e->getMessage()); // Log for debugging
}

// Redirect back to the demands page
header('Location: demandes.php');
exit;
?>