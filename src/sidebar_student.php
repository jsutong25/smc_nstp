<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto bg-black dark:bg-black">
        <a href="./student_home.php?section_id=<?php echo $section_id; ?>" class="flex items-center ps-2.5 mb-5">
            <!-- <img src="../../assets/logo.png" class="h-6 me-3 sm:h-7" alt="Flowbite Logo" /> -->
            <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">SMC NSTP</span>
        </a>
        <ul class="space-y-2 font-medium">
            <li>
                <a href="./student_home.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 22 21">
                        <path fill="currentColor" d="M18 18V7.132l-8-4.8l-8 4.8V18h4v-2.75a4 4 0 1 1 8 0V18h4zm-6 2v-4.75a2 2 0 1 0-4 0V20H2a2 2 0 0 1-2-2V7.132a2 2 0 0 1 .971-1.715l8-4.8a2 2 0 0 1 2.058 0l8 4.8A2 2 0 0 1 20 7.132V18a2 2 0 0 1-2 2h-6z" />
                    </svg>
                    <span class="ms-3">Home</span>
                </a>
            </li>
            <li>
                <a href="../activities.php?section_id=<?php echo $section_id; ?>" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 512 512">
                        <path fill="currentColor" d="M474.444 19.857a20.336 20.336 0 0 0-21.592-2.781L33.737 213.8v38.066l176.037 70.414L322.69 496h38.074l120.3-455.4a20.342 20.342 0 0 0-6.62-20.743ZM337.257 459.693L240.2 310.37l149.353-163.582l-23.631-21.576L215.4 290.069L70.257 232.012L443.7 56.72Z" />
                    </svg>
                    <span class="ms-3">Activities</span>
                </a>
            </li>
            <li>
                <a href="./profile.php?section_id=<?php echo $section_id; ?>&user_id=<?php echo $user_id; ?>" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 512 512">
                        <path fill="currentColor" d="m411.6 343.656l-72.823-47.334l27.455-50.334A80.23 80.23 0 0 0 376 207.681V128a112 112 0 0 0-224 0v79.681a80.236 80.236 0 0 0 9.768 38.308l27.455 50.333l-72.823 47.334A79.725 79.725 0 0 0 80 410.732V496h368v-85.268a79.727 79.727 0 0 0-36.4-67.076ZM416 464H112v-53.268a47.836 47.836 0 0 1 21.841-40.246l97.66-63.479l-41.64-76.341A48.146 48.146 0 0 1 184 207.681V128a80 80 0 0 1 160 0v79.681a48.146 48.146 0 0 1-5.861 22.985L296.5 307.007l97.662 63.479A47.836 47.836 0 0 1 416 410.732Z" />
                    </svg>
                    <span class="ms-3">Profile</span>
                </a>
            </li>
            <li>
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