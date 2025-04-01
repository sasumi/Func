<?php
/**
 * Image Functions
 */
namespace LFPhp\Func;

use Exception;

const IMG_RESIZE_MODE_FILL = 'fill';
const IMG_RESIZE_MODE_COVER = 'cover';
const IMG_RESIZE_MODE_CONTAIN = 'contain';
const IMG_RESIZE_MODE_SCALEDOWN = 'scaledown';

/**
 * convert image file to data url
 * @param string $img_file
 * @return string data url
 * @throws \Exception
 * @example echo "<img src=".img_to_data_url("a.jpg")."/>";
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

/**
 * 转换图片格式
 * @param string $img_file 源图片路径
 * @param string $save_path 目标图片路径
 * @param string $format 目标格式（如 'png', 'jpg' 等）
 */
function img_change_format($img_file, $save_path, $format){
	if(!file_exists($img_file)){
		throw new Exception('image file no exists:'.$img_file);
	}

	$img_info = getimagesize($img_file);
	if(!$img_info){
		throw new Exception('image info read fail');
	}

	// 获取源图片类型
	$source_type = $img_info[2]; // IMAGETYPE 常量

	// 手动映射 IMAGETYPE 常量到扩展名
	$type_ext_map = [
		IMAGETYPE_JPEG => 'jpg',
		IMAGETYPE_PNG  => 'png',
		IMAGETYPE_GIF  => 'gif',
		IMAGETYPE_WEBP => 'webp',
	];

	// 获取源文件扩展名
	$source_ext = isset($type_ext_map[$source_type]) ? $type_ext_map[$source_type] : null;
	if(!$source_ext){
		throw new Exception('format no support:'.$source_type);
	}

	// 检查目标格式是否与源格式相同
	$format = strtolower($format);
	if($source_ext === $format){
		return;
	}

	// 创建源图片资源
	switch($source_type){
		case IMAGETYPE_JPEG:
			$sourceImage = imagecreatefromjpeg($img_file);
			break;
		case IMAGETYPE_PNG:
			$sourceImage = imagecreatefrompng($img_file);
			break;
		case IMAGETYPE_GIF:
			$sourceImage = imagecreatefromgif($img_file);
			break;
		case IMAGETYPE_WEBP:
			$sourceImage = imagecreatefromwebp($img_file);
			break;
	}

	if(!$sourceImage){
		throw new Exception('image re-format fail');
	}

	// 根据目标格式保存图片
	$result = false;
	switch($format){
		case 'jpg':
		case 'jpeg':
			$result = imagejpeg($sourceImage, $save_path, 90); // 质量为 90
			break;
		case 'png':
			$result = imagepng($sourceImage, $save_path, 6); // 压缩级别为 6
			break;
		case 'gif':
			$result = imagegif($sourceImage, $save_path);
			break;
		case 'webp':
			$result = imagewebp($sourceImage, $save_path, 90); // 质量为 90
			break;
		default:
			throw new Exception('format no support:'.$source_type);
	}

	imagedestroy($sourceImage);
	if(!$result){
		throw new Exception('image re-format fail');
	}
}

/**
 * 修正图片方向的函数，仅对jpg有效
 * @param string $img_file 图片路径
 * @param string $new_file 保存为新图片的地址
 * @return bool|resource 修正后的图片资源，失败或非jpg图片返回 false
 */
function img_fix_orientation($img_file, $new_file = ''){
	if(!file_exists($img_file)){
		return false;
	}

	[$orig_width, $orig_height, $imag_type] = getimagesize($img_file);
	if($imag_type !== IMAGETYPE_JPEG){
		return false;
	}

	// 获取图片的 EXIF 数据
	$exif = @exif_read_data($img_file);
	if(!$exif || !isset($exif['Orientation'])){
		// 如果没有 EXIF 数据或没有 Orientation 标签，直接返回原始图片资源
		return imagecreatefromjpeg($img_file);
	}

	// 获取图片的方向
	$orientation = $exif['Orientation'];

	// 创建图片资源
	$img = imagecreatefromjpeg($img_file);
	if(!$img){
		return false;
	}

	// 根据 Orientation 标签修正图片方向
	switch($orientation){
		case 2: // 水平翻转
			imageflip($img, IMG_FLIP_HORIZONTAL);
			break;
		case 3: // 旋转180度
			$img = imagerotate($img, 180, 0);
			break;
		case 4: // 垂直翻转
			imageflip($img, IMG_FLIP_VERTICAL);
			break;
		case 5: // 顺时针90度旋转并垂直翻转
			$img = imagerotate($img, -90, 0);
			imageflip($img, IMG_FLIP_VERTICAL);
			break;
		case 6: // 顺时针90度旋转
			$img = imagerotate($img, -90, 0);
			break;
		case 7: // 逆时针90度旋转并垂直翻转
			$img = imagerotate($img, 90, 0);
			imageflip($img, IMG_FLIP_VERTICAL);
			break;
		case 8: // 逆时针90度旋转
			$img = imagerotate($img, 90, 0);
			break;
	}
	if($new_file){
		imagejpeg($img, $new_file, 90);
	}
	return $img;
}

