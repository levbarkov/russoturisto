<?php
/*
 * ОБРАБОТКА ОТПРАВЛЕННЫХ КОММЕНТАРИЕВ и ajax-форма для добавления комментария - компонент write_comment_ajax
 */

/*
 * ПРИМЕРЫ
 *
$com = new comments("ex", $database, $reg);

$params = Array(
	"parent" => "20",
	"name" => "tester",
	"mail" => "mail@mail.ru", 
	"text" => "<div>'DROP TABLE'</div> \"BOLT\"",
	"ip" => $_SERVER['REMOTE_ADDR']
);

//print $com->set($params);
//$res = $com->getChildren(18);
$res = $com->get(16, 2, 4);
//2 - limitstart
//4 - limit
//$user = $com->user(2851);
*/


/**
 * Класс для работы с комментариями
 *
 * @author dmitry
 */
/**
construct ( component,  database,  registry,  moderation = 0 )
--> moderOn( id ) : обозначает комментарий как провереный
--> moderOff( id ) : -- " -- не провереный
--> delete( id ) : удаляет комментарии и вложенные комментарии для элемента id
--> set( -params) : задает комментарий
	    params { -parent => int родительская запись, -uid => int пользователь, -name => string имя пользователя
			   -mail => string почтовый адрес, -ip => адрес пользователя }

-->get( id, limitstart, limit ) : возвращает древовидный массив объектов комментариев

*/
class comments
{
	var $say = array(
		'say_comment' => array	(
								'Many' => "Комментарии",
								'many' => "комментарии",
								'One'  => "Комментарий",
								'one'  => "комментарий",
								'Write'=> "Написать",
								'MessageText'=> "Комментарий",
								'Thank'=> "<br /><strong>Спасибо.</strong><br />Ваш комментарий сохранен,<br />для просмотра обновите страницу."
								//'Thank'=> "<br /><strong>Спасибо.</strong><br />После проверки Ваш комментарий будет опубликован."
								),
		'say_review'  => array	(
								'Many' => "Отзывы",
								'many' => "отзывы",
								'One'  => "Отзыв",
								'one'  => "отзыв",
								'Write'=> "Написать",
								'MessageText'=> "Отзыв",
								'Thank'=> "<br /><strong>Спасибо.</strong><br />Ваш отзыв сохранен,<br />для просмотра обновите страницу."
								//'Thank'=> "<br /><strong>Спасибо.</strong><br />После проверки Ваш отзыв будет опубликован."
								),
		'say_question' => array	(
								'Many' => "Вопросы по заказу",
								'many' => "вопросы по заказу",
								'One'  => "Вопрос менеджеру",
								'one'  => "вопрос менеджеру",
								'Write'=> "Задать",
								'MessageText'=> "Вопрос",
								'Thank'=> "<br /><strong>Спасибо.</strong><br />В ближайшее время Вам ответят."
								),
		'say_answer' => array	(
								'Many' => "Ответы",
								'many' => "ответы",
								'One'  => "Ответ",
								'one'  => "ответ",
								'Write'=> "Написать",
								'MessageText'=> "Текст ответа",
								'Thank'=> "<br /><strong>Спасибо.</strong><br />Ответ отправлен."
								)

	);
	var $load_parent=0;
	var $can_answer=1; // можем отвечать
	var $type="comment";
    private $vars;

    function __get($val)
    {
        if(isset($this->vars[$val])) return $this->vars[$val];

        return false;
    }

    function __set($key, $val)
    {
        $this->vars[$key] = $val;
    }

    function  __construct($component, database $db, registry $reg, $moderation = 0) {
        $this->component = $component;
		$this->db = $db;
        $this->reg = $reg;
		$this->type = $component;

		$this->premoderation = $moderation;
    }
	function load_comment($id){
		$mycomment = ggo ($id, "#__comments");
		if ( $mycomment->id  )return $mycomment; 
		else return false;
	}
    /* устанавливает значение проверки для комментария */
    private function setMod($id, $val)
    {        
        $this->db->setQuery("UPDATE #__comments SET moderate = ".$val." WHERE id = ".$id);
        $this->db->query();
        return $this->db->getAffectedRows();
    }

    /** ставит значение проверки 1 */
    public function moderOn($id)
    {
        if($id != intval($id)) return false;
        return $this->setMod($id, 1);
    }

    /** ставит значение проверки 0 */
    public function moderOff($id)
    {
        if($id != intval($id)) return false;
        return $this->setMod($id,0);
    }

