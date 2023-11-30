# Func 库
常见PHP使用函数库，该库部分函数依赖PHP扩展，如使用环境不需要使用该部分函数可以手工忽略。
依赖扩展列表如下：
- mbstring 字符串处理函数
- json json处理函数、json网络请求相关
- curl 网络请求库
- pdo DB操作函数，其他函数基本不依赖该扩展

## 安装
1. PHP 版本大于或等于 5.6
2. 部分功能必须安装扩展：mb_string、json 才能使用

请使用Composer进行安装：
```shell script
# 正常安装模式
composer require lfphp/func

# 忽略平台环境扩展依赖安装模式
composer require lfphp/func --ignore-platform-reqs
```
具体使用方法请参考具体函数代码。

## 获取最新版本库
https://github.com/sasumi/Func
