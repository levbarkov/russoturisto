<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* Utility class for all HTML drawing classes
* @package Joomla
*/
class mosHTML {
		public static function makeOption( $value, $text='', $value_name='value', $text_name='text' ) {
				$obj = new stdClass;
				$obj->$value_name = $value;
				$obj->$text_name = trim( $text ) ? $text : $value;
				return $obj;
		}

  function writableCell( $folder, $relative=1, $text='', $visible=1 ) {
	$writeable 		= '<b><font color="green">Доступен для записи</font></b>';
	$unwriteable 	= '<b><font color="red">Недоступен для записи</font></b>';

          echo '<tr>';
  	echo '<td class="item">';
	echo $text;
	if ( $visible ) {
		echo $folder . '/';
	}
	print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if ( $relative ) {
		echo is_writable( "../$folder" ) 	? $writeable : $unwriteable;
	} else {
		echo is_writable( "$folder" ) 		? $writeable : $unwriteable;
	}
	echo '</td>';
          echo '</tr>';
  }

        /**
        * Generates an HTML select list
        * @param array An array of objects
        * @param string The value of the HTML name attribute
        * @param string Additional HTML attributes for the <select> tag
        * @param string The name of the object variable for the option value
        * @param string The name of the object variable for the option text
        * @param mixed The key that is selected
        * @returns string HTML for the select list
        */
        public static function selectList( &$arr, $tag_name, $tag_attribs, $key, $text, $selected=NULL, $freecode="" ) {
		// check if array
		if ( is_array( $arr ) ) {
                reset( $arr );
		}
		
                $html = "<select $freecode name=\"$tag_name\" $tag_attribs>";
		$count 	= count( $arr );
		
		for ($i=0, $n=$count; $i < $n; $i++ ) {
                        $k = $arr[$i]->$key;
                        $t = $arr[$i]->$text;
                        $id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

                        $extra = '';
                        $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
                        if (is_array( $selected )) {
                                foreach ($selected as $obj) {
                                        $k2 = $obj->$key;
                                        if ($k == $k2) {
                                                $extra .= " selected=\"selected\"";
                                                break;
                                        }
                                }
                        } else {
                                $extra .= ($k == $selected ? " selected=\"selected\"" : '');
                        }
                        $html .= "<option value=\"".$k."\"$extra>" . $t . "</option>";
                }
                $html .= "</select>";
		
                return $html;
        }

