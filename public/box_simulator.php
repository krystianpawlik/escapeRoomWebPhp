<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Green Screen Interface</title>
  <style>
    body {
      font-family: monospace;
      background-color: #f0f0f0;
      padding: 20px;
    }
    .screen-box {
      background-color: yellow;
      padding: 20px;
      display: inline-block;
      border: 2px solid #ccc;
    }
    .green-screen {
      background-color: #003300;
      color: #00FF00;
      padding: 10px 20px;
      font-size: 18px;
      height: 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      border: 2px inset #006600;
    }
    .controls {
      margin-top: 20px;
    }
    .control-row {
      margin: 5px 0;
      display: flex;
      align-items: center;
    }
    .control-row button {
      padding: 5px 10px;
      margin-right: 10px;
    }
    .control-row input[type="text"] {
      padding: 5px;
      width: 200px;
    }
  </style>
</head>
<body>

<div class="screen-box">
  <div class="green-screen">
    <div id="line1">Welcome to the 16-bit world</div>
    <div id="line2">Enter your command below</div>
  </div>
</div>

<div class="controls" id="controls"></div>

<script>
  const buttonLabels = ['teamName XFT-Maintanance', 'Card2', 'Card3', 'Card4'];
  const controlsContainer = document.getElementById('controls');

  const updateScreen = (line1, line2) => {
    document.getElementById('line1').textContent = line1;
    document.getElementById('line2').textContent = line2;
  };

  buttonLabels.forEach(label => {
    const row = document.createElement('div');
    row.className = 'control-row';

    const button = document.createElement('button');
    button.textContent = label;

    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = `Enter ${label.toLowerCase()} value`;

    button.addEventListener('click', () => {
      const payload = {
        action: "box",
        value: label
      };

      fetch('../database_api.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
        updateScreen(data.line1 || '', data.line2 || '');
      })
      .catch(error => {
        updateScreen('Error:', error.message);
      });
    });

    row.appendChild(button);
    row.appendChild(input);
    controlsContainer.appendChild(row);
  });
</script>

</body>
</html>