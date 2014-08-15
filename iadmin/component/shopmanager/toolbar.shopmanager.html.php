<?php


defined( '_VALID_INSITE' ) or die( 'умр' );


class TOOLBAR_users {

	/**

	* Draws the menu to edit a user

	*/

	function _EDIT() {

		global $id;



		mosMenuBar::startTable();

		mosMenuBar::save();

		mosMenuBar::spacer();

		mosMenuBar::spacer();

		if ( $id ) {

			mosMenuBar::cancel( 'cancel', 'Закрыть' );

		} else {

			mosMenuBar::cancel();

		}

		mosMenuBar::endTable();

	}



	function _DEFAULT() {

		mosMenuBar::startTable();
		mosMenuBar::spacer();
		mosMenuBar::endTable();

	}

}

?>

