<?php
	include_once "./includes/db.php";

	session_start ();

	// check login status
	if ( !empty( $_SESSION['user'] ) )  {
		$user = $_SESSION['user'];

		$db = create_db_connection ();
		
		// if update 
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) 	{

			$wp_address = $_POST['nm_wp_address'];
			$wp_hostname = $_POST['nm_wp_hostname'];
			$wp_apipath = $_POST['nm_wp_apipath'];
			$wp_id = $_POST['nm_wp_id'];
			$wp_password = $_POST['nm_wp_password'];
				
			$query = "REPLACE INTO wp_account SET user='$user', wp_address='$wp_address', wp_hostname='$wp_hostname', wp_apipath='$wp_apipath', wp_id='$wp_id', wp_password='$wp_password'";
				
			$sth = $db->query ( $query );

			if ( $sth == false ) {
				echo json_encode ( array ( 'result_code' => 1, 'message' => 'Couldn\'t update blog account.' ) );
				exit;
			} else {
				echo json_encode ( array ( 'result_code' => 0, $row ) );
				exit;
			}
			
		// if read 
		} else if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$query = "SELECT * FROM wp_account WHERE user='$user'";

			$sth = $db->query ( $query );

			foreach ( $sth->fetchAll() as $row )	{
				echo json_encode ( array ( 'result_code' => 0, $row ) );
				exit;
			}	
			echo json_encode ( array ( 'result_code' => 1, 'message' => 'No matching account.' ) );
		}
	} else {
		exit;
	}
?>
