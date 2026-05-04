<?php
require_once 'config.php';
require_once 'telegram.php';

$json = file_get_contents('php://input');
$d = json_decode($json, true);

if ($d) {
    // جلب الـ IP الحقيقي لبيئة Railway
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];
    
    date_default_timezone_set('Africa/Cairo');
    $time = date('h:i:s A');

    // الرسالة منظمة سكشنات للتحليل السريع
    $msg = "🚀 <b>تقرير فحص System-X الجديد</b>\n";
    $msg .= "━━━━━━━━━━━━━━\n";
    
    $msg .= "👤 <b>الهوية الحقيقية:</b>\n";
    $msg .= "• الموديل: <code>{$d['model']}</code>\n";
    $msg .= "• إصدار النظام: <code>{$d['realAndroidVer']}</code>\n";
    $msg .= "• الـ IP الحقيقي: <code>$ip</code>\n\n";

    $msg .= "🖼️ <b>الشاشة والعرض:</b>\n";
    $msg .= "• الدقة (Physical): <b>{$d['res']}</b>\n";
    $msg .= "• الـ DPR: {$d['dpr']} | لمس: {$d['touch']}\n\n";

    $msg .= "🎮 <b>كارت الشاشة (GPU):</b>\n";
    $msg .= "• Renderer: <code>{$d['gpuRenderer']}</code>\n";
    $msg .= "• Vendor: <code>{$d['gpuVendor']}</code>\n\n";

    $msg .= "⚙️ <b>المواصفات والبيئة:</b>\n";
    $msg .= "• معالج: {$d['cores']} | رام: {$d['ram']} GB\n";
    $msg .= "• بطارية: {$d['bat']}\n";
    $msg .= "• شبكة: {$d['conn']} | لغة: {$d['lang']}\n\n";

    $msg .= "🔍 <b>التتبع وكشف البوت:</b>\n";
    $msg .= "• بصمة الكانفاس: <code>..." . substr($d['canvas'], -15) . "</code>\n";
    $msg .= "• WebDriver: {$d['webdriver']}\n";
    $msg .= "• UserAgent:\n<code>{$d['ua']}</code>\n\n";

    $msg .= "—\n";
    $msg .= "👨‍💻 <a href='https://t.me/" . DEV_USERNAME . "'>" . DEV_USERNAME . "</a>";

    sendTelegramMessage($msg);
}
