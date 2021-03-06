<!DOCTYPE HTML>
<?php
	session_start ();

	if ( !empty($_SESSION['user']) ) {
		$isLogin = 1;
	} else {
		$isLogin = 0;
	}

?>

<html lang="en">
	<head>
		<title>Facebook to Wordpress, Daily backup</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
		<meta http-equiv="Content-Language" content="en-us"> 

		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<link href="./css/main.css" rel="stylesheet" type="text/css">
		<link href="./css/style.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>

   		<script type="text/javascript">
   			$( function () {
   				$("#id_accordian").accordion ();
   				$( document ).tooltip();
   			} );

   			function show_message ( title, msg ) {
   				$("#id_notification_bar_title").html ( title );
   				$("#id_notification_bar_message").html ( msg );

   				$("#id_notification_bar").fadeIn ( 300, function () {
   					$("#id_notification_bar").fadeTo ( 2000, 0.9, function () {
   						$("#id_notification_bar").fadeOut ( 1000 ) ;  }  )  } );
   			}

   			function switch_settings_availability ( flag ) {
   				if ( flag ) {
					$(".cs_settings button").removeAttr ( "disabled" );
					$(".cs_settings textarea" ).removeAttr ( "disabled" );
   				} else {
   					$(".cs_settings button").attr ( "disabled", "true" );
   					$(".cs_settings textarea" ).attr ( "disabled", "true");
   				}
   			}

   			function switch_login_logout_button ( flag )	{
   				if ( flag ) {
   					$("#id_button_login").hide ();
   					$("#id_button_logout").show ();
   				} else {
      				$("#id_button_logout").hide ();
   					$("#id_button_login").show ();					
   				}
   			}

   			function login () {
   				$.post ( 'login.php', { usermail: $("#id_text_login_usermail").val (), password: $("#id_text_login_password").val () } , function ( result ) {
					if ( result['result_code'] == 0 )	{
						switch_to_login_state ();

						show_message ( "Login success", "You are successfully logged in to the service." );
					} else {
						show_message ( "Login failed", result['message'] );
					}
				}, 'json' );

				// update for login failure
				return false;
   			}

   			function switch_to_login_state () {
   				// login success
				$("#id_text_login_usermail").addClass ( "loggedin" ).attr ( "disabled", "true" );
				$("#id_text_login_password").addClass ( "loggedin" ).attr ( "disabled", "true" );

				// update forms
				fill_fb_account_form ();
				fill_wp_account_form ();
				fill_template ();

				switch_login_logout_button ( true );
				switch_settings_availability ( true );
				sync_activate_buttons ( true );		

				get_wp_categories ();
   			}

			function clean_up_login_form ()	{
				$("#id_text_login_usermail").focus ().val ("");
				$("#id_text_login_password").val ("");
			}  
  
			function clean_up_fb_account_form () {
				$("#id_text_fb_name").val ( "" );
			}

			function fill_fb_account_form ( fb_name ) {
				if ( fb_name == null )	{
					$.get ( 'fb.php', null, function ( result ) {
						$("#id_text_fb_name").val ( result['fb_name'] );
					}, 'json' );
				} else {
					$("#id_text_fb_name").val ( fb_name );
				}
			}

			function clean_up_wp_account_form () {
				$("#id_text_wp_id").val ( "" );
				$("#id_text_wp_address").val ( "" );
				$("#id_text_wp_password").val ( "" );		
				$("#id_text_wp_hostname").val ( "" );
				$("#id_text_wp_apipath").val ( "" );		
			}  
 
			function fill_wp_account_form () {
				$.get ( 'wp.php', { method: 'account' }, function ( result ) {
					var sentence = " is selected as a category for publishing.";

					$("#id_text_wp_address").val ( result[0]['wp_address'] );
					$("#id_text_wp_hostname").val ( result[0]['wp_hostname'] );
					$("#id_text_wp_apipath").val ( result[0]['wp_apipath'] );
					$("#id_text_wp_id").val ( result[0]['wp_id'] );
					$("#id_text_wp_password").val ( result[0]['wp_password'] );
					$("#id_p_category").html ( result[0]['wp_category'] + sentence );
					$("#id_form_wp_account input").attr( 'disabled', 'true' );
				}, 'json' );
			}

			function sync_activate_buttons ( force ) {
				if ( force == false ) {
					$("#id_button_activate").attr ( "disabled", "true" );
					$("#id_button_deactivate").attr ( "disabled", "true" );
				} else {
					$.get ( 'crontab.php', null, function ( result ) {
						if ( result[0]['on_off'] == 0 ) {
							$("#id_button_activate").removeAttr ( "disabled" );
							$("#id_button_deactivate").attr ( "disabled", "true" );
						} else if ( result[0]['on_off'] == 1 ) {
							$("#id_button_activate").attr ( "disabled", "true" );
							$("#id_button_deactivate").removeAttr ( "disabled" );
						} 
					}, 'json' );
				}
			}

			function sync_category () {
				$.get ( 'wp.php', { method: 'account' }, function ( result ) {
					var sentence = " is selected as a category for publishing.";
					$("#id_p_category").html ( result[0]['wp_category'] + sentence );
				}, 'json' );
			}

			function get_wp_categories () {
				$.get ( 'wp.php', { method: 'categories' }, function ( result ) {
					var categories = jQuery.parseJSON ( result['categories'] );
					
					if ( categories != null )	{		
						$("#id_category").html ( "" );		
						for (var i = 0; i < categories.length; i++ ) {
							$("#id_category").append('<option value=' + i + '>' + categories[i]['categoryName'] + '</option>');
						} 
					}
				}, 'json' );
			}

			function fill_template () {
				$.get ( 'wp.php', { method: 'template' }, function ( result ) {
					var sentence = " is selected as a category for publishing.";

					$("#id_textarea_title").val ( result[0]['wp_title'] );
					$("#id_textarea_header").val ( result[0]['wp_header'] );
					$("#id_textarea_entry").val ( result[0]['wp_entry'] );
					$("#id_textarea_footer").val ( result[0]['wp_footer'] );
				}, 'json' );
			}

			function save_template () {
				var post_variables = { method: 'template',
										wp_title: $("#id_textarea_title").val (), 
										wp_header: $("#id_textarea_header").val (),
										wp_entry: $("#id_textarea_entry").val (),
										wp_footer: $("#id_textarea_footer").val () 	};
				$.post ( 'wp.php', post_variables, function ( result ) { 
					if ( result['result_code'] == 0 )	{
						show_message ( "Saved.", result['message'] );
					} else {
						show_message ( "Failed", result['message'] );
					}			
				}, 'json' );
			}

			function preview_template () {
				var contents = 	$('#id_textarea_entry').val ();
				$('#id_div_preview').html( contents );
 			}

			$( function () {
				$( "#id_from" ).datepicker ( {
					defaultDate: "+1w",
					changeMonth: true,
					numberofMonths: 1,
					onClose: function ( selectedDate ) {
					}
				});
				$( "#id_to" ).datepicker ( {
					defaultDate: "+1w",
					changeMonth: true,
					numberofMonths: 1,
					onClose: function ( selectedDate ) {
					}
				});
			});
			
			$(document).ready ( function () {

				// Login form animation
				$("#id_text_login_usermail").focus( function() { 
					if ( $("#id_text_login_usermail").val () == 'Usermail' )	{
						$("#id_text_login_usermail").val ('');
					} 
					$(".cs_user_icon").css("left","12px");
				});
				$("#id_text_login_usermail").blur(function() {
					$(".cs_user_icon").css("left","50px");
				});
	
				$("#id_text_login_password").focus(function() {
					if ( $("#id_text_login_password").val () == 'Password' )	{
						$("#id_text_login_password").val ( '' );
					}
					$(".cs_pass_icon").css("left","12px");
				});
				$("#id_text_login_password").blur(function() {
					$(".cs_pass_icon").css("left","50px");
				});

				$("#id_button_register").click ( function () {
					$("#id_form_signin").fadeIn ( 'slow' );
				} );

				$("#id_button_signin_cancel").click ( function () {
					$("#id_form_signin").fadeOut ( 'slow' );
				});

				// Signin form animation
				$("#id_text_signin_usermail").focus ( function () {
					if ( $("#id_text_signin_usermail").val () == 'Email address' ) {
						$("#id_text_signin_usermail").val ( '' );
					}
				} );

				$("#id_text_signin_password").focus ( function () {
					if ( $("#id_text_signin_password").val () == 'password' ) {
						$("#id_text_signin_password").val ( '' );
					}
				});

				$("#id_text_signin_password_confirm").focus ( function () {
					if ( $("#id_text_signin_password_confirm").val () == 'password' ) {
						$("#id_text_signin_password_confirm").val ( '' );
					}
				});

				// When login button clicked 
				$("#id_text_login_password").keydown ( function ( event ) { if ( event.which == 13 ) { login (); get_wp_categories (); }});
				$("#id_button_login").click ( function ()	{ login (); get_wp_categories ();});

				// When logout button clicked
				$("#id_button_logout").click ( function () {
					$.post ( 'logout.php' );
					$("#id_text_login_usermail").removeClass ( "loggedin" ).removeAttr ( "disabled" );
					$("#id_text_login_password").removeClass ( "loggedin" ).removeAttr ( "disabled" );

					clean_up_login_form ();
					clean_up_wp_account_form ();
					clean_up_fb_account_form ();	

					switch_login_logout_button ( false );
					switch_settings_availability ( false );

					sync_activate_buttons ( false );

					show_message ( "Logout success", "You are successfully logged out." );		
				});

				// When sign in button clicked
				$("#id_button_signin_create").click ( function ()	{
					$.post ( 'signin.php', { usermail: $("#id_text_signin_usermail").val (), password: $("#id_text_signin_password").val () },
						function ( result ) {
							if ( result )	{
								show_message ( "Account Created", "Your account is ready. Please login with your account information." );		
							}
						}, 'json' );

					// update for signin failure
					return false;
				});

				// When fb Login button clicked
				$("#id_button_login_fb_account").click ( function ()	{
					var result = window.showModalDialog ( "./fbaccess.php");

					return false;
				});

				// When wp edit button clicked
				$("#id_button_edit_wp_account").click ( function () {
					$("#id_form_wp_account input").removeAttr( 'disabled' );
				});

				// When wp save button clicked
				$("#id_form_wp_account").submit ( function () {
					var post_variables = $("#id_form_wp_account").serialize () + "&method=account";
					$.post ( 'wp.php', post_variables, function ( result ) {
						if ( result['result_code'] == 0 ) {
							show_message ( 'Saved', 'Wordpress account updated successfully.' );
							$("#id_form_wp_account input").attr( 'disabled', 'true' );
						} else {
							show_message ( 'Save Failed', result['message'] );
						}
					}, 'json' );
					return false;	
				});

				// When activate button clicked
				$("#id_button_activate").click ( function () {
					$.post ( 'crontab.php', { periodic_condition: "1 0 * * *",
								command_line: "ruby /home/root/f2b/f2b.rb", on_off: 1 }, 
						function ( result ) { 
							show_message ( 'Update result', result['message'] );
							sync_activate_buttons ();
						}, 'json' );
					return false;
				} );

				// When deactivate button clicked
				$("#id_button_deactivate").click ( function () {
					$.post ( 'crontab.php', { periodic_condition: "1 0 * * *",
								command_line: "ruby /home/root/f2b/f2b.rb", on_off: 0 }, 
						function ( result ) { 
							show_message ( 'Update result', result['message'] );
							sync_activate_buttons ();
						}, 'json' );
					return false;
				} );

				// When force button clicked
				$("#id_button_force").click ( function () {
					$.post ( 'publish.php', { from : $("#id_from").val (), to: $("#id_to").val() }, function ( result ) {
						show_message ( 'Publishing complete', 'Facebook messages are published to your Wordpress blog.' );
					}, 'json' );
					return false;
				} );

				// When category save button clicked
				$("#id_button_save_category").click ( function () {
					var txt_category = $("#id_category option:selected").text ();
					$.post ( 'wp.php', { wp_category: txt_category, method: 'category' }, 
					function ( result ) {
						if ( result['result_code'] == 0 ) {
							sync_category ();
							show_message ( 'Category saved', 'Category selected and saved.' );
						} else {
							show_message ( 'Category save failed.', result['message'] );
						}
					}, 'json' );
					return false;
				});

				// When category update button clicked
				$("#id_button_update_category").click ( function () {
					get_wp_categories ();
					show_message ( 'Category update', 'trying to update categories of blog.');
				});

				// When Save temaplte button clicked 
				$("#id_button_save_template").click ( function () {
					// Save template to database
					save_template ();
					preview_template ();
				});

				// Check login state 
				if ( <?php echo $isLogin ?> == 1 ) {
					$("#id_text_login_usermail").val ( "<?php echo $_SESSION['user']?>" );
					switch_to_login_state ();
				}
			});
		</script>
	</head>

	<body>
		<header class="cs_header_logo">
			<div id="id_header_logo"></div>
			<a href="http://211.51.13.118/f2b/" style="text-decoration:none;"><h1>Facebook to Wordpress</h1></a>
			<p>This service write daily summerize of your Facebook account.</p>
		</header>

		<div id="id_notification_bar" hidden="true">
			<h1 id="id_notification_bar_title"></h1> 
			<p id="id_notification_bar_message"></p>
		</div>

		<div id="id_sidebar">
			<!--SLIDE-IN ICONS-->
		   <div class="cs_user_icon"></div>
		   <div class="cs_pass_icon"></div>		

		   <form name="nm_form_login" class="cs_form_login" action="">
		   		<div class="cs_header_login">
		   			<h1>Login</h1>
		   			<span>Input your ID and Password.</span>
		   		</div>
		 	   		
		  		<div class="cs_content_login">
					<input id="id_text_login_usermail" name="nm_text_usermail" type="text" class="cs_text_usermail" value="Usermail" />
				    <input id="id_text_login_password" name="nm_text_password" type="password" class="cs_text_password" value="Password" />
				</div>
					    
				<!--FOOTER-->
				<div class="cs_footer_login">
				    <input id="id_button_register" type="button" name="submit" value="Register" class="cs_button_register" />					
					<input id="id_button_login" action="" type="button" name="submit" value="Login" class="cs_button_login" />
					<input id="id_button_logout" action="" type="button" name="logout" value="Logout" class="cs_button_logout" hidden= "true"/>
				</div>
		   	</form>

		   	<form id="id_form_signin" name="nm_form_signin" class="cs_form_signin" action="" hidden="true">
		   		<div class="cs_header_signin">
		   			<h1>Sign in</h1>
		   			<span>create new account.</span>
		   		</div>
		   		<div class="cs_content_signin">
		 	   		<input id="id_text_signin_usermail" name="nm_text_usermail" type="text" class="cs_text_usermail" value="Email address" />
		 	   		<input id="id_text_signin_password" name="nm_text_password" type="password" class="cs_text_password" value="password" />
		 	   		<input id="id_text_signin_password_confirm" name="mn_text_password_confirm" type="password" class="cs_text_password" value="password" />
			   	</div>

			   	<div class="cs_footer_signin">
			   		<input id="id_button_signin_cancel" type="button" name="cancel" value="Cancel" class="cs_button_signin_cancel"/>
			   		<input id="id_button_signin_create" type="button" name="submit" value="Create" class="cs_button_signin_create"/>
			   	</div>
		 	</form>
		</div>

		<!-- Edit settings -->

		<div id="id_settings" class="cs_settings">
			<h1>Your settings</h1>
			<div id="id_accordian">
				<h3> Accounts </h3>
				<div id="id_settings_accounts" class="cs_settings_accounts">
		
					
					<h4>Facebook account</h4>
					<p>Click the login button below and type your facebook username and password. Access key for your facebook contents will be issued ans we save it associated with your F2B service accounts. </p>
					<form id="id_form_accounts" accept-charset="utf-8">
						<label>Facebook ID</label>
						<input type="text" id="id_text_fb_name" name="nm_text_fb_name" class="cs_text_settings" required disabled>
						<button id="id_button_login_fb_account" class="cs_button_login_fb_account" disabled>Login</button>
					</form>
						
					<h4>Wordpress account</h4>
					<p>Click Edit button and input your wordpress account information. F2B service logged in your authorization schemes everyday. </p>
					<form id="id_form_wp_account" accept-charset="utf-8">
						<button id="id_button_edit_wp_account" type="button" disabled>Edit</button>
						<button id="id_button_save_wp_account" disabled>Save</button><br/>
						<label>Blog Address</label>
						<input type="text" id="id_text_wp_address" name="nm_wp_address" class="cs_text_settings" style="display:inline-block;width: 300px;" required disabled><br/>
						<label>Host Name</label>
						<input type="text" id="id_text_wp_hostname" name="nm_wp_hostname" class="cs_text_settings" style="display:inline-block;width: 300px;" required disabled><br/>
						<label>API Path </label>
						<input type="text" id="id_text_wp_apipath" name="nm_wp_apipath" class="cs_text_settings" style="display:inline-block;width: 300px;" required disabled><br/>					
						<label>User ID</label>
						<input type="text" id="id_text_wp_id" name="nm_wp_id" class="cs_text_settings" required disabled><br/>
						<label>Password</label>
						<input type="password" id="id_text_wp_password" name="nm_wp_password" class="cs_text_settings" required disabled><br/>
					</form>
				</div>

				<h3> Publishing </h3>
				<div id="id_settings_publishing" class="cs_settings_publishing">
					
					<h4> Periodic publishing</h4>
					<p>Turn on or turn off the periodical publishing. This service automatically posts your facebook entries onto your blog at the start of everyday.</p>
					<button id="id_button_activate" style="margin-left:20px;" disabled>Activate</button>
					<button id="id_button_deactivate" disabled>Deactivate</button>
					<h4> Force publishing</h4>
					<p>First, set time range where your facebook words are included. And then click Force Publishing button to publish them right now.</p>
					<label>From</label>
					<input type="text" id="id_from" name="from" class="cs_text_settings"/><br/>
					<label>To</label>
					<input type="text" id="id_to" name="to" class="cs_text_settings"/>
					<button id="id_button_force" style="margin-left:20px;" disabled>Force Publishing</button>
					<h4>Category</h4>
					<p>The blog category where this service create new posts. </p>
					<label>Category</label>
					<select id="id_category" name="category"></select>
					<button id="id_button_save_category" disabled>Save</button>
					<button id="id_button_update_category" disabled>Update</button>
					<p id="id_p_category"></p>
				</div> 
			
				<h3> Templates </h3>
				<div id="id_settings_templates" class="cs_settings_templates">
					<p>Edit each part of your post template in HTML form.</p>
					<label>Title</label>
					<textarea id="id_textarea_title" title="This is the title of your blog posts. '[## YEAR ##]' will be replaced with the actual year of posting date. '[## MONTH ##], [## DAY ##] are month and day of posting date, respectively." disabled></textarea><br/>
					<label>Header</label>
					<textarea id="id_textarea_header" style="height:30px;" title="This is the header of your blog posts." disabled> </textarea><br/>	
					<label>Entry</label>
					<textarea id="id_textarea_entry" style="height:50px;" title="This is main part of your blog posts." disabled> </textarea><br/>		
					<label>Footer</label>
					<textarea id="id_textarea_footer" style="height:30px;" title="This is the footer of your blog posts." disabled> </textarea><br/>	
					<button id="id_button_save_template" style="margin-left:60px;margin-top:10px;" disabled>Save</button><br/><br/>
					<label>Preview</label>
					<div id="id_div_preview" style="height:50px;"> </div>
				</div>
			</div>
		</div>

		<footer>
			<div id="id_footer_line"></div>
			<nav>
      			<p>
           			<a href="/tos.html">Terms of Service</a> —
           			<a href="http://www.linus.pe.kr/home/wordpress">Blog</a>
           		</p>
    		</nav>

    		<p>Copyright © 2013 <a href="mailto:hwijung.ryu@gmail.com">Hwijung Ryu</a></p>
		</footer>
	</body>
</html>
