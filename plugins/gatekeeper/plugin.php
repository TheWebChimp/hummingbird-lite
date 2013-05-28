<?php
	/**
	 * Gatekeeper plugin
	 * User management and access control for Hummingbird.
	 * Version: 	2.0
	 * Author(s):	biohzrdmx <github.com/biohzrdmx>
	 * ToDo:		Port v1.0 functions
	 * 				Make sure createUser sets the correct password
	 */

	require dirname(__FILE__) . '/lib/PasswordHash.php';
	require dirname(__FILE__) . '/lib/StatelessCookie.php';

	class Gatekeeper {

		protected $user_roles;
		protected $user_id;

		function __construct() {
			global $site;
			#
			$this->user_roles = array();
			# Insert routes
			$site->addRoute('/login', 'Gatekeeper::getPage', true);
			$site->addRoute('/logout', 'Gatekeeper::getPage', true);
			$site->addRoute('/admin/users/:page', 'Gatekeeper::getAdminPage');
			$site->addRoute('/admin/users', 'Gatekeeper::getAdminPage');
			$site->addPage('logout');
			$site->addPage('login');
			# Add default user roles
			$this->addUserRole('admin', 'Administrator');
			$this->addUserRole('user', 'User');
		}

		static function getPage($params) {
			global $site;
			$dir = dirname(__FILE__);
			return $site->getPage($params[0], $dir, false);
		}

		static function getAdminPage($params) {
			global $site;
			$dir = dirname(__FILE__);
			$page = isset( $params[1] ) ? $params[1] : 'manage';
			return $site->getPage($page, $dir, false);
		}

		function install() {
			global $site;
			$dbh = $site->getDatabase();
			$driver = $site->getOption('db_driver');
			if ($driver == 'sqlite') {
				$sql = array(
					"CREATE TABLE gk_user (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL, nickname TEXT NOT NULL, password TEXT NOT NULL, registered DATETIME NOT NULL, status INTEGER NULL DEFAULT 1, role TEXT NOT NULL DEFAULT '')",
					"CREATE TABLE gk_user_meta (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, meta_key TEXT NOT NULL, meta_value TEXT NOT NULL, UNIQUE (user_id, meta_key))"
				);
			} else if ($driver == 'mysql') {
				$sql = array(
					"CREATE TABLE gk_user (id BIGINT NOT NULL AUTO_INCREMENT, name VARCHAR(60) NOT NULL, email VARCHAR(64) NOT NULL, nickname VARCHAR(250) NOT NULL, password VARCHAR(180) NOT NULL, registered DATETIME NOT NULL, status INTEGER NULL DEFAULT 1, role VARCHAR(50) NOT NULL DEFAULT '', PRIMARY KEY (id)) CHARACTER SET utf8",
					"CREATE TABLE gk_user_meta (id BIGINT NOT NULL AUTO_INCREMENT, user_id BIGINT NOT NULL, meta_key VARCHAR(255) NOT NULL, meta_value TEXT NOT NULL, PRIMARY KEY (id), UNIQUE KEY user_meta (user_id, meta_key)) CHARACTER SET utf8"
				);
			}
			if ($sql) {
				try {
					foreach ($sql as $query) {
						$stmt = $dbh->prepare($query);
						$stmt->execute();
					}
					return true;
				} catch (PDOException $e) {
				    echo 'Database error: ' . $e->getMessage();
				}
			}
		    return false;
		}

		function isInstalled() {
			global $site;
			$dbh = $site->getDatabase();
			if ($dbh) {
				try {
					$sql = "SELECT COUNT(*) FROM gk_user";
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

		function createUser($name, $email, $nickname, $password = '', $status = 0, $role ='user') {
			global $site;
			$dbh = $site->getDatabase();
			$driver = $site->getOption('db_driver');
			$pass_salt = $site->hashPassword('gk');
			$hasher = new StatelessCookie($pass_salt);
			if (strlen($password) > 72) {
				throw new Exception("Password must be 72 characters or less", 1);
			} else if (strlen($password) == 0) {
				# Generate a new, random password
				// $password = substr( md5( $hasher( get_random_bytes(32) ) ), 0, 8);
			}
			$hashed_password = $hasher->HashPassword($password);
			try {
				if ($driver == 'sqlite') {
					$sql = "INSERT INTO gk_user (name, email, nickname, password, registered, status, role) VALUES (:name, :email, :nickname, :password, :registered, :status, :role)";
				} else if ($driver == 'mysql') {
					$sql = "INSERT INTO gk_user (id, name, email, nickname, password, registered, status, role) VALUES (0, :name, :email, :nickname, :password, :registered, :status, :role)";
				}
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':name', $name);
				$stmt->bindValue(':email', $email);
				$stmt->bindValue(':nickname', $nickname == '' ? $name : $nickname);
				$stmt->bindValue(':password', $hashed_password);
				$stmt->bindValue(':registered', date('Y-m-d h:i:s'));
				$stmt->bindValue(':status', $status);
				$stmt->bindValue(':role', $role);
				$stmt->execute();
				return $dbh->lastInsertId();
			} catch (PDOException $e) {
			    echo 'Database error: ' . $e->getMessage();
			}
			return false;
		}

		function getCurrentUser() {
			return $this->getUser( $this->user_id );
		}

		function getCurrentUserId() {
			return $this->user_id;
		}

		function getUser($id) {
			global $site;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, name, email, nickname, password, registered, status, role FROM gk_user WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				return $stmt->fetch();
			} catch (PDOException $e) {
				// echo 'Database error: ' . $e->getMessage();
			}
			return false;
		}

		function getUserBy($field, $value) {
			global $site;
			$dbh = $site->getDatabase();
			switch ($field) {
				case 'id':
				case 'name':
				case 'email':
				case 'nickname':
					break;
				default:
					throw new Exception("Invalid field specified", 1);
					break;
			}
			try {
				$sql = sprintf("SELECT id, name, email, nickname, password, registered, status, role FROM gk_user WHERE %s = :value", $field);
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':value', $value);
				$stmt->execute();
				return $stmt->fetch();
			} catch (PDOException $e) {
				echo 'Database error: ' . $e->getMessage();
			}
		}

		function updateUser($id, $fields) {
			global $site;
			$dbh = $site->getDatabase();
			$pass_salt = $site->hashPassword('gk');
			$set = '';
			foreach ($fields as $field => $value) {
				$set .= sprintf('%s = :%s, ', $field, $field);
			}
			$set = rtrim($set, ', ');
			$hasher = new StatelessCookie($pass_salt);
			try {
				$sql = sprintf("UPDATE gk_user SET %s WHERE id = %d", $set, $id);
				$stmt = $dbh->prepare($sql);
				foreach ($fields as $field => $value) {
					$stmt->bindValue(sprintf(':%s', $field), $field == 'password' ? $hasher->HashPassword($value) : $value);
				}
				$stmt->execute();
				return $stmt->rowCount();
			} catch (PDOException $e) {
				echo 'Database error: ' . $e->getMessage();
			}
			return false;
		}

		function deleteUser($id) {
			global $site;
			$dbh = $site->getDatabase();
			try {
				$sql = "DELETE FROM gk_user WHERE :id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
			} catch (PDOException $e) {
				echo 'Database error: ' . $e->getMessage();
			}
		}

		function setUserMeta($id, $key, $value) {
			global $site;
			$dbh = $site->getDatabase();
			$driver = $site->getOption('db_driver');
			try {
				$sql = "UPDATE gk_user_meta SET meta_value =  :meta_value WHERE user_id = :user_id AND meta_key = :meta_key";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':user_id', $id);
				$stmt->bindValue(':meta_key', $key);
				$stmt->bindValue(':meta_value', $value);
				$stmt->execute();
				if ($stmt->rowCount() == 0) {
					if ($driver == 'sqlite') {
						$sql = "INSERT INTO gk_user_meta (user_id, meta_key, meta_value) VALUES (:user_id, :meta_key, :meta_value)";
					} else if ($driver == 'mysql') {
						$sql = "INSERT INTO gk_user_meta (id, user_id, meta_key, meta_value) VALUES (0, :user_id, :meta_key, :meta_value)";
					}
					$stmt = $dbh->prepare($sql);
					$stmt->bindValue(':user_id', $id);
					$stmt->bindValue(':meta_key', $key);
					$stmt->bindValue(':meta_value', $value);
					$stmt->execute();
				}
			} catch (PDOException $e) {
				echo 'Database error: ' . $e->getMessage();
			}
		}

		function getUserMeta($id, $key, $value, $default = '') {
			global $site;
			$dbh = $site->getDatabase();
			$sql = "SELECT meta_value FROM gk_user_meta WHERE user_id = :user_id AND meta_key = :meta_key";
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(':user_id', $id);
			$stmt->bindValue(':meta_key', $key);
			$stmt->execute();
			if ( $stmt->fetch() ) {
				return $row->meta_value;
			} else {
				return $default;
			}
		}

		function login($name, $password, $remember = false) {
			global $site;
			$dbh = $site->getDatabase();
			$pass_salt = $site->hashPassword('gk');
			$cookies = new StatelessCookie($pass_salt);
			$sql = "SELECT id, password FROM gk_user WHERE name = :name";
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(':name', $name);
			$stmt->execute();
			if ($row = $stmt->fetch()) {
				$auth = $cookies->login($password, $row->password);
				if ($auth) {
					$url_parts = parse_url( $site->baseUrl() );
					$path = sprintf('%s/', isset($url_parts['path']) ? $url_parts['path'] : '');
				    $cookie = $cookies->buildCookie(strtotime("+12 hour"), $row->id, $auth);
				    $this->user_id = $row->id;
				    return setcookie("gatekeeper", $cookie, strtotime("+12 hour"), '/', $url_parts['host']);
				}
			}
			return false;
		}

		function logout() {
			global $site;
			$url_parts = parse_url( $site->baseUrl() );
			$path = sprintf('%s/', isset($url_parts['path']) ? $url_parts['path'] : '');
			$ret = setcookie("gatekeeper", '', strtotime("+12 hour"), '/', $url_parts['host']);
		    $this->user_id = null;
			return $ret;
		}

		function requireLogin($role = '', $return = '', $headers = true) {
			global $site;
			if ($headers) {
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Cache-Control: no-cache");
				header("Pragma: no-cache");
			}
			if (! $this->checkLogin($role) ) {
				$redirect = sprintf( '/login/?return=%s', ltrim($return, '/') );
				$site->redirectTo( $site->urlTo($redirect) );
			}
		}

		function checkLogin($role = '') {
			global $site;
			$dbh = $site->getDatabase();
			$pass_salt = $site->hashPassword('gk');
			$cookies = new StatelessCookie($pass_salt);
			$cookie = isset( $_COOKIE['gatekeeper'] ) ? $_COOKIE['gatekeeper'] : false;
			if ($cookie) {
		    	$id = $cookies->getCookieData($cookie);
			} else {
				$id = -1;
			}
			try {
			    if ($id >= 0) {
			    	$sql = "SELECT id, password, role FROM gk_user WHERE id = :id";
					$stmt = $dbh->prepare($sql);
					$stmt->bindValue(':id', $id);
					$stmt->execute();
					if ($row = $stmt->fetch()) {
						# Check role
						if ($role != '' && $role != $row->role) {
							return false;
						}
						# Now check the hashed password
					    $result = $cookies->checkCookie($cookie, $row->password);
						if ($result !== false) {
							$this->user_id = $row->id;
			    			return true;
						}
					}
			    }
			} catch (PDOException $e) {
				//
			}
		    return false;
		}

		function getUserRoles() {
			return $this->user_roles;
		}

		function delete_user_role($name) {
			if ( isset( $this->user_roles[$name] ) ) {
				unset($this->user_roles[$name]);
			}
		}

		function addUserRole($name, $label) {
			$this->user_roles[$name] = $label;
		}
	}

	$gatekeeper = new Gatekeeper();
?>