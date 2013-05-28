<?php
	/**
	 * CMS plugin
	 * Adds CMS capabilities to Humingbird.
	 * Version: 	1.0
	 * Author(s):	biohzrdmx <github.com/biohzrdmx>
	 * ToDo: 		Delete posts
	 * 				Media manager
	 * 				Search
	 * 				Comments
	 * 				User accounts (with Gatekeeper)
	 * 				Taxonomies
	 * 				Internationalization support (with I18N)
	 */

	class CMS {
		protected $post_types;

		/**
		 * Constructor
		 */
		function __construct() {
			global $site;
			# Initialize variables
			$this->post_types = array();
			$locale = '';
			# Register router
			$site->addRoute('/:post', 'CMS::getPage', true);
			$site->addRoute('/:type/:post', 'CMS::getPage', true);
			$site->addRoute('/admin/cms/:page', 'CMS::getAdminPage');
			$site->addRoute('/admin/cms', 'CMS::getAdminPage');
			# Register pages
			$site->addPage('single', 'single-page');
			$site->addPage('archive', 'archive-page');
			# Register default post type
			$this->registerPostType(
				'post',
				array(
					'name' => 'Post',
					'plural_name' => 'Posts',
					'prefix' => false
				)
			);
		}

		/**
		 * Get a single/archive for the current request
		 * @param  array $params 	Router params
		 * @return null 			TRUE if the page was rendered, FALSE otherwise
		 */
		static function getPage($params) {
			global $site;
			global $cms;
			$dbh = $site->getDatabase();
			$post_id = false;

			# Get the corerct parameters
			if ( count($params) < 3) {

				# Request MAY be of type '/post'
				$post_name = $params[1];
				$post_type = false;
			} else {

				# Request MAY be of type '/post_type/post'
				$post_type = $params[1];
				$post_name = $params[2];
			}

			try {

				# Try to get the post by name (slug)
				$sql = "SELECT id, post_type, post_status FROM cms_posts WHERE post_name = :post_name";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':post_name', $post_name);
				$stmt->execute();
				$row = $stmt->fetch();
				if ($row) {

					# Gotcha! we may got a winner, check if the prefix if ok
					$cpt = $cms->getPostType($row->post_type);
					if ( ( $cpt['prefix'] && $post_type == $row->post_type ) ||
						( !$cpt['prefix'] && !$post_type )
					) {

						# Yay! The post's type has a valid prefix
						$post_id = $row->id;
						$site->post_id = $post_id;
						$site->getPage('single', dirname(__FILE__));
					}
				} else {

					# There is no post with such name, this may be an archive page
					if ( array_key_exists($post_name, $cms->post_types) ) {
						$post_id = -1;
						$site->post_type = $post_name;
						$site->getPage('archive', dirname(__FILE__));
					}
				}

			} catch (PDOException $e) {
				// error_log( $e->getMessage() );
			}

			return $post_id;
		}

		/**
		 * Get an administrative page
		 * @param  array $params 	Router parameters
		 * @return boolean         	TRUE if the page was rendered, FALSE otherwise
		 */
		static function getAdminPage($params) {
			global $site;
			$dir = dirname(__FILE__);
			$page = isset( $params[1] ) ? $params[1] : 'manage';
			return $site->getPage($page, $dir, false);
		}

		/**
		 * Install the plugin
		 * @return boolean 		TRUE if installation was successful, FALSE otherwise
		 */
		function install() {
			global $site;
			$dbh = $site->getDatabase();
			$driver = $site->getOption('db_driver');
			if ( $driver == 'mysql' ) {
				$queries = array(
					"CREATE TABLE cms_posts (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, post_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', post_content LONGTEXT NOT NULL, post_title TEXT NOT NULL, post_name VARCHAR(200) NOT NULL, post_excerpt TEXT NOT NULL, post_status VARCHAR(20) NOT NULL DEFAULT 'publish', post_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', post_parent BIGINT UNSIGNED NOT NULL DEFAULT '0', post_type VARCHAR(20) NOT NULL DEFAULT 'post', post_mime_type VARCHAR(100) NOT NULL DEFAULT '', PRIMARY KEY (ID), INDEX post_name (post_name), INDEX type_status_date (post_type, post_status, post_date, ID), INDEX post_parent (post_parent) ) CHARSET UTF8",
					"CREATE TABLE cms_postmeta (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, post_id BIGINT UNSIGNED NOT NULL, meta_key VARCHAR(255) NOT NULL, meta_value LONGTEXT NOT NULL PRIMARY KEY (ID), INDEX post_id (post_id), INDEX meta_key (meta_key), UNIQUE post_meta (post_id, meta_key) ) CHARSET UTF8"
				);
			} else if ( $driver == 'sqlite' ) {
				$queries = array(
					"CREATE TABLE cms_posts (id INTEGER PRIMARY KEY AUTOINCREMENT, post_date TEXT NOT NULL DEFAULT '0000-00-00 00:00:00', post_content TEXT NOT NULL, post_title TEXT NOT NULL, post_name TEXT NOT NULL, post_excerpt TEXT NOT NULL, post_status TEXT NOT NULL DEFAULT 'publish', post_modified TEXT NOT NULL DEFAULT '0000-00-00 00:00:00', post_parent INTEGER NOT NULL DEFAULT '0', post_type TEXT NOT NULL DEFAULT 'post', post_mime_type TEXT NOT NULL DEFAULT '')",
					"CREATE TABLE cms_postmeta (id INTEGER PRIMARY KEY AUTOINCREMENT, post_id INTEGER, meta_key TEXT NOT NULL, meta_value TEXT NOT NULL, UNIQUE(post_id, meta_key) ON CONFLICT REPLACE)"
				);
			} else {
				die('No database driver specified, CMS installation can not continue.');
			}
			# Run each query
			try {
				foreach ($queries as $query) {
					$dbh->exec($query);
				}
				return true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return false;
		}

		/**
		 * Is the plugin installed?
		 * @return boolean		TRUE if the plugin is installed, FALSE otherwise
		 */
		function isInstalled() {
			global $site;
			$dbh = $site->getDatabase();
			$dbh = $site->getDatabase();
			if ($dbh) {
				try {
					$sql = "SELECT COUNT(*) FROM cms_posts";
					$dbh->exec($sql);
					$installed = true;
				} catch (PDOException $e) {
					$installed = false;
				}
			} else {
				$installed = false;
			}
			return $installed;
		}

		/**
		 * Register a new post type
		 * @param  string $slug 	Post type slug
		 * @param  array $opts 		Options for the new post type
		 * @return null
		 */
		function registerPostType($slug, $opts) {
			$this->post_types[ $slug ] = $opts;
		}

		/**
		 * Get the options of a post type
		 * @param  string $slug 	Post type slug
		 * @return array       		Post type options
		 */
		function getPostType($slug) {
			if ( isset( $this->post_types[ $slug ] ) ) {
				return $this->post_types[ $slug ];
			}
			return false;
		}

		/**
		 * Get all registered post types
		 * @return array 			Registered post types
		 */
		function getPostTypes() {
			return count($this->post_types) > 0 ? $this->post_types : false;
		}

		/**
		 * Insert a new post
		 * @param  array $args 		Options for the new post
		 * @return mixed       		New post id or FALSE if there was an error
		 */
		function insertPost($args) {
			global $site;
			$dbh = $site->getDatabase();
			$driver = $site->getOption('db_driver');

			# Get parameters
			$post_date = isset( $args['post_date'] ) ? $args['post_date'] : date('Y-m-d H:i:s');
			$post_content = isset( $args['post_content'] ) ? $args['post_content'] : '';
			$post_title = isset( $args['post_title'] ) ? $args['post_title'] : '';
			$post_name = isset( $args['post_name'] ) ? $args['post_name'] : '';
			$post_excerpt = isset( $args['post_excerpt'] ) ? $args['post_excerpt'] : '';
			$post_status = isset( $args['post_status'] ) ? $args['post_status'] : 'publish';
			$post_modified = isset( $args['post_modified'] ) ? $args['post_modified'] : date('Y-m-d H:i:s');
			$post_parent = isset( $args['post_parent'] ) ? $args['post_parent'] : 'NULL';
			$post_type = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
			$post_mime_type = isset( $args['post_mime_type'] ) ? $args['post_mime_type'] : '';
			if ( empty($post_name) ) {
				$post_name = $site->toAscii($post_title);
			}

			# Now with the query
			try {
				$sql =
					"INSERT INTO cms_posts (
						id,
						post_date,
						post_content,
						post_title,
						post_name,
						post_excerpt,
						post_status,
						post_modified,
						post_parent,
						post_type,
						post_mime_type
					) VALUES (
						:id,
						:post_date,
						:post_content,
						:post_title,
						:post_name,
						:post_excerpt,
						:post_status,
						:post_modified,
						:post_parent,
						:post_type,
						:post_mime_type
					)";

				# Build the query
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $driver == 'sqlite' ? null : 0);
				$stmt->bindValue(':post_date', $post_date);
				$stmt->bindValue(':post_content', $post_content);
				$stmt->bindValue(':post_title', $post_title);
				$stmt->bindValue(':post_name', $post_name);
				$stmt->bindValue(':post_excerpt', $post_excerpt);
				$stmt->bindValue(':post_status', $post_status);
				$stmt->bindValue(':post_modified', $post_modified);
				$stmt->bindValue(':post_parent', $post_parent);
				$stmt->bindValue(':post_type', $post_type);
				$stmt->bindValue(':post_mime_type', $post_mime_type);

				# Run the query
				$stmt->execute();
				return $dbh->lastInsertId();

			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return false;
		}

		/**
		 * Update the specified post
		 * @param  integer $id   	Post id
		 * @param  array $args 		Post options to be updated
		 * @return boolean       	TRUE if the post was updated, FALSE otherwise
		 */
		function updatePost($id, $args) {
			global $site;
			$dbh = $site->getDatabase();
			$driver = $site->getOption('db_driver');

			# Get parameters
			$set = '';
			$args['post_modified'] = date('Y-m-d H:i:s');
			$args['post_name'] = $site->toAscii( $args['post_name'] );
			foreach ($args as $key => $value) {
				$set .= sprintf('%s = :%s, ', $key, $key);
			}
			$set = rtrim($set, ', ');

			# Now with the query
			try {
				$sql =
					"UPDATE cms_posts SET
						$set
					WHERE id = $id";

				# Build the query
				$stmt = $dbh->prepare($sql);
				foreach ($args as $key => $value) {
					$stmt->bindValue(':' . $key, $value);
				}

				# Run the query
				return $stmt->execute();

			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return false;
		}

		/**
		 * Get the number of post for a given post type
		 * @param  string $post_type 	Post type slug
		 * @return mixed             	Number of posts or FALSE on error
		 */
		function getPostCount($post_type) {
			global $site;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT COUNT(id) AS count FROM cms_posts WHERE post_type = :post_type";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':post_type', $post_type);
				$stmt->execute();
				$posts = $stmt->fetch();
				return $posts->count;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return false;
		}

		/**
		 * Query the posts of a given type
		 * @param  array  $args 	Query options (pagination, ordering, etc)
		 * @return mixed       		Array of posts or FALSE if there was an error
		 */
		function getPosts($args = array()) {
			global $site;
			$dbh = $site->getDatabase();
			$post_type = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
			$post_status = isset( $args['post_status'] ) ? $args['post_status'] : '';
			$order_by = isset( $args['order_by'] ) ? $args['order_by'] : 'post_date';
			$order = isset( $args['order'] ) ? $args['order'] : 'desc';
			$limit = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : 10;
			$page = isset( $args['paged'] ) ? $args['paged'] : 1;
			$offset = ($page - 1) * $limit;
			$order = strtoupper($order);
			try {
				if ($page > 0) {
					$pagination = "LIMIT $offset, $limit";
				}
				$sql =
					"SELECT
						id,
						post_date,
						post_content,
						post_title,
						post_name,
						post_excerpt,
						post_status,
						post_modified,
						post_parent,
						post_type,
						post_mime_type
					FROM cms_posts
					WHERE
						post_type = :post_type
						".( !empty($post_status) ? "AND	post_status = :post_status" : '' )."
					ORDER BY $order_by $order $pagination";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':post_type', $post_type);
				if (! empty($post_status) ) {
					$stmt->bindValue(':post_status', $post_status);
				}
				$stmt->execute();
				$posts = $stmt->fetchAll();
				return $posts;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return false;
		}

		/**
		 * Get the specified post
		 * @param  integer $id 		Post id
		 * @return mixed     		Post object or FALSE if there was an error
		 */
		function getPost($id) {
			global $site;
			$dbh = $site->getDatabase();
			try {
				$sql =
					"SELECT
						id,
						post_date,
						post_content,
						post_title,
						post_name,
						post_excerpt,
						post_status,
						post_modified,
						post_parent,
						post_type,
						post_mime_type
					FROM cms_posts
					WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$post = $stmt->fetch();
				return $post;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return false;
		}
	}

	# Instantiate the plugin object
	$cms = new CMS();
?>