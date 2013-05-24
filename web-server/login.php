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
				echo json_encode ( $row );
			} else {
				exit;
			}
		}
	} else {
		exit;
	}
?>

