<?php
$db = new SQLite3('database.sqlite');

// Create table
$db->exec("CREATE TABLE IF NOT EXISTS emails (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    avatar TEXT,
    name TEXT,
    email TEXT,
    subject TEXT,
    content TEXT,
    response TEXT,
    expectedResponse TEXT,
    solutionType TEXT,
    category TEXT,
    solved INTEGER,
    previous_ids TEXT
)");

// Check if table is empty
$result = $db->querySingle("SELECT COUNT(*) FROM emails");
if ($result == 0) {
    $initialData = [
        "General" => [
            [
                "id" => 1,
                "avatar" => "https://i.pravatar.cc/40?img=1",
                "name" => "John Doe",
                "email" => "john@example.com",
                "subject" => "Hello!",
                "content" => "Welcome to the mailbox system.",
                "response" => null,
                "expectedResponse" => "Hello!",
                "solutionType" => "TextBox",
                "solved" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 2,
                "avatar" => "https://i.pravatar.cc/40?img=2",
                "name" => "Jane Smith",
                "email" => "jane@example.com",
                "subject" => "Meeting Reminder",
                "content" => "Don't forget the meeting at 3 PM.",
                "response" => null,
                "expectedResponse" => "Got it!",
                "solutionType" => "external",
                "solved" => false,
                "previous_ids" => ""
            ]
        ],
        "Announcements" => [
            [
                "id" => 3,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Admin",
                "email" => "admin@example.com",
                "subject" => "System Update",
                "content" => "A new update will be released tomorrow.",
                "response" => 'Achivment : <b/><img src="https://i.pravatar.cc/40?img=2" style="width:50px; border-radius:50%;">',
                "expectedResponse" => "Thanks for the update!",
                "solutionType" => "external",
                "solved" => false,
                "previous_ids" => ""
            ]
        ]
    ];

    //insert data
    $stmt = $db->prepare("INSERT INTO emails 
        (avatar, name, email, subject, content, response, expectedResponse, solutionType, category, solved, previous_ids)
        VALUES (:avatar, :name, :email, :subject, :content, :response, :expectedResponse, :solutionType, :category, :solved, :previous_ids)");

    foreach ($initialData as $category => $emails) {
        foreach ($emails as $email) {
            $stmt->bindValue(':avatar', $email['avatar'], SQLITE3_TEXT);
            $stmt->bindValue(':name', $email['name'], SQLITE3_TEXT);
            $stmt->bindValue(':email', $email['email'], SQLITE3_TEXT);
            $stmt->bindValue(':subject', $email['subject'], SQLITE3_TEXT);
            $stmt->bindValue(':content', $email['content'], SQLITE3_TEXT);
            $stmt->bindValue(':response', $email['response'], SQLITE3_TEXT);
            $stmt->bindValue(':expectedResponse', $email['expectedResponse'], SQLITE3_TEXT);
            $stmt->bindValue(':solutionType', $email['solutionType'], SQLITE3_TEXT);
            $stmt->bindValue(':category', $category, SQLITE3_TEXT);
            $stmt->bindValue(':solved', $email['solved'] ? 1 : 0, SQLITE3_INTEGER);
            $stmt->bindValue(':previous_ids',  $email['previous_ids'], SQLITE3_TEXT);
            $stmt->execute();
        }
    }
}

// Nagłówki
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// POST — add a message
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['action'])) {
        http_response_code(400);
        echo "Missing field: action";
        exit;
    }

    switch ($data['action']) {
        case "update":
            // Handles "solved" update
            if (!isset($data['id'])) {
                http_response_code(400);
                echo "Missing field: id";
                exit;
            }

            $id = (int)$data['id'];

            // Check if the message exists
            $exists = $db->querySingle("SELECT COUNT(*) FROM emails WHERE id = $id");
            if (!$exists) {
                http_response_code(404);
                echo "Message with id = $id not found";
                exit;
            }

            // Update "solved"
            $stmt = $db->prepare("UPDATE emails SET solved = :solved WHERE id = :id");
            $stmt->bindValue(':solved', $data['solved'], SQLITE3_INTEGER);
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            echo "Message $id updated (solved = true)";
            break;
    }

    exit;
}

// GET — fetch data
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $all = isset($_GET['all']) && $_GET['all'] === 'true';

    // If the 'all' parameter is not set, only select messages marked as solved
    if ($all) {
        // Get all messages (ignore 'solved' state)
        $results = $db->query("SELECT * FROM emails ORDER BY category, id DESC");
    } else {
        // Get only messages with solved = 1
        $results = $db->query("SELECT * FROM emails WHERE solved = 1 ORDER BY category, id DESC");
    }

    $grouped = [];

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $cat = $row['category'];
        unset($row['category']); // Remove category from data, since we are grouping by it
        $grouped[$cat][] = $row;
    }

    // Set response header to JSON
    header('Content-Type: application/json');
    echo json_encode($grouped);
}
?>