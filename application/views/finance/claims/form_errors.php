<?php
	$errors = validation_errors();
	if(!empty($errors) or (isset($file_errors) and !empty($file_errors) and ($file_errors !== FALSE))){
?>		<div class="validation_errors">
<?php
			
			$error = explode('</p>', str_replace('<p>', '', $errors), -1);
			foreach($error as $e)
				echo '<p style="display:block;"><span class="ui-icon ui-icon-notice inline-block"></span>'.$e.'</p>';
			if(isset($file_errors) and !empty($file_errors) and ($file_errors !== FALSE)){
				echo '<p style="display:block;"><span class="ui-icon ui-icon-notice inline-block"></span>'.str_replace('<p>','',$file_errors);
			}
?>
		</div>
<?php
	}
?>