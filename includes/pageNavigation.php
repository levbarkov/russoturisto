<?php
// запрет прямого доступа
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

/**
* Page navigation support class
*/
class mosPageNav {
	/** @var int The record number to start dislpaying from */
	var $limitstart = null;
	/** @var int Number of rows to display per page */
	var $limit 		= null;
	/** @var int Total number of rows */
	var $total 		= null;
	var $sign 		= array();	//	МАССИВ В КОТОРЫЙ ПОМЕЩАЕМ ДОПОЛНИТЕЛЬНЫЕ ПАРАМЕТРЫ ДЛЯ ФОРМИРОВАНИЯ ССЫЛОК НАВИГАЦИИ

	function mosPageNav( $total, $limitstart, $limit ) {
                if (  $limit==0  ){ // отображать навигатор по страницам не нужно
                    $this->total 		= (int) $total;
                    $this->limitstart 	=         0;
                    $this->limit 		= 0;
                    return;
                }
		$this->total 		= (int) $total;
		$this->limitstart 	= (int) max( $limitstart, 0 );
		$this->limit 		= (int) max( $limit, 1 );
		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}
		if (($this->limit-1)*$this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}
	}
	/**
	* @return string The html for the limit # input box
	*/
	function getLimitBox () {
		//$limits = array();
		//for ($i=5; $i <= 30; $i+=5) {
	//		$limits[] = mosHTML::makeOption( "$i" );
//		}
		//$limits[] = mosHTML::makeOption( "50" );

		// build the html select list
		//$html = mosHTML::selectList( $limits, 'limit', 'class="inputbox" size="1" onchange="document.adminForm.submit();"','value', 'text', $this->limit );
		
		return $html;
	}
	/**
	* Writes the html limit # input box
	*/
	function writeLimitBox () {
		echo mosPageNav::getLimitBox();
	}
	function writePagesCounter() {
		echo $this->getPagesCounter();
	}
	/**
	* @return string The html for the pages counter, eg, Results 1-10 of x
	*/
	function getPagesCounter() {
		$html = '';
		$from_result = $this->limitstart+1;
		if ($this->limitstart + $this->limit < $this->total) {
			$to_result = $this->limitstart + $this->limit;
		} else {
			$to_result = $this->total;
		}
		if ($this->total > 0) {
			$html .= "Показано " . $from_result . " - " . $to_result . " из " . $this->total;
		} else {
			$html .= "Записи не найдены.";
		}
		return $html;
	}
	/**
	* Writes the html for the pages counter, eg, Results 1-10 of x
	*/
	function writePagesLinks() {
		echo $this->getPagesLinks();
	}
	/**
	* @return string The html links for pages, eg, previous, next, 1 2 3 ... x
	*/
	function getPagesLinks() {
		$html 			= '';
		$displayed_pages 	= 5;
		$total_pages 		= ceil( $this->total / $this->limit );
		$this_page 			= ceil( ($this->limitstart+1) / $this->limit );
		if($total_pages > 5 ){
			$start_loop = $this_page < 3 ? 1 : $this_page - 2;
                        
			if($this_page == $total_pages || $this_page == ($total_pages - 1))
                $start_loop = $total_pages - 4;
                
		}
		else
            $start_loop = 1;
        
		$stop_loop = ($start_loop + $displayed_pages - 1 < $total_pages) ? $start_loop + $displayed_pages - 1 : $total_pages;
        
		$html .= '<td class="left">';
		
        $link_postfix = $this->get_link_additional();

		if ($this_page > 1) {
			$page = ($this_page - 2) * $this->limit;
			$pp = $this_page-1;
			$printpage = "?page=".$pp;
			$html .= "<a class='prev_arrow' href={$printpage}{$link_postfix}>&larr;</a> <a class='prev' href='{$printpage}{$link_postfix}'>Предыдущая</a>";
		} else {
			$html .= "<span class='prev_noactive_arrow'>&larr;</span> <span class='prev_noactive'>Предыдущая</span>";
		}
		if ($this_page < $total_pages) {
			$page = $this_page * $this->limit;
			$pp = $this_page + 1;
			$printpage =  "?page={$pp}";
			$end_page = ($total_pages-1) * $this->limit;
			$html .= " <a class='next' href='{$printpage}{$link_postfix}'>Следующая</a> <a class='next_arrow' href='{$printpage}{$link_postfix}' class='no-line'>&rarr;</a";
		} else {
			$html .= " <span class='next_noactive'>Следующая</span> <span class='next_noactive_arrow'>&rarr;</span";
		}
        
		$html.='</td><td class="right"><span class="pages">Страницы:</span>';
        
		if ($start_loop > 1){
			$html .= " <a class='page' href='?page=1{$link_postfix}'>1</a>";
			$html .= " <span class='page_separator'>...</span> ";
		}
        
		for ($i = $start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->limit;
			if ($i == $this_page) {
				$html .= " <span class='page_noactive'>{$i}</span> ";
			} else {
				$html .= " <a class='page' href='?page={$i}{$link_postfix}'>{$i}</a>";
			}
		}
        
		if ($stop_loop < $total_pages){
			$html .= " <span class='page_separator'>...</span> ";
			$html .= " <a class='page' href='?page={$total_pages}{$link_postfix}'>{$total_pages}</a>";
		}
        
		$html .= '</td>';
        
		return $html;
	}

	function get_link_additional(){
		if (  count($this->sign)==0  ) return "";
		$ret_link = "";
		foreach ( $this->sign as $sign_name => $sign_val ) {
                        if (  $sign_val!=''  ) $ret_link .= "&".$sign_name."=".$sign_val;
		}
		return $ret_link;
	}
	function getListFooter($sign="") {
                if (  $this->limit==0  ) return;  // показываем все товары
		if (  $this->limitstart==0  and  $this->total<=$this->limit  ) return "";
		$html = '<table width=100%  class="pager"><tr>';
		$html .= $this->getPagesLinks($sign);
		$html .= '</tr></table>';
  		return $html;
	}
