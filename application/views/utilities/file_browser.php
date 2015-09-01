<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="uploads">
<?php
	if(!isset($admin)){
		$admin = FALSE;
	}

	if(!isset($path)){
		$path = '/file_store/';
	}
	
	if(!isset($section)){
		$section = 'file_store';
	}

	if(!isset($filetypes)){
		$filetypes = 'gif|jpg|png|pdf';
	}
	
	if($admin){
		if(isset($_POST['file_browse_upload'])){
			$upload = $this->common_model->upload_files($section, $path, $filetypes);
			if($upload === TRUE){
				echo '<p class="validation_success"><span style="display:inline-block" class="ui-icon ui-icon-check green-icon"></span>Upload Success</p>';
			}else{
				echo $upload;
			}
		}
		if(isset($_POST['file_browse_remove'])){
			if($this->common_model->remove_file($_POST['id'], $section)){
				echo '<p class="validation_success"><span style="display:inline-block" class="ui-icon ui-icon-check green-icon"></span>File Removed</p>';
			}
		}
	}
	$files = $this->common_model->get_files($section);
	
	if($admin){
		echo form_open_multipart($this->uri->uri_string().'#uploads', 'class="jcr-form no-jsify"');
		
		echo '<p>'.form_label('Upload New:').form_upload('file_upload').'</p>';
		echo '<p>'.form_label('Name:').form_input('file_name').'</p>';
		echo '<p>'.form_label('Date:').form_dropdown('file_day', array_combine(range(1,31), range(1,31)), date('d')).form_dropdown('file_month', array_combine(range(1,12), range(1,12)), date('m')).form_dropdown('file_year', array_combine(range(2000, date('Y')+1), range(2000, date('Y')+1)), date('Y')).'</p>';
		
		echo form_label().form_submit('file_browse_upload', 'Upload File');
		
		echo form_close();
	}
	
	$last_year = 0;
?>
<?php
	foreach($files as $f){
		$this_year = date('Y', $f['date']) + (date('m', $f['date']) >= 9);
		if($last_year !== $this_year){
			echo ($last_year==0?'':'</table></div>').'<div><h3 class="year-title"><span class="file-arrow'.($last_year==0?' file-rotate':'').'"></span>'.($this_year-1).' - '.$this_year.'</h3><table class="file-table">';
			$last_year = $this_year;
		}
		echo '<tr><td>'.anchor('application/views'.$f['file_name'], $f['name'], 'target="_blank"').'</td><td>'.date('d/m/Y', $f['date']).'</td>'.($admin?'<td>'.form_open($this->uri->uri_string().'#uploads').form_hidden(array('id'=>$f['id'])).form_submit('file_browse_remove', 'Remove').form_close().'</td>':'').'</tr>';
	}
if($last_year != 0){
	echo '</table></div>';
}
?></div>