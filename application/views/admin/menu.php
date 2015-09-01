<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	echo back_link('admin');
	$this->load->helper('html');
	
	echo form_open('admin/menu?newmenu=1', array('class'=>'jcr-form no-jsify', 'id'=>'header-menu-form'));
	
	function menu_input($name, $input){
		return 'Display: '.form_input('menu-'.$name, $input['display']).nbs(5).'Link: '.form_input('link-'.$name, $input['link']);
	}
	
?>
<h3>Menu Structure</h3>
<div id="accordion" role="tablist">
	<h3>Instructions</h3>
	<div>
        <ol>
            <li>Use each of the tabs below to change the above menus.</li>
            <li>For internal links only include everything after "<?php echo anchor(site_url(), site_url()); ?>".</li>
            <li>For external links include the whole link.</li>
            <li>Links can be left blank.</li>
            <li>Use the +/- buttons to add/remove menus and sub-menus.</li>
            <li>There must be 7 menus.</li>
            <li>Once done, click the Submit button (final tab).</li>
        </ol>
	</div>
<?php
	$i = -1;
	foreach($menu as $m){
?>
		<h3><?php echo $m['title']['display']; ?></h3>
		<div>
<?php
			echo menu_input(++$i, $m['title']).nbs(1).'<a class="jcr-button inline-block add-child-1" id="'.$i.'"><span class="ui-icon ui-icon-plus"></span></a><a class="jcr-button inline-block remove-child-1"><span class="ui-icon ui-icon-minus"></span></a><br>';
			$ii = -1;
			foreach($m as $k => $m2){
?>
				<span>
<?php
					if(is_numeric($k)){
						echo nbs(5).menu_input($i.'-'.++$ii, $m2['title']).nbs(1).'<a class="jcr-button inline-block add-child-2" id="'.$i.'-'.$ii.'"><span class="ui-icon ui-icon-plus"></span></a><a class="jcr-button inline-block remove-child-2"><span class="ui-icon ui-icon-minus"></span></a><br>';
						$iii = -1;
						foreach($m2 as $k2 => $m3){
?>
							<span>
<?php
								if(is_numeric($k2)){
									echo nbs(10).menu_input($i.'-'.$ii.'-'.++$iii, $m3).'<br>';
								}
?>
							</span>
<?php
						}
					}
?>
				</span>
<?php
			}
?>
		</div>
<?php
	}
?>
	<h3>Submit</h3>
	<div>
		<p>Once you are happy with all of the menus, click the submit button below.</p>
		<a href="#" class="jcr-button" id="submit-menu-form">Submit</a>
		<a href="#" class="jcr-button" id="cancel-menu-form">Cancel</a>
		<p>Note: These menus will become live immediately.</p>
	</div>
</div>
<?php
	echo form_close();
?>
<span style="display:none;">
	<span id="level-1-entry">
		<?php
			echo nbs(5).'Display: '.form_input(array('name'=>'menu-AAA-BBB', 'class'=>'name')).nbs(5).'Link: '.form_input(array('name'=>'link-AAA-BBB', 'class'=>'link')).nbs(1).'<a class="jcr-button inline-block add-child-2"><span class="ui-icon ui-icon-plus"></span></a><a class="jcr-button inline-block remove-child-2"><span class="ui-icon ui-icon-minus"></span></a><br>';
		?>
	</span>
	<span id="level-2-entry">
		<?php
			echo nbs(10).'Display: '.form_input(array('name'=>'menu-AAA-BBB-CCC', 'class'=>'name')).nbs(5).'Link: '.form_input(array('name'=>'link-AAA-BBB-CCC', 'class'=>'link')).'<br>';
		?>
	</span>
</span>