<?php
function cut_tr(&$data_rows){
	//отсекаем до <tr>
		$str_pos = '<tr';
			$pos = strpos($data_rows, $str_pos);
			$data_rows = substr($data_rows,  ($pos+strlen($str_pos))  );
//		ggt ($data_rows);
		$str_pos2 = ">";
			$pos2 = strpos($data_rows, $str_pos2);
			$data_rows = substr($data_rows,  ($pos2+strlen($str_pos2))  );
//		ggt ($data_rows);

	//отсекаем все до </tr>  сохраняем результирующуя строку
	$str_pos = '</tr>';
	$pos = strpos($data_rows, $str_pos); 
		$tr_string = substr($data_rows, 0,$pos);
		$data_rows = substr($data_rows, $pos+strlen($str_pos));
	
//	ggt ($data_rows);
	return $tr_string;
	
}
function cut_td(&$data_rows){
	//отсекаем до <tr>
		$str_pos = '<td';
			$pos = strpos($data_rows, $str_pos);
			$data_rows = substr($data_rows,  ($pos+strlen($str_pos))  );
//		ggt ($data_rows);
		$str_pos2 = ">";
			$pos2 = strpos($data_rows, $str_pos2);
			$data_rows = substr($data_rows,  ($pos2+strlen($str_pos2))  );
//		ggt ($data_rows);

	//отсекаем все до </tr>  сохраняем результирующуя строку
	$str_pos = '</td>';
	$pos = strpos($data_rows, $str_pos); 
		$td_string = substr($data_rows, 0,$pos);
		$data_rows = substr($data_rows, $pos+strlen($str_pos));
	
//	ggt ($data_rows);
	return $td_string;
	
}
function parse_tr ($data_rows){
	if (  strpos($data_rows,"<td")===false  ) return 0;
	$tr_data = array();
	$td_i = 0;
	while (1){
		$td_data = cut_td($data_rows);
		$tr_data[$td_i] = $td_data;
		$td_i++;
		if (  strpos($data_rows,"<td")===false  )  break; 
	}
	return $tr_data;
}


function parse_table ($data_rows, &$lots){
	$data_rows = str_replace( "<TR", "<tr", $data_rows );
	$data_rows = str_replace( "</TR", "</tr", $data_rows );

	$data_rows = str_replace( "<TD", "<td", $data_rows );
	$data_rows = str_replace( "</TD", "</td", $data_rows );
	$data_rows = str_replace( "<th", "<td", $data_rows );
	$data_rows = str_replace( "</th", "</td", $data_rows );
	$data_rows = str_replace( "<TH", "<td", $data_rows );
	$data_rows = str_replace( "</TH", "</td", $data_rows );
	
	if (  strpos($data_rows,"<tr")===false  ) return 0;
	while (1){
		$tr_data = cut_tr($data_rows);
		$lots[] = parse_tr($tr_data);
		if (  strpos($data_rows,"<tr")===false  )  break; 
	}
	return ;
}

function parse_table_requrse ($data_rows, &$lots){
	$data_rows = str_replace( "<TR", "<tr", $data_rows );
	$data_rows = str_replace( "</TR", "</tr", $data_rows );

	$data_rows = str_replace( "<TD", "<td", $data_rows );
	$data_rows = str_replace( "</TD", "</td", $data_rows );
	$data_rows = str_replace( "<th", "<td", $data_rows );
	$data_rows = str_replace( "</th", "</td", $data_rows );
	$data_rows = str_replace( "<TH", "<td", $data_rows );
	$data_rows = str_replace( "</TH", "</td", $data_rows );
	
	if (  strpos($data_rows,"<tr")===false  ) return 0;
	while (1){
		$tr_data = cut_tr($data_rows);
		$lots[] = parse_tr($tr_data);
		if (  strpos($data_rows,"<tr")===false  )  break; 
	}
	return ;
}

function out_element ($data_rows, $str_pos, $str_pos2){
	if (  strcmp($str_pos,"")==0  ) $pos = 0;
	else {
		$pos = strpos($data_rows, $str_pos);
		if (  $pos === false  ) $pos=0;
	}
	if (  strcmp($str_pos2,"")==0  ) $pos = 0;
	else {
		$pos2_x = strpos($data_rows, $str_pos2);
		if (  $pos2_x === false  ) $pos2=0;
		else $pos2=$pos2_x+1;
	}
	
	$data_element = substr($data_rows, 0, $pos).substr($data_rows, $pos2);
	return $data_element;
}



function cut_element ($data_rows, $str_pos, $str_pos2){
//	ggtr ($data_rows,2);
//	ggtr ($str_pos,2);
	if (  strcmp($str_pos,"")==0  ) $pos = 0;
	else  $pos = strpos($data_rows, $str_pos); 
	
	if ($pos === false) $data1 = substr($data_rows, 0);
	else $data1 = substr($data_rows, $pos+strlen($str_pos));
//	ggtr ($data1,2);
	
	if (  strcmp($str_pos2,"")==0  ) $pos2 = strlen($data1);
	else {
		$pos2 = strpos($data1, $str_pos2);
		if ($pos2 === false) $pos2 = strlen($data1);
	}
	$data_element = substr($data1, 0, $pos2);
//	ggtr ($data_element,2);
	return $data_element;
}
?>