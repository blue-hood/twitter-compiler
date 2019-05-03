<?php
// miniblog に含まれる年月日を取得します。

$miniblog = json_decode(file_get_contents($argv[1]), true);

// ソート
usort($miniblog, function ($log1, $log2) {
    return (new DateTime($log2['date']))->getTimestamp() <=> (new DateTime($log1['date']))->getTimestamp();
});

// 年月日を抽出
$days = [];
foreach ($miniblog as $log) {
    $ymd = (new DateTime($log['date']))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y-m-d');
    $days[$ymd] = true;
}

foreach ($days as $day => $value) {
    echo "${day}\n";
}
?>