    /** Поиск всех потомков, возвращает массив id*/
    public function getChildren($id, $res = 'array', $type = 'comment', $limitstart = 0, $limit = 0)
    {
        if($id != intval($id)) return false;
        
        $result = array(); //Массив с выходными значениями
	
	$fields = "id"; //Выборка по полям
	if($res == 'object') $fields = "*";
	
	$query = "SELECT ".$fields." FROM #__comments WHERE parent = ".$id." AND type = '".$type."' AND `publish`=1 ORDER BY id";
	if($type != 'comment')
	{
	      if($limit != 0) $query .= " limit ".$limitstart.", ".$limit;
	}	
        $this->db->setQuery($query);
        $this->db->query();
        if($this->db->getNumRows() > 0) 
        {
              if($res == 'array') 
	      {
		    $result = $this->db->loadResultArray();
		    $tmp = $result;
		    foreach($tmp as $l)
		    {
			$list = $this->getChildren($l, $res);
			if($list != false) $result = array_merge($result, $list);
		    }
		    return $result;
	      }

	      if($res == 'object')
	      {
		    $result = $this->db->loadObjectList();
		    foreach($result as $l)
		    {
			  if($l->userid != 0) $l->user = $this->user($l->userid);
			  $list = $this->getChildren($l->id,$res);
			  if($list != false) $l->children = $list;
		    }
		    return $result;
	      }
	}
	return false;
    }


    /** Удаляет все комментарий и вложенные для родителя parent по типу $this->type */
    public function deleteAllComments($parent){
		$all_coomments = ggsql (  "select * from #__comments where parent='$parent' and type='".$this->type."'"  );
		foreach ( $all_coomments as $all_coomment1)	$this->delete($all_coomment1->id);
	}

    /** Удаляет комментарий и вложенные */
    public function delete($id)
    {
        if($id != intval($id)) return false;
        if($this->component == "") return false;

        $children = $this->getChildren($id);
		if (   !is_array($children)  )  $children = array();
        array_push($children, $id);
        $str = join(", ", $children);
        $str = "( ".$str." )";
        $this->db->setQuery("DELETE FROM #__comments WHERE id IN ".$str);
        $this->db->query();
        return $this->db->getAffectedRows();
    }
	
    public function del_for_type($parent){
		$allcoms = ggsql ( "SELECT * FROM #__comments WHERE type='".$this->type."' AND parent=$parent ; " );
		foreach ( $allcoms as $com1 ) $this->delete( $com1->id );
    }


    /** Редактируем комментарий */
    function edit (array $params){
		global $reg;
		$i24r = new mosDBTable( "#__comments", "id", $reg['db'] );

		$i24r->id = $params['id'];
		if (  isset($params['text'])  )		$i24r->text = $params['text'];
		if (  isset($params['publish'])  )	$i24r->publish = $params['publish'];
		
		if (!$i24r->check()) {	echo "<script> alert('".$i24r->getError()."'); window.history.go(-1); </script>\n";  } else $i24r->store();

	}


    /** Записываем новый комментарий */
    function set (array $params)
    {
		//ggd ($params);
        $parent = $params['parent'];
        if($parent == 0 || $parent == "" || $parent != intval($parent)) throw new Exception("неверная родительская запись");
        
		$name = "";
		$mail = "";
	
		$uid = intval($params['uid']);
		if($uid == "") $uid = 0;
		$name = $this->filter($params['name']);
		$mail = $this->filter($params['mail']);
	
		if($uid == 0 && $name == "") throw new Exception("пользователь не задан");
	
		$text = $this->filter($params['text']);
	
		$time = time();
		$ip = $params['ip'];
		$url = $params['url'];
		$type = $this->component;
	
		$publish = !$this->premoderation;
		
		/*
		 * ЭТО ВОПРОС ПО ЗАКАЗУ СЛЕДОВАТЕЛЬНО - УВЕДОМЛЕНИЕ ПО СМС
		 */
		if (  $type=='order'  ){
			$managers = ggsql("  select * from #__ordermanagers where order_id=$parent  ");
			//ggtr5 ($managers);	ggr ($this);
			foreach ($managers as $managers1){
				if (  $managers1->manager_id>0  ){
					$manager = ggo ($managers1->manager_id, "#__users");
					if (  $manager->note_sms_tel2!=''  ){
						$order_text = short_surl().", заказ #".$parent." покупатель задал вопрос ";
						$mail2sms = new mail2sms();
						$mail2sms->tel = $manager->note_sms_tel1.$manager->note_sms_tel2;
						$mail2sms->tel = preg_replace("/[- ]/", "", $mail2sms->tel);
						$mail2sms->oper = $manager->note_sms_oper;
						$mail2sms->text = $order_text;
						$mail2sms->sendSms();
					}
				}
			}// foreach

		}
		// отправляем письмо
		if (  $type=='comment'  ){
			$parent_comment = ggo ($parent, "#__comments");
			$message_link = $url."#com".$parent_comment->id;
			$etmp = $parent_comment->name.",<br /><br />на Ваше сообщение ответили.<br />
Просмотреть новое сообщение можно на странице:<br />
<a href='".$message_link."'>".$message_link."</a>
<br />
<br />
С уважением,<br />
Администрация ".short_surl();
			$mymail = new mymail();
			$mymail->add_address ( $parent_comment->mail );
			$mymail->set_subject ( "Уважаемый ".$parent_comment->name.", на Ваше сообщение ответили. ".short_surl() );
			$mymail->set_body	 ( $etmp );
			$mymail->send ();
		}
		
		$this->db->setQuery("INSERT INTO #__comments VALUES (0, $parent, $uid, \"$name \", \"$mail \", \"$text \", $time, \"$ip\", \"$type\", $publish, \"$url\")");
		$this->db->query();	
		return $this->db->insertid();
    }