        /**
        * Writes a select list of integers
        * @param int The start integer
        * @param int The end integer
        * @param int The increment
        * @param string The value of the HTML name attribute
        * @param string Additional HTML attributes for the <select> tag
        * @param mixed The key that is selected
        * @param string The printf format to be applied to the number
        * @returns string HTML for the select list
        */
        function integerSelectList( $start, $end, $inc, $tag_name, $tag_attribs, $selected, $format="", $freecode="" ) {
                $start         = intval( $start );
                $end         = intval( $end );
                $inc         = intval( $inc );
                $arr         = array();

                for ($i=$start; $i <= $end; $i+=$inc) {
                        $fi = $format ? sprintf( "$format", $i ) : "$i";
                        $arr[] = mosHTML::makeOption( $fi, $fi );
                }

                return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected , $freecode );
        }

        /**
        * Writes a select list of month names based on Language settings
        * @param string The value of the HTML name attribute
        * @param string Additional HTML attributes for the <select> tag
        * @param mixed The key that is selected
        * @returns string HTML for the select list values
        */
        function monthSelectList( $tag_name, $tag_attribs, $selected ) {
                $arr = array(
                        mosHTML::makeOption( '01', _JAN ),
                        mosHTML::makeOption( '02', _FEB ),
                        mosHTML::makeOption( '03', _MAR ),
                        mosHTML::makeOption( '04', _APR ),
                        mosHTML::makeOption( '05', _MAY ),
                        mosHTML::makeOption( '06', _JUN ),
                        mosHTML::makeOption( '07', _JUL ),
                        mosHTML::makeOption( '08', _AUG ),
                        mosHTML::makeOption( '09', _SEP ),
                        mosHTML::makeOption( '10', _OCT ),
                        mosHTML::makeOption( '11', _NOV ),
                        mosHTML::makeOption( '12', _DEC )
                );

                return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
        }

        /**
        * Generates an HTML select list from a tree based query list
        * @param array Source array with id and parent fields
        * @param array The id of the current list item
        * @param array Target array.  May be an empty array.
        * @param array An array of objects
        * @param string The value of the HTML name attribute
        * @param string Additional HTML attributes for the <select> tag
        * @param string The name of the object variable for the option value
        * @param string The name of the object variable for the option text
        * @param mixed The key that is selected
        * @returns string HTML for the select list
        */
        function treeSelectList( &$src_list, $src_id, $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected ) {

                // establish the hierarchy of the menu
                $children = array();
                // first pass - collect children
                foreach ($src_list as $v ) {
                        $pt = $v->parent;
                        $list = @$children[$pt] ? $children[$pt] : array();
                        array_push( $list, $v );
                        $children[$pt] = $list;
                }
                // second pass - get an indent list of the items
                $ilist = mosTreeRecurse( 0, '', array(), $children );

                // assemble menu items to the array
                $this_treename = '';
                foreach ($ilist as $item) {
                        if ($this_treename) {
                                if ($item->id != $src_id && strpos( $item->treename, $this_treename ) === false) {
                                        $tgt_list[] = mosHTML::makeOption( $item->id, $item->treename );
                                }
                        } else {
                                if ($item->id != $src_id) {
                                        $tgt_list[] = mosHTML::makeOption( $item->id, $item->treename );
                                } else {
                                        $this_treename = "$item->treename/";
                                }
                        }
                }
                // build the html select list
                return mosHTML::selectList( $tgt_list, $tag_name, $tag_attribs, $key, $text, $selected );
        }

        /**
        * Writes a yes/no select list
        * @param string The value of the HTML name attribute
        * @param string Additional HTML attributes for the <select> tag
        * @param mixed The key that is selected
        * @returns string HTML for the select list values
        */
        function yesnoSelectList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
                $arr = array(
                mosHTML::makeOption( '0', $no ),
                mosHTML::makeOption( '1', $yes ),
                );

                return mosHTML::selectList( $arr, $tag_name, $tag_attribs, 'value', 'text', $selected );
        }

        /**
        * Generates an HTML radio list
        * @param array An array of objects
        * @param string The value of the HTML name attribute
        * @param string Additional HTML attributes for the <select> tag
        * @param mixed The key that is selected
        * @param string The name of the object variable for the option value
        * @param string The name of the object variable for the option text
        * @returns string HTML for the select list
        */
        function radioList( &$arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text' ) {
                reset( $arr );
                $html = "";
                for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
                        $k = $arr[$i]->$key;
                        $t = $arr[$i]->$text;
                        $id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

                        $extra = '';
                        $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
                        if (is_array( $selected )) {
                                foreach ($selected as $obj) {
                                        $k2 = $obj->$key;
                                        if ($k == $k2) {
                                                $extra .= " selected=\"selected\"";
                                                break;
                                        }
                                }
                        } else {
                                $extra .= ($k == $selected ? " checked=\"checked\"" : '');
                        }
                        $html .= "\n\t<input type=\"radio\" name=\"$tag_name\" id=\"$tag_name$k\" value=\"".$k."\"$extra $tag_attribs />";
                        $html .= "\n\t<label for=\"$tag_name$k\">$t</label>";
                }
                $html .= "\n";
		
                return $html;
        }

        /**
        * Writes a yes/no radio list
        * @param string The value of the HTML name attribute
        * @param string Additional HTML attributes for the <select> tag
        * @param mixed The key that is selected
        * @returns string HTML for the radio list
        */
        function yesnoRadioList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO ) {
                $arr = array(
                mosHTML::makeOption( '0', $no ),
                mosHTML::makeOption( '1', $yes )
                );
		
                return mosHTML::radioList( $arr, $tag_name, $tag_attribs, $selected );
        }

        /**
        * @param int The row index
        * @param int The record id
        * @param boolean
        * @param string The name of the form element
        * @return string
        */
        function idBox( $rowNum, $recId, $checkedOut=false, $name='cid' ) {
                if ( $checkedOut ) {
                        return '';
                } else {
                        return '<input type="checkbox" id="cb'.$rowNum.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);" />';
                }
        }

        function sortIcon( $base_href, $field, $state='none' ) {
                $alts = array(
                        'none'         => _CMN_SORT_NONE,
                        'asc'         => _CMN_SORT_ASC,
                        'desc'         => _CMN_SORT_DESC,
                );
                $next_state = 'asc';
                if ($state == 'asc') {
                        $next_state = 'desc';
                } else if ($state == 'desc') {
                        $next_state = 'none';
                }

                $html = "<a href=\"$base_href&field=$field&order=$next_state\">"
                . "<img src=\"".site_url."/images/M_images/sort_$state.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"{$alts[$next_state]}\" />"
                . "</a>";
                return $html;
        }

        /**
        * Writes Close Button
        */
        function CloseButton ( &$params, $hide_js=NULL ) {
                // displays close button in Pop-up window
                if ( $params->get( 'popup' ) && !$hide_js ) {
                        ?>
			<script language="javascript" type="text/javascript">
			<!--
			document.write('<div align="center" style="margin-top: 30px; margin-bottom: 30px;">');
			document.write('<a href="#" onclick="javascript:window.close();"><span class="small"><?php echo _PROMPT_CLOSE;?></span></a>');
			document.write('</div>');
			//-->
			</script>
                        <?php
                }
        }

        /**
        * Writes Back Button
        */
        function BackButton ( &$params, $hide_js=NULL ) {
                // Back Button
                if ( $params->get( 'back_button' ) && !$params->get( 'popup' ) && !$hide_js) {
                        ?>
                        <div class="back_button">
                        <a href='javascript:history.go(-1)'>
					<?php echo _BACK; ?></a>
                        </div>
                        <?php
                }
        }

        /**
        * Cleans text of all formating and scripting code
        */
        function cleanText ( &$text ) {
                $text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
                $text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
                $text = preg_replace( '/<!--.+?-->/', '', $text );
                $text = preg_replace( '/{.+?}/', '', $text );
                $text = preg_replace( '/&nbsp;/', ' ', $text );
                $text = preg_replace( '/&amp;/', ' ', $text );
                $text = preg_replace( '/&quot;/', ' ', $text );
                $text = strip_tags( $text );
                $text = htmlspecialchars( $text );
		
                return $text;
        }

        /**
        * Writes Print icon
        */
        function PrintIcon( &$row, &$params, $hide_js, $link, $status=NULL ) {
                if ( $params->get( 'print' )  && !$hide_js ) {
                        // use default settings if none declared
                        if ( !$status ) {
                                $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
                        }

                        // checks template image directory for image, if non found default are loaded
                        if ( $params->get( 'icons' ) ) {
                                $image = mosAdminMenus::ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, _CMN_PRINT, _CMN_PRINT );
                        } else {
                                $image = _ICON_SEP .'&nbsp;'. _CMN_PRINT. '&nbsp;'. _ICON_SEP;
                        }

                        if ( $params->get( 'popup' ) && !$hide_js ) {
                                // Print Preview button - used when viewing page
                                ?>
				<script language="javascript" type="text/javascript">
				<!--
				document.write('<td align="right" width="100%" class="buttonheading">');
				document.write('<a href="#" onclick="javascript:window.print(); return false;" title="<?php echo _CMN_PRINT;?>">');
				document.write('<?php echo $image;?>');
				document.write('</a>');
				document.write('</td>');
				//-->
				</script>				
                                <?php
                        } else {
                                // Print Button - used in pop-up window
                                ?>
                                <td align="right" width="100%" class="buttonheading">
					<a href="<?php echo $link; ?>" target="_blank" onclick="window.open('<?php echo $link; ?>','win2','<?php echo $status; ?>'); return false;" title="<?php echo _CMN_PRINT;?>">
						<?php echo $image;?></a>
                                </td>
                                <?php
                        }
                }
        }

        /**
        * simple Javascript Cloaking
        * email cloacking
         * by default replaces an email with a mailto link with email cloacked
        */
        function emailCloaking( $mail, $mailto=1, $text='', $email=1 ) {
                // convert text
                $mail                 = mosHTML::encoding_converter( $mail );
                // split email by @ symbol
                $mail                = explode( '@', $mail );
                $mail_parts        = explode( '.', $mail[1] );
                // random number
                $rand        = rand( 1, 100000 );

		$replacement 	= "\n <script language='JavaScript' type='text/javascript'>";
		$replacement 	.= "\n <!--";
		$replacement 	.= "\n var prefix = '&#109;a' + 'i&#108;' + '&#116;o';";
		$replacement 	.= "\n var path = 'hr' + 'ef' + '=';";
		$replacement 	.= "\n var addy". $rand ." = '". @$mail[0] ."' + '&#64;';";
		$replacement 	.= "\n addy". $rand ." = addy". $rand ." + '". implode( "' + '&#46;' + '", $mail_parts ) ."';";
		
                if ( $mailto ) {
                        // special handling when mail text is different from mail addy
                        if ( $text ) {
                                if ( $email ) {
                                        // convert text
                                        $text         = mosHTML::encoding_converter( $text );
                                        // split email by @ symbol
                                        $text         = explode( '@', $text );
                                        $text_parts        = explode( '.', $text[1] );
					$replacement 	.= "\n var addy_text". $rand ." = '". @$text[0] ."' + '&#64;' + '". implode( "' + '&#46;' + '", @$text_parts ) ."';";
                                } else {
					$replacement 	.= "\n var addy_text". $rand ." = '". $text ."';";
                                }
				$replacement 	.= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' );";
				$replacement 	.= "\n document.write( addy_text". $rand ." );";
				$replacement 	.= "\n document.write( '<\/a>' );";
                        } else {
				$replacement 	.= "\n document.write( '<a ' + path + '\'' + prefix + ':' + addy". $rand ." + '\'>' );";
				$replacement 	.= "\n document.write( addy". $rand ." );";
				$replacement 	.= "\n document.write( '<\/a>' );";
                        }
                } else {
			$replacement 	.= "\n document.write( addy". $rand ." );";
                }
		$replacement 	.= "\n //-->";
		$replacement 	.= '\n </script>';
		
		// XHTML compliance `No Javascript` text handling
		$replacement 	.= "<script language='JavaScript' type='text/javascript'>";
		$replacement 	.= "\n <!--";
		$replacement 	.= "\n document.write( '<span style=\'display: none;\'>' );";
		$replacement 	.= "\n //-->";
		$replacement 	.= "\n </script>";
                $replacement         .= _CLOAKING;
		$replacement 	.= "\n <script language='JavaScript' type='text/javascript'>";
		$replacement 	.= "\n <!--";
		$replacement 	.= "\n document.write( '</' );";
		$replacement 	.= "\n document.write( 'span>' );";
		$replacement 	.= "\n //-->";
		$replacement 	.= "\n </script>";

                return $replacement;
        }

        function encoding_converter( $text ) {
                // replace vowels with character encoding
                $text         = str_replace( 'a', '&#97;', $text );
                $text         = str_replace( 'e', '&#101;', $text );
                $text         = str_replace( 'i', '&#105;', $text );
                $text         = str_replace( 'o', '&#111;', $text );
                $text        = str_replace( 'u', '&#117;', $text );

                return $text;
        }
}

?>