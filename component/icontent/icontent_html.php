<?php
defined( '_VALID_INSITE' ) or die( 'Доступ запрещен' );

# Шаблоны для вывода компонента НОВОСТИ-СТАТЬИ

class tplContent {

	public function __construct(){
		
	}
	
	public function tpl($name, $params){
		$tpl_name = 'html' . mb_ucfirst($name);
		// var_dump($params);
		if(method_exists($this, $tpl_name))
			return $this->$tpl_name($params);
			
		return false;
	}
	
	private function htmlShowContent(&$p){
		global $reg, $sefname;
		
		if( substr($sefname,0,6)=='tours_' ){ $this->htmlShowContent_tours(&$p); return; }
		
		$row = $p->row;
		$component_foto = $p->component_foto;
		$component_file = $p->component_file;
		
		?>
		<?php if ($reg['mainparent']->sefname==='visa_center'): ?>	
			<div class="visa_mens_new inv" style="float:right; margin: 40px 20px;">
				<h4>Есть вопросы?</h4>
				<img class="manager_img" src="/images/ava.png" alt="marina">
				<div class="manager_name">Светлана Жмакова</div>
				<div class="manager_phones">тел.&nbsp;+7(391) 2888-306<br>тел. +7(391) 2414-888<br>icq 604971433</div>
				<div class="manager_email"><a href="mailto:visarusso@mail.ru">visarusso@mail.ru</a></div>
				<div class="manager_vk"><a href="http://vk.com/club54033732">http://vk.com/club54033732</a></div>
				<div class="manager_button"><a class="btn colorbox2 cboxElement" href="/ping?s=tour"><span class="icon1 iworld">&nbsp;</span> Заявка на визу</a></div>			
			
			</div>
		<?php endif; ?>
		
		<div class="holst">
		<div class="inner_content news">
		<?php if ($reg['mainparent']->sefname==='visa_center'): ?>
			<div id="ipathway_div" class="iway_ipathway_div unl">
				<a class="iway_pathway_link" href='/'>Главная</a> 
				<?php echo print_pathwayspliter(); ?>
				<a class="iway_pathway_link" href="/<? echo $reg['mainparent']->sefname?>"><? echo $reg['mainparent']->name?></a>
				<?php echo print_pathwayspliter(); ?>	
				<?=$row->title ?>
			</div>
		<?php else: ?>
			<?ipathway();?>
		<?php endif; ?>

			<h1><?=$reg['mainparent']->name ?></h1>
			<? if( substr($sefname,0,4)=='news' ){ ?><p class="date"><?=mindate($row->created); ?></p><? } ?>
			<h4><?=$row->title ?></h4>
			<? /**/if(!empty($row->org)){ echo "<img class=fr src='/images/icat/icont/{$row->org}' alt='image'>"; }/**/  ?>
			<?=desafelySqlStr($row->introtext); ?>
			<?=desafelySqlStr($row->fulltext); ?>
		</div>
		</div>
		<?
	}
	
	private function htmlShowRubrics(&$p){
		global $reg, $sefname;
		
		if( substr($sefname,0,6)=='hotels'	){ $this->htmlShowRubrics_hotel(&$p); return; }
		if( substr($sefname,0,6)=='tours_'	){ $this->htmlShowRubrics_tours(&$p); return; }
		
		$html = '';
		if(count($p->rubrics)){
			$component_foto = $p->component_foto;	
			foreach($p->rubrics as $rubric){
				$component_foto->parent = $rubric->id;
				$photos = $component_foto->get_1stfoto();
				$name = desafelySqlStr($rubric->name);
				$url  = $rubric->sefnamefull . '/' . $rubric->sefname . '/';
				$html .= "<p><a href='{$url}'>{$name}</a></p>";
			}
		}
		echo $html;
	}

