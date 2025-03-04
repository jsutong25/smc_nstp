<?php
session_start();
include '../connect.php'; 

$user_id = $_SESSION['user_id'];

$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;
$user_type = $_SESSION['user_type'];

if ($user_type == 'student') {
    header("Location: ../student/student_home.php?section_id=$section_id");
}

if (!isset($user_id)) {
    $_SESSION['message'] = "User ID is not set. Please log in.";
    header("Location: ../index.php");
    exit();
}

$course = [
    'name' => '',
    'insti_code' => ''
];

if ($course_id) {
    
    $sql = "SELECT * FROM course WHERE course_id = $course_id"; 
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $course = mysqli_fetch_assoc($result); 
    } else {
        $_SESSION['message'] = "Course not found.";
        header("Location: ./faculty_information.php?section_id=$section_id");
        exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST['course_name'];
    $insti_code = $_POST['insti_code'];

    $sql = "UPDATE course SET name = '$course_name', insti_code = '$insti_code' WHERE course_id = '$course_id'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Course updated successfully.";
        header("Location: ./faculty_information.php?section_id=$section_id"); 
        exit();
    } else {
        $_SESSION['message'] = "Failed to update course.";
        header("Location: ./edit_course.php?section_id=$section_id&course_id=$course_id"); 
        exit();
    }
}
$sql_course = "SELECT * FROM course WHERE course_id = $course_id";
$result_course = mysqli_query($conn, $sql_course);


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Section</title>
    <link href="../output.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../assets/favicon.ico" type="image/x-icon">
</head>

<body class="bg-bg font-primary text-white my-8 mx-8 overflow-hidden">

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

        <div class="mt-4 p-2 sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[230px]">
            <a href="./faculty_information.php?section_id=<?php echo $section_id; ?>"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                    <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                </svg></a>
        </div>

        <div class="flex h-screen w-full overflow-hidden">
            <?php include '../sidebar_faculty.php'; ?>

            <div class="mx-auto w-full sm:w-[500px] md:w-[600px] h-[65vh] content-center flex-grow p-4 sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[230px]">
                <div class="mb-8">
                    <h1 class="text-center text-[32px]">Edit Course</h1>
                </div>
                <div class="">
                    <form action="" class="flex flex-col" method="POST">
                        <label class="text-[16px]" for="course_name">Course Name:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="course_name" value="<?php echo htmlspecialchars($course['name']); ?>" required type="text">

                        <label class="text-[16px]" for="insti_code">Institutional Code:<span class="text-primary ml-1"></span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="insti_code" value="<?php echo htmlspecialchars($course['insti_code']); ?>" type="text">
                        <input class="bg-primary py-3 rounded-full mt-9 hover:cursor-pointer hover:bg-red-700" type="submit" value="Update Course">
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