<?php

try {
	if (!isset($image)) {
		throw new Exception('Please select a file.');
	}
	
	if (!isset($save_path)) {
		throw new Exception('Please specify save path in php!');
	}
	
	// ensure the file was successfully uploaded
	$ul = assert_valid_upload($image['error']);
	if($ul) throw new Exception($ul);
	
	if (!is_uploaded_file($image['tmp_name'])) {
		throw new Exception('File is not an uploaded file');
	}
	
	$info = getImageSize($image['tmp_name']);
	
	if (!$info) {
		throw new Exception('File is not an image');
	}
	
	if(!in_array($info[2], array(IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
		throw new Exception('Image is not an allowed format');
	}
	
	if($info[0] < 200 OR $info[1] < 200) {
		throw new Exception('Image is too small.  Please choose another.');
	}
}
catch (Exception $ex) {
	$errors[] = $ex->getMessage();
}

if(empty($errors)) {
	$tempFile = $image['tmp_name'];
	if(!file_exists($save_path)) mkdir($folder, 0755, true);
	$img = new Imagick($tempFile);
	$img_small = $img->clone();
	$size = $img->getImageGeometry();
	$limit = 600;
	$thumb_size = 100;
	if($size['width'] > $size['height']) {
		if($size['width'] > $limit) $img->resizeImage($limit, 0, Imagick::FILTER_LANCZOS, 1);
		$img_small->thumbnailImage($thumb_size, 0);
	}
	else {
		if($size['height'] > $limit) $img->resizeImage(0, $limit, Imagick::FILTER_LANCZOS, 1);
		$img_small->thumbnailImage(0, $thumb_size);
	}
	if(strtolower($img->getImageFormat()) != 'jpeg') {
		$img->setCompressionQuality(80);
		$img->setImageFormat('jpeg');
		$img_small->setCompressionQuality(80);
		$img_small->setImageFormat('jpeg');
	}
	$img->writeImage($save_path.'/'.sprintf('%03d', $img_num).'.jpg');
	$img_small->writeImage($save_path.'/'.sprintf('%03d', $img_num).'_thumb.jpg');
}

function assert_valid_upload($code) {
	if ($code == UPLOAD_ERR_OK) {
		return;
	}

	switch ($code) {
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			$msg = 'Image is too large';
			break;
		
		case UPLOAD_ERR_PARTIAL:
			$msg = 'Image was only partially uploaded';
			break;
		
		case UPLOAD_ERR_NO_FILE:
			$msg = 'No image was uploaded';
			break;
		
		case UPLOAD_ERR_NO_TMP_DIR:
			$msg = 'Upload folder not found';
			break;
		
		case UPLOAD_ERR_CANT_WRITE:
			$msg = 'Unable to write uploaded file';
			break;
		
		case UPLOAD_ERR_EXTENSION:
			$msg = 'Upload failed due to extension';
			break;
		
		default:
			$msg = 'Unknown error';
	}
	
	return $msg;
}
