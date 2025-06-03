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

// Tworzymy tabelę z kolumną state (jeśli nie istnieje)
$db->exec("
    CREATE TABLE IF NOT EXISTS devices (
        name TEXT PRIMARY KEY,
        last_seen INTEGER,
        state TEXT DEFAULT 'idle'
    )
");

//Tworzy tablelę puzzles do przechowywania czesci stanow 
$db->exec("
    CREATE TABLE IF NOT EXISTS puzzles (
        name TEXT PRIMARY KEY,
        status TEXT,
        value TEXT
    )
");

// Funkcja zapisująca lub aktualizująca wpis
function setPuzzleData($name, $status, $value) {
    global $db; // obiekt SQLite3

    // Przygotuj zapytanie - INSERT OR REPLACE dla aktualizacji lub dodania
    $stmt = $db->prepare('INSERT OR REPLACE INTO puzzles (name, status, value) VALUES (:name, :status, :value)');
    if (!$stmt) {
        return false;
    }

    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':status', $status, SQLITE3_TEXT);
    $stmt->bindValue(':value', $value, SQLITE3_TEXT);

    $result = $stmt->execute();
    if (!$result) {
        return false;
    }

    $result->finalize(); // zwolnij zasoby wyniku
    return true;
}

// Funkcja pobierająca dane po name
function getPuzzleData($name) {
    global $db; // obiekt SQLite3

    $stmt = $db->prepare('SELECT name, status, value FROM puzzles WHERE name = :name LIMIT 1');
    if (!$stmt) {
        return false;
    }

    $stmt->bindValue(':name', $name, SQLITE3_TEXT);

    $result = $stmt->execute();
    if (!$result) {
        return false;
    }

    $row = $result->fetchArray(SQLITE3_ASSOC);
    $result->finalize();

    if ($row === false) {
        return false;
    }

    return $row;
}

function initPuzzleState() {
    global $db;

    $result = $db->query("SELECT COUNT(*) AS count FROM puzzles");
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $count = $row['count'];

    if ($count == 0) {
        setPuzzleData('card', 'idle', '-1');
    }
}

initPuzzleState();

$resultDevices = $db->querySingle("SELECT COUNT(*) FROM devices");

$rbsStates = ["idle", "power", "connected", "buring", "weglan_lizard" , "monitor_lizard"];

if ($resultDevices == 0) {
    $time = time();
    $devices = ['box', 'lamp', 'raspberry1', 'raspberry2', 'rbs', "power_connector", "router"];

    $stmt = $db->prepare('INSERT INTO devices (name, last_seen, state) VALUES (:name, :time, :state)');

    foreach ($devices as $device) {
        $stmt->bindValue(':name', $device, SQLITE3_TEXT);
        $stmt->bindValue(':time', $time, SQLITE3_INTEGER);
        $stmt->bindValue(':state', "idle", SQLITE3_TEXT);
        $stmt->execute();
    }
}

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
// 5X Origami
// 6X Key
// 7X Energy
// 8X InstallLms
// 9X RBS restart? Time quize?

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
                "subject" => "Veryfication!(Solved)",
                "content" => 'It just begining<BR> <img src="img/keyCardIcon.png" style="width:150px; border-radius:50%;">',
                "response" => null,
                "expectedResponse" => "Got it!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ]
        ],
        "Network_Configuration/Kontaktrons" => [
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
            ],
            [
                "id" => 31,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Admin",
                "email" => "admin@example.com",
                "subject" => "System Update",
                "content" => "Wygląda że sieć została skonfigurowana. Natomiast aby zatwierdzić zmiany potrzebujemy hasła.<br>
                              Poprzednik technik wspominał że chasło jest ukryte w światłowodzie. Ale niestety nie wiem o co mu chodziło.<br>
                              Poszukaj wskazuwek w pokoju i prześlij mi hasło.<br>

                            Best Regards,<br>
                            Network Control",

                "response" => null,
                "expectedResponse" => "will",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 32,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Admin",
                "email" => "admin@example.com",
                "subject" => "System Update(Solved)",
                "content" => 'Brawo hasło które mi podaliście jest poprawne.<br>
                            <img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">
                            <br>
                            Best Regards,<br>
                            Network Control',
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
                "name" => "Jack Lin?",
                "email" => "Jack.Lin@ericsson.com",
                "subject" => "Router_Configuration",
                "content" => "Kpi drop for some reasone, counters indicate problem with router, please configure it, and send us password. Instruction how to configure router should be in room.",//aby poszukali w pokoju
                "response" => null,
                "expectedResponse" => "Haslo!!!", //
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 41,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Jack Lin?",
                "email" => "Jack.Lin@ericsson.com",
                "subject" => "Router_Configuration",
                "content" => 'KPI were restored, but for some reasone peopel who should not have access have additional features. Did you connect firewall? <br>
                              <img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">
                              <br>
                                Best Regards,<br>
                                Jack Lin',
                "response" => null,
                "expectedResponse" => "Thanks for the update!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 42,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Jack Lin?",
                "email" => "Jack.Lin@ericsson.com",
                "subject" => "(Solved)",
                "content" => 'KPI were restored, And firewall was properly connected bravo. <br>
                              <img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">
                              <br>
                                Best Regards,<br>
                                Jack Lin',
                "response" => null,
                "expectedResponse" => "Thanks for the update!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ]
            ],
            "Password Needed" => [
            [
                "id" => 50,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Potrzebujemy hasła",
                "content" => 'Podrzebujemy hasła do sieci, powino być gdzies na poskładanej kartce papieru.',
                "response" => null,
                "expectedResponse" => "Haslo123!!!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 51,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Potrzebujemy hasła",
                "content" => 'Podrzebujemy hasła przez to że firewall był źle podłączony do sieci, powino być gdzies na poskładanej kartce papieru.',
                "response" => null,
                "expectedResponse" => "Haslo123!!!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 52,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Potrzebujemy hasła",
                "content" => 'Udało się zalogować dzięki <br>
                              <img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">
                              <br>
                                Pozdrawiam,<br>
                                Stefan Batty',
                "response" => null,
                "expectedResponse" => "Haslo123!!!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ],
            ],
            "Find Key" => [
            [
                "id" => 60,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Znajdzie Klucz",
                "content" => "Podrzebujemy kodu z Sejfu który powinien być w pokoju. Znajdują się w nim ważne elementy aby kontytować z konfiguracją noda.",
                "response" => null,
                "expectedResponse" => "??????????",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => ""
            ]
            ],
            "Connect Energy" => [
            [
                "id" => 70,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Admin",
                "email" => "admin@example.com",
                "subject" => "Otworz Sejf",
                "content" => "Jak poda się kod z sejfu prosimy o podpięcie Energii.",
                "response" => 'No response',
                "expectedResponse" => "Kod",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 71,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Jack Lin?",
                "email" => "Jack.Lin@ericsson.com",
                "subject" => "Connect Energy (Solved1)",
                "content" => 'KPI were restored, And firewall was properly connected bravo. <br>
                              <img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">
                              <br>
                                Best Regards,<br>
                                Jack Lin',
                "response" => null,
                "expectedResponse" => "Thanks for the update!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 72,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Jack Lin?",
                "email" => "Jack.Lin@ericsson.com",
                "subject" => "Connect Energy (Solved2)",
                "content" => 'KPI were restored, And firewall was properly connected bravo. <br>
                              <img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">
                              <br>
                                Best Regards,<br>
                                Jack Lin',
                "response" => null,
                "expectedResponse" => "Thanks for the update!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => ""
            ]
            ],
            "Install Lm" => [
            [
                "id" => 80,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Zainstaluj soft na YB",
                "content" => "Zainstaluje LM na yellow box zgodnie z instrukcją",
                "response" => null,
                "expectedResponse" => "??????????",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 81,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Zainstaluj soft na YB(Solved)",
                "content" => '<img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">',
                "response" => null,
                "expectedResponse" => "??????????",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => ""
            ]
            ],
            "Restart RBS" => [
            [
                "id" => 90,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Znajdzie Klucz",
                "content" => 'Aby zrestartować RBS naciśnijcie poniższy przycisk<BR> <a href="awaria.php" class="restart-button">Restart RBS</a>',
                "response" => null,
                "expectedResponse" => "??????????",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 91,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Carbon of RBS from Monitor Lizard(Solved)",
                "content" => '<img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">',
                "response" => null,
                "expectedResponse" => "??????????",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => ""
            ],
            [
                "id" => 92,
                "avatar" => "https://i.pravatar.cc/40?img=3",
                "name" => "Stefan Batty",
                "email" => "Stefan.Batty@example.com",
                "subject" => "Free Monitor Lizard(Solved)",
                "content" => '<img src="img/achievementPlaceholder.png" style="width:150px; border-radius:50%;">',
                "response" => null,
                "expectedResponse" => "??????????",
                "solutionType" => "internal",
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

function updateDeviceTime($db, $device)
{
    $time = time();
    $stmt = $db->prepare("INSERT INTO devices (name, last_seen) VALUES (:name, :seen)
                        ON CONFLICT(name) DO UPDATE SET last_seen = excluded.last_seen");
    $stmt->bindValue(':name', $device, SQLITE3_TEXT);
    $stmt->bindValue(':seen', $time, SQLITE3_INTEGER);
    $stmt->execute();
}

function updateDeviceState($db, $deviceName, $newState) {
    $currentTime = time();
    $stmt = $db->prepare('UPDATE devices SET state = :state, last_seen = :time WHERE name = :name');
    $stmt->bindValue(':state', $newState, SQLITE3_TEXT);
    $stmt->bindValue(':time', $currentTime, SQLITE3_INTEGER);
    $stmt->bindValue(':name', $deviceName, SQLITE3_TEXT);
    return $stmt->execute();
}

function getDeviceState($db, $deviceName) {
    $stmt = $db->prepare('SELECT state FROM devices WHERE name = :name');
    $stmt->bindValue(':name', $deviceName, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        return $row['state'];
    } else {
        return null;
    }
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
            updateDeviceTime($db, "box");
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

// Tylko dla kart
function checkNextPuzzleStep($expected) {
    global $db;

    //Hardkoded CARD sequence
    $cardsSequence = ["red", "yellow", "blue"];

    $current = getPuzzleData('card');
    if (!$current) {
        return ['ok' => false, 'message' => 'Brak wpisu "card".'];
    }

    $currentIndex = intval($current['value']);
    $nextIndex = $currentIndex + 1;

    if (!isset($cardsSequence[$nextIndex])) {
        return ['ok' => false, 'message' => 'Nie ma kolejnego kroku.'];
    }

    $expectedNextStep = $cardsSequence[$nextIndex];

    if ($expected === $expectedNextStep) {
        $isLastStep = ($nextIndex === count($cardsSequence) - 1);

        if ($isLastStep) {
            //!!!!!!!!!! Ostatni Krok nastepne zadanie
            setPuzzleData('card', 'completed', (string)$nextIndex);
            return ['ok' => true, 'message' => "rpc installed"];
        } else {
            setPuzzleData('card', 'in_progress', (string)$nextIndex);
            return ['ok' => true, 'message' => "Installing {$expected}"];
        }
    } else {
        setPuzzleData('card', 'idle', "-1");
        return ['ok' => false, 'message' => "Incorrect, Reseting"];
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
        case "vip":
            // First Login Card
            setVisableById($db, 12); //!!! Trigger next mail.
            setVisableById($db, 13); //!!! Trigger next mail.
            setVisableById($db, 30); //!!! Trigger next mail.
            setVisableById($db, 40); //!!! Trigger next mail.
            echo "Sucesfull Log";

            break;
        case "red":
            $resultCard = checkNextPuzzleStep("red");
            echo $resultCard['message'];
            break;
        case "blue":
            $resultCard = checkNextPuzzleStep("blue");
            echo $resultCard['message'];
            break;
        case "yellow":
            $resultCard = checkNextPuzzleStep("yellow");
            echo $resultCard['message'];;
            break;
        case "reset":
            setPuzzleData('card', 'idle', "-1");
            echo "reset";
            break;
        default:
            echo "Incorect Action";    
            //echo "defult action";
            break;
    }
}


function handleLampPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "alive":
            //log to database
            updateDeviceTime($db, "lamp");
            echo "alive";
            break; 
        default:
            echo "defult action";
            break;
    }

}


function handleKontaktPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "connected":
            setVisableById($db, 31); //!!! Trigger next mail.
            updateDeviceState($db, "lamp", "shine");
            //start music
            //start light

            break;
        case "disconnected":
            updateDeviceState($db, "lamp", "idle");
            //stop music
            //stop light
            break;
        case "alive":
            //log to database
            //should i add it to database?
            //updateDeviceTime($db, "lamp");
            echo "alive";
            break; 
        // case "button":
        //     echo "button";
        //     break;
        default:
            echo "defult action";
            break;
    }

}

function handleMailboxPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "31"://Post that mailbox 31 was sucesfull
            setVisableById($db, 32);
            updateDeviceState($db, "lamp", "idle");
            echo "31";
            break;
        default:
            echo "defult action";
            break;
    }

}

function handlePowerConnectorPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "power_sucesfull":
            // setVisableById($db, XXX);
            updateDeviceState($db, "power_connector", "power_sucesfull");
            echo "power_sucesfull";
            break;
        case "power_wrong":
            // setVisableById($db, XXX);
            updateDeviceState($db, "power_connector", "power_wrong");
            echo "power_wrong";
            break;
        case "idle":
            updateDeviceState($db, "power_connector", "idle");
            echo "idle";
            break;
        case "alive":
            updateDeviceTime($db, "power_connector");
            echo "alive";
            break;
        default:
            echo "defult action";
            break;
    }
}

function handleRouterPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "firewall_connected":
            // setVisableById($db, XXX);
            updateDeviceState($db, "router", "firewall_connected");
            echo "firewall_connected";
            break;
        case "firewall_disconnected":
            // setVisableById($db, XXX);
            updateDeviceState($db, "router", "firewall_disconnected");
            echo "firewall_disconnected";
            break;
        case "idle":
            updateDeviceState($db, "router", "idle");
            echo "idle";
            break;
        case "alive":
            updateDeviceTime($db, "router");
            echo "alive";
            break;
        default:
            echo "defult action";
            break;
    }
}

//   <button onclick="sendValue(this)">idle</button>
//   <button onclick="sendValue(this)">connected</button>
//   <button onclick="sendValue(this)">power</button>
//   <button onclick="sendValue(this)">buring</button>
//   <button onclick="sendValue(this)">weglan_lizard</button>
//   <button onclick="sendValue(this)">monitor_lizard</button>

function handleRbsSimulatorPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "idle":
            updateDeviceState($db, "rbs", "idle");
            echo "idle";
            break;
        default:
            updateDeviceState($db, "rbs", $splitValues[0]);
            echo $splitValues[0];
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
        case "kontaktrons":
            handleKontaktPost($db, $data);
            break;
        case "lamp":
            handleLampPost($db, $data);
            break;
        case "mailbox":
            handleMailboxPost($db, $data);
            break;
        case "rbs_simulator":
            handleRbsSimulatorPost($db, $data); 
            break;
        case "power_connector":
            handlePowerConnectorPost($db, $data);
            break;
        case "router":
            handleRouterPost($db, $data);
            break;
        default:
            echo "action not recognised";
            break;
    }

    exit;
}

// GET — fetch data
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    //to rm
    $lamp = isset($_GET['lamp']) && $_GET['lamp'] === 'true';
    if ($lamp)
    {
        //header('Content-Type: application/json');
        echo getDeviceState($db, "lamp");
        return;
    }

    $lamp = isset($_GET['device']) && $_GET['device'] === 'lamp';
    if ($lamp)
    {
        echo getDeviceState($db, "lamp");
        return;
    }

    $rbs = isset($_GET['device']) && $_GET['device'] === 'rbs';

    if($rbs)
    {
        echo getDeviceState($db, "rbs");;
        return;
    }

    $all = isset($_GET['all']) && $_GET['all'] === 'true';

    // If the 'all' parameter is not set, only select messages marked as visable
    if ($all) {
        // Get all messages (ignore 'visable' state)
        $results = $db->query("SELECT * FROM emails ORDER BY id");
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