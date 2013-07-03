<?php
	/**
	 * functions.inc
	 * Add any extra utility functions in this file
	 */

	# Include styles
	$site->registerStyle('style', $site->baseUrl('/css/style.css') );
	$site->enqueueStyle('style');

	# Include scripts
	$site->enqueueScript('jquery');

	# Sample AJAX action
	function ajax_test() {
		echo '<pre>'.print_r($_REQUEST, true).'</pre>';
		exit;
	}
	$site->addAjaxAction('test', 'ajax_test');

	# Localization
	$i18n->addLocale('en', ABSPATH . '/plugins/i18n/lang/enUS.php');
	$i18n->addLocale('es', ABSPATH . '/plugins/i18n/lang/esMX.php');
	$i18n->setLocale('en');

	# Additional helper functions

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