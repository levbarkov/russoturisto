<?php
/*
* ThemeOffice2003
*/
// Note.  When the menu bar is horizontal, mainFolderLeft and mainFolderRight are put in <span></span>.
//        When the menu bar is vertical, they would be put in a separate TD cell.
echo <<<EOD
var cmThemeOffice2003{$this->iMenuNumber} = {
  // main menu display attributes
   mainFolderLeft: '&nbsp;'                        // HTML code to the left of the folder item
  ,mainFolderRight: '{$this->sFolderImageMain}'    // HTML code to the right of the folder item
  ,mainItemLeft: '&nbsp;'                          // HTML code to the left of the regular item
  ,mainItemRight: '&nbsp;'                         // HTML code to the right of the regular item
  // sub menu display attributes
  ,folderLeft: '<img alt="" src="{$this->sMenuImagePath}spacer.gif">'       // HTML code to the left of the folder item
  ,folderRight: '{$this->sFolderImageSub}'                                  // HTML code to the right of the folder item
  ,itemLeft: '<img alt="" src="{$this->sMenuImagePath}spacer.gif">'         // HTML code to the left of the regular item
  ,itemRight: '<img alt="" src="{$this->sMenuImagePath}blank.gif">'         // HTML code to the right of the regular item
  // spacing and delay
  ,mainSpacing: 0	   // cell spacing for main menu
  ,subSpacing: 0	   // cell spacing for sub menus
  ,delay: 500	       // auto dispear time for submenus in milli-seconds
};
// splits
var cmThemeOffice2003HSplit = [_cmNoClick, '<td class="ThemeOffice2003MenuItemLeft"><\/td><td colspan="2"><div class="ThemeOffice2003MenuSplit"><\/div><\/td>'];
var cmThemeOffice2003MainHSplit = [_cmNoClick, '<td class="ThemeOffice2003MainItemLeft"><\/td><td colspan="2"><div class="ThemeOffice2003MenuSplit"><\/div><\/td>'];
var cmThemeOffice2003MainVSplit = [_cmNoClick, '|'];
EOD;
?>
