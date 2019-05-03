<?php
// 1日分の miniblog を html として書き出します。
$title = "カメネギのブログ";

$miniblog = json_decode(file_get_contents($argv[2]), true);

// 該当データを抽出
$logs = array_filter($miniblog, function ($log) use ($argv) {
    return (new DateTime($log['date']))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y-m-d') == $argv[5];
});

// ソート
usort($logs, function ($log1, $log2) {
    return (new DateTime($log2['date']))->getTimestamp() <=> (new DateTime($log1['date']))->getTimestamp();
});

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
    $article_title = (new DateTime($argv[5]))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y年m月d日');
} else {
    $article_title = $argv[5];
}
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $article_title; ?> - <?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="stylesheet" href="main.css">
  </head>
  <body>
    <article>
      <h1><?php echo $title; ?></h1>
      <h2><?php echo $article_title; ?></h2>

      <?php foreach ($logs as $log): ?>
        <section>
          <div class="date"><?php echo $log['date']; ?></div>
          <p><?php echo $log['text']; ?></p>
        </section>
      <?php endforeach; ?>

      <?php if (true): ?><a href="<?php echo $argv[3]; ?>.html">前の記事</a>&emsp;<?php endif; ?>
      <?php if (true): ?><a href="<?php echo $argv[4]; ?>.html">次の記事</a>&emsp;<?php endif; ?>
      <br>
      <br>
      <?php
/*
      <h3>タグ一覧</h3>
      <?php foreach ($tags as $tag => $value): ?>
        <a href="<?php echo $tag; ?>.html" style="display: inline-block; "><?php echo $tag; ?></a>&nbsp;
      <?php endforeach; ?>
      <br>
      <h3>日付一覧</h3>
      <?php foreach ($days as $day => $value): ?>
        <a href="<?php echo $day; ?>.html" style="display: inline-block; "><?php echo (new DateTime($day))->format(
    'Y年m月d日'
); ?></a>&nbsp;
      <?php endforeach; ?>
*/
?>
    </article>
    <footer>
      <?php echo date('Y'); ?> BlueHood<br>
      <a href="https://github.com/blue-hood/twitter-miniblog" target="_blank">Published</a> under the MIT license.
    </footer>
  </body>
</html>
