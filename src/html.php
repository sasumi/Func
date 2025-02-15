<?php
/**
 * HTML quick operation functions
 */
namespace LFPhp\Func;

use DateTime;
use Exception;
use InvalidArgumentException;

/**
 * HTML self-closing tags
 */
const HTML_SELF_CLOSING_TAGS = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr', 'command', 'keygen', 'menuitem'];

/**
 * Build html <select>, support optgroup mode
 * @param string $name
 * @param array $options option data,
 * If it is grouping mode, the format is: [value=>text, label=>options, ...]
 * If it is normal mode, the format is: options: [value1=>text, value2=>text,...]
 * @param string|array $current_value
 * @param string $placeholder
 * @param array $attributes
 * @return string
 */
function html_tag_select($name, array $options, $current_value = null, $placeholder = '', $attributes = []){
	$attributes = array_merge($attributes, [
		'name'        => $name ?: null,
		'placeholder' => $placeholder ?: null,
	]);

	//Multiple selection
	if(is_array($current_value)){
		$attributes['multiple'] = 'multiple';
	}

	$option_html = $placeholder ? html_tag_option($placeholder, '') : '';

	//Single layer option
	if(count($options, COUNT_RECURSIVE) == count($options, COUNT_NORMAL)){
		$option_html .= html_tag_options($options, $current_value);
	}//optgroup support
	else{
		foreach($options as $var1 => $var2){
			if(is_array($var2)){
				$option_html .= html_tag_option_group($var1, $var2, $current_value);
			}else{
				$option_html .= html_tag_option($var2, $var1, $current_value);
			}
		}
	}
	return html_tag('select', $attributes, $option_html);
}

/**
 * Build html select options
 * @param array $options [value=>text,...] option data option array
 * @param string|array $current_value current value
 * @return string
 */
function html_tag_options(array $options, $current_value = null){
	$html = '';
	foreach($options as $val => $ti){
		$html .= html_tag_option($ti, $val, html_value_compare($val, $current_value));
	}
	return $html;
}

/**
 * Build html <option>
 * @param string $text text, spaces will be escaped into &nbsp;
 * @param string $value
 * @param bool $selected
 * @param array $attributes
 * @return string
 */
function html_tag_option($text, $value = '', $selected = false, $attributes = []){
	return html_tag('option', array_merge([
		'selected' => $selected ? 'selected' : null,
		'value'    => $value,
	], $attributes), text_to_html($text));
}

/**
 * Build html <optgroup>
 * @param string $label
 * @param array $options
 * @param string|array $current_value current value
 * @return string
 */
function html_tag_option_group($label, $options, $current_value = null){
	$option_html = html_tag_options($options, $current_value);
	return html_tag('optgroup', ['label' => $label], $option_html);
}

/**
 * Build html <textarea>
 * @param string $name
 * @param string $value
 * @param array $attributes
 * @return string
 */
function html_tag_textarea($name, $value = '', $attributes = []){
	$attributes['name'] = $name;
	return html_tag('textarea', $attributes, htmlspecialchars($value));
}

/**
 * Build html <input type="hidden">
 * @param string $name
 * @param string $value
 * @return string
 */
function html_tag_hidden($name, $value = ''){
	return html_tag('input', ['type' => 'hidden', 'name' => $name, 'value' => $value]);
}

/**
 * Build html data list
 * @param array $data_list data list (can be multi-dimensional array)
 * @return string
 */
function html_tag_hidden_list($data_list){
	$html = '';
	$entries = explode('&', http_build_query($data_list));
	foreach($entries as $entry){
		$tmp = explode('=', $entry);
		$key = $tmp[0];
		$value = isset($tmp[1]) ? $tmp[1] : null;
		$html .= html_tag_hidden(urldecode($key), urldecode($value)).PHP_EOL;
	}
	return $html;
}

/**
 * Build html number input
 * @param string $name
 * @param string $value
 * @param array $attributes
 * @return string
 */
function html_tag_number_input($name, $value = '', $attributes = []){
	$attributes['type'] = 'number';
	$attributes['name'] = $name;
	$attributes['value'] = $value;
	return html_tag('input', $attributes);
}

