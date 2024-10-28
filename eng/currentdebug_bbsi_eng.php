<?php

// WARNING !  THIS FILE NEEDS
// TO BE IN A STANDARD FORMAT
// DO NOT CHANGE THIS FILE !!

function currentdebug_bbsi_eng($bbaddress, $username, $password, $template, $dbg = 0)
{
	
	//BBVAR:$bbName
		$bbName			=	'Testing';
	//BBVAR:$bbUnique
		$bbUnique		=	'currentdebug_bbsi_eng';
	//BBVAR:$bbcheckURL
		$bbcheckURL		=	'/usercp.php';
	//BBVAR:$bbcheckTXT
		$bbcheckTXT		=	'%<a href="http://www.woltlab.de" target="_blank">(.*)Burning Board  2.%';
	//BBVAR:$bbloginURL
		$bbloginURL		=	'/login.php';
	//BBVAR:$bbeditSURL
		$bbeditSURL		=	'/usercp.php?action=signature_edit';
	//BBVAR:$bbupdatURL
		$bbupdatURL		=	'/usercp.php';
	
	
	
	if($dbg === 1)
	{
		echo '<pre>Function params:<br />';
		echo date('y-m-d h:i') . ' - $bbaddress = ' . $bbaddress . '<br />';
		echo date('y-m-d h:i') . ' - $username = ' . $username . '<br />';
		echo date('y-m-d h:i') . ' - $password = ' . $password . '<br />';
		echo date('y-m-d h:i') . ' - $template = ' . $template . '<br /><br /><br />';
	}
	
	$loginFields	=	'l_username=' . $username . '&send=send&l_password=' . $password . '&s=&sid=' . '';
	$bbaddress		=	str_replace($bbcheckURL, '', $bbaddress);
	
	if($dbg === 1)
	{
		echo date('y-m-d h:i') . ' - $bbName = ' . $bbName . '<br />';
		echo date('y-m-d h:i') . ' - $bbUnique = ' . $bbUnique . '<br />';
		echo date('y-m-d h:i') . ' - $bbcheckURL = ' . $bbcheckURL . '<br />';
		echo date('y-m-d h:i') . ' - $bbcheckTXT = ' . htmlspecialchars($bbcheckTXT) . '<br />';
		echo date('y-m-d h:i') . ' - $bbloginURL = ' . $bbloginURL . '<br />';
		echo date('y-m-d h:i') . ' - $bbeditSURL = ' . $bbeditSURL . '<br />';
		echo date('y-m-d h:i') . ' - $bbupdatURL = ' . $bbupdatURL . '<br />';
		echo '<br /><br />' . date('y-m-d h:i') . ' - POST-FIELDS for Login: ' . $loginFields . '<br /><br />Base-URI: ' . $bbaddress . '<br /><br /><br />Now executing the login on: ' . $bbaddress . $bbloginURL . '<br />';
	}
	
	// LOGIN
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.4) Gecko/2008102920 Firefox/3.0.4');
	curl_setopt($ch, CURLOPT_REFERER, '');
	curl_setopt($ch, CURLOPT_COOKIEJAR, './cookie/'); 
	
	// CHARSET
	curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbcheckURL);
	curl_exec($ch);
	
	$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	preg_match( '@([\w/+]+)(;\s+charset=(\S+))?@i', $content_type, $matches);
	if (isset( $matches[3]))
	{
		$charset = $matches[3];
		if($dbg === 1)
		{
			$blogCS = 'UTF-8';
		}
		else
		{
			$blogCS = get_option('blog_charset');
		}
		$username = iconv(get_option('blog_charset'), $charset . '//IGNORE', $username);
		$password = iconv(get_option('blog_charset'), $charset . '//IGNORE', $password);
		$template = iconv(get_option('blog_charset'), $charset . '//IGNORE', $template);
				
		if($dbg === 1)
		{
			echo date('y-m-d h:i') . ' - $charset = ' . $charset . ', the following are the converted values:<br />';
			echo date('y-m-d h:i') . ' - $username = ' . $username . '<br />';
			echo date('y-m-d h:i') . ' - $password = ' . $password . '<br />';
			echo date('y-m-d h:i') . ' - $template = ' . htmlspecialchars($template) . '<br />';
			echo '<br /><br />';
		}	
		
	}

	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbloginURL);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $loginFields);
	$result = curl_exec($ch);
	
	if($dbg === 1)
	{
		echo '<br /><br />' . date('y-m-d h:i') . ' - URI for in between Edit-Page-Call: ' . $bbaddress . $bbeditSURL . '<br />Now executing!<br /><br />';
	}
	
	// GET SECURITY TOKEN
	curl_setopt($ch, CURLOPT_POST, FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, '');
	curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbeditSURL);
	$result = curl_exec($ch);
	
	/*preg_match('%var SECURITYTOKEN = "(.*)";%', $result, $res);
	$token = $res[1];

	if($dbg === 1)
	{
		echo date('y-m-d h:i') . ' - RegexResults:<br />';
		print_r($res);
		
		echo date('y-m-d h:i') . ' - $token = ' . $token . '<br />';
	}
	*/
	
	// UPDATE THE SIGNATURE
	$editFields = 'message=' . $template . '&send=send&action=signature_edit&sid=&s=';
	
	if($dbg === 1)
	{
		echo '<br /><br />' . date('y-m-d h:i') . ' - POST-FIELDS for Update: ' . $editFields . '<br /><br />Now executing the update on: ' . $bbaddress . $bbupdatURL . '<br />';
	}

	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $editFields);
	curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbupdatURL);
	$result = curl_exec($ch);
	
	curl_close($ch);
	
	if($dbg === 1)
	{
		echo  '<br /><br />' . date('y-m-d h:i') . ' - <b>Done!</b><br /></pre>';
	}
}



/* TESTING */

currentdebug_bbsi_eng('URL', 'USER', 'PASS', 'SIG', 1);

/* TESTING */







?>