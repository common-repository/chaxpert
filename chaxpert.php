<?php  
    /* 
    Plugin Name: ChaXpert Widget 
    Plugin URI: http://www.chaxpert.com 
    Description: Plugin for displaying the ChaXpert Widget
    Author: M. Pena
    Version: 1.0 
    Author URI: http://chaxpert.com/es_ES/u/59/moncho-pena
    */  
    
   
//chaxpert 
    
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------ MENU DISPLAY FUNCTIONS ------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

//function to add admin page
function chaxpertAdminPage_init() {
	add_action('admin_menu', 'chaxpertAdminPage');
	
	//check if the db needs to be updated
	chaxpertUpdateDBStruct();
}

//function to write admin page and sub pages
function chaxpertAdminPage() {
	add_menu_page('ChaXpert', 'ChaXpert', 8, __FILE__, 'chaxpertCWAdmin');
	add_submenu_page(__FILE__, 'ChaXpert', 'Widget Settings', 8, 'chaxpert-widget', 'chaxpertCWOptions');
	add_submenu_page(__FILE__, 'ChaXpert', 'Change ChaXpert User', 8, 'chaxpert-user', 'chaxpertCWChangeUser');
	add_submenu_page(__FILE__, 'ChaXpert', 'Uninstall', 8, 'chaxpert-uninstall', 'chaxpertCWUninstall');
}


//function to display admin page
function chaxpertCWAdmin() {
	//get options
	$chaxpertUser = get_option('chaxpert_CW_user');
	
	//page contents
	echo '<div class="wrap">';
	echo '<h2>ChaXpert Widget</h2>';
	echo '<p>Welcome <strong>' . $chaxpertUser . '</strong> (<a href="?page=chaxpert-user" title="Change ChaXpert User">Change ChaXpert User</a>)</p>';
	echo '<p>ChaXpert Widget. (<a href="?page=chaxpert-widget" title="Change Widget Settings">Change Widget Settings</a>)</p>';
	echo '</form>';
	
	//display the output table based on user preference
	chaxpertCWDisplay();
	
	//close div
	echo '</div>';
}
    
    
//function to display pre-WP 2.7 widget settings form
function chaxpertCWOptions() {
	//array to hold options for title link
	$chaxpertTitleLinkOpts = array(
		'none' => 'None',
		'page' => 'My ChaXpert Page'
	);
	
			
	//build the form
	echo '<div class="wrap">';
	echo '<h2>ChaXpert Widget Settings</h2>';
	echo '<p>Here is where you can change the settings for the ChaXpert Widget.</p>';
	echo '<div style="display: block;">';
	echo '<form action="options.php" method="post">';
	
	echo '<fieldset style="display: block; float: left; border: 1px solid #aaa; background-color: #eee; padding: 10px; margin: 0 20px 40px 20px; width: 320px;"><legend style="padding: 0 5px; color: #666;">User Settings</legend>';
	echo '<p><label style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;" for="chaxpert_CW_user">ChaXpert User Name:</label><input style="display:block; width: 300px; margin: 10px 0;" type="text" name="chaxpert_CW_user" id="chaxpert_CW_user" value="' . get_option('chaxpert_CW_user') . '" /></p>';
	echo '<p><label style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;" for="chaxpert_CW_password">ChaXpert Password:</label><input style="display:block; width: 290px; margin: 10px 0;" type="password" name="chaxpert_CW_password" id="chaxpert_CW_password" value="' . get_option('chaxpert_CW_password') . '" /></p>';
	echo '</fieldset>';

	echo '<fieldset style="display: block; border: 1px solid #aaa; background-color: #eee; padding: 10px; margin: 40px 20px; width: 320px;"><legend style="padding: 0 5px; color: #666;">Output Settings</legend>';
	echo '<p><label for="chaxpert_CW_title" style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;">Widget Title:</label><input style="display:block; width: 300px; margin: 10px 0;" type="text" name="chaxpert_CW_title" id="chaxpert_CW_title" value="' . get_option('chaxpert_CW_title') . '" /></p>';
		
	//echo '<p><label style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;" for="chaxpert_CW_title_link">Title Link (Select TEST):</label><select style="display:block; width: 300px; margin: 10px 0;" name="chaxpert_CW_title_link" id="chaxpert_CW_title_link">';
	//echo chaxpertWriteSelect($chaxpertTitleLinkOpts, get_option('chaxpert_CW_title_link'));
	//echo '</select></p>';
	
	echo '<p><label for="chaxpert_CW_title" style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;">Background color:</label><input style="display:block; width: 300px; margin: 10px 0;" type="text" name="chaxpert_CW_background_color" id="chaxpert_CW_background_color" value="' . get_option('chaxpert_CW_background_color') . '" /></p>';
	
	echo '<p><label for="chaxpert_CW_inner_background_color" style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;">Inner background color:</label><input style="display:block; width: 300px; margin: 10px 0;" type="text" name="chaxpert_CW_inner_background_color" id="chaxpert_CW_inner_background_color" value="' . get_option('chaxpert_CW_inner_background_color') . '" /></p>';
	
	echo '<p><label for="chaxpert_CW_width" style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;">Width (Min 220px):</label><input style="display:block; width: 300px; margin: 10px 0;" type="text" name="chaxpert_CW_width" id="chaxpert_CW_width" value="' . get_option('chaxpert_CW_width') . '" /></p>';
	
	echo '</fieldset>';
	
	echo '<p style="display: block; clear: both; margin: 20px;" ><input type="submit" value="Save settings" class="button-primary" /><input type="reset" value="Cancel" class="button-primary" /></p>';
	
	//check if the settings api is supported by looking for the register_setting function
	if (!function_exists('register_setting')) {
		//pre WP 2.7 functionality
		wp_nonce_field('update-options');
		echo '<input type="hidden" name="action" value="update" />';
		echo '<input type="hidden" name="page_options" value="chaxpert_CW_user,chaxpert_CW_password,chaxpert_CW_title,chaxpert_CW_title_link,chaxpert_CW_background_color,chaxpert_CW_inner_background_color,chaxpert_CW_width" />';
	} else {
		//WP 2.7+ functionality
		//also reqd for WPMU compatibility
		settings_fields('chaxpert-widget');
	}
	
	echo '</form>';
	echo '</div>';
	echo '</div>';	
}

