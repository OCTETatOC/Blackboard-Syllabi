<?php
    $connection = mysqli_connect("localhost", "root", "zfkeP9GJ", "Syllabi");
    if(!$connection)
    {
        die("<div>Could not connect to mysql table.</div><div>Please contact the OCTET office with this information.</div>");
    }

    $base_url = "https://conevals.csr.oberlin.edu/Syllabi";
    $base_directory = "/home/cegerton/Uploaded_Syllabi/";

    $username = $_POST['username'];
    $course_id = $_POST['course_id'];
    $course_data = explode('-', $course_id);

    $course_term = $course_data[0];
    $course_department = $course_data[1];
    $course_number = $course_data[2];
    $course_section = $course_data[3];

    $syllabus_query =
        "SELECT * FROM syllabi WHERE ".
        "term = '".$course_term."' AND ".
        "department = '".$course_department."' AND ".
        "number = '".$course_number."' AND ".
        "section = '".$course_section."';";

    $syllabus_result = mysqli_query($connection, $syllabus_query);

    if($syllabus_result)
    {
        $syllabus_row = mysqli_fetch_array($syllabus_result, MYSQLI_ASSOC);
        if($syllabus_row)
        {
            do
            {
                if($syllabus_row['is_link'] == 'N')
                {
                    $syllabus_location = $syllabus_row['location'];
                    unlink($base_directory.$syllabus_location);
                }
            } while($syllabus_row = mysqli_fetch_array($syllabus_result, MYSQLI_ASSOC));
            mysqli_free_result($syllabus_result);
            $removal_query =
                "DELETE FROM syllabi WHERE ".
                "term = '".$course_term."' AND ".
                "department = '".$course_department."' AND ".
                "number = '".$course_number."' AND ".
                "section = '".$course_section."' AND ".
                "instructor = '".$username."';";
            if(mysqli_query($connection, $removal_query))
            {
                echo "<div>Your syllabus was successfully removed.</div>";
            }
            else
            {
                echo "<div>We were unable to remove you syllabus. Please contact the OCTET office with this information.</div>";
            }
        }
        else
        {
            echo "<div>You do not currently have a syllabus uploaded.</div>";
        }
    }
    else
    {
        echo "<div>We were unable to locate your current syllabus. Please contact the OCTET office with this information.</div>";
    }
?>
