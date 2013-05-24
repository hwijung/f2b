<?php
	include_once "./includes/db.php";

	session_start ();

	// check login status
	if ( !empty( $_SESSION['user'] ) )  {
		$user = $_SESSION['user'];

		$db = create_db_connection ();

		// if update 
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) 	{

		// if read 
		} else if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$query = "SELECT * FROM fb_account WHERE user='$user'";

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
