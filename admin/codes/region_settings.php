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
    $alt_currency = mysqli_real_escape_string($con, $_POST['alt_currency'] ?? '');
    $crypto = isset($_POST['crypto']) ? 1 : 0;
    $Channel = mysqli_real_escape_string($con, $_POST['Channel'] ?? '');
    $alt_channel = mysqli_real_escape_string($con, $_POST['alt_channel'] ?? '');
    $Channel_name = mysqli_real_escape_string($con, $_POST['Channel_name'] ?? '');
    $alt_ch_name = mysqli_real_escape_string($con, $_POST['alt_ch_name'] ?? '');
    $Channel_number = mysqli_real_escape_string($con, $_POST['Channel_number'] ?? '');
    $alt_ch_number = mysqli_real_escape_string($con, $_POST['alt_ch_number'] ?? '');
    $chnl_value = mysqli_real_escape_string($con, $_POST['chnl_value'] ?? '');
    $chnl_name_value = mysqli_real_escape_string($con, $_POST['chnl_name_value'] ?? '');
    $chnl_number_value = mysqli_real_escape_string($con, $_POST['chnl_number_value'] ?? '');
    $payment_amount = mysqli_real_escape_string($con, $_POST['payment_amount'] ?? '');
    $rate = mysqli_real_escape_string($con, $_POST['rate'] ?? '');
    $alt_rate = mysqli_real_escape_string($con, $_POST['alt_rate'] ?? '');

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
        $_SESSION['error'] = "Currency must be a 3-letter code.";
        header("Location: ../region_settings.php");
        exit(0);
    }

    // Validate alt_currency format if provided
    if (!empty($alt_currency) && !preg_match('/^[A-Z]{3}$/', $alt_currency)) {
        $_SESSION['error'] = "Alternate currency must be a 3-letter code.";
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

    // Validate alt_rate if provided
    if (!empty($alt_rate) && (!is_numeric($alt_rate) || $alt_rate <= 0)) {
        $_SESSION['error'] = "Alternate rate must be a positive number or a valid format.";
        header("Location: ../region_settings.php");
        exit(0);
    }

    // Check if country already exists
    $check_query = "SELECT id FROM region_settings WHERE country = ? LIMIT 1";
    $stmt = $con->prepare($check_query);
    $stmt->bind_param("s", $country);
    $stmt->execute();
    $check_run = $stmt->get_result();
    if ($check_run->num_rows > 0) {
        $_SESSION['error'] = "Region settings for this country already exist.";
        header("Location: ../region_settings.php");
        exit(0);
    }
    $stmt->close();

    // Insert new region using prepared statement
    $insert_query = "INSERT INTO region_settings (country, currency, alt_currency, crypto, Channel, alt_channel, Channel_name, alt_ch_name, Channel_number, alt_ch_number, chnl_value, chnl_name_value, chnl_number_value, payment_amount, rate, alt_rate) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($insert_query);
    $stmt->bind_param("sssissssssssssds", $country, $currency, $alt_currency, $crypto, $Channel, $alt_channel, $Channel_name, $alt_ch_name, $Channel_number, $alt_ch_number, $chnl_value, $chnl_name_value, $chnl_number_value, $payment_amount, $rate, $alt_rate);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Region added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add region: " . $stmt->error;
        error_log("region_settings.php - Insert query error: " . $stmt->error);
    }
    $stmt->close();
    header("Location: ../region_settings.php");
    exit(0);
}

