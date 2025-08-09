<?php
session_start();
include('../config/dbcon.php');
// Include the countries file for validation
include('../users/inc/countries.php');

if (isset($_POST['register'])) {
    // Sanitize and validate input data
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $country = mysqli_real_escape_string($con, $_POST['country']);
    $ref = isset($_POST['ref']) ? mysqli_real_escape_string($con, $_POST['ref']) : '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($country)) {
        $_SESSION['error'] = "All required fields must be filled.";
        header("Location: ../signup");
        exit(0);
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: ../signup");
        exit(0);
    }

    // Validate country
    if (!in_array($country, $countries)) {
        $_SESSION['error'] = "Invalid country selected.";
        header("Location: ../signup");
        exit(0);
    }

    // Check if email already exists
    $check_email_query = "SELECT email FROM users WHERE email = '$email' LIMIT 1";
    $check_email_run = mysqli_query($con, $check_email_query);

    if (mysqli_num_rows($check_email_run) > 0) {
        $_SESSION['error'] = "Email already exists!";
        header("Location: ../signup");
        exit(0);
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Set default values for other fields
    $balance = 0; // Default balance
    $image = ''; // Default image
    $address = ''; // Default address
    $btc_wallet = ''; // Default btc_wallet

    // Insert user data into the database
    $insert_query = "INSERT INTO users (name, email, password, country, ref, balance, image, address, btc_wallet, created_at) 
                     VALUES ('$name', '$email', '$hashed_password', '$country', '$ref', '$balance', '$image', '$address', '$btc_wallet', NOW())";
    $insert_query_run = mysqli_query($con, $insert_query);

    if ($insert_query_run) {
        // Get the newly created user's data for login
        $user_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $user_query_run = mysqli_query($con, $user_query);

        if (mysqli_num_rows($user_query_run) > 0) {
            foreach ($user_query_run as $data) {
                $user_id = $data['id'];
                $user_name = $data['name'];
                $balance = $data['balance'];
                $email = $data['email'];
                $image = $data['image'];
                $address = $data['address'];
                $btc_wallet = $data['btc_wallet'];
                $ref = $data['ref'];
                $country = $data['country']; // Include country in session
            }

            // Set session variables for login
            $_SESSION['auth'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['name'] = $user_name;
            $_SESSION['balance'] = $balance;
            $_SESSION['address'] = $address;
            $_SESSION['btc_wallet'] = $btc_wallet;
            $_SESSION['image'] = $image;
            $_SESSION['ref'] = $ref;
            $_SESSION['country'] = $country; // Add country to session

            // Set success message for users/index.php
            $_SESSION['success'] = "Registration successful!";

            // Redirect to the user dashboard
            header("Location: ../users/index");
            exit(0);
        } else {
            $_SESSION['error'] = "Error retrieving user data after registration.";
            header("Location: ../signup");
            exit(0);
        }
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        error_log("signup.php - Insert query error: " . mysqli_error($con));
        header("Location: ../signup");
        exit(0);
    }
} else {
    $_SESSION['error'] = "Invalid form submission.";
    header("Location: ../signup");
    exit(0);
}
?>
