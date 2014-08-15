<?php
defined('_VALID_INSITE') or die( 'Доступ запрещен' );

class messager {
    private $model;
    private $user_data = array();
    private $sender = 0;
    private $sql = '';
    public  $total = 0;
    
    public function __construct($type = 'inbox', $params = array()){
       $user_id = Api::$user->id;
        
        $this->model = '#__messages';
        if($user_id > 0){
            $this->sender = $user_id;
            $this->user_data = ggo($user_id, '#__users');
               
            # view:
            #    all - все сообщения
            #    new - только не прочитанные      
            $default = array('view' => 'all');
            $setting = array_merge($default, $params);
            # inbox - входящие
            # outbox - исходящие
            switch($type){
                case 'outbox':
                    $type = 0;
                    $field = 'sender';
                    break;
                default:
                    $type = 1;
                    $field = 'recipient';
            }
            $cond = $setting['view'] == 'new' ? ' AND `status` = 0 ' : '';
            $sql = sprintf('SELECT COUNT(id) FROM %s WHERE `%s` = %d AND `type` = %d AND `deleted` = 0' . $cond, $this->model, $field, $this->sender, $type);
            $this->total = ggsqlr($sql);
            $this->sql   = str_replace('COUNT(id)', '*', $sql);
            $this->sql  .= ' ORDER BY `time` DESC ';
        }
    }
    
    # Получаем список
    public function get_list($offset = 0, $limit = 0){
        if($this->total == 0)
            return false;
        $messages = ggsql($this->sql, $offset, $limit);
        return $messages;      
    }
    
    # Отправляем   
    public function send($recipient, $message, $subject = ''){
        global $database;
        
        $recipient = intval($recipient);
        if($this->sender == 0 || $recipient <= 0 || !count($this->user_data))
            return false;
                
        $sender_login = $this->user_data->username;
        
        # формируем как исходящее сообщение для отправителя
        $msg = new mosDBTable($this->model, 'id', $database);
        $msg->id = 0;
        $msg->sender = $this->sender;
        $msg->recipient = $recipient;
        $msg->subject = !empty($subject) ? $subject : "Новое сообщение от {$sender_login}";
        $msg->message = $message;
        $msg->time = time();   
        $msg->store();
               
        # формируем как входящее сообщение для получателя
        $msg->parent = $msg->id;
        $msg->type = 1;
        $msg->id = 0;           
        $msg->store();
        
        return true;        
    }
    
    # Получаем по id
    public function get($id){
        if($id != intval($id))
            return false;
        $sql = sprintf('SELECT * FROM %s WHERE `id` = %d AND `deleted` = 0', $this->model, $id);
        $message = ggsql($sql);
        return count($message) ? $message[0] : false;
    }
    
    # Удаляем
    public function delete($id){
        if($id != intval($id))
            return false;
        $message = ggo($id, $this->model);
        if($message){
            $sql = sprintf("UPDATE %s SET `deleted` = 1 WHERE `id` = %d", $this->model, $message->id);
            ggsqlq($sql);
            return true;
        }
        return false;
    }
    
    # Говорим что прочитано
    public function read($id){
        if($id != intval($id))
            return false;
        $message = ggo($id, $this->model);
        if($message){
            $sql = sprintf('UPDATE %s SET `status` = 1 WHERE `id` IN (%d, %d)', $this->model, $message->id, $message->parent);
            ggsqlq($sql);
            return true;
        }
        return false;
    }
    
}