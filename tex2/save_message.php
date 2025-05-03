<?php
session_start();
header('Content-Type: application/json');

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=g2_stage_etudiant_medcine", "root", "hiba");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les données
    $user = $_POST['user'] ?? '';
    $message = $_POST['message'] ?? '';
    $messageId = $_POST['messageId'] ?? null; // Check if messageId is already provided
    
    if (empty($user) || empty($message)) {
        throw new Exception("Données manquantes");
    }
    
    // If messageId is provided, check if the message already exists
    if ($messageId) {
        $checkStmt = $pdo->prepare("SELECT ID_MSG FROM messagerie WHERE ID_MSG = ?");
        $checkStmt->execute([$messageId]);
        if ($checkStmt->fetch()) {
            // Message already exists, return success with existing ID
            echo json_encode(['success' => true, 'messageId' => $messageId, 'status' => 'existing']);
            exit;
        }
    }
    
    // Déterminer si l'utilisateur est un encadrant ou un stagiaire
    $id_enc = null;
    $id_stg = null;
    
    // Vérifier si l'utilisateur est un encadrant
    $stmt = $pdo->prepare("SELECT ID_ENC FROM encadrant WHERE NOM_ENC = ?");
    $stmt->execute([$user]);
    $encadrant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($encadrant) {
        $id_enc = $encadrant['ID_ENC'];
    } else {
        // Vérifier si l'utilisateur est un stagiaire
        $stmt = $pdo->prepare("SELECT ID_STG FROM stagiaire WHERE NOM_STG = ?");
        $stmt->execute([$user]);
        $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($stagiaire) {
            $id_stg = $stagiaire['ID_STG'];
        } else {
            // Create a new encadrant if user not found
            $stmt = $pdo->prepare("INSERT INTO encadrant (NOM_ENC) VALUES (?)");
            $stmt->execute([$user]);
            $id_enc = $pdo->lastInsertId();
        }
    }
    
    // Check for duplicate message in the last 5 seconds
    $checkDuplicateStmt = $pdo->prepare("
        SELECT ID_MSG FROM messagerie 
        WHERE CONT_MSG = ? 
        AND (ID_ENC = ? OR ID_STG = ?) 
        AND DATE_MSG > DATE_SUB(NOW(), INTERVAL 5 SECOND)
    ");
    $checkDuplicateStmt->execute([$message, $id_enc, $id_stg]);
    
    if ($duplicate = $checkDuplicateStmt->fetch()) {
        // Duplicate message found, return the existing ID
        echo json_encode(['success' => true, 'messageId' => $duplicate['ID_MSG'], 'status' => 'duplicate']);
        exit;
    }
    
    // Insérer le message dans la base de données
    $stmt = $pdo->prepare("INSERT INTO messagerie (CONT_MSG, DATE_MSG, ID_ENC, ID_STG) VALUES (?, NOW(), ?, ?)");
    $stmt->execute([$message, $id_enc, $id_stg]);
    $messageId = $pdo->lastInsertId();
    
    echo json_encode(['success' => true, 'messageId' => $messageId, 'status' => 'new']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
