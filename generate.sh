#!/bin/bash

# 前回のファイル削除
rm html/*.html html/videos/* html/images/* 2> /dev/null

# miniblog 変換
php compile.php > miniblog/twitter.json

# 日毎ページ生成
for day in `php days.php miniblog/twitter.json`
do
  php day.php miniblog/twitter.json ${day} > html/${day}.html
done
