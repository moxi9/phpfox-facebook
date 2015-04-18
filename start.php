<?php

new Core\Event([
	'lib_phpfox_template_getheader' => function(Phpfox_Template $Template) {
			if (!setting('m9_facebook_enabled')) {
				$Template->setHeader('<script>var Fb_Login_Disabled = true;</script>');
				$Template->setHeader('<style>.fb_login_go, #header_menu #js_block_border_user_login-block form > .table:first-of-type:before {display:none !important;} #header_menu #js_block_border_user_login-block .title { margin-bottom: 0px; }</style>');
			}
		}
]);

if (!setting('m9_facebook_enabled')) {
	return;
}

// $Url = new Core\Url();
// if ($Url->route())
// d($Url->route()); exit;

Facebook\FacebookSession::setDefaultApplication(setting('m9_facebook_app_id'), setting('m9_facebook_app_secret'));

$Url = new Core\Url();
if (Phpfox::isUser() && $Url->uri() == '/user/setting/' && substr(Phpfox::getUserBy('email'), -3) == '@fb') {
	(new Core\Route('/user/setting'))->run(function(\Core\Controller $Controller) {
		return $Controller->render('setting.html');
	});
}

(new Core\Route('/fb/login'))->run(function(\Core\Controller $Controller) {
	$helper = new Facebook\FacebookRedirectLoginHelper($Controller->url->make('/fb/auth'));
	$loginUrl = $helper->getLoginUrl();

	header('Location: ' . $loginUrl);
	exit;
});

(new Core\Route('/fb/auth'))->run(function(\Core\Controller $Controller) {
	$helper = new Facebook\FacebookRedirectLoginHelper($Controller->url->make('/fb/auth'));
	$session = $helper->getSessionFromRedirect();

	if ($session) {
		$request = new Facebook\FacebookRequest($session, 'GET', '/me');
		$response = $request->execute();

		$user = $response->getGraphObject(Facebook\GraphUser::className());
		if ($user instanceof Facebook\GraphUser) {
			$Service = new \Apps\Moxi9\Facebook\Model\Service();
			$Service->create($user);

			$Controller->url->send('/');
		}
	}
});