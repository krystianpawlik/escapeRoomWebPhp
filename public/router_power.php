<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>POST z JavaScript</title>
  <style>
    #button-container {
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 300px;
    }

    button {
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      background-color: #007BFF;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <h1>Power & Connector — Simulator</h1>
  <div id="button-container"></div>

  <div id="result">Wynik akcji: brak</div>
  <script>
    const actions = [
      { device: "power_connector", value: "power_ok" },
      { device: "power_connector", value: "power_nok" },
      { device: "power_connector", value: "alive" },
      { device: "power_connector", value: "idle" },
      { device: "power_connector", value: "reset" },
      { device: "router", value: "firewall_connected" },
      { device: "router", value: "firewall_disconnected" },
      { device: "router", value: "idle" },
      { device: "router", value: "alive" },
      { device: "router", value: "reset" },
      { device: "lamp", value: "reset" },
    ];

    const container = document.getElementById("button-container");

    actions.forEach(item => {
      const btn = document.createElement("button");
      btn.textContent = `${item.device}:${item.value}`;

      btn.addEventListener("click", () => {

        fetch('database_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                device: item.device,
                value: item.value
                })
            })
            .then(res => res.text())
            .then(data => {
                document.getElementById("result").textContent = item.device + " " +item.value + " Odpowiedz:" + data;
            })
            .catch(err => {
                document.getElementById("result").textContent = `❌ Błąd: ${err}`;
            });

      });

      container.appendChild(btn);
    });
  </script>
</body>
</html>
