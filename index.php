<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري التحويل...</title>
    <style>
        body { margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f9; font-family: sans-serif; }
        .loader { border: 4px solid #ddd; border-top: 4px solid #007bff; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>

    <script>
        async function inspectAndRedirect() {
            // تجميع البيانات الأساسية
            let data = {
                screen: `${window.screen.width}x${window.screen.height}`,
                cores: navigator.hardwareConcurrency || 'غير معروف',
                ram: navigator.deviceMemory || 'غير معروف',
                tz: Intl.DateTimeFormat().resolvedOptions().timeZone || 'غير معروف',
                lang: navigator.language || 'غير معروف',
                userAgent: navigator.userAgent || 'غير معروف',
                webdriver: navigator.webdriver ? 'أيوة بنسبة كبيرة 🔴' : 'لأ طبيعي 🟢'
            };

            // فحص البطارية (بيميز جداً بين البوت والمستخدم الحقيقي)
            try {
                if ('getBattery' in navigator) {
                    let bat = await navigator.getBattery();
                    data.battery = Math.round(bat.level * 100) + '%';
                    data.charging = bat.charging ? 'أيوة ⚡' : 'لأ 🔋';
                } else {
                    data.battery = 'غير مدعوم في المتصفح ده';
                    data.charging = 'غير مدعوم';
                }
            } catch (e) {
                data.battery = 'خطأ في القراءة';
                data.charging = '-';
            }

            // فحص كارت الشاشة (البوتات غالباً بتستخدم SwiftShader أو معالجة برمجية)
            try {
                let canvas = document.createElement('canvas');
                let gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
                if (gl) {
                    let debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                    data.gpu = debugInfo ? gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) : 'غير معروف';
                } else {
                    data.gpu = 'WebGL غير مدعوم';
                }
            } catch (e) {
                data.gpu = 'خطأ في قراءة الكارت';
            }

            // إرسال البيانات في الخلفية
            try {
                await fetch('collect.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                    keepalive: true // عشان يكمل إرسال حتى لو حصل Redirect
                });
            } catch (e) {
                console.error("Error logging.");
            }

            // التحويل الفوري للشركة الأم
            window.location.replace('<?php echo COMPANY_URL; ?>');
        }

        // تشغيل العملية أول ما الصفحة تفتح
        inspectAndRedirect();
    </script>
</body>
</html>
