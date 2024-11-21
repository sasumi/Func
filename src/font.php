<?php
/**
 * Font Handle Functions
 */
namespace LFPhp\Func;

/**
 * Get ttf font file info
 * @param $ttf_file
 * @return array
 */
function ttf_info($ttf_file){
	$fd = fopen($ttf_file, "r");
	$text = fread($fd, filesize($ttf_file));
	fclose($fd);

	$number_of_tables = hexdec(_ttf_dec2ord($text[4])._ttf_dec2ord($text[5]));
	$offset_storage_dec = $ntOffset = 0;
	$number_name_records_dec = null;

	for($i = 0; $i < $number_of_tables; $i++){
		$tag = $text[12 + $i*16].$text[12 + $i*16 + 1].$text[12 + $i*16 + 2].$text[12 + $i*16 + 3];
		if($tag == 'name'){
			$ntOffset = hexdec(_ttf_dec2ord($text[12 + $i*16 + 8])._ttf_dec2ord($text[12 + $i*16 + 8 + 1])._ttf_dec2ord($text[12 + $i*16 + 8 + 2])._ttf_dec2ord($text[12 + $i*16 + 8 + 3]));
			$offset_storage_dec = hexdec(_ttf_dec2ord($text[$ntOffset + 4])._ttf_dec2ord($text[$ntOffset + 5]));
			$number_name_records_dec = hexdec(_ttf_dec2ord($text[$ntOffset + 2])._ttf_dec2ord($text[$ntOffset + 3]));
		}
	}

	$storage_dec = $offset_storage_dec + $ntOffset;
	$storage_hex = strtoupper(dechex($storage_dec));
	$font_tags = [];

	for($j = 0; $j < $number_name_records_dec; $j++){
		$platform_id_dec = hexdec(_ttf_dec2ord($text[$ntOffset + 6 + $j*12 + 0])._ttf_dec2ord($text[$ntOffset + 6 + $j*12 + 1]));
		$name_id_dec = hexdec(_ttf_dec2ord($text[$ntOffset + 6 + $j*12 + 6])._ttf_dec2ord($text[$ntOffset + 6 + $j*12 + 7]));
		$string_length_dec = hexdec(_ttf_dec2ord($text[$ntOffset + 6 + $j*12 + 8])._ttf_dec2ord($text[$ntOffset + 6 + $j*12 + 9]));
		$string_offset_dec = hexdec(_ttf_dec2ord($text[$ntOffset + 6 + $j*12 + 10])._ttf_dec2ord($text[$ntOffset + 6 + $j*12 + 11]));

		if(!empty($name_id_dec) and empty($font_tags[$name_id_dec])){
			for($l = 0; $l < $string_length_dec; $l++){
				if(ord($text[$storage_dec + $string_offset_dec + $l]) == '0'){
					continue;
				}else{
					$font_tags[$name_id_dec] .= ($text[$storage_dec + $string_offset_dec + $l]);
				}
			}
		}
	}
	return [
		'name'            => $font_tags[1],
		'style'           => $font_tags[2],
		'full_name'       => $font_tags[3],
		'full_name_x'     => $font_tags[4],
		'version'         => str_ireplace('version ', '', $font_tags[5]),
		'var_name'        => $font_tags[6],
		'trademark'       => $font_tags[7],
		'company'         => $font_tags[8],
		'author'          => $font_tags[9],
		'description'     => $font_tags[10],
		'font_website'    => $font_tags[11],
		'company_website' => $font_tags[12],
		'copyrights'      => $font_tags[13],
		'license_url'     => $font_tags[14],
	];
}

/**
 * Get the Windows system font list (in file format, not necessarily accurate)
 * @return string[]
 */
function get_windows_fonts(){
	$font_dir = $_SERVER['SystemRoot'].'/Fonts';
	$files = glob_recursive($font_dir.'/*');
	foreach($files as $k => $file){
		if(is_dir($file)){
			unset($files[$k]);
		}
	}
	return array_values($files);
}

function _ttf_dec2ord($dec){
	return _ttf_dec2hex(ord($dec));
}

function _ttf_dec2hex($dec){
	return str_repeat('0', 2 - strlen(($hex = strtoupper(dechex($dec))))).$hex;
}
