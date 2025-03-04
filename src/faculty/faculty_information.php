<?php

session_start();
include "../connect.php";
$message = "";

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
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


$sql_sections = "SELECT u.*, GROUP_CONCAT(s.section_name SEPARATOR ', ') AS sections
        FROM user u
        LEFT JOIN section s ON u.user_id = s.faculty_id
        WHERE u.user_type = 'faculty' OR u.user_type = 'nstp_coordinator'
        GROUP BY u.user_id";
$stmt = $conn->prepare($sql_sections);
$stmt->execute();
$sections_result = $stmt->get_result();

$sql_sections2 = "SELECT section.section_id, section.section_name, section.schedule, user.last_name, section.code 
                 FROM section 
                 JOIN user ON section.faculty_id = user.user_id";
$stmt2 = $conn->prepare($sql_sections2);
$stmt2->execute();
$sections_result2 = $stmt2->get_result();

$course_select = "SELECT * FROM course";
$stmt_course = $conn->prepare($course_select);
$stmt_course->execute();
$course_results = $stmt_course->get_result();

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

    header("Location: ./sections.php?section_id=<?php echo $section_id; ?>");
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
    <title>Faculty Information</title>
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

<body class="bg-bg font-primary text-white my-8 mx-8 h-[100vh] overflow-y-auto overflow-x-auto">


    <div class="container mx-auto pb-10 mb-10">
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

        <div class="flex w-full">
        <?php include '../sidebar_faculty.php'; ?>

            <div class="flex-grow p-4 sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[180px]">

                
                <div class="h-full pb-10">
                    <!-- Faculty Info -->
                    <div class="">
                        <h2 class="text-[24px]">Faculty Information</h2>
                    </div>

                    <div class="w-[20%] flex mb-8 gap-1">
                        <a class="bg-primary py-3 text-center w-full rounded-full mt-8 hover:cursor-pointer hover:bg-red-700 flex items-center justify-center" href="./new_faculty.php?section_id=<?php echo $section_id; ?>">
                            <svg class="transition ease-linear duration-200 hover:text-primary mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                    <path d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12Zm10-8a8 8 0 1 0 0 16a8 8 0 0 0 0-16Z" />
                                    <path d="M13 7a1 1 0 1 0-2 0v4H7a1 1 0 1 0 0 2h4v4a1 1 0 1 0 2 0v-4h4a1 1 0 1 0 0-2h-4V7Z" />
                                </g>
                            </svg>
                            Add new faculty
                        </a>
                    </div>

                    <div class="bg-white p-2 rounded-md mt-8">
                        <table id="faculty" class="display" style="width:100%">
                            <thead class="text-gray-900" style="width:100%; border: 1px solid #ccc;">
                                <tr>
                                    <th>First Name</th>
                                    <th>Middle Name</th>
                                    <th>Last Name</th>
                                    <th>Extension</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Birthday</th>
                                    <th>Sex</th>
                                    <th>Barangay</th>
                                    <th>City</th>
                                    <th>Province</th>
                                    <th>Contact Number</th>
                                    <th>Section/s</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                if ($sections_result->num_rows > 0) {
                                    while ($row = $sections_result->fetch_assoc()) {

                                        echo "<tr class='text-gray-900' style='width:100%; border: 1px solid #ccc;'>
                                                <td>{$row['first_name']}</td>
                                                <td>{$row['middle_name']}</td>
                                                <td>{$row['last_name']}</td>
                                                <td>{$row['extension_name']}</td>
                                                <td>{$row['user_type']}</td>
                                                <td>{$row['email']}</td>
                                                <td>{$row['birthday']}</td>
                                                <td>{$row['sex']}</td>
                                                <td>{$row['barangay']}</td>
                                                <td>{$row['city']}</td>
                                                <td>{$row['province']}</td>
                                                <td>{$row['contact_number']}</td>
                                                <td>{$row['sections']}</td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='15'>No data found</td></tr>";
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sections -->
                    <div class="mt-10">
                        <h2 class="text-[24px]">Sections</h2>
                    </div>

                    <div class="w-[20%] flex mb-8 gap-1">
                        <a class="bg-primary py-3 text-center w-full rounded-full mt-8 hover:cursor-pointer hover:bg-red-700 flex justify-center" href="./new_section.php?section_id=<?php echo $section_id; ?>">
                            <svg class="transition ease-linear duration-200 hover:text-primary mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                    <path d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12Zm10-8a8 8 0 1 0 0 16a8 8 0 0 0 0-16Z" />
                                    <path d="M13 7a1 1 0 1 0-2 0v4H7a1 1 0 1 0 0 2h4v4a1 1 0 1 0 2 0v-4h4a1 1 0 1 0 0-2h-4V7Z" />
                                </g>
                            </svg>
                            Add new section
                        </a>
                    </div>

                    <div class="bg-white w-[80%] p-2 rounded-md mt-8 pb-10 mb-10">
                        <table id="section" class="display" style="width:100%; border: 1px solid #ccc;">
                            <thead class="text-gray-900" style="width:100%; border: 1px solid #ccc;">
                                <tr>
                                    <th>Section</th>
                                    <th>Schedule</th>
                                    <th>Faculty</th>
                                    <th>Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-900" style="width:100%; border: 1px solid #ccc;">
                                <?php
                                if ($sections_result2->num_rows > 0) {
                                    while ($row = $sections_result2->fetch_assoc()) {
                                        echo "<tr class='py-2' style='width:100%; border: 1px solid #ccc;'>
                                                <td>{$row['section_name']}</td>
                                                <td>{$row['schedule']}</td>
                                                <td>{$row['last_name']}</td>
                                                <td>{$row['code']}</td>
                                                <td class='text-center'>
                                                    <a class='bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500 transition inline-block text-center' href='./edit_section.php?section_id={$row['section_id']}'  style='min-width: 80px; display: inline-block;'>Edit</a>

                                                    <form method='POST' style='display:inline-block;'>
                                                        <input type='hidden' name='section_id_to_delete' value='" . $row['section_id'] . "'>
                                                        <input type='hidden' name='delete_section' value='1'>
                                                        <button type='submit' class='bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition' 
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

                    <!-- Course -->
                    <div class="mt-10">
                        <h2 class="text-[24px]">Course</h2>
                    </div>

                    <div class="w-[20%] flex mb-8 gap-1">
                        <a class="bg-primary py-3 text-center w-full rounded-full mt-8 hover:cursor-pointer hover:bg-red-700 flex justify-center" href="./new_course.php?section_id=<?php echo $section_id; ?>">
                            <svg class="transition ease-linear duration-200 hover:text-primary mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                    <path d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12Zm10-8a8 8 0 1 0 0 16a8 8 0 0 0 0-16Z" />
                                    <path d="M13 7a1 1 0 1 0-2 0v4H7a1 1 0 1 0 0 2h4v4a1 1 0 1 0 2 0v-4h4a1 1 0 1 0 0-2h-4V7Z" />
                                </g>
                            </svg>
                            Add new course
                        </a>
                    </div>

                    <div class="bg-white w-[80%] p-2 rounded-md mt-8 pb-10 mb-10">
                        <table id="course" class="display" style="width:100%; border: 1px solid #ccc;">
                            <thead class="text-gray-900" style="width:100%; border: 1px solid #ccc;">
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Insitutional Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-900" style="width:100%; border: 1px solid #ccc;">
                                <?php
                                if ($course_results->num_rows > 0) {
                                    while ($row = $course_results->fetch_assoc()) {
                                        echo "<tr class='py-2' style='width:100%; border: 1px solid #ccc;'>
                                                <td>{$row['course_id']}</td>
                                                <td>{$row['name']}</td>
                                                <td>{$row['insti_code']}</td>
                                                <td class='text-center'>
                                                    <a class='bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500 transition inline-block text-center' href='./edit_course.php?course_id={$row['course_id']}&section_id={$section_id}'  style='min-width: 80px; display: inline-block;'>Edit</a>

                                                    <form method='POST' style='display:inline-block;'>
                                                        <input type='hidden' name='course_id_to_delete' value='" . $row['course_id'] . "'>
                                                        <input type='hidden' name='delete_course' value='1'>
                                                        <button type='submit' class='bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition' 
                                                                style='min-width: 80px; display: inline-block; text-align: center;' 
                                                                onclick='return confirm(\"Are you sure you want to delete this course and its corresponding values?\");'>Delete</button>
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
            $('#faculty').DataTable({
                dom: 'frtip',
                language: {
                    info: "Displaying _START_ to _END_ of _TOTAL_ entries", // Custom text for the entries display
                    emptyTable: "No data available", // Text when the table is empty
                    zeroRecords: "No matching records found", // Text when no records match
                }
            });
        });

        $(document).ready(function() {
            $('#section').DataTable({
                dom: 'frtip',
                language: {
                    info: "Displaying _START_ to _END_ of _TOTAL_ entries", // Custom text for the entries display
                    emptyTable: "No data available", // Text when the table is empty
                    zeroRecords: "No matching records found", // Text when no records match
                }
            });
        });

        $(document).ready(function() {
            $('#course').DataTable({
                dom: 'frtip',
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