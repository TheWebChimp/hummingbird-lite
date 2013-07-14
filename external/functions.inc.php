<?php
	/**
	 * functions.inc.php
	 * Add extra functions in this file
	 */

	# Basic set-up ------------------------------------------------------------

	# Include styles
	$site->registerStyle('style', $site->baseUrl('/css/style.css') );
	$site->enqueueStyle('style');

	# Include scripts
	$site->enqueueScript('jquery');

	# Include extra files
	include ABSPATH . '/external/ajax.inc.php';

	# Localization
	if ( isset($i18n) ) {
		$i18n->addLocale('en', ABSPATH . '/plugins/i18n/lang/enUS.php');
		$i18n->addLocale('es', ABSPATH . '/plugins/i18n/lang/esMX.php');
		$i18n->setLocale('en');
	}

	# Additional helper functions ---------------------------------------------

	/**
	 * Pretty-print an array or object
	 * @param  mixed $a Array or object
	 */
	function print_a( $a ) {
		print( '<pre>' );
		print_r( $a );
		print( '</pre>' );
	}

?>