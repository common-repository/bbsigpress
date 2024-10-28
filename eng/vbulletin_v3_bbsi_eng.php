<?php

// WARNING !  THIS FILE NEEDS
// TO BE IN A STANDARD FORMAT
// DO NOT CHANGE THIS FILE !!

function vbulletin_v3_bbsi_eng($bbaddress, $username, $password, $template)
{
	
//BBVAR:$bbName
	$bbName			=	'vBulletin 3.x';
//BBVAR:$bbUnique
	$bbUnique		=	'vbulletin_v3_bbsi_eng';
//BBVAR:$bbcheckURL
	$bbcheckURL		=	'/usercp.php';
//BBVAR:$bbcheckTXT
	$bbcheckTXT		=	'%<meta name="generator" content="vBulletin 3.%';
//BBVAR:$bbloginURL
	$bbloginURL		=	'/login.php?do=login';
//BBVAR:$bbeditSURL
	$bbeditSURL		=	'/profile.php?do=editsignature';
//BBVAR:$bbupdatURL
	$bbupdatURL		=	'/profile.php?do=updatesignature';

	
	$loginFields	=	'vb_login_username=' . $username . '&cookieuser=true&do=login&vb_login_password=' . $password . '&s=&vb_login_md5password' . md5($password) . '&vb_login_md5password_utf' . utf8_encode(md5($password)) . '';
	$bbaddress		=	str_replace($bbcheckURL, '', $bbaddress);
	
	
	$ch = curl_init();
	@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	@curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.4) Gecko/2008102920 Firefox/3.0.4');
	@curl_setopt($ch, CURLOPT_REFERER, '');
	@curl_setopt($ch, CURLOPT_COOKIEJAR, './cookie/'); 
		
	// CHARSET
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbcheckURL);
	curl_exec($ch);
	
	$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	preg_match( '@([\w/+]+)(;\s+charset=(\S+))?@i', $content_type, $matches);
	if (isset( $matches[3]))
	{
		$charset = $matches[3];
		$username = iconv(get_option('blog_charset'), $charset . '//IGNORE', $username);
		$password = iconv(get_option('blog_charset'), $charset . '//IGNORE', $password);
		$template = iconv(get_option('blog_charset'), $charset . '//IGNORE', $template);
	}
	
	// LOGIN
	@curl_setopt($ch, CURLOPT_POST, TRUE);
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbloginURL);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $loginFields);
	$result = curl_exec($ch);

	// GET SECURITY TOKEN
	@curl_setopt($ch, CURLOPT_POST, FALSE);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, '');
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbeditSURL);
	$result = curl_exec($ch);
	
	preg_match('%var SECURITYTOKEN = "(.*)";%', $result, $res);
	$token = $res[1];

	// UPDATE THE SIGNATURE
	$editFields = 'message=' . $template . '&securitytoken=' . $token . '&do=updatesignature&s=';

	@curl_setopt($ch, CURLOPT_POST, TRUE);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $editFields);
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbupdatURL);
	$result = curl_exec($ch);
	
	curl_close($ch);
}

?>