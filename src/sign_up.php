<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "./connect.php";

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$error_message = '';

if (isset($_GET['section_id'])) {
  $section_id = $_GET['section_id'];

  $sql_section = "SELECT * FROM section WHERE section_id = ?";
  $stmt = $conn->prepare($sql_section);
  $stmt->bind_param("i", $section_id);
  $stmt->execute();
  $result_section = $stmt->get_result();

  if ($result_section->num_rows > 0) {
    $section = $result_section->fetch_assoc();
    $section_name = $section['section_name'];
  } else {
    $_SESSION['error'] = 'Section not found. Please try again.';
    header("Location: ./code.php");
    exit;
  }
} else {
  $_SESSION['error'] = 'No section ID provided. Please enter a valid code.';
  header("Location: ./code.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // var_dump($_POST);
  
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
  $section_id = $_POST['section_id'];
  $password = $_POST['password'];
  $user_type = 'student';

  if (!preg_match("/@my\.smciligan\.edu\.ph$/", $email)) {
    $error_message = "Please use an email address ending with @my.smciligan.edu.ph";
    header("Location: ./sign_up.php?section_id=$section_id");
    exit;
  }

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO user (id_number, program, first_name, middle_name, last_name, extension_name, email, birthday, age, sex, course, barangay, city, province, contact_number, section, password, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssssisssssssss", $id_number, $program, $first_name, $middle_name, $last_name, $extension,  $email, $birthday, $age, $sex, $course, $barangay, $city, $province, $contact_number, $section_id, $hashed_password, $user_type);

  if ($stmt->execute()) {
    echo "Sign up successful";
    header("Location: ./index.php");
    exit();
  } else {
    $error_message = "Error: " . $stmt->error;
  }

  $stmt->close();
}

$sql_section = "SELECT * FROM section";
$result_section = mysqli_query($conn, $sql_section);

$conn->close();


?>

<!doctype html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up</title>
  <link href="./output.css" rel="stylesheet">
  <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
</head>

<body class="bg-bg font-primary text-white my-8 mx-8">
  <nav class="flex flex-row">
    <img src="" alt="">
    <h2 class="text-[20px]">SMC NSTP</h2>
  </nav>

  <section>
    <div class="bg-bg mx-auto w-full sm:w-[500px] md:w-[600px]">
    <?php if (!empty($error_message)): ?>
        <div class="bg-red-500 text-white p-4 rounded mb-5">
          <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
      <?php endif; ?>
      <div class="mb-5">
        <h1 class="text-center text-[40px]">Sign up</h1>
      </div>
      <div class="">
        <form action="" class="flex flex-col" method="POST">

          <label for="id_number">School ID Number:<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="id_number" placeholder="Enter your school id number (Format: C00-0000)" type="text" required>

          <label for="program">Program:<span class="text-primary ml-1">*</span></label>
          <select class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="program" required>
            <option value="">--- Select ---</option>
            <option value="CWTS">CWTS</option>
            <option value="LTS">LTS</option>
            <option value="ROTC">ROTC</option>
          </select>

          <label for="first_name">First Name:<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="first_name" placeholder="Enter your first name" type="text" required>

          <label for="middle_name">Middle Name:<span class="text-primary ml-1 mt-2">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="middle_name" placeholder="Enter your middle name" type="text" required>

          <label for="last_name">Last Name:<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="last_name" placeholder="Enter your last name" type="text" required>

          <label for="extension">Suffix (Leave blank if none):</label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="extension" placeholder="Enter your extension name" type="text">

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

          <label for="birthday">Birthday<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="birthday" type="date" required>

          <label for="sex">Sex:<span class="text-primary ml-1">*</span></label>
          <select class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="sex" required>
            <option value="">--- Select ---</option>
            <option value="F">F</option>
            <option value="M">M</option>
          </select>

          <label for="course">Course:<span class="text-primary ml-1">*</span></label>
          <select id="course" name="course" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" required>
              <option value="" disabled selected>Select course</option>
              <?php
              include "./connect.php";
              
              $sql_course = "SELECT * FROM course";
              $result_course = $conn->query($sql_course);

              $courses = []; // Array to store course details for JavaScript
              if ($result_course->num_rows > 0) {
                  while ($row = $result_course->fetch_assoc()) {
                      echo "<option value='" . $row['course_id'] . "' data-code='" . $row['insti_code'] . "'>" . $row['name'] . "</option>";
                      $courses[] = $row; // Store course data for JavaScript
                  }
              }
              ?>
          </select>
            
          <!-- INSTITUIONAL CODE -->
          <!-- <label for="institutional_code">Institutional Code:<span class="text-primary ml-1">*</span></label>
          <input id="institutional_code" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" type="text" disabled> -->


          <label for="barangay">Barangay:<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="barangay" placeholder="Enter your barangay" type="text" required>

          <label for="city">City:<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="city" placeholder="Enter your barangay" type="text" required>

          <label for="province">Province:<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="province" placeholder="Enter your barangay" type="text" required>

          <label for="contact_number">Contact Number<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="contact_number" placeholder="Enter your contact number" type="text" required>

          <label for="section">Section:<span class="text-primary ml-1">*</span></label>
          <input type="text" id="section" name="section" style="padding-left: 2em; padding-right: 2em;" value="<?php echo htmlspecialchars($section_name); ?>" disabled class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" />
          <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($section_id); ?>" />

          <label class="text-[16px]" for="password">Password<span class="text-primary ml-1">*</span></label>
          <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2" style="padding-left: 2em; padding-right: 2em;" name="password" placeholder="Enter your password" type="password" required>

          <div class="flex mt-5">
            <input class="rounded-md w-4 h-4" type="checkbox" name="terms" id="terms" required>
            <p>I agree to the <a class="text-primary" href="/">Terms and Privacy Policy</a></p>
          </div>

          <input class="bg-primary py-3 rounded-full mt-5 hover:cursor-pointer hover:bg-red-700" type="submit" value="Sign Up">
        </form>
        <p class="mt-4">Already have an account? <a class="text-primary" href="./index.php">Sign in</a></p>
      </div>
    </div>
  </section>


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

  <!-- <script>
  document.getElementById('course').addEventListener('change', function() {
      let selectedOption = this.options[this.selectedIndex];
      let instiCode = selectedOption.getAttribute('data-code'); // Get institutional code from data attribute
      document.getElementById('institutional_code').value = instiCode; // Set institutional code
  });
  </script> -->

</body>

</html>