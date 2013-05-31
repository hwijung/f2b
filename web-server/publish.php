 <?php
	session_start ();

	if ( !empty( $_SESSION['user'] ) )  {

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			# YYYYMMDDhhmmss
			$from = $_POST['from'];
			$to = $_POST['to'];

			$from = SUBSTR ( $_POST['from'], 6, 4 ) . SUBSTR ( $_POST['from'], 0, 2 ) . SUBSTR ( $_POST['from'], 3, 2 ) . "000000";
			$to = SUBSTR ( $_POST['to'], 6, 4 ) . SUBSTR ( $_POST['to'], 0, 2 ) . SUBSTR ( $_POST['to'], 3, 2 ) . "000000";
			
			$shell_command = "ruby /home/root/f2b/f2b.rb " . $_SESSION['user'] . " " . $from . " " . $to . " >> /home/root/f2b/force.log;
			$output = shell_exec ( $shell_command );

			echo json_encode ( array ( 'result_code' => 0, 'message' => 'published' ) );
			exit;
		}
	}
	exit;
 ?>
