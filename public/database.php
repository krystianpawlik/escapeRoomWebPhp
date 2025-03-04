<?php
// Połączenie z bazą danych SQLite
$db_file = 'emails.db';
$db = new SQLite3($db_file);

// Tworzenie tabeli, jeśli nie istnieje
$query_create_table = "
CREATE TABLE IF NOT EXISTS emails (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL,
    subject TEXT NOT NULL,
    content TEXT NOT NULL,
    category TEXT NOT NULL
);";
$db->exec($query_create_table);

// Dodanie przykładowych danych (jeśli tabela jest pusta)
$query_check_data = "SELECT COUNT(*) as count FROM emails";
$result = $db->querySingle($query_check_data, true);

if ($result['count'] == 0) {
    $query_insert_data = "
    INSERT INTO emails (email, subject, content, category) VALUES
    ('example1@example.com', 'General Email 1', 'Content of general email 1.', 'General'),
    ('example2@example.com', 'General Email 2', 'Content of general email 2.', 'General'),
    ('example3@example.com', 'Announcement 1', 'Details about announcement 1.', 'Announcements'),
    ('example4@example.com', 'Announcement 2', 'Details about announcement 2.', 'Announcements'),
    ('example5@example.com', 'Support Ticket 1', 'Response to support ticket 1.', 'Support'),
    ('example6@example.com', 'Support Ticket 2', 'Response to support ticket 2.', 'Support'),
    ('example7@example.com', 'Feedback 1', 'User feedback 1.', 'Feedback'),
    ('example8@example.com', 'Feedback 2', 'User feedback 2.', 'Feedback');
    ";
    $db->exec($query_insert_data);
}

//every request add one 
$query_insert_data = "INSERT INTO emails (email, subject, content, category) VALUES ('example1@example.com', 'General Email 1', 'Content of general email 1.', 'General');";
$db->exec($query_insert_data);


// Pobieranie wszystkich e-maili, pogrupowanych według kategorii
$query = "SELECT category, json_group_array(
               json_object(
                   'subject', subject,
                   'content', content
               )
           ) AS emails_json
           FROM emails
           GROUP BY category";

// Wykonanie zapytania
$categories = $db->query($query);

// Przygotowanie wynikowego JSON
$result_data = [];

while ($row = $categories->fetchArray(SQLITE3_ASSOC)) {
    $result_data[$row['category']] = json_decode($row['emails_json']);
}

// Zamknięcie połączenia
$db->close();

// Wyświetlanie wyników w formacie JSON
echo json_encode($result_data, JSON_PRETTY_PRINT);
?>