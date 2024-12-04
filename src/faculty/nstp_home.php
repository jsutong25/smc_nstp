<div class="flex-grow p-4 sm:ml-64 md:ml-52 lg:ml-[210px] overflow-hidden max-w-full">
    <h1 class="text-[22px] mb-8">NSTP Coordinator</h1>

    <div class="flex space-x-4 mb-8 overflow-hidden">
        <?php if ($sections_result && mysqli_num_rows($sections_result) > 0): ?>
            <?php
            $selected_section_id = isset($_GET['section_id']) ? $_GET['section_id'] : null;
            ?>
            <?php while ($section = mysqli_fetch_assoc($sections_result)): ?>
                <a href="?section_id=<?php echo $section['section_id']; ?>"
                    class="<?php echo ($selected_section_id == $section['section_id'])
                                ? 'border-2 border-red-900 text-white py-2 px-4 rounded'
                                : 'bg-primary text-white py-2 px-4 rounded hover:bg-red-700';
                            ?>">
                    <?php echo $section['section_name']; ?>
                </a>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>


    <h2 class="text-[18px] text-primary mb-7 hover:text-red-900"><a href="./student_information.php?section_id=<?php echo $section_id; ?>">Student Information</a></h2>
    <h2 class="text-[18px] text-primary mb-7 hover:text-red-900"><a href="./faculty_information.php?section_id=<?php echo $section_id; ?>">Faculty Information</a></h2>

    <!-- Upcoming activities -->
    <div class="w-full">
        <div class="flex justify-between items-center">
            <h2 class="text-[18px] mb-4 hover:text-gray-400"><a href="../activities.php?section_id=<?php echo $section_id; ?>">Upcoming Activities</a></h2>
            <a href="./new_activity.php?section_id=<?php echo $section_id; ?>">
                <svg class="transition-colors hover:text-primary ease-in-out" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16">
                    <path fill="currentColor" d="M8 15c-3.86 0-7-3.14-7-7s3.14-7 7-7s7 3.14 7 7s-3.14 7-7 7ZM8 2C4.69 2 2 4.69 2 8s2.69 6 6 6s6-2.69 6-6s-2.69-6-6-6Z" />
                    <path fill="currentColor" d="M8 11.5c-.28 0-.5-.22-.5-.5V5c0-.28.22-.5.5-.5s.5.22.5.5v6c0 .28-.22.5-.5.5Z" />
                    <path fill="currentColor" d="M11 8.5H5c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h6c.28 0 .5.22.5.5s-.22.5-.5.5Z" />
                </svg>
            </a>
        </div>

        <div>
            <?php
            if ($activities_result !== null) {
                if (mysqli_num_rows($activities_result) > 0) {
                    $counter = 0;
                    while ($row = mysqli_fetch_assoc($activities_result)) {
                        if ($counter >= 3) {
                            break;
                        }
                        $formatted_date = date("F j, Y", strtotime($row['date']));
                        $formatted_time = date("h:i A", strtotime($row['time']));
                        $activity_name = $row['activity_name'];
                        $description = $row['description'];

                        $short_title = substr($activity_name, 0, 25) . (strlen($activity_name) > 25 ? '...' : '');
                        $short_description = substr($description, 0, 10) . (strlen($description) > 20 ? '...' : '');
            ?>
                        <div class="flex gap-1 mb-4">
                            <div class="w-[100px] flex flex-col text-subtext items-start">
                                <p class="text-[16px]"><?php echo $formatted_date; ?></p>
                                <p class="text-[16px]"><?php echo $formatted_time; ?></p>
                            </div>

                            <div class="w-1.5 h-auto bg-gray-500"></div>

                            <a class="flex-1 transition ease-linear hover:bg-gray-400 hover:bg-opacity-15 mx-2 sm:mx-8 w-full"
                                href="../view_activity.php?activity_id=<?php echo $row['activity_id']; ?>&section_id=<?php echo $section_id; ?>&documentation_id=<?php echo $row['documentation_id']; ?>">
                                <div class="flex-1">
                                    <h5 class="block md:hidden"><?php echo $short_title; ?></h5>
                                    <h5 class="hidden md:block text-[16px]"><?php echo $activity_name; ?></h5>

                                    <p class="block md:hidden text-subtext"><?php echo $short_description; ?></p>
                                    <p class="hidden md:block text-subtext">
                                        <?php echo (strlen($description) > 40) ? substr($description, 0, 40) . '...' : $description; ?>
                                    </p>
                                </div>
                            </a>
                        </div>
            <?php
                        $counter++;
                    }
                } else {
                    echo "<p class='italic text-center text-[16px] mt-4'>No upcoming activities for this section.</p>";
                }
            } else {
                echo "<p class='italic text-center text-[16px]'>Select a section above to see activities.</p>";
            }
            ?>
        </div>
    </div>
</div>