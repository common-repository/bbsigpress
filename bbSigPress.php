<?php

/* 
Plugin Name: bbSigPress
Plugin URI: http://blog.matrixagents.org/wp-plugins/
Version: v0.4.8
Author: Manuel Grabowski
Author URI: http://blog.matrixagents.org/
Description: Automatically updates your <a href="http://en.wikipedia.org/wiki/Internet_forum">bb</a>-signature(s) when publishing a new post.
*/


if (!class_exists("BBsigPlugin"))
{
	class BBsigPlugin
	{
		var $adminOptionsName = 'BBsigOptions';
		
		function BBsigPlugin()
		{ 
			
		}
		
		function bbSigSettingsLink($links)
		{
			$settings_link = '<a href="../wp-admin/plugins.php?page=bbsigpress/bbSigPress.php">' . __('Settings') . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}
		
		function bbsig_activation()
		{
			$this->getAdminOptions();
		}
		
		function bbsig_deactivation()
		{
			delete_option($this->adminOptionsName);
		}
		
		function getAdminOptions()
		{
			$bbsigAdminOptions = array(
			
				'bbsigBB_cmn_count' => '0',
				'bbsigBB_cmn_doDebug' => 'bbsigBB_cmn_doDebug',
				'bbsigBB_cmn_onlyLatest' => 'bbsigBB_cmn_onlyLatest',
				'bbsigBB_cmn_preCat' => '',
				'bbsigBB_cmn_postCat' => ', ',
				'bbsigBB_cmn_lastTitle' => '',
				'bbsigBB1_url' => '', 
				'bbsigBB1_usr' => '', 
				'bbsigBB1_pwd' => '',
				'bbsigBB1_tpl' => ''
				
				);
			
			
			$settings = get_option($this->adminOptionsName);
			
			if(!empty($settings))
			{
				foreach ($settings as $key => $option)
				{
					$bbsigAdminOptions[$key] = $option;
				}
				
				update_option($this->adminOptionsName, $bbsigAdminOptions);
			}			
			return $bbsigAdminOptions;
		}
		
		
		
		
		
		function getAuthorNameByID($autID)
		{
			// i hate the freaking loop - it's sick bullshit and makes everything a absolute pain in the ass
			global $wpdb;
			return $wpdb->get_var('SELECT display_name FROM ' . $wpdb->users . ' WHERE ID=' . $autID);
		}
		
		
		
		
		function updateSigs($post_ID)
		{
			
			$settings = $this->getAdminOptions();
			
			// get the placeholders' values
			$post = get_post($post_ID);
			
			$actvalTitle	= $post->post_title;
			
			
			$onlyLastCheckBool = TRUE;
			
			// if needed get the latest post ID
			if($settings['bbsigBB_cmn_onlyLatest'] == 'bbsigBB_cmn_onlyLatest')
			{
				$lastpost = get_posts('numberposts=1');
				foreach ($lastpost as $postComp)
				{
					$lastpostID = $postComp->ID;
					$lastpostTS = $postComp->post_date;
				}
				
				// set the correct TRUE/FALSE value for re-using the IF below
				if($lastpostTS > $post->post_date)
				{
					$onlyLastCheckBool = FALSE;
				}
			}
			
			// compare the last title to prevent double updating when correcting typos or such like
			if(($post->post_title != $settings['bbsigBB_cmn_lastTitle']) && ($onlyLastCheckBool === TRUE))
			{
				$actvalPerma	= get_permalink($post_ID);
				$actvalCC		= $post->comment_count;
				$actvalAuthor	= $this->getAuthorNameByID($post->post_author);
				$actvalTitle	= $post->post_title;
					$settings['bbsigBB_cmn_lastTitle'] = $actvalTitle;
					update_option($this->adminOptionsName, $settings);
				$timestamp		= strtotime($post->post_date);
				$actvaldtDate	= date(get_option('date_format'), $timestamp);
				$actvaldtTime	= date(get_option('time_format'), $timestamp);
				foreach(get_the_category($post_ID) as $category)
				{
					$actvalCats .= $settings['bbsigBB_cmn_preCat'] . $category->cat_name . $settings['bbsigBB_cmn_postCat'];
				}
				$actvalCats		= substr($actvalCats, strlen($settings['bbsigBB_cmn_preCat']), strlen($actvalCats) - strlen($settings['bbsigBB_cmn_preCat']));
				$actvalCats		= substr($actvalCats, 0, strlen($actvalCats) - strlen($settings['bbsigBB_cmn_postCat']));
				
				
				// for every saved forum do:
				for($i = 1; $i <= $settings['bbsigBB_cmn_count']; $i++)
				{
					$bbaddress	=	$settings['bbsigBB' . $i . '_url'];
					$username	=	apply_filters('format_to_edit', stripslashes($settings['bbsigBB' . $i . '_usr']));
					$password	=	apply_filters('format_to_edit', stripslashes($settings['bbsigBB' . $i . '_pwd']));
					$kindofbb	=	$settings['bbsigBB' . $i . '_typ'];
					$template	=	apply_filters('format_to_edit', stripslashes($settings['bbsigBB' . $i . '_tpl']));
					
					if(($bbaddress != '') && ($username != '') && ($password != '') && ($template != ''))
					{
						// replace the placeholders in the template				
						
						$template 	=	str_replace('%title%', $actvalTitle, $template);
						$template 	=	str_replace('%permalink%', $actvalPerma, $template);
						$template 	=	str_replace('%commentcount%', $actvalCC, $template);
						$template 	=	str_replace('%author%', $actvalAuthor, $template);
						$template	=	str_replace('%date%', $actvaldtDate, $template);
						$template	=	str_replace('%time%', $actvaldtTime, $template);
						$template	= str_replace('%category%', $actvalCats, $template);
										
						// $template = str_replace('%firstwords:xy%', '', $template); // needs regex, maybe substr
						
						
						$functionCall = $kindofbb . '(\'' . $bbaddress . '\', \'' . $username . '\', \'' . $password . '\', \'' . $template . '\');';
						
						if($settings['bbsigBB_cmn_doDebug'] == 'bbsigBB_cmn_doDebug')
						{
							@file_put_contents(WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/debug.txt', date('Y-m-d H:i') . ' - bbSigPress will use ' . $kindofbb . '.eng.php' . ' to update ' . $username . '\'s account on ' . $bbaddress . ' with this signature, based on post ' . $post_ID . ':' . chr(13) . chr(10) . 'Therefore the following function call will be executed: ' . $functionCall . chr(13) . chr(10) . chr(13) . chr(10), FILE_APPEND);
						}
						else
						{
							// import the needed function to actually set the signature
							include_once(WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/eng/' . $kindofbb . '.php');
							
							$kindofbb($bbaddress, $username, $password, $template);
							unset($password);
							
						}
					}
				}
			}
		}
		
		
		
		function bbsig_getBBlist()
		{
			$path = WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/eng/';
			
			if($dir = opendir($path))
			{
				while($file=readdir($dir))
				{
					if (!is_dir($file) && $file != '.' && $file != '..')
					{
						$curElement = file($path . $file);
						foreach($curElement as $num=>$line)
						{
							if(strpos($line, '//BBVAR:$') === 0)
							{
								unset($res);
								preg_match('%\$([A-z]*)\s*=\s*\'(.*)\';%', $curElement[$num + 1], $res);
								
								$bbSigEngines[$file][$res[1]] = $res[2];
							}
						}
					}
				}
				closedir($dir);
			}
			
			return $bbSigEngines;
		}
		
		function bbsig_getSELECT($marked = '', $id)
		{
			$bbsigEngArr = $this->bbsig_getBBlist();
			
			$output = '<select size="1" name="bbsigBB' . $id . '_typ">';
			
			foreach($bbsigEngArr as $bbsigEng)
			{
				$output .= '<option value="' . $bbsigEng['bbUnique'] . '"';
				if($marked == $bbsigEng['bbUnique'])
				{
					$output .= ' selected="selected"';
				}
				$output .= '>' . $bbsigEng['bbName'] . '</option>';
			}
			$output .= '</select>';
			
			return $output;
		}
		
		
		
		function bbSigprintAdminPage()
		{
			$this->bbsig_getBBlist();
			$imghelp = WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) . '/help.png';
			
			$settings = $this->getAdminOptions();
			
			if (isset($_POST['update_BBsigPluginSettings']))
			{
				if(isset($_POST['bbsigBB_cmn_doDebug']))
				{
					$settings['bbsigBB_cmn_doDebug'] = $_POST['bbsigBB_cmn_doDebug'];
				}
				else
				{
					$settings['bbsigBB_cmn_doDebug'] = '';
				}
				
				if(isset($_POST['bbsigBB_cmn_onlyLatest']))
				{
					$settings['bbsigBB_cmn_onlyLatest'] = $_POST['bbsigBB_cmn_onlyLatest'];
				}
				else
				{
					$settings['bbsigBB_cmn_onlyLatest'] = '';
				}
				
				if(isset($_POST['bbsigBB_cmn_preCat']))
				{
					$settings['bbsigBB_cmn_preCat'] = $_POST['bbsigBB_cmn_preCat'];
				}
				
				if (isset($_POST['bbsigBB_cmn_postCat']))
				{
					$settings['bbsigBB_cmn_postCat'] = $_POST['bbsigBB_cmn_postCat'];
				}
				
				$settings['bbsigBB_cmn_count'] = $_POST['bbsigBB_cmn_count'] + 1;
				
				for($i = 1; $i <= $settings['bbsigBB_cmn_count']; $i++)
				{
					if(($_POST['bbsigBB' . $i . '_url'] != '') && ($_POST['bbsigBB' . $i . '_usr'] != '') && ($_POST['bbsigBB' . $i . '_pwd'] != '') && ($_POST['bbsigBB' . $i . '_tpl'] != '') && ($_POST['bbsigBB' . $i . '_typ'] != ''))
					{
						if(isset($_POST['bbsigBB' . $i . '_url']))
						{
							$settings['bbsigBB' . $i . '_url'] = $_POST['bbsigBB' . $i . '_url'];
						}
						
						if(isset($_POST['bbsigBB' . $i . '_usr']))
						{
							$settings['bbsigBB' . $i . '_usr'] = apply_filters('content_save_pre', htmlspecialchars_decode($_POST['bbsigBB' . $i . '_usr']));
						}
						
						if(isset($_POST['bbsigBB' . $i . '_pwd']))
						{
							$settings['bbsigBB' . $i . '_pwd'] = apply_filters('content_save_pre', htmlspecialchars_decode($_POST['bbsigBB' . $i . '_pwd']));
						}
						
						if(isset($_POST['bbsigBB' . $i . '_typ']))
						{
							$settings['bbsigBB' . $i . '_typ'] = $_POST['bbsigBB' . $i . '_typ'];
						}							
						
						if(isset($_POST['bbsigBB' . $i . '_tpl']))
						{
							$settings['bbsigBB' . $i . '_tpl'] = apply_filters('content_save_pre', $_POST['bbsigBB' . $i . '_tpl']);
						}
					}
				}
				
				if($settings['bbsigBB' . $settings['bbsigBB_cmn_count'] . '_tpl'] == '')
				{
					$settings['bbsigBB_cmn_count'] = $settings['bbsigBB_cmn_count'] - 1;
				}
				
				update_option($this->adminOptionsName, $settings);
				
				echo '<div class="updated"><p><strong>';
				_e("Settings Updated.", "BBsigPlugin");
				echo '</strong></p></div>';
			}
			
			echo '	<div class="wrap">
					<form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
					<h2>bbSigPress</h2><p>Need Help? Please visit the <a href="http://matrixagents.org/phpBB/">discussion board</a>.</p><h3>General Settings</h3><p><input type="checkbox" id="bbsigBB_cmn_doDebug" name="bbsigBB_cmn_doDebug" value="bbsigBB_cmn_doDebug"';
			
			if($settings['bbsigBB_cmn_doDebug'] == 'bbsigBB_cmn_doDebug')
			{
				echo ' checked="checked"';
			}
			
			echo ' /> Use Debug mode? <a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a><br /><br />
			<input type="checkbox" id="bbsigBB_cmn_onlyLatest" name="bbsigBB_cmn_onlyLatest" value="bbsigBB_cmn_onlyLatest"';
			
			if($settings['bbsigBB_cmn_onlyLatest'] == 'bbsigBB_cmn_onlyLatest')
			{
				echo ' checked="checked"';
			}
			
			echo ' /> Update only the latest post, even when you save/edit an older one? <a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a><br /><br />
			Before Categories: <input size="4" type="text" id="bbsigBB_cmn_preCat" name="bbsigBB_cmn_preCat" value="' . $settings['bbsigBB_cmn_preCat'] . '" /></label> &nbsp; 
			After Categories: <input size="4" type="text" id="bbsigBB_cmn_postCat" name="bbsigBB_cmn_postCat" value="' . $settings['bbsigBB_cmn_postCat'] . '" /></label> <a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a></p><br /><br />';
			
			if($settings['bbsigBB_cmn_count'] > 0)
			{						
				for($i = 1; $i <= $settings['bbsigBB_cmn_count']; $i++)
				{
					echo '	<h3>Forum #' . $i . '</h3>
							<p>

							<label for="bbsigBB' . $i . '_url">URL: <input size="48" type="text" id="bbsigBB' . $i . '_url" name="bbsigBB' . $i . '_url" value="' . $settings['bbsigBB' . $i . '_url'] . '" /></label> 
							' . $this->bbsig_getSELECT($settings['bbsigBB' . $i . '_typ'], $i) . ' &nbsp;<!--input type="button" value="Check" onclick="" /--><a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a>
							<br /><br />
							<label for="bbsigBB' . $i . '_usr">Username: <input size="16" type="text" id="bbsigBB' . $i . '_usr" name="bbsigBB' . $i . '_usr" value="' . stripslashes(htmlspecialchars(apply_filters('format_to_edit', $settings['bbsigBB' . $i . '_usr']))) . '" /></label> &nbsp; 
							<label for="bbsigBB' . $i . '_pwd">Password: <input size="20" type="password" id="bbsigBB' . $i . '_pwd" name="bbsigBB' . $i . '_pwd" value="' . stripslashes(htmlspecialchars(apply_filters('format_to_edit', $settings['bbsigBB' . $i . '_pwd']))) . '" /></label> <a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a>

							<br /></p><br />';
					
					echo '	Template <a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a><br /><br />
							<textarea name="bbsigBB' . $i . '_tpl" style="width: 60%; height: 90px;">' . apply_filters('format_to_edit', stripslashes($settings['bbsigBB' . $i . '_tpl']))
							. '</textarea><br /><br /><br />';
				}
			}
			
			// ADD NEW FORUM Field
			echo '<br /><br />	<h3>Add new forum</h3>
						<p>

						<label for="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_url">URL: <input size="48" type="text" id="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_url" name="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_url" value="" /></label>
						' . $this->bbsig_getSELECT('', ($settings['bbsigBB_cmn_count'] + 1)) . ' &nbsp;<!--input type="button" value="Check" onclick="" /--><a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a>
						<br /><br />
						<label for="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_usr">Username: <input size="16" type="text" id="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_usr" name="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_usr" value="" /></label> &nbsp; 
						<label for="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_pwd">Password: <input size="20" type="password" id="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_pwd" name="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_pwd" value="" /></label> <a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a>

						<br /></p>
						Template <a href="http://matrixagents.org/phpBB/viewtopic.php?f=15&p=446#p446"><img src="' . $imghelp . '" border="0" alt="Help" /></a><br />%title%, %author%, %permalink%, %category%, %date%, %time%, %commentcount%<br />
						<textarea name="bbsigBB' . ($settings['bbsigBB_cmn_count'] + 1) . '_tpl" style="width: 60%; height: 90px;"></textarea><br />
						
						<input type="hidden" name="bbsigBB_cmn_count" value="' . $settings['bbsigBB_cmn_count'] . '" />';
				
			
			
			echo '	<div class="submit">
					<input type="submit" name="update_BBsigPluginSettings" value="';
					
			_e('Update Settings', 'BBsigPlugin');
			
			echo '	" /></div>
					</form>
					 </div>';
		}	
	}
}



