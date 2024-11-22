<?php

use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\Set\PhpDocumentor;
use Jasny\PhpdocParser\Tag\FlagTag;
use function LFPhp\Func\explode_by;
use function LFPhp\Func\glob_recursive;

const PRJ_ROOT = __DIR__.'/../';
const NL = PHP_EOL;
include PRJ_ROOT."vendor/autoload.php";

function resolve_function_description($doc){
	$lines = explode("\n", $doc);
	$desc = [];
	foreach($lines as $line){
		$line = trim($line);
		if(preg_match('/\*\s*\@/', $line) || preg_match('/\*\/$/', $line)){
			continue;
		}
		else {
			$line = preg_replace("/^\/\*+/", '', $line);
			$line = preg_replace("/\*\s*/", '', $line);
			$line = trim($line);
			if($line){
				$desc[] = $line;
			}
		}
	}
	return join("\n", $desc);
}

$files = glob_recursive(PRJ_ROOT.'/src/*');
foreach($files as $f){
	echo "Processing file: $f", PHP_EOL;

	$output_file = PRJ_ROOT.'/doc/'.preg_replace('/\..*?$/', '', basename($f)).'.md';
	$fp = fopen($output_file, 'w');

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
	fwrite($fp, "# ".ucfirst($mod_name).NL.$mod_desc.NL);

	if(preg_match_all('/^function\s(\w+)/ism', $file_content, $matches)){
		$fun_doc = '';
		$func_index = 0;
		foreach($matches[1] as $func){
			$full_func = '\\LFPhp\\Func\\'.$func;
			if(function_exists($full_func)){
				$meta = [];
				$fun_desc = '';
				try{
					$rf = new ReflectionFunction($full_func);
					$doc = $rf->getDocComment();
					$fun_desc = resolve_function_description($doc);
					$customTags = [new FlagTag('important')];
					$tags = PhpDocumentor::tags()->with($customTags);
					$parser = new PhpdocParser($tags);
					$meta = $parser->parse($doc);
				}catch(\Exception $e){
					continue;
				}

				$ps = [];
				$rs = '';
				$p_str = '';
				$r_str = '';

				if(isset($meta['params'])){
					$p_str = '#### Parameters'.NL;
					foreach($meta['params'] as $name => $p){
						$type = isset($p['type']) ? $p['type'] : 'mixed';
						$desc = isset($p['description']) ? $p['description'] : '';
						$p_str .= " - {".$type."} *\$$name* $desc".NL;
						$ps[] = "\$$name";
					}
				}

				if(isset($meta['return'])){
					$desc = isset($meta['return']['description']) ? $meta['return']['description'] : '';
					$r_str = NL.'#### Returns'.NL.' - '.$meta['return']['type']." ".$desc.NL.NL;
					$rs = ': '.$meta['return']['type'];
				}
				$ps = join(",", $ps);
				$fun_doc .= "## ".(++$func_index).". $func($ps)$rs".NL
					.($fun_desc ? $fun_desc.NL : '')
					.$p_str.$r_str;
			}
		}
		fwrite($fp, $fun_doc.NL.NL);
	}
}

fclose($fp);
echo "DONE";
