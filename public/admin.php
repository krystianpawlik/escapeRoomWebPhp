<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tabbed External Pages - Preloaded</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
    .tabs {
      display: flex;
      background-color: #333;
    }
    .tab {
      padding: 14px 20px;
      cursor: pointer;
      color: white;
      background-color: #333;
      border: none;
      outline: none;
    }
    .tab:hover {
      background-color: #575757;
    }
    .tab.active {
      background-color: #007bff;
    }
    .iframe-container {
      width: 100%;
      height: 90vh;
      position: relative;
    }
    iframe {
      position: absolute;
      width: 100%;
      height: 100%;
      border: none;
      display: none;
    }
    iframe.active {
      display: block;
    }
  </style>
</head>
<body>

<div class="tabs">
  <button class="tab active" onclick="showTab('frame1', this)">Mailbox All</button>
  <button class="tab" onclick="showTab('frame2', this)">Box simulator</button>
  <button class="tab" onclick="showTab('frame3', this)">Device Status</button>
  <button class="tab" onclick="showTab('frame4', this)">Rbs simulator</button>
  <button class="tab" onclick="showTab('frame5', this)">Achivments</button>
</div>

<div class="iframe-container">
  <iframe id="frame1" class="active" src="database_manager.php"></iframe>
  <iframe id="frame2" src="box_simulator.php"></iframe>
  <iframe id="frame3" src="device_status.php"></iframe>
  <iframe id="frame4" src="rbs_simulator.php"></iframe>
  <iframe id="frame5" src="achivments.php"></iframe>
</div>

<script>
  function showTab(frameId, tabButton) {
    // Hide all iframes
    document.querySelectorAll('iframe').forEach(frame => {
      frame.classList.remove('active');
    });

    // Show selected iframe
    document.getElementById(frameId).classList.add('active');

    // Update active tab
    document.querySelectorAll('.tab').forEach(tab => {
      tab.classList.remove('active');
    });
    tabButton.classList.add('active');
  }
</script>

</body>
</html>