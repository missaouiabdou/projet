<?php
include "connection.php"; // Include DB connection
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['id_stg'])) {
    // Redirect to login or show an error if not logged in
    header('Location: ../html/index.html?error=not_logged_in'); // Adjust login page path if needed
    exit;
}

// Check for flash messages from previous action (submit_demande.php)
$flash_message = $_SESSION['flash_message'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_message'], $_SESSION['flash_error']); // Clear flash messages after reading

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Demandes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Include styles from your theme or reference dashboard */
         body {
            background-color: #f9fafb; /* Example background */
            padding: 2rem;
        }
        .card-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="container mx-auto p-4 md:p-8">

    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Mes Demandes</h1>
        <p class="text-gray-600">Effectuez vos demandes administratives.</p>
    </div>

    <!-- Flash Messages -->
    <?php if ($flash_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow" role="alert">
            <p><?php echo htmlspecialchars($flash_message); ?></p>
        </div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
         <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow" role="alert">
            <p><?php echo htmlspecialchars($flash_error); ?></p>
        </div>
    <?php endif; ?>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
        <!-- Demande Attestation Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 text-center card-hover">
            <div class="mb-4">
                <i class="fas fa-file-contract text-4xl text-blue-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-3">Attestation de Stage</h2>
            <p class="text-gray-600 mb-6 text-sm">Demandez une attestation prouvant votre période de stage.</p>
            <form action="submit_demande.php" method="POST" onsubmit="return confirm('Confirmez-vous la demande d\'attestation de stage ?');">
                <input type="hidden" name="type_demande" value="attestation_stage">
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150 ease-in-out shadow hover:shadow-lg transform hover:scale-105">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Demander l'Attestation
                </button>
            </form>
        </div>

        <!-- Demande Reussite Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 text-center card-hover">
            <div class="mb-4">
                <i class="fas fa-graduation-cap text-4xl text-green-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-3">Attestation de Réussite</h2>
            <p class="text-gray-600 mb-6 text-sm">Demandez une attestation confirmant la validation de votre stage/formation.</p>
            <form action="submit_demande.php" method="POST" onsubmit="return confirm('Confirmez-vous la demande d\'attestation de réussite ?');">
                <input type="hidden" name="type_demande" value="attestation_reussite">
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-150 ease-in-out shadow hover:shadow-lg transform hover:scale-105">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Demander l'Attestation
                </button>
            </form>
        </div>
    </div>

    <!-- Add a section here to display past demands history if needed -->
    <!-- This requires a dedicated 'demandes' table in your DB -->
    <!-- Example Placeholder:
    <div class="mt-10 bg-white rounded-lg shadow p-6 border border-gray-200 max-w-4xl mx-auto">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Historique des Demandes</h3>
        <p class="text-gray-500 italic">Fonctionnalité à venir (nécessite une table 'demandes').</p>
        <ul>
            <li>Demande X - Statut Y - Date Z</li>
        </ul>
    </div>
    -->

     <!-- Optional: Back button or link -->
     <div class="mt-10 text-center">
        <a href="dashboard.php" class="text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out">
            <i class="fas fa-arrow-left mr-1"></i> Retour au Dashboard
        </a>
    </div>

</body>
</html>