//function to create sections and fields for WP 2.7+ options form - required for WPMU 2.7 compatibility
function chaxpertCWOptions_init() {	
	
	//register the settings
	register_setting('chaxpert-widget', 'chaxpert_CW_user');
	register_setting('chaxpert-widget', 'chaxpert_CW_password');
	register_setting('chaxpert-widget', 'chaxpert_CW_title');
	register_setting('chaxpert-widget', 'chaxpert_CW_title_link');
	register_setting('chaxpert-widget', 'chaxpert_CW_background_color');
	register_setting('chaxpert-widget', 'chaxpert_CW_inner_background_color');
	register_setting('chaxpert-widget', 'chaxpert_CW_width');	

}

//function to change Twitter User
function chaxpertCWChangeUser() {
	
	//check if the user has submitted the form
	if ($_POST['update_user']) {
		//security check
		check_admin_referer('change-cw-user');
		
		//check that the user has entered a username and password
		$chaxpertUser = $_POST['chaxpert_CW_user'];
		$chaxpertPass = $_POST['chaxpert_CW_password'];
		
		if ($chaxpertUser != '' and $chaxpertPass != '') {
			//update the username
			update_option('chaxpert_CW_user', $chaxpertUser);
			//update the password
			update_option('chaxpert_CW_password', $chaxpertPass);
			//delete current friends from the database
		
			chaxpertGetDate();
			
			//build message
			$chaxpertMessage = 'Your ChaXpert user name and password have been updated. The database has been updated with your prefences for your new user name.';
	} else {
		//check if the username or password have been left blank
		//start output message
		$chaxpertMessage = 'Unable to update your ChaXpert details. Please provide the following:  ';
		
		if ($chaxpertUser == '') {
			$chaxpertMessage .= ' ChaXpert user name. ';
		}
		
		if ($chaxpertPass == '') {
			$chaxpertMessage .= ' ChaXpert password. ';
		}
		
	}

	//display the message
	echo '<div class="updated fade"><p>' . $chaxpertMessage . '</p></div>';

	}
	
	//build the form
	echo '<div class="wrap">';
	echo '<h2>Change ChaXpert User</h2>';
	echo '<p>If you need to change your ChaXpert user name and password, you can do so here.</p>';
	echo '<form action="" method="post">';
	wp_nonce_field('change-cw-user');
	echo '<table class="form-table">';
	echo '<tr><td>';
	echo '<fieldset style="border: 1px solid #aaa; background-color: #eee; padding: 10px; margin 10px; width: 320px;"><legend style="padding: 0 5px; color: #666;">User Settings</legend>';
	echo '<p><label style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;" for="chaxpert_CW_user">ChaXpert User Name:</label><input style="display:block; width: 300px; margin: 10px 0;" type="text" name="chaxpert_CW_user" id="chaxpert_CW_user"';
	if ($_POST['chaxpert_CW_user']) {
		echo ' value="' . $_POST['chaxpert_CW_user'] . '"';
	} else {
		echo ' value="' . get_option('chaxpert_CW_user') . '"';
	}
	echo ' /></p>';
	echo '<p><label style="display:block; width: 300px; margin: 10px 0; padding: 10px 0;" for="chaxpert_CW_password">ChaXpert Password:</label><input style="display:block; width: 290px; margin: 10px 0;" type="password" name="chaxpert_CW_password" id="chaxpert_CW_password"';
	if ($_POST['chaxpert_CW_password']) {
		echo ' value="' . $_POST['chaxpert_CW_password'] . '"';
	} else {
		echo ' value="' . get_option('chaxpert_CW_password') . '"';
	}
	echo ' /></p>';
	echo '</fieldset>';
	echo '</td></tr>';
	echo '<tr><td>';
	echo '<p><input type="submit" value="Save settings" class="button-primary" /><input type="reset" value="Cancel" class="button-primary" /></p>';
	echo '</td></tr></table>';
	echo '<input type="hidden" name="update_user" value="1" />';
	echo '</form>';
	echo '</div>';
	
}
    
    
//function to display uninstall page
function chaxpertCWUninstall() {
	
	//check if the form has been submitted
	if ($_POST['uninstall']) {
		//security check
		check_admin_referer('cw-uninstall');
		
		//delete the various options from the database
		if (delete_option('chaxpert_CW_user')) echo '<p>Deleted chaxpert_CW_user</p>';
		if (delete_option('chaxpert_CW_password')) echo '<p>Deleted chaxpert_CW_password</p>';
		if (delete_option('chaxpert_CW_title')) echo '<p>Deleted chaxpert_CW_title</p>';
		if (delete_option('chaxpert_CW_title_link')) echo '<p>Deleted chaxpert_CW_title_link</p>';
		if (delete_option('chaxpert_CW_ID')) echo '<p>Deleted chaxpert_CW_ID</p>';
		if (delete_option('chaxpert_CW_user_data')) echo '<p>Deleted chaxpert_CW_user_data</p>';
		if (delete_option('chaxpert_CW_background_color')) echo '<p>Deleted chaxpert_CW_background_color</p>';
		if (delete_option('chaxpert_CW_inner_background_color')) echo '<p>Deleted chaxpert_CW_inner_background_color</p>';
		if (delete_option('chaxpert_CW_width')) echo '<p>Deleted chaxpert_CW_width</p>';
		if (delete_option('chaxpert_CW_name')) echo '<p>Deleted chaxpert_CW_name</p>';
				
	}
			
	echo '<div class="wrap">';
	echo '<h2>Uninstall ChaXpert Widget</h2>';
	echo '<p>If you are experiencing problems or simply want to remove the ChaXpert Widget from your WordPress installation, then click the uninstall button.</p>';
	echo '<p><strong>IMPORTANT:</strong> Before uninstalling, please remove the ChaXpert Widget from your <a href="./widgets.php" title="Widgets">sidebar</a>.</p>';
	echo '<p style="color: red; font-weight: bold;">Proceeding with the uninstall will remove all ChaXpert settings, cache settings and cache content.</p>';
	echo '<p style="color: red; font-weight: bold;">Please backup your WordPress database before proceeding.</p>';
	echo '<p style="color: red; font-weight: bold;">Uninstallation Of The "ChaXpert Widget" Settings Cannot Be Undone.</p>';
	echo '<form action="" method="post">';
	wp_nonce_field('cw-uninstall');
	echo '<input type="hidden" name="uninstall" value="1" /><input type="submit" class="button-primary" value="Uninstall ChaXpert" /></form>';
	echo '</div>';
}


