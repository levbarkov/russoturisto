<? global $reg; ?>
<style>
p.html_tags a:link, p.html_tags a:visited{	text-decoration:none;		}
p.html_tags a:hover{						text-decoration:underline;	}
.w0{	font-size:	9pt;	}
.w1{	font-size:	11pt;	}
.w2{	font-size:	12pt;	}
.w3{	font-size:	13pt;	}
.w4{	font-size:	14pt;	}
.w5{	font-size:	15pt;	}
.w6{	font-size:	16pt;	}
.w7{	font-size:	18pt;	}
.w8{	font-size:	20pt;	}
.w9{	font-size:	22pt;	}
.w10{	font-size:	24pt;	}
.c0{	color:#e5e5e5;	}
.c1{	color:#d3d3d3;	}
.c2{	color:#b9b9b8;	}
.c3{	color:#a4a4a4;	}
.c4{	color:#8e8e8e;	}
.c5{	color:#6f6f6e;	}
.c6{	color:#555555;	}
.c7{	color:#4c4c4c;	}
.c8{	color:#3e3e3e;	}
.c9{	color:#202020;	}
.c10{	color:#000000;	}
</style>
<p class="html_tags"><?
	$tag_file = site_path.$reg['tags_file'];
	if (  file_exists($tag_file)  ) {
		$xml = simplexml_load_file(  $tag_file  );
		$data = get_object_vars($xml);
		
//		ggr ($data['tag'][2]->size);
//		$arrXml = objectsIntoArray($xml);
//		print_r($arrXml);
		
		$tags = $xml->tag;
		foreach($tags as $index=>$tag){	//$tag['ex']
			?><a href='<?=site_url ?>/search?isearch=<?=stripslashes($tag->name) ?>' class='w<?=round((float)$tag->size*10) ?> c<?=round((float)$tag->bright*10) ?>'><?=stripslashes($tag->name) ?></a>&nbsp;&nbsp;&nbsp; <?
		}
	} else { ?><a href='<?=site_url ?>' class='w10 c10'>NO XML TEGS FILE</a><? }
?>
<!--<a href="#" class="w4 c10">intrigue</a> -->
</p>