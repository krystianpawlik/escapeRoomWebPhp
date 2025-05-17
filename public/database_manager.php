<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        
        <!-- Sekcja dla ogólnych wiadomości -->
        <h2>Ogólne</h2>
        <div id="generalEmailsList"></div>

        <!-- Sekcja dla ogłoszeń -->
        <h2>Ogłoszenia</h2>
        <div id="announcementsEmailsList"></div>
    </div>

    <script>
        // Funkcja do pobierania wszystkich wiadomości
        async function fetchEmails() {
            const response = await fetch("database_api.php?all=true", { method: "GET" });
            const data = await response.json();
            console.log(data); // Sprawdzamy odpowiedź

            if (!data) {
                console.error('Brak danych');
                return;
            }

            // Pobieramy kontenery dla obu kategorii
            const generalEmailsList = document.getElementById("generalEmailsList");
            const announcementsEmailsList = document.getElementById("announcementsEmailsList");

            // Czyszczenie obecnej zawartości
            generalEmailsList.innerHTML = '';
            announcementsEmailsList.innerHTML = '';

            // Renderujemy wiadomości w kategorii "General"
            data.General.forEach(email => {
                const emailElement = document.createElement('div');
                emailElement.className = 'email';
                emailElement.innerHTML = `
                    <h3>${email.subject} <span class="${email.visable ? 'visable' : 'invisable'}">[${email.visable ? 'visable' : 'invisable'}]</span></h3>
                    <p><strong>From:</strong> ${email.name} (${email.email})</p>
                    <p><strong>Content:</strong> ${email.content}</p>
                    <button onclick="toggleVisable(${email.id}, ${email.visable})">Change Status</button>
                `;
                generalEmailsList.appendChild(emailElement);
            });

            // Renderujemy wiadomości w kategorii "Announcements"
            data.Announcements.forEach(email => {
                const emailElement = document.createElement('div');
                emailElement.className = 'email';
                emailElement.innerHTML = `
                    <h3>${email.subject} <span class="${email.visable ? 'visable' : 'invisable'}">[${email.visable ? 'visable' : 'invisable'}]</span></h3>
                    <p><strong>From:</strong> ${email.name} (${email.email})</p>
                    <p><strong>Content:</strong> ${email.content}</p>
                    <button onclick="toggleVisable(${email.id}, ${email.visable})">Change Status</button>
                `;
                announcementsEmailsList.appendChild(emailElement);
            });
        }

        // Funkcja do zmiany stanu 'visable' wiadomości
        async function toggleVisable(id, visable) {
            const newVisable = visable === 1 ? 0 : 1;  // Zmienia stan visable na przeciwny

            // Wysłanie żądania POST do API PHP z dodatkowym polem 'action'
            const response = await fetch("database_api.php", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json" 
                },
                body: JSON.stringify({
                    action: "update",  // Określamy, że chodzi o akcję "update"
                    id: id,
                    visable: newVisable
                })
            });

            // Oczekiwanie na odpowiedź
            const result = await response.text();
            //alert(result);  // Wyświetl komunikat zwrócony przez API

            // Odśwież listę po zmianie stanu
            fetchEmails();
        }

        // Pobierz maile przy załadowaniu strony
        fetchEmails();
    </script>
</body>
</html>