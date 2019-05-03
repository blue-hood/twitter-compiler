#!/bin/bash

# 前回のファイル削除
rm html/*.html html/videos/* html/images/* 2> /dev/null

# miniblog 変換
php compile.php > miniblog/twitter.json

# タグページ生成
#tags=(`php tags.php miniblog/twitter.json`)
#for tag in ${tags[@]}
#do
#  echo ${tag}
#  php page.php tag miniblog/twitter.json ${tag} > html/${tag}.html
#done

# 日毎ページ生成
days=(`php days.php miniblog/twitter.json`)
for ((i = 0; i < ${#days[@]}; i++))
do
  echo ${days[$i]}

  next=${days[($i + 1)%${#days[@]}]}
  php page.php day miniblog/twitter.json ${days[$i - 1]} ${next} ${days[$i]} > html/${days[$i]}.html
done

# index.html のシンボリックリンク生成
ln -s ${days[0]}.html html/index.html
