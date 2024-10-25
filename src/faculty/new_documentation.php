<?php

session_start();
include '../connect.php'; 

$user_type = $_SESSION['user_type'];
$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;

if ($user_type == 'student') {
    header("Location: ../student/student_home.php?section_id=$section_id");
}

$user_id = $_SESSION['user_id'];

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Check if user_id is set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "User ID is not set. Please log in.";
    header("Location: ../index.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documentation_name = $_POST['documentation_name'];
    $user_id = $_SESSION['user_id'];
    // $section = $_POST['section'];

    // Sanitize input to prevent SQL injection
    $documentation_name = mysqli_real_escape_string($conn, $documentation_name);
    $user_id = (int)$user_id; // Cast to integer

    $sql = "INSERT INTO documentation (documentation_name, created_by, section_id) VALUES ('$documentation_name', '$user_id', '$section_id')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Created documentation album.";
        header("Location: ../documentation.php?section_id=$section_id");
        exit();
    } else {
        $_SESSION['message'] = "Failed to create new documentation album: " . mysqli_error($conn);
        header("Location: ./new_documentation.php?section_id=$section_id");
        exit();
    }
}

$sql = "SELECT * FROM section WHERE faculty_id = $user_id";
$result_section = mysqli_query($conn, $sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Activity</title>
    <link href="../output.css" rel="stylesheet">
</head>

<body class="bg-bg font-primary text-white my-8 mx-8 overflow-hidden">

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

        <div class="mt-4 p-2 sm:ml-64">
            <a href="../documentation.php?section_id=<?php echo $section_id; ?>"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                    <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                </svg></a>
        </div>

        <div class="flex h-screen w-full overflow-hidden">
        <?php include '../sidebar_faculty.php'; ?>

            <div class="mx-auto w-full sm:w-[500px] md:w-[600px] h-[65vh] content-center flex-grow p-4 sm:ml-64">
                <div class="mb-8">
                    <h1 class="text-center text-[32px]">Create new album</h1>
                </div>
                <div class="">
                    <form action="" class="flex flex-col" method="POST">
                        <label class="text-[16px]" for="documentation_name">Album name:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="documentation_name" placeholder="Enter album name" required type="text">

                        <!-- <label for="section">Section<span class="text-primary ml-1">*</span></label>
                        <select class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="section" required>
                            <?php if ($result_section && mysqli_num_rows($result_section) > 0): ?>
                                <option value="">--- Select ---</option>
                                <?php while ($section = mysqli_fetch_assoc($result_section)): ?>
                                    <option value="<?php echo $section['section_id']; ?>"><?php echo $section['section_name']; ?></option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="">No sections available</option>
                            <?php endif; ?>
                        </select> -->

                        <input class="bg-primary py-3 rounded-full mt-9 hover:cursor-pointer hover:bg-red-700" type="submit" value="Create new album">
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