/**
 * 压缩图片函数
 * @param string $source_path 原始图片路径
 * @param string $dest_path 压缩后图片保存路径
 * @param int $target_width 目标宽度
 * @param int $target_height 目标高度
 * @param string $mode 压缩模式：contain, scaledown, fill, cover
 */
function img_resize($source_path, $dest_path, $target_width, $target_height, $mode = IMG_RESIZE_MODE_SCALEDOWN){
	if(!file_exists($source_path)){
		throw new Exception('image file no exists', $source_path);
	}

	[$orig_width, $orig_height, $imag_type] = getimagesize($source_path);

	// 创建源图片资源
	switch($imag_type){
		case IMAGETYPE_JPEG:
			$src_image = imagecreatefromjpeg($source_path);
			break;
		case IMAGETYPE_PNG:
			$src_image = imagecreatefrompng($source_path);
			break;
		case IMAGETYPE_GIF:
			$src_image = imagecreatefromgif($source_path);
			break;
		default:
			throw new Exception('image type no support:'.$imag_type);
	}

	// 计算目标尺寸
	$dst_width = 0;
	$dst_height = 0;
	$scale = 0;

	switch($mode){
		case IMG_RESIZE_MODE_CONTAIN:
			$scale = min($target_width/$orig_width, $target_height/$orig_height);
			$dst_width = (int)($orig_width*$scale);
			$dst_height = (int)($orig_height*$scale);
			break;

		case IMG_RESIZE_MODE_SCALEDOWN:
			if($orig_width <= $target_width && $orig_height <= $target_height){
				$dst_width = $orig_width;
				$dst_height = $orig_height;
			}else{
				$scale = min($target_width/$orig_width, $target_height/$orig_height);
				$dst_width = (int)($orig_width*$scale);
				$dst_height = (int)($orig_height*$scale);
			}
			break;

		case IMG_RESIZE_MODE_FILL:
			$scale = max($target_width/$orig_width, $target_height/$orig_height);
			$dst_width = (int)($orig_width*$scale);
			$dst_height = (int)($orig_height*$scale);
			break;

		case IMG_RESIZE_MODE_COVER:
			$scale = max($target_width/$orig_width, $target_height/$orig_height);
			$dst_width = $target_width;
			$dst_height = $target_height;
			break;

		default:
			throw new Exception('no support mode:'.$mode);
	}

	//scaledown模式小图直接处理
	if($dst_width === $orig_width && $dst_height === $orig_height && $mode === IMG_RESIZE_MODE_SCALEDOWN){
		if($source_path === $dest_path){
			return;
		}
		copy($source_path, $dest_path);
		return;
	}

	// 创建目标图片资源
	$dst_image = imagecreatetruecolor($target_width, $target_height);

	// 处理透明背景（针对 PNG 和 GIF）
	if($imag_type === IMAGETYPE_PNG || $imag_type === IMAGETYPE_GIF){
		imagealphablending($dst_image, false);
		imagesavealpha($dst_image, true);
		$transparent = imagecolorallocatealpha($dst_image, 0, 0, 0, 127);
		imagefilledrectangle($dst_image, 0, 0, $target_width, $target_height, $transparent);
	}

	// 根据模式调整图片位置
	$srcX = $srcY = 0;
	$dstX = $dstY = 0;

	if($mode === IMG_RESIZE_MODE_FILL || $mode === IMG_RESIZE_MODE_COVER){
		$srcX = ($dst_width > $target_width) ? (int)(($dst_width - $target_width)/2/$scale) : 0;
		$srcY = ($dst_height > $target_height) ? (int)(($dst_height - $target_height)/2/$scale) : 0;
	}else{
		$dstX = (int)(($target_width - $dst_width)/2);
		$dstY = (int)(($target_height - $dst_height)/2);
	}

	// 复制并调整图片大小
	imagecopyresampled($dst_image, $src_image, $dstX, $dstY, $srcX, $srcY, $dst_width, $dst_height, $orig_width, $orig_height);

	// 保存图片
	$result = false;
	switch($imag_type){
		case IMAGETYPE_JPEG:
			$result = imagejpeg($dst_image, $dest_path, 90);
			break;
		case IMAGETYPE_PNG:
			$result = imagepng($dst_image, $dest_path);
			break;
		case IMAGETYPE_GIF:
			$result = imagegif($dst_image, $dest_path);
			break;
		default:
	}

	imagedestroy($src_image);
	imagedestroy($dst_image);
	if(!$result){
		throw new Exception('image resize failed');
	}
}
