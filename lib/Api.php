<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

class Api {
    public static $user = null;
    public static $request;
        
    public static function init(){        
        self::$user = self::loadUser();        
        self::$request = new Request();
    }
    
    public static function loadUser() {
        global $my, $mainframe, $database;
        
        $mainframe = new mosMainFrame($database, '', '.');
        $mainframe->initSession();
        $my = $mainframe->getUser();

        $guest = (object) array(
            'id' => 0,
            'gid' => 0,
            'group_name' => 'guest',
            'username' => 'guest',
            'guest' => true,
            'admin' => false,
            'superadmin' => false,
            'developer' => false,
        );
        
        if ($my->id == 0)
            $user = $guest;
        else {
            $user = new stdClass();
            foreach($my as $key => $value){
                if(mb_substr($key, 0, 1) == '_')
                    continue;
                if($key == 'usertype')
                    $key = 'group_name';
                $user->$key = $value;
            }
        
            $user->guest = false;
            $user->developer = in_array($user->id, array());
            $user->admin = $user->gid >= 24 || $user->developer;
            $user->superadmin = $user->gid == 25 || $user->developer;
            if ($user->developer)
                $user->group_name = 'admin';
        }

        return $user;
    }
    
    
}