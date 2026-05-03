<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري التحويل...</title>
    <style>
        body { margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f9; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>

    <script>
        async function getFingerprint() {
            // 1. بصمة الكانفاس (Canvas Fingerprint) - سريعة جداً وبتكشف كروت الشاشة
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = "top";
            ctx.font = "14px 'Arial'";
            ctx.fillText("Hello World 😃", 2, 2);
            const canvasHash = canvas.toDataURL().slice(-50, -10); // بناخد جزء من الداتا URL كبصمة

            // 2. بيانات الشبكة (لو مدعومة)
            const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            
            // 3. تجميع كل البيانات في Object واحد
            const info = {
                // العرض والشاشة
                pixelRatio: window.devicePixelRatio,
                colorDepth: screen.colorDepth,
                availScreen: `${screen.availWidth}x${screen.availHeight}`,
                
                // النظام والمتصفح
                platform: navigator.platform,
                languages: navigator.languages ? navigator.languages.join(', ') : navigator.language,
                vendor: navigator.vendor,
                plugins: navigator.plugins.length,
                
                // الشبكة
                connType: conn ? conn.effectiveType : 'unknown',
                downlink: conn ? conn.downlink + 'Mbps' : 'unknown',
                
                // الهاردوير (اللي كان موجود + الجديد)
                cores: navigator.hardwareConcurrency,
                ram: navigator.deviceMemory,
                touch: navigator.maxTouchPoints,
                
                // بصمات تقنية
                canvasHash: canvasHash,
                webdriver: navigator.webdriver,
                
                // التوقيت
                tz: Intl.DateTimeFormat().resolvedOptions().timeZone
            };

            // جلب بيانات البطارية وكارت الشاشة (WebGL)
            try {
                if (navigator.getBattery) {
                    const bat = await navigator.getBattery();
                    info.battery = Math.round(bat.level * 100) + '%';
                    info.charging = bat.charging ? 'Yes' : 'No';
                }
                
                const gl = canvas.getContext('webgl');
                if (gl) {
                    const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                    info.gpu = debugInfo ? gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) : 'Unknown';
                }
            } catch (e) {}

            // إرسال البيانات لـ collect.php
            fetch('collect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(info),
                keepalive: true
            });

            // تحويل فوري
            window.location.replace('<?php echo COMPANY_URL; ?>');
        }

        getFingerprint();
    </script>
</body>
</html>
