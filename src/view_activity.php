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
    header("Location: ../index.php");
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : null;
$activity_id = isset($_GET['activity_id']) ? intval($_GET['activity_id']) : null;

if ($activity_id === null || $section_id === null) {
    $_SESSION['message'] = "Missing activity or section ID in the URL.";
    header("Location: ./index.php");
    exit();
}

$activity_details = null;

if ($activity_id) {
    $activity_sql = "SELECT * FROM activities WHERE activity_id = ? AND section_id = ?";
    $stmt = $conn->prepare($activity_sql);
    if ($stmt) {    
        $stmt->bind_param("ii", $activity_id, $section_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $activity_details = $result->fetch_assoc();
            } else {
                echo "No activity found with the given ID.";
                exit();
            }
        } else {
            echo "Query execution failed: " . $stmt->error;
            exit();
        }
    } else {
        echo "Query preparation failed: " . $conn->error;
        exit();
    }
}

$album = null;
$result_images = null;

if (isset($_GET['documentation_id'])) {
    $album_id = intval($_GET['documentation_id']);

    $sql = "SELECT * FROM documentation WHERE documentation_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $album_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $album = $result->fetch_assoc();
        } else {
            echo "Album not found.";
            exit();
        }

        $sql_images = "SELECT * FROM images WHERE documentation_id = ?";
        $stmt_images = $conn->prepare($sql_images);
        $stmt_images->bind_param("i", $album_id);
        $stmt_images->execute();
        $result_images = $stmt_images->get_result();

        $current_year = date('Y');

        $album_name = $album['documentation_name'];
        $section_id = $activity_details['section_id'];
        $section_name = '';

        if ($section_id) {
            $sql_section = "SELECT section_name FROM section WHERE section_id = ?";
            $stmt_section = $conn->prepare($sql_section);
            $stmt_section->bind_param("i", $section_id);
            $stmt_section->execute();
            $result_section = $stmt_section->get_result();
            if ($result_section && $result_section->num_rows > 0) {
                $row_section = $result_section->fetch_assoc();
                $section_name = $row_section['section_name'];
            }
        }

        $upload_dir = "./uploads/" . $current_year . "/" . $album_name . "-" . $section_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
            $image = $_FILES['image'];
            $target_file = $upload_dir . '/' . basename($image['name']);

            if (move_uploaded_file($image['tmp_name'], $target_file)) {
                $uploaded_by = $_SESSION['user_id'];
                $sql_insert = "INSERT INTO images (documentation_id, image, uploaded_by) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("isi", $album_id, $target_file, $uploaded_by);
                if ($stmt_insert->execute()) {
                    $_SESSION['message'] = "Image uploaded successfully.";
                    header("Location: ./view_activity.php?activity_id=$activity_id&documentation_id=$album_id&section_id=$section_id");
                    exit();
                } else {
                    $_SESSION['message'] = "Failed to upload image.";
                }
            } else {
                $_SESSION['message'] = "Failed to move uploaded file.";
            }
        }
    } else {
        echo "Query preparation failed: " . $conn->error;
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid album.";
    header("Location: ./view_activity.php?section_id=$section_id");
    exit();
}

$user_type = $_SESSION['user_type'];
$_SESSION['last_activity'] = time();

?>



<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./output.css" rel="stylesheet">
    <title>Activity</title>

    <style>
        .lightbox {
            display: none;
        }
    </style>
</head>

