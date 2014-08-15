<?
define( "_VALID_INSITE", 1 );
/* - URLS for testing
http://vodyanoy.dev/calcdata.php?g24decor=19&l=10&h=5&w=10&dm=19
http://vodyanoy.dev/calcdata.php?g24decor=19&l=10&h=5&w=10&dm=28&mount=1
*/
// code to access joomla functions

require_once("../../../../iconfig.php");
require_once(site_path . '/iconfig.php');
require_once(site_path . '/i24.php' );
require_once(site_path. '/idb.php');
$mosConfig_dbprefix = $DBPrefix;
$mosConfig_host = $DBhostname;
$mosConfig_user = $DBuserName;
$mosConfig_password = $DBpassword;
$mosConfig_db = $DBname;
$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );

function eGetShopId(){
		global $database;
		$G24sql2 = "SELECT id FROM #__menu WHERE link = 'index.php?option=com_virtuemart'";
		$database->setQuery( $G24sql2 );
		$rows = $database->loadObjectList( );
		if (  count ($rows)==0  ) return -1;
		return $rows[0]->id;
}
function eGetCatName($ecatid){
		global $database;
		$G24sql2 = "SELECT name, id FROM #__excat WHERE id=".$ecatid;
		$database->setQuery( $G24sql2 );
		$rows = $database->loadObjectList( );
		if (  count ($rows)==0  ) return "Folders";
		return just_del_quotes( $rows[0]->name );
}
function eGetUpCatId($ecatid){
		global $database;
		$G24sql2 = "SELECT parent FROM #__excat WHERE id=".$ecatid;
		$database->setQuery( $G24sql2 );
		$rows = $database->loadObjectList( );
		if (  count ($rows)==0  ) return "0";
		return $rows[0]->parent;
}
function eGetUpCat($ecatid){
		global $database;
		$G24sql2 = "SELECT * FROM #__vm_category, #__vm_category_xref WHERE #__vm_category.category_id=".$ecatid." AND #__vm_category_xref.category_child_id=#__vm_category.category_id;";
		$database->setQuery( $G24sql2 );
		$rows = $database->loadObjectList( );
		if (  count ($rows)==0  ) return "0";
		return $rows[0];
}



$eShopId = eGetShopId();

