<?php

session_start();
include "../connect.php";
$message = "";
$user_type = $_SESSION['user_type'];
$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;

$faculty_id = $_SESSION['user_id'];
$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;

if ($user_type == 'student') {
    header("Location: ../student/student_home.php?section_id=$section_id");
}

$timeout_duration = 3600;

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

$_SESSION['last_activity'] = time();

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../output.css" rel="stylesheet">
    <title>Archived Classes</title>
    <link rel="shortcut icon" href="../../assets/favicon.ico" type="image/x-icon">

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

<body class="bg-bg font-primary text-white my-8 mx-8 h-[100vh] overflow-x-auto">


    <div class="container mx-auto">
        <div class="flex flex-row items-center gap-2 md:hidden">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>
            <a href="./faculty_home.php?section_id=<?php echo $section_id; ?>"><span class="text-lg">SMC NSTP</span></a>
        </div>

        <div class="mt-4 p-2 sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[180px]">
            <a href="./faculty_home.php?section_id=<?php echo $section_id; ?>"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                    <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                </svg></a>
        </div>

        <div class="flex h-screen w-full">
            <?php include '../sidebar_faculty.php'; ?>

            <div class="flex-grow p-4 sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[180px]">

                <div class="">
                    <h2 class="text-[24px]">Archived Classes</h2>
                </div>

                <div class="w-full mt-10">
                    <?php
                    // Fetching the archived classes and displaying the button
                    $query = "SELECT archived_year, GROUP_CONCAT(user_id) as user_ids FROM user WHERE archive = 1 GROUP BY archived_year";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $archived_year = $row['archived_year'];
                        $user_ids = $row['user_ids'];

                        echo "
                    <form action='export_archived.php' method='post' class='inline'>
                        <input type='hidden' name='archived_year' value='$archived_year'>
                        <input type='hidden' name='user_ids' value='$user_ids'>
                        <button type='submit' class='download-btn hover:text-primary hover:underline'>
                            --- $archived_year NSTP Class [Click to download] ---
                        </button>
                    </form>";
                    }
                    ?>
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

        <script>
            document.querySelectorAll('.download-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userIds = this.dataset.userIds;
                    window.location.href = 'download_archived.php?user_ids=' + userIds;
                });
            });
        </script>

</body>

</html>
