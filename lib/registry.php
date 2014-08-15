<?php
defined( '_VALID_INSITE' ) or die('Доступ запрещен');

class registry implements ArrayAccess {
	private $vars = array();
	
	public function __construct(){
		global $iseoname;
		$this['surl'] = site_url;
		$this['iseoname'] = $iseoname;
	}
	
	public function load_all_config(){
		$conf_list = ggsql ("select name, val from #__config");
		foreach($conf_list as $conf)
			$this->vars[$conf->name] = $conf->val;
	}
		
	public function set($key, $var){
		if (isset($this->vars[$key]) == true) {
			throw new Exception("Unable to set var `{$key}`. Already set.");
		}
        $this->vars[$key] = $var;
	    return true;
	}

	public function get($key) {
		if (isset($this->vars[$key]) == false) {				
			if($this->vars['db'] == false)
				return false;
			else {						 
				$this->vars['db']->setQuery("select val from #__config where name = '{$key}'");
				$val = $this->vars['db']->loadResult();
				if ($val == null)
					return false;
				else
					$this->set($key, $val);
			}
		}
		return $this->vars[$key];
	}

	public function remove($key){
	    unset($this->vars[$key]);
	}

	public function offsetExists($offset){
	    return isset($this->vars[$offset]);
	}

	public function offsetGet($offset) {
	    return $this->get($offset);
	}

	public function offsetSet($offset, $value) {
	    $this->set($offset, $value);
	}

	public function offsetUnset($offset) {
	    unset($this->vars[$offset]);
	}
	
	public function degub($reg_var, $txt){	
		if ($this[$reg_var] == 1)
			return $txt;
	}
}
