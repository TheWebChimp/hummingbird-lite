<?php
	# Create database connection
	try {
		$db_dsn = sprintf('mysql:dbname=%s;host=%s', $db_name, $db_host);
	    $dbh = new PDO($db_dsn, $db_user, $db_password);
		# Override some defaults
	    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
	    echo 'Connection failed: ' . $e->getMessage();
	}
?>