<?php
include "connection.php";
session_start();


if (!isset($_SESSION['id'])) {
    header('Location: ../html/index.html');
    exit;
}

// Get user info
$stmt = $conn->prepare("SELECT * FROM stagiaire WHERE ID_STG = ?");
$stmt->execute([$_SESSION['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's niveau
$niveau = $user['ID_NIV'] ?? 1;

// Get total messages for the user
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM messagerie WHERE ID_STG = ?");
$stmt->execute([$_SESSION['id']]);
$messageCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get online users count (this would need WebSocket integration for real-time)
$onlineCount = 0; // This would be updated via WebSocket

// Get stage info
$stmt = $conn->prepare("SELECT * FROM stage ");
$stmt->execute();
$stage = $stmt->fetch(PDO::FETCH_ASSOC);

// Get encadrant info
$stmt = $conn->prepare("SELECT * FROM encadrant e WHERE e.ID_ENC = (SELECT s.ID_ENC FROM stagiaire s WHERE s.ID_STG = ?)");
$stmt->execute([$_SESSION['id']]);
$encadrant = $stmt->fetch(PDO::FETCH_ASSOC);

// Get service info
$stmt = $conn->prepare("SELECT * FROM service WHERE ID_STAGE = ?");
$stmt->execute([$stage['ID_STAGE']]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($_SESSION['nom']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-blue-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-indigo-600 to-blue-700 text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="fas fa-tachometer-alt mr-2 text-xl"></i>
                    <span class="text-xl font-bold tracking-wide">Dashboard</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 bg-indigo-700/50 px-3 py-1 rounded-full">
                        <div class="w-8 h-8 rounded-full bg-indigo-400 flex items-center justify-center text-sm font-semibold">
                            <?php echo strtoupper(substr($_SESSION['nom'], 0, 1) . substr($_SESSION['prenom'], 0, 1)); ?>
                        </div>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['nom'] . ' ' . $_SESSION['prenom']); ?></span>
                    </div>
                    <a href="logout.php" class="text-indigo-200 hover:text-white transition duration-150 ease-in-out flex items-center space-x-1">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Welcome Message -->
        <div class="mb-8 p-6 bg-white rounded-lg shadow-md border-l-4 border-indigo-500">
             <h1 class="text-2xl font-semibold text-gray-800">Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom']); ?>!</h1>
             <p class="text-gray-600 mt-1">Voici un aperçu de votre activité.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Profile Card -->
            <a href="profil.php" style="text-decoration: none;">
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover border border-gray-100 cursor-pointer transform transition-all duration-150 hover:-translate-y-1">
                    <div class="flex items-center">
                        <div class="p-4 rounded-full bg-gradient-to-tl from-purple-100 to-indigo-100 text-indigo-600 shadow-inner">
                            <i class="fas fa-user-graduate text-2xl"></i>
                        </div>
                        <div class="ml-5">
                            <h2 class="text-gray-900 text-lg font-semibold">Profil</h2>
                            <p class="text-gray-600 text-sm">Cliquez pour voir les détails</p>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Grades Stats -->
            <div onclick="window.location.href= 'notes.php'" class="bg-white rounded-lg shadow-lg p-6 card-hover border border-gray-100 cursor-pointer transform transition-all duration-150 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="p-4 rounded-full bg-gradient-to-tl from-yellow-100 to-orange-100 text-yellow-600 shadow-inner">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <h2 class="text-gray-900 text-lg font-semibold">Mes Notes</h2>
                        <p class="text-gray-600 text-sm">Voir mes résultats</p>
                    </div>
                </div>
            </div>

            <!-- Requests Stats -->
            <div onclick="window.location.href= 'demandes.php'" class="bg-white rounded-lg shadow-lg p-6 card-hover border border-gray-100 cursor-pointer transform transition-all duration-150 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="p-4 rounded-full bg-gradient-to-tl from-emerald-100 to-green-100 text-green-600 shadow-inner">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <h2 class="text-gray-900 text-lg font-semibold">Mes Demandes</h2>
                        <p class="text-gray-600 text-sm">Attestations et documents</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Internship Information -->
        <div class="mb-10 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-gradient-to-tl from-blue-100 to-indigo-100 text-blue-600 shadow-inner">
                        <i class="fas fa-briefcase text-xl"></i>
                    </div>
                    <h3 class="ml-3 text-xl font-semibold text-gray-900">Information du Stage</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Entreprise</h4>
                        <p class="font-medium text-gray-800">CHU</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Période</h4>
                        <p class="font-medium text-gray-800">
                            <?php 
                            if (isset($stage['DATE_DEB_STG']) && isset($stage['DATE_FIN_STG'])) {
                                echo htmlspecialchars(date('d/m/Y', strtotime($stage['DATE_DEB_STG']))) . ' - ' . 
                                     htmlspecialchars(date('d/m/Y', strtotime($stage['DATE_FIN_STG'])));
                            } else {
                                echo 'Non défini';
                            }
                            ?>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">2 mois</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Encadrant</h4>
                        <p class="font-medium text-gray-800">
                            <?php 
                            if (isset($encadrant['NOM_ENC']) && isset($encadrant['PRN_ENC'])) {
                                echo htmlspecialchars($encadrant['NOM_ENC'] . ' ' . $encadrant['PRN_ENC']);
                            } else {
                                echo 'Non assigné';
                            }
                            ?>
                        </p>
                        <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($encadrant['MAIL_ENC'] ?? ''); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Service</h4>
                        <p class="text-gray-800"><?php echo htmlspecialchars($service['NOM_SER'] ?? 'Non défini'); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Statut</h4>
                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($stage['STATUT'] ?? 'En cours'); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Documents</h4>
                        <p class="text-gray-600">Aucun document disponible</p>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="openInternshipModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150 ease-in-out">
                        <i class="fas fa-eye mr-2"></i>
                        Voir les détails
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
            <!-- General Chat -->
            <div class="bg-gradient-to-br from-white to-indigo-50 rounded-lg shadow-lg overflow-hidden card-hover border border-gray-100">
                <div class="p-8">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-comments text-indigo-500 text-2xl mr-3"></i>
                        <h3 class="text-xl font-semibold text-gray-900">Chat Général</h3>
                    </div>
                    <p class="text-gray-600 mb-6">Discutez avec tous les utilisateurs connectés.</p>
                    <a href="../tex2/index.php" class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition duration-150 ease-in-out shadow hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Rejoindre
                    </a>
                </div>
            </div>

            <!-- Group Chat -->
            <div class="bg-gradient-to-br from-white to-green-50 rounded-lg shadow-lg overflow-hidden card-hover border border-gray-100">
                <div class="p-8">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-user-friends text-green-500 text-2xl mr-3"></i>
                        <h3 class="text-xl font-semibold text-gray-900">Chat de Niveau <?php echo htmlspecialchars($niveau); ?></h3>
                    </div>
                    <p class="text-gray-600 mb-6">Discutez avec les autres stagiaires de votre niveau.</p>
                    <a href="../tex2/chat_group.php" class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-full hover:bg-green-700 transition duration-150 ease-in-out shadow hover:shadow-lg transform hover:scale-105">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Rejoindre
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Modal -->
        <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-semibold text-gray-900">Détails du Profil</h3>
                        <button onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Informations Personnelles</h4>
                                <div class="mt-2 space-y-2">
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Nom:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($_SESSION['nom']); ?></span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Prénom:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($_SESSION['prenom']); ?></span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($user['EMAIL'] ?? ''); ?></span>
                                    </p>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Informations Académiques</h4>
                                <div class="mt-2 space-y-2">
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">ID Stagiaire:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($_SESSION['id']); ?></span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Niveau:</span>
                                        <span class="font-medium">Niveau <?php echo htmlspecialchars($niveau); ?></span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Statut:</span>
                                        <span class="font-medium text-green-600">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            Actif
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Statistiques</h4>
                                <div class="mt-2 space-y-2">
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Messages Envoyés:</span>
                                        <span class="font-medium"><?php echo $messageCount; ?></span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="text-gray-600">Date d'inscription:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($user['DATE_INSCRIPTION'] ?? 'N/A'); ?></span>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button onclick="openUpdateModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                                    <i class="fas fa-edit mr-2"></i>
                                    Modifier le Profil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Profile Modal -->
        <div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Modifier le Profil</h3>
                        <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="updateForm" class="space-y-4">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($_SESSION['id']); ?>">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" name="nom" id="inputNom" value="<?php echo htmlspecialchars($_SESSION['nom']); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" name="prenom" id="inputPrenom" value="<?php echo htmlspecialchars($_SESSION['prenom']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="inputEmail" value="<?php echo htmlspecialchars($user['EMAIL'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau Mot de Passe (optionnel)</label>
                            <input type="password" name="password" id="inputPassword"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Niveau</label>
                            <select name="niveau" id="inputNiveau" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <?php for($i = 1; $i <= 3; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $niveau == $i ? 'selected' : ''; ?>>
                                        Niveau <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeUpdateModal()" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Annuler
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grades Modal -->
        <div id="gradesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl transform transition-all">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-semibold text-gray-900">Mes Notes</h3>
                        <button onclick="closeGradesModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coefficient</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM notes WHERE ID_STG = ?");
                                $stmt->execute([$_SESSION['id']]);
                                $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($notes as $note): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($note['NOM_MODULE']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($note['NOTE']); ?>/20
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($note['COEFFICIENT']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($note['OBSERVATION']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Modal -->
        <div id="requestsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-semibold text-gray-900">Mes Demandes</h3>
                        <button onclick="closeRequestsModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- New Request Form -->
                        <form id="requestForm" class="mb-6">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de Document</label>
                                    <select name="type_document" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="attestation_stage">Attestation de Stage</option>
                                        <option value="attestation_scolarite">Attestation de Scolarité</option>
                                        <option value="releve_notes">Relevé de Notes</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire (optionnel)</label>
                                    <textarea name="commentaire" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                                </div>
                                <div>
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Envoyer la demande
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Requests History -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Historique des demandes</h4>
                            <div class="space-y-3">
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM demandes WHERE ID_STG = ? ORDER BY DATE_DEMANDE DESC");
                                $stmt->execute([$_SESSION['id']]);
                                $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($demandes as $demande): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h5 class="font-medium text-gray-900"><?php echo htmlspecialchars($demande['TYPE_DOCUMENT']); ?></h5>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars(date('d/m/Y', strtotime($demande['DATE_DEMANDE']))); ?></p>
                                    </div>
                                    <span class="px-3 py-1 text-sm rounded-full <?php echo $demande['STATUT'] === 'En attente' ? 'bg-yellow-100 text-yellow-800' : ($demande['STATUT'] === 'Approuvé' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo htmlspecialchars($demande['STATUT']); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Internship Modal -->
        <div id="internshipModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl transform transition-all">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-semibold text-gray-900">Détails du Stage</h3>
                        <button onclick="closeInternshipModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Entreprise</h4>
                                <p class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($stage['NOM_ENTREPRISE'] ?? 'Non assigné'); ?></p>
                                <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($stage['ADRESSE_ENTREPRISE'] ?? ''); ?></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Période</h4>
                                <p class="text-lg font-medium text-gray-900">
                                    <?php 
                                    if (isset($stage['DATE_DEBUT']) && isset($stage['DATE_FIN'])) {
                                        echo htmlspecialchars(date('d/m/Y', strtotime($stage['DATE_DEBUT']))) . ' - ' . 
                                             htmlspecialchars(date('d/m/Y', strtotime($stage['DATE_FIN'])));
                                    } else {
                                        echo 'Non défini';
                                    }
                                    ?>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <?php
                                    if (isset($stage['DATE_DEBUT']) && isset($stage['DATE_FIN'])) {
                                        $debut = new DateTime($stage['DATE_DEBUT']);
                                        $fin = new DateTime($stage['DATE_FIN']);
                                        $duree = $debut->diff($fin);
                                        echo $duree->format('%m mois et %d jours');
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Encadrant</h4>
                            <p class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($stage['NOM_ENCADRANT'] ?? 'Non assigné'); ?></p>
                            <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($stage['EMAIL_ENCADRANT'] ?? ''); ?></p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Sujet du Stage</h4>
                            <p class="text-gray-900"><?php echo nl2br(htmlspecialchars($stage['SUJET'] ?? 'Non défini')); ?></p>
                        </div>

                        <?php if (isset($stage['RAPPORT_URL'])): ?>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Rapport de Stage</h4>
                            <a href="<?php echo htmlspecialchars($stage['RAPPORT_URL']); ?>" target="_blank" 
                               class="inline-flex items-center text-indigo-600 hover:text-indigo-700">
                                <i class="fas fa-file-pdf mr-2"></i>
                                Télécharger le rapport
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openGradesModal() {
                document.getElementById('gradesModal').classList.remove('hidden');
                document.getElementById('gradesModal').classList.add('flex');
            }

            function closeGradesModal() {
                document.getElementById('gradesModal').classList.add('hidden');
                document.getElementById('gradesModal').classList.remove('flex');
            }

            function openRequestsModal() {
                document.getElementById('requestsModal').classList.remove('hidden');
                document.getElementById('requestsModal').classList.add('flex');
            }

            function closeRequestsModal() {
                document.getElementById('requestsModal').classList.add('hidden');
                document.getElementById('requestsModal').classList.remove('flex');
            }

            function openInternshipModal() {
                document.getElementById('internshipModal').classList.remove('hidden');
                document.getElementById('internshipModal').classList.add('flex');
            }

            function closeInternshipModal() {
                document.getElementById('internshipModal').classList.add('hidden');
                document.getElementById('internshipModal').classList.remove('flex');
            }

            // Handle request form submission
            document.getElementById('requestForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch('submit_request.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Demande envoyée avec succès!');
                        window.location.reload();
                    } else {
                        alert(data.error || 'Erreur lors de l\'envoi de la demande');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de l\'envoi de la demande');
                });
            });
        </script>
    </body>
</html>
