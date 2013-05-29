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
				echo json_encode ( array ( 'result_code' => 0, $row ) )
				exit;
			}
		// UPDATE SETTINGS IN DB
		} else if ( $_SERVER['REQUEST_METHOD'] == 'PUT' ) 	{
			$condition = $_POST['periodic_condition'];
			$command = $_POST['command_line'] . $user;

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
			$on_off = $_POST['on_off'];

			$query = "REPLACE INTO cron SET user='$user', periodic_condition='$condition', command_line='$command', on_off='$on_off'";

			$sth = $db->query ( $query );	

			if ( $sth == false ) {
				echo json_encode ( array ( 'result_code' => 1, 'message' => 'Couldn\'t update settings.' ) );
			} else {
				echo json_encode ( array ( 'result_code' => 0, $row ) );
			}	

			if ( $on_off == 0 ) {
				// DEACTIVATE CRONTAB

			} else {
				// ACTIVATE CRONTAB
				
			}	
		}
	}

/*
	$output = shell_exec ( 'ls' );

	echo "<pre> $output </pre>";

http://blog.fayland.org/2011/10/removeadd-job-to-crontab-by-commandline.html
*/	
?>
 