<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* Tab Creation handler
*/
class iTabs {
        /** @var int Use cookies */
        var $useCookies = 0;

        /**
        * Constructor
        * Includes files needed for displaying tabs and sets cookie options
        * @param int useCookies, if set to 1 cookie will hold last used tab between page refreshes
        */
        function iTabs( $useCookies, $xhtml=NULL ) {
                global $mainframe;

                if ( $xhtml ) {
                        $mainframe->addCustomHeadTag( '<link rel="stylesheet" type="text/css" media="all" href="includes/js/tabs/tabpane.css" id="luna-tab-style-sheet" />' );
                } else {
                        echo "<link id=\"luna-tab-style-sheet\" type=\"text/css\" rel=\"stylesheet\" href=\"" . site_url. "/includes/js/tabs/tabpane.css\" />";
                }

                echo "<script type=\"text/javascript\" src=\"". site_url . "/includes/js/tabs/tabpane_mini.js\"></script>";

                $this->useCookies = $useCookies;
        }

        /**
        * creates a tab pane and creates JS obj
        * @param string The Tab Pane Name
        */
        function startPane($id){
		echo "<div class=\"tab-page\" id=\"".$id."\">";
                echo "<script type=\"text/javascript\">\n";
                echo "        var tabPane1 = new WebFXTabPane( document.getElementById( \"".$id."\" ), ".$this->useCookies." )\n";
                echo "</script>\n";
        }

        /**
        * Ends Tab Pane
        */
        function endPane() {
                echo "</div>";
        }

        /*
        * Creates a tab with title text and starts that tabs page
        * @param tabText - This is what is displayed on the tab
        * @param paneid - This is the parent pane to build this tab on
        */
        function startTab( $tabText, $paneid ) {
                echo "<div class=\"tab-page\" id=\"".$paneid."\">";
                echo "<h2 class=\"tab\">".$tabText."</h2>";
                echo "<script type=\"text/javascript\">\n";
                echo "  tabPane1.addTabPage( document.getElementById( \"".$paneid."\" ) );";
                echo "</script>";
        }

        /*
        * Ends a tab page
        */
        function endTab() {
                echo "</div>";
        }
}
?>