<?php
// 1日分の miniblog を html として書き出します。

$timestamp = (new DateTime($argv[2]))->getTimestamp();
$miniblog = json_decode(file_get_contents($argv[1]), true);

// 該当データを抽出
$logs = [];
foreach ($miniblog as $log) {
    $ymd = (new DateTime($log['date']))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y-m-d');

    switch ($timestamp <=> (new DateTime($ymd))->getTimestamp()) {
        case -1:
            $prev = $ymd;
            break;
        case 0:
            $logs[] = $log;
            break;
        case 1:
            $next = $ymd;
            break 2;
    }
}

foreach ($logs as &$log) {
    // 日時フォーマット変換
    $log['date'] = (new DateTime($log['date']))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('H時');
}
unset($log);

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
    <article>
      <?php foreach ($logs as $log): ?>
        <section>
          <div class="date"><?php echo $log['date']; ?></div>
          <p><?php echo $log['text']; ?></p>
        </section>
      <?php endforeach; ?>
    </article>

    <?php if (isset($prev)): ?><a href="<?php echo $prev; ?>.html">前の記事</a><?php endif; ?>
    <?php if (isset($next)): ?><a href="<?php echo $next; ?>.html">次の記事</a><?php endif; ?>
  </body>
</html>
