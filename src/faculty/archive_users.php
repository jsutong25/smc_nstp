<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../connect.php'; // Include your database connection

    // Decode the incoming JSON request
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data['user_id'])) {
        $user_ids = $data['user_id']; // This contains the array of user IDs
        $current_year = date("Y"); // Get the current year

        // Sanitize user IDs and prepare for SQL query
        $ids = implode(",", array_map('intval', $user_ids));

        // Update the archive status and (optional) archived_year
        $sql = "UPDATE user SET archive = 1, archived_year = $current_year WHERE user_id IN ($ids)";

        if ($conn->query($sql) === TRUE) {
            echo "Users archived successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "No user IDs provided.";
    }

    $conn->close();
}
?>