/**
 * Build html radio group
 * @param string $name
 * @param array $options options [value=>title,...] format
 * @param string $current_value
 * @param string $wrapper_tag Each option wraps the tag outside, such as li, div, etc.
 * @param array $radio_extra_attributes Extra custom attributes for each radio
 * @return string
 */
function html_tag_radio_group($name, $options, $current_value = '', $wrapper_tag = '', $radio_extra_attributes = []){
	$html = [];
	foreach($options as $val => $ti){
		$html[] = html_tag_radio($name, $val, $ti, html_value_compare($val, $current_value), $radio_extra_attributes);
	}

	if($wrapper_tag){
		$rst = '';
		foreach($html as $h){
			$rst .= ' '.html_tag($wrapper_tag, [], $h);
		}
		return $rst;
	}else{
		return join(' ', $html);
	}
}

/**
 * Build html radio button
 * Use label>(input:radio+{text}) structure
 * @param string $name
 * @param mixed $value
 * @param string $title
 * @param bool $checked
 * @param array $attributes
 * @return string
 */
function html_tag_radio($name, $value, $title = '', $checked = false, $attributes = []){
	$attributes['type'] = 'radio';
	$attributes['name'] = $name;
	$attributes['value'] = $value;
	if($checked){
		$attributes['checked'] = 'checked';
	}
	return html_tag('label', [], html_tag('input', $attributes).' '.$title);
}

/**
 * @param string $name
 * @param array $options options [value=>title,...] format
 * @param string|array $current_value
 * @param string $wrapper_tag Each option wraps the tag outside, such as li, div, etc.
 * @param array $checkbox_extra_attributes Extra custom attributes for each checkbox
 * @return string
 */
function html_tag_checkbox_group($name, $options, $current_value = null, $wrapper_tag = '', $checkbox_extra_attributes = []){
	$html = [];
	foreach($options as $val => $ti){
		$html[] = html_tag_checkbox($name, $val, $ti, html_value_compare($val, $current_value), $checkbox_extra_attributes);
	}
	if($wrapper_tag){
		$rst = '';
		foreach($html as $h){
			$rst .= ' '.html_tag($wrapper_tag, [], $h);
		}
		return $rst;
	}else{
		return join(' ', $html);
	}
}

/**
 * Build a checkbox button
 * Use label>(input:checkbox+{text}) structure
 * @param string $name
 * @param mixed $value
 * @param string $title
 * @param bool $checked
 * @param array $attributes
 * @return string
 */
function html_tag_checkbox($name, $value, $title = '', $checked = false, $attributes = []){
	$attributes['type'] = 'checkbox';
	$attributes['name'] = $name;
	$attributes['value'] = $value;
	if($checked){
		$attributes['checked'] = 'checked';
	}
	$checkbox = html_tag('input', $attributes);
	if(!$title){
		return $checkbox;
	}
	return html_tag('label', [], $checkbox.' '.$title);
}

/**
 * Build progress bar (if no value is set, it can be used as loading effect)
 * @param null|number $value
 * @param null|number $max
 * @param array $attributes
 * @return string
 * @throws \Exception
 */
function html_tag_progress($value = null, $max = null, $attributes = []){
	//If there is a max, it must be greater than 0
	if(isset($max) && floatval($max) <= 0){
		throw new Exception('Progress max should bigger or equal to zero');
	}
	if(isset($value)){
		//If max is set, the value range must be 0~max
		if(isset($max) && $value > $max){
			throw new Exception('Progress value should less or equal than max');
		}
		//No max is set, value must be in the range of 0 to 1
		if($value > 1 || $value < 0){
			throw new Exception('Progress value should be between 0 to 1');
		}
	}
	$attributes['max'] = $max;
	$attributes['value'] = $value;
	return html_tag('progress', $attributes);
}

/**
 * HTML <img> tag
 * @param $src
 * @param $attributes
 * @return string
 */
function html_tag_img($src, $attributes = [
	//alt=>''
	//loading='lazy'
]){
	$attributes['src'] = $src;
	return html_tag('img', $attributes);
}

