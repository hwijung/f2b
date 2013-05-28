<?php
	include_once "./includes/db.php";
	include_once "./facebook-php-sdk/facebook.php";

	session_start();

	$fb_user = null;

	// check login status
	if ( !empty( $_SESSION['user'] ) )  {
		$user = $_SESSION['user'];

		$appId = '159335287416086';
		$secret = '02c7d1369768a0a602cac78caebce7d3';
		$site_url = "http://211.51.13.118/f2b/fbaccess.php";
		$logout_url = "http://211.51.13.118/f2b/fblogout.php";
		$fb_name = '';

		$facebook = new Facebook ( array ( 
				'appId' => $appId,
				'secret' => $secret ));

		$fb_user = $facebook->getUser ();

		// If a user already logged in
		if ($fb_user) {
			// Get logout URL
			$params = array ( 'next' => 'http://211.51.13.118/f2b/fblogout.php' );
			$logoutUrl = $facebook->getLogoutUrl( $params );
			$fb_access_token = $facebook->getAccessToken();

			try {
				$fql = 'SELECT name from user where uid = ' . $fb_user;
				$ret_obj = $facebook->api ( array ( 
											'method' => 'fql.query',
											'query' => $fql, ));
			
				$fb_name = $ret_obj[0]['name'];

			} catch ( FacebookApiException $e )	{

			}

			$db = create_db_connection ();

			// $user, $fb_user, $fb_access_token
			$query = "REPLACE INTO fb_account SET user='$user', fb_user='$fb_user', fb_access_token='$fb_access_token', fb_name='$fb_name'";
				
			$sth = $db->query ( $query );

			foreach ( $sth->fetchAll() as $row )	{
				echo json_encode ( $row );
			}
		} else {
			// Get login URL
			$loginUrl = $facebook->getLoginUrl();
		}
	}

	// Call by any explicit request
	if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {

		

	}
?>

<html>
	<head>
		<script type="text/javascript">
			function self_close () {
				var fb_id = "<?php echo $fb_name; ?>";
				
				if ( fb_id != "" )	{
					parent.window.opener.fill_fb_account_form ( fb_id );
				}

				window.close ();
			}

			function logout () {
				var logout_url = "<?php echo $logoutUrl; ?>";

				location.href = logout_url;
			}
		</script>

	</head>
	<body>
<?php 
	if ( $fb_user != null )  { 
?>
	<h4> You are logged in as <?php echo $fb_name; ?></h4>
	<input type="button" value="close" onclick="javascript:self_close ();"></input>
	<input type="button" value="logout" onclick="javascript:logout ();"></input>
<?php
	} else {
		header ( 'Location: ' . $loginUrl );
?>
	<a href="<?php echo $loginUrl?>">Login with Facebook </a>
<!--
		echo "<form action ='$loginUrl'>";
		echo "<input type='submit' value='Login'>";
		echo "</form>";
-->	
<?php
 	}
?>

	</body>
</html>
