<?php

/* SVN FILE: $Id$ */
/**
 * 起動スクリプト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
require CORE_PATH . 'Baser' . DS . 'Config' . DS . 'paths.php';
require BASER . 'basics.php';

/**
 * Baserパス追加
 */
App::build(array(
	'Controller'				=> array_merge(App::path('Controller'), array(BASER_CONTROLLERS)),
	'Model'						=> array_merge(App::path('Model'), array(BASER_MODELS)),
	'Model/Behavior'			=> array_merge(App::path('Model/Behavior'), array(BASER_BEHAVIORS)),
	'Model/Datasource'			=> array_merge(App::path('Model/Datasource'), array(BASER_DATASOURCE)),
	'Model/Datasource/Database'	=> array_merge(App::path('Model/Datasource/Database'), array(BASER_DATABASE)),
	'Controller/Component'		=> array_merge(App::path('Controller/Component'), array(BASER_COMPONENTS)),
	'View'						=> array_merge(array(WWW_ROOT), App::path('View'), array(BASER_VIEWS)),
	'View/Helper'				=> array_merge(App::path('View/Helper'), array(BASER_HELPERS)),
	'Plugin'					=> array_merge(App::path('Plugin'), array(BASER_PLUGINS)),
	'Vendor'					=> array_merge(App::path('Vendor'), array(BASER_VENDORS)),
	'Locale'					=> array_merge(App::path('Locale'), array(BASER_LOCALES)),
	'Lib'						=> array_merge(App::path('Lib'), array(BASER_LIBS)),
	'Console'					=> array_merge(App::path('Console'), array(BASER_CONSOLES)),
	'Console/Command'			=> array_merge(App::path('Console/Command'), array(BASER_CONSOLES . 'Command' . DS)),
));
App::build(array(
	'Event'						=> array(APP . 'Event', BASER_EVENTS),
), App::REGISTER);

App::uses('AppModel',			'Model');
App::uses('BcAppModel'	,		'Model');
App::uses('BcCache',			'Model/Behavior');
App::uses('ClassRegistry',		'Utility');
App::uses('Multibyte',			'I18n');
App::uses('BcCsv',				'Model/Datasource/Database');
App::uses('BcPostgres',			'Model/Datasource/Database');
App::uses('BcSqlite',			'Model/Datasource/Database');
App::uses('BcMysql',			'Model/Datasource/Database');
App::uses('PhpReader',			'Configure');
App::uses('CakeSession',		'Model/Datasource');
App::uses('Folder',				'Utility');
App::uses('File',				'Utility');
App::uses('BcControllerEvent',	'Event');
App::uses('BcModelEvent',		'Event');
App::uses('BcViewEvent',		'Event');
/**
 * baserUrl取得
 */
define('BC_BASE_URL', baseUrl());
/**
 * define類は vendors内の静的ファイルの読み込みの場合はスキップの処理の時のnotice抑制の為上位に持ってきた
 */
/**
 * 配置パターン
 * Windows対策として、「\」を「/」へ変換してチェックする
 */
if (!preg_match('/' . preg_quote(str_replace('\\', '/', docRoot()), '/') . '/', ROOT)) {
	// CakePHP標準の配置
	define('BC_DEPLOY_PATTERN', 3);
} elseif (ROOT . DS == WWW_ROOT) {
	// webrootをドキュメントルートにして、その中に app / baser / cake を配置
	define('BC_DEPLOY_PATTERN', 2);
} else {
	// baserCMS配布時の配置
	define('BC_DEPLOY_PATTERN', 1);
}
/**
 * インストール状態
 */
define('BC_INSTALLED', isInstalled());
/**
 * 設定ファイル読み込み
 * install.php で設定している為、一旦読み込んで再設定
 */
$baserSettings = array();
$baserSettings['BcEnv'] = Configure::read('BcEnv');
$baserSettings['BcApp'] = Configure::read('BcApp');
Configure::config('baser', new PhpReader(BASER_CONFIGS));
if (Configure::load('baser', 'baser') === false) {
	$config = array();
	include BASER_CONFIGS . 'baser.php';
	Configure::write($config);
}
/**
 * vendors内の静的ファイルの読み込みの場合はスキップ
 */
$uri = @$_SERVER['REQUEST_URI'];
if (preg_match('/^' . preg_quote(BC_BASE_URL, '/') . 'css\//', $uri) ||
	preg_match('/^' . preg_quote(BC_BASE_URL, '/') . 'js\//', $uri) ||
	preg_match('/^' . preg_quote(BC_BASE_URL, '/') . 'img\//', $uri)) {
	$assets = array('js', 'css', 'gif', 'jpg', 'png');
	$aryUri = explode('.', $uri);
	$ext = array_pop($aryUri);
	if (in_array($ext, $assets)) {
		Configure::write('BcRequest.asset', true);
		return;
	}
}

