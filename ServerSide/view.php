<?php
    if($_POST['test'] == 'success')
    {
        header("Location: https://conevals.csr.oberlin.edu/Syllabi/Uploaded_Syllabi/public/cegerton_201502-MUTH-665-01.pdf");
        die();
    }
    else
        echo "<div>Post data failed. Post['test'] was ".$_POST['test'].".</div>";
?>
