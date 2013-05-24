<?php
	include_once "./includes/db.php";

	session_start ();

	// check login status
	if ( !empty( $_SESSION['user'] ) )  {
		$user = $_SESSION['user'];

		$db = create_db_connection ();
		
		// if update 
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) 	{

			$wp_address = $_POST['wp_address'];
			$wp_id = $_POST['wp_id'];
			$wp_password = $_POST['wp_password'];
				
			$query = "REPLACE INTO wp_account SET user='$user', wp_address='$wp_address', wp_id='$wp_id', wp_password='$wp_password'";
				
			$sth = $db->query ( $query );

			foreach ( $sth->fetchAll() as $row )	{
				echo json_encode ( $row );
				exit;
			}
		// if read 
		} else if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$query = "SELECT * FROM wp_account WHERE user='$user'";

			$sth = $db->query ( $query );

			foreach ( $sth->fetchAll() as $row )	{
				echo json_encode ( $row );
				exit;
			}		
		}
	} else {
		exit;
	}
?>