/**
 * calculate object-fit size
 * @see https://developer.mozilla.org/zh-CN/docs/Web/CSS/object-fit
 * @param int[] $container_size container size: [width, height]
 * @param int[] $target_size target size: [width, height]
 * @param string $object_fit_type object-fit type: “contain”, “cover”, “fill”, “none”, “scale-down”
 * @return array result position info: [left, top, width, height]
 */
function html_object_fit_calculate($container_size, $target_size, $object_fit_type){
	[$container_width, $container_height] = $container_size;
	[$target_width, $target_height] = $target_size;
	$container_ratio = $container_width / $container_height;
	$target_ratio = $target_width/$target_height;
	$result = ['left' => 0, 'top' => 0, 'width' => 0, 'height' => 0,];
	switch($object_fit_type){
		case 'contain':
			if ($target_ratio > $container_ratio) {
				$result['width'] = $container_width;
				$result['height'] = $container_width / $target_ratio;
				$result['top'] = ($container_height - $result['height']) / 2;
				$result['left'] = 0;
			} else {
				$result['width'] = $container_height * $target_ratio;
				$result['height'] = $container_height;
				$result['top'] = 0;
				$result['left'] = ($container_width - $result['width']) / 2;
			}
			break;

		case 'cover':
			if ($target_ratio > $container_ratio) {
				$result['width'] = $container_height * $target_ratio;
				$result['height'] = $container_height;
				$result['top'] = 0;
				$result['left'] = ($container_width - $result['width']) / 2;
			} else {
				$result['width'] = $container_width;
				$result['height'] = $container_width / $target_ratio;
				$result['top'] = ($container_height - $result['height']) / 2;
				$result['left'] = 0;
			}
			break;

		case 'fill':
			$result['width'] = $container_width;
			$result['height'] = $container_height;
			$result['top'] = 0;
			$result['left'] = 0;
			break;

		case 'none':
			$result['width'] = $target_width;
			$result['height'] = $target_height;
			$result['top'] = ($container_height - $target_height) / 2;
			$result['left'] = ($container_width - $target_width) / 2;
			break;

		case 'scale-down':
			$contain_result = html_object_fit_calculate($container_size, $target_size, 'contain');
			$none_result = html_object_fit_calculate($container_size, $target_size, 'none');
			if ($contain_result['width'] * $contain_result['height'] < $none_result['width'] * $none_result['height']) {
				$result = $contain_result;
			} else {
				$result = $none_result;
			}
			break;
		default:
			throw new InvalidArgumentException("Invalid object-fit type: $object_fit_type");
	}
	//convert px to integer
	return array_map('intval', $result);
}

/**
 * Html loop scrolling progress bar
 * alias to htmlProgress
 * @param array $attributes
 * @return string
 */
function html_loading_bar($attributes = []){
	return html_tag_progress(null, null, $attributes);
}

/**
 * Html range selector
 * @param string $name
 * @param string $value current value
 * @param int $min minimum value
 * @param int $max maximum value
 * @param int $step step length
 * @param array $attributes
 * @return string
 */
function html_tag_range($name, $value, $min = 0, $max = 100, $step = 1, $attributes = []){
	$attributes['type'] = 'range';
	$attributes['name'] = $name;
	$attributes['value'] = $value;
	$attributes['min'] = $min;
	$attributes['max'] = $max;
	$attributes['step'] = $step;
	return html_tag('input', $attributes);
}

/**
 * Get HTML summary information
 * @param string $html_content
 * @param int $len
 * @return string
 */
function html_abstract($html_content, $len = 200){
	$str = str_replace(["\n", "\r"], "", $html_content);
	$str = preg_replace('/<br([^>]*)>/i', '$$NL', $str);
	//todo convert <p> <div> to line break
	$str = strip_tags($str);
	$str = html_entity_decode($str, ENT_QUOTES);
	$str = h($str, $len);
	$str = str_replace('$$NL', '<br/>', $str);

	//Remove leading and trailing blank lines
	$str = preg_replace('/^(<br[^>]*>)*/i', '', $str);
	$str = preg_replace('/(<br[^>]*>)*$/i', '', $str);
	return $str;
}

