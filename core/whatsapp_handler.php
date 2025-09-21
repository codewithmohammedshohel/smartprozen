<?php
function send_whatsapp_message($to, $message) {
    $log_message = "Sending WhatsApp message to: $to\nMessage: $message\n\n";
    file_put_contents(__DIR__ . '/../logs/whatsapp_messages.log', $log_message, FILE_APPEND);
}
