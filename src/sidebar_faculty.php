<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto bg-black dark:bg-black">
        <a href="./faculty_home.php?section_id=<?php echo $section_id; ?>" class="flex items-center ps-2.5 mb-5">
            <!-- <img src="../../assets/logo.png" class="h-6 me-3 sm:h-7" alt="Flowbite Logo" /> -->
            <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">SMC NSTP</span>
        </a>
        <ul class="space-y-2 font-medium">
            <li>
                <a href="./faculty_home.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group <?php echo $current_page == 'faculty_home.php' ? 'bg-active text-black' : ''; ?>">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 22 21">
                        <path fill="currentColor" d="M18 18V7.132l-8-4.8l-8 4.8V18h4v-2.75a4 4 0 1 1 8 0V18h4zm-6 2v-4.75a2 2 0 1 0-4 0V20H2a2 2 0 0 1-2-2V7.132a2 2 0 0 1 .971-1.715l8-4.8a2 2 0 0 1 2.058 0l8 4.8A2 2 0 0 1 20 7.132V18a2 2 0 0 1-2 2h-6z" />
                    </svg>
                    <span class="ms-3">Home</span>
                </a>
            </li>
            <li>
                <a href="./student_information.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group <?php echo $current_page == 'student_information.php' ? 'bg-active text-black' : ''; ?>">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 256 256">
                        <path fill="currentColor" d="m227.79 52.62l-96-32a11.85 11.85 0 0 0-7.58 0l-96 32A12 12 0 0 0 20 63.37a6.05 6.05 0 0 0 0 .63v80a12 12 0 0 0 24 0V80.65l23.71 7.9a67.92 67.92 0 0 0 18.42 85A100.36 100.36 0 0 0 46 209.44a12 12 0 1 0 20.1 13.11C80.37 200.59 103 188 128 188s47.63 12.59 61.95 34.55a12 12 0 1 0 20.1-13.11a100.36 100.36 0 0 0-40.18-35.92a67.92 67.92 0 0 0 18.42-85l39.5-13.17a12 12 0 0 0 0-22.76Zm-99.79-8L186.05 64L128 83.35L70 64ZM172 120a44 44 0 1 1-81.06-23.71l33.27 11.09a11.89 11.89 0 0 0 7.58 0l33.27-11.09A43.85 43.85 0 0 1 172 120Z" />
                    </svg>
                    <span class="ms-3">Students</span>
                </a>
            </li>
            <?php if ($_SESSION['user_type'] == 'nstp_coordinator'): ?>
                <li>
                    <a href="./faculty_information.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group <?php echo $current_page == 'faculty_information.php' ? 'bg-active text-black' : ''; ?>">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 512 512">
                            <path fill="currentColor" d="m462.541 316.3l-64.344-42.1l24.774-45.418A79.124 79.124 0 0 0 432.093 192v-72a103.941 103.941 0 0 0-174.609-76.477L279.232 67a71.989 71.989 0 0 1 120.861 53v72a46.809 46.809 0 0 1-5.215 21.452L355.962 284.8l89.058 58.274a42.16 42.16 0 0 1 19.073 35.421V432h-72v32h104v-85.506a74.061 74.061 0 0 0-33.552-62.194Z" />
                            <path fill="currentColor" d="m318.541 348.3l-64.343-42.1l24.773-45.418A79.124 79.124 0 0 0 288.093 224v-72A104.212 104.212 0 0 0 184.04 47.866C126.723 47.866 80.093 94.581 80.093 152v72a78 78 0 0 0 9.015 36.775l24.908 45.664L50.047 348.3A74.022 74.022 0 0 0 16.5 410.4L16 496h336.093v-85.506a74.061 74.061 0 0 0-33.552-62.194Zm1.552 115.7H48.186l.31-53.506a42.158 42.158 0 0 1 19.073-35.421l88.682-58.029l-39.051-71.592A46.838 46.838 0 0 1 112.093 224v-72a72 72 0 1 1 144 0v72a46.809 46.809 0 0 1-5.215 21.452L211.962 316.8l89.058 58.274a42.16 42.16 0 0 1 19.073 35.421Z" />
                        </svg>
                        <span class="ms-3">Faculty</span>
                    </a>
                </li>
                <li>
                    <a href="./sections.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group <?php echo $current_page == 'sections.php' ? 'bg-active text-black' : ''; ?>">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 512 512"><path fill="currentColor" d="M472 328h-24v-64a24.027 24.027 0 0 0-24-24H272v-64h32a24.028 24.028 0 0 0 24-24V80a24.028 24.028 0 0 0-24-24h-96a24.028 24.028 0 0 0-24 24v72a24.028 24.028 0 0 0 24 24h32v64H88a24.027 24.027 0 0 0-24 24v64H40a24.028 24.028 0 0 0-24 24v72a24.028 24.028 0 0 0 24 24h80a24.028 24.028 0 0 0 24-24v-72a24.028 24.028 0 0 0-24-24H96v-56h144v56h-24a24.028 24.028 0 0 0-24 24v72a24.028 24.028 0 0 0 24 24h80a24.028 24.028 0 0 0 24-24v-72a24.028 24.028 0 0 0-24-24h-24v-56h144v56h-24a24.028 24.028 0 0 0-24 24v72a24.028 24.028 0 0 0 24 24h80a24.028 24.028 0 0 0 24-24v-72a24.028 24.028 0 0 0-24-24ZM216 88h80v56h-80ZM112 360v56H48v-56Zm176 0v56h-64v-56Zm176 56h-64v-56h64Z"/></svg>
                        <span class="ms-3">Sections</span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="./section_code.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group <?php echo $current_page == 'section_code.php' ? 'bg-active text-black' : ''; ?>">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 512 512">
                        <path fill="currentColor" d="M457.47 55.833c-53.026-53.026-139.307-53.026-192.332 0L168.971 152l22.629 22.627l96.165-96.167a104 104 0 0 1 147.078 147.079l-96.167 96.167l22.624 22.627l96.167-96.167C510.5 195.14 510.5 108.86 457.47 55.833Zm-231.931 379.01a104 104 0 0 1-147.078 0a104 104 0 0 1 0-147.078l90.511-90.511l-22.627-22.627l-90.512 90.511A136 136 0 1 0 248.166 457.47l90.51-90.51l-22.627-22.627Z" />
                        <path fill="currentColor" d="m129.373 361.303l226.274-226.275l22.628 22.628L152 383.93z" />
                    </svg>
                    <span class="ms-3">Code</span>
                </a>
            </li>
            <li>
                <a href="../activities.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group <?php echo $current_page == 'activities.php' ? 'bg-active text-black' : ''; ?>">
                <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 512 512"><path fill="currentColor" d="M474.444 19.857a20.336 20.336 0 0 0-21.592-2.781L33.737 213.8v38.066l176.037 70.414L322.69 496h38.074l120.3-455.4a20.342 20.342 0 0 0-6.62-20.743ZM337.257 459.693L240.2 310.37l149.353-163.582l-23.631-21.576L215.4 290.069L70.257 232.012L443.7 56.72Z"/></svg>
                    <span class="ms-3">Activities</span>
                </a>
            </li>
            <li>
                <a href="./archived_classes.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group <?php echo $current_page == 'archived_classes.php' ? 'bg-active text-black' : ''; ?>">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 16 16"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M1.75 2.75h12.5v3.5H1.75zm5 6.5h2.5m-6.5-2.5v7.5h10.5v-7.5"/></svg>
                    <span class="ms-3">Archived</span>
                </a>
            </li>
            <li>
                <hr class="rounded-lg">
            </li>
            <li>
                <form method="POST">
                    <a href="../logout.php" class="w-full flex items-center p-2 text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
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