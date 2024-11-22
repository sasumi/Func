# Html
 > HTML quick operation functions

## 1. html_tag_select($name,$options,$current_value,$placeholder,$attributes): string
Build html <select>, support optgroup mode
If it is grouping mode, the format is: [value=>text, label=>options, ...]
If it is normal mode, the format is: options: [value1=>text, value2=>text,...]
#### Parameters
 - {string} *$name* 
 - {array} *$options* option data,
 - {string|array} *$current_value* 
 - {string} *$placeholder* 
 - {array} *$attributes* 

#### Returns
 - string 

## 2. html_tag_options($options,$current_value): string
Build html select options
#### Parameters
 - {array} *$options* [value=>text,...] option data option array
 - {string|array} *$current_value* current value

#### Returns
 - string 

## 3. html_tag_option($text,$value,$selected,$attributes): string
Build html <option>
#### Parameters
 - {string} *$text* text, spaces will be escaped into &nbsp;
 - {string} *$value* 
 - {bool} *$selected* 
 - {array} *$attributes* 

#### Returns
 - string 

## 4. html_tag_option_group($label,$options,$current_value): string
Build html <optgroup>
#### Parameters
 - {string} *$label* 
 - {array} *$options* 
 - {string|array} *$current_value* current value

#### Returns
 - string 

## 5. html_tag_textarea($name,$value,$attributes): string
Build html <textarea>
#### Parameters
 - {string} *$name* 
 - {string} *$value* 
 - {array} *$attributes* 

#### Returns
 - string 

## 6. html_tag_hidden($name,$value): string
Build html <input type="hidden">
#### Parameters
 - {string} *$name* 
 - {string} *$value* 

#### Returns
 - string 

## 7. html_tag_hidden_list($data_list): string
Build html data list
#### Parameters
 - {array} *$data_list* data list (can be multi-dimensional array)

#### Returns
 - string 

## 8. html_tag_number_input($name,$value,$attributes): string
Build html number input
#### Parameters
 - {string} *$name* 
 - {string} *$value* 
 - {array} *$attributes* 

#### Returns
 - string 

## 9. html_tag_radio_group($name,$options,$current_value,$wrapper_tag,$radio_extra_attributes): string
Build html radio group
#### Parameters
 - {string} *$name* 
 - {array} *$options* options [value=>title,...] format
 - {string} *$current_value* 
 - {string} *$wrapper_tag* Each option wraps the tag outside, such as li, div, etc.
 - {array} *$radio_extra_attributes* Extra custom attributes for each radio

#### Returns
 - string 

## 10. html_tag_radio($name,$value,$title,$checked,$attributes): string
Build html radio button
Use label>(input:radio+{text}) structure
#### Parameters
 - {string} *$name* 
 - {mixed} *$value* 
 - {string} *$title* 
 - {bool} *$checked* 
 - {array} *$attributes* 

#### Returns
 - string 

## 11. html_tag_checkbox_group($name,$options,$current_value,$wrapper_tag,$checkbox_extra_attributes): string
#### Parameters
 - {string} *$name* 
 - {array} *$options* options [value=>title,...] format
 - {string|array} *$current_value* 
 - {string} *$wrapper_tag* Each option wraps the tag outside, such as li, div, etc.
 - {array} *$checkbox_extra_attributes* Extra custom attributes for each checkbox

#### Returns
 - string 

## 12. html_tag_checkbox($name,$value,$title,$checked,$attributes): string
Build a checkbox button
Use label>(input:checkbox+{text}) structure
#### Parameters
 - {string} *$name* 
 - {mixed} *$value* 
 - {string} *$title* 
 - {bool} *$checked* 
 - {array} *$attributes* 

#### Returns
 - string 

## 13. html_tag_progress($value,$max,$attributes): string
Build progress bar (if no value is set, it can be used as loading effect)
#### Parameters
 - {null|number} *$value* 
 - {null|number} *$max* 
 - {array} *$attributes* 

#### Returns
 - string 

## 14. html_tag_img($src,$attributes): string
HTML <img> tag
#### Parameters
 - {mixed} *$src* 
 - {mixed} *$attributes* 

