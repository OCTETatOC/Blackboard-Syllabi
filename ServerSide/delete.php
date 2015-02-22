<?php
    $course_id = "<None received>";
    $username = "<None received>";
    $role = "<None received>";
    if(array_key_exists('course_id', $_POST))
    {
        $course_id = $_POST['course_id'];
    }
    if(array_key_exists('username', $_POST))
    {
        $username = $_POST['username'];
    }
    if(array_key_exists('role', $_POST))
    {
        $role = $_POST['role'];
    }

    echo "<div>Your username is ".$username.", ";
    echo "your role is represented by the letter ".$role.", ";
    echo "and you would like to remove the syllabus for the course with the id ".$course_id.".</div>";
?>
