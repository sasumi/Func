<?php

use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\Set\PhpDocumentor;
use Jasny\PhpdocParser\Tag\FlagTag;
use function LFPhp\Func\explode_by;
use function LFPhp\Func\glob_recursive;

const PRJ_ROOT = __DIR__.'/../';
const NL = PHP_EOL;
include PRJ_ROOT."vendor/autoload.php";

$output_file = PRJ_ROOT.'/functions.md';
$fp = fopen($output_file, 'w');

$files = glob_recursive(PRJ_ROOT.'/src/*');
$mod_index = 0;
foreach($files as $f){
	$first_doc = [];
	$mod_name = preg_replace('/\..*$/', '', basename($f));
	$file_content = file_get_contents($f);
	if(preg_match('/\/\*\*(.*?)\*\//ism', $file_content, $matches)){
		$lines = explode_by("\n", $matches[1]);
		foreach($lines as $i => $line){
			$line = preg_replace("/^\**\s*/", "", $line);
			if($line){
				$lines[$i] = $line;
			}else{
				unset($lines[$i]);
			}
		}
		$first_doc = $lines;
	}
	$mod_desc = '';
	if($first_doc){
		$mod_desc = ' > '.join(NL.' > ', $first_doc).NL;
	}
	fwrite($fp, "## ".(++$mod_index).". ".strtoupper($mod_name).NL.$mod_desc.NL);

	if(preg_match_all('/^function\s(\w+)/ism', $file_content, $matches)){
		$fun_doc = '';
		$func_index = 0;
		foreach($matches[1] as $func){
			$full_func = '\\LFPhp\\Func\\'.$func;
			if(function_exists($full_func)){
				$meta = [];
				try{
					$doc = (new ReflectionFunction($full_func))->getDocComment();
					$customTags = [new FlagTag('important')];
					$tags = PhpDocumentor::tags()->with($customTags);
					$parser = new PhpdocParser($tags);
					$meta = $parser->parse($doc);
				}catch(\Exception $e){
				}

				$ps = [];
				$rs = '';
				$p_str = '';
				$r_str = '';

				if(isset($meta['params'])){
					$p_str = '#### 参数'.NL;
					foreach($meta['params'] as $name => $p){
						$type = isset($p['type']) ? $p['type'] : 'mixed';
						$desc = isset($p['description']) ? $p['description'] : '';
						$p_str .= " - {".$type."} *$name* $desc".NL;
						$ps[] = "\$$name";
					}
				}

				if(isset($meta['return'])){
					$desc = isset($meta['return']['description']) ? $meta['return']['description'] : '';
					$r_str = NL.'#### 返回值'.NL.' - '.$meta['return']['type']." ".$desc.NL.NL;
					$rs = ': '.$meta['return']['type'];
				}
				$ps = join(",", $ps);
				$fun_doc .= "### ".$mod_index.'.'.(++$func_index)." $func($ps)$rs".NL.$p_str.$r_str;
			}
		}
		fwrite($fp, $fun_doc.NL.NL);
	}
}

fclose($fp);
