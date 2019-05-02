<?php
// miniblog に含まれるタグを取得します。

$miniblog = json_decode(file_get_contents($argv[1]), true);

// タグを抽出
$tags = [];
foreach ($miniblog as $log) {
    foreach ($log['tags'] as $tag) {
        $tags[$tag] = true;
    }
}

foreach ($tags as $tag => $value) {
    echo "${tag}\n";
}
?>