/**
 * Build html input:text text input box
 * @param string $name
 * @param string $value
 * @param array $attributes
 * @return string
 */
function html_tag_input_text($name, $value = '', $attributes = []){
	$attributes['type'] = 'text';
	$attributes['name'] = $name;
	$attributes['value'] = $value;
	return html_tag('input', $attributes);
}

/**
 * Build html date input box
 * @param string $name
 * @param string $date_or_timestamp
 * @param array $attributes
 * @return string
 */
function html_tag_date($name, $date_or_timestamp = '', $attributes = []){
	$attributes['type'] = 'date';
	$attributes['name'] = $name;
	if($date_or_timestamp){
		$attributes['value'] = is_numeric($date_or_timestamp) ? date('Y-m-d', $date_or_timestamp) : date('Y-m-d', strtotime($date_or_timestamp));
	}
	return html_tag('input', $attributes);
}

/**
 * Build html date input box
 * @param string $name
 * @param string $time_str
 * @param array $attributes
 * @return string
 */
function html_tag_time($name, $time_str = '', $attributes = []){
	$attributes['type'] = 'time';
	$attributes['name'] = $name;
	if($time_str){
		$attributes['value'] = $time_str;
	}
	return html_tag('input', $attributes);
}

/**
 * Build html date + time input box
 * @param string $name
 * @param string $datetime_or_timestamp
 * @param array $attributes
 * @return string
 */
function html_tag_datetime($name, $datetime_or_timestamp = '', $attributes = []){
	$attributes['type'] = 'datetime-local';
	$attributes['name'] = $name;
	$attributes['step'] = 1; //Step must be filled in order to select seconds in the case of an empty value
	if($datetime_or_timestamp){
		$attributes['value'] = is_numeric($datetime_or_timestamp) ? date('Ymd\TH:i:s', $datetime_or_timestamp) : date('Ymd\TH:i:s', strtotime($datetime_or_timestamp));
	}else{
		$attributes['value'] = '0000-00-00T00:00:00';
	}
	return html_tag('input', $attributes);
}

/**
 * Build html month selector
 * @param string $name
 * @param int|null $current_month Current month, range 1~12
 * @param string $format Month format, consistent with the format accepted by the date function
 * @param array $attributes attributes
 * @return string
 */
function html_tag_month_select($name, $current_month = null, $format = 'm', $attributes = []){
	$opts = [];
	$format = $format ?: 'm';
	for($i = 1; $i <= 12; $i++){
		$opts[$i] = date($format, strtotime('1970-'.$current_month.'-01'));
	}
	return html_tag_select($name, $opts, $current_month, $attributes['placeholder'], $attributes);
}

/**
 * Build html year selector
 * @param string $name
 * @param int|null $current_year Current year
 * @param int $start_year starting year (default is 1970)
 * @param string $end_year Ending year (default is this year)
 * @param array $attributes
 * @return string
 */
function html_tag_year_select($name, $current_year = null, $start_year = 1970, $end_year = '', $attributes = []){
	$start_year = $start_year ?: 1970;
	$end_year = $end_year ?: date('Y');
	$opts = [];
	for($i = $start_year; $i <= $end_year; $i++){
		$opts[$i] = $i;
	}
	return html_tag_select($name, $opts, $current_year, $attributes['placeholder'], $attributes);
}

/**
 * Build html node
 * @param string $tag
 * @param array $attributes
 * @param string $inner_html
 * @return string
 */
function html_tag($tag, $attributes = [], $inner_html = ''){
	$tag = strtolower($tag);
	$single_tag = in_array($tag, HTML_SELF_CLOSING_TAGS);
	$html = "<$tag ";

	//For the textarea tag, identify the value and fill it into inner_html
	if($tag === 'textarea' && isset($attributes['value'])){
		$inner_html = $inner_html ?: h($attributes['value']);
		unset($attributes['value']);
	}

	$html .= html_attributes($attributes);
	$html .= $single_tag ? "/>" : ">".$inner_html."</$tag>";
	return $html;
}

