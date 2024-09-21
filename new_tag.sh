#!/bin/bash

# 获取最后一个版本号
lastTag=$(git describe --tags --abbrev=0)

# 提取版本号中的数字部分并递增
IFS='.' read -r major minor patch <<< "${lastTag}"
patch=$((patch + 1))

# 生成新的版本号
newTag="${major}.${minor}.${patch}"

# 创建新的标签
git tag "$newTag"

# 推送标签到远程仓库
git push origin "$newTag"

# 提交版本号文件
echo "$newTag" > version.txt
git add version.txt
git commit -m "Update version to $newTag"
git push origin master
