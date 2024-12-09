<?php
session_start();
require('../../libs/fpdf.php');
include "../connect.php";

$conn = new mysqli('localhost', 'root', '', 'smc_nstpms');

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $template_path = $_POST['template_path'] ?? '';

    $student = $conn->query("SELECT last_name, first_name, middle_name FROM user WHERE user_id = $user_id")->fetch_assoc();
    $last_name = $student['last_name'];
    $first_name = $student['first_name'];
    $middle_name = $student['middle_name'];

    // Set up PDF certificate
    $pdf = new FPDF('L', 'mm', 'A4'); // Landscape mode
    $pdf->AddPage();

    // Check if template path is provided and file exists
    if (!empty($template_path) && file_exists($template_path)) {
        // Set the template image as the background
        $pdf->Image($template_path, 0, 0, 297, 360); // Fit the design to A4 landscape
    }

    // Add name at a fixed position
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(100, 120); // Position the name line
    $pdf->Cell(100, 10, $last_name . ', ' . $first_name . ' ' . $middle_name, 0, 0, 'C');

    // Output the PDF for download
    $pdf->Output("D", "Certificate_$last_name,$first_name.pdf");
} else {
    echo "Error: Student or template not specified.";
}
?>
