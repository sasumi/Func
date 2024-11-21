<?php
/**
 * Color Enhancement Functions
 */
namespace LFPhp\Func;

use Exception;

/**
 * Convert hexadecimal color format to RGB format (array)
 * @param string $hex_color #ff00bb
 * @return array
 * @throws \Exception
 */
function color_hex2rgb($hex_color){
	$hex_color = ltrim($hex_color, '#');
	if(strlen($hex_color) === 3){
		$hex = [$hex_color[0].$hex_color[0], $hex_color[1].$hex_color[1], $hex_color[2].$hex_color[2]];
	}else if(strlen($hex_color) === 6){
		$hex = [$hex_color[0].$hex_color[1], $hex_color[2].$hex_color[3], $hex_color[4].$hex_color[5]];
	}else{
		throw new Exception('Color format error:'.$hex_color);
	}
	return array_map('hexdec', $hex);
}

/**
 * Convert RGB format to hexadecimal color format
 * @param array $rgb [r,g,b]
 * @param string $prefix
 * @return string
 */
function color_rgb2hex(array $rgb, $prefix = '#'){
	return $prefix.str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT).
		str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT).
		str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);
}

/**
 * Convert RGB format to HSL format
 * @param array $rgb
 * @return float[] [h,s,l]
 */
function color_rgb2hsl(array $rgb){
	$r     = $rgb[0] / 255;
	$g     = $rgb[1] / 255;
	$b     = $rgb[2] / 255;
	$min   = min($r, $g, $b);
	$max   = max($r, $g, $b);
	$delta = $max - $min;
	$h     = 0;
	$s     = 0;
	$l     = ($max + $min) / 2;

	if ($max != $min) {
		$s = $delta / ($max + $min);
		if ($l >= 0.5) {
			$s = $delta / (2 - $max - $min);
		}
		_rgb_hsl_hue($h, $r, $g, $b, $max, $delta);
	}
	return [
		$h * 360,
		$s * 100,
		$l * 100
	];
}

/**
 * Convert HSL format to RGB format
 * @param array $hsl [h,s,l]
 * @return int[] [r,g,b]
 */
function color_hsl2rgb(array $hsl){
	[$h, $s, $l] = $hsl;
	$s /= 100;
	$l /= 100;
	$c = (1 - abs((2*$l) - 1))*$s;
	$x = $c*(1 - abs(fmod(($h/60), 2) - 1));
	$m = $l - ($c/2);
	$r = $c;
	$g = 0;
	$b = $x;

	if($h < 180){
		_hsl_rgb_low($r, $g, $b, $c, $x, $h);
	}elseif($h < 300){
		_hsl_rgb_high($r, $g, $b, $c, $x, $h);
	}

	return array(
		(int)round(($r + $m)*255),
		(int)round(($g + $m)*255),
		(int)round(($b + $m)*255),
	);
}

/**
 * Convert RGB format to CMYK format
 * @param array $rgb
 * @return array [c,m,y,k]
 */
function color_rgb2cmyk(array $rgb){
	$rgbp = array(
		$rgb[0]/255*100,
		$rgb[1]/255*100,
		$rgb[2]/255*100,
	);
	$k = 100 - max($rgbp);
	return [
		((100 - $rgbp[0] - $k)/(100 - $k))*100,
		((100 - $rgbp[1] - $k)/(100 - $k))*100,
		((100 - $rgbp[2] - $k)/(100 - $k))*100,
		$k,
	];
}

/**
 * Convert CMYK format to RGB format
 * @param array $cmyk
 * @return int[] [r,g,b]
 */
function cmyk_to_rgb(array $cmyk) {
	[$c, $m, $y, $k] = $cmyk;
	$c /= 100;
	$m /= 100;
	$y /= 100;
	$k /= 100;
	$r  = 1 - min(1, $c * (1 - $k) + $k);
	$g  = 1 - min(1, $m * (1 - $k) + $k);
	$b  = 1 - min(1, $y * (1 - $k) + $k);
	return array(
		'r' => round($r * 255),
		'g' => round($g * 255),
		'b' => round($b * 255)
	);
}

