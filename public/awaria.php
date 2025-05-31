<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Awaria Serwera - Quiz</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #111;
      color: #fff;
      text-align: center;
      padding: 40px;
    }

    #fire-gif {
      width: 300px;
      margin: 0 auto 30px;
      display: block;
      border-radius: 16px;
      box-shadow: 0 0 30px red;
      transition: all 0.5s ease;
    }

    #timer {
      font-size: 60px;
      margin-bottom: 30px;
    }

    .question {
      font-size: 24px;
      margin-bottom: 20px;
    }

    .answers button {
      display: block;
      margin: 10px auto;
      padding: 15px 30px;
      font-size: 18px;
      border: none;
      border-radius: 8px;
      background-color: #333;
      color: #fff;
      cursor: pointer;
      transition: background 0.3s;
      width: 60%;
    }

    .answers button:hover:not(:disabled) {
      background-color: #555;
    }

    .answers button:disabled {
      background-color: #777;
      cursor: not-allowed;
    }

    #result {
      font-size: 28px;
      margin-top: 30px;
    }
  </style>
</head>
<body>

  <img id="fire-gif" src="https://media.giphy.com/media/26ufdipQqU2lhNA4g/giphy.gif" alt="Płonący serwer">
  <div id="timer">30</div>
  <div class="question" id="question"></div>
  <div class="answers" id="answers"></div>
  <div id="result"></div>

  <script>
    const questions = [
      {
        question: "Co to jest JavaScript?",
        answers: ["System operacyjny", "Język programowania", "Edytor tekstu", "Baza danych"],
        correct: 1
      },
      {
        question: "Który język służy do stylowania stron?",
        answers: ["Python", "C++", "CSS", "SQL"],
        correct: 2
      },
      {
        question: "Co oznacza HTML?",
        answers: ["Hyper Trainer Markup Language", "Hyper Text Markup Language", "Home Tool Markup Language", "Hot Mail Language"],
        correct: 1
      }
    ];

    let timeLeft = 30;
    let currentQuestionIndex = 0;
    let timerInterval;
    let timeRanOut = false;
    let quizFinished = false;

    const timerEl = document.getElementById("timer");
    const questionEl = document.getElementById("question");
    const answersEl = document.getElementById("answers");
    const resultEl = document.getElementById("result");
    const gifEl = document.getElementById("fire-gif");

    function startTimer() {
      timerInterval = setInterval(() => {
        if (quizFinished) return;

        timeLeft--;
        timerEl.textContent = timeLeft;

        if (timeLeft <= 0) {
          clearInterval(timerInterval);
          timeRanOut = true;
          timerEl.textContent = "0";
          gifEl.src = "https://i.imgur.com/bXoJ7ZT.png"; // zwęglony komputer
          gifEl.style.boxShadow = "0 0 30px gray";
        }
      }, 1000);
    }

    function loadQuestion() {
      if (currentQuestionIndex >= questions.length) {
        endQuiz();
        return;
      }

      const q = questions[currentQuestionIndex];
      questionEl.textContent = q.question;
      answersEl.innerHTML = "";

      q.answers.forEach((answer, i) => {
        const btn = document.createElement("button");
        btn.textContent = answer;
        btn.onclick = () => checkAnswer(i === q.correct);
        answersEl.appendChild(btn);
      });
    }

    function checkAnswer(isCorrect) {
      if (quizFinished) return;

      if (isCorrect) {
        currentQuestionIndex++;
        loadQuestion();
      } else {
        blockButtons(5000);
      }
    }

    function blockButtons(ms) {
      const buttons = answersEl.querySelectorAll("button");
      buttons.forEach(btn => btn.disabled = true);
      setTimeout(() => {
        buttons.forEach(btn => btn.disabled = false);
      }, ms);
    }

    function endQuiz() {
        quizFinished = true;
        answersEl.innerHTML = "";
        questionEl.textContent = "";

        let message = "";
        let gif = "";

        if (!timeRanOut) {
            clearInterval(timerInterval);
            gif = "https://media.giphy.com/media/l0Exk8EUzSLsrErEQ/giphy.gif"; // sukces
            gifEl.style.boxShadow = "0 0 30px green";
            message = "🎉 Udało się odpowiedzieć na wszystkie pytania na czas!";
        } else {
            gif = "https://i.imgur.com/bXoJ7ZT.png"; // zwęglony komputer
            gifEl.style.boxShadow = "0 0 30px gray";
            message = "🧨 Udało się odpowiedzieć na wszystkie pytania,<br>ale jeden z elementów został zwęglony!";
        }

        gifEl.src = gif;

        resultEl.innerHTML = `
            <p>${message}</p>
            <button onclick="location.href='mailbox.php'" style="
            margin-top: 20px;
            padding: 12px 24px;
            font-size: 18px;
            border: none;
            border-radius: 10px;
            background-color: #06f;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
            ">📬 Powrót do mailboxa</button>
        `;
    }

    // Start
    loadQuestion();
    startTimer();
  </script>

</body>
</html>