	private function htmlContentList(&$p){
		global $reg, $sefname;
		
		if( substr($sefname,0,6)=='hotels'	){ $this->htmlContentList_hotel(&$p); return; }
		if( substr($sefname,0,6)=='tours_'	){ $this->htmlContentList_tours(&$p); return; }
				
		$html = '';
		$component_foto = $p->component_foto;
		$rows = $p->rows;
		
		if(count($rows) && substr($sefname,0,4)!='visa' ){
			$content = new content();		
			$html = '<ul>';
			foreach($rows as $row)
			{
				$content->id 	= $row->id;
				$content->vars 	= $row;
				
				$name = desafelySqlStr($content->vars->title);
				$description = $content->get_preview_text();
				$url = $content->get_link();
				$date = $content->get_date();
				
				$html .= '<li>';
				$html .= <<<HTML
					<div>
						<a class="ih2newtitle" href="{$url}">{$name}</a><br />
						<span class="realkk"><img width="8" height="10" alt="" src="/component/icontent/kk.gif" border="0"/> {$date}</span><br />
						<span class="ih2newtext">{$description}</span>
					</div>					
HTML;
				$html .= '</li>';
			}
			$html .= '</ul>';
			
			# Навигация по страницам
			require_once( site_path . '/includes/pageNavigation.php' );
			$pageNav = new mosPageNav($p->total, $p->limitstart, $p->limit);

			$pageNav->sign['param1'] = 'del_me_test';			// пример дополнительных параметров для поиска
			$pageNav->sign['param2'] = 'it_is_demonstration';	// пример дополнительных параметров для поиска
		
			$html .= $pageNav->getListFooter();			
			
		}
		else
		{
			//$html = '<p>База пустаs</p>';
		}

		?>
		
		<div class="holst">
		<div class="inner_content news_list unl">			
			<?ipathway();?>
			<h1><?//=$reg['mainobj']->name ?></h1>
			<?=desafelySqlStr($reg['mainobj']->fdesc); ?>
			<?=$html ?>
		</div>
		</div>
		<?
	}
	
	private function htmlShowRubrics_hotel(&$p){
		global $reg;

		$html = '';
		if(count($p->rubrics)){
			$component_foto = $p->component_foto;	

			foreach($p->rubrics as $rubric){
				$component_foto->parent = $rubric->id;
				
				$photos = $component_foto->get_1stfoto();
				$name = desafelySqlStr($rubric->name);
				$url  = $rubric->sefnamefull . '/' . $rubric->sefname . '/'; #xmp($photos);
				
				$img = $photos->org ? $photos->org : 'side_breeze_hotel_5_13_2_1.jpg';

				$html .= "<a href=\"{$url}\"><img src=\"{$component_foto->url_prefix}{$img}\" width=\"270\" height=\"206\" alt=\"фото отеля\"> <div><span>{$name}</span></div></a>";
			}
			
			?>
			<div class="inner_content wide">
				<?ipathway();?>
				<h1><?=$reg['mainobj']->name ?></h1>
				<div><?=desafelySqlStr($reg['mainobj']->sdesc); ?></div>
				<div class="item_list org"><?=$html ?></div>
				<div><?=desafelySqlStr($reg['mainobj']->fdesc); ?></div>
			</div>
			<?
		}
	}
	
