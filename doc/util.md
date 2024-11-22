# Util
 > Miscellaneous Functions

## 1. tick_dump($step,$fun)
Step-by-step debugging
#### Parameters
 - {int} *$step* step length
 - {string} *$fun* debug function, dump is used by default
## 2. readline()
Read console line input. If the system has an extension installed, the extension function is used first.
## 3. try_many_times($payload,$tries): int
Try calling the function
#### Parameters
 - {callable} *$payload* processing function, returning FALSE means aborting subsequent attempts
 - {int} *$tries* The number of additional attempts when an error occurs (excluding the first normal execution)

#### Returns
 - int total number of attempts (excluding the first normal execution)

## 4. dump()
Program debugging function
Calling method: dump($var1, $var2, ..., 1), when the last value is 1, it means to exit (die) the program
## 5. printable($var,$print_str): bool
Check whether the variable can be printed (such as strings, numbers, objects containing toString methods, etc.)
Boolean values, resources, etc. are not printable variables
#### Parameters
 - {mixed} *$var* 
 - {string} *$print_str* printable string

#### Returns
 - bool whether it is printable

## 6. print_exception($ex,$include_external_properties,$as_return): string
Print exception information
#### Parameters
 - {\Exception} *$ex* 
 - {bool} *$include_external_properties* whether to include additional exception information
 - {bool} *$as_return* whether to process in return mode (not printing exceptions)

#### Returns
 - string 

## 7. print_trace($trace,$with_callee,$with_index,$as_return): string
Print trace information
#### Parameters
 - {array} *$trace* 
 - {bool} *$with_callee* 
 - {bool} *$with_index* 
 - {bool} *$as_return* 

#### Returns
 - string 

## 8. print_sys_error($code,$msg,$file,$line,$trace_string)
Print system errors and trace information
#### Parameters
 - {integer} *$code* 
 - {string} *$msg* 
 - {string} *$file* 
 - {integer} *$line* 
 - {string} *$trace_string* 
## 9. error2string($code): string
Convert error code value to string
#### Parameters
 - {int} *$code* 

#### Returns
 - string 

## 10. string2error($string): int
Convert error codes to specific code values
#### Parameters
 - {string} *$string* 

#### Returns
 - int 

## 11. exception_convert($exception,$target_class): mixed
Convert the exception object to other specified exception class objects
#### Parameters
 - {Exception} *$exception* 
 - {string} *$target_class* 

#### Returns
 - mixed 

## 12. register_error2exception($error_levels,$exception_class): callable|null
Register to convert PHP errors into exceptions
#### Parameters
 - {int} *$error_levels* 
 - {\ErrorException|null} *$exception_class* 

#### Returns
 - callable|null 

## 13. is_function($f): boolean
Check if it is a function
#### Parameters
 - {mixed} *$f* 

#### Returns
 - boolean 

## 14. class_uses_recursive($class_or_object): string[]
Get all inherited parent classes of objects and classes (including trait classes)
If you don't need traits, try class_parents
#### Parameters
 - {string|object} *$class_or_object* 

#### Returns
 - string[] 

## 15. trait_uses_recursive($trait): array
Get traits recursively
#### Parameters
 - {string} *$trait* 

#### Returns
 - array 

## 16. get_constant_name($class,$const_val): string|null
Get the name of the specified class constant
#### Parameters
 - {string} *$class* class name
 - {mixed} *$const_val* constant value

#### Returns
 - string|null 

## 17. assert_via_exception($expression,$err_msg,$exception_class)
Handle assertions by throwing exceptions
#### Parameters
 - {mixed} *$expression* assertion value
 - {string} *$err_msg* 
 - {string} *$exception_class* exception class, default is \Exception
## 18. pdog($fun,$handler)
pdog
#### Parameters
 - {string} *$fun* 
 - {callable|string} *$handler* 
## 19. guid(): mixed
Get the current context GUID

#### Returns
 - mixed 

## 20. var_export_min($var,$return): string|null
Export variables using minimal format (similar to var_export)
#### Parameters
 - {mixed} *$var* 
 - {bool} *$return* whether to return in return mode, the default is to output to the terminal

#### Returns
 - string|null 

## 21. memory_leak_check($threshold,$leak_payload)
Detect memory overflow. It is not recommended to enable this check when running the code to avoid performance loss.
#### Parameters
 - {int} *$threshold* 
 - {callable|string} *$leak_payload* Function called when memory leaks
## 22. debug_mark($tag,$trace_location,$mem_usage): mixed
Code management
#### Parameters
 - {string} *$tag* 
 - {bool} *$trace_location* 
 - {bool} *$mem_usage* 

#### Returns
 - mixed 

## 23. debug_mark_output($as_return): string|null
Output dot information
#### Parameters
 - {bool} *$as_return* 

#### Returns
 - string|null 



