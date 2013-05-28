<?php
	function create_db_connection () {
		// Create PDO Connection
		$config['dbconnect'] = array (
				'host' => 'localhost',
				'dbname' => 'f2b',
				'username' => 'root',
				'password' => '!qazxsw2'
			);

		try {
			$dbh = new PDO ( 'mysql:host='.$config['dbconnect']['host'].'; dbname='.$config['dbconnect']['dbname'], $config['dbconnect']['username'], $config['dbconnect']['password'] );
		} catch ( PDOException $e )	{
			$e->getMessage ();
		}

		$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		$dbh->setAttribute( PDO::ATTR_CASE, PDO::CASE_LOWER );				

		return $dbh;
	}
?>
