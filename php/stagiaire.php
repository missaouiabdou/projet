<?php
require_once 'connection.php';

class Stagiaire {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Vérification du login
    public function login($email, $password) {
        $query = "SELECT * FROM stagiaire WHERE MAIL_STG = :email AND MDP_STG = :password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                session_start();
                $_SESSION['nom'] = $user['NOM_STG'];
                $_SESSION['prenom'] = $user['PRN_STG'];
                $_SESSION['id'] = $user['ID_STG'];
                $_SESSION['user_type'] = 'stagiaire';
                $_SESSION['id_niv'] = $user['ID_NIV'];
                return $user;
            }
        }

        return false;
    }

    // Update stagiaire information
    public function update($id, $nom, $prenom, $email, $password, $id_niv) {
        try {
            $query = "UPDATE stagiaire SET NOM_STG = :nom, PRENOM_STG = :prenom, MAIL_STG = :email, MDP_STG = :password, ID_NIV = :id_niv WHERE ID_STG = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':id_niv', $id_niv);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // Delete stagiaire
    public function delete($id) {
        try {
            // First check if stagiaire has any associated records
            $checkQuery = "SELECT COUNT(*) FROM stagiaire WHERE ID_STG = :id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':id', $id);
            $checkStmt->execute();
            
            if ($checkStmt->fetchColumn() == 0) {
                return false; // Stagiaire doesn't exist
            }
            
            $query = "DELETE FROM stagiaire WHERE ID_STG = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // Get stagiaire by ID
    public function getById($id) {
        $query = "SELECT * FROM stagiaire WHERE ID_STG = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Récupération de la liste des stagiaires
   
    
    // Récupérer les encadrants disponibles pour un stagiaire
    
}
