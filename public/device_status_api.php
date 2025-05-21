<?php
header('Content-Type: application/json');

$db = new SQLite3('database.sqlite');
$db->exec("CREATE TABLE IF NOT EXISTS devices (name TEXT PRIMARY KEY, last_seen INTEGER)");
$result = $db->query("SELECT name, last_seen FROM devices");

$devices = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $devices[] = $row;
}

echo json_encode($devices);
?>