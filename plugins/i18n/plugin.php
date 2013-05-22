<?php
	class I18N {
		protected $locales;
		protected $locale;

		/**
		 * Constructor
		 */
		function __construct() {
			global $site;
			#
			$this->locales = array();
			$locale = '';
			#
			$site->routeAdd('/:lang/:page', 'I18N::getPage', true);
			$site->registerHook('baseUrl', 'I18N::localizeUrl');
		}

		/**
		 * Get the specified, localized page
		 * @param  mixed $params         String slug or array with parameters
		 * @param  string $templates_dir Templates folder
		 * @return boolean               TRUE if the page was found, FALSE otherwise
		 */
		static function getPage($params, $templates_dir = '') {
			global $i18n;
			$locale = $params[1];
			if ( is_array($params) ) {
				$params = array_pop($params);
			}
			if ( isset($i18n->locales[ $locale ] ) ) {
				$i18n->setLocale($locale);
			}
			return Site::getPage($params, $templates_dir);
		}

		/**
		 * Listener for the baseUrl hook
		 * @param  string  $path   Path
		 * @param  boolean $echo   Whether to print the result or not
		 * @param  string  $locale Override current locale
		 * @return string          Localized url (if there are any locales)
		 */
		static function localizeUrl($path, $echo = false, $locale = '') {
			global $site;
			global $i18n;
			if ( empty($locale) ) {
				$locale = $i18n->locale;
			}
			#
			$base_url = rtrim($site->base_url, '/');
			if ( isset($_SERVER['HTTPS']) ) {
				$base_url = str_replace('http://', 'https://', $base_url);
			}
			if ( $i18n->locale ) {
				$ret = sprintf('%s/%s%s', $base_url, $locale, $path);
			} else {
				$ret = sprintf('%s%s', $base_url, $path);
			}
			#
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Get localized link
		 * @param  string  $path   Path
		 * @param  boolean $echo   Whether to print the result or not
		 * @param  string  $locale Override current locale
		 * @return string          Localized link to the given path
		 */
		function localizeBaseUrl($path, $echo = false, $locale = '') {
			return self::localizeUrl($path, $echo, $locale);
		}

		/**
		 * Register a new locale
		 * @param string $key         	Locale identifier (es, en, it, etc)
		 * @param string $translation 	The translation file
		 */
		function addLocale($key, $translation) {
			$ret = $this->loadLocale($translation);
			$this->locales[$key] = $this->loadLocale($translation);
		}

		/**
		 * Load a locale from a file
		 * @param  string $translation 	The translation file
		 * @return array              	The array of translation strings
		 */
		function loadLocale($translation) {
			return include($translation);
		}

		/**
		 * Set the current locale
		 * @param string $key 			Locale identifier
		 */
		function setLocale($key) {
			$this->locale = $key;
		}

		/**
		 * Get the current locale
		 * @return string  				The current locale identifier
		 */
		function getLocale() {
			return $this->locale;
		}

		/**
		 * Get the list of registered locales
		 * @return array 				List of registered locales
		 */
		function getLocales() {
			return $this->locales;
		}

		/**
		 * Get specified translation
		 * @param  string $key Translation key
		 * @return string      The specified translation or the key if it wasn't found
		 */
		function getTranslation($key, $echo = true) {
			$ret = $key;
			if (! empty($this->locale) && isset( $this->locales[$this->locale][$key] ) ) {
				$ret = $this->locales[$this->locale][$key];
			}
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}
	}

	# Instantiate the plugin object
	$i18n = new I18N();

	# Register global functions

	/**
	 * Get a translated string and optionally print it
	 * @param  string  $key  Translation key
	 * @param  boolean $echo Whether to print the result or not
	 * @return string        The translated string of its name if it wasn't found
	 */
	function _t($key, $echo = true) {
		global $i18n;
		return $i18n->getTranslation($key, $echo);
	}

	/**
	 * Get a translated selection box and optionally print it
	 * @param  string  $key   Translation key
	 * @param  boolean $echo  Whether to print the result or not
	 * @param  string  $sel   The value of the selected item
	 * @param  array   $attrs Any extra attribute to add to the select tag
	 * @return string         Translated select tag markup
	 */
	function _select($key, $echo = true, $sel = '', $attrs = array()) {
		$options = _t($key, false);
		$attr_text = '';
		foreach ($attrs as $attr => $value) {
			$attr_text .= sprintf(' %s="%s"', $attr, $value);
		}
		$ret = sprintf('<select%s>', $attr_text);
		foreach ($options as $option => $name) {
			$ret .= '<option '.($option == $sel ? 'selected="selected"' : '').'value="'.$option.'">'.$name.'</option>';
		}
		$ret .= '</select>';
		if ($echo) {
			echo $ret;
		}
		return $ret;
	}
?>