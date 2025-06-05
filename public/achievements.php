<?php
$db = new SQLite3('database.sqlite');
$results = $db->query("SELECT achievement, achievement_text FROM emails WHERE visable = 1 AND achievement IS NOT NULL ORDER BY id DESC");
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
      padding: 10px;
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
  </style>
</head>
<body>

  <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)): ?>
    <?php if (!empty($row['achievement'])): ?>
      <div class="achievement-entry">
        <img src="<?php echo htmlspecialchars($row['achievement']); ?>" alt="Osiągnięcie">

        <?php if (!empty($row['achievement_text'])): ?>
          <div class="achievement-text">
            <?php echo htmlspecialchars($row['achievement_text']); ?>
          </div>
        <?php endif; ?>

      </div>
    <?php endif; ?>
  <?php endwhile; ?>

</body>
</html>