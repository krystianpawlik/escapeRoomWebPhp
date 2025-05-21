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
    visable INTEGER,
    previous_ids TEXT
)");

// Create team table if it doesn't exist
$db->exec("
    CREATE TABLE IF NOT EXISTS team (
        id INTEGER PRIMARY KEY CHECK (id = 1),
        teamName TEXT NOT NULL
    )
");

$db->exec("CREATE TABLE IF NOT EXISTS devices (name TEXT PRIMARY KEY, last_seen INTEGER)");

function getTeamName($db) {
    $result = $db->querySingle("SELECT teamName FROM team WHERE id = 1");
    return $result ?: "No team name set.";
}

function setTeamName($db, $teamName) {
    $stmt = $db->prepare("
        INSERT INTO team (id, teamName)
        VALUES (1, :teamName)
        ON CONFLICT(id) DO UPDATE SET teamName = excluded.teamName
    ");
    $stmt->bindValue(':teamName', $teamName, SQLITE3_TEXT);
    return $stmt->execute() !== false;
}


//
setTeamName($db, "Troubleshooting team");

// 1X Card veryfication
// 3X Contactrons and Lamp
// 4X Router
// 5X Energy
// 6X InstallLms
// 7X RBS burning?

// Check if table is empty
$result = $db->querySingle("SELECT COUNT(*) FROM emails");
if ($result == 0) {
    $initialData = [
        "General" => [
            [
                "id" => 11,
                "avatar" => "https://i.pravatar.cc/40?img=1",
                "name" => "Safty team",
                "email" => "support@ericsson.com",
                "subject" => "Veryfication!",
                "content" => "We kindly ask you to carry out a <b>verification of personnel using ID cards</b>. This process is part of our ongoing efforts to ensure site security and confirm that all individuals have the appropriate access permissions.<br>
                              In light of our recent suspicion regarding possible unauthorized access to the RBS system, we kindly request an urgent verification. <b>Use available yellow box for veryfication.</b>",
                "response" => null,
                "expectedResponse" => "",
                "solutionType" => "external",
                "visable" => true,
                "previous_ids" => ""
            ],
            [
                "id" => 12,
                "avatar" => "https://i.pravatar.cc/40?img=1",
                "name" => "Safty team",
                "email" => "support@ericsson.com",
                "subject" => "Veryfication!",
                "content" => "The verification has been successfully completed. You will receive new emails with further instructions shortly.",
                "response" => null,
                "expectedResponse" => "",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 13,
                "avatar" => "https://i.pravatar.cc/40?img=2",
                "name" => "Jane Smith",
                "email" => "jane@example.com",
                "subject" => "Topic Solved",
                "content" => 'It just begining<BR> <img src="img/keyCardIcon.png" style="width:150px; border-radius:50%;">',
                "response" => null,
                "expectedResponse" => "Got it!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ]
        ],
        "Network_Configuration" => [
            [
                "id" => 30,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Admin",
                "email" => "admin@example.com",
                "subject" => "System Update",
                "content" => "We've lost contact with multiple RBS in the customer network.<br>

                            Your objectives:<br>
                                <ol type = \"1\" style=\"margin-left: 20px\">
                                <li>Locate missing antenna components hidden on-site.</li>
                                <li>Use the map to correctly reconfigure our programable network.</li>
                                </ol><br>
                                This failure impacts client operations. <br>
                                Act fast. Restore the link.<br><br>

                            Best Regards,<br>
                            Network Control",

                "response" => null,
                "expectedResponse" => "",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ]
            ],
        "Router_Configuration" => [
            [
                "id" => 40,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Admin",
                "email" => "admin@example.com",
                "subject" => "System Update",
                "content" => "A new update will be released tomorrow.",
                "response" => 'Achivment : <b/><img src="https://i.pravatar.cc/40?img=2" style="width:50px; border-radius:50%;">',
                "expectedResponse" => "Thanks for the update!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ]
        ]
    ];

    //insert data
    $stmt = $db->prepare("INSERT INTO emails 
        (id, avatar, name, email, subject, content, response, expectedResponse, solutionType, category, visable, previous_ids)
        VALUES (:id, :avatar, :name, :email, :subject, :content, :response, :expectedResponse, :solutionType, :category, :visable, :previous_ids)");

    foreach ($initialData as $category => $emails) {
        foreach ($emails as $email) {
            $stmt->bindValue(':id', $email['id'], SQLITE3_INTEGER);
            $stmt->bindValue(':avatar', $email['avatar'], SQLITE3_TEXT);
            $stmt->bindValue(':name', $email['name'], SQLITE3_TEXT);
            $stmt->bindValue(':email', $email['email'], SQLITE3_TEXT);
            $stmt->bindValue(':subject', $email['subject'], SQLITE3_TEXT);
            $stmt->bindValue(':content', $email['content'], SQLITE3_TEXT);
            $stmt->bindValue(':response', $email['response'], SQLITE3_TEXT);
            $stmt->bindValue(':expectedResponse', $email['expectedResponse'], SQLITE3_TEXT);
            $stmt->bindValue(':solutionType', $email['solutionType'], SQLITE3_TEXT);
            $stmt->bindValue(':category', $category, SQLITE3_TEXT);
            $stmt->bindValue(':visable', $email['visable'] ? 1 : 0, SQLITE3_INTEGER);
            $stmt->bindValue(':previous_ids',  $email['previous_ids'], SQLITE3_TEXT);
            $stmt->execute();
        }
    }
}

// Nagłówki
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

function updateDeviceState($db, $device)
{
    $time = time();
    $stmt = $db->prepare("INSERT INTO devices (name, last_seen) VALUES (:name, :seen)
                        ON CONFLICT(name) DO UPDATE SET last_seen = excluded.last_seen");
    $stmt->bindValue(':name', $device, SQLITE3_TEXT);
    $stmt->bindValue(':seen', $time, SQLITE3_INTEGER);
    $stmt->execute();
}

function setVisableById($db, $id) {
    // $stmt = $db->prepare("UPDATE emails SET visable = 1 WHERE id = :id");
    // $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    // return $stmt->execute();

    // Update "visable"
    $stmt = $db->prepare("UPDATE emails SET visable = :visable WHERE id = :id");
    $stmt->bindValue(':visable', 1, SQLITE3_INTEGER);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    
    return $stmt->execute();
}

function handleBoxPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    //error_log("values:", $values);
    switch ($splitValues[0]) {
        case "teamName":
            //Todo probably to be removed
            //for now leaving for future
            $teamName = $splitValues[1];
            
            if (setTeamName($db, $teamName)) {
                setVisableById($db, 12); //!!! Trigger next mail.
                setVisableById($db, 13); //!!! Trigger next mail.
                setVisableById($db, 30); //!!! Trigger next mail.
                setVisableById($db, 40); //!!! Trigger next mail.
                echo json_encode(['line1' => "Authentication", 'line2' => $teamName]);
            }
            break;
        case "alive":
            //log to database
            updateDeviceState($db, "box");
            echo "alive";
            break; 
        case "button":
            echo "button";
            break;
        default:
            echo "defult action";
            break;
    }

}

function handleBoxCardPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    //error_log("values:", $values);
    switch ($splitValues[0]) {
        case "0":
            //Cards that reject authentication
            echo "Unauthorized";
            break;
        case "1":
            // First Login Card
            setVisableById($db, 12); //!!! Trigger next mail.
            setVisableById($db, 13); //!!! Trigger next mail.
            setVisableById($db, 30); //!!! Trigger next mail.
            setVisableById($db, 40); //!!! Trigger next mail.
            echo "Sucesfull Log";

            break;
        case "2":

            break;
        default:
            echo "defult action";
            break;
    }
}


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
            // Handles "visable" update
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

            // Update "visable"
            $stmt = $db->prepare("UPDATE emails SET visable = :visable WHERE id = :id");
            $stmt->bindValue(':visable', $data['visable'], SQLITE3_INTEGER);
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            echo "Message $id updated (visable = true)";
            break;
        case "box":
            handleBoxPost($db, $data);
            break;
        case "box-card":
            handleBoxCardPost($db, $data);
            break;
        default:
            echo "action not recognised";
            break;
    }

    exit;
}

// GET — fetch data
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $all = isset($_GET['all']) && $_GET['all'] === 'true';

    // If the 'all' parameter is not set, only select messages marked as visable
    if ($all) {
        // Get all messages (ignore 'visable' state)
        $results = $db->query("SELECT * FROM emails ORDER BY category, id");
    } else {
        // Get only messages with visable = 1
        $results = $db->query("SELECT * FROM emails WHERE visable = 1 ORDER BY category, id");
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