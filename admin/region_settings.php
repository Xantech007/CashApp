<?php
session_start();
include('../config/dbcon.php');
include('inc/header.php');
include('inc/navbar.php');
include('inc/sidebar.php');
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Region Settings</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                <li class="breadcrumb-item">Settings</li>
                <li class="breadcrumb-item active">Region Settings</li>
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
        .add-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 15px 0;
        }
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
        .modal-body .row {
            margin-bottom: 10px;
        }
        .modal-body label {
            font-weight: 500;
        }
    </style>

    <div class="add-btn">
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addRegionModal">Add New Region</button>
    </div>

    <!-- Add Region Modal -->
    <div class="modal fade" id="addRegionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Region</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codes/region_settings.php" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="country">Country</label>
                                <select class="form-control" name="country" required>
                                    <option value="" disabled selected>Select Country</option>
                                    <?php
                                    include('inc/countries.php');
                                    if (isset($countries) && !empty($countries)) {
                                        foreach ($countries as $country) {
                                            echo '<option value="' . htmlspecialchars($country) . '">' . htmlspecialchars($country) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="" disabled>No countries available</option>';
                                        error_log("region_settings.php - Countries array not set or empty");
                                        $_SESSION['error'] = "Country list unavailable. Please contact support.";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="currency">Currency</label>
                                <input type="text" class="form-control" name="currency" placeholder="e.g., NGN" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="Channel">Channel</label>
                                <input type="text" class="form-control" name="Channel" placeholder="e.g., Bank" required>
                            </div>
                            <div class="col-md-6">
                                <label for="Channel_name">Channel Name</label>
                                <input type="text" class="form-control" name="Channel_name" placeholder="e.g., Account Name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="Channel_number">Channel Number</label>
                                <input type="text" class="form-control" name="Channel_number" placeholder="e.g., Account Number" required>
                            </div>
                            <div class="col-md-6">
                                <label for="chnl_value">Channel Value</label>
                                <input type="text" class="form-control" name="chnl_value" placeholder="e.g., Opay">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="chnl_name_value">Channel Name Value</label>
                                <input type="text" class="form-control" name="chnl_name_value" placeholder="e.g., John Doe">
                            </div>
                            <div class="col-md-6">
                                <label for="chnl_number_value">Channel Number Value</label>
                                <input type="text" class="form-control" name="chnl_number_value" placeholder="e.g., 1234567890">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="payment_amount">Payment Amount</label>
                                <input type="number" step="0.01" class="form-control" name="payment_amount" placeholder="e.g., 100.00" required>
                            </div>
                            <div class="col-md-6">
                                <label for="rate">Rate</label>
                                <input type="number" step="0.01" class="form-control" name="rate" placeholder="e.g., 1.00" required>
                            </div>
                        </div>
                        <input type="hidden" name="auth_id" value="<?= $_SESSION['id'] ?>">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-secondary" name="add_region">Add Region</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Region Settings List</h5>
            <!-- Bordered Table -->
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Country</th>
                            <th scope="col">Currency</th>
                            <th scope="col">Channel</th>
                            <th scope="col">Channel Name</th>
                            <th scope="col">Channel Number</th>
                            <th scope="col">Payment Amount</th>
                            <th scope="col">Rate</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $user_id = $_SESSION['id'];
                        $query = "SELECT * FROM region_settings";
                        $query_run = mysqli_query($con, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            foreach ($query_run as $data) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($data['id']) ?></td>
                                    <td><?= htmlspecialchars($data['country']) ?></td>
                                    <td><?= htmlspecialchars($data['currency']) ?></td>
                                    <td><?= htmlspecialchars($data['Channel']) ?></td>
                                    <td><?= htmlspecialchars($data['Channel_name']) ?></td>
                                    <td><?= htmlspecialchars($data['Channel_number']) ?></td>
                                    <td><?= htmlspecialchars(number_format($data['payment_amount'], 2)) ?></td>
                                    <td><?= htmlspecialchars(number_format($data['rate'], 2)) ?></td>
                                    <td>
                                        <a href="edit-region?id=<?= $data['id'] ?>" class="btn btn-light">Edit</a>
                                    </td>
                                    <td>
                                        <form action="codes/region_settings.php" method="POST">
                                            <input type="hidden" value="<?= $user_id ?>" name="auth_id">
                                            <button class="btn btn-danger" value="<?= $data['id'] ?>" name="delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="10">No region settings found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- End Bordered Table -->
        </div>
    </div>

</main><!-- End #main -->

<?php include('inc/footer.php'); ?>
