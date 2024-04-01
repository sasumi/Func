<?php
/**
 * Html 快速操作函数
 */
namespace LFPhp\Func;
use Exception;

const HTML_SELF_CLOSING_TAGS = [
	'area',
	'base',
	'br',
	'col',
	'embed',
	'hr',
	'img',
	'input',
	'link',
	'meta',
	'param',
	'source',
	'track',
	'wbr',
	'command',
	'keygen',
	'menuitem',
];


/**
 * 构建select节点，支持optgroup模式
 * @param string $name
 * @param array $options 选项数据，
 * 如果是分组模式，格式为：[value=>text, label=>options, ...]
 * 如果是普通模式，格式为：options: [value1=>text, value2=>text,...]
 * @param string|array $current_value
 * @param string $placeholder
 * @param array $attributes
 * @return string
 */
function html_tag_select($name, array $options, $current_value = null, $placeholder = '', $attributes = []){
	$attributes = array_merge($attributes, [
		'name'        => $name ?: null,
		'placeholder' => $placeholder ?: null
	]);

	//多选
	if(is_array($current_value)){
		$attributes['multiple'] = 'multiple';
	}

	$option_html = $placeholder ? html_tag_option($placeholder, '') : '';

	//单层option
	if(count($options, COUNT_RECURSIVE) == count($options, COUNT_NORMAL)){
		$option_html .= html_tag_options($options, $current_value);
	}

	//optgroup支持
	else{
		foreach($options as $var1 => $var2){
			if(is_array($var2)){
				$option_html .= html_tag_option_group($var1, $var2, $current_value);
			} else {
				$option_html .= html_tag_option($var2, $var1, $current_value);
			}
		}
	}
	return html_tag('select', $attributes, $option_html);
}

/**
 * 构建select选项
 * @param array $options [value=>text,...] option data 选项数组
 * @param string|array $current_value 当前值
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
 * 构建option节点
 * @param string $text 文本，空白将被转义成&nbsp;
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
 * 构建optgroup节点
 * @param string $label
 * @param array $options
 * @param string|array $current_value 当前值
 * @return string
 */
function html_tag_option_group($label, $options, $current_value = null){
	$option_html = html_tag_options($options, $current_value);
	return html_tag('optgroup', ['label' => $label], $option_html);
}

/**
 * 构建textarea
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
 * 构建hidden表单节点
 * @param string $name
 * @param string $value
 * @return string
 */
function html_tag_hidden($name, $value = ''){
	return html_tag('input', ['type' => 'hidden', 'name' => $name, 'value' => $value]);
}

/**
 * 构建数据hidden列表
 * @param array $data_list 数据列表（可以多维数组）
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
 * 构建Html数字输入
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
 * @param string $name
 * @param array $options 选项[value=>title,...]格式
 * @param string $current_value
 * @param string $wrapper_tag 每个选项外部包裹标签，例如li、div等
 * @param array $radio_extra_attributes 每个radio额外定制属性
 * @return string
 */
function html_tag_radio_group($name, $options, $current_value = '', $wrapper_tag = '', $radio_extra_attributes = []){
	$html = [];
	foreach($options as $val=>$ti){
		$html[] = html_tag_radio($name, $val, $ti, html_value_compare($val, $current_value), $radio_extra_attributes);
	}

	if($wrapper_tag){
		$rst = '';
		foreach($html as $h){
			$rst .= ' '.html_tag($wrapper_tag, [], $h);
		}
		return $rst;
	} else {
		return join(' ', $html);
	}
}

/**
 * 构建 radio按钮
 * 使用 label>(input:radio+{text}) 结构
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
	return html_tag('label', [], html_tag('input', $attributes).$title);
}

/**
 * @param string $name
 * @param array $options 选项[value=>title,...]格式
 * @param string|array $current_value
 * @param string $wrapper_tag 每个选项外部包裹标签，例如li、div等
 * @param array $checkbox_extra_attributes 每个checkbox额外定制属性
 * @return string
 */
function html_tag_checkbox_group($name, $options, $current_value = null, $wrapper_tag = '', $checkbox_extra_attributes = []){
	$html = [];
	foreach($options as $val=>$ti){
		$html[] = html_tag_checkbox($name, $val, $ti, html_value_compare($val, $current_value), $checkbox_extra_attributes);
	}
	if($wrapper_tag){
		$rst = '';
		foreach($html as $h){
			$rst .= ' '.html_tag($wrapper_tag, [], $h);
		}
		return $rst;
	} else {
		return join(' ', $html);
	}
}

/**
 * 构建 checkbox按钮
 * 使用 label>(input:checkbox+{text}) 结构
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
	return html_tag('label', [], $checkbox.$title);
}

/**
 * 构建进度条（如果没有设置value，可充当loading效果使用）
 * @param null|number $value
 * @param null|number $max
 * @param array $attributes
 * @return string
 * @throws \Exception
 */
