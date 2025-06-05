<?php
$db = new SQLite3('database.sqlite');

// 1. Pobierz unikalne kategorie z tabeli
$categoriesResult = $db->query("SELECT DISTINCT category FROM emails");

// Tablica na wszystkie kategorie
$categories = [];
while ($catRow = $categoriesResult->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $catRow['category'];
}

// 2. Dla każdej kategorii sprawdź czy jest widoczny achievement
$categoriesWithoutAchievement = 0;
foreach ($categories as $category) {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM emails WHERE category = :category AND visable = 1 AND achievement IS NOT NULL AND achievement != ''");
    $stmt->bindValue(':category', $category, SQLITE3_TEXT);
    $res = $stmt->execute();
    $row = $res->fetchArray(SQLITE3_ASSOC);

    if ($row['count'] == 0) {
        // ta kategoria nie ma żadnego widocznego achievementa
        $categoriesWithoutAchievement++;
    }
}

// 3. Pobierz widoczne achievementy
$results = $db->query("SELECT achievement, achievement_text FROM emails WHERE visable = 1 AND achievement IS NOT NULL AND achievement != '' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Osiągnięcia</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      background-color: #fff;
    }
    .achievement-entry {
      display: flex;
      align-items: center;
      border-bottom: 1px solid #ddd;
      padding: 10px 0;
    }
    .achievement-entry img {
      max-width: 100px;
      height: auto;
      margin-right: 15px;
      border-radius: 6px;
    }
    .achievement-text {
      font-size: 14px;
      color: #333;
    }
    .summary {
      margin-bottom: 20px;
      font-weight: bold;
      font-size: 16px;
      color: #222;
    }
  </style>
</head>
<body>

  <p class="summary">
    <?php if ($categoriesWithoutAchievement == 0): ?>
      Rozwiązaliście wszystkie zadania
    <?php else: ?>
      Nadal nie rozwiązaliście dodatkowych zadań :<?php echo $categoriesWithoutAchievement; ?>
    <?php endif; ?>
  </p>

  <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)): ?>
    <div class="achievement-entry">
      <img src="<?php echo htmlspecialchars($row['achievement']); ?>" alt="Osiągnięcie">

      <?php if (!empty($row['achievement_text'])): ?>
        <div class="achievement-text">
          <?php echo htmlspecialchars($row['achievement_text']); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>

</body>
</html>