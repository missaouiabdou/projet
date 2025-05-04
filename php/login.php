<?php
// **MUST be the very first line BEFORE any output**
session_start();

// Include necessary class files (adjust paths if needed)
require_once 'stagiaire.php'; // Assumes this file contains the Stagiaire class with a login method
require_once 'encadrant.php'; // Assumes this file contains the Encadrant class with a login method
// It's better practice to include connection.php here if the classes don't do it themselves.
// require_once 'connection.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic input sanitization (consider more robust validation)
    $email = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Password should NOT be sanitized before verification

    if (empty($email) || empty($password)) {
        // Handle empty fields - redirect back with an error
        // Determine which login page to redirect to based on referer
        $referer = $_SERVER['HTTP_REFERER'] ?? '../index.html'; // Default fallback
        if (strpos($referer, 'encadrant.html') !== false) {
             header('Location: ../html/encadrant.html?error=empty');
        } else {
             header('Location: ../html/index.html?error=empty'); // Assuming index.html is stagiaire login
        }
        exit;
    }

    // Get the referring page to determine login type
    $referer = $_SERVER['HTTP_REFERER'] ?? ''; // Get referer safely

    // Determine User Type and Attempt Login
    $user = null;
    $userType = null;
    $loginPage = '../html/index.html'; // Default to stagiaire login page
    $dashboardPage = 'dashboard.php'; // Default dashboard

    if (strpos($referer, 'encadrant.html') !== false) {
        // --- Encadrant Login ---
        $userType = 'encadrant';
        $loginPage = '../html/encadrant.html';
        $dashboardPage = 'dashboard_encadrant.php'; // CHANGE to your encadrant dashboard page

        $encadrant = new Encadrant(); // Assumes Encadrant class is defined
        $user = $encadrant->login($email, $password); // Assumes login method returns user data array or false

    } elseif (strpos($referer, 'admin.html') !== false) {
         // --- Admin Login (Optional - Add if needed) ---
         // require_once 'admin.php';
         // $userType = 'admin';
         // $loginPage = '../html/admin.html';
         // $dashboardPage = 'dashboard_admin.php';
         // $admin = new Admin();
         // $user = $admin->login($email, $password);

    } else {
        // --- Stagiaire Login (Default) ---
        $userType = 'stagiaire';
        $loginPage = '../html/index.html'; // Or stagiaire.html if you have a separate one
        $dashboardPage = 'dashboard.php'; // Correct dashboard for stagiaire

        $stagiaire = new Stagiaire(); // Assumes Stagiaire class is defined
        $user = $stagiaire->login($email, $password); // Assumes login method returns user data array or false
    }

    // --- Process Login Result ---
    if ($user && is_array($user)) {
        // Login successful! Set session variables.
        // **Crucially, adapt the array keys ('ID_STG', 'NOM_STG', etc.)
        // **to match EXACTLY what your login methods return!**

        $_SESSION['user_type'] = $userType; // Store the user type

        if ($userType === 'stagiaire') {
            $_SESSION['id_stg'] = $user['ID_STG']; // Use the correct key from your Stagiaire class login method
            $_SESSION['nom'] = $user['NOM_STG'];   // Use the correct key
            $_SESSION['prenom'] = $user['PRN_STG']; // Use the correct key
            // Add any other needed stagiaire info:
            $_SESSION['niveau_id'] = $user['ID_NIV']; // Example if needed
            $_SESSION['email'] = $user['MAIL_STG']; // Example if needed

        } elseif ($userType === 'encadrant') {
            $_SESSION['id_enc'] = $user['ID_ENC']; // Use the correct key from your Encadrant class login method
            $_SESSION['nom'] = $user['NOM_ENC'];   // Use the correct key
            $_SESSION['prenom'] = $user['PRN_ENC']; // Use the correct key
            // Add any other needed encadrant info:
             $_SESSION['email'] = $user['MAIL_ENC']; // Example

        }
        // Add elseif for 'admin' if implemented

        // Redirect to the appropriate dashboard
        header("Location: $dashboardPage");
        exit;

    } else {
        // Login failed - Redirect back to the specific login page with an error
        header("Location: $loginPage?error=invalid_credentials");
        exit;
    }

} else {
    // If accessed directly without POST, redirect to a default page (e.g., main login selector)
     header('Location: ../index.html'); // Redirect to the main user type selection page
    exit;
}
?>