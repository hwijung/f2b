<?php
	include_once "./includes/db.php";
	session_start ();

	if ( !empty($_POST) )	{
		$user = $_POST['usermail'];
		$password = $_POST['password'];
		$password_md5 = md5($password);

		$db = create_db_connection ();

		$sth = $db->query ( "INSERT INTO user VALUES ( '$user', '$password_md5') " );

		if ( $sth )	{
			$sth->execute ( PDO::FETCH_BOTH );
			echo json_encode ( $sth );
		} else {
			exit;
		}

	} else {
		exit;
	}
?>