if (BC_INSTALLED && $baserSettings) {
	foreach ($baserSettings as $key1 => $settings) {
		if ($settings) {
			foreach ($settings as $key2 => $setting) {
				Configure::write($key1 . '.' . $key2, $setting);
			}
		}
	}
}
/**
 * クレジット読込 
 */
$config = array();
include BASER_CONFIGS . 'credit.php';
Configure::write($config);
/**
 * パラメーター取得
 */
$url = getUrlFromEnv(); // 環境変数からパラメータを取得
$parameter = getUrlParamFromEnv();
Configure::write('BcRequest.pureUrl', $parameter); // ※ requestActionに対応する為、routes.php で上書きされる	

if (BC_INSTALLED) {
/**
 * データキャッシュ
 */
	Cache::config('_cake_data_', array(
		'engine' => 'File',
		'duration' => Configure::read('BcCache.dataCachetime'),
		'probability' => 100,
		'path' => CACHE . 'datas',
		'prefix' => 'cake_',
		'lock' => false,
		'serialize' => true
	));
/**
 * 環境情報キャッシュ
 */
	Cache::config('_cake_env_', array(
		'engine' => 'File',
		'duration' => Configure::read('BcCache.defaultCachetime'),
		'probability' => 100,
		'path' => CACHE . 'environment',
		'prefix' => 'cake_',
		'lock' => false,
		'serialize' => true
	));
	Cache::config('_cake_core_', array(
		'engine' => $engine,
		'prefix' => $prefix . 'cake_core_',
		'path' => CACHE . 'persistent' . DS,
		'serialize' => ($engine === 'File'),
		'duration' => $duration
	));
	Cache::config('_cake_model_', array(
		'engine' => $engine,
		'prefix' => $prefix . 'cake_model_',
		'path' => CACHE . 'models' . DS,
		'serialize' => ($engine === 'File'),
		'duration' => $duration
	));
/**
 * サイト基本設定を読み込む
 * bootstrapではモデルのロードは行わないようにする為ここで読み込む
 */
	loadSiteConfig();
/**
 * tmpフォルダ確認
 */
	checkTmpFolders();
/**
 * メンテナンスチェック
 */
	$isMaintenance = ($parameter == 'maintenance/index');
	Configure::write('BcRequest.isMaintenance', $isMaintenance);
/**
 * アップデートチェック
 */
	$isUpdater = false;
	$bcSite = Configure::read('BcSite');
	$updateKey = preg_quote(Configure::read('BcApp.updateKey'), '/');
	if(preg_match('/^'.$updateKey.'(|\/index\/)/', $parameter)) {
		$isUpdater = true;
	}elseif(BC_INSTALLED && !$isMaintenance && (!empty($bcSite['version']) && (getVersion() > $bcSite['version']))) {
		header('Location: '.topLevelUrl(false).baseUrl().'maintenance/index');exit();
	}
	Configure::write('BcRequest.isUpdater', $isUpdater);
}
/**
 * プラグインをCake側で有効化
 */
if(BC_INSTALLED && !$isUpdater && !$isMaintenance) {
	$plugins = getEnablePlugins();
	$CakeEvent = CakeEventManager::instance();
	foreach($plugins as $plugin) {
		CakePlugin::load($plugin);
		$pluginPath = CakePlugin::path($plugin);
		$config = array(
			'bootstrap'	=> file_exists($pluginPath . 'Config' . DS . 'bootstrap.php'),
			'routes'	=> file_exists($pluginPath . 'Config' . DS . 'routes.php')
		);
		CakePlugin::load($plugin, $config);
		CakePlugin::bootstrap($plugin);

		// プラグインイベント登録
		$eventTargets = array('Controller', 'Model', 'View');
		foreach($eventTargets as $eventTarget) {
			$eventClass = $plugin . $eventTarget . 'Event';
			if(file_exists($pluginPath . 'Event' . DS . $eventClass . '.php')) {
				App::uses($eventClass, $plugin . '.Event');
				$CakeEvent->attach(new $eventClass());
			}
		}
		
	}
	Configure::write('BcStatus.enablePlugins', $plugins);
	
/**
 * イベント登録
 */
	App::uses('BcControllerDispatch', 'Event');
	App::uses('BcModelDispatch', 'Event');
	App::uses('BcViewDispatch', 'Event');
	$CakeEvent->attach(new BcControllerDispatch());
	$CakeEvent->attach(new BcModelDispatch());
	$CakeEvent->attach(new BcViewDispatch());
	
/**
 * テーマの bootstrap を実行する
 */
	$themePath = WWW_ROOT.'theme'.DS.Configure::read('BcSite.theme').DS;
	$themeBootstrap = $themePath . 'Config' . DS . 'bootstrap.php';
	if(file_exists($themeBootstrap)) {
		include $themeBootstrap;
	}
}
/**
 * 文字コードの検出順を指定
 */