	private function htmlContentList_hotel(&$p){
		global $reg;
		
		$html = '';

		$rows = $p->rows; #xmp($rows);
		if(count($rows))
		{
			$i_colorbox = 4;
			$content = new content();		
			$html  = "<div class='clear'><br></div>";
			$html .= "<h2>Номера отеля</h2>";
			$html .= "<ul class='list_room'>";
			foreach($rows as $row)
			{
				$content->id 	= $row->id;
				$content->vars 	= $row;
				
				$name = desafelySqlStr($content->vars->title);
				$description = $row->introtext;
				$url = $content->get_link();
				$date = $content->get_date();
				
				$img = $row->small ? '/images/icat/icont/'.$row->small : '/images/icat/icont/room.jpg';
				$img2 = $row->org ? '/images/icat/icont/'.$row->org : '/images/icat/icont/room.jpg';
				
				$i_colorbox++;

				$html .= "<li>";
				$html .= "<a class='crb colorbox{$i_colorbox}' href='{$img2}'><img src='{$img}' width='276' height='207' alt='photo' /><i></i></a>\n";

				$component_foto = new component_foto(0);
				$component_foto->init('content');
				$component_foto->parent = $row->id;
				$rows3 = $component_foto->get_fotos();
				if($rows3) foreach($rows3 as $row3)
				{
					$html .= "<a class='colorbox{$i_colorbox}' href='/images/icat/icont/{$row3->org}'> </a>\n";
				}
				$html .= "<b>{$name}</b></br>";
				$html .= "<div>{$description}</div>";
				$html .= "</li>\n";
				$i_colorbox_arr[] = $i_colorbox;
			}
			$html .= "</ul>";
			$html .= "<script>var colorbox_arr = ".json_encode($i_colorbox_arr).";</script>";
			
			# Навигация по страницам
			require_once( site_path . '/includes/pageNavigation.php' );
			$pageNav = new mosPageNav($p->total, $p->limitstart, $p->limit);

			$pageNav->sign['param1'] = 'del_me_test';			// пример дополнительных параметров для поиска
			$pageNav->sign['param2'] = 'it_is_demonstration';	// пример дополнительных параметров для поиска
		
			$html .= $pageNav->getListFooter();
		}
		
		if($reg['mainobj']->sefname != 'hotels')
		{
			$component_foto = new component_foto(0);
			$component_foto->init('icat');
			$component_foto->parent = $reg['mainobj']->id;
			$photos = $component_foto->get_fotos();
			?>
			<div class="holst hotel">
				<div class="inner_content">
				
					<?ipathway();?>
					<h1><?=$reg['mainobj']->name ?></h1>
					<div class="photo_innner">
						
						<? //$this->gallery($photos, '/images/icat/icat/'); ?>
						
						<?/*/?>
						<div class="photo_big">
							<img id="big_img" src="/images/icat/icat/<?=$photos[0]->org ?>" width="598" height="456" alt="photo" />
						</div>
						<div class="photo_small">		
							<?
								if($photos)foreach($photos as $photo)
								{
									echo "<a href='/images/icat/icat/{$photo->org}'><img src='/images/icat/icat/{$photo->small}' width='112' height='84' alt='{$photo->name}' /></a>";
								}
							?>
						</div>
						<script>
							$(document).ready(function()
							{
								$('.photo_small a').on('click',function(event)
								{
									var isrc = $(this).attr('href');
									if($(this).hasClass('current')) return false;
									
									$('.photo_small a').removeClass('current');
									$(this).addClass('current');
									$('#big_img').attr('src', isrc);

									event.preventDefault();
								});
								
							});
						</script>						
						<?/**/?>
					</div>
					<div><?php echo desafelySqlStr($reg['mainobj']->sdesc); ?></div>
					<?php echo $html ?>
					<div class="clear"></div>
				</div>
			</div>
			<?
		}
	}
	
	private function gallery2($rows, $path='/images/icat/icat/')
	{
		js('/component/ex/js.js');
		
		?>
		<div id='top' class="igal" >
		<div id='wrap_all' class='stretched'>
		
			<div class="wrapper wrapper_shadow" id='wrapper_featured_area'>
				<div class='overlay_top'></div>
				<div class='overlay_bottom'></div>
				<div class="center">
					<div class="feature_wrap">
						<ul class='slideshow aviaslider'>
						<?
						if($rows) foreach($rows as $row)
						{
							?>
							<li class='featured'>
								<span>
									<? if($row->name || $row->desc): ?>
									<span class='feature_excerpt'>
										<?=$row->name ? "<strong class='sliderheading'>{$row->name}</strong>":''; ?>
										<?=$row->desc ? "<span class='slidercontent'>{$row->desc}</span>":''; ?>
									</span>
									<? endif; ?>
									<img src='<?=$path.$row->org ?>' title='<?=$row->name ?>' width="598" height="456" alt="photo" />
								</span>
							</li>
							<?
						}
						?>
						</ul>
					</div>
				</div>
			</div>

			<div class="wrapper" id='wrapper_featured_stripe'>
				<div class="center">
					<ul class='slideshowThumbs'>
					<?
					if($rows) foreach($rows as $row)
					{
						?>
						<li class='slideThumb'>
							<span class='slideThumWrap'>
								<?=$row->name ? "<span class='slideThumbTitle'><strong class='slideThumbHeading rounded'>{$row->name}</strong></span>":''; ?>
								<span class='fancy'></span><img src='<?=$path.$row->small ?>' alt='img' width='112' height='84' />
							</span>
						</li>
						<?
					}
					?>
					</ul>
				</div>
			</div>
			
		</div>
		</div>
		<?
	}
	
