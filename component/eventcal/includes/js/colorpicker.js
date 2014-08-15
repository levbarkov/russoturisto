var offsetX;
var offsetY;

	function savePosition(mouseclick) {
		offsetX = mouseclick.pageX;
		offsetY = mouseclick.pageY;
	}

	document.onmousedown = savePosition;

	function previewColor(color) {
		var preview = document.getElementById('ColorPreview');
		preview.style.backgroundColor = '#' + color;
	}

	function sendColor(color,eingabefeld) {
		var formfield = document.getElementById(eingabefeld);
		var hiddenfield = eingabefeld.substr(0,eingabefeld.indexOf("display"));
		hiddenfield = document.getElementById(hiddenfield);
		if (color.charAt(0) != '#') color = '#' + color;
		formfield.value = color;
		hiddenfield.value = color;
		formfield.style.backgroundColor = color;
		var ColorPickerDiv = document.getElementById('colorPicker');
		ColorPickerDiv.style.visibility = 'hidden';
	}

	function writeColorTable(eingabefeld) {
		var hexCode = new Array("0","3","6","9","C","F");
		var SourceCode = '<table cellspacing="0" cellpadding="1" border="0" style="background-color:#FFFFFF;">';
		
		SourceCode = SourceCode + '<tr><td colspan="18" align="right">';
		SourceCode = SourceCode + '<table cellpadding="0" cellspacing="0" style="font-family:Arial;font-size:6pt;background-color:#CCCCCC;"><tr>';
		SourceCode = SourceCode + '<td colspan="2" align="center" style="font-family:Arial;font-size:6pt;border:solid 1px #000000;width:9px;cursor:pointer;" onMouseOut="this.style.backgroundColor = \'#CCCCCC\'" onMouseOver="this.style.backgroundColor = \'#AAAAAA\'" onClick="hideDiv()">X</td>';
		SourceCode = SourceCode + '</tr></table></td></tr>';
		
		var counter1 = 0;
		var counter2 = 0;
		var counter3 = 0;
		
		for(var o=0; o<=11; o++) {
			SourceCode = SourceCode + '<tr>';
			for(var i=0; i<=17; i++) {
				var color = hexCode[counter1] + hexCode[counter1] + hexCode[counter2] + hexCode[counter2] + hexCode[counter3] + hexCode[counter3];
				SourceCode = SourceCode + '<td id="' + color + '" onMouseOver="previewColor(\'' + color + '\')" onClick="sendColor(\'' + color + '\',\'' + eingabefeld + '\')" style="background-color:#' + color + ';width:8px;height:8px;"></td>';
				counter1++;
				if(counter1 > 5) {
					counter2++;
					counter1 = 0;
				}
				if(counter2 > 5) {
					counter3++;
					counter2 = 0;
				}
				if(counter3 > 5) {
					counter3 = 15
				}
			}
			SourceCode = SourceCode + '</tr>'
		}
		var colorPicker = document.getElementById('colorPicker');
		SourceCode = SourceCode + '<tr><td colspan="18" id="ColorPreview" style="border:solid 1px #000000;font-size:9pt;">&nbsp;</td></tr></table>';
		return SourceCode;
	}

	function ColorPicker(eingabefeld,checkbox) {
		var el = document.getElementById(eingabefeld);
		var x = el.offsetLeft;
		var y = el.offsetTop;
		var Div = document.getElementById('colorPicker');
		Div.innerHTML = writeColorTable(eingabefeld);
		if(!window.event) {
			Div.style.left = (offsetX-134) + 'px';
			Div.style.top = (offsetY-6) + 'px';
		}
		else {
			Div.style.left = window.event.offsetX;
		}
		Div.style.visibility = 'visible';
		document.getElementById(checkbox).checked = true;
	}

	function hideDiv() {
		var ColorPickerDiv = document.getElementById('colorPicker');
		ColorPickerDiv.style.visibility = 'hidden';
	}