<?php
session_start();
require('../../libs/fpdf.php');
include "../connect.php";

$conn = new mysqli('localhost', 'root', '', 'smc_nstpms');

$message = "";
$user_id = $_SESSION['user_id'];
$timeout_duration = 3600;

// Check login status
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

$_SESSION['last_activity'] = time();
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../output.css" rel="stylesheet">
    <title>Student</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" />

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

        <div class="flex h-screen w-full">
            <?php include '../registrar_sidebar.php'; ?>

            <div class="flex-grow p-4 sm:ml-[210px]">
                <h2 class="text-[32px] mt-5 mb-8 font-secondary">Registrar Dashboard</h2>

                <div class="bg-white p-2 rounded-md w-fit">

                    <!-- Add file upload for certificate design -->
                    <form action="upload_template.php" method="POST" enctype="multipart/form-data">
                        <label for="template" class="block text-gray-900">Upload Certificate Design:</label>
                        <input type="file" name="template" id="template" class="bg-gray-100 text-gray-900 p-2 rounded mb-4">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                            Upload Design
                        </button>
                    </form>

                    <!-- Table of students -->
                    <table id="student" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold text-gray-900">Name</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-900">Generate Certificate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch and display students
                            $result = $conn->query("SELECT user_id, last_name, first_name, middle_name FROM user WHERE user_type = 'student'");
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                    <td class='py-3 px-4 text-gray-900'>{$row['last_name']}, {$row['first_name']} {$row['middle_name']}</td>
                    <td class='py-3 px-4'>
                        <form action='generate_certificate.php' method='POST'>
                            <input type='hidden' name='user_id' value='{$row['user_id']}'>
                            <input type='hidden' name='template_path' value='{$_SESSION['template_path']}' />
                            <button type='submit' class='bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition'>
                                Generate
                            </button>
                        </form>
                    </td>
                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>



        <div id="messageModal" class="fixed top-0 inset-0 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-5 relative">
                <p class="mt-2 text-center text-gray-600"><?php echo $message; ?></p>
                <div class="mt-4 flex justify-center">
                    <button onclick="closeModal()" class="bg-primary text-white font-semibold py-2 px-4 rounded-full">Close</button>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#student').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['excel'],
                    language: {
                        info: "Displaying _START_ to _END_ of _TOTAL_ entries", // Custom text for the entries display
                        emptyTable: "No data available", // Text when the table is empty
                        zeroRecords: "No matching records found", // Text when no records match
                    }
                });
            });
        </script>

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

        <script>
            window.onload = function() {
                <?php if (!empty($message)): ?>
                    document.getElementById('messageModal').classList.remove('hidden');
                <?php endif; ?>
            };

            function closeModal() {
                document.getElementById('messageModal').classList.add('hidden');
            }
        </script>
</body>

</html>