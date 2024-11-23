# Session
 > Session Enhancement Functions

## 1. session_start_once(): bool
Open a session once
If the original session status is not open, the session will be automatically closed after reading to avoid session locking

#### Returns
 - bool 

## 2. session_write_once()
Submit session data immediately and selectively close the session based on the context
## 3. session_write_scope($handler): bool
Automatically determine the current session status and write data from $_SESSION to the session
If the original session status is not open, the session will be automatically closed after the write operation is completed to avoid session locking, otherwise it will remain unchanged
Calling method:
session_write_scope(function(){
$_SESSION['hello'] = 'world';
unset($_SESSION['info']);
});
#### Parameters
 - {callable} *$handler* 

#### Returns
 - bool 

## 4. session_start_in_time($expire_seconds): void
Start the session at the specified time
#### Parameters
 - {int} *$expire_seconds* seconds

#### Returns
 - void 



