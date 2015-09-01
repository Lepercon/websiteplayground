<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
echo '<h3>'.anchor('projects/view_request/'.$request['id'], $request['title'].' - '.$request['progress']).'</h3>';
echo '<p>'.$category_names[$request['category']].' request by '.$this->users_model->get_full_name($request['request_by']).' on '.date('l jS F Y \a\t G:i',$request['request_time']).'.</p>';
echo '<p>'.$request['description'].'</p>';
?>