// Update Region
if (isset($_POST['update_region'])) {
    $region_id = mysqli_real_escape_string($con, $_POST['region_id'] ?? '');
    $country = mysqli_real_escape_string($con, $_POST['country'] ?? '');
    $currency = mysqli_real_escape_string($con, $_POST['currency'] ?? '');
    $alt_currency = mysqli_real_escape_string($con, $_POST['alt_currency'] ?? '');
    $crypto = isset($_POST['crypto']) ? 1 : 0;
    $Channel = mysqli_real_escape_string($con, $_POST['Channel'] ?? '');
    $alt_channel = mysqli_real_escape_string($con, $_POST['alt_channel'] ?? '');
    $Channel_name = mysqli_real_escape_string($con, $_POST['Channel_name'] ?? '');
    $alt_ch_name = mysqli_real_escape_string($con, $_POST['alt_ch_name'] ?? '');
    $Channel_number = mysqli_real_escape_string($con, $_POST['Channel_number'] ?? '');
    $alt_ch_number = mysqli_real_escape_string($con, $_POST['alt_ch_number'] ?? '');
    $chnl_value = mysqli_real_escape_string($con, $_POST['chnl_value'] ?? '');
    $chnl_name_value = mysqli_real_escape_string($con, $_POST['chnl_name_value'] ?? '');
    $chnl_number_value = mysqli_real_escape_string($con, $_POST['chnl_number_value'] ?? '');
    $payment_amount = mysqli_real_escape_string($con, $_POST['payment_amount'] ?? '');
    $rate = mysqli_real_escape_string($con, $_POST['rate'] ?? '');
    $alt_rate = mysqli_real_escape_string($con, $_POST['alt_rate'] ?? '');

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
        $_SESSION['error'] = "Currency must be a 3-letter code.";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }

    // Validate alt_currency format if provided
    if (!empty($alt_currency) && !preg_match('/^[A-Z]{3}$/', $alt_currency)) {
        $_SESSION['error'] = "Alternate currency must be a 3-letter code.";
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

    // Validate alt_rate if provided
    if (!empty($alt_rate) && (!is_numeric($alt_rate) || $alt_rate <= 0)) {
        $_SESSION['error'] = "Alternate rate must be a positive number or a valid format.";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }

    // Check if country already exists for another record
    $check_query = "SELECT id FROM region_settings WHERE country = ? AND id != ? LIMIT 1";
    $stmt = $con->prepare($check_query);
    $stmt->bind_param("si", $country, $region_id);
    $stmt->execute();
    $check_run = $stmt->get_result();
    if ($check_run->num_rows > 0) {
        $_SESSION['error'] = "Region settings for this country already exist.";
        header("Location: ../edit-region.php?id=$region_id");
        exit(0);
    }
    $stmt->close();

    // Update region using prepared statement
    $update_query = "UPDATE region_settings SET 
                     country = ?, 
                     currency = ?, 
                     alt_currency = ?,
                     crypto = ?, 
                     Channel = ?, 
                     alt_channel = ?,
                     Channel_name = ?, 
                     alt_ch_name = ?,
                     Channel_number = ?, 
                     alt_ch_number = ?,
                     chnl_value = ?, 
                     chnl_name_value = ?, 
                     chnl_number_value = ?, 
                     payment_amount = ?, 
                     rate = ?, 
                     alt_rate = ? 
                     WHERE id = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param("sssissssssssssdsi", $country, $currency, $alt_currency, $crypto, $Channel, $alt_channel, $Channel_name, $alt_ch_name, $Channel_number, $alt_ch_number, $chnl_value, $chnl_name_value, $chnl_number_value, $payment_amount, $rate, $alt_rate, $region_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Region updated successfully.";
        header("Location: ../region_settings.php");
    } else {
        $_SESSION['error'] = "Failed to update region: " . $stmt->error;
        error_log("region_settings.php - Update query error: " . $stmt->error);
        header("Location: ../edit-region.php?id=$region_id");
    }
    $stmt->close();
    exit(0);
}

// Delete Region
if (isset($_POST['delete'])) {
    $region_id = mysqli_real_escape_string($con, $_POST['delete']);
    $delete_query = "DELETE FROM region_settings WHERE id = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param("i", $region_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Region deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete region: " . $stmt->error;
        error_log("region_settings.php - Delete query error: " . $stmt->error);
    }
    $stmt->close();
    header("Location: ../region_settings.php");
    exit(0);
}

// Invalid request
$_SESSION['error'] = "Invalid request.";
header("Location: ../region_settings.php");
exit(0);
?>
