<?php
include_once '../php/connection.php'; // Include your database connection file
// filepath: c:\xampp\htdocs\PROJJET\tex2\delete_message.php
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $messageId = $data['messageId'] ?? null;

    if (!$messageId || !is_numeric($messageId)) {
        echo json_encode(['success' => false, 'error' => 'Invalid message ID.']);
        exit;
    }

    try {

        $stmt = $conn->prepare("DELETE FROM messagerie WHERE ID_MSG = :messageId");
        $stmt->execute(['messageId' => $messageId]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>