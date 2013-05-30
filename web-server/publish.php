 <?php
	session_start ();

	if ( !empty( $_SESSION['user'] ) )  {

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			# YYYYMMDDhhmmss
			$since = "20130101000000";
			$before = "20130501000000";
			$shell_command = "ruby /home/root/f2b/f2b.rb " . $_SESSION['user'] . " " . $since . " " . $before;
			$output = shell_exec ( $shell_command );

			echo json_encode ( array ( 'result_code' => 0, 'message' => 'published' ) );
			exit;
		}
	}
	exit;
 ?>
