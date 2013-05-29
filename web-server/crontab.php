<?php
	include_once "./includes/db.php";

	session_start ();

	// check login status
	if ( !empty( $_SESSION['user'] ) )  {

		$user = $_SESSION['user'];
		$db = create_db_connection ();

		// RETURN SETTINGS IN DB
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) 	{
			$query = "SELECT * FROM cron WHERE user='$user'";

			$sth = $db->query ( $query );
			foreach ( $sth->fetchAll() as $row )	{
				if ( $sth == false ) {
					echo json_encode ( array ( 'result_code' => 1, 'message' => 'No matching results' ) );
					exit;				
				} else {
					echo json_encode ( array ( 'result_code' => 0, $row ) );
					exit;
				}
			}
		// UPDATE SETTINGS IN DB
		} else if ( $_SERVER['REQUEST_METHOD'] == 'PUT' ) 	{
			$condition = $_PUT['periodic_condition'];
			$command = $_PUT['command_line'] . " " . $user;

			$query = "REPLACE INTO cron SET user='$user', periodic_condition='$condition', command_line='$command', on_off='$on_off'";

			$sth = $db->query ( $query );

			if ( $sth == false ) {
				echo json_encode ( array ( 'result_code' => 1, 'message' => 'Couldn\'t update settings.' ) );
				exit; 
			} else {
				echo json_encode ( array ( 'result_code' => 0, $row ) );
				exit;
			}
		// UPDATE ON AND OFF SETTING IN DB, 
		// AND UPDATE CRONTAB according to THE ON_OFF
		} else if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) 	{
			$condition = "";
			$command = "";
			$on_off = $_POST['on_off'];

			$query_update = "REPLACE INTO cron SET user='$user', on_off='$on_off'";

			$sth = $db->query ( $query_update );	

			if ( $sth == false ) {
				echo json_encode ( array ( 'result_code' => 1, 'message' => 'Couldn\'t update settings.' ) );
			} else {
				echo json_encode ( array ( 'result_code' => 0, $row ) );
			}	

			$query_select = "SELECT * FROM cron WHERE user='$user'";

			$settings = $db->query ( $query_select );
			
			if ( $settings != FALSE ) {
				foreach ( $settings->fetchAll() as $row )	{
					$condition = $row['periodic_condition'];
					$command = $row['command_line'];
				}

				if ( $on_off == 0 ) {
					// DEACTIVATE CRONTAB
					$shell_command = "crontab -u apache -l | grep -v '" . $command . "' | crontab -u apache -";

					$output = shell_exec ( $shell_command );
				} else {
					// ACTIVATE CRONTAB
					if ( $condition != "" && $command != "" ) {
						$shell_command = "(crontab -u apache -l ; echo \"" . $condition . " " . $command . "\") | crontab -u apache -";

						$output = shell_exec ( $shell_command );
					}
				}	
			}
		}
	}
?>
 