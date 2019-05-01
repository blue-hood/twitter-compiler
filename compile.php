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

    // 改行
    $tweet['full_text'] = nl2br($tweet['full_text']);

    // URL を変換
    foreach ($tweet['entities']['urls'] as $url) {
        $tweet['full_text'] = str_replace(
            $url['url'],
            "<a href=\"{$url['expanded_url']}\" target=\"_blank\">" . htmlspecialchars($url['display_url']) . "</a>",
            $tweet['full_text']
        );
    }

    // 画像を変換
    if (!empty($tweet['extended_entities']['media'])) {
        foreach ($tweet['extended_entities']['media'] as $media) {
            if ($media['type'] == 'photo') {
                $basename = $tweet['id_str'] . '-' . basename($media['media_url_https']);
                $image = file_get_contents("data/tweet_media/${basename}");
                $tweet['full_text'] = str_replace(
                    $media['url'],
                    "<img src=\"data:image/" .
                        pathinfo($basename, PATHINFO_EXTENSION) .
                        ";base64," .
                        base64_encode($image) .
                        "\">",
                    $tweet['full_text']
                );
            }
        };
    }
}
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <?php foreach ($tweets as $tweet): ?>
      <section>
        <div><?php echo $tweet['created_at']; ?></div>
        <p><?php echo $tweet['full_text']; ?></p>
      </section>
    <?php endforeach; ?>
  </body>
</html>