mb_detect_order(Configure::read('BcEncode.detectOrder'));
/**
 * メモリー設定
 */
$memoryLimit = (int)ini_get('memory_limit');
if ($memoryLimit < 32 && $memoryLimit != -1) {
	ini_set('memory_limit', '32M');
}
/**
 * セッションスタート 
 */
$Session = new CakeSession();
$Session->start();
/**
 * パラメーター取得
 * モバイル判定・簡易リダイレクト
 */
$agentSettings = Configure::read('BcAgent');
if (!Configure::read('BcApp.mobile')) {
	unset($agentSettings['mobile']);
}
if (!Configure::read('BcApp.smartphone')) {
	unset($agentSettings['smartphone']);
}
$agentOn = false;
if ($agentSettings) {
	foreach ($agentSettings as $key => $setting) {
		$agentOn = false;
		if (!empty($url)) {
			$parameters = explode('/', $url);
			if ($parameters[0] == $setting['alias']) {
				$agentOn = true;
			}
		}
		if (!$agentOn && $setting['autoRedirect']) {
			$agentAgents = $setting['agents'];
			$agentAgents = implode('||', $agentAgents);
			$agentAgents = preg_quote($agentAgents, '/');
			$regex = '/' . str_replace('\|\|', '|', $agentAgents) . '/i';
			if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
				$getParams = str_replace(BC_BASE_URL . $parameter, '', $_SERVER['REQUEST_URI']);
				if($getParams == '/' || $getParams == '/index.php') { 
					$getParams = '';
				}

				$redirect = true;

				// URLによる AUTO REDIRECT 設定
				if (isset($_GET[$setting['prefix'] . '_auto_redirect'])) {
					if ($_GET[$setting['prefix'] . '_auto_redirect'] == 'on') {
						$_SESSION[$setting['prefix'] . '_auto_redirect'] = 'on';
					} elseif ($_GET[$setting['prefix'] . '_auto_redirect'] == 'off') {
						$_SESSION[$setting['prefix'] . '_auto_redirect'] = 'off';
					}
				}

				if (isset($_SESSION[$setting['prefix'] . '_auto_redirect'])) {
					if ($_SESSION[$setting['prefix'] . '_auto_redirect'] == 'off') {
						$redirect = false;
					}
				}

				if (isset($_GET[$setting['prefix']])) {
					if ($_GET[$setting['prefix']] == 'on') {
						$redirect = true;
					} elseif ($_GET[$setting['prefix']] == 'off') {
						$redirect = false;
					}
				}

				if ($redirect) {
					$redirectUrl = FULL_BASE_URL . BC_BASE_URL . $setting['alias'] . '/' . $parameter . $getParams;
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: " . $redirectUrl);
					exit();
				}
			}
		}
		if ($agentOn) {
			Configure::write('BcRequest.agent', $key);
			Configure::write('BcRequest.agentPrefix', $setting['prefix']);
			Configure::write('BcRequest.agentAlias', $setting['alias']);
			break;
		}
	}
}
if ($agentOn) {
	//======================================================================
	// /m/files/... へのアクセスの場合、/files/... へ自動リダイレクト
	// CMSで作成するページ内のリンクは、モバイルでアクセスすると、
	// 自動的に、/m/ 付のリンクに書き換えられてしまう為、
	// files内のファイルへのリンクがリンク切れになってしまうので暫定対策。
	//======================================================================
	$_parameter = preg_replace('/^' . Configure::read('BcRequest.agentAlias') . '\//', '', $parameter);
	if (preg_match('/^files/', $_parameter)) {
		$redirectUrl = FULL_BASE_URL . '/' . $_parameter;
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: " . $redirectUrl);
		exit();
	}
}
/**
 * Viewのキャッシュ設定
 */
if (Configure::read('debug') > 0) {
	Configure::write('Cache.check', false);
	clearViewCache();
} else {
	if (Configure::read('Session.start')) {
		// 管理ユーザーでログインしている場合、ページ機能の編集ページへのリンクを表示する為、キャッシュをオフにする。
		// ただし、現在の仕様としては、セッションでチェックしているので、ブラウザを閉じてしまった場合、一度管理画面を表示する必要がある。
		// TODO ブラウザを閉じても最初から編集ページへのリンクを表示する場合は、クッキーのチェックを行い、認証処理を行う必要があるが、
		// セキュリティ上の問題もあるので実装は検討が必要。
		// bootstrapで実装した場合、他ページへの負荷の問題もある
		if (isset($_SESSION['Auth']['User'])) {
			Configure::write('Cache.check', false);
		}
	}
}