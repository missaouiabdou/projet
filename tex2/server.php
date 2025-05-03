<?php
require 'vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;

// Configuration de la base de données
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'g2_stage_etudiant_medcine',
    'username' => 'root',
    'password' => 'hiba'
];

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat($dbConfig)
        )
    ),
    8080
);

echo "Server running on port 8080\n";
$server->run();