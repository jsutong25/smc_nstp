<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'smc_nstpms');

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = mysqli_real_escape_string($conn, $_POST['code']);
    
    $sql = "SELECT section_id FROM section WHERE code = '$entered_code'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $section = mysqli_fetch_assoc($result);
        $section_id = $section['section_id'];
        
        header("Location: ./sign_up.php?section_id=$section_id");
        exit;
    } else {
        $_SESSION['error'] = 'Invalid code. Please try again.';
        
        header("Location: ./code.php");
        exit;
    }
}

$conn->close();
?>

<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up</title>
  <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-bg font-primary text-white my-8 mx-8">
  <nav class="flex flex-row">
    <img src="" alt="">
    <h2 class="text-[20px]">SMC NSTP</h2>
  </nav>

  <section>
    <div class="bg-bg mx-auto w-full sm:w-[500px] md:w-[600px]">
      <div class="mb-5 mt-10">
        <h1 class="text-center text-[40px]">Enter code: </h1>
      </div>
      <div class="">
        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='text-red-500 text-center mb-4'>{$_SESSION['error']}</p>";
            unset($_SESSION['error']);
        }
        ?>
        
        <form action="code.php" class="flex flex-col" method="POST">
            <label for="code">Code:<span class="text-primary ml-1">*</span></label>
            <input autocomplete="off" class="bg-bg border-2 border-white rounded-full py-3 mt-2 mb-2" style="padding-left: 2em; padding-right: 2em;" name="code" placeholder="Enter code given" type="text" required>
            <button type="submit" class="bg-primary text-white py-2 mt-4">Submit</button>
        </form>
      </div>
    </div>
  </section>
</body>
</html>
