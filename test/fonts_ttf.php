<?php
error_reporting(0);

use function LFPhp\Func\array_orderby;
use function LFPhp\Func\get_windows_fonts;
use function LFPhp\Func\ha;
use function LFPhp\Func\ttf_info;

include "../vendor/autoload.php";
$example_text = $_GET['text'] ?: 'Hello World';
$fonts = get_windows_fonts();
$ttfs = [];

foreach($fonts as $font){
	if(strcasecmp(substr($font, -4), '.ttf') === 0){
		$ttfs[] = ttf_info($font);
	}
}

$ttfs = array_orderby($ttfs, 'name', SORT_ASC);
?>
<style>
	html {font-size:14px; background-color:#666;}
	body {padding:2em;}
	table {border-collapse:collapse; border-spacing:0; border:10px solid white; border-bottom-width:20px; box-shadow:0px 10px 10px #333;}
	thead {background-color:#fff;}
	tbody {background-color:#eee;}
	th, td {border:1px solid #ccc; padding:0.2em 1em; text-align:left;}
	th {padding:0.5em 1em;  text-transform:capitalize}
	th:first-child {padding:0.2em 0.3em;}
	tbody tr:nth-child(even) td {background-color:#e7e7e7;}
	tbody td:first-child {padding:0.25em 0 !important; background-color:#fff !important;}
	.example-text {max-width:20em; word-break:break-all;font-size:1.5em;}
	#form {margin:0; padding:0; white-space:nowrap; display:flex;}
	#form input {height:30px; line-height:30px; box-sizing:border-box; flex:1}
	#form input[type=search] {border:none; border-bottom:1px solid gray; outline:none; font-size:1.5rem;}
	#form input[type=submit] {max-width:5em; margin-left:0.5em;}
</style>
<table>
	<thead>
		<tr>
			<th>
				<form action="?" method="get" id="form">
					<input type="search" name="text" value="<?=ha($example_text);?>" list="list">
					<datalist id="list">
						<option value="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789">Text</option>
						<option value="0123456789">Number</option>
					</datalist>
					<input type="submit" value="Save">
				</form>
			</th>
			<th>name</th>
			<th>style</th>
			<th>full name</th>
			<th>full name Extend</th>
			<th>var name</th>
			<th>version</th>
<!--			<th>trademark</th>-->
			<th>company</th>
<!--			<th>author</th>-->
<!--			<th>description</th>-->
<!--			<th>font_website</th>-->
<!--			<th>company_website</th>-->
<!--			<th>copyrights</th>-->
<!--			<th>license_url</th>-->
		</tr>
	</thead>
	<tbody>
		<?php foreach($ttfs as $ttf):?>
		<tr>
			<td class="example-text-cell">
				<span class="example-text" style="font-family:<?=$ttf['full_name_x'];?>"><?=$example_text;?></span>
			</td>
		<td><?=$ttf['name'];?></td>
		<td><?=$ttf['style'];?></td>
		<td><?=$ttf['full_name'];?></td>
		<td><?=$ttf['full_name_x'];?></td>
		<td><?=$ttf['var_name'];?></td>
		<td><?=$ttf['version'];?></td>
<!--		<td>--><?//=$ttf['trademark'];?><!--</td>-->
		<td><?=$ttf['company'];?></td>
<!--		<td>--><?//=$ttf['author'];?><!--</td>-->
<!--		<td>--><?//=$ttf['description'];?><!--</td>-->
<!--		<td>--><?//=$ttf['font_website'];?><!--</td>-->
<!--		<td>--><?//=$ttf['company_website'];?><!--</td>-->
<!--		<td>--><?//=$ttf['copyrights'];?><!--</td>-->
<!--		<td>--><?//=$ttf['license_url'];?><!--</td>-->
		</tr>
	<? endforeach;?>
	</tbody>
</table>
