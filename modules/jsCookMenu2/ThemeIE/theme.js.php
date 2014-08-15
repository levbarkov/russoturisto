<?php
/*
* ThemeIE
*/
// Note.  When the menu bar is horizontal, mainFolderLeft and mainFolderRight are put in <span></span>.
//        When the menu bar is vertical, they would be put in a separate TD cell.
echo <<<EOD
var cmThemeIE{$this->iMenuNumber} = {
  // main menu display attributes
   mainFolderLeft: ''                              // HTML code to the left of the folder item
  ,mainFolderRight: '{$this->sFolderImageMain}'    // HTML code to the right of the folder item
  ,mainItemLeft: ''                                // HTML code to the left of the regular item
  ,mainItemRight: ''                               // HTML code to the right of the regular item
  // sub menu display attributes
  ,folderLeft: '<img alt="" src="{$this->sMenuImagePath}folder.gif">'       // HTML code to the left of the folder item
  ,folderRight: '{$this->sFolderImageSub}'                                  // HTML code to the right of the folder item
  ,itemLeft: '<img alt="" src="{$this->sMenuImagePath}link.gif">'           // HTML code to the left of the regular item
  ,itemRight: '&nbsp;'                                                      // HTML code to the right of the regular item
  // spacing and delay
  ,mainSpacing: 0	   // cell spacing for main menu
  ,subSpacing: 0     // cell spacing for sub menus
  ,delay: 100	       // auto dispear time for submenus in milli-seconds
};
// splits
var cmThemeIEHSplit = [_cmNoClick, '<td colspan="3" style="height: 3px; overflow: hidden"><div class="ThemeIEMenuSplit"><\/div><\/td>'];
var cmThemeIEMainHSplit = [_cmNoClick, '<td colspan="3"><div class="ThemeIEMenuSplit"><\/div><\/td>'];
var cmThemeIEMainVSplit = [_cmNoClick, '<div class="ThemeIEMenuVSplit"><\/div>'];
EOD;
?>
