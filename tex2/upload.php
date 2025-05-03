<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$uploadDir = 'uploads/';
$maxFileSize = 10 * 1024 * 1024; // 10MB
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'application/pdf' => 'pdf',
    'text/plain' => 'txt'
];

// Create uploads directory if it doesn't exist
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        die(json_encode(['success' => false, 'message' => 'Failed to create upload directory']));
    }
}

// Check if directory is writable
if (!is_writable($uploadDir)) {
    die(json_encode(['success' => false, 'message' => 'Upload directory is not writable']));
}

$response = [
    'success' => false,
    'message' => '',
    'files' => []
];

try {
    if (!isset($_FILES['files'])) {
        throw new Exception('No files uploaded');
    }

    $files = $_FILES['files'];
    $uploadedFiles = [];

    // Process each file
    for ($i = 0; $i < count($files['name']); $i++) {
        $fileName = $files['name'][$i];
        $fileType = $files['type'][$i];
        $fileSize = $files['size'][$i];
        $fileTmp = $files['tmp_name'][$i];
        $fileError = $files['error'][$i];

        // Check for errors
        if ($fileError !== UPLOAD_ERR_OK) {
            throw new Exception('Error uploading file: ' . $fileName . ' - Error code: ' . $fileError);
        }

        // Validate file size
        if ($fileSize > $maxFileSize) {
            throw new Exception('File too large: ' . $fileName . ' (' . $fileSize . ' bytes)');
        }

        // Validate file type
        if (!isset($allowedTypes[$fileType])) {
            throw new Exception('Invalid file type: ' . $fileType . ' for file: ' . $fileName);
        }

        // Generate unique filename with proper extension
        $extension = $allowedTypes[$fileType];
        $uniqueName = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $uniqueName;

        // Move uploaded file
        if (move_uploaded_file($fileTmp, $targetPath)) {
            $uploadedFiles[] = $uniqueName;
            error_log("Successfully uploaded file: " . $fileName . " to " . $targetPath);
        } else {
            throw new Exception('Failed to move uploaded file: ' . $fileName . ' to ' . $targetPath);
        }
    }

    $response['success'] = true;
    $response['message'] = 'Files uploaded successfully';
    $response['files'] = $uploadedFiles;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Upload error: " . $e->getMessage());
}

echo json_encode($response); 