#### Returns
 - string 

## 15. html_loading_bar($attributes): string
Html loop scrolling progress bar
alias to htmlProgress
#### Parameters
 - {array} *$attributes* 

#### Returns
 - string 

## 16. html_tag_range($name,$value,$min,$max,$step,$attributes): string
Html range selector
#### Parameters
 - {string} *$name* 
 - {string} *$value* current value
 - {int} *$min* minimum value
 - {int} *$max* maximum value
 - {int} *$step* step length
 - {array} *$attributes* 

#### Returns
 - string 

## 17. html_abstract($html_content,$len): string
Get HTML summary information
#### Parameters
 - {string} *$html_content* 
 - {int} *$len* 

#### Returns
 - string 

## 18. html_tag_input_text($name,$value,$attributes): string
Build html input:text text input box
#### Parameters
 - {string} *$name* 
 - {string} *$value* 
 - {array} *$attributes* 

#### Returns
 - string 

## 19. html_tag_date($name,$date_or_timestamp,$attributes): string
Build html date input box
#### Parameters
 - {string} *$name* 
 - {string} *$date_or_timestamp* 
 - {array} *$attributes* 

#### Returns
 - string 

## 20. html_tag_time($name,$time_str,$attributes): string
Build html date input box
#### Parameters
 - {string} *$name* 
 - {string} *$time_str* 
 - {array} *$attributes* 

#### Returns
 - string 

## 21. html_tag_datetime($name,$datetime_or_timestamp,$attributes): string
Build html date + time input box
#### Parameters
 - {string} *$name* 
 - {string} *$datetime_or_timestamp* 
 - {array} *$attributes* 

#### Returns
 - string 

## 22. html_tag_month_select($name,$current_month,$format,$attributes): string
Build html month selector
#### Parameters
 - {string} *$name* 
 - {int|null} *$current_month* Current month, range 1~12
 - {string} *$format* Month format, consistent with the format accepted by the date function
 - {array} *$attributes* attributes

#### Returns
 - string 

## 23. html_tag_year_select($name,$current_year,$start_year,$end_year,$attributes): string
Build html year selector
#### Parameters
 - {string} *$name* 
 - {int|null} *$current_year* Current year
 - {int} *$start_year* starting year (default is 1970)
 - {string} *$end_year* Ending year (default is this year)
 - {array} *$attributes* 

#### Returns
 - string 

## 24. html_tag($tag,$attributes,$inner_html): string
Build html node
#### Parameters
 - {string} *$tag* 
 - {array} *$attributes* 
 - {string} *$inner_html* 

#### Returns
 - string 

## 25. html_tag_link($inner_html,$href,$attributes): string
Construct HTML link
#### Parameters
 - {string} *$inner_html* 
 - {string} *$href* 
 - {array} *$attributes* 

#### Returns
 - string 

## 26. html_tag_css()
## 27. html_tag_js()
## 28. html_tag_date_input($name,$value,$attributes): string
Build html date input
#### Parameters
 - {string} *$name* 
 - {string} *$value* 
 - {array} *$attributes* 

#### Returns
 - string 

## 29. html_tag_date_time_input($name,$value,$attributes): string
Build html time input
#### Parameters
 - {string} *$name* 
 - {string} *$value* 
 - {array} *$attributes* 

#### Returns
 - string 

## 30. html_tag_data_list($id,$data_map): string
Build DataList
#### Parameters
 - {string} *$id* 
 - {array} *$data_map* index array: [val=>title,...], or natural growth array: [title1, title2,...]

#### Returns
 - string 

## 31. html_tag_input_submit($value,$attributes): string
submit input
#### Parameters
 - {mixed} *$value* 
 - {array} *$attributes* 

#### Returns
 - string 

## 32. html_tag_no_script($html): string
no script support html
#### Parameters
 - {string} *$html* 

#### Returns
 - string 

## 33. html_tag_button_submit($inner_html,$attributes): string
submit button
#### Parameters
 - {string} *$inner_html* 
 - {array} *$attributes* 

