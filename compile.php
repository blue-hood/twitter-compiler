<?php
$tweets = json_decode(file_get_contents('data/tweet.json'), true);

// ソート
usort($tweets, function ($tweet_1, $tweet_2) {
    return (new DateTime($tweet_2['created_at']))->getTimestamp() <=>
        (new DateTime($tweet_1['created_at']))->getTimestamp();
});

// フィルタ
foreach ($tweets as $index => &$tweet) {
    // リツイートを除去
    if (preg_match('/^RT/', $tweet['full_text']) === 1) {
        unset($tweets[$index]);
        continue;
    }

    // @ツイートを除去
    if (!empty($tweet['entities']['user_mentions'])) {
        unset($tweets[$index]);
        continue;
    }

    // 時間フォーマットを変換
    $tweet['created_at'] = (new DateTime($tweet['created_at']))
        ->setTimeZone(new DateTimeZone('Asia/Tokyo'))
        ->format('Y年m月d日 H時');
}
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <?php foreach ($tweets as $tweet): ?>
      <div>
        <div><?php echo $tweet['created_at']; ?></div>
        <p><?php echo $tweet['full_text']; ?></p>
      </div>
    <?php endforeach; ?>
  </body>
</html>
