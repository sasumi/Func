#!/bin/sh

set -e

# 确保是 git 仓库
if ! git rev-parse --git-dir > /dev/null 2>&1; then
  echo "❌ 当前目录不是 Git 仓库"
  exit 1
fi

# 拉取远程 tag（只 fetch 不合并代码）
git fetch --tags

# 获取所有 tag 中“语义版本号最大”的
last_tag=$(git tag | sort -V | tail -n 1)

if [ -z "$last_tag" ]; then
  echo "⚠️ 未找到 tag，默认初始版本为 0.0.1"
  new_version="0.0.1"
else
  echo "🔍 检测到最新 tag：$last_tag"
  if echo "$last_tag" | grep -q "^v"; then
    has_v_prefix=true
    clean_tag=$(echo "$last_tag" | sed 's/^v//')
  else
    has_v_prefix=false
    clean_tag="$last_tag"
  fi

  # 拆分 x.y.z
  IFS='.' read -r major minor patch <<EOF
$clean_tag
EOF

  patch=${patch:-0}
  patch=$((patch + 1))

  new_version="${major}.${minor}.${patch}"
  [ "$has_v_prefix" = true ] && new_version="v$new_version"
fi

echo "✅ 最新 tag：$last_tag"
echo "🔧 新版本号：$new_version"

# 创建并推送 tag
git tag "$new_version"
echo "🏷️ 已创建本地 tag：$new_version"

git push origin "$new_version"
echo "🚀 已推送 tag 到远程：origin/$new_version"