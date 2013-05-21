<?php
	/**
	 * Site class
	 */
	class Site {
		public $base_url;
		protected $base_dir;
		protected $routes;
		protected $actions;
		protected $scripts;
		protected $styles;
		protected $slugs;
		protected $params;
		protected $pages;
		protected $plugins;
		protected $site_title;
		protected $page_title;
		protected $pass_salt;
		protected $token_salt;
		protected $hooks;
		protected $filters;
		protected $dbh;

		/**
		 * Class constructor
		 */
		function __construct($settings) {
			# Load settings
			$opts = $settings[PROFILE];
			$this->base_dir = ABSPATH;
			$this->base_url = $opts['site_url'];
			# Create arrays
			$this->routes = array();
			$this->actions = array();
			$this->scripts = array();
			$this->styles = array();
			$this->enqueued_scripts = array();
			$this->enqueued_styles = array();
			$this->slugs = array();
			$this->params = array();
			$this->pages = array();
			$this->hooks = array();
			$this->plugins = $opts['plugins'];
			# Initialize variables
			$this->pass_salt = $settings['shared']['pass_salt'];
			$this->token_salt = $settings['shared']['token_salt'];
			$this->site_title = $settings['shared']['site_name'];
			$this->page_title = $this->site_title;
			# Add routes
			$this->routeAdd('/:page', 'Site::getPage');
			$this->routeAdd('/ajax', 'Site::ajaxRequest', true);
			# Add pages
			$this->pageAdd('home', 'home-page');
			# Register base scripts
			$this->registerScript('jquery', $this->baseUrl('/js/jquery-1.9.1.min.js') );
			$this->registerScript('jquery.form', $this->baseUrl('/js/jquery.form.js') );
			$this->registerScript('jquery.cycle', $this->baseUrl('/js/jquery.cycle.all.js') );
			$this->registerScript('underscore', $this->baseUrl('/js/underscore.js') );
			$this->registerScript('backbone', $this->baseUrl('/js/backbone.js') );
			# Create database connection
			try {
				switch ( $opts['db_driver'] ) {
					case 'sqlite':
						$dsn = sprintf('sqlite:%s', $opts['db_file']);
						$this->dbh = new PDO($dsn);
						break;
					case 'mysql':
						$dsn = sprintf('mysql:host=%s;dbname=%s', $opts['db_host'], $opts['db_name']);
						$this->dbh = new PDO($dsn, $opts['db_user'], $opts['db_pass']);
						break;
				}
				# Change error and fetch mode
				if ($this->dbh) {
					$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
		}

		/**
		 * Get the specified page
		 * @param  mixed $params         String with slug or array with parameters
		 * @param  string $templates_dir Override default template dir
		 * @return boolean               TRUE if the page was found, FALSE otherwise
		 */
		static function getPage($params, $templates_dir = '') {
			global $site;
			if ( empty($templates_dir) ) {
				$templates_dir = $site->base_dir;
			}
			if ( is_array($params) ) {
				$slug = isset( $params[1] ) ? $params[1] : 'home';
			} else {
				$slug = $params;
			}
			$template = isset($site->pages[$slug]) ? $site->pages[$slug] : 'page';
			$page = sprintf('%s/pages/%s.php', $templates_dir, $template);
			if ( (! isset($site->pages[$slug]) ) || !file_exists($page) ) {
				# The page does not exist
				$slug = '404';
				$site->addBodyClass('error-404');
				$page = sprintf('%s/pages/%s.php', $site->base_dir, $slug);
				header('HTTP/1.0 404 Not Found');
			} else {
				$site->addBodyClass($slug . '-page');
			}
			# Include the file
			include $page;
			return true;
		}

		/**
		 * Handle AJAX request
		 */
		static function ajaxRequest() {
			global $site;
			$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			if ( isset( $site->actions[$action] ) ) {
				call_user_func( $site->actions[$action] );
			} else {
				echo 0;
				exit;
			}
		}

		/**
		 * Get base folder
		 * @param  string  $path Path to append
		 * @param  boolean $echo Whether to print the resulting string or not
		 * @return string        The well-formed path
		 */
		function baseDir($path = '', $echo = false) {
			$ret = sprintf('%s%s', $this->base_dir, $path);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Get base URL
		 * @param  string  $path Path to append
		 * @param  boolean $echo Whether to print the resulting string or not
		 * @return string        The well-formed URL
		 */
		function baseUrl($path = '', $echo = false) {
			$base_url = rtrim($this->base_url, '/');
			if ( isset($_SERVER['HTTPS']) ) {
				$base_url = str_replace('http://', 'https://', $base_url);
			}
			$ret = sprintf('%s%s', $base_url, $path);
			#
			$trans = $this->executeHook('baseUrl', $path);
			if ($trans) {
				$ret = $trans;
			}
			#
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Add a new route
		 * @param  string  $route     Parametrized route
		 * @param  string  $functName Handler function name
		 * @param  boolean $insert    If set, the route will be inserted at the beginning
		 */
		function routeAdd($route, $functName, $insert = false) {
			if ($insert) {
				$this->routes = array_reverse($this->routes, true);
			    $this->routes[$route] = $functName;
			    $this->routes = array_reverse($this->routes, true);
			} else {
				$this->routes[$route] = $functName;
			}
		}

		/**
		 * Process current request
		 * @return boolean TRUE if routing has succeeded, FALSE otherwise
		 */
		function routeRequest() {
			# Routing stuff, first get the site url
			$site_url = trim($this->base_url, '/');

			# Remove the protocol from it
			$domain = preg_replace('/^(http|https):\/\//', '', $site_url);

			# Now remove the path
			$segments = explode('/', $domain, 2);
			if (count($segments) > 1) {
				$domain = array_pop($segments);
			}

			# Get the request and remove the domain
			$request = trim($_SERVER['REQUEST_URI'], '/');
			$request = str_replace($domain, '', $request);
			$request = ltrim($request, '/');

			# Get the parameters
			$segments = explode('?', $request);
			if (count($segments) > 1) {
				$params_str = array_pop($segments);
				parse_str($params_str, $this->params);
			}

			# And the segments
			$cur_route = array_shift($segments);
			$segments = explode('/', $cur_route);

			# Now make sure the current route begins with '/' and doesn't end with '/'
			$cur_route = '/' . $cur_route;
			$cur_route = rtrim($cur_route, '/');

			if ( empty($cur_route) ) {
				$cur_route = '/home';
			}

			# And try to match the route with the registered ones
			$matches = array();
			foreach ($this->routes as $route => $handler) {
				# Compile route into regular expression
				$a = preg_replace('/[\-{}\[\]+?.,\\\^$|#\s]/', '\\$&', $route); // escapeRegExp
				$b = preg_replace('/\((.*?)\)/', '(?:$1)?', $a);                // optionalParam
				$c = preg_replace('/(\(\?)?:\w+/', '([^\/]+)', $b);             // namedParam
				$d = preg_replace('/\*\w+/', '(.*?)', $c);                      // splatParam
				$pattern = "~^{$d}$~";
				if ( preg_match($pattern, $cur_route, $matches) == 1) {
					# We've got a winner, render the page and exit the loop
					call_user_func($handler, $matches);
					break;
				}
			}
		}

		/**
		 * Load the specified template parts
		 * @param  mixed $mixed An string or array of parts
		 */
		function getParts($mixed, $parts_dir = '') {
			# Check parameter type
			if ( is_array($mixed) ) {
				# If is an array we should call this recursively for each part
				foreach($mixed as $part) {
					$this->getParts($part);
				}
			} else if ( is_string($mixed) ) {
				# If it's an string we just include the file
				if ($parts_dir == '') {
					$part = sprintf('%s/parts/%s.php', $this->base_dir, $mixed);
				} else {
					$part = sprintf('%s/%s.php', $parts_dir, $mixed);
				}
				if (file_exists($part)) {
					global $site;
					# Include the file
					include $part;
					echo "\n";
				}
			}
		}

		/**
		 * Get the current slug list
		 * @param  boolean $echo Whether to print the result or not
		 * @return string        String with space-delimited slugs
		 */
		function bodyClass($echo = true) {
			$ret = implode(' ', $this->slugs);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Append a class to the body classes array
		 * @param string $class CSS class name
		 */
		function addBodyClass($class) {
			$this->slugs[] = $class;
		}

		/**
		 * Add a new page to the whitelist
		 * @param  string $slug     Page slug
		 * @param  string $template Page template name (without extension)
		 */
		function pageAdd($slug, $template = '') {
			if ( empty($template) ) {
				$template = $slug;
			}
			$this->pages[$slug] = $template;
		}

		/**
		 * Sanitize the given string (slugify it)
		 * @param  string $str       The string to sanitize
		 * @param  array  $replace   Optional, an array of characters to replace
		 * @param  string $delimiter Optional, specify a custom delimiter
		 * @return string            Sanitized string
		 */
		function toAscii($str, $replace = array(), $delimiter = '-') {
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
		 * Redirect to given route
		 * @param  string $route Route to redirect to
		 */
		function redirectTo($route) {
			if ( preg_match('/^(http:\/\/|https:\/\/).*/', $route) !== 1 ) {
				$url = sprintf('%s/%s', rtrim($this->base_url, '/'), ltrim($route, '/'));
			} else {
				$url = $route;
			}
			$header = sprintf('Location: %s', $url);
			header($header);
			exit;
		}

		/**
		 * Get a well formed url to the specified route or page slug
		 * @param  string  $route Route or page slug
		 * @param  boolean $echo  Whether to print out the resulting url or not
		 * @return string         The resulting url
		 */
		function urlTo($route, $echo = false) {
			$url = sprintf('%s/%s', rtrim($this->base_url, '/'), ltrim($route, '/'));
			if ($echo) {
				echo $url;
			}
			return $url;
		}

		/**
		 * Add an stylesheet to the list
		 * @param  string $name Name of the stylesheet
		 * @param  string $url  URL to the stylesheet (absolute)
		 */
		function registerStyle($name, $url) {
			$this->styles[$name] = $url;
		}

		/**
		 * Add an script to the list
		 * @param  string $name Name of the script
		 * @param  string $url  URL to the script (absolute)
		 */
		function registerScript($name, $url) {
			$this->scripts[$name] = $url;
		}

		/**
		 * Output a well-formed stylesheet link tag to the specified stylesheet
		 * @param  string $name Name of the stylesheet
		 */
		function enqueueStyle($name) {
			$this->enqueued_styles[] = $name;
		}

		/**
		 * Output a well-formed script tag to the specified script
		 * @param  string $name Name of the script
		 */
		function enqueueScript($name) {
			$this->enqueued_scripts[] = $name;
		}

		/**
		 * Output all the registered stylesheets
		 */
		function includeStyles() {
			foreach ($this->enqueued_styles as $style) {
				if ( isset( $this->styles[$style] ) ) {
					$output = sprintf('<link rel="stylesheet" type="text/css" href="%s">', $this->styles[$style]);
					echo($output."\n");
				}
			}
		}

		/**
		 * Output all the registered scripts
		 */
		function includeScripts() {
			foreach ($this->enqueued_scripts as $script) {
				if ( isset( $this->scripts[$script] ) ) {
					$output = sprintf('<script type="text/javascript" src="%s"></script>', $this->scripts[$script]);
					echo($output."\n");
				}
			}
		}

		/**
		 * Set the page title
		 * @param string $title New page title
		 */
		function setPageTitle($title) {
			$this->page_title = $title;
		}

		/**
		 * Return page title with optional prefix/suffix
		 * @param  string $prefix    Prefix to prepend
		 * @param  string $suffix    Suffix to append
		 * @param  string $separator Separator character
		 * @return string            Formatted and escaped title
		 */
		function getPageTitle($prefix = '', $suffix = '', $separator = '-') {
			$ret = $this->page_title;
			if (! empty($prefix) ) {
				$ret = sprintf('%s %s %s', $prefix, $separator, $ret);
			}
			if (! empty($suffix) ) {
				$ret = sprintf('%s %s %s', $ret, $separator, $suffix);
			}
			return htmlentities($ret);
		}

		/**
		 * Get the site name
		 * @param  boolean $echo Print the result?
		 * @return string        Site name
		 */
		function getSiteTitle($echo = false) {
			$ret = $this->site_title;
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Check if the current request was made via AJAX
		 * @return boolean Whether the request was made via AJAX or not
		 */
		function isAjaxRequest() {
			return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		}

		/**
		 * Add a new AJAX action and register its handler function
		 * @param string $action    Action slug
		 * @param string $functName Callback function name
		 */
		function addAjaxAction($action, $functName) {
			$this->actions[$action] = $functName;
		}

		/**
		 * Get registered plugins
		 * @return array Array of registered plugins
		 */
		function getPlugins() {
			return $this->plugins;
		}

		/**
		 * Hash the specified token
		 * @param  string  $action Action name
		 * @param  boolean $echo   Whether to output the resulting string or not
		 * @return string          The hashed token
		 */
		function hashToken($action, $echo = false) {
			$ret = md5($this->token_salt.$action);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Hash the specified password
		 * @param  string  $password 	Plain-text password
		 * @param  boolean $echo   		Whether to output the resulting string or not
		 * @return string          		The hashed password
		 */
		function hashPassword($password, $echo = false) {
			$ret = md5($this->pass_salt.$password);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Register a hook listener
		 * @param  string  $hook      Hook name
		 * @param  string  $functName Callback function name
		 * @param  boolean $prepend   Whether to add the listener at the beginning or the end
		 */
		function registerHook($hook, $functName, $prepend = false) {
			if (! isset( $this->hooks[$hook] ) ) {
				$this->hooks[$hook] = array();
			}
			if ($prepend) {
				array_unshift($this->hooks[$hook], $functName);
			} else {
				array_push($this->hooks[$hook], $functName);
			}
		}

		/**
		 * Execute a hook (run each listener incrementally)
		 * @param  string $hook   	Hook name
		 * @param  mixed  $params 	Parameter to pass to each callback function
		 * @return mixed          	The processed data or the same data if no callbacks were found
		 */
		function executeHook($hook, $param = '') {
			if ( isset( $this->hooks[$hook] ) ) {
				$hooks = $this->hooks[$hook];
				$ret = true;
				foreach ($hooks as $hook) {
					$ret = call_user_func($hook, $param);
				}
				return $ret;
			}
			return false;
		}
	}
?>