/**
 * Build HTML link
 * @param string $href
 * @param string|null $inner_html use href while [null] was set.
 * @param array|[] $attributes
 * @return string
 */
function html_tag_link($href, $inner_html = null, $attributes = []){
	$inner_html = is_null($inner_html) ? $href : $inner_html;
	$attributes['href'] = $href;
	return html_tag('a', $attributes, $inner_html);
}

/**
 * Build html external site link, with no-referrer, no-opener, open in new window as default for safe
 * @param string $href
 * @param string|null $inner_html
 * @param array|[] $attributes
 * @return string
 */
function html_tag_external_link($href, $inner_html = null, $attributes = []){
	$attributes = array_merge([
		'target'         => '_blank',
		'rel'            => 'noreferrer noopener',
		'referrerpolicy' => "no-referrer",
	], $attributes);
	return html_tag_link($href, $inner_html, $attributes);
}

/***
 * Build css node
 * @param string $href
 * @param array $attributes
 * @return string
 */
function html_tag_css($href, $attributes = []){
	return html_tag('link', array_merge([
		'type'  => 'text/css',
		'rel'   => 'stylesheet',
		'media' => 'all',
		'href'  => $href,
	], $attributes));
}

/***
 * Build js node
 * @param string $src
 * @param array $attributes
 * @return string
 */
function html_tag_js($src, $attributes = []){
	return html_tag('script', array_merge([
		'type'    => 'text/javascript',
		'charset' => 'utf-8',
		'src'     => $src,
	], $attributes));
}

/**
 * Build html date input
 * @param string $name
 * @param string $value
 * @param array $attributes
 * @return string
 */
function html_tag_date_input($name, $value = '', $attributes = []){
	$attributes['type'] = 'date';
	$attributes['name'] = $name;
	$attributes['value'] = ($value && strpos($value, '0000') !== false) ? date('Ymd', strtotime($value)) : '';
	return html_tag('input', $attributes);
}

/**
 * Build html time input
 * @param string $name
 * @param string $value
 * @param array $attributes
 * @return string
 */
function html_tag_date_time_input($name, $value = '', $attributes = []){
	$attributes['type'] = 'datetime-local';
	$attributes['name'] = $name;
	$attributes['value'] = ($value && strpos($value, '0000') !== false) ? date('Ymd H:i:s', strtotime($value)) : '';
	return html_tag('input', $attributes);
}

/**
 * Build DataList
 * @param string $id
 * @param array $data_map index array: [val=>title,...], or natural growth array: [title1, title2,...]
 * @return string
 */
function html_tag_data_list($id, $data_map = []){
	$opts = '';
	$is_assoc_array = is_assoc_array($data_map);
	foreach($data_map as $key => $val){
		if($is_assoc_array){
			$opts .= '<option value="'.ha($key).'" label="'.ha($val).'">';
		}else{
			$opts .= '<option value="'.ha($val).'">';
		}
	}
	return html_tag('datalist', ['id' => $id], $opts);
}

/**
 * submit input
 * @param mixed $value
 * @param array $attributes
 * @return string
 */
function html_tag_input_submit($value, $attributes = []){
	$attributes['type'] = 'submit';
	$attributes['value'] = $value;
	return html_tag('input', $attributes);
}

/**
 * no script support html
 * @param string $html
 * @return string
 */
function html_tag_no_script($html){
	return '<noscript>'.$html.'</noscript>';
}

/**
 * submit button
 * @param string $inner_html
 * @param array $attributes
 * @return string
 */
function html_tag_button_submit($inner_html, $attributes = []){
	$attributes['type'] = 'submit';
	return html_tag('button', $attributes, $inner_html);
}

/**
 * Build table node
 * @param array $data
 * @param array|false $headers header list [field name => alias, ...], if false, it means do not display the header
 * @param string $caption
 * @param array $attributes
 * @return string
 */
