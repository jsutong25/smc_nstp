<?php

session_start();

include './connect.php'; // Include the database connection

$user_id = $_SESSION['user_id'];

$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $activity_name = $_POST['activity_name'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];

    $sql = "INSERT INTO activities (activity_name, description, date, time, location) VALUES ('$activity_name', '$description', '$date', '$time', '$location')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Activity added successfully.";
        header("Location: ./activities.php");
        exit();
    } else {
        $_SESSION['message'] = "Failed to create new activity";
        header("Location: ./new_activity.php");
        exit();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Activity</title>
    <link href="./output.css" rel="stylesheet">
</head>

<body class="bg-bg font-primary text-white my-8 mx-8 overflow-hidden">

    <div class="">
        <div class="flex flex-row items-center gap-2">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>

            <span class="text-lg">SMC NSTP</span>
        </div>

        <div class="mt-4 p-2 flex-grow sm:ml-64">
            <a href="./activities.php"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                    <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                </svg></a>
        </div>

        <div class="flex h-screen">
        <?php if ($_SESSION['user_type'] == 'nstp_coordinator' || $_SESSION['user_type'] == 'faculty'): ?>
                <?php include './sidebar_faculty-src.php'; ?>
            <?php else: ?>
                <?php include './sidebar_student-src.php'; ?>
            <?php endif; ?>

            <div class="mx-auto w-full sm:w-[500px] md:w-[600px] h-[65vh] content-center flex-grow p-4 sm:ml-64">
                <div class="mb-8">
                    <h1 class="text-center text-[40px]">Upload image</h1>
                </div>
                <div class="">
                    <form action="" class="flex flex-col" method="POST">
                        <label class="text-[16px]" for="activity_name">Activity name:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="activity_name" placeholder="Enter activity name" required type="text">

                        <label class="text-[16px] mt-5" for="description">Description:<span class="text-primary ml-1">*</span></label>
                        <textarea autocomplete="off" class="bg-bg border-2 border-white rounded-md py-3 mb-2 mt-2 min-h-32" style="padding-left: 2em; padding-right: 2em;" name="description" placeholder="Enter activity description" required type="password"></textarea>

                        <div class="flex justify-between mt-2 mb-2">
                            <div class="flex flex-col">
                                <label class="text-[16px]" for="date">Date:<span class="text-primary ml-1">*</span></label>
                                <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="date" required type="date">
                            </div>

                            <div class="flex flex-col">
                                <label class="text-[16px]" for="time">Time:<span class="text-primary ml-1">*</span></label>
                                <input autocomplete="off" class="appearance-none bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="time" required type="time">
                            </div>
                        </div>

                        <label class="text-[16px]" for="location">Location:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="appearance-none capitalize bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="location" placeholder="Enter location of activity" required type="text">

                        <input class="bg-primary py-3 rounded-full mt-9 hover:cursor-pointer hover:bg-red-700" type="submit" value="Add activity">
                    </form>
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

        // Function to toggle the sidebar
        const toggleSidebar = () => {
            sidebar.classList.toggle('-translate-x-full');
        };

        // Event listener for the hamburger button
        button.addEventListener('click', toggleSidebar);

        // Event listener for clicks outside the sidebar
        document.addEventListener('click', (event) => {
            // Check if the click is outside the sidebar and the button
            if (!sidebar.contains(event.target) && !button.contains(event.target)) {
                sidebar.classList.add('-translate-x-full'); // Close the sidebar
            }
        });
    </script>

    <script>
        window.onload = function() {
            <?php if (!empty($message)): ?>
                document.getElementById('messageModal').classList.remove('hidden'); // Show modal
            <?php endif; ?>
        };

        function closeModal() {
            document.getElementById('messageModal').classList.add('hidden'); // Hide modal
        }
    </script>

</body>

</html>