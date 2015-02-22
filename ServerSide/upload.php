<?php
    if(count($_FILES) > 0)
    {
        $connection = mysqli_connect("localhost", "root", "zfkeP9GJ", "Syllabi");
        if(!$connection)
        {
            die("<div>Could not connect to mysql table. Please contact the OCTET office with this information.</div>");
        }
        $username = mysqli_real_escape_string($connection, $_POST['username']);
        $base_directory = "/home/cegerton/Uploaded_Syllabi/";
        foreach($_POST['courses'] as $course_id)
        {
            if($_FILES[$course_id]['error'] === UPLOAD_ERR_OK)
            {
                if($_FILES[$course_id]['size'] != 0)
                {
                    $course_data = explode('-', $course_id);

                    $course_term = mysqli_real_escape_string($connection, $course_data[0]);
                    $course_department = mysqli_real_escape_string($connection, $course_data[1]);
                    $course_number = mysqli_real_escape_string($connection, $course_data[2]);

                    $is_visible = $_POST['is_visible_'.$course_id] == 'true';
                    // $is_link = $_POST['is_link_'.$course_id] == 'true'; USE WHEN READY
                    $is_link = false;

                    $filename = $_FILES[$course_id]['name'];
                    $extension = end(explode('.', $filename));

                    $tmp_filepath = $_FILES[$course_id]['tmp_name'];
                    $new_filepath = "";

                    if($is_visible)
                        $new_filepath .= "public/";
                    else
                        $new_filepath .= "private/";

                    $new_filename = uniqid(rand());
                    while(file_exists($base_directory.$new_filepath.$new_filename.$extension))
                    {
                        $new_filename = uniqid(rand());
                    }

                    $new_filepath .= $new_filename;
                    $new_filepath .= '.'.$extension;

                    $new_filepath_sanitized = mysqli_real_escape_string($connection, $new_filepath);

                    echo "<div>Your syllabus for the course $course_id was named $filename, and you elected ";
                    if(!$is_visible)
                        echo "not ";
                    echo "to make it visible to students outside the course. It has been uploaded ";
                    if(move_uploaded_file($tmp_filepath, $base_directory.$new_filepath))
                    {
                        echo "successfully.</div>";
                        chmod($base_directory.$new_filepath, 0666);

                        $exists_query =
                            "SELECT EXISTS(SELECT 1 FROM syllabi WHERE ".
                            "instructor = '".$username."' AND ".
                            "term = '".$course_term."' AND ".
                            "department = '".$course_department."' AND ".
                            "number = '".$course_number."');";

                        $exists_result = mysqli_query($connection, $exists_query);

                        if($exists_result)
                        {
                            $exists_array = mysqli_fetch_array($exists_result, MYSQLI_NUM);
                            $query = "";

                            if($exists_array[0])
                            {
                                $query =
                                    "UPDATE syllabi SET ".
                                    "is_visible = '".($is_visible ? "Y" : "N")."', ".
                                    "is_link = '".($is_link ? "Y" : "N")."', ".
                                    "location = '".$new_filepath_sanitized."' ".
                                    "WHERE ".
                                    "instructor = '".$username."' AND ".
                                    "term = '".$course_term."' AND ".
                                    "department = '".$course_department."' AND ".
                                    "number = '".$course_number."';";
                            }
                            else
                            {
                                $query =
                                    "INSERT INTO syllabi VALUES ('".
                                    $username."', '".
                                    $course_term."', '".
                                    $course_department."', '".
                                    $course_number."', '".
                                    ($is_visible ? "Y" : "N")."', '".
                                    ($is_link ? "Y" : "N")."', '".
                                    $new_filepath_sanitized."');";
                            }

                            echo "<div>";
                            if(mysqli_query($connection, $query))
                            {
                                echo "Our attempt to add it to our table has also succeeded.";
                            }
                            else
                            {
                                echo "Unfortunately, we were not able to add it to our table due to an insertion/update error.";
                            }
                            echo "</div>";
                        }
                        else
                        {
                            echo "<div>Unfortunately, we were not able to add it to our table due to a selection error.</div>";
                        }
                    }
                    else
                    {
                        echo "unsuccessfully.</div>";
                    }
                }
                else
                {
                    echo "<div>You have tried to upload an empty file for the course $course_id.</div>";
                }
            }
            else if($_FILES[$course_id]['error'] != UPLOAD_ERR_NO_FILE)
            {
                echo "<div>";
                switch($_FILES[$course_id]['error'])
                {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        echo "Your syllabus for the course $course_id the exceeds the maximum allowed size.";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        echo "Your syllabus for the course $course_id was only partially uploaded.";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        echo "A temporary directory does not exist on the server to store your syllabus for the course $course_id.";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        echo "Your syllabus for the course $course_id could not be written to the server.";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        echo "A PHP extension stopped the upload of your syllabus for the course $course_id.";
                        break;
                    default:
                        echo "An unknown error occurred with code ".$_FILES[$course_id]['error']." while uploading your syllabus for course ".$course_id.".";
                        break;
                }
                echo " Please contact the OCTET office with this information.</div>";
            }
        }
    }
    else
    {
        echo "<div>You got here without sending us any files! How'd that happen?</div>";
    }
?>
