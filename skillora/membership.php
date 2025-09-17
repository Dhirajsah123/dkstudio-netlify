<?php
// skillora/membership.php
$page_title = 'Become a Member';
require_once __DIR__ . '/includes/header.php';

// --- Data for the form ---
// In a real application, this might come from the database
$memberships = [
    '1' => ['name' => 'Bronze', 'price' => 300], // Using ID from DB as key
    '2' => ['name' => 'Silver', 'price' => 500],
    '3'   => ['name' => 'Gold', 'price' => 700],
];

$payment_methods = [
    'esewa' => ['name' => 'eSewa', 'qr_code' => 'esewa_qr.png'],
    'khalti' => ['name' => 'Khalti', 'qr_code' => 'khalti_qr.png'],
    'imepay' => ['name' => 'IME Pay', 'qr_code' => 'imepay_qr.png'],
    'bank' => ['name' => 'Bank Transfer', 'qr_code' => 'bank_qr.png'],
];

// Get the selected tier from the URL, if present
$tier_map = ['bronze' => '1', 'silver' => '2', 'gold' => '3'];
$selected_tier_id = '1'; // Default to Bronze
if (isset($_GET['tier']) && isset($tier_map[$_GET['tier']])) {
    $selected_tier_id = $tier_map[$_GET['tier']];
}

?>

<section class="membership-form-section">
    <h2 class="section-title">Membership Checkout</h2>
    <p class="section-subtitle" style="text-align:center; margin-top:-2rem; margin-bottom: 2rem;">Complete the steps below to join Skillora.</p>

    <form action="<?php echo $base_url; ?>/handle_payment.php" method="POST" enctype="multipart/form-data" id="membership-form" class="styled-form">

        <!-- Step 1: Select Membership -->
        <fieldset>
            <legend>Step 1: Confirm Your Membership Plan</legend>
            <div class="form-group radio-group">
                <?php foreach ($memberships as $id => $tier): ?>
                    <label class="radio-label">
                        <input type="radio" name="selected_membership_id" value="<?php echo $id; ?>" <?php if ($id === $selected_tier_id) echo 'checked'; ?>>
                        <?php echo sanitize_output($tier['name']); ?> - <?php echo sanitize_output($tier['price']); ?> NPR/year
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <!-- Step 2: Your Details -->
        <fieldset>
            <legend>Step 2: Enter Your Details</legend>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="referrer_code">Referrer Code (Optional)</label>
                <input type="text" id="referrer_code" name="referrer_code">
            </div>
        </fieldset>

        <!-- Step 3: Make Payment -->
        <fieldset>
            <legend>Step 3: Choose Payment Method & Pay</legend>
            <div class="form-group radio-group">
                <p>Select a payment method to reveal the QR code for payment.</p>
                <?php foreach ($payment_methods as $key => $method): ?>
                    <label class="radio-label">
                        <input type="radio" name="payment_method" value="<?php echo $key; ?>" data-qr="<?php echo $base_url . '/assets/images/' . $method['qr_code']; ?>">
                        <?php echo sanitize_output($method['name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <div id="qr-code-display" style="text-align: center; margin-top: 1rem; display: none;">
                <p><strong>Scan the QR code below to pay.</strong></p>
                <img src="" alt="Payment QR Code" id="qr-code-image" style="max-width: 250px; border: 1px solid #ddd; padding: 10px; border-radius: var(--border-radius);">
            </div>
        </fieldset>

        <!-- Step 4: Upload Receipt -->
        <fieldset>
            <legend>Step 4: Upload Your Payment Receipt</legend>
            <div class="form-group">
                <label for="receipt">Receipt Screenshot/File</label>
                <input type="file" id="receipt" name="receipt" accept="image/jpeg,image/png,application/pdf" required>
                <small>Max file size: 5MB. Allowed formats: JPG, PNG, PDF.</small>
            </div>
        </fieldset>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem;">Submit Application</button>
    </form>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