function html_tag_progress($value = null, $max = null, $attributes = []){
	//如果有max，必须大于0
	if(isset($max) && floatval($max) <= 0){
		throw new Exception('Progress max should bigger or equal to zero');
	}
	if(isset($value)){
		//有设置max，value范围必须在0~max
		if(isset($max) && $value > $max){
			throw new Exception('Progress value should less or equal than max');
		}
		//没有设置max，value范围必须在0~1
		if($value > 1 || $value < 0){
			throw new Exception('Progress value should between 0 to 1');
		}
	}
	$attributes['max'] = $max;
	$attributes['value'] = $value;
	return html_tag('progress', $attributes);
}

/**
 * Html循环滚动进度条
 * alias to htmlProgress
 * @param array $attributes
 * @return string
 */
function html_loading_bar($attributes = []){
	return html_tag_progress(null, null, $attributes);
}

/**
 * Html范围选择器
 * @param string $name
 * @param string $value 当前值
 * @param int $min 最小值
 * @param int $max 最大值
 * @param int $step 步长
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
 * 获取HTML摘要信息
 * @param string $html_content
 * @param int $len
 * @return string
 */
function html_abstract($html_content, $len = 200){
	$str = str_replace(array("\n", "\r"), "", $html_content);
	$str = preg_replace('/<br([^>]*)>/i', '$$NL', $str);
	//todo convert <p> <div> to line break
	$str = strip_tags($str);
	$str = html_entity_decode($str, ENT_QUOTES);
	$str = h($str, $len);
	$str = str_replace('$$NL', '<br/>', $str);

	//移除头尾空白行
	$str = preg_replace('/^(<br[^>]*>)*/i', '', $str);
	$str = preg_replace('/(<br[^>]*>)*$/i', '', $str);
	return $str;
}

/**
 * 构建Html input:text文本输入框
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
 * 构建Html日期输入框
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
 * 构建Html日期输入框
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
 * 构建Html日期+时间输入框
 * @param string $name
 * @param string $datetime_or_timestamp
 * @param array $attributes
 * @return string
 */
function html_tag_datetime($name, $datetime_or_timestamp = '', $attributes = []){
	$attributes['type'] = 'datetime-local';
	$attributes['name'] = $name;
	$attributes['step'] = 1; //必须填充step 才能在空值情况出现秒的选择
	if($datetime_or_timestamp){
		$attributes['value'] = is_numeric($datetime_or_timestamp) ? date('Y-m-d\TH:i:s', $datetime_or_timestamp) : date('Y-m-d\TH:i:s', strtotime($datetime_or_timestamp));
	} else {
		$attributes['value'] = '0000-00-00T00:00:00';
	}
	return html_tag('input', $attributes);
}

/**
 * 构建Html月份选择器
 * @param string $name
 * @param int|null $current_month 当前月份，范围1~12表示
 * @param string $format 月份格式，与date函数接受格式一致
 * @param array $attributes 属性
 * @return string
 */
function html_tag_month_select($name, $current_month = null, $format = 'm', $attributes = []){
	$opts = [];
	$format = $format ?: 'm';
	for($i=1; $i<=12; $i++){
		$opts[$i] = date($format, strtotime('1970-'.$current_month.'-01'));
	}
	return html_tag_select($name, $opts, $current_month, $attributes['placeholder'], $attributes);
}

/**
 * 构建Html年份选择器
 * @param string $name
 * @param int|null $current_year 当前年份
 * @param int $start_year 开始年份（缺省为1970）
 * @param string $end_year 结束年份（缺省为今年）
 * @param array $attributes
 * @return string
 */
function html_tag_year_select($name, $current_year = null, $start_year = 1970, $end_year = '', $attributes = []){
	$start_year = $start_year ?: 1970;
	$end_year = $end_year ?: date('Y');
	$opts = [];
	for($i = $start_year; $i<=$end_year; $i++){
		$opts[$i] = $i;
	}
	return html_tag_select($name, $opts, $current_year, $attributes['placeholder'], $attributes);
}

/**
 * 构建HTML节点
 * @param string $tag
 * @param array $attributes
 * @param string $inner_html
 * @return string
 */
function html_tag($tag, $attributes = [], $inner_html = ''){
	$tag = strtolower($tag);
	$single_tag = in_array($tag, HTML_SELF_CLOSING_TAGS);
	$html = "<$tag ";

	//针对textarea标签，识别value填充到inner_html中
	if($tag === 'textarea' && isset($attributes['value'])){
		$inner_html = $inner_html ?: h($attributes['value']);
		unset($attributes['value']);
	}

	$html .= html_attributes($attributes);
	$html .= $single_tag ? "/>" : ">".$inner_html."</$tag>";
	return $html;
}

/**
 * 构建HTML链接
 * @param string $inner_html
 * @param string $href
 * @param array $attributes
 * @return string
 */
function html_tag_link($inner_html, $href = '', $attributes = []){
	$attributes['href'] = $href;
	return html_tag('a', $attributes, $inner_html);
}

/***
 * 构建css节点
 * @param string $href
 * @param array $attributes
 * @return string
 */
function html_tag_css($href, $attributes = []){
	return html_tag('link', array_merge([
		'type'  => 'text/css',
		'rel'   => 'stylesheet',
		'media' => 'all',
		'href'  => $href
	], $attributes));
}

