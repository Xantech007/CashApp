<?php
session_start();
include('../../config/dbcon.php');
include('../inc/countries.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../signin.php");
    exit(0);
}

// Verify user authorization
$auth_id = mysqli_real_escape_string($con, $_POST['auth_id'] ?? '');
if ($auth_id != $_SESSION['id']) {
    $_SESSION['error'] = "Unauthorized action.";
    header("Location: ../region_settings.php");
    exit(0);
}

// Add Region
if (isset($_POST['add_region'])) {
    $country = mysqli_real_escape_string($con, $_POST['country'] ?? '');
    $currency = mysqli_real_escape_string($con, $_POST['currency'] ?? '');
    $crypto = isset($_POST['crypto']) ? 1 : 0; // Handle checkbox (1 for checked, 0 for unchecked)
    $Channel = mysqli_real_escape_string($con, $_POST['Channel'] ?? '');
    $Channel_name = mysqli_real_escape_string($con, $_POST['Channel_name'] ?? '');
    $Channel_number = mysqli_real_escape_string($con, $_POST['Channel_number'] ?? '');
    $chnl_value = mysqli_real_escape_string($con, $_POST['chnl_value'] ?? '');
    $chnl_name_value = mysqli_real_escape_string($con, $_POST['chnl_name_value'] ?? '');
    $chnl_number_value = mysqli_real_escape_string($con, $_POST['chnl_number_value'] ?? '');
    $payment_amount = mysqli_real_escape_string($con, $_POST['payment_amount'] ?? '');
    $rate = mysqli_real_escape_string($con, $_POST['rate'] ?? '');

    // Validate inputs
    if (empty($country) || empty($currency) || empty($Channel) || empty($Channel_name) || empty($Channel_number) || empty($payment_amount) || empty($rate)) {
        $_SESSION['error'] = "All required fields must be filled.";
        header("Location: ../region_settings.php");
        exit(0);
    }

    // Validate country
    if (!isset($countries) || !in_array($country, $countries)) {
        $_SESSION['error'] = "Invalid country selected.";
        header("Location: ../region_settings.php");
        exit(0);
    }

    // Validate currency format (3-letter code)
    if (!preg_match('/^[A-Z]{3}$/', $currency)) {
        $_SESSION['error'] = "Currency must be a 3-letter code (e.g., NGN or USDT).";
        header("Location: ../region_settings.php");
        exit(0);
    }

    // Validate payment_amount and rate (must be positive numbers)
    if (!is_numeric($payment_amount) || $payment_amount <= 0) {
        $_SESSION['error'] = "Payment amount must be a positive number.";
        header("Location: ../region_settings.php");
        exit(0);
    }
    if (!is_numeric($rate) || $rate <= 0) {
        $_SESSION['error'] = "Rate must be a positive number.";
        header("Location: ../region_settings.php");
        exit(0);
    }

    // Check if country already exists
    $check_query = "SELECT id FROM region_settings WHERE country = '$country' LIMIT 1";
    $check_run = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_run) > 0) {
        $_SESSION['error'] = "Region settings for this country already exist.";
        header("Location: ../region_settings.php");
        exit(0);
    }

    // Insert new region
    $insert_query = "INSERT INTO region_settings (country, currency, crypto, Channel, Channel_name, Channel_number, chnl_value, chnl_name_value, chnl_number_value, payment_amount, rate) 
                     VALUES ('$country', '$currency', '$crypto', '$Channel', '$Channel_name', '$Channel_number', '$chnl_value', '$chnl_name_value', '$chnl_number_value', '$payment_amount', '$rate')";
    if (mysqli_query($con, $insert_query)) {
        $_SESSION['success'] = "Region added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add region: " . mysqli_error($con);
        error_log("region_settings.php - Insert query error: " . mysqli_error($con));
    }
    header("Location: ../region_settings.php");
    exit(0);
}

