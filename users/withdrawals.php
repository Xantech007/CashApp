<?php
session_start();
include('../config/dbcon.php');
include('inc/header.php');
include('inc/navbar.php');
?>

<main id="main" class="main">

    <div class="pagetitle">
        <?php
        $email = mysqli_real_escape_string($con, $_SESSION['email']);

        // Fetch user data
        $query = "SELECT balance, verify, message, country, verify_time FROM users WHERE email='$email' LIMIT 1";
        $query_run = mysqli_query($con, $query);
        
        if ($query_run && mysqli_num_rows($query_run) > 0) {
            $row = mysqli_fetch_array($query_run);
            $balance = $row['balance'];
            $verify = $row['verify'] ?? 0;
            $message = $row['message'] ?? '';
            $user_country = $row['country'];
            $verify_time = $row['verify_time'];

            // Auto-reset verify=1 after 5h15m
            if ($verify == 1 && !empty($verify_time)) {
                $current_time = new DateTime('now', new DateTimeZone('Africa/Lagos'));
                $verify_time_dt = new DateTime($verify_time, new DateTimeZone('Africa/Lagos'));
                $interval = $current_time->diff($verify_time_dt);
                $total_minutes_passed = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

                if ($total_minutes_passed >= 315) {
                    $update_query = "UPDATE users SET verify = 0 WHERE email = ?";
                    $stmt = mysqli_prepare($con, $update_query);
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    if (mysqli_stmt_execute($stmt)) {
                        $verify = 0;
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        } else {
            $_SESSION['error'] = "User not found.";
            header("Location: ../signin.php");
            exit(0);
        }

        // Fetch payment settings
        $payment_query = "SELECT crypto, Channel, Channel_name, Channel_number, currency, 
                                 alt_channel, alt_ch_name, alt_ch_number, alt_currency 
                          FROM region_settings 
                          WHERE country = '" . mysqli_real_escape_string($con, $user_country) . "' 
                          AND Channel IS NOT NULL 
                          LIMIT 1";
        $payment_query_run = mysqli_query($con, $payment_query);
        $channel_label = 'Bank';
        $channel_name_label = 'Account Name';
        $channel_number_label = 'Account Number';
        $currency = '$';

        if ($payment_query_run && mysqli_num_rows($payment_query_run) > 0) {
            $payment_data = mysqli_fetch_assoc($payment_query_run);
            if ($payment_data['crypto'] == 1) {
                $channel_label = $payment_data['alt_channel'] ?? 'Crypto Channel';
                $channel_name_label = $payment_data['alt_ch_name'] ?? 'Crypto Name';
                $channel_number_label = $payment_data['alt_ch_number'] ?? 'Crypto Address';
                $currency = $payment_data['alt_currency'] ?? '$';
            } else {
                $channel_label = $payment_data['Channel'] ?? 'Bank';
                $channel_name_label = $payment_data['Channel_name'] ?? 'Account Name';
                $channel_number_label = $payment_data['Channel_number'] ?? 'Account Number';
                $currency = $payment_data['currency'] ?? '$';
            }
        }
        ?>
        <h1>Available Balance: USD<?= number_format($balance, 2) ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index">Home</a></li>
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item active">Withdrawals</li>
            </ol>
        </nav>
    </div>

    <!-- User Message -->
    <?php if (!empty(trim($message))) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top: 20px;">
            <i class="bi bi-exclamation-triangle me-2"></i><strong><?= htmlspecialchars($message) ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <!-- BLOCK: verify = 3 → Show Error & Disable Withdrawal -->
    <?php if ($verify == 3) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top: 15px;">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>An error occurred while converting to your local currency.</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <!-- Error Modal -->
    <?php if (isset($_SESSION['error'])) { ?>
        <div class="modal fade show" id="errorModal" tabindex="-1" style="display: block;" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Error</h5></div>
                    <div class="modal-body"><?= htmlspecialchars($_SESSION['error']) ?></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.reload();">Ok</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    <?php unset($_SESSION['error']); } ?>

    <!-- Success Modal -->
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="modal fade show" id="successModal" tabindex="-1" style="display: block;" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Success</h5></div>
                    <div class="modal-body"><?= htmlspecialchars($_SESSION['success']) ?></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.reload();">Ok</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    <?php unset($_SESSION['success']); } ?>

    <style>
        .form1 { padding: 10px; width: 300px; background: white; display: flex; justify-content: space-between; opacity: 0.85; border-radius: 10px; }
        input { border: none; outline: none; }
        #button { border: none; color: #012970; background: #f7f7f7; border-radius: 5px; }
        input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        @media (max-width: 500px) { .form { width: 100%; margin: auto; } }
        .action-buttons { display: flex; justify-content: space-between; margin: 15px 0; }
        .btn-verify { background: #ffc107; flex: 1; padding: 12px; font-size: 16px; font-weight: bold; border: none; border-radius: 5px; cursor: pointer; margin: 0 5px; text-align: center; text-decoration: none; color: white; }
        .btn-withdraw.disabled { opacity: 0.6; cursor: not-allowed; pointer-events: none; }
    </style>

    <!-- Withdrawal Card -->
    <div class="card" style="margin-top:20px">
        <div class="card-body">
            <h5 class="card-title">Withdrawal Request</h5>
            <p>Fill in amount, <?= htmlspecialchars($channel_label) ?>, <?= htmlspecialchars($channel_name_label) ?>, and <?= htmlspecialchars($channel_number_label) ?>, then submit.</p>

            <!-- Withdrawal Button: Disabled if verify = 3 -->
            <button type="button" 
                    class="btn btn-secondary <?= $verify == 3 ? 'disabled' : '' ?>" 
                    data-bs-toggle="modal" 
                    data-bs-target="#verticalycentered"
                    id="withdrawBtn"
                    <?= $verify == 3 ? 'disabled' : '' ?>>
                Request Withdrawal
            </button>

            <!-- Withdrawal Modal -->
            <div class="modal fade" id="verticalycentered" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Minimum withdrawal is $50</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="../codes/withdrawals.php" method="POST" class="F" id="withdrawForm">
                                <div class="error"></div>
                                <div class="inputbox">
                                    <input class="input" type="number" name="amount" required min="50" step="0.01" />
                                    <span>Amount In USD</span>
                                </div>
                                <div class="inputbox">
                                    <input class="input" type="text" name="channel" required />
                                    <span><?= htmlspecialchars($channel_label) ?></span>
                                </div>
                                <div class="inputbox">
                                    <input class="input" type="text" name="channel_name" required />
                                    <span><?= htmlspecialchars($channel_name_label) ?></span>
                                </div>
                                <div class="inputbox">
                                    <input class="input" type="text" name="channel_number" required />
                                    <span><?= htmlspecialchars($channel_number_label) ?></span>
                                </div>
                                <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>">
                                <input type="hidden" name="balance" value="<?= htmlspecialchars($balance) ?>">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="withdraw" class="btn btn-secondary">Submit Request</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #form, .F { margin: auto; width: 80%; }
        .inputbox { position: relative; width: 100%; margin-top: 20px; }
        .inputbox input { width: 100%; padding: 5px 0; font-size: 12px; border: none; outline: none; background: transparent; border-bottom: 2px solid #ccc; }
        .inputbox span { position: absolute; left: 0; padding: 5px 0; font-size: 12px; color: #aaa; pointer-events: none; transition: 0.4s; }
        .inputbox input:focus ~ span, .inputbox input:valid ~ span { color: #0dcefd; font-size: 10px; transform: translateY(-20px); }
    </style>

    <!-- Withdrawal History -->
    <div class="pagetitle"><h1>Withdrawal History</h1></div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th><?= htmlspecialchars($channel_label) ?></th>
                            <th><?= htmlspecialchars($channel_name_label) ?></th>
                            <th><?= htmlspecialchars($channel_number_label) ?></th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $history_query = "SELECT id, amount, channel, channel_name, channel_number, status, created_at 
                                          FROM withdrawals WHERE email='$email' ORDER BY created_at DESC";
                        $history_run = mysqli_query($con, $history_query);
                        if (mysqli_num_rows($history_run) > 0) {
                            foreach ($history_run as $data) { ?>
                                <tr>
                                    <td><?= $currency ?><?= number_format($data['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($data['channel']) ?></td>
                                    <td><?= htmlspecialchars($data['channel_name']) ?></td>
                                    <td><?= htmlspecialchars($data['channel_number']) ?></td>
                                    <td><span class="badge <?= $data['status'] == 0 ? 'bg-warning' : 'bg-success' ?> text-light">
                                        <?= $data['status'] == 0 ? 'Pending' : 'Completed' ?>
                                    </span></td>
                                    <td><?= date('d-M-Y', strtotime($data['created_at'])) ?></td>
                                    <td>
                                        <form action="../codes/withdrawals.php" method="POST" style="display:inline;">
                                            <button class="btn btn-light btn-sm" name="delete" value="<?= $data['id'] ?>">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr><td colspan="7">No withdrawals found.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Verify Account Button: HIDDEN when verify = 2 OR 3 -->
    <?php if ($verify != 2 && $verify != 3) { ?>
        <div class="action-buttons">
            <a href="verify.php" class="btn btn-verify">Verify Account</a>
        </div>
    <?php } ?>

</main>

<!-- JavaScript: Block form & button if verify = 3 -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($verify == 3): ?>
            const form = document.getElementById('withdrawForm');
            const btn = document.getElementById('withdrawBtn');

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    alert("An error occurred while converting to your local currency.");
                });
            }

            if (btn) {
                btn.disabled = true;
                btn.classList.add('disabled');
                btn.title = "Withdrawal blocked due to currency conversion error.";
            }
        <?php endif; ?>
    });
</script>

<?php include('inc/footer.php'); ?>
</html>
