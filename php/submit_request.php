<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_stg = $_SESSION['id'];
    $type_document = $_POST['type_document'] ?? '';
    $commentaire = $_POST['commentaire'] ?? '';
    $date_demande = date('Y-m-d H:i:s');
    $statut = 'En attente';

    if (empty($type_document)) {
        echo json_encode(['success' => false, 'error' => 'Type de document requis']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO demandes (ID_STG, TYPE_DOCUMENT, COMMENTAIRE, DATE_DEMANDE, STATUT) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$id_stg, $type_document, $commentaire, $date_demande, $statut]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'enregistrement de la demande']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur de base de données']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}
?>
