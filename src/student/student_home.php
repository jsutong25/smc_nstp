<?php

session_start();
include "../connect.php";
$message = "";

$user_id = $_SESSION['user_id'];

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

$activities_result = null;
$documentation_result = null;

$sql_sections = "SELECT section_id, section_name FROM section WHERE faculty_id = ? OR section_name ='All' ORDER BY CASE WHEN section_name ='All' THEN 0 ELSE 1 END, section_name ASC";
$stmt = $conn->prepare($sql_sections);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$sections_result = $stmt->get_result();

$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;

$section_name = '';

if ($section_id) {
    $sql_sectionname = "SELECT section_name FROM section WHERE section_id = ?";
    $stmt = $conn->prepare($sql_sectionname);
    if ($stmt) {
        $stmt->bind_param("i", $section_id);
        if ($stmt->execute()) {
            $result_sectionname = $stmt->get_result();
            if ($result_sectionname->num_rows > 0) {
                $row_sectionname = $result_sectionname->fetch_assoc();
                $section_name = $row_sectionname['section_name'];
            } else {
                $section_name = "No Section Found";
            }
        } else {
            echo "Query execution failed: " . $stmt->error;
        }
    } else {
        echo "Query preparation failed: " . $conn->error;
    }

    $activities_sql = "
        SELECT a.*, d.documentation_id
        FROM activities a
        LEFT JOIN documentation d ON a.activity_id = d.activity_id
        WHERE a.section_id = ? AND CONCAT(a.date, ' ', a.time) > NOW()
        ORDER BY a.date, a.time ASC";

    $stmt = $conn->prepare($activities_sql);
    if ($stmt) {
        $stmt->bind_param("i", $section_id);
        if ($stmt->execute()) {
            $activities_result = $stmt->get_result();
        } else {
            echo "Query execution failed: " . $stmt->error;
        }
    } else {
        echo "Query preparation failed: " . $conn->error;
    }
}

$sql = "SELECT * FROM activities WHERE CONCAT(date, ' ', time) > NOW() ORDER by date, time ASC";
$result = mysqli_query($conn, $sql);

$_SESSION['last_activity'] = time();

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../output.css" rel="stylesheet">
    <title>Student</title>
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
            <?php include '../sidebar_student.php'; ?>

            <div class="flex-grow p-4 sm:ml-64 md:ml-52 lg:ml-[210px] overflow-hidden max-w-full">

                <div class="w-full">
                    <h2 class="text-[32px] mt-5 mb-8 font-secondary">Section - <?php echo htmlspecialchars($section_name); ?></h2>

                    <div class="flex justify-between items-center">
                        <h2 class="text-[18px] mb-8 hover:text-gray-400"><a href="../activities.php?section_id=<?php echo $section_id; ?>">Upcoming Activities</a></h2>
                    </div>

                    <div>
                        <?php
                        if ($activities_result !== null) {
                            if (mysqli_num_rows($activities_result) > 0) {
                                $counter = 0;
                                while ($row = mysqli_fetch_assoc($activities_result)) {
                                    if ($counter >= 3) {
                                        break;
                                    }
                                    $formatted_date = date("F j, Y", strtotime($row['date']));
                                    $formatted_time = date("h:i A", strtotime($row['time']));
                                    $activity_name = $row['activity_name'];
                                    $description = $row['description'];

                                    $short_title = substr($activity_name, 0, 25) . (strlen($activity_name) > 25 ? '...' : '');
                                    $short_description = substr($description, 0, 10) . (strlen($description) > 20 ? '...' : '');
                        ?>
                                    <div class="flex gap-1 mb-4">
                                        <div class="w-[100px] flex flex-col text-subtext items-start">
                                            <p class="text-[16px]"><?php echo $formatted_date; ?></p>
                                            <p class="text-[16px]"><?php echo $formatted_time; ?></p>
                                        </div>

                                        <div class="w-1.5 h-auto bg-gray-500"></div>

                                        <a class="transition ease-linear hover:bg-gray-400 hover:bg-opacity-15 w-full sm:w-12 md:w-fit lg:w-full mx-8 ml-5" href="../view_activity.php?activity_id=<?php echo $row['activity_id']; ?>&section_id=<?php echo $section_id; ?>&documentation_id=<?php echo $row['documentation_id']; ?>">
                                            <div class="flex-1">
                                                <h5 class="block md:hidden"><?php echo $short_title; ?></h5>
                                                <h5 class="hidden md:block text-[16px]"><?php echo $activity_name; ?></h5>

                                                <p class="block md:hidden text-subtext"><?php echo $short_description; ?></p>
                                                <p class="hidden md:block text-subtext">
                                                    <?php echo (strlen($description) > 40) ? substr($description, 0, 40) . '...' : $description; ?>
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                        <?php
                                    $counter++;
                                }
                            } else {
                                echo "<p class='italic text-[16px] mt-4'>No upcoming activities.</p>";
                            }
                        } else {
                            echo "<p class='italic text-center text-[16px]'>Select a section first.</p>";
                        }
                        ?>
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