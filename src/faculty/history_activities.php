<?php

session_start();
include "../connect.php";
$message = "";

$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

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
    header("Location: ../sign_in.php");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../sign_in.php");
    exit();
}

$activities_result = null;
$documentation_result = null;

$sql_sections = "SELECT section_id, section_name FROM section WHERE faculty_id = ? OR section_name ='All' ORDER BY CASE WHEN section_name ='All' THEN 0 ELSE 1 END, section_name ASC";
$stmt = $conn->prepare($sql_sections);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$sections_result = $stmt->get_result();

if ($section_id) {
    $sql = "
        SELECT a.*, d.documentation_id
        FROM activities a
        LEFT JOIN documentation d ON a.activity_id = d.activity_id
        WHERE a.section_id = ? AND a.date < CURDATE()
        ORDER BY a.date, a.time DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $section_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
        } else {
            echo "Query execution failed: " . $stmt->error;
        }
    } else {
        echo "Query preparation failed: " . $conn->error;
    }
} else {
    echo "<p>No section selected</p>";
    $result = null;
}


$_SESSION['last_activity'] = time();

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../output.css" rel="stylesheet">
    <title>History</title>
    <link rel="shortcut icon" href="../../assets/favicon.ico" type="image/x-icon">
</head>

<body class="bg-bg font-primary text-white mx-8 h-[100vh] overflow-hidden">
    <div class="container mx-auto">
        <div class="flex flex-row items-center gap-2">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>
            <a href="./faculty_home.php?section_id=<?php echo $section_id; ?>"><span class="text-lg">SMC NSTP</span></a>
        </div>

        <div class="mt-4 p-2 sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[180px]">
            <a href="../activities.php?section_id=<?php echo $section_id; ?>"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                    <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                </svg></a>
        </div>

        <div class="flex h-screen w-full overflow-hidden">
            <?php include '../sidebar_faculty.php'; ?>

            <div class="flex-grow p-4 sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[180px]">

                <!-- Finished activities -->
                <div class="w-full">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-[24px]">Finished Activities</h2>
                    </div>

                    <div class="w-full">
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $formatted_date = date("F j, Y", strtotime($row['date']));
                                $formatted_time = date("h:i A", strtotime($row['time']));
                        ?>
                                <div class="flex gap-1 mb-4">
                                    <div class="w-[100px] flex flex-col text-subtext items-start">
                                        <p><?php echo $formatted_date; ?></p>
                                        <p><?php echo $formatted_time; ?></p>
                                    </div>

                                    <div class="w-1.5 h-auto bg-gray-500"></div>

                                    <a class="transition ease-linear hover:bg-gray-400 hover:bg-opacity-15 mx-8" href="../view_activity.php?activity_id=<?php echo $row['activity_id']; ?>&section_id=<?php echo $section_id; ?>&documentation_id=<?php echo $row['documentation_id']; ?>">
                                        <div class="flex-1">
                                            <h5><?php echo substr($row['activity_name'], 0, 25) . '...'; ?></h5>
                                            <p class="text-subtext"><?php echo substr($row['description'], 0, 20) . '...'; ?></p>
                                        </div>
                                    </a>

                                </div>
                        <?php
                            }
                        } else {
                            echo "<p>No finished activities for this section</p>";
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