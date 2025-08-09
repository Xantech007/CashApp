<?php
session_start();
include('../config/dbcon.php');
include('inc/header.php');
include('inc/navbar.php');

// Check if user is logged in
if (!isset($_SESSION['auth'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    error_log("verify.php - User not logged in, redirecting to signin.php");
    header("Location: ../signin.php");
    exit(0);
}

// Get user data (including country) from users table
$email = mysqli_real_escape_string($con, $_SESSION['email']);
$user_query = "SELECT verify, country FROM users WHERE email = '$email' LIMIT 1";
$user_query_run = mysqli_query($con, $user_query);
if ($user_query_run && mysqli_num_rows($user_query_run) > 0) {
    $user_data = mysqli_fetch_assoc($user_query_run);
    $verify = $user_data['verify'] ?? 0;
    $user_country = $user_data['country'] ?? '';
} else {
    $_SESSION['error'] = "User not found.";
    error_log("verify.php - User not found for email: $email");
    header("Location: ../signin.php");
    exit(0);
}

// Get crypto setting from region_settings table based on user's country
$crypto_label = "Local Bank Deposit/Transfer"; // Default label
if (!empty($user_country)) {
    $region_query = "SELECT crypto FROM region_settings WHERE country = '" . mysqli_real_escape_string($con, $user_country) . "' LIMIT 1";
    $region_query_run = mysqli_query($con, $region_query);
    if ($region_query_run && mysqli_num_rows($region_query_run) > 0) {
        $region_data = mysqli_fetch_assoc($region_query_run);
        $crypto_value = $region_data['crypto'] ?? 0;
        if ($crypto_value == 1) {
            $crypto_label = "Crypto Deposit/Transfer";
        }
    } else {
        error_log("verify.php - No region settings found for country: $user_country");
    }
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Verify Identity</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index">Home</a></li>
                <li class="breadcrumb-item active">Verify</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <?php
    if (isset($_SESSION['error'])) { ?>
        <div class="modal fade show" id="errorModal" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="window.location.href='verify.php'">Ok</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
        <?php unset($_SESSION['error']);
    }
    ?>

    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-header">
                        Select Verification Method
                    </div>
                    <div class="card-body mt-3">
                        <form action="verify-complete.php" method="POST">
                            <div class="mb-3">
                                <select class="form-select" id="verification_method" name="verification_method" required>
                                    <option value="" disabled selected>Select a verification method</option>
                                    <option value="Driver's License">Driver's License</option>
                                    <option value="USA Support Card">USA Support Card</option>
                                    <option value="<?= htmlspecialchars($crypto_label) ?>"><?= htmlspecialchars($crypto_label) ?></option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Proceed</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main><!-- End #main -->

<?php include('inc/footer.php'); ?>

<!-- Inline CSS for Layout (unchanged) -->
<style>
    html, body {
        height: 100%;
        margin: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    #main {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
    }

    .container {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #f8f9fa;
        z-index: 1000;
        text-align: center;
        padding: 10px 0;
    }

    body {
        padding-bottom: 60px;
    }

    @media (max-width: 576px) {
        .footer {
            padding: 5px 0;
            font-size: 14px;
        }
    }
</style>
</html>
