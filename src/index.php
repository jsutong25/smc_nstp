<?php

session_start();
include "./connect.php";
$message = "";

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, user_type, password, section FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows>0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            $user_type = $row['user_type'];
            $user_id = $row['user_id'];
            $user_type = $row['user_type'];
            $section_id = $row['section'];

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = $user_type;
            $_SESSION['loggedin'] = true;
            $_SESSION['last_activity'] = time();
            $_SESSION['section_id'] = $section_id;

            if($user_type == 'student') {
                header("Location: ./student/student_home.php?section_id=$section_id");
            } elseif ($user_type == 'faculty') {
                header("Location: ./faculty/faculty_home.php");
            } elseif ($user_type == 'registrar') {
                header("Location: ./registrar/registrar_home.php");
            } else if ($user_type == 'nstp_coordinator') {
                header("Location: ./faculty/faculty_home.php");
            } else if ($user_type == 'admin') {
                header("Location: ./admin/admin_home.php");
            } else {
                echo "Invalid user type.";
            }
            exit();
        } else {
            $_SESSION['message'] = "Incorrect password.";
            header("Location: ./index.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "No user found with this email.";
        header("Location: ./index.php");
        exit();
    }
    $stmt->close();

}

$conn->close();

?>

<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./output.css" rel="stylesheet">
  <title>Sign in</title>
  <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
</head>
<body class="bg-bg font-primary text-white my-8 mx-8 h-[100vh] overflow-hidden">
  <nav class="flex flex-row">
    <img src="" alt="">
    <h2 class="text-[20px]">SMC NSTP</h2>
  </nav>

  <div id="messageModal" class="fixed top-0 inset-0 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-5 relative">
      <p class="mt-2 text-center text-gray-600"><?php echo $message; ?></p>
      <div class="mt-4 flex justify-center">
        <button onclick="closeModal()" class="bg-primary text-white font-semibold py-2 px-4 rounded-full">Close</button>
      </div>
    </div>
  </div>

  <section>
    <div class="mx-auto w-full sm:w-[500px] md:w-[600px] h-[65vh] content-center">
      <div class="mb-8">
        <h1 class="text-center text-[40px]">Sign in</h1>
      </div>
      <div class="">
        <form action="" class="flex flex-col" method="POST">
            <label class="text-[16px]" for="email">Email:<span class="text-primary ml-1">*</span></label>
            <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="email" placeholder="Enter your email" required type="email">

            <label class="text-[16px] mt-5" for="password">Password:<span class="text-primary ml-1">*</span></label>
            <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mb-2 mt-2" style="padding-left: 2em; padding-right: 2em;" name="password" placeholder="Enter your password" required type="password">

            <input class="bg-primary py-3 rounded-full mt-9 hover:cursor-pointer hover:bg-red-700" type="submit" value="Sign In">
        </form>
        <p class="mt-4 text-end">Don't have an account? <a class="text-primary hover:underline" href="./code.php">Sign Up</a></p>
      </div>
    </div>
  </section>

  

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