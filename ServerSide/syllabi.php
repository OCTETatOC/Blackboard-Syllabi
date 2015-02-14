<?php
    if(count($_FILES) > 0)
    {
        $username = $_POST['username'];
        $base_directory = "/home/cegerton/Blackboard_Syllabi/";
        foreach($_POST['courses'] as $course_id)
        {
            if($_FILES[$course_id]['error'] === UPLOAD_ERR_OK)
            {
                if($_FILES[$course_id]['size'] != 0)
                {
                    $visible = $_POST['public_'.$course_id] === 'true';
                    $filename = $_FILES[$course_id]['name'];
                    $tmp_filepath = $_FILES[$course_id]['tmp_name'];
                    $tmp_filepath = $_FILES[$course_id]['tmp_name'];
                    $new_filepath = $base_directory;
                    if($visible)
                        $new_filepath .= "public/";
                    else
                        $new_filepath .= "private/";
                    $new_filepath .= $username."___".$course_id;

                    echo "<div>Your syllabus for the course $course_id was named $filename, and you elected ";
                    if(!$visible)
                        echo "not ";
                    echo "to make it visible to students outside the course. It has been uploaded ";
                    if(move_uploaded_file($tmp_filepath, $new_filepath))
                    {
                        echo "successfully.</div>";
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
            else
            {
                echo "<div>";
                switch($_FILES[$course_id]['error'])
                {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        echo "Your syllabus for course ".$course_id."exceeds the maximum allowed size.";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        echo "Your syllabus for course ".$course_id."was only partially uploaded.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        echo "Your syllabus for course ".$course_id."was not uploaded.";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        echo "A temporary directory does not exist on the server to store your syllabus for course ".$course_id.".";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        echo "Your syllabus for course ".$course_id." could not be written to the server.";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        echo "A PHP extension stopped the upload of your syllabus for course ".$course_id.".";
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
