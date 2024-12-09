<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto bg-black dark:bg-black">
        <a href="./admin_home.php" class="flex items-center ps-2.5 mb-5">
            <!-- <img src="../../assets/logo.png" class="h-6 me-3 sm:h-7" alt="Flowbite Logo" /> -->
            <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">SMC NSTP</span>
        </a>
        <ul class="space-y-2 font-medium">
            <li>
                <a href="./admin_home.php" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group <?php echo $current_page == 'admin_home.php' ? 'bg-active' : ''; ?>">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 22 21">
                        <path fill="currentColor" d="M18 18V7.132l-8-4.8l-8 4.8V18h4v-2.75a4 4 0 1 1 8 0V18h4zm-6 2v-4.75a2 2 0 1 0-4 0V20H2a2 2 0 0 1-2-2V7.132a2 2 0 0 1 .971-1.715l8-4.8a2 2 0 0 1 2.058 0l8 4.8A2 2 0 0 1 20 7.132V18a2 2 0 0 1-2 2h-6z" />
                    </svg>
                    <span class="ms-3">Home</span>
                </a>
            </li>
                <hr class="rounded-lg">
            </li>
            <li>
                <form method="POST">
                    <a href="../logout.php" class="w-full flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M77.155 272.034H351.75v-32.001H77.155l75.053-75.053v-.001l-22.628-22.626l-113.681 113.68l.001.001h-.001L129.58 369.715l22.628-22.627v-.001l-75.053-75.053z" />
                            <path fill="currentColor" d="M160 16v32h304v416H160v32h336V16H160z" />
                        </svg>
                        <span class="ms-3">Logout</span>
                    </a>
                </form>
            </li>
        </ul>
    </div>
</aside>