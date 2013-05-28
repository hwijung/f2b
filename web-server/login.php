<?php
	include_once "./includes/db.php";
	session_start ();

	if ( !empty($_POST) )	{
		$user = $_POST['usermail'];
		$password = $_POST['password'];

		$db = create_db_connection ();

		$sth = $db->query ( "SELECT user, password FROM user WHERE user='$user' " );

		foreach ( $sth->fetchAll() as $row )	{
			if ( md5($password) === $row['password'] )	{
				$_SESSION['user'] = $user;
				echo json_encode ( array ( 'result_code' => 0, $row ) );
			} else {
				echo json_encode ( array ( 'result_code' => 1, 'message' => 'No matching account' ) );
			}
		}
	} else {
		echo json_encode ( array ( 'result_code' => 2, 'message' => 'Invalid parameters' ) );
	}
?>

