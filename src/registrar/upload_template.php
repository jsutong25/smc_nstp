<?php
session_start();

// Ensure the directory for uploads exists
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (isset($_FILES['template'])) {
    $file = $_FILES['template'];
    $upload_path = $upload_dir . basename($file['name']);
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $_SESSION['template_path'] = $upload_path; // Save path in session
        $_SESSION['message'] = "Template uploaded successfully.";
    } else {
        $_SESSION['message'] = "Failed to upload template.";
    }
} else {
    $_SESSION['message'] = "No file selected.";
}

header("Location: registrar_home.php");
exit();
?>
