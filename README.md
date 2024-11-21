# PHP Enhancement Functions Library
Common PHP function library, some functions of this library depend on PHP extensions, if the use environment does not need to use these functions, you can manually ignore them.
The list of dependent extensions is as follows:
- mbstring string processing function
- json json processing function, json network request related
- curl network request library
- pdo DB operation function, other functions basically do not depend on this extension

## Installation
1. PHP version is greater than or equal to 5.6
2. Some functions must be installed with extensions: mb_string, json to use

Please use Composer to install:
```shell script
# Normal installation mode
composer require lfphp/func

# Ignore platform environment extension dependency installation mode
composer require lfphp/func --ignore-platform-reqs
```
For specific usage, please refer to the specific function code.

[Detailed function list](functions.md)

## Source Introduction
array.php
This file contains functions for working with arrays. It includes functions for filtering, mapping, and reducing arrays.

color.php
This file contains functions for working with colors. It includes functions for converting between different color formats, such as RGB, HSL, and HEX.

cron.php
This file contains functions for working with cron schedules. It includes functions for checking if a cron format matches a specified timestamp and for monitoring cron commands.

csp.php
This file contains functions for working with Content Security Policy (CSP). It includes functions for building CSP rules and reporting rules.

curl.php
This file contains functions for working with cURL. It includes functions for sending GET, POST, and other types of HTTP requests.

db.php
This file contains functions for working with databases. It includes functions for connecting to databases, executing queries, and retrieving results.

env.php
This file contains functions for working with environment variables. It includes functions for getting and setting environment variables.

event.php
This file contains functions for working with events. It includes functions for triggering and listening to events.

file.php
This file contains functions for working with files. It includes functions for reading, writing, and deleting files.

font.php
This file contains functions for working with fonts. It includes functions for loading and manipulating font data.

html.php
This file contains functions for working with HTML. It includes functions for parsing and generating HTML.

http.php
This file contains functions for working with HTTP. It includes functions for sending and receiving HTTP requests.

session.php
This file contains functions for working with sessions. It includes functions for starting, reading, and writing session data.

sheet.php
This file contains functions for working with spreadsheets. It includes functions for reading and writing spreadsheet data.

string.php
This file contains functions for working with strings. It includes functions for manipulating and formatting strings.

time.php
This file contains functions for working with time and dates. It includes functions for formatting and manipulating dates and times.

util.php
This file contains miscellaneous utility functions. It includes functions for working with arrays, strings, and other data types.

## Get the latest version of the library
https://github.com/sasumi/Func
