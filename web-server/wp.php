<?php
	include_once "./includes/db.php";

	session_start ();

	// check login status
	if ( !empty( $_SESSION['user'] ) )  {
		$user = $_SESSION['user'];

		$db = create_db_connection ();
		
		// if update 
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) 	{

			$method = $_POST['method'];
			 
			// account
			if ( $method == 'account' ) {
				syslog ( LOG_ERR, "wp_category: " + $_POST['wp_category'] );

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
					echo json_encode ( array ( 'result_code' => 0, 'message' => 'Account information saved.' ) );
					exit;
				}

			// category
			} else if ( $method == 'category' ) {
				syslog ( LOG_ERR, "wp_category: " + $_POST['wp_category'] );

				$wp_category = $_POST['wp_category']; 

				$query = "UPDATE wp_account SET wp_category='$wp_category' WHERE user='$user'";
				$sth = $db->query ( $query );

				if ( $sth == false ) {
					echo json_encode ( array ( 'result_code' => 1, 'message' => 'Couldn\'t update category.' ) );
					exit;
				} else {
					echo json_encode ( array ( 'result_code' => 0, 'message' => 'Category information saved.' ) );
					exit;
				}
			} else if ( $method == 'template' ) {
				$wp_title = $_POST['wp_title'];
				$wp_header = $_POST['wp_header'];
				$wp_entry = $_POST['wp_entry'];
				$wp_footer = $_POST['wp_footer'];

				$query = "REPLACE INTO wp_template SET user='$user', wp_title='$wp_title', wp_header='$wp_header', wp_entry='$wp_entry', wp_footer='$wp_footer'";
				$sth = $db->query ( $query );

				if ( $sth == false )	{
					echo json_encode( array ( 'result_code' => 1, 'message' => 'Couldn\'t update template' ) );
					exit;
				}  else  {
					echo json_encode( array ( 'result_code' => 0, 'message' => 'Template updated.') );
					exit;
				}
			}
				
		// if read 
		} else if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {

			$method = $_GET['method'];

			if ( $method == 'account' ) {
				$query = "SELECT * FROM wp_account WHERE user='$user'";

				$sth = $db->query ( $query );

				foreach ( $sth->fetchAll() as $row )	{
					echo json_encode ( array ( 'result_code' => 0, $row ) );
					exit;
				}	
				echo json_encode ( array ( 'result_code' => 1, 'message' => 'No matching account.' ) );
				exit;
			} else if ( $method == 'categories' ) {
				$output = shell_exec ( "ruby /home/root/f2b/getCategories.rb " . $user );
				echo json_encode ( array ( 'result_code' => 0, 'categories' => $output ) );
				exit;
			} else if ( $method == 'template' ) {
				$query = "SELECT * FROM wp_template WHERE user='$user'";

				$sth = $db->query ( $query );

				foreach ( $sth->fetchAll() as $row )	{
					echo json_encode ( array ( 'result_code' => 0, $row ) );
					exit;
				}	
				echo json_encode ( array ( 'result_code' => 1, 'message' => 'No matching account.' ) );
				exit;			
			}
		}
	} else {
		exit;
	}
?>