	private function getImagesArray($rows, $path='/images/icat/icont/')
	{
		if($rows) {
			$images = array();
			foreach($rows as $row) {
				$images['small'][] = $path.$row->small;
				$images['fullsize'][] = $path.$row->org;
			}
			return $images;
		} else {
			return false;
		}
	}
	
	private function gallery($photos, $path='/images/icat/icont/')
	{
		$images = $this->getImagesArray($photos, $path);
		// var_dump ($images);
		?>
		<!-- fotorama.css & fotorama.js. -->
		<link  href="http://fotorama.s3.amazonaws.com/4.6.0/fotorama.css" rel="stylesheet"> <!-- 3 KB -->
		<script src="http://fotorama.s3.amazonaws.com/4.6.0/fotorama.js"></script> <!-- 16 KB -->
		<section class="suite-section">
			<div class="suite-section__container country-gallery">
				<h2 class="suite-section__title country-gallery__title">
					Общая информация по стране
				</h2>
				<div class="right-column country-gallery__image-small__column">
					<?php 
					// var_dump ($images);
					foreach ($images['small'] as $key => $image):
						echo '<a class="country-gallery__image-small" rel="gallery1"><img class="" src="'.$image.'" alt=""/ onclick="setGalleryImage(\''.$images['fullsize'][$key].'\')"></a>';
					endforeach;
					?>
					
				</div>
				<div class="left-column">
					<a class="fancybox" id="img2" rel="gallery1" href="<?php echo $images['fullsize'][0]; ?>">
						<img id="img1" class="country-gallery__image-big" src="<?php echo $images['fullsize'][0]; ?>" alt=""/>
					</a>
				</div>
			</div>
		</section>
		<script>
			function setGalleryImage(img)
			{
				$('#img1').attr('src', img);
				$('#img2').attr('href', img);
			}

		</script>
		<?
	}
	
	private function showVisaFormalitiesLink($html, $sefname)
	{
		if (strpos($html, '[visa-formalities]')) {
			// var_dump(1);
			$row = ggsql("SELECT title, sefname, sefnamefullcat FROM #__content WHERE sefname='".$sefname."' AND sefnamefullcat = '/visa_center';");
			$link = '<br>Ознакомьтесь подробнее с визовым режимом и закажите визу он-лайн: <a class="individual-tour__tours-title" href='.$row[0]->sefnamefullcat.'/'.$row[0]->sefname.'.html>'.$row[0]->title.'</a>';
			$result = str_replace('[visa-formalities]', $link, $html);
			
			return $result;
		}
	}
	
	private function showAnswerContactForm($html)
	{
		$rand = rand(1,2);
		if ($rand === 1) {
			$answerContactForm = '
				<div class="causes__answer">
					<h3 class="causes__answer-title">
						Есть вопросы?
					</h3>
					<img src="/images/foto/contacts/marina.jpg" alt="" class="causes__answer-avatar"/>

					<p class="causes__answer-name">
						Вахрушева Марина
					</p>
					<table class="causes__answer-contact">
						<tr>
							<td class="causes__answer-contact-td-icon">
								<span class="icon icon-phone"> </span>
							</td>
							<td>
								<a class="causes__answer-phone active" href="tel:391-2414-888">тел. 391-2414-888</a>
								<a class="causes__answer-phone active" href="tel:(391) 2888-306 ">тел. (391) 2888-306 </a>
							</td>
						</tr>
						<tr>
							<td class="causes__answer-contact-td-icon">
								<span class="icon icon-email"> </span>
							</td>
							<td>
								<a class="causes__answer-email active"
								   href="mailto:russoturisto5@mail.ru">russoturisto5@mail.ru</a>
							</td>
						</tr>
						<tr>
							<td class="causes__answer-contact-td-icon">
								<span class="icon icon-vk"> </span>
							</td>
							<td>
								<a class="causes__answer-vk active" href="http://vk.com/travelclubrusso">http://vk.com/travelclubrusso</a>
							</td>
						</tr>

					</table>
					<div class="causes__answer-btn_wrapper">
						<a class="btn colorbox2 cboxElement causes__answer-btn" href="/ping?s=tour"><span
								class="icon1 iworld">&nbsp;</span>
							Заявка на тур</a>
					</div>
				</div>';
			} else {
				$answerContactForm = '
				<div class="causes__answer">
					<h3 class="causes__answer-title">
						Есть вопросы?
					</h3>
					<img src="/images/foto/contacts/dasha2.jpg" alt="" class="causes__answer-avatar"/>

					<p class="causes__answer-name">
						Фомичева Дарья 
					</p>
					<table class="causes__answer-contact">
						<tr>
							<td class="causes__answer-contact-td-icon">
								<span class="icon icon-phone"> </span>
							</td>
							<td>
								<a class="causes__answer-phone active" href="(391) 2418-048">тел. (391) 2418-048</a>
								<a class="causes__answer-phone active" href="tel:391-2414-888">тел. 391-2414-888</a>
							</td>
						</tr>
						<tr>
							<td class="causes__answer-contact-td-icon">
								<span class="icon icon-email"> </span>
							</td>
							<td>
								<a class="causes__answer-email active"
								   href="mailto:russoturisto77@mail.ru ">russoturisto77@mail.ru </a>
							</td>
						</tr>
						<tr>
							<td class="causes__answer-contact-td-icon">
								<span class="icon icon-vk"> </span>
							</td>
							<td>
								<a class="causes__answer-vk active" href="http://vk.com/travelclubrusso">http://vk.com/travelclubrusso</a>
							</td>
						</tr>

					</table>
					<div class="causes__answer-btn_wrapper">
						<a class="btn colorbox2 cboxElement causes__answer-btn" href="/ping?s=tour"><span
								class="icon1 iworld">&nbsp;</span>
							Заявка на тур</a>
					</div>
				</div>';
			}
		$result = str_replace('[answer-contact]', $answerContactForm, $html);
		return $result;
	}

