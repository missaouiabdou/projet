<?php
// filepath: c:\xampp\htdocs\PROJJET\php\encadrant.class.php
require_once 'connection.php';

class Encadrant {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Vérification du login
    public function login($email, $password) {
        $query = "SELECT * FROM encadrant WHERE MAIL_ENC = :email AND MDP_ENC = :password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                session_start();
                $_SESSION['nom'] = $user['NOM_ENC'];
                $_SESSION['prenom'] = $user['PRENOM_ENC'];
                $_SESSION['id'] = $user['ID_ENC'];
                $_SESSION['user_type'] = 'encadrant';
                return $user;
            }
        }

        return false;
    }

    // Update encadrant information
    public function update($id, $nom, $prenom, $email, $password) {
        try {
            $query = "UPDATE encadrant SET NOM_ENC = :nom, PRENOM_ENC = :prenom, MAIL_ENC = :email, MDP_ENC = :password WHERE ID_ENC = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // Delete encadrant
    public function delete($id) {
        try {
            // First check if encadrant has any associated stagiaires
            $checkQuery = "SELECT COUNT(*) FROM stagiaire WHERE ID_ENC = :id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':id', $id);
            $checkStmt->execute();
            
            if ($checkStmt->fetchColumn() > 0) {
                return false; // Cannot delete if there are associated stagiaires
            }
            
            $query = "DELETE FROM encadrant WHERE ID_ENC = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get encadrant by ID
    public function getById($id) {
        $query = "SELECT * FROM encadrant WHERE ID_ENC = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Ajoutez d'autres méthodes pour l'encadrant ici si besoin
}
?>