    /* Преобразование символов */
    private function filter($text){
		$text = trim($text);
		$text = preg_replace("/\"/","&quot;", $text);
		$text = preg_replace("/</","&lt;", $text);
		$text = preg_replace("/>/","&gt;", $text);
		$text = preg_replace("/'/","&rsquo;", $text);     
		return $text;
    }

   public function show_comm($coms, $comm_level, $com_say){
   	global $my, $reg;
   	 if (  $coms!=false  )
     foreach ($coms as $com1) {  
	 	$user = new user($com1->userid);
		$user->get_smalllogo();	// "/lib/user.php" function
		?>
		<ul class="comments<? if (  $comm_level==0  ) print ' comments_first'; ?>">
			<li id="comment_222" class="comment-item"><a name="com<?=$com1->id ?>"></a><!-- comment big block start -->
				<div class="comment-block">                
					<!-- avatar -->
					<div class="comment-avatar">
						<span><? if (  $user->vars->org  ) { ?><a href="/images/cab/logo/<?=$user->vars->org ?>" class="fancy"><? } ?>
							<img src="<?=$user->get_smalllogo(); ?>" width="60" height="60" alt="<?=$com1->name ?>" />
						<? if (  $user->vars->org  ) { ?></a><? } ?></span>
					</div><!-- avatar -->
				
					<div class="comment-body">
						<ul class="comment-info">
							<li><a href="#com<?=$com1->id ?>">#</a></li><!-- ссылка-якорь на коммент -->
							<li class="comment-user"><a href="#com<?=$com1->id ?>" class="m"><?=$com1->name ?></a></li><!-- name -->
							<li class="comment-date"><?=date( 'd', $com1->time ); ?> <?=ru::GGgetMonthNames(date( 'm', $com1->time )) ?> <nobr>в <?=date( 'H:i', $com1->time+$reg['iServerTimeOffset'] ); ?></nobr></li><!-- date -->
							<? if (  $my->id==$com1->userid  and  $my->id>0  ){ ?><li class="comment-reply"><a href="javascript: ins_ajax_open('/?c=write_comment_ajax&4ajax=1&id=<?=$com1->id ?>&type=comment&say=<?=$com_say ?>&task=edit', 400, 470); void(0);" title="Изменить">изменить</a></li><!-- knopka otvetit na koment --> <? } 
							else if (  $this->can_answer==1  ) { ?><li class="comment-reply"><a href="javascript: ins_ajax_open('/?c=write_comment_ajax&4ajax=1&parent=<?=$com1->id ?>&type=comment&say=<?=$com_say ?>', 400, 470); void(0);" title="Ответить на комментарий">ответить</a></li><!-- knopka otvetit na koment --><? } ?>
						</ul>
						<div class="cl"></div><!-- clear float-->
						<div class="comment-text"><?=desafelySqlStr($com1->text); ?></div><!-- /comment text -->
					</div><!-- comment body -->
		  
					<? /*<div class="voting-container"><!-- golosovalka +-1 -->
						<ul class="voting">
							<li class="minus"><a href="javascript:void(0)" onclick="do_vote(this,13,1022414,-1);"></a></li>
							<li class="numb"><a class="numb-plus" href="javascript:void(0)" class="numb" onclick="voting_history(13,1022414);" title="">4</a></li><!-- в тег А добавляем класс numb-plus если в плюсе либо numb-minus в противном случае -->
							<li class="plus"><a href="javascript:void(0)" onclick="do_vote(this,13,1022414,1);"></a></li>
						</ul>
					</div><!-- /golosovalka --> */ ?>
					<div class="cl"></div><!-- clear -->
					<!-- ответы на коммент вставляем таким же списком с такими же классами в блоке самого коммента </li> перед закрывающим тэгом (по дефалту паддинг у них будет 20 px)-->
					<? if(  is_array($com1->children)  ) $this->show_comm(  $com1->children, ($comm_level+1), $com_say  ); ?>
				</div>
			</li>  
		</ul><?
	 }
   }
  /** Получение дерева записей */
   public function get($id, $limitstart = 0, $limit = 30)
   {
	if($id != intval($id)) return false;
	else return $this->getChildren($id, 'object', $this->component, $limitstart, $limit);
    }

