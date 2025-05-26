<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lista Wiadomości</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }
    .container {
      width: 80%;
      margin: 20px auto;
    }
    .email {
      background-color: #fff;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .email h3 {
      margin: 0;
      font-size: 20px;
    }
    .email p {
      font-size: 14px;
      color: #555;
    }
    .visable {
      color: green;
      font-weight: bold;
    }
    .invisable {
      color: red;
      font-weight: bold;
    }
    button {
      padding: 5px 10px;
      font-size: 14px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      background-color: #007bff;
      color: white;
    }
    button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Lista Wiadomości</h1>
    <div id="emailSections"></div>
  </div>

  <script>
    async function fetchEmails() {
      const response = await fetch("database_api.php?all=true", { method: "GET" });
      const data = await response.json();

      if (!data || typeof data !== 'object') {
        console.error('Brak poprawnych danych');
        return;
      }

      const emailSections = document.getElementById("emailSections");
      emailSections.innerHTML = ''; // wyczyść przed dodaniem

      Object.keys(data).forEach(topic => {
        const topicHeader = document.createElement('h2');
        topicHeader.textContent = topic;
        emailSections.appendChild(topicHeader);

        data[topic].forEach(email => {
          const emailElement = document.createElement('div');
          emailElement.className = 'email';
          emailElement.innerHTML = `
            <h3>${email.subject} 
              <span class="${email.visable ? 'visable' : 'invisable'}">
                [${email.visable ? 'visable' : 'invisable'}]
              </span>
            </h3>
            <p><strong>From:</strong> ${email.name} (${email.email})</p>
            <p><strong>Content:</strong> ${email.content}</p>
            <button onclick="toggleVisable(${email.id}, ${email.visable})">Change Status</button>
          `;
          emailSections.appendChild(emailElement);
        });
      });
    }

    async function toggleVisable(id, visable) {
      const newVisable = visable === 1 ? 0 : 1;

      const response = await fetch("database_api.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          action: "update",
          id: id,
          visable: newVisable
        })
      });

      await response.text(); // optional: show confirmation
      fetchEmails();
    }

    fetchEmails();
  </script>
</body>
</html>