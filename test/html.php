<?php

include __DIR__.'/../vendor/autoload.php';

$tree = [
	['name'=>'公司', 'value'=>'1', 'children'=>[
		['name'=>'行政部', 'value'=>'2'],
		['name'=>'IT部', 'value'=>'3', 'children'=>[
			['name'=>'前端组', 'value'=>'4'],
			['name'=>'设计组', 'value'=>'5', 'children'=>[
				['name'=>'UI小组', 'value'=>'4'],
				['name'=>'重构小组', 'value'=>'4'],
			]],
			['name'=>'后端组', 'value'=>'4'],
			['name'=>'终端组', 'value'=>'4'],
		]],
		['name'=>'客服部', 'value'=>'2', 'children'=>[
			['name'=>'售前组', 'value'=>'4'],
			['name'=>'售后组', 'value'=>'4'],
		]],
		['name'=>'行政部', 'value'=>'2'],
		['name'=>'销售部', 'value'=>'2', 'children'=>[
			['name'=>'华南销售组', 'value'=>'4'],
			['name'=>'华东销售组', 'value'=>'4'],
			['name'=>'华中销售组', 'value'=>'4'],
			['name'=>'北上广深销售组', 'value'=>'4'],
			['name'=>'西北销售组', 'value'=>'4'],
		]],
	]]
];

$options = \LFPhp\Func\html_tree_to_options($tree);
foreach($options as [$text, $val]){
	echo $text.'<br/>';
}
;?>
<select name="" size="1">
	<?php foreach($options as [$text, $val]){
		echo '<option value="'.$val.'">'.$text.'</option>';
	}
	?>
</select>
