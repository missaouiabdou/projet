<?php
// filepath: c:\xampp\htdocs\PROJJET\tex2\update_message.php
header('Content-Type: application/json');
session_start();
include_once '../php/connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn!=null) {
    $data = json_decode(file_get_contents('php://input'), true);
    $messageId = $data['messageId'];
    $newContent = $data['newContent'];

    try {
        
        $stmt = $conn->prepare("UPDATE messagerie SET CONT_MSG = :newContent WHERE ID_MSG = :messageId");
        $stmt->execute(['newContent' => $newContent, 'messageId' => $messageId]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>