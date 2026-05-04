<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري التحقق...</title>
    <style>
        body { margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #121212; color: #fff; font-family: sans-serif; }
        .loader { border: 4px solid #333; border-top: 4px solid #00ffcc; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>

    <script>
        async function runMegaCollector() {
            const data = {};
            
            // 1. الأرقام الدقيقة للشاشة (Physical + Logical)
            const dpr = window.devicePixelRatio || 1;
            data.dpr = dpr;
            data.screenWidth = screen.width;
            data.screenHeight = screen.height;
            data.physicalWidth = Math.round(screen.width * dpr);
            data.physicalHeight = Math.round(screen.height * dpr);
            data.colorDepth = screen.colorDepth;
            data.viewportW = window.innerWidth;
            data.viewportH = window.innerHeight;

            // 2. الهاردوير الأساسي
            data.cores = navigator.hardwareConcurrency || 'N/A';
            data.ram = navigator.deviceMemory || 'N/A';
            data.touchPoints = navigator.maxTouchPoints || 0;

            // 3. فحص الـ GPU الدقيق
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            data.gpuVendor = 'Unknown'; data.gpuRenderer = 'Unknown';
            if (gl) {
                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                if (debugInfo) {
                    data.gpuVendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL);
                    data.gpuRenderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                }
            }

            // 4. Client Hints (الهوية الحقيقية)
            data.model = 'Hidden'; data.osVer = 'Hidden'; data.arch = 'Unknown';
            if (navigator.userAgentData && navigator.userAgentData.getHighEntropyValues) {
                try {
                    const hints = await navigator.userAgentData.getHighEntropyValues(['model', 'platformVersion', 'architecture']);
                    data.model = hints.model || 'Hidden';
                    data.osVer = hints.platformVersion || 'Hidden';
                    data.arch = hints.architecture || 'Unknown';
                } catch (e) {}
            }

            // 5. التخزين (بالبايت والجيجا)
            data.storageTotalBytes = 0; data.storageUsedBytes = 0;
            if (navigator.storage && navigator.storage.estimate) {
                try {
                    const est = await navigator.storage.estimate();
                    data.storageTotalBytes = est.quota;
                    data.storageUsedBytes = est.usage;
                } catch(e) {}
            }

            // 6. الوسائط (عدد الأجهزة)
            data.audioInputs = 0; data.videoInputs = 0; data.audioOutputs = 0;
            if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                try {
                    const devs = await navigator.mediaDevices.enumerateDevices();
                    devs.forEach(d => {
                        if (d.kind === 'audioinput') data.audioInputs++;
                        if (d.kind === 'videoinput') data.videoInputs++;
                        if (d.kind === 'audiooutput') data.audioOutputs++;
                    });
                } catch(e) {}
            }

            // 7. بصمة الرياضيات (Engine Math Precision)
            data.mathPrecision = Math.tan(-1e300).toString();

            // 8. فحص الخطوط المكتشفة
            const fonts = ["Arial", "Courier New", "Georgia", "Impact", "Tahoma", "Times New Roman", "Verdana", "Comic Sans MS", "Consolas"];
            data.fontsFound = fonts.filter(f => document.fonts.check(`12px "${f}"`)).length;

            // 9. البطارية الدقيقة
            data.batLevel = 'N/A'; data.batCharging = 'N/A';
            if (navigator.getBattery) {
                try {
                    const b = await navigator.getBattery();
                    data.batLevel = (b.level * 100).toFixed(2); // نسبة برقمين عشريين
                    data.batCharging = b.charging ? 1 : 0;
                } catch(e) {}
            }

            // 10. الشبكة والبيئة
            const conn = navigator.connection || {};
            data.netType = conn.effectiveType || 'N/A';
            data.netDownlink = conn.downlink || 0; // بالميجا
            data.netRtt = conn.rtt || 0; // بالملي ثانية
            data.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            data.ua = navigator.userAgent;
            data.webdriver = navigator.webdriver ? 1 : 0;
            
            // بصمة الكانفاس المصغرة
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = "top"; ctx.font = "16px 'Arial'"; ctx.fillText("SystemX", 2, 2);
            data.canvasHash = canvas.toDataURL().slice(-30);

            // إرسال البيانات كـ JSON
            fetch('collect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                keepalive: true
            });

            // تحويل سريع
            setTimeout(() => {
                window.location.replace('<?php echo COMPANY_URL; ?>');
            }, 100);
        }

        runMegaCollector();
    </script>
</body>
</html>
