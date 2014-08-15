<?php
/**
 * Description of filter
 *
 * @author dmitry
 */
class filter {
            private $level;
            private $advanced;
            function  __construct( $level ) {
                $this->level = 0;
                $this->advanced = false;
                if($level == "MIN") $this->level = 1;
                if($level == "MAX") $this->level = 2;
                if (  version_compare(phpversion(), '5.2.0', '>=') == true  )  $this->advanced = true;
            }

            function go($input){
				$ret_array = array();
				foreach($input as $key=>$value){
					if (  is_array($value)  ) {  $value = $this->go ($value);  }
					else{
						//Средний
						if(  $this->level == 1  ) {
							$value = safelySqlStr(  stripslashes($value)  );
							//$value = escape_quotes($value);
						 }
						 //Максимальный
						 else if(  $this->level == 2  ){ // ggtr01 ($value);// ggtr ($value);
							$value = safelySqlStr(  stripslashes($value)  );
						 	$value = strip_tags($value); 
							$value = filter_var($value, FILTER_SANITIZE_STRING); 
						 }
					}
					$ret_array[$key] = $value;
				}
			return $ret_array;
		}  
}
?>