/**
 * Convert RGB format to HSB format
 * @param array $rgb [r,g,b]
 * @param int $accuracy
 * @return array
 */
function color_rgb2hsb(array $rgb, $accuracy = 3) {
	[$r, $g, $b] = $rgb;
	$r /= 255;
	$g /= 255;
	$b /= 255;

	$max = max($r, $g, $b);
	$min = min($r, $g, $b);
	$v = $max;
	$d = $max - $min;
	$s = $max == 0 ? 0 : $d/$max;
	$h = 0; // achromatic
	if($max != $min){
		_rgb_hsl_hue($h, $r, $g, $b, $max, $d);
	}
	$h = round($h*360, $accuracy);
	$s = round($s*100, $accuracy);
	$v = round($v*100, $accuracy);
	return array($h, $s, $v);
}

/**
 * Convert HSB format to RGB format
 * @param array $hsb [h,s,b]
 * @param int $accuracy
 * @return int[] [r,g,b]
 */
function color_hsb2rgb(array $hsb, $accuracy = 3){
	[$h, $s, $v] = $hsb;
	if($v == 0){
		return [0,0,0];
	}
	$s /= 100;
	$v /= 100;
	$h /= 60;
	$i = floor($h);
	$f = $h - $i;
	$p = $v*(1 - $s);
	$q = $v*(1 - ($s*$f));
	$t = $v*(1 - ($s*(1 - $f)));
	$calc = [
		[$v, $t, $p],
		[$q, $v, $p],
		[$p, $v, $t],
		[$p, $q, $v],
		[$t, $p, $v],
		[$v, $p, $q],
	];
	return [
		round($calc[$i][0]*255, $accuracy),
		round($calc[$i][1]*255, $accuracy),
		round($calc[$i][2]*255, $accuracy),
	];
}

/**
 * Calculate the molarity of a color
 * @param string|array $color_val HEX color string, or RGB array
 * @param float $inc_pec range of percent, from -99 to 99
 * @return array|string
 */
function color_molarity($color_val, $inc_pec){
	$is_hex = is_string($color_val);
	if($is_hex){
		$rgb = color_hex2rgb($color_val);
	}else{
		$rgb = $color_val;
	}

	$limitation = function($v){
		if($v > 255){
			return 255;
		}
		if($v < 0){
			return 0;
		}
		return intval($v);
	};

	$rgb[0] = $limitation($rgb[0]*(1 + $inc_pec));
	$rgb[1] = $limitation($rgb[1]*(1 + $inc_pec));
	$rgb[2] = $limitation($rgb[2]*(1 + $inc_pec));
	return $is_hex ? color_rgb2hex($rgb) : $rgb;
}

/**
 * Random color
 * @return string
 */
function color_rand(){
	return substr('00000'.dechex(mt_rand(0, 0xffffff)), -6);
}


function _hsl_rgb_low(&$r, &$g, &$b, $c, $x, $h){
	if($h < 60){
		$r = $c;
		$g = $x;
		$b = 0;
	}elseif($h < 120){
		$r = $x;
		$g = $c;
		$b = 0;
	}else{
		$r = 0;
		$g = $c;
		$b = $x;
	}
}

function _hsl_rgb_high(&$r, &$g, &$b, $c, $x, $h){
	if($h < 240){
		$r = 0;
		$g = $x;
		$b = $c;
	}else{
		$r = $x;
		$g = 0;
		$b = $c;
	}
}

function _rgb_hsl_delta_rgb($rgb, $max, $delta) {
	return ((($max - $rgb) / 6) + ($delta / 2)) / $delta;
}

function _rgb_hsl_hue(&$h, $r, $g, $b, $max, $delta) {
	$delta_r = _rgb_hsl_delta_rgb($r, $max, $delta);
	$delta_g = _rgb_hsl_delta_rgb($g, $max, $delta);
	$delta_b = _rgb_hsl_delta_rgb($b, $max, $delta);

	$h = (2 / 3) + $delta_g - $delta_r;
	if ($r == $max) {
		$h = $delta_b - $delta_g;
	} elseif ($g == $max) {
		$h = (1 / 3) + $delta_r - $delta_b;
	}
	if ($h < 0) {
		$h++;
	}
}
