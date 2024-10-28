<?php

// WARNING !  THIS FILE NEEDS
// TO BE IN A STANDARD FORMAT
// DO NOT CHANGE THIS FILE !!

function woltlabBB_v2_bbsi_eng($bbaddress, $username, $password, $template)
{
	
//BBVAR:$bbName
	$bbName			=	'Burning Board 2.x';
//BBVAR:$bbUnique
	$bbUnique		=	'woltlabBB_v2_bbsi_eng';
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
	
	
	$loginFields	=	'l_username=' . $username . '&send=send&l_password=' . $password . '&s=&sid=' . '';
	$bbaddress		=	str_replace($bbcheckURL, '', $bbaddress);
	
	// LOGIN
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
	
	@curl_setopt($ch, CURLOPT_POST, TRUE);
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbloginURL);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $loginFields);
	$result = curl_exec($ch);
	
	// LOAD EDITING PAGE
	@curl_setopt($ch, CURLOPT_POST, FALSE);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, '');
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbeditSURL);
	$result = curl_exec($ch);
	
	// UPDATE THE SIGNATURE
	$editFields = 'message=' . $template . '&send=send&action=signature_edit&sid=&s=';
	
	@curl_setopt($ch, CURLOPT_POST, TRUE);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $editFields);
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbupdatURL);
	$result = curl_exec($ch);
	
	curl_close($ch);	
}

?>