	private function htmlShowContent_tours(&$p)
	{
		global $reg;
		
		$row = $p->row;
		$component_foto = $p->component_foto;
		$photos = $component_foto->get_fotos();
		
		xmp($photof);
		?>
		<div class="holst hotel">
			<div class="page-content">
			<section class="suite-section country-preview">
				<div class="suite-section__container">
					<div class="country-preview__title">
						<h3 class="country-preview__title-header">
							<?=$row->title ?>
						</h3>

						<div class="country-preview__title-underline"></div>
						<p class="country-preview__title-description">
							для жителей Красноярска и Красноярского края
						</p>
					</div>
					<div id="header-search-form">
						<div id="TVSearchForm"></div><script src="http://tourvisor.ru/module/newform/searchform.min.js"></script>
				<script type="text/javascript"> TV.initModule({ moduleid: 258}); </script>
					</div>
				</div>
				
				
			</section>
			<script>
				$(".country-preview").backstretch([
					<?php 
						// $images = $this->getImagesArray($photos);
						// if ($images) {
							// foreach ($images['fullsize'] as $image) {
								// echo "\"" . $image . "\",\n";
							// }
						// } else {
							echo "\"" . '/images/icat/icont/' . $row->org . "\",\n";
						// }
					?>
				], {duration: 3000, fade: true});
				
				
				
				$(document).ready(function () {
					$(".fancybox").fancybox({
						openEffect: 'none',
						closeEffect: 'none'
					});
				});
			</script>
			<section class="suite-section">
				<div class="suite-section__container causes">
				<br><br>
				<pre>
				<?php //var_dump ($reg['mainparent']->parent); ?>
				</pre>
				<?ipathway();?>

					<?php 
						$introtext = desafelySqlStr($row->introtext); 
						$intotext = $this->showAnswerContactForm($introtext);
						echo $intotext;
						?>
				</div>
			</section>
				
				<? if($photos){ ?>
					<? $this->gallery($photos, '/images/icat/icont/'); ?>
				<? } ?>
				<div class="clear"></div>
				<section class="suite-section">
					<div class="suite-section__container causes">
						<?php 
						echo $fulltext = desafelySqlStr($row->fulltext); 
						
						?>
					</div>
				</section>
		</div>
		</div>
		<?
	}
	
	
	private function htmlShowRubrics_tours(&$p){
		global $reg;
		
		$html = '';
		if(count($p->rubrics)){
			$component_foto = $p->component_foto;
			// var_dump ($p);
			foreach($p->rubrics as $rubric)
			{
				// if( $rubric->name != 'подкатегория')
				// $html .= "<h6>{$rubric->name}</h6>\n";
				// var_dump ($rubric);
				$rows = ggsql(" select * from #__content where catid = '{$rubric->id}' and `state` = 1 order by `title`; ");
				if($rows) foreach($rows as $row)
				{
					$img = $row->small ? '/images/icat/icont/'.$row->small : '/images/ex/good/12.jpg';
					$name = desafelySqlStr($row->title);
					$url  = $row->sefnamefullcat . '/' . $row->sefname . '.html';
					
					$html .= "<a href=\"{$url}\"><img src=\"{$img}\" width=\"206\" height=\"206\" alt=\"фото отеля\"> <div><span>{$name}</span></div></a> \n"; 
				}
			}
			// var_dump ($rubric->sefnamefull);
			// $category = explode('_', $rubric->sefnamefull);
			// $sefname = $category[1];
			// $rows = ggsql("select b.name, b.sefname, b.sefnamefullcat, b.small  from #__excat as a, #__exgood as b where a.sefname='$sefname' and b.parent = a.id and b.publish = '1' order by b.order ;");
			
			// if($rows) foreach($rows as $row) {
				// $img = $row->small ? '/images/ex/good/'.$row->small : '/images/ex/good/12.jpg';
				// $name = desafelySqlStr($row->name);
				// $url  = $row->sefnamefullcat . '/' . $row->sefname . '.html';
				// $html .= "<a href=\"{$url}\"><img src=\"{$img}\" width=\"206\" height=\"206\" alt=\"фото отеля\"> <div><span>{$name}</span></div></a> \n"; 
			// }
			
			// var_dump ($rows);
			?>
			<div class="inner_content wide">
				<?ipathway();?>
				<h1><?=$reg['mainobj']->name ?></h1>
				
				<div class="item_list medical_tours"><?=$html ?></div>
				<pre>
				<?php //print_r ($p->rubrics[0]->fdesc); ?>
				</pre>
				<? //if($reg['sefname1'] == 'tours_europa'){ $this->ins_iframe(); } ?>
			</div>
			<?
		}
	}
	
