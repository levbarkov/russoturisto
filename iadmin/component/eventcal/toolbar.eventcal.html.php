<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class TOOLBAR_eventcal {
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::editList();
		mosMenuBar::spacer();
		mosMenuBar::addNew();
		mosMenuBar::spacer();
		mosMenuBar::spacer();
		mosMenuBar::spacer();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::spacer();
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::endTable();
	}
	function _NEW() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::apply();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _EDIT(){
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _CATEGORIES() {
		mosMenuBar::startTable();
	?>
		<td>
		<a class="toolbar" href="index2.php?ca=categories&section=com_eventcal" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('categorymanager','','component/eventcal/images/category_f2.png',1);">
		<img name="categorymanager" src="component/eventcal/images/category_f2.png" alt="Show Category-Manager" align="middle" border="0"><br/>
		Category Manager</a>
		</td> 
	<?php
		mosMenuBar::spacer();
		mosMenuBar::custom( 'publishcategory', 'publish.png', 'publish_f2.png', 'Publish', true );
		mosMenuBar::spacer();
		mosMenuBar::custom( 'unpublishcategory', 'unpublish.png', 'unpublish_f2.png', 'Unpublish', true );
		mosMenuBar::spacer();
		mosMenuBar::spacer();
		mosMenuBar::apply();
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
	
	function _CONFIG() {
		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::custom( 'storeconfig', 'save.png', 'save_f2.png', 'Save Settings', false );
		mosMenuBar::spacer();
		mosMenuBar::endTable();
	}
}
?>