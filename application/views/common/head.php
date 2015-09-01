<?php
	$new_menu = true;//isset($_GET['newmenu']);
	function link_and_name($menu_item){
        if(is_null($menu_item['link'])){
            return '<span>'.$menu_item['display_name'].'</span>';
        }else{
        	return '<span class="with-link">'.$menu_item['display_name'].anchor($menu_item['link'], '', ((strpos($menu_item['link'],'http')===FALSE)?'':' class="no-jsify"')).'</span>';
        }
    }
    
    function mobile_menu($menu){
    	echo '<li>';
    	echo anchor($menu['title']['link'],$menu['title']['display_name']);
    	$has_children = sizeof($menu) > 1;
    	echo $has_children?'<ul>':'';
    	foreach($menu as $key => $val){
    		
    		if(is_numeric($key)){
    			mobile_menu($val);
    		}
    	}
    	echo $has_children?'</ul>':'';
    	echo '</li>';
    }

    
    $this->load->model('common_model');
    $data = $this->common_model->get_menu_structure();
    $menu = array();
    $i = -1;
    
    foreach($data as $d){
        if($d['level'] == 0){
        	$menu[++$i]['title'] = $d;
        	$j = -1;
        	foreach($data as $e){
                if($e['parent_id'] == $d['id']){
                	$menu[$i][++$j]['title'] = $e;
                	$k = -1;
                    foreach($data as $f){
                        if($f['parent_id'] == $e['id']){
                            $menu[$i][$j][++$k]['title'] = $f;
                        }
                    }
				}
			}            
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Butler College JCR - <?php echo $page['title']; ?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="author" content="Samuel Stradling, Rupert Maspero, Courney Edgar" />
		<meta name="Keywords" content="Josephine, Butler, College, JCR, Durham, University" />
		<meta name="Description" content="<?php echo $page['description']; ?>" />
		<link href="<?php echo VIEW_URL; ?>common/img/favicon.ico" rel="shortcut icon" type="image/ico" />
		<link href="<?php echo VIEW_URL; ?>common/img/touch-icon-57x57.png" rel="apple-touch-icon-precomposed">
		<link href="<?php echo VIEW_URL; ?>common/img/touch-icon-57x57.png" rel="apple-touch-icon" sizes="57x57">
		<link href="<?php echo VIEW_URL; ?>common/img/touch-icon-72x72.png" rel="apple-touch-icon" sizes="72x72">
		<link href="<?php echo VIEW_URL; ?>common/img/touch-icon-144x144.png" rel="apple-touch-icon" sizes="144x144">
		<link href="<?php echo VIEW_URL; ?>common/img/touch-icon-114x114.png" rel="apple-touch-icon" sizes="114x114">
		<meta name="apple-mobile-web-app-status-bar-style" content="red" />
		<meta name="application-name" content="Josephine Butler College JCR" />
		<meta name="msapplication-starturl" content="<?php echo site_url('home'); ?>" />
		<meta name="msapplication-navbutton-color" content="#c80000" />
		<meta name="msapplication-window" content="width=1366;height=768" />
		<meta name="msapplication-tooltip" content="Josephine Butler College JCR Website" />
		<meta name="msapplication-task" content="name=JCR Events; action-uri=<?php echo site_url('events'); ?>; icon-uri=<?php echo VIEW_URL; ?>common/img/favicon.ico" />
		<meta name="msapplication-task" content="name=Get involved in the JCR; action-uri=<?php echo site_url('involved'); ?>; icon-uri=<?php echo VIEW_URL; ?>common/img/favicon.ico" />
		<meta name="msapplication-task" content="name=Contact the JCR; action-uri=<?php echo site_url('contact'); ?>; icon-uri=<?php echo VIEW_URL; ?>common/img/favicon.ico" />

		<?php foreach($css_links as $link) echo '<link rel="stylesheet" href="'.$link.'" />'; ?>

		<?php foreach($js_links as $link) echo '<script src="'.$link.'"></script>'; ?>

		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-750282-2', 'auto');
		  ga('send', 'pageview');
		
		</script>	
	</head>
	<body>
		<div id="navmob" role="navigation" style="display: none;">
			<ul><?php
				foreach($menu as $m){
					mobile_menu($m);
				}
			?>
				<li>Links
					<ul>
						<li><a href="http://www.facebook.com/groups/274250999315976/" target="_blank">Butler JCR on Facebook</a></li>
						<li><a href="http://twitter.com/butlerjcr" target="_blank">Butler JCR on Twitter</a></li>
						<li><a href="http://www.dur.ac.uk/butler.college/" target="_blank">Butler College Website</a></li>
						<li><a href="http://issuu.com/moundmagazine" target="_blank">Mound Magazine</a></li>
						<li><a href="http://butlerscholarlyjournal.com/" target="_blank">Butler Scholarly Journal</a></li>
					</ul>
				</li>
				<li><?php echo (logged_in() ? 'User Options' : 'Login'); ?>
					<ul>
						<?php if(logged_in()) { ?>
							<li><?php echo anchor('details/profile', user_pref_name($this->session->userdata('firstname'), $this->session->userdata('prefname'))."'s Profile"); ?></li>
							<?php if(is_admin()) echo '<li>'.anchor('admin', 'Admin').'</li>'; ?>
							<?php if(!$this->session->userdata('current')) echo '<li>'.anchor('alumni', 'Alumni').'</li>'; ?>
							<li><?php echo anchor('home/logout', 'Logout'); ?></li>
						<?php } else { ?>
							<form id="login-mob" class="no-jsify"  action="<?php echo str_replace('http://', (ENVIRONMENT == 'local' ? 'http://' : 'https://'), get_last_location()); ?>" method="post">
								<div id="login-top" class="login-block">
									<input type="text" id="username" name="username" maxlength="8" tabindex="1" placeholder="Username" required class="input-help" title="Use your Durham University CIS username to login to Josephine Butler JCR" />
								</div>
								<div id="login-bottom" class="login-block">
									<input type="password" id="password" name="password" tabindex="2" placeholder="Password" required class="input-help" title="Use your Durham University CIS password to login to Josephine Butler JCR" />
									<input type="submit" id="login-button" value="&gt;" />
								</div>
							</form>
						<?php }?>
					</ul>
				</li>
				<li><?php echo anchor('contact', 'Contact Us'); ?></li>
			</ul>
		</div>
		<div id="wrapper">
			<div id="header" <?php echo $new_menu?'class="smaller-header"':''; ?>>
				<div id="header-width">
					<div id="header-top">
						<?php echo anchor('home', '<div id="header-img'.($new_menu?'-small':'').'"></div><div id="small-logo" class="left-logo"></div><div id="header-title"></div>'); ?>
						<div id="login-details">
							<?php if(logged_in()) {
								$has_image = $show_prompt?'<span class="no-profile-image"></span>':'';
								echo anchor('details/profile', '<div id="header-profile" style="background-image: url('.get_usr_img_src($this->session->userdata('uid'), 'small').')" >'.$has_image.'</div>'); ?>
								<div id="header-list">
									<p><?php 
										echo anchor('details/profile', user_pref_name($this->session->userdata('firstname'),$this->session->userdata('prefname')).' '.$this->session->userdata('surname')).'</p>';
										echo '<p>'.(is_admin() ? (anchor('admin', 'Website Admin')) : ($this->session->userdata('current') ? (anchor('details', 'Edit Profile')) : (anchor('alumni', 'Alumni')))).'</p>';
										echo '<p>'.anchor('home/logout', 'Logout').'</p>'; 
									?></p>
								</div>
							<?php } else { ?>
								<form id="login-form" class="no-jsify" action="<?php echo str_replace('http://', (ENVIRONMENT == 'local' ? 'http://' : 'https://'), get_last_location()); ?>" method="post">
									<div id="login-top" class="login-block">
										<div id="login-user"></div>
										<input type="text" id="username" name="username" maxlength="8" tabindex="1" placeholder="Username" required class="input-help" title="Use your Durham University CIS username to login to Josephine Butler JCR" />
									</div>
									<div id="login-bottom" class="login-block">
										<div id="login-pass"></div>
										<input type="password" id="password" name="password" tabindex="2" placeholder="Password" required class="input-help" title="Use your Durham University CIS password to login to Josephine Butler JCR" />
										<input type="submit" id="login-button" value="&gt;" />
									</div>
								</form>
							<?php } ?>
						</div>
						<a href="<?php echo site_url('home'); ?>"><div id="small-logo" class="right-logo"></div></a>
					</div>
					<div id="nav" role="navigation" class="<?php echo $class;?>">
						<?php 
							if($new_menu){
                                
                        ?>
                            <div class="headings">
                        <?php
                                $i = 0;
                                foreach($menu as $m){
                        ?>
                                    <div class="heading">
                                        <h2 <?php echo is_null($m['title']['link'])?'':' class="link-head"'; ?>><?php echo link_and_name($m['title']); ?></h2>
                                        <table>
                                            <?php
                                                foreach($m as $k => $e){
                                                    if(is_numeric($k)){
                                                        $has_child = sizeof($e) > 1;
                                            ?>
                                                        <tr <?php echo is_null($e['title']['link'])?'':'class="link-row"'; ?>>
                                                            <td class="level-1">
                                                                <?php echo link_and_name($e['title']); ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                    if($has_child == 1){ 
                                                                ?>
                                                                        <span class="ui-icon ui-icon-triangle-1-e yellow-icon" style="float:right;color:#eeb300;"></span>
                                                                        <span>
                                                                            <table>
                                                                                <?php
                                                                                    foreach($e as $key => $n){
                                                                                    	if(is_numeric($key)){
                                                                                ?>
                                                                                        	<tr class="sub-menu-link-<?php echo $i++; ?>" <?php echo is_null($n['title']['link'])?'':' class="link-row"'; ?>><td><?php echo link_and_name($n['title']); ?></td></tr>
                                                                                <?php
                                                                                		}
                                                                                    }
                                                                                ?>
                                                                            </table>
                                                                        </span>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </td>
                                                        </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                            </table>
                                        </div>
                        <?php
                                        
                                    
                                }
                        ?>
                        </div>
                        <div id="sub-menu" class="hidden-menu"></div>
                        <?php
                                
                            }else{
								function tab($short, $pages) {
								if(is_array($short)) {
									$tab = '';
									foreach($short as $s) {
										$tab .= tab($s, $pages);
									}
									return $tab;
								}
								else {
									return '<li id="n-'.$short.'">'.anchor($short, $pages[$short]['title']).'</li>';
								}
							} ?>
							<ul class="nolist">
								<?php echo tab(array('home', 'jcr', 'prospective', 'alumni', 'voting', 'green', 'photos', 'bar', 'services', 'contact'), $pages); ?>
							</ul>
							<ul class="nolist">
								<?php echo tab(array('signup', 'events', 'projects', 'involved', 'whoswho', 'welfare', 'liversout', 'markets'), $pages);
								echo '<li id="n-family">'.anchor('family', 'Family Tree', 'class="no-jsify"').'</li>';
								echo tab('archive', $pages);							
                            }
						?>
						</ul>
					</div>
				</div>
			</div>
			<?php if($show_survey){ echo '<span class="show-survey"></span>'; } ?>
			<div id="spinner">
				<img alt="Josephine Butler College JCR Website Loading" src="<?php echo VIEW_URL; ?>common/img/spinner.gif" />
			</div>
			<div id="content-area">
				<div id="content">
					
					