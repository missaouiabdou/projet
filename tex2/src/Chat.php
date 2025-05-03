<?php
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use PDO;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $pdo;
    protected $users = [];

    public function __construct($dbConfig) {
        $this->clients = new \SplObjectStorage;
        try {
            $this->pdo = new PDO(
                "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}",
                $dbConfig['username'],
                $dbConfig['password']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nouvelle connexion ! ({$conn->resourceId})\n" ; 
    }

    // Méthode pour insérer un message
    public function insertMessage($cont_msg, $id_enc=null, $id_stg=null) {
        // Check if at least one ID is provided
        if ($id_enc === null && $id_stg === null) {
            echo "Erreur: ID_ENC et ID_STG ne peuvent pas être tous les deux NULL\n";
            return false;
        }
        
        // If both IDs are provided, prioritize encadrant
        if ($id_enc !== null && $id_stg !== null) {
            $id_stg = null;
        }
        
        // Adjust SQL based on which ID is available
        if ($id_enc !== null) {
            $sql = "INSERT INTO messagerie (CONT_MSG, DATE_MSG, ID_ENC) 
                    VALUES (:cont_msg, NOW(), :id_enc)";
            $params = [
                ':cont_msg' => $cont_msg,
                ':id_enc' => $id_enc
            ];
        } else {
            $sql = "INSERT INTO messagerie (CONT_MSG, DATE_MSG, ID_STG) 
                    VALUES (:cont_msg, NOW(), :id_stg)";
            $params = [
                ':cont_msg' => $cont_msg,
                ':id_stg' => $id_stg
            ];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        if ($result) {
            echo "Message inséré avec succès.\n";
            return true;
        } else {
            echo "Échec de l'insertion du message.\n";
            return false;
        }
    }

    // Méthode pour mettre à jour un message
    public function updateMessage($id_msg, $newContent) {
        $sql = "UPDATE messagerie SET CONT_MSG = :newContent WHERE ID_MSG = :id_msg";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':newContent' => $newContent,
            ':id_msg' => $id_msg
        ]);
        echo "Message mis à jour avec succès.\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if (!$data) {
            echo "Message invalide reçu\n";
            return;
        }
        
        echo "Message reçu: " . $msg . "\n";
        
        // Gérer les différents types de messages
        switch ($data['type']) {
            case 'join':
                // Associer l'utilisateur à cette connexion
                $this->users[$from->resourceId] = $data['user'];
                
                // Informer les autres utilisateurs
                $this->broadcastUserList();
                echo "Utilisateur {$data['user']} a rejoint le chat\n";
                break;
                
            case 'message':
                // Vérifier si l'utilisateur est un encadrant ou un stagiaire
                $id_enc = null;
                $id_stg = null;
                $userFound = false;
                $username = $data['user'];
                
                try {
                    echo "Recherche de l'utilisateur: {$username}\n";
                    
                    // Vérifier si c'est un encadrant - recherche plus flexible
                    $stmt = $this->pdo->prepare("SELECT ID_ENC FROM encadrant WHERE NOM_ENC LIKE :nom");
                    $stmt->execute([':nom' => "%{$username}%"]);
                    $encadrant = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($encadrant) {
                        $id_enc = $encadrant['ID_ENC'];
                        $userFound = true;
                        echo "Utilisateur trouvé comme encadrant, ID: {$id_enc}\n";
                    } else {
                        // Vérifier si c'est un stagiaire - recherche plus flexible
                        $stmt = $this->pdo->prepare("SELECT ID_STG FROM stagiaire WHERE NOM_STG LIKE :nom");
                        $stmt->execute([':nom' => "%{$username}%"]);
                        $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($stagiaire) {
                            $id_stg = $stagiaire['ID_STG'];
                            $userFound = true;
                            echo "Utilisateur trouvé comme stagiaire, ID: {$id_stg}\n";
                        }
                    }
                    
                    // Si l'utilisateur n'est pas trouvé, créer un encadrant temporaire
                    if (!$userFound) {
                        echo "Utilisateur non trouvé dans la base de données: {$username}\n";
                        
                        // Vérifier si un encadrant avec ce nom existe déjà
                        $stmt = $this->pdo->prepare("SELECT ID_ENC FROM encadrant WHERE NOM_ENC = :nom");
                        $stmt->execute([':nom' => $username]);
                        $existingEncadrant = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($existingEncadrant) {
                            $id_enc = $existingEncadrant['ID_ENC'];
                            echo "Utilisation d'un encadrant existant, ID: {$id_enc}\n";
                        } else {
                            // Créer un nouvel encadrant temporaire
                            try {
                                $stmt = $this->pdo->prepare("INSERT INTO encadrant (NOM_ENC) VALUES (:nom)");
                                $stmt->execute([':nom' => $username]);
                                $id_enc = $this->pdo->lastInsertId();
                                echo "Nouvel encadrant créé avec ID: {$id_enc}\n";
                            } catch (\Exception $e) {
                                // Si l'insertion échoue, utiliser un encadrant par défaut
                                echo "Erreur lors de la création d'un encadrant: " . $e->getMessage() . "\n";
                                $stmt = $this->pdo->prepare("SELECT ID_ENC FROM encadrant LIMIT 1");
                                $stmt->execute();
                                $defaultEncadrant = $stmt->fetch(PDO::FETCH_ASSOC);
                                if ($defaultEncadrant) {
                                    $id_enc = $defaultEncadrant['ID_ENC'];
                                    echo "Utilisation de l'encadrant par défaut, ID: {$id_enc}\n";
                                } else {
                                    echo "Aucun encadrant trouvé dans la base de données\n";
                                    return;
                                }
                            }
                        }
                    }
                    
                    // Insérer le message dans la base de données
                    $inserted = $this->insertMessage($data['message'], $id_enc, $id_stg);
                    
                    if ($inserted) {
                        // Ajouter l'heure si elle n'est pas fournie
                        if (!isset($data['time'])) {
                            $data['time'] = date('H:i');
                        }
                        
                        // Diffuser le message à tous les clients
                        foreach ($this->clients as $client) {
                            $client->send(json_encode($data));
                        }
                    }
                    
                } catch (\Exception $e) {
                    echo "Erreur lors du traitement du message: " . $e->getMessage() . "\n";
                }
                break;
                case 'private_message':
                    $recipient = $data['recipient'] ?? null;
                    if ($recipient) {
                        foreach ($this->clients as $client) {
                            // Suppose you store usernames in $this->users[$client->resourceId]
                            if (
                                isset($this->users[$client->resourceId]) &&
                                (
                                    $this->users[$client->resourceId] === $recipient ||
                                    $this->users[$client->resourceId] === $data['user']
                                )
                            ) {
                                $client->send(json_encode([
                                    'type' => 'private_message',
                                    'user' => $data['user'],
                                    'recipient' => $recipient,
                                    'message' => $data['message'],
                                    'time' => $data['time']
                                ]));
                            }
                        }
                    }
                    break;
                
            case 'typing':
                // Diffuser l'état de frappe aux autres utilisateurs
                foreach ($this->clients as $client) {
                    if ($from !== $client) {
                        $client->send($msg);
                    }
                }
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Détacher le client
        $this->clients->detach($conn);
        
        // Si l'utilisateur était identifié, le retirer de la liste
        if (isset($this->users[$conn->resourceId])) {
            $username = $this->users[$conn->resourceId];
            unset($this->users[$conn->resourceId]);
            echo "L'utilisateur {$username} a quitté le chat\n";
            
            // Mettre à jour la liste des utilisateurs pour tous
            $this->broadcastUserList();
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Une erreur est survenue: {$e->getMessage()}\n";
        $conn->close();
    }
    
    // Méthode pour diffuser la liste des utilisateurs connectés
    protected function broadcastUserList() {
        $userList = array_values($this->users);
        $message = json_encode([
            'type' => 'userList',
            'users' => $userList
        ]);
        
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }
    public function __destruct() {
        try {
            // Fermer toutes les connexions actives
            foreach ($this->clients as $client) {
                try {
                    $client->close();
                } catch (\Exception $e) {
                    echo "Erreur lors de la fermeture d'une connexion: " . $e->getMessage() . "\n";
                }
            }
            
            // Libérer la mémoire
            $this->clients = null;
            $this->users = [];
            
            // Fermer la connexion à la base de données
            $this->pdo = null;
            
            echo "Ressources du chat libérées avec succès.\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la libération des ressources: " . $e->getMessage() . "\n";
        }
    }
}
