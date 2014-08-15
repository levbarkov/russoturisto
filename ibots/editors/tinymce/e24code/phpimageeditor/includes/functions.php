<?php
    
	
    /*
    Copyright 2008, 2009, 2010 Patrik Hultgren
    
    YOUR PROJECT MUST ALSO BE OPEN SOURCE IN ORDER TO USE PHP IMAGE EDITOR.
    OR ELSE YOU NEED TO BUY THE COMMERCIAL VERSION AT:
    http://www.shareit.com/product.html?productid=300296445&backlink=http%3A%2F%2Fwww.phpimageeditor.se%2F
    
    This file is part of PHP Image Editor.

    PHP Image Editor is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PHP Image Editor is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PHP Image Editor.  If not, see <http://www.gnu.org/licenses/>.
    */


    function PIE_GetTexts($filePath)
	{
		$texts = array();
		$lines = file($filePath);
		
		foreach($lines as $line_num => $line)
		{
			if (substr_count($line, "#") == 0)
			{
				$keyAndText = explode("=", trim($line));
				$texts[$keyAndText[0]] = $keyAndText[1];
			}
		}
		
		return $texts;
	}
	
	function PIE_Echo($text)
	{
		echo $text;
		//echo utf8_encode($text);
	}	
?>