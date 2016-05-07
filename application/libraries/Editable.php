<?php
class Editable {
    var $image_path;
    var $file_path;
    var $built_in_methods;
    var $page;
    var $ci;

    function __construct($param) {
        $this->page = $param['page'];
        $this->ci =& get_instance();
        $this->ci->load->config('editable');
        $pm = $this->ci->config->item('permitted_methods');
        $this->built_in_methods = $pm['editable'];
        $this->load_methods();
    }

    function set_file_path($path) {
        $this->file_path = $path;
    }

    function load_methods() {
        foreach($this->built_in_methods as &$meth) {
            foreach($meth['params'] as $k => &$param) {
                if($param['type'] == 'select' && strpos($param['options'], 'func:') !== FALSE) {
                    $param['options'] = call_user_func(array($this, substr($param['options'], 5)), $k);
                }
            }
        }
    }

    function get_permitted_methods() {
        return $this->built_in_methods;
    }

    function set_image_path($path) {
        $this->image_path = $path;
        if(!file_exists($path)) mkdir($path);
    }

    function set_image_save_url($url) {
        $this->image_save_url = $url;
    }

    function set_image_url($url) {
        $this->image_url = $url;
    }

    function set_doc_path($path) {
        $this->doc_path = $path;
        if(!file_exists($path)) mkdir($path);
    }

    function set_doc_save_url($url) {
        $this->doc_save_url = $url;
    }

    function set_doc_url($url) {
        $this->doc_url = $url;
    }

    function save_page() {
        $self = $this;
        log_message('error', 'Before preg_replace:'.$_POST['content']);
        $content = preg_replace_callback(
            // matches {function_name:param1,param2}
            '/\{([a-z0-9_]+):?([^\}]+)\}/i',
            function($matches) use ($self) {
                $func = $matches[1];
                if(isset($self->built_in_methods[$func]) && method_exists($self, $func) && !empty($matches[2])) {
                    // has some params
                    preg_match_all('/\'([^\']*)\'|"([^"]*)"/i', $matches[2], $params);
                    $params = $params[1];
                    $num_params = count($params);
                    $method_details = $self->built_in_methods[$func];
                    // check the number of parameters
                    if($num_params < $method_details['min_params'] && $num_params > $method_details['max_params']) { echo 'f'; return;}
                    // replace quotes in function parameters.  Checks for ' or ", any characters of any length in the middle, and
                    foreach($params as &$param) {
                        foreach(array('\'', '"') as $char) {
                            if(strpos($param, $char) !== FALSE) {
                                $param = preg_replace('/[\s'.$char.']*([^'.$char.']*)['.$char.'\s]*/i', '$1', $param);
                                break;
                            }
                        }
                    }
                    return call_user_func_array(array($self, $func), $params);
                }
            },
            $_POST['content']
        );
        log_message('error', 'Before clean_html:'.$content);
        $content = $this->clean_html($content);
        log_message('error', $this->file_path.'After clean_html:'.$content);
        file_put_contents($this->file_path, $content);
        $GLOBALS['controller_json'] = json_encode(array('success' => TRUE, 'replaceWith' => $content));
    }

    function clean_html($html) {
        // replace physical with semantic markup
        $html = preg_replace('/\<(\/?)B\>/i','<$1strong>', $html);
        $html = preg_replace('/\<(\/?)I\>/i','<$1em>', $html);
        // strip disallowed tags
        $allowed_tags='<p><strong><em><u><h1><h2><h3><img><a><li><ol><ul><span><div><br><ins><del>';
        $html = strip_tags($html, $allowed_tags.'<iframe>');//Allow iframes for now, we'll have a look at them later
        // make all tags lower case
        $html = preg_replace_callback('/(<\/?)([^\s\/>]+)([^\/>]*\/?>)/', function($matches) { return $matches[1].strtolower($matches[2]).$matches[3]; }, $html);
        // close tags
        $html = preg_replace('/\<([bh]{1}?r)[\s]*\>/', '<$1 />', $html);
        // strip newlines and tabs
        $html = str_replace(array("\n", "\r", "\t"), '', $html);
        
        //check iframes are youtube links
        $re = '/<iframe [^\0]*src="http[s]*:\/\/www\.youtube\.com\/embed\//'; 
        preg_match($re, $html, $matches_youtube);
        $re = "/<iframe/"; 
        preg_match($re, $html, $matches_all);
        if(count($matches_youtube) != count($matches_all)){
            $html = strip_tags($html, $allowed_tags);
        }
        
        return $html;
    }

    function load_page_images() {
        $this->ci->load->library('images');
        $images = $this->ci->images->get_images_in_folder($this->image_path, TRUE);
        $GLOBALS['controller_json'] = $this->ci->load->view('common/editable/image_view', array(
            'image_url' => $this->image_url,
            'images' => $images), true);
    }

