<?php
session_start();
include('../../config/dbcon.php');



if(isset($_POST['complete']))
{
    $id = mysqli_real_escape_string($con, $_POST['complete']);  

    $query = "UPDATE withdrawals SET status = '1' WHERE id='$id' LIMIT 1";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['success'] = "Updated successfully";
        header("Location: ../manage-withdrawals");
        exit(0);
    }
}
