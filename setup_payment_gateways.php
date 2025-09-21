<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<pre>";

// --- Define Bangladeshi Payment Gateways ---
$new_gateways = [
    [
        'name' => 'bKash Payment (API)',
        'slug' => 'bkash_api',
        'type' => 'mfs',
        'settings_json' => json_encode([
            'app_key' => '',
            'app_secret' => '',
            'username' => '',
            'password' => '',
            'mode' => '0011' // Checkout URL mode
        ])
    ],
    [
        'name' => 'bKash Payment (Personal)',
        'slug' => 'bkash_personal',
        'type' => 'mfs_personal',
        'settings_json' => json_encode([
            'personal_number' => '',
            'instructions' => 'Send money to this number and enter transaction ID.'
        ])
    ],
    [
        'name' => 'Nagad Payment (API)',
        'slug' => 'nagad_api',
        'type' => 'mfs',
        'settings_json' => json_encode([
            'merchant_id' => '',
            'merchant_number' => '',
            'private_key' => '-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY_HERE\n-----END PRIVATE KEY-----',
            'public_key' => '-----BEGIN PUBLIC KEY-----NAGAD_PUBLIC_KEY_HERE\n-----END PUBLIC KEY-----'
        ])
    ],
    [
        'name' => 'Nagad Payment (Personal)',
        'slug' => 'nagad_personal',
        'type' => 'mfs_personal',
        'settings_json' => json_encode([
            'personal_number' => '',
            'instructions' => 'Send money to this number and enter transaction ID.'
        ])
    ],
    [
        'name' => 'SSLCOMMERZ',
        'slug' => 'sslcommerz',
        'type' => 'aggregator',
        'settings_json' => json_encode([
            'store_id' => '',
            'store_password' => '',
            'is_live' => '0' // 0 for sandbox, 1 for live
        ])
    ],
    [
        'name' => 'Rocket Mobile Banking (API)',
        'slug' => 'rocket_api',
        'type' => 'mfs',
        'settings_json' => json_encode([
            'agent_number' => '',
            'pin' => ''
        ])
    ],
    [
        'name' => 'Rocket Mobile Banking (Personal)',
        'slug' => 'rocket_personal',
        'type' => 'mfs_personal',
        'settings_json' => json_encode([
            'personal_number' => '',
            'instructions' => 'Send money to this number and enter transaction ID.'
        ])
    ],
    [
        'name' => 'Upay Mobile Banking (API)',
        'slug' => 'upay_api',
        'type' => 'mfs',
        'settings_json' => json_encode([
            'merchant_id' => '',
            'api_key' => ''
        ])
    ],
    [
        'name' => 'Upay Mobile Banking (Personal)',
        'slug' => 'upay_personal',
        'type' => 'mfs_personal',
        'settings_json' => json_encode([
            'personal_number' => '',
            'instructions' => 'Send money to this number and enter transaction ID.'
        ])
    ],
    [
        'name' => 'Bank Cards (Visa, MC, Amex)',
        'slug' => 'bank_cards',
        'type' => 'card',
        'settings_json' => json_encode([
            'merchant_id' => '',
            'api_key' => '',
            'currency' => 'BDT'
        ])
    ],
    [
        'name' => 'Internet Banking',
        'slug' => 'internet_banking',
        'type' => 'ibanking',
        'settings_json' => json_encode([
            'bank_list' => '' // e.g., 'DBBL,BRAC,CityBank'
        ])
    ],
    [
        'name' => 'Cash on Delivery (COD)',
        'slug' => 'cod',
        'type' => 'offline',
        'settings_json' => json_encode([])
    ],
    [
        'name' => 'Payoneer',
        'slug' => 'payoneer',
        'type' => 'international',
        'settings_json' => json_encode([
            'api_username' => '',
            'api_password' => ''
        ])
    ],
    [
        'name' => 'PayPal',
        'slug' => 'paypal',
        'type' => 'international',
        'settings_json' => json_encode([
            'client_id' => '',
            'client_secret' => '',
            'mode' => 'sandbox' // or 'live'
        ])
    ]
];

$count = 0;

foreach ($new_gateways as $gateway) {
    // Check if gateway already exists
    $stmt = $conn->prepare("SELECT id FROM payment_gateways WHERE slug = ?");
    $stmt->bind_param("s", $gateway['slug']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Insert if not found
        $insert_stmt = $conn->prepare("INSERT INTO payment_gateways (name, type, slug, settings_json, is_active) VALUES (?, ?, ?, ?, 0)");
        $insert_stmt->bind_param("ssss", $gateway['name'], $gateway['type'], $gateway['slug'], $gateway['settings_json']);
        if ($insert_stmt->execute()) {
            echo "Successfully added '" . htmlspecialchars($gateway['name']) . "' gateway.\n";
            $count++;
        } else {
            echo "Error adding '" . htmlspecialchars($gateway['name']) . "': " . $insert_stmt->error . "\n";
        }
        $insert_stmt->close();
    } else {
        echo "Gateway '" . htmlspecialchars($gateway['name']) . "' already exists. Skipping.\n";
    }
    $stmt->close();
}

if ($count > 0) {
    echo "\nSetup complete. $count new payment gateway(s) added.\n";
} else {
    echo "\nNo new payment gateways were added.\n";
}

echo "You can now delete this script.";
echo "</pre>";

?>