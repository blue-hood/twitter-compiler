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
    $is_reply =
        !empty($tweet['in_reply_to_user_id_str']) &&
        $tweet['in_reply_to_user_id_str'] != '904984308967923712' /*自分のID*/;
    if (!empty($tweet['entities']['user_mentions']) || $is_reply) {
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

    if (!empty($tweet['extended_entities']['media'])) {
        foreach ($tweet['extended_entities']['media'] as $media) {
            if ($media['type'] == 'photo') {
                // 画像
                $basename = $tweet['id_str'] . '-' . basename($media['media_url_https']);
                copy("data/tweet_media/${basename}", "html/images/${basename}");
                $tweet['full_text'] = str_replace(
                    $media['url'],
                    "<img src=\"images/${basename}\">",
                    $tweet['full_text']
                );
            } elseif ($media['type'] == 'video') {
                // ビデオ
                $variant_index = 0;
                foreach ($media['video_info']['variants'] as $index2 => $variant) {
                    // mp4 形式のみ
                    if ($variant['content_type'] != "video/mp4") {
                        continue;
                    }

                    if (!isset($media['video_info']['variants'][$variant_index]['bitrate'])) {
                        $variant_index = $index2;
                        continue;
                    }

                    // ビットレートが低いものを使用する
                    if ($media['video_info']['variants'][$variant_index]['bitrate'] > $variant['bitrate']) {
                        $variant_index = $index2;
                        continue;
                    }
                }

                $basename =
                    $tweet['id_str'] .
                    '-' .
                    preg_replace('/\?(.*)/', '', basename($media['video_info']['variants'][$variant_index]['url']));
                copy("data/tweet_media/${basename}", "html/videos/${basename}");
                $tweet['full_text'] = str_replace(
                    $media['url'],
                    "<video src=\"videos/${basename}\" controls autoplay loop muted></video>",
                    $tweet['full_text']
                );
            }
        }
    }
}
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Twitter Compiler</title>
    <link rel="stylesheet" href="main.css">
  </head>
  <body>
    <?php foreach ($tweets as $index => $tweet): ?>
      <section>
        <div class="date"><?php echo $tweet['created_at']; ?></div>
        <p><?php echo $tweet['full_text']; ?></p>
      </section>
    <?php endforeach; ?>
  </body>
</html>
