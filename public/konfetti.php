<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Strona z Konfetti i Popupem</title>
  <style>
    body, html {
      margin: 0; padding: 0; height: 100%;
      font-family: Arial, sans-serif;
      overflow-x: hidden;
    }

    /* Canvas konfetti na pełnym ekranie, nad wszystkim */
    #confetti-canvas {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      pointer-events: none;
      z-index: 10000;
    }

    /* Przykładowy div Twojej istniejącej strony */
    #moja-strona {
      padding: 40px;
      background: #eef2f7;
      min-height: 100vh;
      box-sizing: border-box;
      text-align: center;
      z-index: 1;
      position: relative;
    }

    /* Popup */
    #popup-wrapper-konfetti {
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0, 0, 0, 0.3);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 10001; /* nad canvasem */
      animation: fadeIn 0.4s ease-in;
    }

    .popup-content-konfetti {
      background: white;
      border-radius: 16px;
      width: 400px;
      max-width: 90%;
      padding: 20px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
      text-align: center;
    }

    .popup-content-konfetti img {
      width: 100px;
      height: auto;
      border-radius: 50%;
      margin-bottom: 15px;
    }

    .konfetti-close-btn {
      margin-top: 15px;
      padding: 12px 25px;
      background-color: #ff5c5c;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }

    @keyframes fadeIn {
      from { opacity: 0 }
      to { opacity: 1 }
    }
  </style>
</head>
<body>

  <!-- Przykładowy div Twojej istniejącej strony -->
  <div id="moja-strona">
    <h1>Witaj na mojej stronie!</h1>
    <p>To jest przykładowa zawartość Twojej obecnej strony.</p>
  </div>

  <!-- Popup -->
  <div id="popup-wrapper-konfetti">
    <div class="popup-content-konfetti">
      <img src="https://i.imgur.com/OUzWj2Y.png" alt="Wybuchająca buzia z konfetti" />
      <h2>Gratulacje!</h2>
      <p>Oto Twoja zawartość w popupie.</p>
      <button class="konfetti-close-btn" id="popup-close-btn">Zakończ</button>
    </div>
  </div>

  <!-- Canvas do konfetti -->
  <canvas id="confetti-canvas"></canvas>

  <!-- Biblioteka konfetti -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

  <script>
    const confettiCanvas = document.getElementById('confetti-canvas');
    confettiCanvas.width = window.innerWidth;
    confettiCanvas.height = window.innerHeight;

    let konfettiAnimationId = null;

    function startKonfettiLoop() {
      konfettiAnimationId = requestAnimationFrame(startKonfettiLoop);
      confetti({
        particleCount: 7,
        startVelocity: 30,
        spread: 360,
        ticks: 60,
        origin: {
          x: Math.random(),
          y: Math.random() - 0.2
        }
      });
    }

    function stopKonfetti() {
      cancelAnimationFrame(konfettiAnimationId);
    }

    // Startujemy konfetti i pokazujemy popup od razu
    window.onload = () => {
      startKonfettiLoop();
      document.getElementById('popup-wrapper-konfetti').style.display = 'flex';
    };

    // Obsługa przycisku "Zakończ" - ukrywa popup i zatrzymuje konfetti
    document.getElementById('popup-close-btn').addEventListener('click', () => {
      document.getElementById('popup-wrapper-konfetti').style.display = 'none';
      stopKonfetti();
    });

    // Aktualizacja rozmiaru canvasa przy resize
    window.addEventListener('resize', () => {
      confettiCanvas.width = window.innerWidth;
      confettiCanvas.height = window.innerHeight;
    });
  </script>

</body>
</html>