<?  $faqs = ggsql (" select * from #__easybook where published=1 order by gbid DESC limit 0,3  ");

//ggr ($faqs);
?><table id="faq-additional">
<tbody><tr>
		<td id="home-additional-news">
		<h3><a href="/?c=easybook">Вопрос-ответ</a></h3>
			<? foreach ($faqs as $faq){ ?>
				<div class="faq-additional-item">
					<a href="/?c=easybook"><?=stripslashes($faq->gbtext) ?></a>
					<p><? print trim(  str::get_substr_clean(stripslashes($faq->gbcomment), 70)  ); ?></p>
				</div>
			<? } ?>
		</td>
	</tr>
<tr>
		<td id="home-additional-news"><?
                    // отображаем кнопку редактирования
                    editme( site_url.'/iadmin/index2.php?ca=easybook&task=view', array('id'=>$catid, 'note'=>'редактировать вопрос-ответ<br>'), 'small' );
		?></td>
	</tr>
</tbody></table>