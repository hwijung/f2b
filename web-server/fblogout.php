<?php
	include_once "./includes/db.php";
	include_once "./facebook-php-sdk/facebook.php";

	session_start();

	if ( !empty( $_SESSION['user'] ) )  {

		$appId = '159335287416086';
		$secret = '02c7d1369768a0a602cac78caebce7d3';

		$facebook = new Facebook ( array ( 
				'appId' => $appId,
				'secret' => $secret, 
				'cookie' => true ));

		$fb_key = 'fbs_159335287416086';
		setcookie ( $fb_key, '', '', '', '/', '' );
		$facebook->destroySession ();

		// Clean DB
		$user = $_SESSION['user'];
		$db = create_db_connection ();

		// $user, $fb_user, $fb_access_token
		$query = "DELETE FROM fb_account WHERE user='$user'";
				
		$sth = $db->query ( $query );

		foreach ( $sth->fetchAll() as $row )	{
			echo json_encode ( $row );
		}
	}
?>

<html>
	<head>
		<script type="text/javascript">
			function self_close () {
				parent.window.opener.fill_fb_account_form ( "" );
				window.close ();
			}
		</script>

	</head>
	<body>

		You are logged out.
		<input type="button" value="close" onclick="javascript:self_close ();"></input>

	</body>
</html>
