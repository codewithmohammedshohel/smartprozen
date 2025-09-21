<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_payments')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gateway_id'])) {
    $gateway_id = $_POST['gateway_id'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $settings = $_POST['settings'];

    $settings_json = json_encode($settings);

    $stmt = $conn->prepare("UPDATE payment_gateways SET is_active = ?, settings_json = ? WHERE id = ?");
    $stmt->bind_param("isi", $is_active, $settings_json, $gateway_id);
    $stmt->execute();

    log_activity('admin', $_SESSION['admin_id'], 'gateway_update', "Updated gateway ID: $gateway_id");
    $_SESSION['success_message'] = "Payment gateway updated successfully.";
    header("Location: manage_gateways.php");
    exit;
}

$gateways = $conn->query("SELECT * FROM payment_gateways ORDER BY id");

require_once '../includes/admin_header.php';
?>
<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Payment Gateways</h1>
        </div>
        <p>Enable and configure the payment methods you want to offer at checkout.</p>
        
        <?php show_flash_messages(); ?>

        <?php while($gateway = $gateways->fetch_assoc()):
            $settings = json_decode($gateway['settings_json'], true);
        ?>
        <div class="form-container gateway-form">
            <form action="manage_gateways.php" method="POST">
                <input type="hidden" name="gateway_id" value="<?php echo $gateway['id']; ?>">
                <h3>
                    <?php echo htmlspecialchars($gateway['name']); ?>
                    (<?php echo ucfirst($gateway['type']); ?>)
                </h3>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" value="1" <?php if($gateway['is_active']) echo 'checked'; ?>>
                    <span class="slider"></span>
                    Enable Gateway
                </label>
                
                <fieldset>
                    <legend>Settings</legend>
                    <?php foreach ($settings as $key => $value): ?>
                        <label for="settings_<?php echo $key; ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?>:</label>
                        <input type="text" id="settings_<?php echo $key; ?>" name="settings[<?php echo $key; ?>]" value="<?php echo htmlspecialchars($value); ?>">
                    <?php endforeach; ?>
                </fieldset>
                
                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>