<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Images {

	function __construct() {

	}

	function get_images_in_folder($folder, $thumbs = FALSE) {
		$image_types = array('jpg', 'jpeg', 'png');
		$array =
			array_filter(
				scandir($folder),
				function($file) use ($image_types, $thumbs) {
					return (
						in_array(
							strtolower(
								pathinfo(
									$file,
									PATHINFO_EXTENSION
								)
							),
							$image_types
						)
						&&
						(strpos($file, '_thumb') === FALSE) === !$thumbs
					);
				}
			);
		sort($array);
		return $array;
	}
}