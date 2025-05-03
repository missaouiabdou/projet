
<?php
class Database {
    private $host = "localhost";
    private $dbname = "g2_stage_etudiant_medcine";
    private $username = "root";
    private $password = "hiba";
    private $conn;

    // Connexion à la base de données
    public function connect() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->dbname,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
$database = new Database();
// Call the connect method and assign the PDO object to $conn
$conn = $database->connect();