    function save_image() {
        // determine unique number to assign to image
        $this->ci->load->library('images');
        $images = $this->ci->images->get_images_in_folder($this->image_path, TRUE);
        $img_num = pathinfo(end($images), PATHINFO_FILENAME) + 1;
        // define file uploading parameters
        $config = array(
            'upload_path' => $this->image_path.'/',
            'allowed_types' => 'jpg|jpeg|png',
            'file_name'        => $img_num,
            'overwrite'        => TRUE, // if file of same name already exists, overwrite it
            'max_size'        => '6144', // 6MB
            'max_width'        => '0', // No limit on width
            'max_height'    => '0' // No limit on height
        );
        $this->ci->load->library('upload', $config);
        if($this->ci->upload->do_upload('image')) {
            // get info about uploaded file
            $image = $this->ci->upload->data();

            $limit = 600;
            $thumb_size = 100;

            list($source_image_width, $source_image_height, $source_image_type) = getimagesize($image['full_path']);
            switch ($source_image_type) {
                case IMAGETYPE_JPEG:
                    $source_gd_image = imagecreatefromjpeg($image['full_path']);
                    break;
                case IMAGETYPE_PNG:
                    $source_gd_image = imagecreatefrompng($image['full_path']);
                    break;
            }

            $source_aspect_ratio = $image['image_width'] / $image['image_height'];
            $source_image_width = $image['image_width'];
            $source_image_height = $image['image_height'];
            if ($image['image_width'] <= $thumb_size && $image['image_height'] <= $thumb_size) {
                $thumbnail_image_width = $image['image_width'];
                $thumbnail_image_height = $image['image_height'];
            } elseif (1 > $source_aspect_ratio) {
                $thumbnail_image_width = (int) ($thumb_size * $source_aspect_ratio);
                $thumbnail_image_height = $thumb_size;
                if($image['image_height'] > $limit) {
                    $source_image_width = (int) ($limit * $source_aspect_ratio);
                    $source_image_height = $limit;
                }
            } else {
                $thumbnail_image_width = $thumb_size;
                $thumbnail_image_height = (int) ($thumb_size / $source_aspect_ratio);
                if($image['image_width'] > $limit) {
                    $source_image_width = $limit;
                    $source_image_height = (int) ($limit / $source_aspect_ratio);
                }
            }
            $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
            $large_gd_image = imagecreatetruecolor($source_image_width, $source_image_height);
            imagesavealpha( $thumbnail_gd_image, true );
            imagesavealpha( $large_gd_image, true );
            $trans_colour = imagecolorallocatealpha($thumbnail_gd_image, 255, 255, 255, 127);
            imagefill($thumbnail_gd_image, 0, 0, $trans_colour);
            $trans_colour = imagecolorallocatealpha($large_gd_image, 255, 255, 255, 127);
            imagefill($large_gd_image, 0, 0, $trans_colour);
            imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $image['image_width'], $image['image_height']);
            imagecopyresampled($large_gd_image, $source_gd_image, 0, 0, 0, 0, $source_image_width, $source_image_height, $image['image_width'], $image['image_height']);
            imagejpeg($thumbnail_gd_image, $this->image_path.'/'.sprintf('%03d', $img_num).'_thumb.jpg', 95);
            imagejpeg($large_gd_image, $this->image_path.'/'.sprintf('%03d', $img_num).'.jpg', 95);
            imagedestroy($source_gd_image);
            imagedestroy($thumbnail_gd_image);

            // delete original file
            unlink($image['full_path']);
            $GLOBALS['controller_json'] = '';
        } else {
            $errors = $this->ci->upload->display_errors('<p class="editable-error">', '</p>');
            $GLOBALS['controller_json'] = $errors;
        }
    }

    function delete_image() {
        if(!isset($_POST['image']))  {
            $this->ci->index();
            return;
        }
        foreach(array($this->image_path.'/'.$_POST['image'],$this->image_path.'/'.pathinfo($_POST['image'], PATHINFO_FILENAME).'_thumb.'.pathinfo($_POST['image'], PATHINFO_EXTENSION)) as $p) if(file_exists($p)) unlink($p);
        $GLOBALS['controller_json'] = '';
    }

    function load_page_docs() {
        $this->ci->load->library('docs');
        $GLOBALS['controller_json'] = $this->ci->load->view('common/editable/doc_view', array(
            'doc_url' => $this->doc_url,
            'docs' => $this->ci->docs->get_docs_in_folder($this->doc_path, TRUE)
        ), true);
    }

    function save_doc() {
        $this->ci->load->library('upload', array(
            'upload_path'    => $this->doc_path.'/',
            'allowed_types'    => '*',
            'overwrite'        => FALSE, // if file of same name already exists, overwrite it
            'remove_spaces'    => TRUE,
            'max_size'        => '6144', // 6MB
            'max_width'     => '0', // No limit on width
            'max_height'    => '0' // No limit on height
        ));
        if($this->ci->upload->do_upload('doc')) {
            $GLOBALS['controller_json'] = '';
        } else {
            $errors = $this->ci->upload->display_errors('<p class="editable-error">', '</p>');
            $GLOBALS['controller_json'] = $errors;
        }
    }

    function delete_doc() {
        if(!isset($_POST['doc']))  {
            $this->ci->index();
            return;
        }
        $file = $this->doc_path.'/'.$_POST['doc'];
        if(file_exists($file)) unlink($file);
        $GLOBALS['controller_json'] = '';
    }

    // TEXT EDITING FUNCTIONS
    function get_exec_contact($lev_id = NULL, $display_title = TRUE, $display_email = FALSE) {
        $this->ci->load->model('users_model');
        return $this->ci->users_model->get_exec_contact($lev_id, $display_title, $display_email);
    }

    function get_exec_contact_options($param) {
        $this->ci->db->select('id, full');
        $this->ci->db->order_by('full', 'ASC');
        $levs = $this->ci->db->get('levels')->result_array();
        $string = '';
        foreach($levs as $l) {
            $string .= '<option value="'.$l['id'].'">'.$l['full'].'</option>';
        }
        return $string;
    }
}