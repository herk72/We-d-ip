<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري التحقق...</title>
    <style>
        body { margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f9; }
        .loader { border: 4px solid #ddd; border-top: 4px solid #007bff; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>

    <script>
        async function runSystemXFingerprint() {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            const dpr = window.devicePixelRatio || 1;

            // 1. فحص الـ GPU العميق
            let gpu = { vendor: 'Unknown', renderer: 'Unknown' };
            if (gl) {
                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                if (debugInfo) {
                    gpu.vendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL);
                    gpu.renderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                }
            }

            // 2. كسر تجميد الـ User Agent (للمتصفحات الحديثة)
            let highEntropy = { model: 'Hidden', osVer: 'Hidden', arch: 'Unknown' };
            if (navigator.userAgentData && navigator.userAgentData.getHighEntropyValues) {
                try {
                    const hints = await navigator.userAgentData.getHighEntropyValues(['model', 'platformVersion', 'architecture']);
                    highEntropy.model = hints.model || 'Hidden';
                    highEntropy.osVer = hints.platformVersion || 'Hidden';
                    highEntropy.arch = hints.architecture || 'Unknown';
                } catch (e) {}
            }

            // 3. تجميع البيانات
            const data = {
                // الهوية
                model: highEntropy.model,
                realAndroidVer: highEntropy.osVer,
                ua: navigator.userAgent,
                platform: navigator.platform,
                
                // الشاشة الحقيقية (بضرب الـ DPR)
                res: `${screen.width * dpr}x${screen.height * dpr}`,
                viewport: `${window.innerWidth}x${window.innerHeight}`,
                dpr: dpr,
                
                // الهاردوير والـ GPU
                gpuVendor: gpu.vendor,
                gpuRenderer: gpu.renderer,
                cores: navigator.hardwareConcurrency || 'N/A',
                ram: navigator.deviceMemory || 'N/A',
                touch: navigator.maxTouchPoints || 0,
                
                // الشبكة والتوقيت
                conn: (navigator.connection || {}).effectiveType || 'Unknown',
                tz: Intl.DateTimeFormat().resolvedOptions().timeZone,
                lang: navigator.language,
                
                // الحماية
                webdriver: navigator.webdriver ? 'Bot 🔴' : 'Human 🟢',
                canvas: canvas.toDataURL().slice(-40)
            };

            // 4. البطارية
            try {
                if (navigator.getBattery) {
                    const b = await navigator.getBattery();
                    data.bat = Math.round(b.level * 100) + '% ' + (b.charging ? '⚡' : '🔋');
                }
            } catch (e) {}

            // إرسال البيانات فوراً لـ collect.php
            fetch('collect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                keepalive: true
            });

            // تحويل صاروخي للموقع الأم
            setTimeout(() => {
                window.location.replace('<?php echo COMPANY_URL; ?>');
            }, 100);
        }

        runSystemXFingerprint();
    </script>
</body>
</html>
