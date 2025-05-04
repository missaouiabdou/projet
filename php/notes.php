<?php
include "connection.php"; // Include DB connection
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['id_stg'])) {
    // Redirect to login or show an error if not logged in
    header('Location: ../html/index.html?error=not_logged_in'); // Adjust login page path if needed
    exit;
}

// !!!!! IMPORTANT NOTE !!!!!
// The database schema provided does NOT contain a 'notes' table.
// This code assumes a table named 'notes' exists with columns:
// - ID_NOTE (INT, PK, AI)
// - ID_STG (INT, FK to stagiaire.ID_STG)
// - NOM_MODULE (VARCHAR)
// - NOTE_MODULE (DECIMAL(4,2) or FLOAT) - Represents the grade out of 20
// You MUST create this table and populate it for this page to work.

$notes = [];
$average = 0;
$totalNotes = 0;
$countNotes = 0;
$status = "Non défini";
$error_message = null;

try {
    // Attempt to fetch notes for the logged-in stagiaire
    // Replace 'notes', 'NOM_MODULE', 'NOTE_MODULE' with your actual table/column names
    $stmt = $conn->prepare("SELECT NOM_MODULE, NOTE_MODULE FROM notes WHERE ID_STG = ?");
    $stmt->execute([$_SESSION['id_stg']]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($notes) {
        foreach ($notes as $note) {
            // Basic validation: Ensure note is numeric and within a reasonable range (e.g., 0-20)
            if (isset($note['NOTE_MODULE']) && is_numeric($note['NOTE_MODULE']) && $note['NOTE_MODULE'] >= 0 && $note['NOTE_MODULE'] <= 20) {
                $totalNotes += (float)$note['NOTE_MODULE'];
                $countNotes++;
            }
            // Add handling for weighted average here if you have coefficients
        }

        if ($countNotes > 0) {
            $average = $totalNotes / $countNotes;
            // *** Apply the validation rule: > 12 = Validé ***
            $status = ($average >= 12) ? "Validé" : "Non validé";
        } else {
            $status = "Aucune note valide";
        }
    } else {
         $status = "Aucune note enregistrée";
    }

} catch (PDOException $e) {
    // Check if the error is "Table not found" (error code for base table not found is typically 1146 or similar)
    if ($e->getCode() == '42S02' || strpos(strtolower($e->getMessage()), 'base table or view not found') !== false || strpos(strtolower($e->getMessage()), "relation \"notes\" does not exist") !== false) {
         $error_message = "Erreur Technique : La table des notes ('notes') semble manquante. Veuillez contacter l'administrateur.";
         $notes = []; // Ensure notes array is empty
         $status = "Erreur Configuration";
    } else {
        // Handle other potential database errors
        $error_message = "Erreur Base de Données : Impossible de récupérer les notes.";
        // Log the specific error in a real app: error_log("Notes Fetch Error: " . $e->getMessage());
        $status = "Erreur BD";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Notes</title>
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
         /* Styles for validation status */
        .status-valide { background-color: #d1fae5; color: #065f46; } /* green-100, green-800 */
        .status-non-valide { background-color: #fee2e2; color: #991b1b; } /* red-100, red-800 */
        .status-erreur { background-color: #fef3c7; color: #92400e; } /* amber-100, amber-800 */
        .status-autre { background-color: #e5e7eb; color: #374151; } /* gray-200, gray-700 */
    </style>
</head>
<body class="container mx-auto p-4 md:p-8">

    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Mes Notes</h1>
        <p class="text-gray-600">Consultez vos résultats par module.</p>
    </div>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow" role="alert">
            <p class="font-bold">Alerte Système</p>
            <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
    <?php endif; ?>


    <!-- Notes Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Module
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Note (/20)
                        </th>
                        <!-- Add Coefficient column header if needed -->
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($notes)): ?>
                        <?php foreach ($notes as $note): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($note['NOM_MODULE'] ?? 'Module inconnu'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php
                                    // Display note only if it's valid
                                    if (isset($note['NOTE_MODULE']) && is_numeric($note['NOTE_MODULE']) && $note['NOTE_MODULE'] >= 0 && $note['NOTE_MODULE'] <= 20) {
                                        echo htmlspecialchars(number_format((float)$note['NOTE_MODULE'], 2));
                                    } else {
                                        echo '<span class="text-red-500 italic">N/A</span>';
                                    }
                                ?>
                            </td>
                            <!-- Add Coefficient column data if needed -->
                        </tr>
                        <?php endforeach; ?>
                    <?php elseif (!$error_message): // Show only if no error and no notes ?>
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 italic">
                                Aucune note à afficher pour le moment.
                            </td>
                        </tr>
                    <?php else: // Show if error occurred and table couldn't be loaded ?>
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-red-500 italic">
                                Impossible de charger les notes en raison d'une erreur.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Average and Status -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-center sm:text-left">
                <span class="text-sm font-medium text-gray-600">Moyenne Générale :</span>
                <span class="ml-2 text-lg font-bold text-gray-800">
                    <?php echo ($countNotes > 0) ? number_format($average, 2) . ' / 20' : 'N/A'; ?>
                </span>
            </div>
            <div class="text-center sm:text-right">
                <span class="text-sm font-medium text-gray-600 mr-2">Statut Global :</span>
                <?php
                    $statusClass = 'status-autre'; // Default gray
                    if ($status === 'Validé') {
                        $statusClass = 'status-valide';
                    } elseif ($status === 'Non validé') {
                        $statusClass = 'status-non-valide';
                    } elseif (strpos($status, 'Erreur') !== false) {
                        $statusClass = 'status-erreur';
                    }
                ?>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                    <?php echo htmlspecialchars($status); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Optional: Back button or link -->
     <div class="mt-6 text-center">
        <a href="dashboard.php" class="text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out">
            <i class="fas fa-arrow-left mr-1"></i> Retour au Dashboard
        </a>
    </div>

</body>
</html>