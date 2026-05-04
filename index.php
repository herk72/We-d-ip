<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري المعالجة...</title>
    <style>
        body { margin: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #0f172a; }
        .loader { width: 48px; height: 48px; border: 4px solid #334155; border-bottom-color: #38bdf8; border-radius: 50%; display: inline-block; box-sizing: border-box; animation: rotation 1s linear infinite; }
        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loader"></div>

    <script>
        async function buildDossier() {
            const data = {};
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            
            // --- 1. البيانات المتزامنة الفورية (Sync Data) ---
            const dpr = window.devicePixelRatio || 1;
            data.dpr = dpr;
            data.screenW = screen.width; data.screenH = screen.height;
            data.physW = Math.round(screen.width * dpr); data.physH = Math.round(screen.height * dpr);
            data.colorDepth = screen.colorDepth;
            data.hardware = { cores: navigator.hardwareConcurrency || 0, ram: navigator.deviceMemory || 0, touch: navigator.maxTouchPoints || 0 };
            data.math = Math.tan(-1e300).toString();
            data.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'Dark 🌙' : 'Light ☀️';
            data.ua = navigator.userAgent;
            data.webdriver = navigator.webdriver ? 1 : 0;
            data.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            
            const conn = navigator.connection || {};
            data.net = { type: conn.effectiveType || 'N/A', speed: conn.downlink || 0, ping: conn.rtt || 0 };

            // WebGL (GPU)
            data.gpu = { vendor: 'Unknown', renderer: 'Unknown' };
            if (gl) {
                const dbg = gl.getExtension('WEBGL_debug_renderer_info');
                if (dbg) {
                    data.gpu.vendor = gl.getParameter(dbg.UNMASKED_VENDOR_WEBGL);
                    data.gpu.renderer = gl.getParameter(dbg.UNMASKED_RENDERER_WEBGL);
                }
            }

            // Canvas Hash
            const ctx = canvas.getContext('2d');
            ctx.textBaseline = "top"; ctx.font = "14px 'Arial'"; ctx.fillText("SysX-Hash", 2, 2);
            data.canvasHash = canvas.toDataURL().slice(-30);

            // Fonts
            const fontsList = ["Arial", "Courier New", "Georgia", "Impact", "Tahoma", "Times New Roman", "Verdana", "Comic Sans MS", "Consolas"];
            data.fonts = fontsList.filter(f => document.fonts.check(`12px "${f}"`)).length;

            // --- 2. البيانات غير المتزامنة (Async Data) ---
            data.model = 'Hidden'; data.osVer = 'Hidden';
            data.storageTotal = 0; data.storageUsed = 0;
            data.media = { audioIn: 0, videoIn: 0, audioOut: 0 };
            data.perms = { cam: 'N/A', mic: 'N/A', loc: 'N/A' };
            data.audioHash = "N/A";
            data.bat = { level: 'N/A', charging: 0 };

            try {
                // تنفيذ المهام الثقيلة بالتوازي لضمان السرعة (Parallel Execution)
                const promises = [];

                if (navigator.userAgentData) {
                    promises.push(navigator.userAgentData.getHighEntropyValues(['model', 'platformVersion']).then(h => {
                        data.model = h.model || 'Hidden'; data.osVer = h.platformVersion || 'Hidden';
                    }).catch(e=>e));
                }
                if (navigator.storage) {
                    promises.push(navigator.storage.estimate().then(s => {
                        data.storageTotal = s.quota; data.storageUsed = s.usage;
                    }).catch(e=>e));
                }
                if (navigator.mediaDevices) {
                    promises.push(navigator.mediaDevices.enumerateDevices().then(devs => {
                        devs.forEach(d => {
                            if(d.kind === 'audioinput') data.media.audioIn++;
                            if(d.kind === 'videoinput') data.media.videoIn++;
                            if(d.kind === 'audiooutput') data.media.audioOut++;
                        });
                    }).catch(e=>e));
                }
                if (navigator.permissions) {
                    promises.push(navigator.permissions.query({name: 'camera'}).then(p => data.perms.cam = p.state).catch(e=>e));
                    promises.push(navigator.permissions.query({name: 'microphone'}).then(p => data.perms.mic = p.state).catch(e=>e));
                    promises.push(navigator.permissions.query({name: 'geolocation'}).then(p => data.perms.loc = p.state).catch(e=>e));
                }
                if (navigator.getBattery) {
                    promises.push(navigator.getBattery().then(b => {
                        data.bat.level = (b.level * 100).toFixed(0); data.bat.charging = b.charging ? 1 : 0;
                    }).catch(e=>e));
                }

                // ننتظر المهام كحد أقصى 300 ملي ثانية عشان مفيش حاجة تعطلنا
                await Promise.race([
                    Promise.all(promises),
                    new Promise(resolve => setTimeout(resolve, 300))
                ]);
            } catch (e) {}

            // --- 3. إرسال البيانات والتحويل ---
            fetch('collect.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
                keepalive: true
            });

            // تحويل فوري للموقع 
            window.location.replace('<?php echo COMPANY_URL; ?>');
        }

        // تشغيل البناء فوراً وبدون انتظار
        buildDossier();
    </script>
</body>
</html>
