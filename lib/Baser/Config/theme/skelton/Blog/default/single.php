<?php
/**
 * ブログ詳細ページ
 */
$this->BcBaser->css('colorbox/colorbox', array('inline' => true));
$this->BcBaser->js('jquery.colorbox-min-1.4.5', false);
$this->BcBaser->setDescription($this->Blog->getTitle().'｜'.$this->Blog->getPostContent($post,false,false,50));
?>

<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
});
</script>

<h2 class="contents-head">
	<?php $this->Blog->title() ?>
</h2>
<h3 class="contents-head">
	<?php $this->BcBaser->contentsTitle() ?>
</h3>

<div class="eye-catch">
	<?php $this->Blog->eyeCatch($post) ?>
</div>

<div class="post">
	<?php $this->Blog->postContent($post) ?>
	<div class="meta"> <span>
		<?php $this->Blog->category($post) ?>
		&nbsp;
		<?php $this->Blog->postDate($post) ?>
		&nbsp;
		<?php $this->Blog->author($post) ?>
		</span> </div>
	<?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?>
</div>
<div id="contentsNavi">
	<?php $this->Blog->prevLink($post) ?>
	&nbsp;｜&nbsp;
	<?php $this->Blog->nextLink($post) ?>
</div>
<?php $this->BcBaser->element('blog_comments') ?>