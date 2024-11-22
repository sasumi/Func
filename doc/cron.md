# Cron
 > Crontab Enhancement Functions

## 1. cron_match($format,$time,$error): bool
Check if the cron format matches the specified timestamp
#### Parameters
 - {string} *$format* cron format. Currently not supporting year, format is: minutes hours days months weeks
 - {int} *$time* Default is current timestamp
 - {string|null} *$error* mismatch error info

#### Returns
 - bool 

## 2. cron_watch_commands($rules,$on_before_call,$check_interval)
#### Parameters
 - {array} *$rules* 
 - {callable|null} *$on_before_call* 
 - {int} *$check_interval* seconds, must min than one minutes


