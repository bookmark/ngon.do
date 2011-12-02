<? if (!defined('BASEPATH'))
	exit('No direct script access allowed.');
/*****
 * Thumbnailer is a very flexible helper that generates thumbnails easily but only when
 * a thumnail with the same dimensions don't yet exist, making it light on server load.
 * @author     Stephen Belanger
 * @email      admin@withstyledesign.com
 * @filename   thumbnailer_healper.php
 * @title      Thumbnailer
 * @url        http://www.withstyledesign.com/
 * @version    1.0
 *****/
function thumbnailer($image_path, $file_name, $scalevalue = "100", $scalemode = 'auto') {
	$ext_to_func = array(
		'jpg',
		'jpeg',
		'gif',
		'png',
		'bmp'
	);

	$ext = pathinfo($file_name, PATHINFO_EXTENSION);

	if (false == in_array($ext, $ext_to_func)) {
		return;
	}

	// Get current dimensions
	list($width, $height) = getimagesize($image_path.$file_name);

	// Set scaled dimensions
	switch ($scalemode) {
	case 'auto':
		if ($width > $height) {
			$newwidth = $scalevalue;
			$newheight = ($scalevalue / $width) * $height;
		} else {
			$newwidth = ($scalevalue / $height) * $width;
			$newheight = $scalevalue;
		}
		break;
	case 'x':
		$newwidth = $scalevalue;
		$newheight = ($scalevalue / $width) * $height;
		break;
	case 'y':
		$newwidth = ($scalevalue / $height) * $width;
		$newheight = $scalevalue;
		break;
	}

	// Load
	if ($ext == 'jpg' || $ext == 'jpeg') {
		$source = imagecreatefromjpeg($image_path.$file_name);
	} elseif ($ext == 'png') {
		$source = imagecreatefrompng($image_path.$file_name);
	} elseif ($ext == 'gif') {
		$source = imagecreatefromgif($image_path.$file_name);
	} elseif ($ext == 'bmp') {
		$source = imagecreatefromxbm($image_path.$file_name);
	}

	$thumb = imagecreatetruecolor($newwidth, $newheight);

	// Resize
	imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	// Output Handling
	$thumbpath = $image_path.'/thumbnails/'.$file_name;
	if (!file_exists($thumbpath)) {
		if ($ext == 'jpg' || $ext == 'jpeg') {
			imagejpeg($thumb, $thumbpath);
		} elseif ($ext == 'png') {
			imagepng($thumb, $thumbpath);
		} elseif ($ext == 'gif') {
			imagegif($thumb, $thumbpath);
		} elseif ($ext == 'bmp') {
			imagexbm($thumb, $thumbpath);
		}
	}

	return "/".$thumbpath;
}