<body class="bg-bg font-primary text-white my-8 mx-8 h-[100vh] overflow-y-hidden overflow-x-auto">


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

        <div class="mt-4 p-2 flex-grow sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[180px]">
            <a href="./activities.php?section_id=<?php echo $section_id; ?>"><svg class="transition ease-in-out hover:text-primary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 42 42">
                    <path fill="currentColor" fill-rule="evenodd" d="M27.066 1L7 21.068l19.568 19.569l4.934-4.933l-14.637-14.636L32 5.933z" />
                </svg></a>
        </div>

        <div class="flex h-full w-full">
        <?php if ($_SESSION['user_type'] == 'nstp_coordinator' || $_SESSION['user_type'] == 'faculty'): ?>
                <?php include './sidebar_faculty-src.php'; ?>
            <?php else: ?>
                <?php include './sidebar_student-src.php'; ?>
            <?php endif; ?>

            <div class="flex-grow p-4 sm:ml-[230px] md:ml-[240px] lg:ml-[240px] xl:ml-[230px] xxl:ml-[180px]">

                <!-- Activity -->
                <div class="h-full">
                    <div class="flex flex-col">
                        <h2 class="text-[32px]"><?php echo htmlspecialchars($activity_details['activity_name']); ?></h2>
                        <div class="">
                            <span class="text-primary"><?php echo htmlspecialchars($activity_details['date']); ?></span>
                            <span class="text-primary"><?php echo htmlspecialchars($activity_details['time']); ?></span>
                            <span class="text-primary"> | </span>
                            <span class="text-primary"><?php echo htmlspecialchars($activity_details['location']); ?></span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-lg"><?php echo htmlspecialchars($activity_details['description']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- Documentation -->
        <div class="flex h-screen">
            <div class="flex-grow p-4 sm:ml-[210px]">
                <div>
                    <div>
                        <h3 class="text-lg mb-2">Documentation</h3>
                    </div>

                    <div class="mx-auto w-full flex mb-8 gap-2">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="file" name="image" id="image">
                            <input type="submit" name="upload" value="Upload image">
                        </form>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <?php if ($result_images && mysqli_num_rows($result_images) > 0): ?>
                            <?php while ($image_row = mysqli_fetch_assoc($result_images)): ?>
                                <div class="relative cursor-pointer" onclick="openLightbox('<?php echo $image_row['image']; ?>', <?php echo $image_row['image_id']; ?>)">
                                    <img src="<?php echo $image_row['image']; ?>" alt="Uploaded Image" class="w-full h-64 object-cover rounded-lg shadow-md" />
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-500">No images uploaded in this album.</p>
                        <?php endif; ?>
                    </div>

                    <div id="lightbox" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex justify-center items-center lightbox" onclick="closeLightbox()">
                        <span class="absolute left-4 text-white outline-2 outline-black text-[50px] cursor-pointer z-10" onclick="changeImage(-1, event)">&#10094;</span>
                        <img id="lightboxImage" src="" alt="Lightbox Image" class="max-w-[90%] max-h-[90%] rounded-lg z-1" />
                        <span class="absolute right-4 text-white outline-2 outline-black text-[50px] cursor-pointer z-10" onclick="changeImage(1, event)">&#10095;</span>
                    </div>

                </div>
            </div>

            <!-- <div>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $album_name = urlencode($row['documentation_name']);
                        ?>
                                <div class="w-[100px]">
                                    <a href="../view_album.php?album=<?php echo $album_name; ?>">
                                        <div class="bg-white w-[100px] h-[100px]">
                                            <div class="flex h-full justify-center items-center mx-2 z-10">
                                                <span class="text-subtext text-[12px]">View album</span>
                                            </div>
                                            <a href="#"><img src="" alt=""></a>
                                        </div>
                                        <div class="">
                                            <p><?php echo $row['documentation_name']; ?></p>
                                        </div>
                                    </a>
                                </div>
                        <?php
                            }
                        } else {
                            echo "No documentation available.</p>";
                        }
                        ?>
                        
                    </div> -->
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

<script>
        let currentImageIndex = -1;
        let images = [];

        function openLightbox(imageSrc) {
            const lightbox = document.getElementById('lightbox');
            const lightboxImage = document.getElementById('lightboxImage');

            lightbox.style.display = 'flex'; 
            lightboxImage.src = imageSrc; 

            images = [];
            const imageWrappers = document.querySelectorAll('.grid div img');
            imageWrappers.forEach((img, index) => {
                images.push(img.src);
                if (img.src === imageSrc) {
                    currentImageIndex = index; 
                }
            });

            document.addEventListener('keydown', handleKeyPress);
        }

        function closeLightbox(event) {
            const lightbox = document.getElementById('lightbox');
            lightbox.style.display = 'none';
            document.removeEventListener('keydown', handleKeyPress);
            if (event) {
                event.stopPropagation();
            }
        }

        function changeImage(direction, event) {
            event.stopPropagation();
            currentImageIndex += direction;
            if (currentImageIndex < 0) {
                currentImageIndex = images.length - 1;
            } else if (currentImageIndex >= images.length) {
                currentImageIndex = 0;
            }
            document.getElementById('lightboxImage').src = images[currentImageIndex];
        }

        function handleKeyPress(event) {
            if (event.key === "ArrowLeft") {
                changeImage(-1, event); 
            } else if (event.key === "ArrowRight") {
                changeImage(1, event); 
            } else if (event.key === "Escape") {
                closeLightbox(event); 
            }
        }
    </script>
</body>

</html>