<?php
session_start();

include '../connect.php'; 

$user_type = $_SESSION['user_type'];

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_number = $_POST['id_number'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $extension_name = $_POST['suffix'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql_insert = "INSERT INTO user (id_number, first_name, middle_name, last_name, extension_name, email, user_type, password) VALUES ('$id_number', '$first_name', '$middle_name', '$last_name', '$extension_name', '$email', '$role', '$hashed_password')";

    if (mysqli_query($conn, $sql_insert)) {
        $_SESSION['message'] = "New user created successfully.";
        header("Location: ./admin_home.php");
        exit();
    } else {
        $_SESSION['message'] = "Failed to create user: " . mysqli_error($conn);
        header("Location: ./new_faculty.php");
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
    <title>New Section</title>
    <link href="../output.css" rel="stylesheet">
</head>

<body class="bg-bg font-primary text-white mx-8 overflow-auto">

    <div class="container mx-auto">
        <div class="flex flex-row items-center gap-2">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>
            <a href="./admin_home.php"><span class="text-lg">SMC NSTP</span></a>
        </div>

        <div class="mt-4 p-2 sm:ml-[210px]">
            <a href="./admin_home.php"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                    <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                </svg></a>
        </div>

        <div class="flex h-screen">
            <?php include '../sidebar_faculty.php'; ?>

            <div class="mx-auto w-full sm:w-[500px] md:w-[600px] h-[65vh] content-center flex-grow p-4 sm:ml-[210px]">
                <div class="mb-8">
                    <h1 class="text-center text-[40px]">Add new faculty</h1>
                </div>
                <div class="pb-12">
                    <form action="" class="flex flex-col" method="POST">
                        <label class="text-[16px]" for="id_number">ID Number:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="id_number" required placeholder="Enter id number" type="text">
                        
                        <label class="text-[16px]" for="first_name">First Name:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="first_name" required placeholder="Enter first name" type="text">

                        <label class="text-[16px]" for="middle_name">Middle Name:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="middle_name" required placeholder="Enter middle name" type="text">

                        <label class="text-[16px]" for="last_name">Last Name:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="last_name" required placeholder="Enter last name" type="text">

                        <label class="text-[16px]" for="suffix">Suffix: (Leave blank if none)</label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="suffix" placeholder="Enter suffix name" type="text">

                        <label class="text-[16px]" for="email">Email<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off"
                            class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2"
                            style="padding-left: 2em; padding-right: 2em;"
                            name="email"
                            id="email"
                            placeholder="Enter your email"
                            type="email"
                            pattern="[a-zA-Z0-9._%+-]+@my\.smciligan\.edu\.ph"
                            title="Email must be from @my.smciligan.edu.ph domain"
                            required
                            oninput="validateEmail()">

                        <label for="role">Role:<span class="text-primary ml-1">*</span></label>
                        <select class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="role" required>
                            <option value="">--- Select ---</option>
                            <option value="faculty">Faculty</option>
                            <option value="nstp_coordinator">NSTP Coordinator</option>
                            <option value="admin">Admin</option>
                        </select>

                        <label class="text-[16px]" for="password">Temporary Password:<span class="text-primary ml-1">*</span></label>
                        <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="password" required placeholder="Enter temporary password" type="password">

                        <input class="bg-primary py-3 rounded-full mt-9 hover:cursor-pointer hover:bg-red-700" type="submit" value="Add">
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
        function validateEmail() {
        const emailInput = document.getElementById('email');
        const emailValue = emailInput.value;

        const requiredDomain = "@my.smciligan.edu.ph";
        if (emailValue && !emailValue.endsWith(requiredDomain)) {
            emailInput.setCustomValidity("Please use an email address ending with " + requiredDomain);
            emailInput.reportValidity();
        } else {
            emailInput.setCustomValidity("");
        }
        }
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