if(class_exists('BBsigPlugin'))
{
	$bbsigInst = new BBsigPlugin();
}

// Initialize the admin panel
if (!function_exists('BBsigPlugin_Panel'))
{
	function BBsigPlugin_Panel()
	{
		global $bbsigInst;
		if(!isset($bbsigInst))
		{
			return;
		}
		
		if (function_exists('add_submenu_page'))
		{
			add_submenu_page('plugins.php', 'bbSigPress', 'bbSigPress', 10, __FILE__, array(&$bbsigInst, 'bbSigprintAdminPage'));
		}
		
		$plugin = plugin_basename(__FILE__);
		add_filter('plugin_action_links_' . $plugin, array(&$bbsigInst, 'bbSigSettingsLink'));		
		
		if (function_exists('register_activation_hook'))
		{
			register_activation_hook(__FILE__, array(&$bbsigInst, 'bbsig_activation'));
		}
		
		if (function_exists('register_deactivation_hook'))
		{
			register_deactivation_hook(__FILE__, array(&$bbsigInst, 'bbsig_deactivation'));
		}
	}	
}

if(isset($bbsigInst))
{
	add_action('admin_menu', 'BBsigPlugin_Panel');
	add_action('publish_post', array(&$bbsigInst, 'updateSigs'));
	add_action('xmlrpc_publish_post', array(&$bbsigInst, 'updateSigs'));
}



?>