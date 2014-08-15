<?php
/*
* ThemePanel
*/
// Note.  When the menu bar is horizontal, mainFolderLeft and mainFolderRight are put in <span></span>.
//        When the menu bar is vertical, they would be put in a separate TD cell.
echo <<<EOD
var cmThemePanel{$this->iMenuNumber} = {
  // main menu display attributes
   mainFolderLeft: ''          // HTML code to the left of the folder item
  ,mainFolderRight: ''                                   // HTML code to the right of the folder item
  ,mainItemLeft: ''            // HTML code to the left of the regular item
  ,mainItemRight: ''           // HTML code to the right of the regular item
  // sub menu display attributes
  ,folderLeft: ''         // HTML code to the left of the folder item
  ,folderRight: ''                                   // HTML code to the right of the folder item
  ,itemLeft: ''           // HTML code to the left of the regular item
  ,itemRight: ''          // HTML code to the right of the regular item
  // spacing and delay
  ,mainSpacing: 0	   // cell spacing for main menu
  ,subSpacing: 0	   // cell spacing for sub menus
  ,delay: 500	       // auto dispear time for submenus in milli-seconds
};
// splits
var cmThemePanelHSplit = [_cmNoClick, '<td colspan="3" style="height: 5px; overflow: hidden"><div class="ThemePanelMenuSplit"><\/div><\/td>'];
var cmThemePanelMainHSplit = [_cmNoClick, '<td colspan="3" style="height: 5px; overflow: hidden"><div class="ThemePanelMenuSplit"><\/div><\/td>'];
var cmThemePanelMainVSplit = [_cmNoClick, '|'];
EOD;
?>
