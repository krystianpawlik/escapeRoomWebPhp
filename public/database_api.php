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
    previous_ids TEXT,
    achievement TEXT,
    achievement_text TEXT
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
    $devices = ['box', 'lamp', 'raspberry1', 'raspberry2', 'rbs', "power_connector", "router", "skyfall"];

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


// Check if table is empty
$result = $db->querySingle("SELECT COUNT(*) FROM emails");
if ($result == 0) {
    $initialData = [
        "Authorisation" => [
            [
                "id" => 11,
                "avatar" => "img/waclaw_jala_sq.png",
                "name" => "Safety team",
                "email" => "support@ericsson.com",
                "subject" => "Authorisation!",
                "content" => 'We kindly ask you to carry out a <b>verification of personnel using ID cards</b>. This process is part of our ongoing efforts to ensure site security and confirm that all individuals have the appropriate access permissions.<br>
                              In the light of our recent suspicion regarding possible unauthorized access to the RBS system, we kindly request an urgent verification. <b>Use available yellow box for verification.</b><br>
                            <br>
                            Best Regards,<br>
                            Wacław Jala',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "external",
                "visable" => true,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 12,
                "avatar" => "img/waclaw_jala_sq.png",
                "name" => "Safety team",
                "email" => "support@ericsson.com",
                "subject" => "Authorisation!",
                "content" => 'The verification has been successfully completed. You will receive new emails with further instructions shortly.<br>
                            <br>
                            Best Regards,<br>
                            Wacław Jala',

                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 13,
                "avatar" => "img/waclaw_jala_sq.png",
                "name" => "Safety team",
                "email" => 'support@ericsson.com  <br> CC: stefan.baty@ericsson.com',
                "subject" => "Authorisation!(Finished)",
                "content" => 'It\'s just the beginning<BR> <img src="img/achievement/1_autoryzacja_ok.png" style="width:150px; border-radius:50%;"/><br>
                            <br>
                            Best Regards,<br>
                            Wacław Jala',

                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/1_autoryzacja_ok.png",
                "achievement_text" => null
            ]
        ],
        "Connect Energy" => [
            [
                "id" => 20,
                "avatar" => "img/ryszard_tomka_sq.png",
                "name" => "Admin IT",
                "email" => "ryszard.tomka@example.com",
                "subject" => "Podpięcie RBS na site Ankh Morpork - niezidentyfikowana awaria sprzętu ",
                "content" => 'Jak wiecie, poprzednio wysłany technik próbując naprawić RBS został porażony prądem.<br>
                              W związku z powyższym, RBS został odłączony od prądu.<br>
                              <b>Znajdźcie kabel zasilający i podepnijcie RBS do prądu</b>, pamiętajcie, żeby skorzystać z <b>instrukcji</b> i przestrzegać zasad bhp (Ericsson Target Zero)<br>
                              <br>
                              Pozdrawiam, <br>
                              Ryszard Tomka',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 21,
                "avatar" => "img/ryszard_tomka_sq.png",
                "name" => "Admin IT",
                "email" => "ryszard.tomka@example.com",
                "subject" => "Podpięcie RBS na site Ankh Morpork - niezidentyfikowana awaria sprzętu (Finished) ",
                "content" => 'Świetnie! Udało Wam się bezbłędnie podłączyć RBS do sieci elektrycznej.<br>
                              <img src="img/achievement/2_wpiecie_do_sieci_ok.png" style="width:150px; border-radius:50%;"/><br>
                              Pozdrawiam, <br>
                              Ryszard Tomka',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/2_wpiecie_do_sieci_ok.png",
                "achievement_text" => null
            ],
            [
                "id" => 22,
                "avatar" => "img/ryszard_tomka_sq.png",
                "name" => "Admin IT",
                "email" => "ryszard.tomka@example.com",
                "subject" => "Podpięcie RBS na site Ankh Morpork - niezidentyfikowana awaria sprzętu (Finished)",
                "content" => 'RBS został podłączony do sieci elektrycznej, ale odnotowaliśmy niepożądany skok napięcia. Następnym razem pamiętajcie, aby czytać instrukcje! <br>
                              <img src="img/achievement/2_porazenie_pradem_nok.png" style="width:150px; border-radius:50%;"/><br>
                              Pozdrawiam, <br>
                              Ryszard Tomka',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/2_porazenie_pradem_nok.png",
                "achievement_text" => null
            ]
            ],

 "Router_Configuration" => [
            [
                "id" => 30,
                "avatar" => "img/adam_michalczewski_sq.png",
                "name" => "CES",
                "email" => "adam.michalczewski@example.com",
                "subject" => "Router Configuration",
                "content" => 'Kpi dropped for some reason, counters indicate problem with router, please configure it and send us password. Instruction how to configure router should be in the room.<br>
                              <br>
                              Best Regards,<br>
                              Adam Michałczewski',//aby poszukali w pokoju
                "response" => null,
                "expectedResponse" => "5963",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 31,
                "avatar" => "img/adam_michalczewski_sq.png",
                "name" => "CES",
                "email" => "adam.michalczewski@example.com",
                "subject" => "Router Configuration (Finished)",
                "content" => 'KPI were restored, but for some reason people who should not have access have additional privileges. Did you connect firewall? <br>
                              <img src="img/achievement/3_hakier_nok.png" style="width:150px; border-radius:50%;"><br>
                              Best Regards,<br>
                              Adam Michałczewski',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/3_hakier_nok.png",
                "achievement_text" => null
            ],
            [
                "id" => 32,
                "avatar" => "img/adam_michalczewski_sq.png",
                "name" => "CES",
                "email" => "adam.michalczewski@example.com",
                "subject" => "Router Configuration (Finished)",
                "content" => 'KPI were restored, And firewall was properly connected, bravo. <br>
                              <img src="img/achievement/3_firewall_zainstalowany_ok.png" style="width:150px; border-radius:50%;"><br>
                              Best Regards,<br>
                              Adam Michałczewski',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/3_firewall_zainstalowany_ok.png",
                "achievement_text" => null
            ]
            ],

"Network_Configuration" => [
            [
                "id" => 40,
                "avatar" => "img/nc_fd_sq.png",
                "name" => "NC",
                "email" => "nc.frontdesk@example.com",
                "subject" => "System Update",
                "content" => "We've lost contact with multiple RBS in the customer network.<br>

                            Your objectives:<br>
                                <ol type = \"1\" style=\"margin-left: 20px\">
                                <li>Locate missing antenna components hidden on-site.</li>
                                <li>Use the Ankh-Morpork map to correctly reconfigure our programable network (Fill missing spaces on the map with found components.</li>
                                </ol><br>
                                This failure impacts client's operations. <br>
                                Act fast. Restore the link.<br><br>

                            Best Regards,<br>
                            Network Control",

                "response" => null,
                "expectedResponse" => "",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 41,
                "avatar" => "img/adam_michalczewski_sq.png",
                "name" => "CES",
                "email" => "michal.adamczewski@example.com",
                "subject" => "Re: System Update",
                "content" => "Wygląda na to, że sieć została skonfigurowana. Natomiast, aby zatwierdzić zmiany potrzebujemy hasła.<br>
                              Poprzednik technik wspominał, że hasło jest ukryte w światłowodzie. Ale niestety nie wiem o co mu chodziło.<br>
                              Poszukaj wskazówek w pokoju i prześlij mi hasło.<br>
                            <br>
                            Best Regards,<br>
                            Michał Adamczewski",

                "response" => null,
                "expectedResponse" => "will",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 42,
                "avatar" => "img/adam_michalczewski_sq.png",
                "name" => "CES",
                "email" => "adam.michalczewski@example.com",
                "subject" => "System Update (Finished)",
                "content" => 'Brawo! Hasło, które mi podaliście jest poprawne.<br>
                            <img src="img/achievement/4_swiatelka_ok.jpg" style="width:150px; border-radius:50%;"><br>
                            Best Regards,<br>
                            Network Control',
                "response" => null,
                "expectedResponse" => "",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/4_swiatelka_ok.jpg",
                "achievement_text" => null
            ]
            ],

            "Password Needed" => [
            [
                "id" => 80,
                "avatar" => "img/sfetan_bafy_sq.png",
                "name" => "Sfetan Bafy",
                "email" => "sfetan.Bafy@erikson.kom",
                "subject" => "Potrzebujemy hasła adamina",
                "content" => 'Potrzebujemy hasła admina do sieci - klient pilnie potrzebuje zmian konfiguracji, powinno być gdzies na poskładanej kartce papieru (origami). <br>
                                <br>
                                Pozdrawiam,<br>
                                Sfetan Bafy',
                "response" => null,
                "expectedResponse" => "Haslo123!!!",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],

            //to do zgloszenie spamu

            [
                "id" => 81,
                "avatar" => "img/sfetan_bafy_sq.png",
                "name" => "Sfetan Bafy",
                "email" => "向前: sfetan.Bafy@erikson.kom, everyone@weihua.cn",
                "subject" => "Potrzebujemy hasła (Finished)",
                "content" => 'Udało się zalogować, dzięki. 谢谢<br>
                              <img src="img/achievement/8_jestes_zrybiony_nok.png" style="width:150px; border-radius:50%;"><br>
                                Pozdrawiam 此致,<br>
                                Sfetan Bafy',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "external",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/8_jestes_zrybiony_nok.png",
                "achievement_text" => null
            ],
        ],

            "Find Lms" => [
            [
                "id" => 50,
                "avatar" => "img/stefan_baty_sq.png",
                "name" => "Stefan Baty",
                "email" => "stefan.baty@ericsson.com",
                "subject" => "Odszukajcie i załadujcie UPki na IUWUU",
                "content" => 
                            "Node jest już widoczny w naszych narzędziach do zarządzania siecią, ale nie możemy manualnie przeinstalować UP. Znajdzcie LMy ukryte w pokoju i zainstalujcie je na zgodnie z instrukcją na prototypie tajnego, wewnętrznego urządzenia Innovative Unofficial Wireless UP Uploader (IUWUU), pieszczotliwie nazywanego Yellow Box.
                            <br><br>
                            Pozdrawiam,<br>
                            Stefan Baty
                            ",
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 51,
                "avatar" => "img/mikolaj_fajans_sq.png",
                "name" => "Mikołaj Fajans",
                "email" => "mikolaj.fajans@ericsson.com",
                "subject" => "Re: Odszukajcie i załadujcie UPki na IUWUU",
                "content" => 'Seems that LM installation failed. Please make sure you follow the instruction. Ask technical support for help, it is urgent.<br>
                             <img src="img/achievement/5_zly_niklas_nok.png" style="width:150px; border-radius:50%;"><br>
                             Best Regards,<br>
                             Mikołaj Fajans<br>
                             ',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/5_zly_niklas_nok.png",
                "achievement_text" => null
            ],
            [
                "id" => 52,
                "avatar" => "img/stefan_baty_sq.png",
                "name" => "Stefan Baty",
                "email" => "stefan.baty@ericsson.com",
                "subject" => "Odszukajcie i załadujcie UPki na IUWUU (Finished)",
                "content" => 'IUWUU zwrócił informację, że LM są wgrane i UP jest gotowy do instalacji. Czekajcie na następne instrukcje przed podejmowaniem jakichkolwiek akcji.<br> 
                            <img src="img/achievement/5_prawidlowe_lmy_ok.jpg" style="width:150px; border-radius:50%;"><br>
                            Pozdrawiam,<br>
                            Stefan Baty
                            ',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/5_prawidlowe_lmy_ok.jpg",
                "achievement_text" => null
            ],

            ],
            
            "Install UPs" => [
            [
                "id" => 60,
                "avatar" => "img/stefan_baty_sq.png",
                "name" => "Stefan Baty",
                "email" => "stefan.baty@ericsson.com",
                "subject" => " Wgranie UP na node",
                "content" => "Czas na finalny krok. Włożcie IUWUU do RBS, IUWUU automatycznie wgra i zainstaluje UP, po czym puści testy. Damy Wam znać o jak wyszedł health check.<br>
                             <br>
                             Pozdrawiam,<br>
                             Stefan Baty",
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 61,
                "avatar" => "img/stefan_baty_sq.png",
                "name" => "Stefan Baty",
                "email" => "stefan.baty@ericsson.com",
                "subject" => "Wgranie UP na node(Finished)",
                "content" => 'UP załadował się poprawnie. Checki trwają, niedługo dostaniecie raport.<br>
                             <img src="img/achievement/iuwuu_w_rbs_gotowe.png" style="width:150px; border-radius:50%;"><br>
                             Pozdrawiam,<br>
                             Stefan Baty"
                             ',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/iuwuu_w_rbs_gotowe.png",
                "achievement_text" => null
            ]
            ],
            "RBS Overheat" => [
            [
                "id" => 70,
                "avatar" => "img/jack_lin_sq.png",
                "name" => "Jack Lin",
                "email" => 'jack.lin@ericsson.com<br>
			    CC: "mikolaj.fajans@ericsson.com, stefan.baty@ericsson.com',
                "subject" => "OVERHEAT alarm!!!",
                "content" => 'Enable troubleshooting mode and answer a series of questions to find the cause. The issue has been escalated, so you have limited time.
                              To begin, press the button below<BR> <a href="awaria.php" class="restart-button">Troubleshooting Emergency Mode</a>
                              <br>
                              Best Regards,<br>
                              Jack Lin<br>',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => null,
                "achievement_text" => null
            ],
            [
                "id" => 71,
                "avatar" => "img/jack_lin_sq.png",
                "name" => "Jack Lin",
                "email" => 'jack.lin@ericsson.com<br>
			    CC: "mikolaj.fajans@ericsson.com, stefan.baty@ericsson.com',
                "subject" => "OVERHEAT alarm!!!(Finished)",
                "content" => 'Congratulations, you saved the node from destruction and located the root cause and saved Monitor Lizard from the fire. <br>
			                  <img src="img/achievement/7_monitor_lizard_zyw_ok.png" style="width:150px; border-radius:50%;">
                              <br>
                              Best Regards,<br>
                              Jack Lin<br>',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/7_monitor_lizard_zyw_ok.png",
                "achievement_text" => null
            ],
            [
                "id" => 72,
               "avatar" => "img/jack_lin_sq.png",
                "name" => "Jack Lin",
                "email" => 'jack.lin@ericsson.com<br>
			    CC: "mikolaj.fajans@ericsson.com, stefan.baty@ericsson.com',

                "subject" => "OVERHEAT alarm!!!(Finished)",
                "content" => 'Unfortunately, the RBS has burned down and so has the Monitor Lizard that nested inside causing the problems.
                              We will send new RBS to the client, and face the consequences of this failure.<br>
                              <img src="img/achievement/7_weglan_rbsu_nok.png" style="width:150px; border-radius:50%;"><br>
                              Best Regards,<br>
                              Jack Lin<br>
                              ',
                "response" => null,
                "expectedResponse" => "???",
                "solutionType" => "internal",
                "visable" => false,
                "previous_ids" => "",
                "achievement" => "img/achievement/7_weglan_rbsu_nok.png",
                "achievement_text" => null
            ]
            ]
            ];


    //insert data
    $stmt = $db->prepare("INSERT INTO emails 
        (id, avatar, name, email, subject, content, response, expectedResponse, solutionType, category, visable, previous_ids, achievement, achievement_text)
        VALUES (:id, :avatar, :name, :email, :subject, :content, :response, :expectedResponse, :solutionType, :category, :visable, :previous_ids, :achievement, :achievement_text)");

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
            $stmt->bindValue(':achievement',  $email['achievement'], SQLITE3_TEXT);
            $stmt->bindValue(':achievement_text',  $email['achievement_text'], SQLITE3_TEXT);
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

function getAllDeviceState($db) {
    $result = $db->prepare("SELECT * FROM devices")->execute();
    $devices = [];

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $devices[] = $row;
    }

    return $devices;
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
            // $teamName = $splitValues[1];
            
            // if (setTeamName($db, $teamName)) {
            //     // setVisableById($db, 12); //!!! Trigger next mail.
            //     // setVisableById($db, 13); //!!! Trigger next mail.
            //     // setVisableById($db, 30); //!!! Trigger next mail.
            //     // setVisableById($db, 40); //!!! Trigger next mail.
            //     echo json_encode(['line1' => "Authentication", 'line2' => $teamName]);
            // }
            echo "team name not working";
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
            setVisableById($db, 52);
            setVisableById($db, 60);

            setPuzzleData('card', 'completed', (string)$nextIndex);
            return ['ok' => true, 'message' => "rpc installed"];
        } else {
            setPuzzleData('card', 'in_progress', (string)$nextIndex);
            return ['ok' => true, 'message' => "Installing {$expected}"];
        }
    } else {
        // mail poganiajacy ze cos sie nie udalo
        setVisableById($db, 51);

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
            
            // setVisableById($db, 13); //!!! Trigger next mail.
            // setVisableById($db, 30); //!!! Trigger next mail.
            // setVisableById($db, 40); //!!! Trigger next mail.

            setVisableById($db, 12);
            setVisableById($db, 13);
            setVisableById($db, 20);
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
        case "cabinet":
            //mozliwe ze bedzie do zrobienia sprawdzenie
            setVisableById($db, 61);
            setVisableById($db, 70);
            echo "cabinet";
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
        case "reset":
            updateDeviceState($db, "lamp", "idle");
            echo "reset";
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
            if (getDeviceState($db, "lamp") != "done")
            {
                updateDeviceState($db, "lamp", "shine");
                setVisableById($db, 41); //mail lampa
            }

            break;
        case "disconnected":
            if (getDeviceState($db, "lamp") != "done")
            {
                updateDeviceState($db, "lamp", "idle");
            }
            //stop music
            //stop light
            break;
        case "alive":
            //log to database
            //should i add it to database?
            //updateDeviceTime($db, "lamp");
            echo "alive";
            break; 
        case "reset":
            updateDeviceState($db, "lamp", "idle");
            break;
        default:
            echo "defult action";
            break;
    }

}

function handleMailboxPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "30":
            //router task
            //router check if router was properly connected
            if (getDeviceState($db, "router") === "firewall_connected")
            {
                setVisableById($db, 32);
                updateDeviceState($db, "rbs", "connected");
                updateDeviceState($db, "router", "done");
                echo "firewall connected";
            } 

            if( getDeviceState($db, "router") === "firewall_disconnected")
            {
                setVisableById($db, 31);
                updateDeviceState($db, "rbs", "connected");
                updateDeviceState($db, "router", "done");
                echo "firewall connected";
            }

            if(getDeviceState($db, "router") === "done" && getDeviceState($db, "lamp") === "done")
            {
                setVisableById($db, 50);
            }

            echo "mailbox 30";
            break;
        case "41":
            updateDeviceState($db, "lamp", "done");
            setVisableById($db, 42);//zakonczenie lampy, will
            if(getDeviceState($db, "router") === "done" && getDeviceState($db, "lamp") === "done")
            {
                setVisableById($db, 50);
            }

            echo "mailbox 30";
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
        case "power_ok":

            if (getDeviceState($db, "power_connector") === "power_nok")
            {
                echo "power alredy power_nok";
            }
            updateDeviceState($db, "power_connector", "power_ok");

            updateDeviceState($db, "rbs", "plugged");
            
            setVisableById($db, 21);
            //start 2 topics
            setVisableById($db, 30);
            setVisableById($db, 40);

            echo "power_ok";
            break;
        case "power_nok":
            updateDeviceState($db, "power_connector", "power_nok");

            updateDeviceState($db, "rbs", "plugged");

            setVisableById($db, 22);
            setVisableById($db, 30);
            setVisableById($db, 40);

            echo "power_nok";
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

function handleSkyfallPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "reset":
        case "idle":
            updateDeviceState($db, "skyfall", "idle");
            echo "idle";
            break;
        case "drop":
            //Todo probably to be removed
            updateDeviceState($db, "skyfall", "drop");
            echo "drop";
            break;
        case "alive":
            updateDeviceTime($db, "skyfall");
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

function handleScriptPost($db, $data) {
    $values = $data['value'];
    $splitValues = explode(" ", $values);
    switch ($splitValues[0]) {
        case "start_burning":
            updateDeviceState($db, "rbs", "burning");
            echo "start_burning";
            break;
        case "weglan_lizard":
            updateDeviceState($db, "rbs", "weglan_lizard");
            setVisableById($db, 72);
            echo "weglan_lizard";
            break;
        case "monitor_lizard":
            updateDeviceState($db, "rbs", "monitor_lizard");
            setVisableById($db, 71);
            echo "monitor_lizard";
            break;
        default:
            echo "default script post";
            break;
    }

}


// POST — add a message
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // if (!isset($data['action'])) {
    //     http_response_code(400);
    //     echo "Missing field: action";
    //     exit;
    // }

    // Check for 'action' or 'device' in the decoded JSON
    $response = [];
    
    if (isset($data['action'])) {
        $response['field'] = 'action';
        $response['device'] = $data['action'];
    } elseif (isset($data['device'])) {
        $response['field'] = 'device';
        $response['device'] = $data['device'];
    } else {
        $response['error'] = 'Neither action nor device field found.';
    }

    switch ($response['device']) {
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
        case "skyfall":
            handleSkyfallPost($db, $data);
            break;
        case "script":
            handleScriptPost($db, $data);
            break;
        default:
            echo "action or device not recognised";
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
    
    $rbs = isset($_GET['device']) && $_GET['device'] === 'skyfall';
    if($rbs)
    {
        echo getDeviceState($db, "skyfall");;
        return;
    }

    $deviceAll = isset($_GET['device']) && $_GET['device'] === 'all';
    if($deviceAll)
    {
        header('Content-Type: application/json');
        echo json_encode(getAllDeviceState($db));;
        return;
    }

    $all = isset($_GET['all']) && $_GET['all'] === 'true';

    // If the 'all' parameter is not set, only select messages marked as visable
    if ($all) {
        // Get all messages (ignore 'visable' state)
        $results = $db->query("SELECT * FROM emails ORDER BY id");
    } else {
        // Get only messages with visable = 1
        $results = $db->query("SELECT * FROM emails WHERE visable = 1 ORDER BY id");
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
