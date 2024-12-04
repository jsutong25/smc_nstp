<?php

session_start();
include "../connect.php";
$message = "";

$user_id = $_SESSION['user_id'];
$section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;

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

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $sql = "SELECT * FROM user WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['message'] = "User not found.";
        header("Location: ./student_home.php?section_id=$section_id");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_number = $_POST['id_number'];
    $program = $_POST['program'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $extension = $_POST['extension'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $birthDate = new DateTime($birthday);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
    $sex = $_POST['sex'];
    $course = $_POST['course'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $contact_number = $_POST['contact_number'];

    $sql = "UPDATE user SET id_number = '$id_number', program = '$program', first_name = '$first_name', middle_name = '$middle_name', last_name = '$last_name', extension_name = '$extension', email = '$email', birthday = '$birthday', age = '$age', sex = '$sex', course = '$course', barangay = '$barangay', city = '$city', province = '$province', contact_number = '$contact_number' WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Profile updated successfully.";
        header("Location: ./profile.php?section_id=$section_id&user_id=$user_id");
        exit();
    } else {
        $_SESSION['message'] = "Failed to update profile.";
        header("Location: ./profile.php?section_id=$section_id&user_id=$user_id");
        exit();
    }
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

        <div class="mt-4 p-2 flex-grow sm:ml-[210px]">
            <a href="./student_home.php?section_id=<?php echo $section_id; ?>"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                    <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                </svg></a>
        </div>

        <div class="flex h-full w-full">
            <?php include '../sidebar_student.php'; ?>

            <div class="flex-grow p-4 sm:ml-64 md:ml-52 lg:ml-[210px] overflow-hidden max-w-full">
                <h1 class="text-[22px] mb-8">Profile</h1>

                <div class="w-full">
                    <form action="" class="flex flex-col" method="POST">

                        <label for="id_number">NSTP Serial number:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" disabled style="padding-left: 2em; padding-right: 2em;" name="id_number" placeholder="Your NSTP coordinator will fill this up for you" type="text" required value="<?php echo htmlspecialchars($user['serial_number']); ?>">

                        <label for="id_number">School ID Number:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="id_number" placeholder="Enter your school id number (Format: C00-0000)" type="text" required value="<?php echo htmlspecialchars($user['id_number']); ?>">

                        <label for="program">Program:<span class="text-primary ml-1">*</span></label>
                        <select class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="program" required>
                            <option value="<?php echo htmlspecialchars($user['program']); ?>"><?php echo htmlspecialchars($user['program']); ?></option>
                            <option value="CWTS">CWTS</option>
                            <option value="LTS">LTS</option>
                            <option value="ROTC">ROTC</option>
                        </select>

                        <label for="first_name">First Name:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="first_name" placeholder="Enter your first name" type="text" required value="<?php echo htmlspecialchars($user['first_name']); ?>">

                        <label for="middle_name">Middle Name:<span class="text-primary ml-1 mt-2">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="middle_name" placeholder="Enter your middle name" type="text" required value="<?php echo htmlspecialchars($user['middle_name']); ?>">

                        <label for="last_name">Last Name:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="last_name" placeholder="Enter your last name" type="text" required value="<?php echo htmlspecialchars($user['last_name']); ?>">

                        <label for="extension">Suffix (Ex: II, Jr.) [Not required]:</label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="extension" placeholder="Enter your suffix/extension name" type="text" value="<?php echo htmlspecialchars($user['extension_name']); ?>">

                        <label class="text-[16px]" for="email">Email<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="email" placeholder="Enter your email" type="email" required value="<?php echo htmlspecialchars($user['email']); ?>">

                        <label for="birthday">Birthday<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="birthday" type="date" required value="<?php echo htmlspecialchars($user['birthday']); ?>">

                        <label for="sex">Sex:<span class="text-primary ml-1">*</span></label>
                        <select class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="sex" required>
                            <option value="<?php echo htmlspecialchars($user['sex']); ?>"><?php echo htmlspecialchars($user['sex']); ?></option>
                            <option value="F">F</option>
                            <option value="M">M</option>
                        </select>

                        <label for="course">Course:<span class="text-primary ml-1">*</span></label>
                        <select class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="course" id="course" required>
                            <option value="<?php echo htmlspecialchars($user['course']); ?>"><?php echo htmlspecialchars($user['course']); ?></option>
                            <option value="BSIT">BSIT</option>
                        </select>

                        <label for="barangay">Barangay:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="barangay" placeholder="Enter your barangay" type="text" required value="<?php echo htmlspecialchars($user['barangay']); ?>">

                        <label for="city">City:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="city" placeholder="Enter your barangay" type="text" required value="<?php echo htmlspecialchars($user['city']); ?>">

                        <label for="province">Province:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="province" placeholder="Enter your barangay" type="text" required value="<?php echo htmlspecialchars($user['province']); ?>">

                        <label for="contact_number">Contact Number<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="contact_number" placeholder="Enter your contact number" type="text" required value="<?php echo htmlspecialchars($user['contact_number']); ?>">

                        <input class="bg-primary py-3 rounded-full mt-9 hover:cursor-pointer hover:bg-red-700" type="submit" value="Update Profile">
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