/**
* @param int The row index
* @return int
*/
	function rowNumber( $i ) {
		return $i + 1 + $this->limitstart;
	}
/**
* @param int The row index
* @param string The task to fire
* @param string The alt text for the icon
* @return string
*/
	function orderUpIcon( $i, $condition=true, $task='orderup', $alt='Передвинуть выше',  $formName='adminForm') {
		if (($i > 0 || ($i+$this->limitstart > 0)) && $condition) {
			return '<a href="#reorder" onClick="return listItemTask(\'cb'.$i.'\',\''.$task.'\',\''.$formName.'\')"><img src="images/uparrow.png" width="12" height="12" border="0"></a>';
  		} else {
  			return '&nbsp;';
		}
	}
/**
* @param int The row index
* @param int The number of items in the list
* @param string The task to fire
* @param string The alt text for the icon
* @return string
*/
	function orderDownIcon( $i, $n, $condition=true, $task='orderdown', $alt='Передвинуть ниже',  $formName='adminForm' ) {
		if (($i < $n-1 || $i+$this->limitstart < $this->total-1) && $condition) {
			return '<a href="#reorder" onClick="return listItemTask(\'cb'.$i.'\',\''.$task.'\',\''.$formName.'\')"><img src="images/downarrow.png" width="12" height="12" border="0"></a>';
  		} else {
  			return '&nbsp;';
		}
	}

	/**
	 * @param int The row index
	 * @param string The task to fire
	 * @param string The alt text for the icon
	 * @return string
	 */
	function orderUpIcon2( $id, $order, $condition=true, $task='orderup', $alt='#' ) {
		// handling of default value
		if ($alt = '#') {
			$alt = 'Переместить выше';
		}
		if ($order == 0) {
			$img = 'uparrow0.png';
			$show = true;
		} else if ($order < 0) {
			$img = 'uparrow-1.png';
			$show = true;
		} else {
			$img = 'uparrow.png';
			$show = true;
		};
		if ($show) {
			$output = '<a href="#ordering" onClick="listItemTask(\'cb'.$id.'\',\'orderup\')" title="'. $alt .'">';
			$output .= '<img src="images/' . $img . '" width="12" height="12" border="0" alt="'. $alt .'" title="'. $alt .'" /></a>';
			return $output;
   		} else {
  			return '&nbsp;';
		}
	}

	/**
	 * @param int The row index
	 * @param int The number of items in the list
	 * @param string The task to fire
	 * @param string The alt text for the icon
	 * @return string
	 */
	function orderDownIcon2( $id, $order, $condition=true, $task='orderdown', $alt='#' ) {
		// handling of default value
		if ($alt = '#') {
			$alt = 'Переместить ниже';
		}
		if ($order == 0) {
			$img = 'downarrow0.png';
			$show = true;
		} else if ($order < 0) {
			$img = 'downarrow-1.png';
			$show = true;
		} else {
			$img = 'downarrow.png';
			$show = true;
		};
		if ($show) {
			$output = '<a href="#ordering" onClick="listItemTask(\'cb'.$id.'\',\'orderdown\')" title="'. $alt .'">';
			$output .= '<img src="images/' . $img . '" width="12" height="12" border="0" alt="'. $alt .'" title="'. $alt .'" /></a>';
			return $output;
  		} else {
  			return '&nbsp;';
		}
	}
	/**
	 * Sets the vars for the page navigation template
	 */
	function setTemplateVars( &$tmpl, $name = 'admin-list-footer' ) {
		$tmpl->addVar( $name, 'PAGE_LINKS', $this->getPagesLinks() );
		$tmpl->addVar( $name, 'PAGE_LIST_OPTIONS', $this->getLimitBox() );
		$tmpl->addVar( $name, 'PAGE_COUNTER', $this->getPagesCounter() );
	}
}
?>