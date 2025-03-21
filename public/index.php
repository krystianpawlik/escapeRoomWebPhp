<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailbox Layout</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; height: 100vh; background: #e3f2fd; }
        .sidebar { width: 25%; background: #2196f3; padding: 15px; border-right: 1px solid #1976d2; color: white; }
        .sidebar h2 { margin-bottom: 10px; }
        .topics { list-style: none; }
        .topics li { padding: 10px; cursor: pointer; border-bottom: 1px solid #64b5f6; }
        .topics li:hover { background: #1976d2; }
        .mailbox { flex: 1; display: flex; justify-content: flex-start; align-items: flex-start; padding: 20px; }
        .mail-content { width: 40%; padding: 20px; overflow-y: auto; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        .email-message { margin-bottom: 10px; padding: 10px; border: 1px solid #bbdefb; border-radius: 5px; background: #f0f8ff; cursor: pointer; }
        .email-message h2 { margin-bottom: 5px; font-size: 16px; }
        .email-message p { display: block; margin-top: 5px; }
        .email-message:last-child { background: #fff176; }
    </style>
    <script>
let lastFetchedData = null; // Store the last fetched email data
let lastTopic = null;
function updateContent(topic, emails)
{
    const mailContent = document.querySelector(".mail-content");
    mailContent.innerHTML = ""; 
    emails[topic].forEach((email, index, array) => {
        const emailDiv = document.createElement("div");
        emailDiv.classList.add("email-message");
        if (index === array.length - 1) {
            emailDiv.style.background = "#fff176"; 
        }
        emailDiv.innerHTML = `<h2>${email.subject}</h2><p>${email.content}</p>`;
        mailContent.appendChild(emailDiv);
    });
}

function loadEmails(topic) {


    // Make a GET request to the PHP server to get all emails
    fetch('database.php')
        .then(response => response.json()) // Parse the JSON response
        .then(data => {

            const hasDataChanged = JSON.stringify(data) !== JSON.stringify(lastFetchedData);
            const topicChanged = lastTopic !== topic;

            // Only update if the data has changed or if it's the first time fetching
            if (hasDataChanged || topicChanged) {
                const mailContent = document.querySelector(".mail-content");
                mailContent.innerHTML = ""; // Clear current content

                updateContent(topic, data); // Update the content with the new emails
                lastFetchedData = data; // Store the current data
                lastTopic = topic;
            }

        })
        .catch(error => {
            console.error('Error fetching data:', error);
            mailContent.innerHTML = "<p>There was an error loading the emails.</p>";
        });
}

// Periodically check for updates every 1 second (1000ms)
setInterval(() => {
    console.log("This action runs every second");
    const activeTopic = document.querySelector(".topics li.active"); // Get the currently active topic

    console.log(activeTopic.textContent);
    if (activeTopic) {
        loadEmails(activeTopic.textContent); // Reload emails for the active topic if data has changed
    }
}, 5000); // Check every 1000ms = 1 second

document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".topics li").forEach(topic => {
                topic.addEventListener("click", () => {
                    loadEmails(topic.textContent);
                    // Set the clicked topic as active
                    document.querySelectorAll(".topics li").forEach(item => item.classList.remove("active"));
                    topic.classList.add("active");
                });
            });
            // Set "General" topic as active by default
            document.querySelector(".topics li").classList.add("active");
            //lastTopic = "General";
            loadEmails("General"); 

        });

    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Topics</h2>
        <ul class="topics">
            <li>General</li>
            <li>Announcements</li>
            <li>Support</li>
            <li>Feedback</li>
        </ul>
    </div>
    <div class="mailbox">
        <div class="mail-content">
        </div>
    </div>
</body>
</html>