function html_tag_table($data, $headers = [], $caption = '', $attributes = []){
	$html = $caption ? html_tag('caption', [], $caption) : '';
	if(is_array($headers) && $data){
		$all_fields = array_keys(array_first($data));
		$headers = $headers ?: array_combine($all_fields, $all_fields);
		$html .= '<thead><tr>';
		foreach($headers as $alias){
			$html .= "<th>$alias</th>";
		}
		$html .= '</tr></thead>';
	}

	$html .= '<tbody>';
	foreach($data ?: [] as $row){
		$html .= '<tr>';
		if($headers){
			foreach($headers as $field => $alias){
				$html .= "<td>{$row[$field]}</td>";
			}
		}
		$html .= '</tr>';
	}
	$html .= '</tbody>';
	return html_tag('table', $attributes, $html);
}

/**
 * Construct HTML node attributes
 * Fix pattern, disabled HTML display when false
 * @param array $attributes
 * @return string
 */
function html_attributes(array $attributes = []){
	$attributes = array_clean_null($attributes);
	$html = [];
	foreach($attributes as $k => $v){
		if($k == 'disabled' && $v === false){
			continue;
		}
		if($k == 'pattern'){
			$html[] = "$k=\"".$v."\"";
		}else{
			$html[] = "$k=\"".ha($v)."\"";
		}
	}
	return join(' ', $html);
}

/**
 * Escape and truncate strings in HTML
 * @param string $str
 * @param number|null $len truncation length, empty means no truncation
 * @param null|string $tail append tail string character
 * @param bool $length_exceeded Exceeded length
 * @return string
 */
function h($str, $len = null, $tail = '...', &$length_exceeded = false){
	$str = cut_string($str, $len, $tail, $length_exceeded);
	return htmlspecialchars($str, ENT_IGNORE);
}

/**
 * Escape and truncate HTML node attribute string
 * @param string $str
 * @param int $len truncation length, empty means no truncation
 * @param string $tail append tail string character
 * @param bool $length_exceeded Exceeded length
 * @return string
 */