//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//---------------------- CHAXPERT API FUNCTIONS ---------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

//function to get friends and followers count from Twitter
function chaxpertGetDate() {

	//get options
	$chaxpertUser = get_option('chaxpert_CW_user');
	$chaxpertPass = get_option('chaxpert_CW_password');
	
	//very important
	$chaxpertApiURL=get_option('chaxpert_CW_apiurl');
	$chaxpertKey=get_option('chaxpert_CW_key');
	
	//need token
			
	//set up the curl options
			
	$data=array(
					'a' => 'login_user',
					'token' => $chaxpertKey,
					'params' => '{"username":"' . $chaxpertUser . '","password":"' . $chaxpertPass . '","remember":"1"}'
				);
			
	$chaxpertCurl = curl_init();
	curl_setopt($chaxpertCurl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($chaxpertCurl, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt($chaxpertCurl, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($chaxpertCurl, CURLOPT_URL, $chaxpertApiURL);
	curl_setopt($chaxpertCurl, CURLOPT_POSTFIELDS, $data);
	
	//get the contents
	$chaxpertLoginUser = curl_exec($chaxpertCurl);
	$chaxpertLoginUser=json_decode($chaxpertLoginUser);
			
	//update the options in the database
	update_option('chaxpert_CW_token', $chaxpertLoginUser->token_user);
	update_option('chaxpert_CW_ID', $chaxpertLoginUser->id_user);
	update_option('chaxpert_CW_name', $chaxpertLoginUser->name . ' ' . $chaxpertLoginUser->surname);
	
	$chaxpertToken=get_option('chaxpert_CW_token');
	$chaxpertID=get_option('chaxpert_CW_ID');
	
	
	if ($chaxpertToken!='' && $chaxpertID>0) {
	

			$data=array(
							'a' => 'user',
							'token' => $chaxpertKey,
							'params' => '{"id_user":"' . $chaxpertID . '"}'
						);
			
			$chaxpertCurl = curl_init();
			curl_setopt($chaxpertCurl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($chaxpertCurl, CURLOPT_BINARYTRANSFER, 1);
			curl_setopt($chaxpertCurl, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($chaxpertCurl, CURLOPT_URL, $chaxpertApiURL);
			curl_setopt($chaxpertCurl, CURLOPT_POSTFIELDS, $data);
	
			//get the contents
			$chaxpertDataUser = curl_exec($chaxpertCurl);
			
			update_option('chaxpert_CW_user_data', $chaxpertDataUser);
			
			return true;
	
	} else {
	
		//API doesn´t works or User Pass Incorrect ... !!!
		update_option('chaxpert_CW_user_data', '');
		return false;
	
	}
	
	
}

//function to display the output in a table
function chaxpertCWDisplay() {
	
	$chaxpertDataUserResult=get_option('chaxpert_CW_user_data');
	
	if ($chaxpertDataUserResult!='') {
	
		$temp=json_decode($chaxpertDataUserResult);
		$url_name=trim(strtolower($temp->name)) . '-' . trim(strtolower($temp->surname));
		$chaxpertDataUserResultShow='<a href="http://chaxpert.com/es_ES/u/' . $temp->id_user . '/' . $url_name . '" target="_blank">' . $temp->name . ' ' . $temp->surname . '</a>';
		
	} else {
	
		$chaxpertDataUserResultShow="Something goes wrong. Check username and password. <a href='http://chaxpert.com/es_ES/suggestions/' target='_blank'>Suggestions</a>";
	
	}
	
	
	//containing div
	echo '<div style="display: block; width: 100%; margin: 20px auto; padding: 5px;">' . "\n";
	
	//display the date

	print_r($chaxpertDataUserResultShow);
	
	
	echo '</div>';

}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------- DATABASE FUNCTIONS ---------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

//function to update the database structure

function chaxpertUpdateDBStruct() {

	//Update always
	update_option('chaxpert_CW_apiurl', 'http://chaxpert.com/apiweb/version1/api_mobile.php');
	update_option('chaxpert_CW_key', '3fc6a63d69a8b920e78f957acdfe36e9b2d64219');

	$test_chaxpert_CW_title=get_option('chaxpert_CW_title');
	$test_chaxpert_CW_background_color=get_option('chaxpert_CW_background_color');
	$test_chaxpert_CW_inner_background_color=get_option('chaxpert_CW_inner_background_color');
	$test_chaxpert_CW_width=get_option('chaxpert_CW_width');

	if ($test_chaxpert_CW_title=='') {
		update_option('chaxpert_CW_title', 'My ChaXpert Widget');
	}
	if ($test_chaxpert_CW_background_color=='') {
		update_option('chaxpert_CW_background_color', '#e6e6e6');
	}
	if ($test_chaxpert_CW_inner_background_color=='') {
		update_option('chaxpert_CW_inner_background_color', '#000000');
	}
	if ($test_chaxpert_CW_width=='') {	
		update_option('chaxpert_CW_width', '300');
	}

}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//--------------------------- MISC. FUNCTIONS ----------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

//function to create drop down list
//takes 2 parameters - array of options and selected option
function chaxpertWriteSelect($chaxpertOptionArray, $chaxpertDefault) {
	
	//loop through the array of options
	foreach ($chaxpertOptionArray as $chaxpertOptKey => $chaxpertOptValue) {
		//check if the current option is the default
		if ($chaxpertOptKey == $chaxpertDefault) {
			$chaxpertSelect .= '<option value="' . $chaxpertOptKey . '" selected="selected">' . $chaxpertOptValue . '</option>';
		} else {
			$chaxpertSelect .= '<option value="' . $chaxpertOptKey . '">' . $chaxpertOptValue . '</option>';
		}
	}
	
	//return the html
	return $chaxpertSelect;
}


function chaxpertShortcode() {

//queda bastante más pero para empezar
$html_iframe='';
$html_iframe.='<iframe id="chaxpertWidget" src="http://chaxpert.com/widget/?id=' . get_option('chaxpert_CW_ID') . '&w=' . get_option('chaxpert_CW_width') . '&language=es_ES&bgcolor=' . get_option('chaxpert_CW_background_color') . '&bgcontent=' . get_option('chaxpert_CW_inner_background_color') . '" width="' . get_option('chaxpert_CW_width') . '" height="350" border="0" onload="document.getElementById(\'chaxpertWidget\').height = window.frames.chaxpertWidget.document.body.offsetHeight"></iframe>';

echo $html_iframe;

}

//function to write styles to Admin Pages header
function chaxpertWriteAdminStyles() {
	echo '<!-- Styles for ChaXpert -->';
	echo '<style type="text/css">' . "\n";
	echo '.chaxpertCWContainAdmin { border: 1px solid #666; background-color: #aaa; padding: 0; margin: 5px; width: 150px; height: 95px; display: inline-block; float: left; }' . "\n";
	echo '.chaxpertCWBlockAdmin { display: block; margin: 5px 63px; }' . "\n";
	echo '.chaxpertCWImgAdmin { margin: 0 auto; padding: 0; width: 24px; height: 24px; }' . "\n";
	echo '.chaxpertCWNameAdmin { display: block; text-align: center; margin: 0; padding: 5px 0 0 0 ; background-color: #666; color: #eee; width: 100%; height: 20px; border-top: 1px solid #666; overflow: hidden; border-bottom: 1px solid #666; }' . "\n";
	echo '.chaxpertCWFormAdmin { text-align: center; background-color: #aaa; padding: 5px 0; }' . "\n";
	echo '* html .chaxpertCWContainAdmin { display: inline; }' . "\n";
	echo '* + html .chaxpertCWContainAdmin { display: inline; }' . "\n";
	echo '</style>' . "\n";
}


//function to add links on plugins page
function chaxpert_CW_add_links($links) { 
 // Add a link to this plugin's settings page
 $settings_link = '<a href="admin.php?page=chaxpert-widget">Settings</a>';
 array_unshift($links, $settings_link); 
 return $links; 
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//-------------------------- WIDGET FUNCTIONS ----------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

//function to initiate the widget
function widget_CW_init() {
	//check that WP supports widgets
	if (!function_exists('register_sidebar')) {
		return;
	}
	
	function chaxpertWidgetDisplay() {
		chaxpertShortcode();
	}

	//function to display widget control panel
	function chaxpertWidgetControl() {
		echo '<p>The settings for ChaXpert can be changed from the <a href="admin.php?page=chaxpert-widget" title="ChaXpert Widget Settings">ChaXpert Settings Page</a></p>';
	}
	
	//register the widget and control
	register_sidebar_widget('ChaXpert', 'chaxpertWidgetDisplay');
	register_widget_control('ChaXpert', 'chaxpertWidgetControl');
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
//-------------------------- WORDPRESS ACTIONS ---------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

//initiate plugin
add_action('plugins_loaded', 'chaxpertAdminPage_init');
//initiate widget
add_action('widgets_init', 'widget_CW_init');
//add shortcode
add_shortcode('chaxpert', 'chaxpertShortcode');
//write styles to admin header
add_action('admin_head', 'chaxpertWriteAdminStyles');
//action for WP 2.7 settings api
add_action('admin_init', 'chaxpertCWOptions_init');
//add links for settings etc to plugins page
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'chaxpert_CW_add_links' );
    
?>