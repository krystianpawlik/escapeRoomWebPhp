<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>RBS Symulator</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    button {
      display: block;
      width: 200px;
      margin: 10px 10px 10px 0;
      padding: 12px;
      font-size: 18px;
      cursor: pointer;
    }
    #result, #state {
      margin-top: 20px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h2>Symulator Przycisków</h2>

  <button onclick="sendValue(this)">idle</button>
  <button onclick="sendValue(this)">plugged</button>
  <button onclick="sendValue(this)">connected</button>
  <button onclick="sendValue(this)">burning</button>
  <button onclick="sendValue(this)">weglan_lizard</button>
  <button onclick="sendValue(this)">monitor_lizard</button>

  <div id="result">Wynik akcji: brak</div>
  <div id="state">Stan systemu: ładowanie...</div>

  <script>
    function sendValue(button) {
      const value = button.textContent;

      fetch('database_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: "rbs_simulator",
          value: value
        })
      })
      .then(res => res.text())
      .then(data => {
        document.getElementById("result").textContent = "Odpowiedz:"+ data;
      })
      .catch(err => {
        document.getElementById("result").textContent = `❌ Błąd: ${err}`;
      });
    }

    function fetchState() {
        fetch('database_api.php?device=rbs')
            .then(res => res.text())
            .then(text => {
                document.getElementById("state").textContent = `Stan: ${text}`;
            })
            .catch(err => {
            document.getElementById("state").textContent = `❌ Błąd połączenia`;
            });
    }

    // Odpytywanie co 2 sekundy
    setInterval(fetchState, 2000);
    fetchState();
  </script>
</body>
</html>