$eid = $_REQUEST["id"];

						// ФОРМИРУЕМ ВХОДНОЙ МАССИВ - СПИСОК ДИРЕКТОРИЙ
						static $links = array();
						if (empty($links)) {
								$G24sql = "SELECT * FROM #__excat WHERE parent=".$eid." ORDER BY name ASC";
								$database->setQuery( $G24sql );
								$rows = $database->loadObjectList( );
								//ggtr ($rows);  ggtr (eGetCatName($eid));
								array_push($links, array(0, 'folder', eGetCatName($eid), 'text', 'text', 1, "") );
								foreach ($rows as $row){
									$ifotocat = ggsql( "SELECT * FROM #__excat_foto WHERE excat_id=".$row->id );  // определим фотки
									if (  count ($ifotocat)==0  ) $row->category_full_image="/includes/images/noimage.png";
									else $row->category_full_image="/images/ex/cat/".$ifotocat[0]->org;

									if (  count ($ifotocat)==0  )	array_push($links, array(1, $row->id,  (just_del_quotes($row->name)), "/includes/images/noimage.png", "/includes/images/noimage.png", 1, "") );
									else	array_push($links, array(1, $row->id, (just_del_quotes($row->name)), "/images/ex/cat/".$ifotocat[0]->small, "/images/ex/cat/".$ifotocat[0]->org, 1, "") );
								} 
								
								// ФОРМИРУЕМ ВХОДНОЙ МАССИВ - СПИСОК ТОВАРОВ
								$G24sql = "SELECT * FROM #__exgood WHERE #__exgood.parent=".$eid;
								$database->setQuery( $G24sql );
								$rows = $database->loadObjectList( );
//								ggtr ($rows);
								foreach ($rows as $row){
/*									$G24sql2 = "SELECT #__vm_product_price.product_price FROM #__vm_product, #__vm_product_price WHERE #__vm_product.product_id = #__vm_product_price.product_id AND #__vm_product.product_id=".$row->product_id;
									$database->setQuery( $G24sql2 );
									$rows2 = $database->loadObjectList( );
									$ggprice = isset ($rows2[0]->product_price) ? $rows2[0]->product_price:"";*/
									$ggprice="";
									//$ifotogood = ggsql( "SELECT * FROM #__exgood_foto WHERE exgood_id=".$row->id );  // определим фотки
									//ggtr (count ($ifotogood));
									if (  $row->org==''  ) $row->product_full_image="/includes/images/noimage.png";
									else $row->product_full_image="/images/ex/good/".$row->org;
									if (  $row->small==''  )	array_push($links, array(1, $row->sefnamefullcat.'/'.$row->sefname.'.html', just_del_quotes($row->name), "/includes/images/noimage.png", "/includes/images/noimage.png", 2, $ggprice) );
									else array_push($links, array(1, $row->sefnamefullcat.'/'.$row->sefname.'.html', just_del_quotes($row->name), "/images/ex/good/".$row->small, "/images/ex/good/".$row->org, 2, $ggprice) );
								} 

							

							$num_links = sizeof($links);
			//				print "alert(".$num_links.")";
							$arr = array();
							for ($i=0; $i<$num_links; $i++) { 
								if ((!empty($links[$i][1])) && (!empty($links[$i][2]))) {
									$links[$i][1] = str_replace(array("\t","\r\n","\r","\n",'/'), array(' ','','','','\/'), strip_tags($links[$i][1]));
									$links[$i][2] = str_replace(array("\t","\r\n","\r","\n",'/'), array(' ','','','','\/'), strip_tags($links[$i][2]));
									$links[$i][3] = str_replace(array("\t","\r\n","\r","\n",'/'), array(' ','','','','\/'), strip_tags($links[$i][3]));
									$links[$i][4] = str_replace(array("\t","\r\n","\r","\n",'/'), array(' ','','','','\/'), strip_tags($links[$i][4]));
									array_push($arr, '['.intval($links[$i][0]).',"'.$links[$i][1].'","'.$links[$i][2].'","'.$links[$i][3].'","'.$links[$i][4].'","'.$links[$i][5].'","'.$links[$i][6].'"]');
								}	
							}
							$links = implode(',',$arr);
						}		
						
						
						
?>
//alert (' <?  print $eid ; ?>');

var CURRENT_HIGHLIGHT2;		
function highlight2(srcElement) {
	if (CURRENT_HIGHLIGHT2) {
		CURRENT_HIGHLIGHT2.style.backgroundColor="#ffffff";
		CURRENT_HIGHLIGHT2.style.color ="#003399";
	}
	srcElement.style.backgroundColor="highlight";
	srcElement.style.color = "highlighttext";
	CURRENT_HIGHLIGHT2 = srcElement;
}


