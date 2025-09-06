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

// Fetch region settings data using prepared statement
$query = "SELECT * FROM region_settings WHERE id = ? LIMIT 1";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $region_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $region = $result->fetch_assoc();
} else {
    $_SESSION['error'] = "Region not found.";
    header("Location: region_settings.php");
    exit(0);
}
$stmt->close();
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Edit Region Settings</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="region_settings.php">Region Settings</a></li>
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
            <h5 class="card-title">Edit Region: <?= htmlspecialchars($region['country']) ?></h5>
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
                    <div class="col-md-3">
                        <label for="currency">Currency</label>
                        <input type="text" class="form-control" name="currency" value="<?= htmlspecialchars($region['currency']) ?>" placeholder="" required>
                    </div>
                    <div class="col-md-3">
                        <label for="alt_currency">Alt Currency</label>
                        <input type="text" class="form-control" name="alt_currency" value="<?= htmlspecialchars($region['alt_currency'] ?? '') ?>" placeholder="">
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
                        <input type="text" class="form-control" name="Channel" value="<?= htmlspecialchars($region['Channel']) ?>" placeholder="" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="alt_channel">Alt Channel</label>
                        <input type="text" class="form-control" name="alt_channel" value="<?= htmlspecialchars($region['alt_channel'] ?? '') ?>" placeholder="">
                    </div>
                    <div class="col-md-6">
                        <label for="Channel_name">Channel Name</label>
                        <input type="text" class="form-control" name="Channel_name" value="<?= htmlspecialchars($region['Channel_name']) ?>" placeholder="" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="alt_ch_name">Alt Channel Name</label>
                        <input type="text" class="form-control" name="alt_ch_name" value="<?= htmlspecialchars($region['alt_ch_name'] ?? '') ?>" placeholder="">
                    </div>
                    <div class="col-md-6">
                        <label for="Channel_number">Channel Number</label>
                        <input type="text" class="form-control" name="Channel_number" value="<?= htmlspecialchars($region['Channel_number']) ?>" placeholder="" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="alt_ch_number">Alt Channel Number</label>
                        <input type="text" class="form-control" name="alt_ch_number" value="<?= htmlspecialchars($region['alt_ch_number'] ?? '') ?>" placeholder="">
                    </div>
                    <div class="col-md-6">
                        <label for="chnl_value">Channel Value</label>
                        <input type="text" class="form-control" name="chnl_value" value="<?= htmlspecialchars($region['chnl_value'] ?? '') ?>" placeholder="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="chnl_name_value">Channel Name Value</label>
                        <input type="text" class="form-control" name="chnl_name_value" value="<?= htmlspecialchars($region['chnl_name_value'] ?? '') ?>" placeholder="">
                    </div>
                    <div class="col-md-6">
                        <label for="chnl_number_value">Channel Number Value</label>
                        <input type="text" class="form-control" name="chnl_number_value" value="<?= htmlspecialchars($region['chnl_number_value'] ?? '') ?>" placeholder="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="payment_amount">Payment Amount</label>
                        <input type="number" step="0.01" class="form-control" name="payment_amount" value="<?= htmlspecialchars($region['payment_amount']) ?>" placeholder="" required>
                    </div>
                    <div class="col-md-6">
                        <label for="rate">Rate</label>
                        <input type="number" step="0.01" class="form-control" name="rate" value="<?= htmlspecialchars($region['rate']) ?>" placeholder="" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="alt_rate">Alt Rate</label>
                        <input type="text" class="form-control" name="alt_rate" value="<?= htmlspecialchars($region['alt_rate'] ?? '') ?>" placeholder="">
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

<?php
$con->close();
include('inc/footer.php');
?>
