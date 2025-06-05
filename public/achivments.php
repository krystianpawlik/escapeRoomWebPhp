<?php
function getVisibleSolvedEmails() {
    $db = new SQLite3('database.sqlite');

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

    $query = "SELECT name, email, subject, content FROM emails WHERE visable = 1 AND subject LIKE '%Solved%'";
    $result = $db->query($query);

    $emails = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $emails[] = $row;
    }

    return $emails;
}

$emails = getVisibleSolvedEmails();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Widoczne rozwiązane maile</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 0px}
        .email-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .email-card h2 { margin: 0 0 10px; font-size: 1.2em; }
        .email-card .subject { font-weight: bold; margin-bottom: 10px; }
        .email-card .content { white-space: pre-wrap; }
        .refresh-button {
            margin-bottom: 30px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background-color: #004999;
        }
    </style>
</head>
<body>
    <h1>Osiągnięcia</h1>

    <div class="refresh-button">
        <form method="GET">
            <button type="submit">🔄 Odśwież</button>
        </form>
    </div>

    <?php if (count($emails) === 0): ?>
        <p>Brak osiągnięć</p>
    <?php else: ?>
        <?php foreach ($emails as $email): ?>
            <div class="email-card">
                <h2><?php echo htmlspecialchars($email['name']); ?> &lt;<?php echo htmlspecialchars($email['email']); ?>&gt;</h2>
                <div class="subject"><?php echo htmlspecialchars($email['subject']); ?></div>
                <div class="content"><?php echo $email['content']; ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>