#### Returns
 - string 

## 34. html_tag_table($data,$headers,$caption,$attributes): string
Build table node
#### Parameters
 - {array} *$data* 
 - {array|false} *$headers* header list [field name => alias, ...], if false, it means do not display the header
 - {string} *$caption* 
 - {array} *$attributes* 

#### Returns
 - string 

## 35. html_attributes($attributes): string
Construct HTML node attributes
Fix pattern, disabled HTML display when false
#### Parameters
 - {array} *$attributes* 

#### Returns
 - string 

## 36. h($str,$len,$tail,$length_exceeded): string
Escape and truncate strings in HTML
#### Parameters
 - {string} *$str* 
 - {number|null} *$len* truncation length, empty means no truncation
 - {null|string} *$tail* append tail string character
 - {bool} *$length_exceeded* Exceeded length

#### Returns
 - string 

## 37. ha($str,$len,$tail,$length_exceeded): string
Escape and truncate HTML node attribute string
#### Parameters
 - {string} *$str* 
 - {int} *$len* truncation length, empty means no truncation
 - {string} *$tail* append tail string character
 - {bool} *$length_exceeded* Exceeded length

#### Returns
 - string 

## 38. text_to_html($text,$len,$tail,$over_length): string
Convert plain text to HTML
#### Parameters
 - {string} *$text* 
 - {null} *$len* 
 - {string} *$tail* 
 - {bool} *$over_length* 

#### Returns
 - string 

## 39. html_fix_relative_path($html,$page_url): string
Correct relative paths in HTML
#### Parameters
 - {string} *$html* 
 - {string} *$page_url* 

#### Returns
 - string Return the original HTML if replacement fails

## 40. html_to_text($html,$option): void
todo
#### Parameters
 - {mixed} *$html* 
 - {mixed} *$option* 

#### Returns
 - void 

## 41. html_text_highlight($text,$keyword,$template): string
Highlight text
#### Parameters
 - {string} *$text* 
 - {string} *$keyword* 
 - {string} *$template* 

#### Returns
 - string Returns the HTML escaped string

## 42. html_tag_meta($equiv,$content): string
Construct HTML meta tags
#### Parameters
 - {string} *$equiv* 
 - {string} *$content* 

#### Returns
 - string 

## 43. html_meta_redirect($url,$timeout_sec): string
Use html meta to redirect pages
#### Parameters
 - {string} *$url* jump target path
 - {int} *$timeout_sec* timeout

#### Returns
 - string html

## 44. html_meta_csp($csp_rules,$report_uri,$report_only): string
Build CSP meta tag
#### Parameters
 - {array} *$csp_rules* 
 - {string} *$report_uri* 
 - {bool} *$report_only* 

#### Returns
 - string 

## 45. html_value_compare($str1,$data): bool
HTML numeric comparison (converted to a string and then strictly compared)
#### Parameters
 - {string|number} *$str1* 
 - {string|number|array} *$data* 

#### Returns
 - bool whether they are equal

## 46. static_version_set($patch_config): array
Set static resource version control items
#### Parameters
 - {array} *$patch_config* version configuration table, format such as: abc/foo.js => '2020', priority is given to matching rules with shorter lengths

#### Returns
 - array all configurations

## 47. static_version_patch($src,$matched): string
Static resource version patch
#### Parameters
 - {string} *$src* 
 - {bool} *$matched* 

#### Returns
 - string 

## 48. static_version_statement_quote($str): string
Static resource version wildcard escape
#### Parameters
 - {string} *$str* 

#### Returns
 - string 

## 49. fix_browser_datetime($datetime_str_from_h5,$fix_seconds): string
Fix the data submitted by input:datetime or input:datetime-local in HTML5 browser
The time format submitted by H5 may be Ymd\TH:i
#### Parameters
 - {string} *$datetime_str_from_h5* 
 - {int} *$fix_seconds* Second correction. The H5 input box does not have second precision when submitted. This can be set to 0 (such as the start time) or 59 (such as the end time) to correct the second unit value.

#### Returns
 - string 



