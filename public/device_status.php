<!DOCTYPE html>
<html>
<head>
    <title>Device Monitor</title>
<style>
    body { font-family: Arial, sans-serif; }
    .device { 
        padding: 10px; 
        margin: 5px; 
        border-radius: 5px; 
        background-color: #f0f0f0; 
        color: black; 
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .status-time {
        font-weight: bold;
    }
    .online {
        color: green;
    }
    .offline {
        color: red;
    }
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
                const statusClass = diff <= 10 ? 'online' : 'offline';

                const div = document.createElement('div');
                div.className = 'device';

                const info = document.createElement('div');
                info.innerHTML = `<strong>${dev.name}</strong> (state: ${dev.state})`;

                const time = document.createElement('div');
                time.className = 'status-time ' + statusClass;
                time.innerText = `Last seen: ${new Date(dev.last_seen * 1000).toLocaleTimeString()} (${diff}s ago)`;

                div.appendChild(info);
                div.appendChild(time);

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