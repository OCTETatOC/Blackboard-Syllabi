<?php
    $base_url = "https://conevals.csr.oberlin.edu/Syllabi";
    $base_directory = "/home/cegerton/Uploaded_Syllabi/";
    if(!array_key_exists('course', $_GET))
    {
        die("<div>No course has been specified.</div>");
    }
    $course_id = $_GET['course'];

    $connection = mysqli_connect("localhost", "root", "zfkeP9GJ", "Syllabi");
    if(!$connection)
    {
        die("<div>Could not connect to mysql table.</div><div>Please contact the OCTET office with this information.</div>");
    }

    $course_data = explode('-', $course_id);

    $course_term = mysqli_real_escape_string($connection, $course_data[0]);
    $course_department = mysqli_real_escape_string($connection, $course_data[1]);
    $course_number = mysqli_real_escape_string($connection, $course_data[2]);
    $course_section = mysqli_real_escape_string($connection, $course_data[3]);

    $syllabus_query =
        "SELECT * FROM syllabi WHERE ".
        "term = '".$course_term."' AND ".
        "department = '".$course_department."' AND ".
        "number = '".$course_number."' AND ".
        "section = '".$course_section."';";

    $syllabus_result = mysqli_query($connection, $syllabus_query);
    if(!$syllabus_result)
    {
        die("<div>We were unable to retrieve the syllabus you requested due to a selection error.</div><div>Please contact the OCTET office with this information.</div>");
    }
    $syllabus_row = mysqli_fetch_assoc($syllabus_result);

    $role = 'S';
    $username = '';
    if(array_key_exists('role', $_POST) && array_key_exists('username', $_POST))
    {
        $role = $_POST['role'];
        $username = $_POST['username'];
    }
    switch($role)
    {
        case 'P':
        case 'T':
            echo '<h3><a href="'.$base_url.'/view.php?course='.$course_id.'">Click here</a> to view the current syllabus (if one exists).</h3>';

            echo '<h3><form action="'.$base_url.'/delete.php" method="post" id="delete_form">';
                echo '<input type="hidden" name="course_id" value="'.$course_id.'" />';
                echo '<input type="hidden" name="username" value="'.$username.'" />';
                echo '<input type="hidden" name="role" value="'.$role.'" />';
                echo '<a href="javascript:{}" onclick="if(confirm(\'Are you sure you want to delete the syllabus for this course?\')) document.getElementById(\'delete_form\').submit();">Click here</a> to remove the current syllabus (if one exists).';
            echo '</form></h3>';

            echo '<h3>Upload a new syllabus here:</h3>';
            echo '<form action="'.$base_url.'/upload.php" method="post" enctype="multipart/form-data">';
                echo '<input type="hidden" name="MAX_FILE_SIZE" value="100000">';
                echo '<input type="hidden" name="role" value="S" />';
                echo '<input type="hidden" name="courses[]" value="'.$course_id.'" />';
                echo '<input type="hidden" name="username" value="'.$username.'" />';
                echo '&emsp;&emsp;';
                echo '<input type="file" name="'.$course_id.'"><br />';
                echo '&emsp;&emsp;';
                echo '<input type="radio" name="is_visible_'.$course_id.'" value="true"><b>Allow</b> students not registered for the course to see this syllabus.<br />';
                echo '&emsp;&emsp;';
                echo '<input type="radio" name="is_visible_'.$course_id.'" value="false" checked><b>Do not</b> allow students not registered for the course to see this syllabus.<br />';
                echo '<input type="submit" value="Upload file" />';
            echo '</form>';
            break;
        case 'S':
        default:
            if($syllabus_row)
            {
                $filepath = $base_directory.$syllabus_row['location'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                ob_start();
                header('Content-Description: File Transfer');
                header('Content-Type: '.finfo_file($finfo, $filepath));
                header('Content-Disposition: inline; filename='.basename($filepath));
                ob_clean();
                ob_end_flush();
                readfile($filepath);
            }
            else
            {
                die("<div>There is no syllabus currently posted for this course.</div><div>If you believe this to be incorrect, please contact your professor.</div>");
            }
            break;
    }
    mysqli_free_result($syllabus_result);
?>
