<?php

/**
 * Sends an email using PHP's mail() function.
 *
 * @param string $to The recipient's email address.
 * @param string $subject The subject of the email.
 * @param string $message The body of the email.
 * @param string $headers Additional headers (e.g., From, Content-Type).
 * @return bool True on success, false on failure.
 */
function send_email($to, $subject, $message, $headers = '') {
    // Basic validation
    if (empty($to) || empty($subject) || empty($message)) {
        error_log("Attempted to send email with missing parameters: To: $to, Subject: $subject");
        return false;
    }

    // Default headers if not provided
    if (empty($headers)) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        // You might want to set a default 'From' address from your config
        // $headers .= 'From: Your App Name <no-reply@yourdomain.com>' . "\r\n";
    }

    // Attempt to send the email
    if (mail($to, $subject, $message, $headers)) {
        error_log("Email sent successfully to: $to, Subject: $subject");
        return true;
    } else {
        error_log("Failed to send email to: $to, Subject: $subject");
        return false;
    }
}

/**
 * Sends an email using a predefined template.
 *
 * @param string $template_name The name of the email template (e.g., 'order_completed').
 * @param string $to The recipient's email address.
 * @param array $data An associative array of data to populate the template (e.g., ['customer_name' => 'John Doe', 'order_id' => 123]).
 * @return bool True on success, false on failure.
 */
function send_email_template($template_name, $to, $data = []) {
    $subject = '';
    $message_body = '';

    // Default sender information (can be moved to config.php)
    $from_email = 'no-reply@smartprozen.com'; // Replace with your actual sender email
    $from_name = 'SmartProZen'; // Replace with your actual sender name

    $headers = "From: {$from_name} <{$from_email}>\r\n";
    $headers .= "Reply-To: {$from_email}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    switch ($template_name) {
        case 'order_completed':
            $customer_name = htmlspecialchars($data['customer_name'] ?? 'Customer');
            $order_id = htmlspecialchars($data['order_id'] ?? 'N/A');
            $site_url = SITE_URL; // Assuming SITE_URL is defined in config.php

            $subject = "Your SmartProZen Order #{$order_id} is Completed!";
            $message_body = "
                <html>
                <head>
                    <title>Order Completed</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
                        .header { background-color: #007bff; color: #fff; padding: 10px 20px; text-align: center; border-radius: 5px 5px 0 0; }\n                        .content { padding: 20px; }\n                        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 20px; }\n                        .button { display: inline-block; padding: 10px 20px; margin-top: 15px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 5px; }\n                    </style>
                </head>
                <body>
                    <div class=\"container\">
                        <div class=\"header\">
                            <h2>Order Completed - SmartProZen</h2>
                        </div>
                        <div class=\"content\">
                            <p>Dear {$customer_name},</p>
                            <p>Your order <strong>#{$order_id}</strong> has been successfully completed!</p>
                            <p>You can now access your purchased digital products from your account dashboard.</p>
                            <p>Thank you for shopping with us!</p>
                            <p><a href=\"{$site_url}/user/downloads.php\" class=\"button\">View Downloads</a></p>
                        </div>
                        <div class=\"footer\">
                            <p>&copy; " . date("Y") . " SmartProZen. All rights reserved.</p>
                            <p><a href=\"{$site_url}\">Visit our store</a></p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            break;
        // Add more cases for other email templates as needed
        default:
            error_log("Unknown email template: {$template_name}");
            return false;
    }

    if (empty($subject) || empty($message_body)) {
        error_log("Failed to generate email content for template: {$template_name}");
        return false;
    }

    return send_email($to, $subject, $message_body, $headers);
}