/***
 * 构建js节点
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
 * 构建Html日期输入
 * @param string $name
 * @param string $value
 * @param array $attributes
 * @return string
 */
function html_tag_date_input($name, $value = '', $attributes = []){
	$attributes['type'] = 'date';
	$attributes['name'] = $name;
	$attributes['value'] = ($value && strpos($value, '0000') !== false) ? date('Y-m-d', strtotime($value)) : '';
	return html_tag('input', $attributes);
}

/**
 * 构建Html时间输入
 * @param string $name
 * @param string $value
 * @param array $attributes
 * @return string
 */
function html_tag_date_time_input($name, $value = '', $attributes = []){
	$attributes['type'] = 'datetime-local';
	$attributes['name'] = $name;
	$attributes['value'] = ($value && strpos($value, '0000') !== false) ? date('Y-m-d H:i:s', strtotime($value)) : '';
	return html_tag('input', $attributes);
}

/**
 * 构建DataList
 * @param string $id
 * @param array $data [val=>title,...]
 * @return string
 */
function html_tag_data_list($id, $data = []){
	$opts = '';
	foreach($data as $value=>$label){
		$opts .= '<option value="'.ha($value).'" label="'.ha($label).'">';
	}
	return html_tag('datalist', ['id' => $id], $opts);
}

/**
 * submit input
 * @param mixed $value
 * @param array $attributes
 * @return string
 */
function html_tag_input_submit($value, $attributes=[]){
	$attributes['type'] ='submit';
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
function html_tag_button_submit($inner_html, $attributes=[]){
	$attributes['type'] ='submit';
	return html_tag('button', $attributes, $inner_html);
}

/**
 * 构建table节点
 * @param array $data
 * @param array|false $headers 表头列表 [字段名 => 别名, ...]，如为false，表示不显示表头
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
		foreach($headers as $field => $alias){
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
 * 构建HTML节点属性
 * 修正pattern，disabled在false情况下HTML表现
 * @param array $attributes
 * @return string
 */
function html_attributes(array $attributes = []){
	$attributes = array_clear_null($attributes);
	$html = [];
	foreach($attributes as $k => $v){
		if($k == 'disabled' && $v === false){
			continue;
		}
		if($k == 'pattern'){
			$html[] = "$k=\"".$v."\"";
		} else{
			$html[] = "$k=\"".ha($v)."\"";
		}
	}
	return join(' ', $html);
}

/**
 * 转化明文文本到HTML
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
	$html = str_replace(array(' ', "\n", "\t"), array('&nbsp;', '<br/>', '&nbsp;&nbsp;&nbsp;&nbsp;'), $html);
	return $html;
}

/**
 * 高亮文本
 * @param string $text
 * @param string $keyword
 * @param string $template
 * @return string 返回HTML转义过的字符串
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
 * 构建HTML meta标签
 * @param string $equiv
 * @param string $content
 * @return string
 */
function html_tag_meta($equiv, $content = ''){
	return '<meta http-equiv="'.$equiv.'" content="'.$content.'" />';
}

/**
 * 使用 html meta 进行页面跳转
 * @param string $url 跳转目标路径
 * @param int $timeout_sec 超时时间
 * @return string html
 */
function html_meta_redirect($url, $timeout_sec = 0){
	return html_tag_meta('refresh', $timeout_sec.'; URL='.$url);
}

/**
 * 构建 CSP meta标签
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
 * HTML数值比较（通过转换成字符串之后进行严格比较）
 * @param string|number $str1
 * @param string|number|array $data
 * @return bool 是否相等
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
 * 设置静态资源版本控制项
 * @param array $patch_config 版本配置表，格式如：abc/foo.js => '2020'，优先匹配长度短的规则
 * @return array 所有配置
 */
function static_version_set(array $patch_config = []){
	static $_config = [];
	if($patch_config){
		foreach($patch_config as $k=>$v){
			$_config[$k] = $v;
		}
		uksort($_config, function($k1, $k2){
			return strlen($k1) < strlen($k2);
		});
	}
	return $_config;
}

/**
 * 静态资源版本补丁
 * @param string $src
 * @param bool $matched
 * @return string
 */
function static_version_patch($src, &$matched = false){
	$config = static_version_set();
	foreach($config as $k=>$version){
		$reg = static_version_statement_quote($k);
		if(preg_match($reg, $src)){
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
 * 静态资源版本通配符转义
 * @param string $str
 * @return string
 */
function static_version_statement_quote($str){
	$map = array(
		':' => '\\:',
		'.' => '\\.',
		'*' => '.*?',
	);
	$str = str_replace(array_keys($map), array_values($map), $str);
	return "|$str|";
}

/**
 * 修正浏览器 HTML5 中 input:datetime或者 input:datetime-local 提交过来的数据
 * @param string $datetime_str_from_h5
 * @return string|null
 * @throws \Exception
 */
function fix_browser_datetime($datetime_str_from_h5){
	return $datetime_str_from_h5 ? (new \DateTime($datetime_str_from_h5))->format('Y-m-d H:i:s') : null;
}
