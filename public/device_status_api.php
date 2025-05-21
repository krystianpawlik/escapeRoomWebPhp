<?php
header('Content-Type: application/json');

$db = new SQLite3('database.sqlite');

// Tworzymy tabelę z kolumną state (jeśli nie istnieje)
$db->exec("
    CREATE TABLE IF NOT EXISTS devices (
        name TEXT PRIMARY KEY,
        last_seen INTEGER,
        state TEXT DEFAULT 'idle'
    )
");

// (Opcjonalnie) ustawiamy domyślny stan na 'idle' dla istniejących rekordów, które mają NULL w state
$db->exec("UPDATE devices SET state = 'idle' WHERE state IS NULL");

$result = $db->query("SELECT name, last_seen, state FROM devices");

$devices = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $devices[] = $row;
}

echo json_encode($devices);