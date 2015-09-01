<div class="jcr-box square-box new-status-box">
	<textarea class="new-status" placeholder="Write a new status..." value=""></textarea>
	<div class="submit-line">
		<div class="smilies-selection">
			<?php 
				require(APPPATH.'config/smileys.php');
				foreach($smileys as $i=>$s)	{
					$sm[$s[0]] = array(
						'width'=>$s[1],
						'height'=>$s[2],
						'alt'=>$s[3],
						'text'=>$i
					);
				}
				foreach($sm as $i => $s){
					if(file_exists(VIEW_PATH.'common/smileys/'.$i)){
			?>
						<img src="<?php echo VIEW_URL.'common/smileys/'.$i; ?>" width="<?php echo $s['width']; ?>" height="<?php echo $s['height']; ?>" alt="<?php echo $s['alt']; ?>" style="border:0;" value="<?php echo $s['text']; ?>"> 
			<?php 	}
				} ?>
		</div>
		<input type="submit" value="Post" class="post-button">
	</div>
</div>