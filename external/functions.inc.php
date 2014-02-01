<?php
	/**
	 * functions.inc.php
	 * Add extra functions in this file
	 */

	# Basic set-up ------------------------------------------------------------

	# Include styles
	$site->registerStyle('sticky-footer', $site->baseUrl('/css/sticky-footer.css') );
	$site->registerStyle('style', $site->baseUrl('/css/style.css') );
	$site->enqueueStyle('style');
	$site->enqueueStyle('sticky-footer');

	# Include scripts
	$site->registerScript('script', $site->baseUrl('/js/script.js'), array('jquery') );
	$site->enqueueScript('script');

	# Include extra files
	include ABSPATH . '/external/utilities.inc.php';
	include ABSPATH . '/external/ajax.inc.php';

	# Meta tags
	$site->addMeta('UTF-8', '', 'charset');
	$site->addMeta('viewport', 'width=device-width, initial-scale=1');

	$site->addMeta('og:title', $site->getPageTitle(), 'property');
	$site->addMeta('og:site_name', $site->getSiteTitle(), 'property');
	$site->addMeta('og:image', $site->urlTo('/favicon.png'), 'property');
	$site->addMeta('og:type', 'website', 'property');
	$site->addMeta('og:url', $site->urlTo('/'), 'property');

	# Localization
	if ( isset($i18n) ) {
		$i18n->addLocale('en', ABSPATH . '/plugins/i18n/lang/enUS.php');
		$i18n->addLocale('es', ABSPATH . '/plugins/i18n/lang/esMX.php');
		$i18n->setLocale('en');
	}

	# Access control
	if ( isset($gatekeeper) ) {
		$gatekeeper->checkLogin();
	}

?>