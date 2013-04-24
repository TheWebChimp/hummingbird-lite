<?php
	/**
	 * Sanitize the given string (slugify it)
	 * @param  string $str       The string to sanitize
	 * @param  array  $replace   Optional, an array of characters to replace
	 * @param  string $delimiter Optional, specify a custom delimiter
	 * @return string            Sanitized string
	 */
	function to_ascii($str, $replace = array(), $delimiter = '-') {
		setlocale(LC_ALL, 'en_US.UTF8');
		# Remove spaces
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}
		# Remove non-ascii characters
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		# Remove non alphanumeric characters and lowercase the result
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		# Remove other unwanted characters
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		return $clean;
	}

	/**
	 * Load the specified template parts
	 * @param  mixed $mixed An string or array of parts
	 */
	function get_parts($mixed) {
		# Check parameter type
		if ( is_array($mixed) ) {
			# If is an array we should call this recursively for each part
			foreach($mixed as $part) {
				get_parts($part);
			}
		} else if ( is_string($mixed) ) {
			# If it's an string we just include the file
			global $base_dir;
			$part = sprintf('%s/parts/%s.php', $base_dir, $mixed);
			if (file_exists($part)) {
				# Include the file with all global variables
				extract($GLOBALS, EXTR_REFS | EXTR_SKIP);
				include $part;
				echo "\n";
			}
		}
	}
?>