	private function htmlContentList_tours(&$p){
		global $reg;
		
		$html = '';

		$rows = $p->rows; #xmp($rows);
		if(count($rows))
		{
			$content = new content();		
			$html  = "<div class='clear'><br></div>";
			$html .= "<h2>Номера отеля</h2>";
			$html .= "<ul class='list_room'>";
			foreach($rows as $row)
			{
				$content->id 	= $row->id;
				$content->vars 	= $row;
				
				$name = desafelySqlStr($content->vars->title);
				$description = $row->introtext;
				$url = $content->get_link();
				$date = $content->get_date();
				
				$img = $row->small ? '/images/icat/icont/'.$row->small : '/images/icat/icont/room.jpg';
				$img2 = $row->org ? '/images/icat/icont/'.$row->org : '/images/icat/icont/room.jpg';
				
				$html .= <<<HTML
					<li>
						<a class="colorbox" href="{$img2}"><img src="{$img}" width="276" height="207" alt="photo" /><i></i></a>
						<b>{$name}</b></br>
						<div>{$description}</div>
					</li>
HTML;
			}
			$html .= "</ul>";
			
			# Навигация по страницам
			require_once( site_path . '/includes/pageNavigation.php' );
			$pageNav = new mosPageNav($p->total, $p->limitstart, $p->limit);

			$pageNav->sign['param1'] = 'del_me_test';			// пример дополнительных параметров для поиска
			$pageNav->sign['param2'] = 'it_is_demonstration';	// пример дополнительных параметров для поиска
		
			$html .= $pageNav->getListFooter();
		}
		
		if( substr($reg['mainobj']->sefname,0,6) != 'tours_'  ) //substr($sefname,0,6)=='tours_'
		{
			$component_foto = new component_foto(0);
			$component_foto->init('icat');
			$component_foto->parent = $reg['mainobj']->id;
			$photos = $component_foto->get_fotos();
			?>
			<div class="holst hotel">
				<div class="inner_content">
					
					<?ipathway();?>
					<h1><?=$reg['mainobj']->name ?></h1>
					<div class="photo_innner">
						<? $this->gallery($photos, '/images/icat/icat/'); ?>
						<?/*/?>
						<div class="photo_big">
							<img id="big_img" src="/images/icat/icat/<?=$photos[0]->org ?>" width="598" height="456" alt="photo" />
						</div>
						<div class="photo_small">		
							<?
								if($photos)foreach($photos as $photo)
								{
									echo "<a href='/images/icat/icat/{$photo->org}'><img src='/images/icat/icat/{$photo->small}' width='112' height='84' alt='{$photo->name}' /></a>";
								}
							?>
						</div>
						<script>
							$(document).ready(function()
							{
								$('.photo_small a').on('click',function(event)
								{
									var isrc = $(this).attr('href');
									if($(this).hasClass('current')) return false;
									
									$('.photo_small a').removeClass('current');
									$(this).addClass('current');
									$('#big_img').attr('src', isrc);

									event.preventDefault();
								});
								
							});
						</script>						
						<?/**/?>
					</div>
					<div><?=desafelySqlStr($reg['mainobj']->sdesc); ?></div>
					<?=$html ?>
					<div class="clear"></div>
				</div>
			</div>

			<?
		}
	}
	
	
	function ins_iframe()
	{
		?>
		<br>
		<br>
		<div class="ins_iframe iframe">
			<form action="" class="form" id="jq_form">
				<h1>Заявка на расчет тура</h1>
				<p>«Заполните заявку с Вашими пожеланиями, и мы оперативно свяжемся с Вами предложив вам самые выгодные предложения и интересные варианты отдыха!».</p>
				<div class="row2">
					<div class="span2">
						<label for="2for1">Имя<sup>*</sup></label>
						<input id="2for1" class="input" type="text" name="name" required />
						<label for="2for4">Удобное время для звонка</label>
						<div class="inp_small">c <input id="2for4" class="input" type="time" name="from" /> до <input class="input" type="time" name="to" /></div>			
					</div>
					<div class="span2">
						<label for="2for2">Телефон<sup>*</sup></label>
						<input id="2for2" class="input" type="tel" name="tel" required />
						<label for="2for3">e-mail</label>
						<input id="2for3" class="input" type="email" name="email" />			
					</div>
					<div class="clear"></div>
				</div>
				<label for="2for5">Детали поездки, пожелания</label>
				<textarea id="2for5" class="input" name="text"></textarea>
				<h5 class="jq_data"><sup>*</sup>Поля отмеченный звездочкой обязательны к заполнению</h5>
				<input type="hidden" name="c" value="ping" />
				<input type="hidden" name="s" value="tour_r" />
				<input type="submit" class="btn2 fr" value="Отправить">
				<div class="clear"></div>
			</form>
		</div>
		<?
	}
	
}
















