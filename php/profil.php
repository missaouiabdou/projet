<?php
$pageTitle = "Mon Profil"; // Set page title
include "connection.php";
session_start();


// Fetch current Stagiaire's full data
$stmt = $conn->prepare("SELECT * FROM stagiaire WHERE ID_STG = ?");
$stmt->execute([$_SESSION['id_stg']]);
$stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stagiaire) {
    // Handle case where stagiaire data is not found (should not happen if session is set)
    echo "<p class='text-red-500'>Erreur: Données du stagiaire non trouvées.</p>";
    exit;
}

// Get Niveau name
$stmtNiveau = $conn->prepare("SELECT NUM_NIV FROM niveau WHERE ID_NIV = ?");
$stmtNiveau->execute([$stagiaire['ID_NIV']]);
$niveauInfo = $stmtNiveau->fetch(PDO::FETCH_ASSOC);
$niveauName = $niveauInfo ? $niveauInfo['NUM_NIV'] : 'N/A';

// Get Encadrant name
$stmtEnc = $conn->prepare("SELECT NOM_ENC, PRN_ENC FROM encadrant WHERE ID_ENC = ?");
$stmtEnc->execute([$stagiaire['ID_ENC']]);
$encadrantInfo = $stmtEnc->fetch(PDO::FETCH_ASSOC);
$encadrantName = $encadrantInfo ? $encadrantInfo['NOM_ENC'] . ' ' . $encadrantInfo['PRN_ENC'] : 'N/A';

?>

<head>
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

<!-- Page Title -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Mon Profil</h1>
    <p class="text-gray-600">Consultez et modifiez vos informations personnelles.</p>
</div>

<!-- Profile Form -->
<div class="bg-white rounded-lg shadow-lg p-6 md:p-8 border border-gray-200 max-w-4xl mx-auto">
    <form action="update_profile.php" method="POST">
        <input type="hidden" name="id_stg" value="<?php echo htmlspecialchars($stagiaire['ID_STG']); ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label for="nom_stg" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" name="nom_stg" id="nom_stg" value="<?php echo htmlspecialchars($stagiaire['NOM_STG']); ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                </div>

                <div>
                    <label for="prn_stg" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <input type="text" name="prn_stg" id="prn_stg" value="<?php echo htmlspecialchars($stagiaire['PRN_STG']); ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                </div>

                 <div>
                    <label for="tel_stg" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="tel" name="tel_stg" id="tel_stg" value="<?php echo htmlspecialchars($stagiaire['TEL_STG'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                </div>

                <div>
                    <label for="mail_stg" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="mail_stg" id="mail_stg" value="<?php echo htmlspecialchars($stagiaire['MAIL_STG']); ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                </div>

                 <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau Mot de Passe (laisser vide pour ne pas changer)</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                </div>

            </div>

            <!-- Right Column (Read-only info) -->
            <div class="space-y-4 bg-gray-50 p-4 rounded-md border">
                 <div>
                    <span class="block text-sm font-medium text-gray-500">ID Stagiaire</span>
                    <p class="mt-1 text-gray-900 font-medium"><?php echo htmlspecialchars($stagiaire['ID_STG']); ?></p>
                </div>
                 <div>
                    <span class="block text-sm font-medium text-gray-500">Date de Naissance</span>
                    <p class="mt-1 text-gray-900 font-medium"><?php echo htmlspecialchars(date('d/m/Y', strtotime($stagiaire['DNAISS_STG']))); ?></p>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Niveau</span>
                    <p class="mt-1 text-gray-900 font-medium"><?php echo htmlspecialchars($niveauName); ?></p>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Encadrant Pédagogique</span>
                    <p class="mt-1 text-gray-900 font-medium"><?php echo htmlspecialchars($encadrantName); ?></p>
                </div>
                 <div>
                    <span class="block text-sm font-medium text-gray-500">Image de Profil</span>
                    <p class="mt-1 text-gray-900 font-medium">
                        <?php echo $stagiaire['IMG_STG'] ? basename(htmlspecialchars($stagiaire['IMG_STG'])) : 'Non définie'; ?>
                         <!-- You might add an image tag if IMG_STG stores a web-accessible path -->
                         <!-- Or add a file upload input here -->
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
            <button type="submit"
                    class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                <i class="fas fa-save mr-2"></i>
                Enregistrer les Modifications
            </button>
        </div>
    </form>
</div>