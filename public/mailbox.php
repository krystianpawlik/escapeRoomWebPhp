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
            display: none;
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
        .disabled {
            opacity: 0.3;
            pointer-events: none;
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
                <li onclick="loadEmails('Announcements')">Announcements</li>
                <li onclick="loadEmails('Support')">Support</li>
                <li onclick="loadEmails('Feedback')">Feedback</li>
            </ul>
        </div>

        <div class="mailbox">
            <div class="mail-list"></div>
            <div class="email-details"></div>
        </div>
    </div>

    <div id="popup" class="popup">
        <p>Was this respons really correct?</p>
        <img src="https://media1.tenor.com/m/x8v1oNUOmg4AAAAd/rickroll-roll.gif" alt="Rickroll">
        <audio id="popupAudio" src="https://www.myinstants.com/media/sounds/rickroll.mp3" loop></audio>
        <p id="question">Quiz to close: Jaką rocznicę będzie świętował Ericsson w tym roku w tym roku?</p>
        <p id="timer" style="display:none; font-weight: bold;"></p>
        <!-- <button onclick="closePopup()">OK</button> -->
        <button onclick="closePopup()">121</button>
        <button onclick="thinkAgain()">120</button>
        <button onclick="thinkAgain()">6</button>
        <button onclick="thinkAgain()">100</button>
    </div>

    <template id="email-template">
        <div class="email-message">
            <img class="avatar" src="" alt="Avatar">
            <div>
                <h3 class="name"></h3>
                <p class="subject"></p>
            </div>
        </div>
    </template>

    <script>

        const popupQuestions = [
            { question: "Was this response really correct?", answers: ["Yes", "No", "Not sure", "Show correct answer"], correct: "Yes" },
            { question: "What is the capital of France?", answers: ["Berlin", "Madrid", "Paris", "Rome"], correct: "Paris" },
            { question: "How many continents are there?", answers: ["5", "6", "7", "8"], correct: "7" },
            { question: "Which planet is known as the Red Planet?", answers: ["Earth", "Mars", "Venus", "Jupiter"], correct: "Mars" }
        ];


        //solutionType=TextBox or solutionType="external"
        const emailsData = {
            "General": [
                { avatar: "https://i.pravatar.cc/40?img=1", name: "John Doe", email: "john@example.com", 
                    subject: "Hello!", content: "Welcome to the mailbox system.", response: null, expectedResponse: "Hello!", solutionType: "TextBox" },
                { avatar: "https://i.pravatar.cc/40?img=2", name: "Jane Smith", email: "jane@example.com", 
                    subject: "Meeting Reminder", content: "Don't forget the meeting at 3 PM.", response: null, expectedResponse: "Got it!", solutionType: "external" }
            ],
            "Announcements": [
                { avatar: "https://i.pravatar.cc/40?img=3", name: "Admin", email: "admin@example.com", 
                    subject: "System Update", content: "A new update will be released tomorrow.", response: 'Achivment : <b/><img src="https://i.pravatar.cc/40?img=2" style="width:50px; border-radius:50%;">', 
                    expectedResponse: "Thanks for the update!", solutionType: "external" }
            ]
        };


        function thinkAgain() {
            const buttons = document.querySelectorAll("#popup button");
            const timerDisplay = document.getElementById("timer");
            let countdown = 5;

            // Wyszarz wszystkie przyciski
            buttons.forEach(btn => btn.classList.add("disabled"));

            // Pokaż timer
            timerDisplay.style.display = "block";
            timerDisplay.textContent = `Pomyśl jeszcze raz... (${countdown}s)`;

            // Odliczanie
            const interval = setInterval(() => {
                countdown--;
                timerDisplay.textContent = `Pomyśl jeszcze raz... (${countdown}s)`;
                
                if (countdown <= 0) {
                    clearInterval(interval);
                    timerDisplay.style.display = "none";

                    // Przywróć przyciski
                    buttons.forEach(btn => btn.classList.remove("disabled"));
                }
            }, 1000);
        }   

        // function generatePopup() {
        //     const randomIndex = Math.floor(Math.random() * popupQuestions.length);
        //     const q = popupQuestions[randomIndex];

        //     // Zbuduj HTML popupu
        //     const popupHtml = `
        //         <div id="popup" class="popup">
        //             <p>${q.question}</p>
        //             <img src="https://media1.tenor.com/m/x8v1oNUOmg4AAAAd/rickroll-roll.gif" alt="Rickroll">
        //             <audio id="popupAudio" src="https://www.myinstants.com/media/sounds/rickroll.mp3" loop autoplay></audio>
        //             <p id="question">Quiz to close: ${q.question}</p>
        //             <p id="timer" style="display:none; font-weight: bold;"></p>
        //             ${q.answers.map(answer => `
        //                 <button onclick="${answer === q.correct ? 'closePopup()' : 'thinkAgain()'}">${answer}</button>
        //             `).join('')}
        //         </div>
        //     `;

        //     // Wstaw do body
        //     document.body.insertAdjacentHTML('beforeend', popupHtml);
        //     document.getElementById('popupAudio').play();
        // }

        function loadEmails(topic) {
            const mailList = document.querySelector(".mail-list");
            const emailDetails = document.querySelector(".email-details");

            mailList.innerHTML = "";
            emailDetails.innerHTML = "";
            emailDetails.style.display = "none";

            document.querySelectorAll(".topics li").forEach(item => item.classList.remove("active"));
            document.querySelectorAll(".topics li").forEach(li => {
                li.classList.toggle("active", li.textContent.trim() === topic);
            });

            if (!emailsData[topic]) return;

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

        function showEmailDetails(email) {
            const emailDetails = document.querySelector(".email-details");
            emailDetails.innerHTML = `
                <h2>${email.subject}</h2>
                <img src="${email.avatar}" style="width:50px; border-radius:50%;">
                <h3>${email.name} (${email.email})</h3>
                <p>${email.content}</p>
                ${email.response ? `<label class="response-label">${email.response}</label>` : `
                <div class="response-box">
                    <input type="text" placeholder="Type your response..." id="responseInput">
                    <button onclick="sendResponse('${email.email}')">Respond</button>
                </div>`}
            `;
            emailDetails.style.display = "flex";
        }

        function sendResponse(email) {
            const responseInput = document.getElementById("responseInput");
            const responseText = responseInput.value.trim();
            const emailData = Object.values(emailsData).flat().find(e => e.email === email);
            if (emailData) {
                emailData.response = responseText;
                showEmailDetails(emailData);
                if (responseText !== emailData.expectedResponse) {
                    document.getElementById("popup").style.display = "block";
                    document.getElementById("popupAudio").play();
                }
            }
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
            document.getElementById("popupAudio").pause();
            document.getElementById("popupAudio").currentTime = 0;
        }

        document.addEventListener("DOMContentLoaded", () => loadEmails("General"));
    </script>
</body>
</html>
