<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailbox</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; flex-direction: column; height: 100vh; background: #e3f2fd; }
        .header {
            text-align: left;
            font-size: 42px;
            font-weight: bold;
            padding: 20px;
            background: linear-gradient(135deg, #f0f8ff, #1976d2);
            color: #0d47a1;
            width: 100%;
            padding-left: 40px;
            letter-spacing: 3px;
            text-shadow: 3px 3px 6px rgba(255, 255, 255, 0.3),
                         -3px -3px 6px rgba(0, 0, 0, 0.3);
            border-bottom: 5px solid #1565c0;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }
        .content { display: flex; flex: 1; }
        .sidebar { width: 25%; background: #2196f3; padding: 15px; color: white; }
        .sidebar h2 { margin-bottom: 10px; }
        .topics { list-style: none; }
        .topics li { padding: 10px; cursor: pointer; border-bottom: 1px solid #64b5f6; }
        .topics li:hover, .topics li.active { background: #1976d2; }

        .mailbox { flex: 1; display: flex; flex-direction: column; padding: 20px; }
        .mail-list { flex: 1; overflow-y: auto; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); height: 48%; margin-bottom: 10px; }
        .email-details { flex: 1; display: flex; flex-direction: column; overflow-y: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); height: 50%; display: none; }
        .email-message { display: flex; align-items: center; padding: 10px; border-bottom: 1px solid #bbdefb; cursor: pointer; }
        .email-message:hover { background: #e3f2fd; }
        .email-message img { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; }
        
        .response-box { margin-top: auto; display: flex; gap: 10px; padding-top: 10px; }
        .response-box input { flex: 1; padding: 5px; border: 1px solid #ccc; border-radius: 5px; }
        .response-box button { padding: 5px 10px; background: #2196f3; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .response-box button:hover { background: #1976d2; }
        .response-label { background: #ccc; padding: 10px; border-radius: 5px; display: block; }
        
         /* .popup {
            
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 10px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }  */

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 2px solid black;
            box-shadow: 0px 0px 10px gray;
            text-align: center;
        }

        .popup button {
            display: block;
            width: 100%;
            margin: 5px 0;
            padding: 10px;
            cursor: pointer;
        }

        .popup .timer {
            display: none;
            font-weight: bold;
            font-size: 1.2em;
            color: #ff0000;
            animation: fadeIn 0.5s ease-in-out;
        }
        .disabled {
            opacity: 0.3;
            pointer-events: none;
        }

        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
        }

        .alerts-container {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .alert {
            background: red;
            color: white;
            font-size: 26px;
            font-weight: bold;
            padding: 25px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.4);
            opacity: 0.95;
            text-align: center;
            min-width: 320px;
        }

        .countdown {
            font-size: 18px;
            margin-top: 10px;
            color: white;
        }

        #newMailPopup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 320px;
            background-color: #fff;
            padding: 20px 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            font-family: Arial, sans-serif;
            text-align: center;
            z-index: 1000;
        }

        #overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        }

        #newMailPopup p {
        font-size: 18px;
        margin: 10px 0;
        }

        #newMailPopup button {
        margin-top: 10px;
        padding: 8px 16px;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        }

        #newMailPopup button:hover {
        background-color: #0056b3;
        }

        .restart-button {
        display: inline-block;
        width: 100px;          /* kwadratowy kształt */
        height: 100px;
        background-color: red;
        color: white;
        text-align: center;
        line-height: 100px;     /* centrowanie tekstu pionowo */
        text-decoration: none;  /* usuwa podkreślenie linku */
        font-weight: bold;
        border-radius: 8px;     /* lekko zaokrąglone rogi */
        transition: background-color 0.3s ease;
        }

        .restart-button:hover {
        background-color: darkred;
        }
        /* .popup img {
            width: 300px;
            height: auto;
        } */
    </style>
</head>
<body>
    <div class="header">CustomerWeb</div>
    <div class="content">
        <div class="sidebar">
            <h2>Topics</h2>
            <ul class="topics">
                <li class="active" onclick="loadEmails('General')">General</li>
                <!-- <li onclick="loadEmails('Announcements')">Announcements</li>
                <li onclick="loadEmails('Support')">Support</li>
                <li onclick="loadEmails('Feedback')">Feedback</li> -->
            </ul>
        </div>

        <div class="mailbox">
            <div class="mail-list"></div>
            <div class="email-details"></div>
        </div>
    </div>

    <div id="overlay"></div>
    <div id="newMailPopup">
        <p style="font-size: 20px;">You have new mail!</p>
        <button onclick="closeNewMailPopup()">Close</button>
    </div>

    <audio id="mailSound" preload="auto">
        <source src="mp3/mailnotyfication.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <template id="popupTemplate">
        <div class="popup">
            <p class="question-text"></p>
            <img src="https://media1.tenor.com/m/x8v1oNUOmg4AAAAd/rickroll-roll.gif" alt="Rickroll">
            <audio class="popup-audio" src="https://www.myinstants.com/media/sounds/rickroll.mp3" preload="auto" loop></audio>
            <p class="quiz-question">Quiz to close: <span class="question-text"></span></p>
            <p class="timer"></p>
            <div class="answers"></div>
        </div>
    </template>

    <template id="email-template">
        <div class="email-message">
            <img class="avatar" src="" alt="Avatar">
            <div>
                <h3 class="name"></h3>
                <div class="subject"></div>
            </div>
        </div>
    </template>

    <audio id="lampMusic" preload="auto" loop>
         <source src="https://incompetech.com/music/royalty-free/mp3-royaltyfree/Carefree.mp3" type="audio/mpeg">
        Twoja przeglądarka nie obsługuje odtwarzania audio.
    </audio>

    <script>

        const popupQuestions = [
            { question: "Was this response really correct?", answers: ["Yes", "No", "Not sure", "Show correct answer"], correct: "No" },
            { question: "What is the capital of France?", answers: ["Berlin", "Madrid", "Paris", "Rome"], correct: "Paris" },
            { question: "How many continents are there?", answers: ["5", "6", "7", "8"], correct: "7" },
            { question: "Which planet is known as the Red Planet?", answers: ["Earth", "Mars", "Venus", "Jupiter"], correct: "Mars" }
        ];

        //solutionType=TextBox or solutionType="external"
        // const emailsData = {
        //     "General": [
        //         { avatar: "https://i.pravatar.cc/40?img=1", name: "John Doe", email: "john@example.com", 
        //             subject: "Hello!", content: "Welcome to the mailbox system.", response: null, expectedResponse: "Hello!", solutionType: "TextBox" },
        //         { avatar: "https://i.pravatar.cc/40?img=2", name: "Jane Smith", email: "jane@example.com", 
        //             subject: "Meeting Reminder", content: "Don't forget the meeting at 3 PM.", response: null, expectedResponse: "Got it!", solutionType: "external" }
        //     ],
        //     "Announcements": [
        //         { avatar: "https://i.pravatar.cc/40?img=3", name: "Admin", email: "admin@example.com", 
        //             subject: "System Update", content: "A new update will be released tomorrow.", response: 'Achivment : <b/><img src="https://i.pravatar.cc/40?img=2" style="width:50px; border-radius:50%;">', 
        //             expectedResponse: "Thanks for the update!", solutionType: "external" }
        //     ]
        // };

        let emailsData = null; // Store the last fetched email data


        function showNewMailPopup() {
            document.getElementById('newMailPopup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('mailSound').play();
        }

        function closeNewMailPopup() {
            document.getElementById('newMailPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function thinkAgain(popupId) {
            const popup = document.getElementById(popupId);
            if (!popup) return;

            const buttons = popup.querySelectorAll("button");
            const timerDisplay = popup.querySelector(".timer");
            let countdown = 5;

            buttons.forEach(btn => btn.disabled = true);
            timerDisplay.style.display = "block";
            timerDisplay.textContent = `Try again in ${countdown}s`;

            const interval = setInterval(() => {
                countdown--;
                timerDisplay.textContent = `Try again in ${countdown}s`;

                if (countdown <= 0) {
                clearInterval(interval);
                timerDisplay.style.display = "none";
                buttons.forEach(btn => btn.disabled = false);
                }
            }, 1000);
        }

        function closePopup(popupId) {
            const popup = document.getElementById(popupId);
            if (popup) {
                const audio = popup.querySelector(".popup-audio");
                if (audio) {
                audio.pause();
                }
                popup.remove();
            }
        }

        function generatePopup() {
            const randomIndex = Math.floor(Math.random() * popupQuestions.length);
            const questionData = popupQuestions[randomIndex];

            const template = document.getElementById("popupTemplate");
            const popupClone = document.importNode(template.content, true);
            const popupElement = popupClone.querySelector(".popup");

            // Ustaw unikalne ID dla popupu
            const uniqueId = `popup-${Date.now()}`;
            popupElement.setAttribute("id", uniqueId);

            // Ustaw tekst pytania
            popupElement.querySelectorAll(".question-text").forEach(el => {
                el.textContent = questionData.question;
            });

            // Dodaj przyciski odpowiedzi
            const answersContainer = popupElement.querySelector(".answers");
            questionData.answers.forEach(answer => {
                const btn = document.createElement("button");
                btn.textContent = answer;
                btn.onclick = () => {
                if (answer === questionData.correct) {
                    closePopup(uniqueId);
                } else {
                    thinkAgain(uniqueId);
                }
                };
                answersContainer.appendChild(btn);
            });

            // Dodaj popup do dokumentu
            document.body.appendChild(popupElement);

            // Odtwórz dźwięk
            const audio = popupElement.querySelector(".popup-audio");
            if (audio) {
                audio.play();
            }
        }

        function loadEmails(topic) {
            const mailList = document.querySelector(".mail-list");
            const emailDetails = document.querySelector(".email-details");
            const topicList = document.querySelector(".topics");

            mailList.innerHTML = "";
            emailDetails.innerHTML = "";
            emailDetails.style.display = "none";

            // 1. Upewnij się, że wszystkie tematy z emailsData są w DOM
            const existingTopics = Array.from(document.querySelectorAll(".topics li")).map(li => li.textContent.trim());

            Object.keys(emailsData).forEach(dataTopic => {
                if (!existingTopics.includes(dataTopic)) {
                    const li = document.createElement("li");
                    li.textContent = dataTopic;
                    li.addEventListener("click", () => loadEmails(dataTopic));
                    topicList.appendChild(li);
                }
            });

            // 2. Ustaw "active" tylko na aktualnie wybranym temacie
            document.querySelectorAll(".topics li").forEach(li => {
                li.classList.toggle("active", li.textContent.trim() === topic);
            });

            // 3. Jeśli nie ma maili dla danego tematu, zakończ
            if (!emailsData[topic]) return;

            // 4. Załaduj maile dla wybranego tematu
            emailsData[topic].forEach(email => {
                const template = document.querySelector("#email-template").content.cloneNode(true);
                template.querySelector(".avatar").src = email.avatar;
                template.querySelector(".name").textContent = email.name;
                template.querySelector(".subject").textContent = email.subject;

                const emailElement = template.querySelector(".email-message");
                emailElement.addEventListener("click", () => showEmailDetails(email));

                mailList.appendChild(emailElement);
            });
        }

        function blockPageWithBigRedAlerts(messages, alertDuration = 4000) {
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            document.body.appendChild(overlay);

            const alertContainer = document.createElement('div');
            alertContainer.className = 'alerts-container';
            document.body.appendChild(alertContainer);

            //const alarmSound = document.getElementById('alarm-sound');

            let index = 0;

            function showNextAlert() {
                if (index >= messages.length) {
                document.body.removeChild(overlay);
                document.body.removeChild(alertContainer);
                return;
                }

                // Alarm!
                // alarmSound.currentTime = 0;
                // alarmSound.play().catch(e => console.warn("Autoplay blocked or no file loaded", e));

                const alertBox = document.createElement('div');
                alertBox.className = 'alert';
                alertBox.innerText = messages[index];

                const countdown = document.createElement('div');
                countdown.className = 'countdown';
                alertBox.appendChild(countdown);

                alertContainer.appendChild(alertBox);

                let timeLeft = alertDuration / 1000;

                const timer = setInterval(() => {
                countdown.textContent = `Pozostało: ${timeLeft--}s`;
                if (timeLeft < 0) {
                    clearInterval(timer);
                    alertBox.remove();
                    index++;
                    showNextAlert();
                }
                }, 1000);
            }

            showNextAlert();
        }

        function sendMailboxPost(emailId){
            const data = {
                action: "mailbox",
                value: emailId // możesz tu wstawić np. zmienną z dynamicznym ID
            };

            fetch("database_api.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json" // jeśli wysyłasz jako JSON
                },
                body: JSON.stringify(data)
            })
            .then(response => response.text()) // lub response.json() jeśli zwracasz JSON
            .then(result => {
                console.log("Odpowiedź serwera:", result);
            })
            .catch(error => {
                console.error("Błąd:", error);
            });
        }

        function sendResponse(emailId) {
            const responseInput = document.getElementById("responseInput");
            const responseText = responseInput.value.trim();
            const emailData = Object.values(emailsData).flat().find(e => e.id === emailId);
            if (emailData) {
                if(responseText == "alert!"){
                    //only temporary
                            // Użycie:
                    blockPageWithBigRedAlerts([
                        "⚠️ ALERT SYSTEMOWY: Naruszenie bezpieczeństwa!",
                        "❗ALERT: Błąd krytyczny systemu!",
                        "⛔ALERT: Zatrzymano wszystkie procesy.",
                        "✅ Proces zakończony. Można kontynuować."
                    ], 5000);
                }else if (responseText !== emailData.expectedResponse) {
                    // document.getElementById("popup").style.display = "block";
                    // document.getElementById("popupAudio").play();
                    generatePopup();
                } else{
                    //emailData.response = responseText;
                    //toDo send update to database
                    sendMailboxPost(emailId);
                    showEmailDetails(emailData);
                }
            }
        }


        function showEmailDetails(email) {
            const emailDetails = document.querySelector(".email-details");
            emailDetails.innerHTML = `
                <h2>${email.subject}</h2>
                <img src="${email.avatar}" style="width:50px; border-radius:50%;">
                <h3>${email.name} (${email.email})</h3>
                <div>${email.content}</div>
                ${email.response ? `<label class="response-label">${email.response}</label>` : `
                <div class="response-box">
                    <input type="text" placeholder="Type your response..." id="responseInput">
                    <button onclick="sendResponse(${email.id})">Respond</button>
                </div>`}
            `;
            emailDetails.style.display = "flex";

            // Add Enter eventListener
            const responseInput = document.getElementById("responseInput");
            if (responseInput) {
                responseInput.addEventListener("keydown", function(event) {
                    if (event.key === "Enter") {
                        sendResponse(email.email);
                    }
                });
            }
        }

        function getEmailsFromDatabase(topic) {
            // Make a GET request to the PHP server to get all emails
            fetch('database_api.php?all=true')
                .then(response => response.json()) // Parse the JSON response
                .then(data => {

                    const hasDataChanged = JSON.stringify(data) !== JSON.stringify(emailsData);
                    //const topicChanged = lastTopic !== topic;

                    // Only update if the data has changed or if it's the first time fetching
                    if (hasDataChanged) {
                        if(emailsData != null)
                        {
                            showNewMailPopup();
                        }
                        //TODO popup when new eamil arive.
                        // const mailContent = document.querySelector(".mail-content");
                        // mailContent.innerHTML = ""; // Clear current content

                        // updateContent(topic, data); // Update the content with the new emails

                        emailsData = data; // Store the current data
                        loadEmails(topic);
                        // lastTopic = topic;
                    }

                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    mailContent.innerHTML = "<p>There was an error loading the emails.</p>";
                });
        }

        const lampAudio = document.getElementById("lampMusic");
        async function checkLampState() {
            try {
                const response = await fetch(`database_api.php?lamp=true`);
                const data = await response.text(); // spodziewamy się np. "shine" lub "idle"
                const trimmedData = data.trim();

                // statusEl.textContent = trimmedData;

                if (trimmedData === "shine") {
                    if (lampAudio.paused) {
                        await lampAudio.play();
                    }
                } else {
                    if (!lampAudio.paused) {
                        lampAudio.pause();
                        lampAudio.currentTime = 0; // reset
                    }
                }
            } catch (error) {
                console.error("Błąd podczas pobierania stanu:", error);
                // statusEl.textContent = "Błąd";
            }
        }

        // Periodically check for updates every 1 second (1000ms)
        setInterval(() => {

            checkLampState();
            //console.log("This action runs every second");
            const activeTopic = document.querySelector(".topics li.active"); // Get the currently active topic

            console.log(activeTopic.textContent);
            if (activeTopic) {
                getEmailsFromDatabase(activeTopic.textContent); // Reload emails for the active topic if data has changed
            }
        }, 1000); // Check every 1000ms = 1 second

        document.addEventListener("DOMContentLoaded", () => {
            getEmailsFromDatabase("General");
            const audio = document.getElementById('mailSound');
            audio.load(); // Wczytaj dźwięk natychmiast
        });

        // // Uruchamianie co 3 sekundy
        // setInterval(checkDeviceState, 1000);

        // // Opcjonalnie uruchom od razu
        // checkDeviceState();


    </script>
</body>
</html>
