<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../php/connection.php";

// Check if user is logged in and has permission
if (!isset($_SESSION['id_niv']) || $_SESSION['id_niv'] != 1) {
    die("Access denied: invalid or missing id_niv in session.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $group = $_POST['group'] ?? 'niveau1';

    // Get sender info
    $id_stg = $_SESSION['id'] ?? null;

    try {
        // Insert message for stagiaire or encadrant
        if ($id_stg) {
            $stmt = $conn->prepare("INSERT INTO messagerie (CONT_MSG, DATE_MSG, ID_STG) VALUES (?, NOW(), ?)");
            $stmt->execute([$message, $id_stg]);
        }  else {
            die("No sender ID found in session (id_stg or id_enc).");
        }

        // Redirect back to chat
        header("Location: chat_group.php");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    die("Form error: message is empty or not sent via POST.");
}
?>