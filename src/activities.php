<?php

session_start();
include "./connect.php";
$message = "";

$user_id = $_SESSION['user_id'];
$timeout_duration = 3600;

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    $_SESSION['message'] = "You are not logged in.";
    header("Location: ./index.php");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ./index.php");
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

if ($section_id) {
    // Adjust the SQL to include the documentation_id
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

// Handle activity deletion as before
if (isset($_POST['delete_activity'])) {
    $activity_id = intval($_POST['activity_id']);

    $sql_delete_activity = "DELETE FROM activities WHERE activity_id = $activity_id";

    if (mysqli_query($conn, $sql_delete_activity)) {
        $_SESSION['message'] = "Activity deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete the activity.";
    }

    header("Location: ./activities.php?section_id=$section_id");
    exit();
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
    <link href="./output.css" rel="stylesheet">
    <title>Faculty</title>
</head>

<body class="bg-bg font-primary text-white my-8 mx-8 h-[100vh] overflow-x-hidden overflow-y-auto box-border">
    <div class="container mx-auto w-full">
        <div class="flex flex-wrap flex-row items-center gap-2 w-full md:hidden">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>

            <a href="./faculty/faculty_home.php?section_id=<?php echo $section_id; ?>"><span class="text-lg">SMC NSTP</span></a>
        </div>

        <div class="mt-4 p-2 flex-grow sm:ml-[210px]">
            <?php if ($_SESSION['user_type'] == 'nstp_coordinator' || $_SESSION['user_type'] == 'faculty'): ?>
                <a href="./faculty/faculty_home.php?section_id=<?php echo $section_id; ?>"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                        <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                    </svg></a>
            <?php else: ?>
                <a href="./student/student_home.php?section_id=<?php echo $section_id; ?>"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                        <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                    </svg></a>
            <?php endif; ?>
        </div>

        <div class="flex h-screen w-full">
            <?php if ($_SESSION['user_type'] == 'nstp_coordinator' || $_SESSION['user_type'] == 'faculty'): ?>
                <?php include './sidebar_faculty-src.php'; ?>
            <?php else: ?>
                <?php include './sidebar_student-src.php'; ?>
            <?php endif; ?>

            <div class="flex-grow p-4 sm:ml-[210px]">

                <!-- Upcoming activities -->
                <div class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-[24px]">Upcoming Activities</h2>
                        <?php if ($_SESSION['user_type'] == 'nstp_coordinator' || $_SESSION['user_type'] == 'faculty'): ?>
                            <a class="text-subtext hover:text-primary underline" href="./history_activities.php">History</a>
                        <?php endif; ?>
                    </div>

                    <?php if ($_SESSION['user_type'] == 'nstp_coordinator' || $_SESSION['user_type'] == 'faculty'): ?>
                        <div class="mx-auto w-full flex mb-8 gap-1">
                            <a class="bg-primary py-3 text-center w-full rounded-full mt-8 hover:cursor-pointer hover:bg-red-700 flex items-center justify-center" href="./faculty/new_activity.php?section_id=<?php echo $section_id; ?>">
                                <svg class="transition ease-linear duration-200 hover:text-primary mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                    <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                        <path d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12Zm10-8a8 8 0 1 0 0 16a8 8 0 0 0 0-16Z" />
                                        <path d="M13 7a1 1 0 1 0-2 0v4H7a1 1 0 1 0 0 2h4v4a1 1 0 1 0 2 0v-4h4a1 1 0 1 0 0-2h-4V7Z" />
                                    </g>
                                </svg>
                                Add new activity
                            </a>
                        </div>

                    <?php endif; ?>

                    <div class="w-full mt-8">
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

                                    // Limit characters for mobile view
                                    $short_title = substr($activity_name, 0, 10) . (strlen($activity_name) > 10 ? '...' : '');
                                    $short_description = substr($description, 0, 10) . (strlen($description) > 10 ? '...' : '');
                        ?>
                                    <div class="flex flex-col gap-1 mb-4">
                                        <div class="flex flex-col sm:flex-row justify-between items-start w-full">
                                            <div class="flex gap-4 w-full sm:w-auto">
                                                <div class="w-[100px] flex flex-col text-subtext items-start">
                                                    <p><?php echo $formatted_date; ?></p>
                                                    <p><?php echo $formatted_time; ?></p>
                                                </div>

                                                <div class="hidden sm:block w-1.5 h-auto bg-gray-500"></div>

                                                <a class="flex-1 transition ease-linear hover:bg-gray-400 hover:bg-opacity-15 mx-2 sm:mx-8 w-full"
                                                    href="./view_activity.php?activity_id=<?php echo $row['activity_id']; ?>&section_id=<?php echo $section_id; ?>&documentation_id=<?php echo $row['documentation_id']; ?>">
                                                    <div>
                                                        <h5 class="block md:hidden"><?php echo $short_title; ?></h5>
                                                        <h5 class="hidden md:block"><?php echo $activity_name; ?></h5>

                                                        <p class="block md:hidden text-subtext"><?php echo $short_description; ?></p>
                                                        <p class="hidden md:block text-subtext">
                                                            <?php echo (strlen($description) > 100) ? substr($description, 0, 100) . '...' : $description; ?>
                                                        </p>
                                                    </div>
                                                </a>
                                            </div>

                                            <?php if ($_SESSION['user_type'] == 'nstp_coordinator' || $_SESSION['user_type'] == 'faculty'): ?>
                                                <div class="flex gap-2 md:gap-4 mt-4 sm:mt-0">
                                                    <a class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500 transition inline-block text-center"
                                                        href="./faculty/edit_activity.php?activity_id=<?php echo $row['activity_id']; ?>&section_id=<?php echo $section_id; ?>"
                                                        style='min-width: 80px; display: inline-block;'>Edit</a>

                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="activity_id" value="<?php echo $row['activity_id']; ?>">
                                                        <input type="hidden" name="delete_activity" value="1">
                                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition"
                                                            style='min-width: 80px; display: inline-block; text-align: center;'
                                                            onclick="return confirm('Are you sure you want to delete this activity?');">Delete</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                        <?php
                                    $counter++;
                                }
                            } else {
                                echo "<p class='italic text-center text-[16px] mt-4'>No upcoming activities</p>";
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