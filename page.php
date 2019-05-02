<?php
// 1日分の miniblog を html として書き出します。

$miniblog = json_decode(file_get_contents($argv[2]), true);

// 該当データと全年月日、全タグを抽出
$logs = [];
$days = [];
$tags = [];
unset($next);
foreach ($miniblog as $log) {
    $ymd = (new DateTime($log['date']))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y-m-d');
    $days[$ymd] = true;

    foreach ($log['tags'] as $tag) {
        $tags[$tag] = true;
    }

    if ($argv[1] == 'day') {
        // 日毎ページ

        switch ((new DateTime($argv[3]))->getTimestamp() <=> (new DateTime($ymd))->getTimestamp()) {
            case -1:
                $prev = $ymd;
                break;
            case 0:
                $logs[] = $log;
                break;
            case 1:
                if (!isset($next)) {
                    $next = $ymd;
                }
                break;
        }
    } else {
        // タグページ

        foreach ($log['tags'] as $tag) {
            if ($tag == $argv[3]) {
                $logs[] = $log;
                break;
            }
        }
    }
}

foreach ($logs as $index => &$log) {
    // 日時フォーマット変換
    $log['date'] = (new DateTime($log['date']))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('H時');

    // 同じ時間は統合する
    if (isset($prevlog) && $log['date'] == $prevlog['date']) {
        $prevlog['text'] .= "<br>{$log['text']}";
        unset($logs[$index]);
        continue;
    }

    $prevlog = &$log;
}
unset($log);
unset($prevlog);

if ($argv[1] == 'day') {
    $title = (new DateTime($argv[3]))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y年m月d日');
} else {
    $title = $argv[3];
}
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?> - カメネギのブログ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="main.css">
  </head>
  <body>
    <article>
      <h1>カメネギのブログ（仮）</h1>
      <h2><?php echo $title; ?></h2>

      <?php foreach ($logs as $log): ?>
        <section>
          <div class="date"><?php echo $log['date']; ?></div>
          <p><?php echo $log['text']; ?></p>
        </section>
      <?php endforeach; ?>

      <?php if (isset($prev)): ?><a href="<?php echo $prev; ?>.html">次の記事</a>&emsp;<?php endif; ?>
      <?php if (isset($next)): ?><a href="<?php echo $next; ?>.html">前の記事</a>&emsp;<?php endif; ?>
      <br>
      <br>
      <?php
/*
      <h3>タグ一覧</h3>
      <?php foreach ($tags as $tag => $value): ?>
        <a href="<?php echo $tag; ?>.html" style="display: inline-block; "><?php echo $tag; ?></a>&nbsp;
      <?php endforeach; ?>
      <br>*/
?>
      <h3>日付一覧</h3>
      <?php foreach ($days as $day => $value): ?>
        <a href="<?php echo $day; ?>.html" style="display: inline-block; "><?php echo (new DateTime($day))->format(
    'Y年m月d日'
); ?></a>&nbsp;
      <?php endforeach; ?>
    </article>
    <footer>
      <?php echo date('Y'); ?> BlueHood<br>
      <a href="https://github.com/blue-hood/twitter-miniblog" target="_blank">Published</a> under the MIT license.
    </footer>
  </body>
</html>
