<?php

session_start();
include "../connect.php";
$message = "";

$faculty_id = $_SESSION['user_id'];
$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;

$timeout_duration = 3600;

$user_type = $_SESSION['user_type'];

if (!$user_type == 'admin') {
    header("Location: ../index.php");
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    $_SESSION['message'] = "You are not logged in.";
    header("Location: ../index.php");
    exit();
}


if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$sql_sections = "SELECT u.*, GROUP_CONCAT(s.section_name SEPARATOR ', ') AS sections
        FROM user u
        LEFT JOIN section s ON u.user_id = s.faculty_id
        WHERE u.user_type IN ('faculty', 'nstp_coordinator')
        GROUP BY u.user_id";
$stmt = $conn->prepare($sql_sections);
$stmt->execute();
$sections_result = $stmt->get_result();

$sql_sections2 = "SELECT section.section_id, section.section_name, section.schedule, user.last_name 
                 FROM section 
                 JOIN user ON section.faculty_id = user.user_id";
$stmt = $conn->prepare($sql_sections2);
$stmt->execute();
$sections_result2 = $stmt->get_result();

if (isset($_POST['delete_section'])) {
    $section_id_to_delete = intval($_POST['section_id_to_delete']);

    $sql_delete_section = "DELETE FROM section WHERE section_id = ?";
    $stmt = $conn->prepare($sql_delete_section);
    $stmt->bind_param("i", $section_id_to_delete);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Section deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete the section.";
    }

    $stmt->close();

    header("Location: ./admin_home.php");
    exit();
}


$_SESSION['last_activity'] = time();

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../output.css" rel="stylesheet">
    <title>Admin</title>

    <style>
        /* Ensure the table has a light background */
        table.dataTable {
            background-color: white;
            /* Set table background to white */
            color: black;
            /* Set text color to black */
        }

        /* Adjust table header styling */
        table.dataTable thead th {
            background-color: #f1f1f1;
            /* Light gray header background */
            color: black;
            /* Black text for header */
            font-weight: bold;
            /* Bold header text */
        }

        /* Row hover effects */
        table.dataTable tbody tr:hover {
            background-color: #e0e0e0;
            /* Light gray on hover */
        }

        /* Table cell styling */
        table.dataTable tbody td {
            padding: 8px;
            /* Add some padding to cells */
            border-bottom: 1px solid #ddd;
            /* Light border for cells */
        }

        /* Table footer styling */
        table.dataTable tfoot th {
            background-color: #f1f1f1;
            /* Light gray footer background */
            color: black;
            /* Black text for footer */
        }

        /* Search input styling */
        div.dataTables_filter input {
            border: 2px solid #fff;
            /* Blue border */
            border-radius: 4px;
            /* Rounded corners */
            padding: 5px;
            /* Add some padding */
            margin-left: 0.5em;
            /* Space between label and input */
            outline: none;
            /* Remove outline */
            color: #fff;
            /* Text color */
        }

        /* Search input focus effect */
        div.dataTables_filter input:focus {
            border-color: #0056b3;
            /* Darker blue on focus */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            /* Subtle shadow effect */
        }

        /* Excel button styling */
        .dt-button {
            background-color: #fff;
            /* Green background */
            color: white;
            /* White text */
            border: none;
            /* No border */
            padding: 8px 12px;
            /* Padding */
            border-radius: 4px;
            /* Rounded corners */
            font-size: 14px;
            /* Font size */
            cursor: pointer;
            /* Pointer cursor on hover */
            margin-left: 10px;
            /* Space between buttons */
        }

        /* Excel button hover effect */
        .dt-button:hover {
            background-color: #218838;
            /* Darker green on hover */
        }

        /* Custom text for showing entries */
        div.dataTables_info {
            color: #333;
            /* Text color */
            font-weight: bold;
            /* Bold text */
        }
    </style>
</head>

<body class="bg-bg font-primary text-white my-8 mx-8 box-border">
    <div class="container mx-auto">
        <div class="flex flex-row items-center gap-2 w-full md:hidden">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>
            <a href="./faculty_home.php?section_id=<?php echo $section_id; ?>"><span class="text-lg">SMC NSTP</span></a>
        </div>

        <div class="flex h-full w-full">
            <?php include './sidebar_admin.php'; ?>

            
            <div class="flex-grow p-4 sm:ml-[210px]">
                <h1 class="text-[22px] mb-8">Admin Dashboard</h1>
                <div class="bg-white p-2 rounded-md mt-8">

                    <div class="flex justify-between items-center mb-2 pb-2">
                        <h2 class="text-[24px] text-gray-900 mx-2">Faculty</h2>
                        <a class="hover:bg-gray-400 text-gray-900 border-2 border-gray-900 rounded-full px-2 mx-2" href="./new_faculty.php">+ Add</a>
                    </div>
                    <table id="student" class="display text-gray-900 mx-2" style="width:100%">
                        <thead>
                            <tr class="text-center font-bold text-lg underline">
                                <th>Name</th>
                                <th>Section</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($sections_result->num_rows > 0) {
                                while ($row = $sections_result->fetch_assoc()) {
                                    echo "<tr class='border border-gray-900'>
                            <td class='text-center border border-gray-900'>{$row['last_name']}, {$row['first_name']} {$row['middle_name']}</td>
                            <td class='text-center border border-gray-900'>{$row['sections']}</td>
                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='15'>No data found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white p-2 rounded-md mt-8">

                    <div class="flex justify-between items-center mb-2 pb-2">
                        <h2 class="text-[24px] text-gray-900 mx-2">Section</h2>
                        <a class="hover:bg-gray-400 text-gray-900 border-2 border-gray-900 rounded-full px-2 mx-2" href="./new_section.php">+ Add</a>
                    </div>
                    <table id="student" class="display text-gray-900 mx-2" style="width:100%">
                        <thead>
                            <tr class="text-center font-bold text-lg underline">
                                <th>Section</th>
                                <th>Schedule</th>
                                <th>Faculty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($sections_result2->num_rows > 0) {
                                while ($row = $sections_result2->fetch_assoc()) {
                                    echo "<tr class='border border-gray-900 text-center'>
                                                <td class='border border-gray-900'>{$row['section_name']}</td>
                                                <td class='border border-gray-900'>{$row['schedule']}</td>
                                                <td class='border border-gray-900'>{$row['last_name']}</td>
                                                <td class='text-center'>
                                                    <a class='bg-yellow-400 text-white px-3 py-2 rounded hover:bg-yellow-500 transition inline-block text-center' href='./edit_section.php?section_id={$row['section_id']}'  style='min-width: 80px; display: inline-block;'>Edit</a>

                                                    <form method='POST' style='display:inline-block;'>
                                                        <input type='hidden' name='section_id_to_delete' value='" . $row['section_id'] . "'>
                                                        <input type='hidden' name='delete_section' value='1'>
                                                        <button type='submit' class='bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 transition' 
                                                                style='min-width: 80px; display: inline-block; text-align: center;' 
                                                                onclick='return confirm(\"Are you sure you want to delete this section and its corresponding values?\");'>Delete</button>
                                                    </form>
                                                </td>
                                            </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='15'>No data found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const button = document.querySelector('[data-drawer-toggle="logo-sidebar"]');
        const sidebar = document.getElementById('logo-sidebar');


        const toggleSidebar = () => {
            sidebar.classList.toggle('-translate-x-full');
        };


        button.addEventListener('click', toggleSidebar);


        document.addEventListener('click', (event) => {

            if (!sidebar.contains(event.target) && !button.contains(event.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
</body>

</html>