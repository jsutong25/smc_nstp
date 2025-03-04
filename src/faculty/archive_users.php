<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../connect.php'; // Include your database connection

    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data['user_id']) && !empty($data['program'])) {
        $user_ids = $data['user_id']; // Array of user IDs
        $program = $conn->real_escape_string($data['program']); // Sanitize program input
        $current_year = date("Y"); // Get the current year

        $ids = implode(",", array_map('intval', $user_ids));

        // Update only students in the correct program
        $sql = "UPDATE user SET archive = 1, archived_year = $current_year WHERE user_id IN ($ids) AND program = '$program'";

        if ($conn->query($sql) === TRUE) {
            echo "Users archived successfully in the $program program.";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "No user IDs or program provided.";
    }

    $conn->close();
}
?>
