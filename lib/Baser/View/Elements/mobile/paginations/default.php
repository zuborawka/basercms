<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] ページネーション
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
if(!empty($this->Paginator)){
	$this->passedArgs['action'] = str_replace('mobile_','',$this->passedArgs['action']);
	$this->passedArgs['plugin'] = '';
	if($this->Paginator->counter(array('format'=>'%pages%'))>1){
		echo $this->Paginator->prev('<<', null, null, null).'&nbsp;';
		echo $this->Paginator->numbers(array('separator'=>'&nbsp;','modulus'=>4)).'&nbsp;';
		echo $this->Paginator->next('>>', null, null, null);
	}
}
?>