    /** Юзеринфо */
    public function user($uid){
	  if($uid != intval($uid)) return false;

	  $this->db->setQuery("SELECT * FROM #__users where id = ".$uid);
	  $this->db->query();
	  list($user) = $this->db->loadObjectList();	
      return $user;
    }
	public function comments_here($com_parent, $com_say){
		global $reg, $my;

		$coms = $this->get($com_parent, 0, 30);
		?><div id="comments_list">
				<a name="comments"></a> <!-- якорь -->
				<div class="header-comments"><!-- заголовок  -->
					<h1><?=$this->say[$com_say]['Many'] ?>: <span class="numb"></span><span class="add">+1<a href="javascript:void(0)" class="add-hover" onclick="javascript: ins_ajax_open('/?c=write_comment_ajax&4ajax=1&parent=<?=$com_parent ?>&type=<?=$this->component ?>&say=<?=$com_say ?>', 400, 470); void(0);">Добавить <br /> <?=$this->say[$com_say]['one'] ?></a></span></h1>
					<div class="cl"></div>
				</div><!-- /заголовок  -->	
				<? $this->show_comm( $coms, 0, $com_say); ?>
		</div><?
		
		if (  isset($_REQUEST['4print'])  ) return;
	
		$captcha = new captcha();    $captcha->img_id="ins_write_comment_code"; 	$captcha->codeid_id="ins_write_comment_codeid";		$captcha->init();
		$myform = new insiteform();
		$myname = "Ваше имя";
		$myemail = "E-mail";
		if (  $my->id  ) $user = new user($my);
		if (  $my->id  ) $myname = $user->getGentleName();
		if (  $my->id  and  $my->email!=''  ) $myemail = desafelysqlstr( $my->email );
		?><div id="wrapper_insite_write_us" class="wrapper_insite_ajax" style=" width:350px; height:380px; ">
		<script language="javascript">
			var options_write_comment_nopopup = {		dataType:		'script',
														beforeSubmit:  function(){	over_fade('#wrapper_insite_write_us', '#wrapper_insite_write_us', '', 0.5, 'nopopup'); },
														success: function(){ over_fade_hide(); }
						  };
			<? //$('#ins_write_comment').submit(function() { 	$(this).ajaxSubmit(options); 	return false; });  ?>
		</script>
		<form <? ctrlEnter( "  $('#ins_write_comment_nopopup').ajaxSubmit(options_write_comment_nopopup); return false; " ) ?> action="/index.php" method="post" name="ins_write_comment_nopopup" id="ins_write_comment_nopopup" onsubmit=" $(this).ajaxSubmit(options_write_comment_nopopup); 	return false; " >
		<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_write_comment_title_table" class="insite_ajax_form_table">
			<tr height="5"><th></th></tr>
			<tr height="20"><th style=" text-align:left" align="left"><?=$this->say[$com_say]['Write'] ?> <?=$this->say[$com_say]['one'] ?></th></tr>
			<tr height="20"><td style="font-size:8px"><div id="ins_write_comment_server_answer" class="insite_ajax_server_answer" style="margin:0; padding:0; width: 300px; height:20px;" >&nbsp;</div></td></tr>
			<tr height="8"><td></td><td style="font-size:8px">&nbsp;</td></tr>
		</table>
		<table width="300" border="0" cellspacing="0" cellpadding="0" align="center"  id="insite_write_comment_main_table" class="insite_ajax_form_table">
			<tr>
				<td><input <? if (  !$my->id  ) $myform->make_java_text_effect('comname', 'input_light'); ?> size="30" class="input_ajax input_width input_gray" name="comname" id="comname" value="<?=$myname ?>" <? if (  $my->id  ) print 'readonly="1"'; ?>  title="<?=$myname ?>" /></td>
			</tr>
			<tr>
				<td><input <? if (  !$my->id  ) $myform->make_java_text_effect('commail', 'input_light'); ?> size="30" class="input_ajax input_width input_gray" name="commail" id="commail" value="<?=$myemail ?>" <? if (  $my->id  ) print 'readonly="1"'; ?> title="<?=$myemail ?>" /></td>
			</tr>
			<tr>
				<td><textarea <? $myform->make_java_text_effect('comcomment', 'input_light'); ?>  class="textarea_ajax input_width input_gray"  cols="35" rows="12" name="comcomment" id="comcomment" title="<?=$this->say[$com_say]['MessageText'] ?>:" ><?=$this->say[$com_say]['MessageText'] ?>:</textarea></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td style="padding-left:2px;">Код&nbsp;безопасности:&nbsp;*&nbsp;<br /><table cellpadding="0" cellspacing="0" border="0"><tr><td valign="middle" style="vertical-align:middle;"><? $captcha->codeid_input(); $captcha->show_captcha() ?></td>
					<td valign="middle" style="vertical-align:middle; font-size:22px; font-weight:normal; font-style:normal; font-family:Arial, Helvetica, sans-serif; ">&nbsp;&rarr;&nbsp;</td>
					<td valign="middle" style="vertical-align:middle; "><input type='text' name='gbcode'  maxlength='5' class='input_ajax input_ajax_gbcode' title='Введите показанный код' /></td>
					<td valign="middle" style="vertical-align:middle; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:spamfixreload('ins_write_comment_code', '<?=$captcha->codeid ?>')" >не&nbsp;вижу&nbsp;код</a></td>
				</tr></table></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td style="text-align:center; " align="center"><input type="submit" value="Отправить" class="button" /><?=ctrlEnterHint() ?></td></tr>
		</table>
		<input type="hidden" name="c" value="write_comment_ajax" />
		<!-- так как встречаются вредные фильтры-блокировщики, то рорup меняем на floating -->
		<input type='hidden' name='task' value='reply_from_nofloating' />
		<input type="hidden" name="4ajax" value="1" />
		<input type="hidden" name="server_answer_id" value="ins_write_comment_server_answer" />
		<input type="hidden" name="comment_main_table_id" value="insite_write_comment_main_table" />
		<input type="hidden" name="type" value="<?=$this->component; ?>" />
		<input type="hidden" name="say" value="<?=$com_say; ?>" />
		<input type="hidden" name="parent" value="<?=$com_parent; ?>" />
		</form></div><?


	}
	function get_link(){
		return "index2.php?ca=comment&task=view&icsmart_comment_parent=".$this->parent."&icsmart_comment_parent2=".$this->parent."&icsmart_comment_type=".$this->type;
	}
	function howmany_comments (){
		$total_fotos =  ggsqlr ("SELECT count(id) FROM #__comments WHERE parent=".$this->parent." AND type='".$this->type."'; ");
		if (  $total_fotos==''  ) return 0;
		else return $total_fotos;
	}