// Update Region
if (isset($_POST['update_region'])) {
    $region_id = mysqli_real_escape_string($con, $_POST['region_id'] ?? '');
    $country = mysqli_real_escape_string($con, $_POST['country'] ?? '');
    $currency = mysqli_real_escape_string($con, $_POST['currency'] ?? '');
    $crypto = isset($_POST['crypto']) ? 1 : 0; // Handle checkbox
    $Channel = mysqli_real_escape_string($con, $_POST['Channel'] ?? '');
    $Channel_name = mysqli_real_escape_string($con, $_POST['Channel_name'] ?? '');
    $Channel_number = mysqli_real_escape_string($con, $_POST['Channel_number'] ?? '');
    $chnl_value = mysqli_real_escape_string($con, $_POST['chnl_value'] ?? '');
    $chnl_name_value = mysqli_real_escape_string($con, $_POST['chnl_name_value'] ?? '');
    $chnl_number_value = mysqli_real_escape_string($con, $_POST['chnl_number_value'] ?? '');
    $payment_amount = mysqli_real_escape_string($con, $_POST['payment_amount'] ?? '');
    $rate = mysqli_real_escape_string($con, $_POST['rate'] ?? '');

    // Validate inputs
    if (empty($region_id) || empty($country) || empty($currency) || empty($Channel) || empty($Channel_name) || empty($Channel_number) || empty($payment_amount) || empty($rate)) {
        $_SESSION['error'] = "All required fields must be filled.";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }

    // Validate country
    if (!isset($countries) || !in_array($country, $countries)) {
        $_SESSION['error'] = "Invalid country selected.";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }

    // Validate currency format
    if (!preg_match('/^[A-Z]{3}$/', $currency)) {
        $_SESSION['error'] = "Currency must be a 3-letter code (e.g., NGN or USDT).";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }

    // Validate payment_amount and rate
    if (!is_numeric($payment_amount) || $payment_amount <= 0) {
        $_SESSION['error'] = "Payment amount must be a positive number.";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }
    if (!is_numeric($rate) || $rate <= 0) {
        $_SESSION['error'] = "Rate must be a positive number.";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }

    // Check if country already exists for another record
    $check_query = "SELECT id FROM region_settings WHERE country = '$country' AND id != '$region_id' LIMIT 1";
    $check_run = mysqli_query($con, $check_query);
    if (mysqli_num_rows($check_run) > 0) {
        $_SESSION['error'] = "Region settings for this country already exist.";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }

    // Update region
    $update_query = "UPDATE region_settings SET 
                     country = '$country', 
                     currency = '$currency', 
                     crypto = '$crypto', 
                     Channel = '$Channel', 
                     Channel_name = '$Channel_name', 
                     Channel_number = '$Channel_number', 
                     chnl_value = '$chnl_value', 
                     chnl_name_value = '$chnl_name_value', 
                     chnl_number_value = '$chnl_number_value', 
                     payment_amount = '$payment_amount', 
                     rate = '$rate' 
                     WHERE id = '$region_id'";
    if (mysqli_query($con, $update_query)) {
        $_SESSION['success'] = "Region updated successfully.";
        header("Location: ../region_settings.php");
    } else {
        $_SESSION['error'] = "Failed to update region: " . mysqli_error($con);
        error_log("region_settings.php - Update query error: " . mysqli_error($con));
        header("Location: ../edit-region.php?id=$region_id");
    }
    exit(0);
}

// Delete Region
if (isset($_POST['delete'])) {
    $region_id = mysqli_real_escape_string($con, $_POST['delete']);
    $delete_query = "DELETE FROM region_settings WHERE id = '$region_id'";
    if (mysqli_query($con, $delete_query)) {
        $_SESSION['success'] = "Region deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete region: " . mysqli_error($con);
        error_log("region_settings.php - Delete query error: " . mysqli_error($con));
    }
    header("Location: ../region_settings.php");
    exit(0);
}

// Invalid request
$_SESSION['error'] = "Invalid request.";
header("Location: ../region_settings.php");
exit(0);
?>
