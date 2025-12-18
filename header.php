<?php
// header.php
// Include after session_start() and db.php are loaded

$current_cashier_id = $_SESSION['cashier_id'] ?? 0;

$sql = "
    SELECT 
        c.Number AS cashier_number,
        c.Name AS cashier_name,
        c.StoreID,
        s.Name AS store_name,
        s.StoreCode,
        s.Address1,
        s.Address2,
        s.City,
        s.Country,
        s.PhoneNumber
    FROM cashier c
    LEFT JOIN store s ON c.StoreID = s.ID
    WHERE c.ID = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_cashier_id);
$stmt->execute();
$result = $stmt->get_result();
$info = $result->fetch_assoc();
$stmt->close();

// Fallbacks
$store_name   = $info['store_name'] ?? 'Unknown Store';
$store_code   = $info['StoreCode'] ?? '';
$address1     = $info['Address1'] ?? '';
$address2     = $info['Address2'] ?? '';
$city         = $info['City'] ?? '';
$country      = $info['Country'] ?? '';
$phone_raw    = $info['PhoneNumber'] ?? '';

// Format phone nicely
function format_phone($phone) {
    $phone = preg_replace('/\D/', '', $phone);
    if (strlen($phone) == 10 && $phone[0] == '0') {
        return '+254 ' . substr($phone, 1, 3) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
    }
    return $phone ?: 'Not set';
}
$phone = $phone_raw ? format_phone($phone_raw) : 'Not set';

$full_address = trim("$address1 $address2, $city, $country");
$full_address = ($full_address && $full_address !== ', ') ? $full_address : 'Address not configured';
?>

<header class="d-flex align-items-center justify-content-between px-4 py-2">
    <!-- Left: Vortex ERP -->
    <div>
        <h5 class="mb-0 text-white fw-bold">
            <i class="fas fa-cubes me-2"></i>Vortex ERP
        </h5>
    </div>

    <!-- Center: Dynamic Title -->
    <div class="text-center flex-grow-1">
        <h6 class="mb-0 text-white fw-semibold" id="headerTitle">
            Sales Order Processing - Sales Return
        </h6>
    </div>

    <!-- Right: Store + Cashier + Logout -->
    <div class="text-end">
        <div class="d-flex align-items-center gap-4">

            <!-- Store Info (Name + Code + Phone on same line) -->
            <div class="text-white text-end">
                <div class="d-flex align-items-center gap-2 justify-content-end flex-wrap">
                    <strong class="fw-bold fs-6">
                        <?= htmlspecialchars($store_name) ?>
                    </strong>
                    <?php if ($store_code): ?>
                        <span class="text-white-50 fs-7">(<?= htmlspecialchars($store_code) ?>)</span>
                    <?php endif; ?>
                    <small class="text-white opacity-75 ms-2">
                        <i class="fas fa-phone-alt fa-xs"></i>
                        <?= htmlspecialchars($phone) ?>
                    </small>
                </div>
                <small class="d-block text-white opacity-90 mt-1">
                    <?= htmlspecialchars($full_address) ?>
                </small>
            </div>

            <!-- Separator -->
            <div class="vr text-white opacity-30 mx-2"></div>

            <!-- Cashier -->
            <div class="text-end">
                <span class="d-block text-white fw-semibold">
                    <i class="fas fa-user-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['cashier_name'] ?? 'Guest') ?>
                </span>
                <small class="text-white opacity-80">
                    Cashier #<?= htmlspecialchars($_SESSION['cashier_number'] ?? '??') ?>
                </small>
            </div>

            <!-- Logout -->
            <a href="logout.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-none d-md-inline">Logout</span>
            </a>
        </div>
    </div>
</header>

<style>
    @media (max-width: 992px) {
        header .d-flex.gap-4 { gap: 1rem !important; }
        header .fs-6 { font-size: 0.95rem !important; }
        header small { font-size: 0.75rem; }
        header .justify-content-end { justify-content: flex-start !important; }
    }
</style>