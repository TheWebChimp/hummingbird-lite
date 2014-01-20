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