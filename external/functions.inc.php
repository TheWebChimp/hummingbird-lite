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
	$site->registerScript('script', $site->baseUrl('/js/script.js') );
	$site->enqueueScript('jquery');
	$site->enqueueScript('script');

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

	/**
	 * Convert a shorthand byte value from a PHP configuration directive to an integer value
	 * @param    string   $value
	 * @return   int
	 */
	function convert_bytes( $value ) {
		if ( is_numeric( $value ) ) {
			return $value;
		} else {
			$value_length = strlen( $value );
			$qty = substr( $value, 0, $value_length - 1 );
			$unit = strtolower( substr( $value, $value_length - 1 ) );
			switch ( $unit ) {
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}
			return $qty;
		}
	}

?>