function ha($str, $len = 0, $tail = '...', &$length_exceeded = false){
	$str = cut_string($str, $len, $tail, $length_exceeded);
	return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * Convert plain text to HTML
 * @param string $text
 * @param null $len
 * @param string $tail
 * @param bool $over_length
 * @return string
 */
function text_to_html($text, $len = null, $tail = '...', &$over_length = false){
	if($len){
		$text = substr_utf8($text, $len, $tail, $over_length);
	}
	$html = htmlspecialchars($text);
	$html = str_replace("\r", '', $html);
	return str_replace([' ', "\n", "\t"], ['&nbsp;', '<br/>', '&nbsp;&nbsp;&nbsp;&nbsp;'], $html);
}

/**
 * Correct relative paths in HTML
 * @param string $html
 * @param string $page_url
 * @return string Return the original HTML if replacement fails
 */
function html_fix_relative_path($html, $page_url){
	//Match and replace all src="", href="" pattern tags
	$new_html = preg_replace_callback('/(<[^>]*?\s)(src|href)(=\s*[\'"])(.*?)([\'"])/iu', function($matches) use ($page_url){
		$matches[4] = http_fix_relative_url($matches[4], $page_url);
		array_shift($matches);
		return join('', $matches);
	}, $html);

	return $new_html ?: $html;
}

/**
 * todo
 * @param $html
 * @param $option
 * @return void
 */
function html_to_text($html, $option){
	$option = array_Merge([
		'trim'            => true,
		'image_replacer'  => '',
		'keep_line_break' => false,
	], $option);
}

/**
 * Highlight text
 * @param string $text
 * @param string $keyword
 * @param string $template
 * @return string Returns the HTML escaped string
 */
function html_text_highlight($text, $keyword, $template = '<span class="highlight">%s</span>'){
	if(!$keyword){
		return h($text);
	}
	return preg_replace_callback('/'.preg_quote(h($keyword)).'/', function($matches) use ($template){
		return sprintf($template, $matches[0]);
	}, h($text));
}

/**
 * Construct HTML meta tags
 * @param string $equiv
 * @param string $content
 * @return string
 */
function html_tag_meta($equiv, $content = ''){
	return '<meta http-equiv="'.$equiv.'" content="'.$content.'" />';
}

/**
 * Use html meta to redirect pages
 * @param string $url jump target path
 * @param int $timeout_sec timeout
 * @return string html
 */
function html_meta_redirect($url, $timeout_sec = 0){
	return html_tag_meta('refresh', $timeout_sec.'; URL='.$url);
}

/**
 * Build CSP meta tag
 * @param array $csp_rules
 * @param string $report_uri
 * @param bool $report_only
 * @return string
 * @throws \Exception
 */
function html_meta_csp(array $csp_rules, $report_uri = '', $report_only = false){
	if($report_only && !$report_uri){
		throw new Exception('CSP report uri required.');
	}
	$equiv = $report_only ? CSP_REPORT_ONLY_PREFIX : CSP_PREFIX;
	$content = join('; ', $csp_rules).';';
	$content .= $report_uri ? csp_report_uri($report_uri).';' : '';
	return html_tag_meta($equiv, $content);
}

/**
 * HTML numeric comparison (converted to a string and then strictly compared)
 * @param string|number $str1
 * @param string|number|array $data
 * @return bool whether they are equal
 */
function html_value_compare($str1, $data){
	$str1 = (string)$str1;
	if(is_array($data)){
		foreach($data as $val){
			if((string)$val === $str1){
				return true;
			}
		}
		return false;
	}
	return $str1 === (string)$data;
}

/**
 * Set static resource version control items
 * @param array $patch_config version configuration table, format such as: abc/foo.js => '2020', priority is given to matching rules with shorter lengths
 * @return array all configurations
 */
function static_version_set(array $patch_config = []){
	static $_config = [];
	if($patch_config){
		foreach($patch_config as $k => $v){
			$_config[$k] = $v;
		}
		uksort($_config, function($k1, $k2){
			return strlen($k1) < strlen($k2);
		});
	}
	return $_config;
}

/**
 * Static resource version patch
 * @param string $src
 * @param bool $matched
 * @return string
 */
function static_version_patch($src, &$matched = false){
	$config = static_version_set();
	foreach($config as $k => $version){
		$reg = static_version_statement_quote($k);
		if(preg_match($reg, $src) && $version){
			$matched = true;
			if(is_callable($version)){
				return call_user_func($version, $src);
			}
			return $src.(stripos($src, '?') !== false ? '&' : '?').$version;
		}
	}
	return $src;
}

/**
 * Static resource version wildcard escape
 * @param string $str
 * @return string
 */
function static_version_statement_quote($str){
	$map = [
		':' => '\\:',
		'.' => '\\.',
		'*' => '.*?',
	];
	$str = str_replace(array_keys($map), array_values($map), $str);
	return "|$str|";
}

/**
 * Fix the data submitted by input:datetime or input:datetime-local in HTML5 browser
 * The time format submitted by H5 may be Ymd\TH:i
 * @param string $datetime_str_from_h5
 * @param int $fix_seconds Second correction. The H5 input box does not have second precision when submitted. This can be set to 0 (such as the start time) or 59 (such as the end time) to correct the second unit value.
 * @return string
 * @throws \Exception
 */
function fix_browser_datetime($datetime_str_from_h5, $fix_seconds = 0){
	if(!$datetime_str_from_h5){
		return '';
	}
	if(preg_match('/[^:]\d{2}:\d{2}$/', $datetime_str_from_h5)){
		$datetime_str_from_h5 .= ':'.str_pad($fix_seconds.'', 2, STR_PAD_LEFT, '0');
	}
	return (new DateTime($datetime_str_from_h5))->format('Y-m-d H:i:s');
}

/**
 * convert image file to data url
 * @example echo "<img src=".img_to_data_url("a.jpg")."/>";
 * @param string $img_file
 * @return string data url
 * @throws \Exception
 */
function img_to_data_url($img_file){
	if(!is_file($img_file)){
		throw new Exception('Image file no exists');
	}
	$mime = get_mime_by_file($img_file);
	if(!$mime){
		throw new Exception('No mime detected');
	}
	$raw_data = file_get_contents($img_file);
	if(!$raw_data){
		throw new Exception('Image file empty or no readable');
	}
	return "data:{$mime};base64,".base64_encode($raw_data);
}
