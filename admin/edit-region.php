<?php
session_start();
include('../config/dbcon.php');
include('inc/header.php');
include('inc/navbar.php');
include('inc/sidebar.php');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid region ID.";
    header("Location: region_settings.php");
    exit(0);
}

$region_id = mysqli_real_escape_string($con, $_GET['id']);
$query = "SELECT * FROM region_settings WHERE id = '$region_id' LIMIT 1";
$query_run = mysqli_query($con, $query);

if (mysqli_num_rows($query_run) > 0) {
    $region = mysqli_fetch_assoc($query_run);
} else {
    $_SESSION['error'] = "Region not found.";
    header("Location: region_settings.php");
    exit(0);
}
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Edit Region Settings</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="region_settings">Region Settings</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <!-- Error/Success Messages -->
    <?php if (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } unset($_SESSION['error']); ?>
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } unset($_SESSION['success']); ?>

    <style>
        .form-control {
            color: black;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            width: 100%;
        }
        .form-control:focus {
            border-color: #f7951d;
            outline: none;
            box-shadow: 0 0 5px rgba(247, 149, 29, 0.3);
        }
        select.form-control {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        .card-body .row {
            margin-bottom: 10px;
        }
        .card-body label {
            font-weight: 500;
        }
    </style>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Region</h5>
            <form action="codes/region_settings.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <label for="country">Country</label>
                        <select class="form-control" name="country" required>
                            <option value="" disabled>Select Country</option>
                            <?php
                            include('inc/countries.php');
                            if (isset($countries) && !empty($countries)) {
                                foreach ($countries as $country) {
                                    $selected = ($country == $region['country']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($country) . '" ' . $selected . '>' . htmlspecialchars($country) . '</option>';
                                }
                            } else {
                                echo '<option value="" disabled>No countries available</option>';
                                error_log("edit-region.php - Countries array not set or empty");
                                $_SESSION['error'] = "Country list unavailable. Please contact support.";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="currency">Currency</label>
                        <input type="text" class="form-control" name="currency" value="<?= htmlspecialchars($region['currency']) ?>" placeholder="e.g., NGN or USDT" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="crypto">Crypto Payment</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="crypto" id="crypto" value="1" <?= $region['crypto'] == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="crypto">Enable Crypto Deposit/Transfer</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="Channel">Channel</label>
                        <input type="text" class="form-control" name="Channel" value="<?= htmlspecialchars($region['Channel']) ?>" placeholder="e.g., Bank or Blockchain Network" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="Channel_name">Channel Name</label>
                        <input type="text" class="form-control" name="Channel_name" value="<?= htmlspecialchars($region['Channel_name']) ?>" placeholder="e.g., Account Name or Wallet Address" required>
                    </div>
                    <div class="col-md-6">
                        <label for="Channel_number">Channel Number</label>
                        <input type="text" class="form-control" name="Channel_number" value="<?= htmlspecialchars($region['Channel_number']) ?>" placeholder="e.g., Account Number or Recipient Address" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="chnl_value">Channel Value</label>
                        <input type="text" class="form-control" name="chnl_value" value="<?= htmlspecialchars($region['chnl_value']) ?>" placeholder="e.g., Opay or Ethereum">
                    </div>
                    <div class="col-md-6">
                        <label for="chnl_name_value">Channel Name Value</label>
                        <input type="text" class="form-control" name="chnl_name_value" value="<?= htmlspecialchars($region['chnl_name_value']) ?>" placeholder="e.g., John Doe or Wallet Address">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="chnl_number_value">Channel Number Value</label>
                        <input type="text" class="form-control" name="chnl_number_value" value="<?= htmlspecialchars($region['chnl_number_value']) ?>" placeholder="e.g., 1234567890 or 0x1234567890abcdef">
                    </div>
                    <div class="col-md-6">
                        <label for="payment_amount">Payment Amount</label>
                        <input type="number" step="0.01" class="form-control" name="payment_amount" value="<?= htmlspecialchars($region['payment_amount']) ?>" placeholder="e.g., 100.00" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="rate">Rate</label>
                        <input type="number" step="0.01" class="form-control" name="rate" value="<?= htmlspecialchars($region['rate']) ?>" placeholder="e.g., 1.00" required>
                    </div>
                </div>
                <input type="hidden" name="region_id" value="<?= $region['id'] ?>">
                <input type="hidden" name="auth_id" value="<?= $_SESSION['id'] ?>">
                <div class="mt-3">
                    <button type="submit" class="btn btn-secondary" name="update_region">Update Region</button>
                    <a href="region_settings.php" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</main><!-- End #main -->

<?php include('inc/footer.php'); ?>
