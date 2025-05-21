<!DOCTYPE html>
<html>
<head>
    <title>Device Monitor</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .device { padding: 10px; margin: 5px; border-radius: 5px; color: white; }
        .online { background-color: green; }
        .offline { background-color: red; }
    </style>
</head>
<body>
    <h1>Device Status</h1>
    <div id="device-list">Loading...</div>

    <script>
        async function fetchStatus() {
            try {
                const res = await fetch('device_status_api.php');
                const devices = await res.json();

                const now = Math.floor(Date.now() / 1000);
                const container = document.getElementById('device-list');
                container.innerHTML = '';

                devices.forEach(dev => {
                    const diff = now - dev.last_seen;
                    const status = diff <= 10 ? 'online' : 'offline';

                    const div = document.createElement('div');
                    div.className = 'device ' + status;
                    div.innerHTML = `<strong>${dev.name}</strong> - Last seen: ${new Date(dev.last_seen * 1000).toLocaleTimeString()} (${diff}s ago)`;
                    container.appendChild(div);
                });
            } catch (e) {
                document.getElementById('device-list').innerText = 'Error loading status...';
            }
        }

        setInterval(fetchStatus, 5000);
        fetchStatus(); // initial call
    </script>
</body>
</html>