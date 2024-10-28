<?php

// WARNING !  THIS FILE NEEDS
// TO BE IN A STANDARD FORMAT
// DO NOT CHANGE THIS FILE !!

function phpBB_v3_bbsi_eng($bbaddress, $username, $password, $template, $dbg = 0)
{
	
//BBVAR:$bbName
		$bbName			=	'phpBB 3';
//BBVAR:$bbUnique
		$bbUnique		=	'phpBB_v3_bbsi_eng';
//BBVAR:$bbcheckURL
		$bbcheckURL		=	'/ucp.php';
//BBVAR:$bbcheckTXT
		$bbcheckTXT		=	'%<meta content="2000, 2002, 2005, 2007 phpBB Group" name="copyright"/>%';
//BBVAR:$bbloginURL
		$bbloginURL		=	'/ucp.php?mode=login';
//BBVAR:$bbeditSURL
		$bbeditSURL		=	'/ucp.php?i=profile&mode=signature';
//BBVAR:$bbupdatURL
		$bbupdatURL		=	'/ucp.php?i=profile&mode=signature';

	
	$loginFields	=	'username=' . $username . '&viewonline=viewonline&redirect=index.php&password=' . $password . '&login=Log+in';
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
	$result = curl_exec($ch);
	
	$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	preg_match( '@([\w/+]+)(;\s+charset=(\S+))?@i', $content_type, $matches);
	if (isset( $matches[3]))
	{
		$charset = $matches[3];
		$blogCS = get_option('blog_charset');
		$username = iconv($blogCS, $charset . '//IGNORE', $username);
		$password = iconv($blogCS, $charset . '//IGNORE', $password);
		$template = iconv($blogCS, $charset . '//IGNORE', $template);
	}
	
	// GET SID
	preg_match('%type\="hidden" name\="sid" value\="(.*)"%', $result, $res);
	$sid = $res[1];
	$loginFields .= '&sid=' . $sid;
	

	@curl_setopt($ch, CURLOPT_POST, TRUE);
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbloginURL);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $loginFields);
	$result = curl_exec($ch);

	
	// GET SECURITY TOKEN
	@curl_setopt($ch, CURLOPT_POST, FALSE);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, '');
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbeditSURL);
	$result = curl_exec($ch);
	
	preg_match('%type\="hidden" name\="form_token" value\="(.*)"%', $result, $res);
	$token = $res[1];
	
	preg_match('%type\="hidden" name\="creation_time" value\="(.*)"%', $result, $res);
	$creation_time = $res[1];
	
	preg_match('%input class\="btnmain" type\="submit" name\="submit" value\="([^ ]*)"%', $result, $res);
	$subValue = $res[1];
	
	
	// UPDATE THE SIGNATURE
	$editFields = 'signature=' . $template . '&form_token=' . $token . '&sid=' . $sid . '&creation_time=' . $creation_time . '&submit=' . $subValue;
	@curl_setopt($ch, CURLOPT_POST, TRUE);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $editFields);
	@curl_setopt($ch, CURLOPT_URL, $bbaddress . $bbupdatURL);
	$result = curl_exec($ch);
	
	curl_close($ch);
}

?>