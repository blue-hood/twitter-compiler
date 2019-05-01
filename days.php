<?php
// miniblog に含まれる年月日を取得します。

$miniblog = json_decode(file_get_contents($argv[1]), true);

// 年月日を抽出
$days = [];
foreach ($miniblog as $log) {
    $ymd = (new DateTime($log['date']))->setTimezone(new DateTimeZone('Asia/Tokyo'))->format('Y-m-d');
    $days[$ymd] = true;
}

foreach ($days as $key => $value) {
    echo "${key}\n";
}
?>
