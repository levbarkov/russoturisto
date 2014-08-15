Верстка - разделы:<br />
<?

	$dir = site_path."/component"; 
	foreach (glob("$dir/*") as $path) {
		if(  is_dir($path)  ){
			preg_match("/([\w-]+)$/",$path, $matches);
			
			if(  preg_match("/^design_/", $matches[1])  ){
				?><a href="/<?=$matches[1] ?>"><?=$matches[1] ?></a><br /><?
			}
		}
	}

?>