#!/bin/bash

if [ -z "$PWD" ]; then
    echo -e "\e[0;31mPWD is not set."
    exit 1
fi

git config user.name "discuz-bot"
git config user.email "171550539@qq.com"

git add src/* -f
git commit -m "php-cs-fixer output for commit $GITHUB_SHA [Skip ci]"

OUT="$(git push https://"$ACTOR":"$PWD"@github.com/"$GITHUB_REPOSITORY".git HEAD:"$GITHUB_REF" 2>&1 > /dev/null)"

echo "$OUT"
