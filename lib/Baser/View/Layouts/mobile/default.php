<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] レイアウト
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?><cake:nocache><?php $this->BcMobile->header() ?></cake:nocache><?php $this->BcBaser->xmlHeader() ?><?php $this->BcBaser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
<head>
<?php $this->BcBaser->charset() ?>
<?php $this->BcBaser->title() ?>
<?php $this->BcBaser->metaDescription() ?>
<?php $this->BcBaser->metaKeywords() ?>
</head>
<body bgcolor="#FFFFFF" id="<?php $this->BcBaser->contentsName() ?>">
<div style="color:#333333;margin:3px">
	<div style="display:-wap-marquee;text-align:center;background-color:#8ABE08;"> <span style="color:white;"><?php echo $this->BcBaser->siteConfig['name'] ?></span> </div>
	
	<center>
		<span style="color:#8ABE08;">Let's baserCMS!</span>
	</center>

	<hr size="2" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:2px solid #8ABE08;" />
	
	<?php echo $content_for_layout; ?><br />
	<?php $this->BcBaser->element('contents_navi') ?><br />
	
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	
	<span style="color:#8ABE08">◆ </span>
	<?php $this->BcBaser->link('トップへ','/'.Configure::read('BcAgent.mobile.alias').'/') ?>
	
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#8ABE08;background:#8ABE08;border:1px solid #8ABE08;" />
	
	<center>
		<?php $this->BcBaser->img('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")); ?>
		<?php $this->BcBaser->img('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")); ?>
		<font size="1">(C)baserCMS</font>
	</center>
	
</div>
<?php $this->BcBaser->element('google_analytics') ?>
</body>
</html>