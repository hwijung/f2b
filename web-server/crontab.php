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
			if ( $sth == false ) {
				echo json_encode ( array ( 'result_code' => 1, 'message' => 'No matching results' ) );
				exit;			
			} else {
				foreach ( $sth->fetchAll() as $row )	{
					echo json_encode ( array ( 'result_code' => 0, $row ) );
				}
				exit;
			}

		// UPDATE SETTINGS IN DB
		} else if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) 	{

			// condition and command are mandatory arguments
			// on_off is optional argument for activating crontab
			$condition = $_POST['periodic_condition'];
			$command = $_POST['command_line'] . " " . $user;
			$on_off = $_POST['on_off'];
			$isActivated = FALSE;

			$query = "REPLACE INTO cron SET user='$user', periodic_condition='$condition', command_line='$command'";
			if ( $on_off  != NULL ) 
				$query =  $query . ", on_off='$on_off'";

			$sth = $db->query ( $query );

			if ( $sth != FALSE ) {
				// UPDATE ON AND OFF SETTING IN DB, 
				// AND UPDATE CRONTAB according to THE ON_OFF
				if ( 0 == $on_off  ) {
					$shell_command = "crontab -l | grep -v '" . $command . "' | crontab -";
					$output = shell_exec ( $shell_command );
					$isActivated = FALSE;
				} else if ( 1 == $on_off  ) {
					$shell_command = "(crontab -l ; echo \"" . $condition . " " . $command . "\") | crontab -";
					$output = shell_exec ( $shell_command );
					$isActivated = TRUE;
				}

				$result_message = "Settings updated.";
				$result_code = 0;

				if ( $isActivated ) {
					$result_message .= " Service activated.";
				} else {
					$result_message .= " Service deactivated.";
				}
			
				echo json_encode ( array ( 'result_code' => $result_code, 
											'message' => $result_message ) );
				exit;
			}
			else {
				echo json_encode ( array ( 'result_code' => 1, 
											'message' => "Couldn\'t update settings.") );
				exit;
			}
		}
	}
?>
 