	function load_parent (  &$parent_obj=NULL  ){
		if (  $parent_obj!=NULL  ) $this->parent_obj = $parent_obj;
		else if (  $this->type=="order"  ){
			$parent_obj->name="Заказ № ".$this->parent;
			$parent_obj->id=$this->parent;
			$this->parent_obj = $parent_obj;
		}
		else if (  $this->parent>0  ){
			$this->parent_obj = ggo (  $this->parent, $this->table_parent, $this->table_parent_id_field  );
			if (  $this->type=="content"  ) $this->parent_obj->name = $this->parent_obj->title;
		}
	}
	
	function init( &$parent_obj=NULL ){
		global $reg;
		
		$this->id 			= ggri('id', $_REQUEST['cid'][0]);
		$this->table_parent_id_field = "id";

		if (  $this->type=="comment"  ){
			$this->table_parent = "#__comments";
			$this->parent_component_name = "Коммент/отзыв";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
			}
		}		
		if (  $this->type=="content"  ){
			$this->table_parent = "#__content";
			$this->parent_component_name = $reg['content_name'];
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__icat", "parent", "", "" , 1, $this->type);
			}
		}
		if (  $this->type=="exgood"  ){
			$this->table_parent = "#__exgood";
			$this->parent_component_name = $reg['ex_name'];
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
				$this->icatway = get_pathway_array_admin($this->parent_obj, "#__excat", "parent", "index2.php?ca=".$this->type, $reg['ex_name'], 1, "exgood");
			}
		}
		if (  $this->type=="order"  ){
			$this->table_parent = "#__order";
			$this->parent_component_name = "Вопрос менеджеру";
			if (  $this->load_parent  ){
				$this->load_parent ($parent_obj);
			}
		}

	}
    
}

?>