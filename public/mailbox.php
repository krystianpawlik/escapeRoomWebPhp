<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailbox</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; height: 100vh; background: #e3f2fd; }
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
    </style>
</head>
<body>
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
        const emailsData = {
            "General": [
                { avatar: "https://i.pravatar.cc/40?img=1", name: "John Doe", email: "john@example.com", subject: "Hello!", content: "Welcome to the mailbox system." },
                { avatar: "https://i.pravatar.cc/40?img=2", name: "Jane Smith", email: "jane@example.com", subject: "Meeting Reminder", content: "Don't forget the meeting at 3 PM." }
            ],
            "Announcements": [
                { avatar: "https://i.pravatar.cc/40?img=3", name: "Admin", email: "admin@example.com", subject: "System Update", content: "A new update will be released tomorrow." }
            ],
            "Support": [
                { avatar: "https://i.pravatar.cc/40?img=4", name: "Support Team", email: "support@example.com", subject: "Ticket Resolved", content: "Your issue has been resolved." }
            ],
            "Feedback": [
                { avatar: "https://i.pravatar.cc/40?img=5", name: "User Feedback", email: "feedback@example.com", subject: "Feature Request", content: "Please add dark mode support." }
            ]
        };

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
                <div class="response-box">
                    <input type="text" placeholder="Type your response..." id="responseInput">
                    <button onclick="sendResponse('${email.email}')">Respond</button>
                </div>
            `;
            emailDetails.style.display = "flex";
        }

        function sendResponse(email) {
            const responseInput = document.getElementById("responseInput");
            const responseText = responseInput.value.trim();
            if (responseText) {
                alert(`Response sent to ${email}: "${responseText}"`);
                responseInput.value = "";
            }
        }

        document.addEventListener("DOMContentLoaded", () => loadEmails("General"));
    </script>
</body>
</html>