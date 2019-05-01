<?php
// 1日分の miniblog を html として書き出します。

$miniblog = json_decode(file_get_contents($argv[1]), true);

// 指定の年月日を抽出
$miniblog = array_filter($miniblog, function ($log) use ($argv) {
    $ymd = (new DateTime($log['date']))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y-m-d');
    return $ymd == $argv[2];
});

$date = (new DateTime($argv[2]))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y年m月d日');
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Miniblog <?php echo $date; ?></title>
    <link rel="stylesheet" href="main.css">
  </head>
  <body>
    <?php foreach ($miniblog as $log): ?>
      <section>
        <div class="date"><?php echo $log['date']; ?></div>
        <p><?php echo $log['text']; ?></p>
      </section>
    <?php endforeach; ?>
  </body>
</html>
