<?php
require_once 'config.php';

function sendTelegramMessage($message) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // مهلة 5 ثواني للإرسال
    curl_exec($ch);
    curl_close($ch);
}
?>
