<?php
// Start output buffering
ob_start();

// Include database connection
include '../connect.php';

// Include the Composer autoloader
require_once '../../vendor/autoload.php';

// Use the PhpSpreadsheet namespace
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check if the form data is set
if (isset($_POST['user_ids']) && isset($_POST['archived_year'])) {
    $user_ids = $_POST['user_ids'];
    $archived_year = $_POST['archived_year'];

    // Set headers to trigger a file download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $archived_year . '_NSTP_Class.xlsx"');
    header('Cache-Control: max-age=0');

    // Create a new spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set column headers
    $headers = [
        'No.', 'Award Year', 'Program', 'Region', 'Serial Number', 'Last Name',
        'First Name', 'Middle Name', 'Extension Name', 'Birthdate', 'Sex',
        'Barangay', 'City', 'Province', 'HEI Name', 'Institutional Code',
        'Types of HEIS', 'Program Level Code', 'Main Program Name', 'Email Address', 'Contact Number'
    ];

    // Add headers to the Excel sheet
    $column = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($column . '1', $header);
        $column++;
    }

    // Create a new database connection
    $conn = new mysqli('localhost', 'root', '', 'smc_nstpms');

    // Check database connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Query archived data for the specific year
    $sql = "SELECT * FROM user WHERE user_id IN ($user_ids) AND archive = 1";
    $result = $conn->query($sql);

    // Add data rows if records are found
    if ($result->num_rows > 0) {
        $rowIndex = 2; // Start adding data from row 2
        $number = 1;

        while ($row = $result->fetch_assoc()) {
            $sheet->setCellValue('A' . $rowIndex, $number); // Serial number
            $sheet->setCellValue('B' . $rowIndex, $archived_year); // Award Year
            $sheet->setCellValue('C' . $rowIndex, $row['program']);
            $sheet->setCellValue('D' . $rowIndex, '10'); // Region (static value)
            $sheet->setCellValue('E' . $rowIndex, $row['serial_number']);
            $sheet->setCellValue('F' . $rowIndex, $row['last_name']);
            $sheet->setCellValue('G' . $rowIndex, $row['first_name']);
            $sheet->setCellValue('H' . $rowIndex, $row['middle_name']);
            $sheet->setCellValue('I' . $rowIndex, $row['extension_name']);
            $sheet->setCellValue('J' . $rowIndex, $row['birthday']);
            $sheet->setCellValue('K' . $rowIndex, $row['sex']);
            $sheet->setCellValue('L' . $rowIndex, $row['barangay']);
            $sheet->setCellValue('M' . $rowIndex, $row['city']);
            $sheet->setCellValue('N' . $rowIndex, $row['province']);
            $sheet->setCellValue('O' . $rowIndex, 'St. Michael\'s College of Iligan, Inc.'); // HEI Name
            $sheet->setCellValue('P' . $rowIndex, '12062'); // Institutional Code
            $sheet->setCellValue('Q' . $rowIndex, 'Private'); // Types of HEIS
            $sheet->setCellValue('R' . $rowIndex, '340101'); // Program Level Code
            $sheet->setCellValue('S' . $rowIndex, $row['course']);
            $sheet->setCellValue('T' . $rowIndex, $row['email']);
            $sheet->setCellValue('U' . $rowIndex, $row['contact_number']);

            $rowIndex++;
            $number++;
        }

        // Output the Excel file to the browser
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output'); // This will prompt the user to save the file
    } else {
        echo "No archived data found.";
    }

    // Close the database connection
    $conn->close();
} else {
    echo "Invalid request.";
}

// End output buffering and flush
ob_end_flush();
?>