function html_contentShowItem(  &$p  ){
    global $reg;

    $row = &$p->row;
    $component_foto = &$p->component_foto;
    $component_file = &$p->component_file;

    if (  isset($_REQUEST['clean'])  )  {  print desafelySqlStr($row->fulltext);  return;  }

    ?><table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" >
            <tbody><tr><td valign="top" height="100%" style="padding: 0px 15px 15px; ">
            <div style="float:right;"><?=printVersion($reg['mainobj']->sefnamefullcat.'/'.$reg['mainobj']->sefname.'.html?4print=1'); ?></div>
            <h1><? print desafelySqlStr($row->title); ?></h1>

            <table class="contentpaneopen"><tr>
                <td valign="top" colspan="2"><? print desafelySqlStr($row->fulltext); ?></td>
            </tr></table>
            <?
            // ВЫВОДИМ ФОТО
            $fotocats = $fotocats = $component_foto->get_fotos();
            $icats_per_row = 3;
            $icats_index = 0;
            $fotos_cnt = $component_foto->howmany_fotos();

            if (  $fotos_cnt>0  ){ ?><br /><span class="goodfoto_view">Фотогалерея (<? print $fotos_cnt; ?>)</span><? }
            ?><br /><br /><table width="100%" cellspacing="1" cellpadding="4" border="0" >
            <tr><?
            foreach ($fotocats as $fotocat){
                    if (  ($icats_index>0)  &&  ($icats_index % $icats_per_row==0)  ) { ?></tr><tr><? }
                    ?><td width="<? print round(100/$icats_per_row); ?>%" valign="middle" align="center" class="foto_td" style="text-align:center"><?
                    $i24foto_link = $component_foto->createPreviewFotoLink ('small', 'org', $fotocat, '', ' rel="foto_group" class="highslide fancy" '  );
                    $i24foto_desc = desafelySqlStr($fotocat->desc);
                    print_foto_gallery($i24foto_link, $i24foto_desc, "/component/icontent/ramka/");
                    ?></td><?
                    $icats_index++;
            }
            ?></tr></table><?

            if (  count ($component_file)>0  &&  !inPrintVersion()  ){ 
                if (  $reg['#__content_ID'.$reg['mainobj']->id.'__показывать видео']=='да'  ){ ?>
                    <p>&nbsp;</p>
                    <h1>Видео ролики</h1><br><?
                    $component_file->show_movies();
                } else { ?>
                    <p>&nbsp;</p>
                    <h1>Прикрепленные файлы</h1><br><?
                    $component_file->show_files();
                }
            }

            // ВЫВОДИМ КОММЕНТАРИИ
            $comments = new comments('content', $reg['db'], $reg);
            $comments->comments_here(ggri('id'), 'say_comment');

    if (  isset($_REQUEST['4print'])  ) return;

    $ima_next = ggsql ( "SELECT * FROM #__content WHERE catid=".$row->catid." AND ordering<".$row->ordering."  ORDER BY ordering DESC LIMIT 0,1; " );
    $ima_prev = ggsql ( "SELECT * FROM #__content WHERE catid=".$row->catid." AND ordering>".$row->ordering."  ORDER BY ordering ASC LIMIT 0,1; " );
    ?>

  <div class="see_more">
	  <h4 class="see_more">Смотрите в этом разделе</h4>
		<table class="see_more" border="0" width="100%" >
		  <tbody><tr>
			<td></td><td class="left" width="33%" >
				<? if (  count($ima_prev)>0  ){
					?><a href="<? $row = $ima_prev[0]; $ilink = $row->sefnamefullcat.'/'.$row->sefname.".html"; print $ilink; ?>"><?  print desafelySqlStr($row->title); ?></a><?
				}
			?></td>
			<td class="center" width="33%"  >&nbsp;</td>
			<td class="right" width="33%" >
				<? if (  count($ima_next)>0  ){
					?><a href="<? $row = $ima_next[0]; $ilink = $row->sefnamefullcat.'/'.$row->sefname.".html"; print $ilink; ?>"><?  print desafelySqlStr($row->title); ?></a><?
				}
			?></td><td></td>
		  </tr>
		  <tr>
			<td class="left_arrow" align="left"><?
				if (  count($ima_prev)>0  ){  $row = $ima_prev[0]; $ilink = $row->sefnamefullcat.'/'.$row->sefname.".html";  ?> <a href="<? print $ilink; ?>">&larr;&nbsp;</a><? }
			?></td>
			<td class="left" align="left" ><?
				if (  count($ima_prev)>0  ) { 	if (  $row->images!=''  ) shadow_effect('<a href="'.$ilink.'"><img vspace="0" hspace="3" border="0" width="200" src="'.$row->images.'" /></a>','','left');
												else { ?><img alt="" style="border: 0px none;" border="0" src="<?=$reg['icontentnoimage'] ?>"/><? }
											}
			?></td>
			<td ></td>
			<td class="right" align="right" ><?
				if (  count($ima_next)>0  ) { $row = $ima_next[0]; $ilink = $row->sefnamefullcat.'/'.$row->sefname.".html";  	if (  $row->images!=''  ) shadow_effect('<a href="'.$ilink.'"><img vspace="0" hspace="3" border="0" width="200" src="'.$row->images.'" /></a>','','right');
																																else { ?><img alt="" style="border: 0px none;" border="0" src="<?=$reg['icontentnoimage'] ?>"/><? }
											}
			?></td>
			<td class="right_arrow" align="right"><?
				if (  count($ima_next)>0  ){   ?>&nbsp;<a href="<? print $ilink; ?>">&rarr;</a><? }
			?></td>
		  </tr>
		</tbody></table>
  </div>

<span class="article_seperator"> </span>
<div class="back_button" ><a href="javascript:history.go(-1)">Вернуться</a></div>
</td></tr></tbody></table>
<?
}