function localLink2(page,e24title,e24img, e24fimg, e24ltype, ggprice) {

	page = page.replace(/&quote;/gi, '"');
	if (e24ltype==2){
		document.getElementById('txtUrl').value=page;
	} else if (e24ltype==1){
		document.getElementById('txtUrl').value="?c=ex&task=excat&id="+page;
	}
//	alert(e24title);
	if (  e24ltype!=3  ){
		var elm = document.getElementById('prev');
		elm.innerHTML = '<img id="previewImg" src="' + e24img + '" border="0" onload="updateImageData();"/>';
		
		document.getElementById('e24hit').value=e24title;
		document.getElementById('e24img').value="<?php echo site_url; ?>"+e24img;
		document.getElementById('e24fimg').value="<?php echo site_url; ?>"+e24fimg;	
		document.getElementById('ggprice').value=ggprice;
	}
	
	if (e24ltype==1 || e24ltype==3){
				document.getElementById('links').innerHTML = '<table width="100%" height="90%"><tr><td align="center" valign="middle">Loading...<br><br><img src="<?php echo site_url; ?>/ibots/editors/tinymce/e24code/img/load_bar.gif" height="12" width="251" alt="" class="inset"><br></td></tr></table>';
				var pt = document.getElementById('js_loader');
				pt.parentNode.removeChild(pt);

				pt = document.createElement('script');
				document.body.appendChild(pt);
				pt.setAttribute('type', 'text/javascript');
				pt.setAttribute('id', 'js_loader');

				pt.setAttribute('src', '<?php echo site_url; ?>/ibots/editors/tinymce/e24code/calcdata.php?id='+page);	
				
				page = page.replace(/&quote;/gi, '"');	
	}
	
}						
							olinks2=[<? print($links); ?>];
							obj2 = "";
							obj2.links = olinks2;
							// show links
							if (obj2.links != '') {
								if (1) {
									var links2 = olinks2;
									var depth = '';
									var indent = '';
									var str = '';
									var num = links2.length;
									for (var i=0; i<num; i++) { 
										if (links2[i][1] && links2[i][2]) {
											if (links2[i][0] >= 1) {
												depth = (links2[i][0]-1)*23;
												indent = "<img src=\"/ibots/editors/tinymce/e24code/img/branch.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">";
											} else {
												depth = 0;
												indent = '';
											}
											if (links2[i][5] == 1) {
												e24imgfile = "<img src=\"/ibots/editors/tinymce/e24code/img/folder.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">";
											} else if (links2[i][5] == 2) {
												e24imgfile = "<img src=\"/ibots/editors/tinymce/e24code/img/htm_icon.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">";
											}
											
											
											if (links2[i][1] == 'folder') {
												str += "<nobr><p class=\"filelink\" style=\"height:22px;margin:2px 2px 2px "+(depth+2)+"px\">" + indent + "<img src=\"/ibots/editors/tinymce/e24code/img/folder.gif\" width=\"23\" height=\"22\" alt=\"\" border=\"0\" align=\"absmiddle\">" + (links2[i][2].replace(/' '/gi, '&nbsp;')) + "<? if (  $eid!=0  ){ ?> <a              href=\"javascript:localLink2(\'<? print eGetUpCatId($eid); ?>\',\'"+(links2[i][2].replace(/'/gi, "\\'").replace(/"/gi, '&quot;'))+"\',\'"+links2[i][3]+"\',\'"+links2[i][4]+"\',\'3\');\"                         ><img src=\"/ibots/editors/tinymce/e24code/img/folderup.gif\" width=\"16\" height=\"16\" alt=\"\" border=\"0\" align=\"absmiddle\"></a> <? } ?> </p></nobr>";
											} else {
												str += "<nobr><a style=\"display:block;\" id=\"" + (links2[i][1].replace(/"/gi, '&quote;')) + "\" style=\"text-decoration:none; height:22px; margin:0px 0px 0px " + depth + "px;\" onclick=\" highlight2(this); \" href=\"javascript:localLink2(\'" + (links2[i][1].replace(/'/gi, "\\'").replace(/"/gi, '&quote;')) + "\',\'"+(links2[i][2].replace(/'/gi, "\\'").replace(/"/gi, '&quot;'))+"\',\'"+links2[i][3]+"\',\'"+links2[i][4]+"\',\'"+links2[i][5]+"\',\'"+links2[i][6]+"\');\" title=\"URL: " + (links2[i][1].replace(/"/gi, '&quote;')) + "\">" + indent +  e24imgfile + (links2[i][2].replace(/ /gi, '&nbsp;')) + " </a></nobr>";
											}
										}
									}
									document.getElementById('links').innerHTML = str;
									obj.linksHTML = str;
								} else {
									document.getElementById('links').innerHTML = obj.linksHTML;
								}
							}



