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
        async function getComprehensiveFingerprint() {
            const canvas = document.createElement('canvas');
            
            // 1. بصمة الكانفاس السريعة
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = "top";
            ctx.font = "14px 'Arial'";
            ctx.fillText("Hello System-X 😃", 2, 2);
            const canvasHash = canvas.toDataURL().slice(-50, -10);

            // 2. فحص كارت الشاشة (GPU) التفصيلي
            let gpuVendor = 'غير معروف', gpuRenderer = 'غير معروف';
            try {
                const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
                if (gl) {
                    const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                    if (debugInfo) {
                        gpuVendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL);
                        gpuRenderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                    }
                }
            } catch(e) {}

            // 3. تحديد نوع الجهاز
            const ua = navigator.userAgent;
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua);
            const deviceType = isMobile ? 'Mobile/Tablet 📱' : 'Desktop 💻';

            // 4. الشبكة
            const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;

            // 5. تجميع الداتا
            let info = {
                // المتصفح والنظام
                userAgent: ua,
                deviceType: deviceType,
                platform: navigator.platform,
                vendor: navigator.vendor,
                primaryLang: navigator.language,
                allLangs: navigator.languages ? navigator.languages.join(', ') : 'غير معروف',
                
                // الشاشة
                realScreen: `${window.screen.width}x${window.screen.height}`,
                viewport: `${window.innerWidth}x${window.innerHeight}`,
                pixelRatio: window.devicePixelRatio,
                colorDepth: window.screen.colorDepth,
                
                // الهاردوير
                cores: navigator.hardwareConcurrency || 'غير معروف',
                ram: navigator.deviceMemory || 'غير معروف',
                gpuVendor: gpuVendor,
                gpuRenderer: gpuRenderer,
                touchPoints: navigator.maxTouchPoints || 0,
                
                // الشبكة
                connType: conn ? conn.effectiveType : 'غير معروف',
                downlink: conn ? conn.downlink + ' Mbps' : 'غير معروف',
                rtt: conn ? conn.rtt + ' ms' : 'غير معروف',
                
                // التوقيت
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                tzOffset: `UTC ${(new Date().getTimezoneOffset() / -60) >= 0 ? '+' : ''}${new Date().getTimezoneOffset() / -60}`,
                
                // الحماية
                canvasHash: canvasHash,
                webdriver: navigator.webdriver ? 'أيوة (مشبوه) 🔴' : 'لأ 🟢',
                cookiesEnabled: navigator.cookieEnabled ? 'شغال' : 'معطل'
            };

            // 6. البطارية التفصيلية
            try {
                if (navigator.getBattery) {
                    const bat = await navigator.getBattery();
                    info.batteryLevel = Math.round(bat.level * 100) + '%';
                    info.isCharging = bat.charging ? 'جاري الشحن ⚡' : 'على البطارية 🔋';
                    info.chargingTime = bat.chargingTime === Infinity ? 'مجهول' : bat.chargingTime + ' ثانية';
                    info.dischargingTime = bat.dischargingTime === Infinity ? 'مجهول' : bat.dischargingTime + ' ثانية';
                } else {
                    info.batteryLevel = 'غير مدعوم';
                    info.isCharging = '-';
                }
            } catch (e) {
                info.batteryLevel = 'خطأ';
            }

            // إرسال البيانات
            fetch('collect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(info),
                keepalive: true
            });

            // تحويل فوري
            window.location.replace('<?php echo COMPANY_URL; ?>');
        }

        getComprehensiveFingerprint();
    </script>
</body>
</html>
