<?php
/*
Plugin Name: UserSumo - Improve Your Conversion Rates
Plugin URI: https://usersumo.com
Description: UserSumo uses social proof to skyrocket your conversion rates by showing that your product is in demand in real time, sending them a strong buying signal.
Version: 0.1
Author: UserSumo
Author Email: support@usersumo.com
License:

  Copyright 2011 UserSumo (support@usersumo.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

class UserSumo {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'UserSumo';
	const slug = 'usersumo';
	
	/**
	 * Constructor
	 */
	function __construct() {
		//register an activation hook for the plugin
		register_activation_hook( __FILE__, array( &$this, 'install_usersumo' ) );

		//Hook up to the init action
		add_action( 'init', array( &$this, 'init_usersumo' ) );
	}
  
	/**
	 * Runs when the plugin is activated
	 */  
	function install_usersumo() {
		// do not generate any output here
		
		
	}
  
	/**
	 * Runs when the plugin is initialized
	 */
	function init_usersumo() {
		// Setup localization
		load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		// Load JavaScript and stylesheets
		$this->register_scripts_and_styles();

	
		if ( is_admin() ) {
			//this will run when in the WordPress admin
		} else {
			//this will run when on the frontend
		}

		/*
		 * TODO: Define custom functionality for your plugin here
		 *
		 * For more information: 
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		//register settings
		add_action( 'admin_init', array( &$this, 'my_plugin_settings' ) );
		
		
		//tell wordpress to register the demolistposts shortcode
		add_shortcode("usersumo-goal", array(&$this, "goalshortcode"));
		
		add_action( 'get_footer', array( &$this, 'embed_usersumo_script' ) );
		add_action('admin_menu', array( &$this, 'test_plugin_setup_menu') );
		add_filter( 'your_filter_here', array( &$this, 'filter_callback_method_name' ) );    
	}

	function goalshortcode($atts) {
	  $atts = shortcode_atts(
		array(
			'id' => '1',
		), $atts, 'usersumo' );;
	  echo "<script type='text/javascript'>if (typeof _us == \"object\") _us.track_goal(" . $atts['id'] . ");else var _usersumo_track_goal = " . $atts['id'] . ";</script>";
	}

	
	function test_plugin_setup_menu(){
		add_menu_page( 'UserSumo Plugin', 'UserSumo', 'manage_options', 'usersumo', array( &$this, 'test_init' ) );
	}
 
	function my_plugin_settings() {
		register_setting( 'usersumo-settings-group', 'user_id' );
		
	}
	
	function test_init(){
	echo "<h1>UserSumo Admin</h1>
	<h2>Enter your UserSumo ID. Don't have an account yet? Sign up for a free 14 day trial 
		<a href='https://usersumo.com'>here</a></h2>
		<p>You can find your UserSumo ID at <a href='https://usersumo.com/member/widgets/'>https://usersumo.com/member/widgets/</a></p>
	    <form method=\"post\" action=\"options.php\">";
	echo    settings_fields( 'usersumo-settings-group' );
	echo    do_settings_sections( 'usersumo-settings-group' );
	echo    "<table class=\"form-table\">
		<tr valign=\"top\">
		<th scope=\"row\">User ID</th>
		<td><input type=\"text\" name=\"user_id\" value=\"";
	 echo esc_attr( get_option('user_id') );
	 echo "\" /></td>
		</tr>
	    </table>
	    <p>To track goals you can use the [usersumo goal='ID'] shortcode. For instance if you wanted to track sign ups for an newsletter subscription, add the shortcode to your thank you page e.g. [usersumo goal='3']</p>
	    <p>Goal IDs can be found on the Goal page in the UserSumo dashboard <a href='https://usersumo.com/member/goals/'>https://usersumo.com/member/goals/</a>";
	    submit_button();
	
	}

	function embed_usersumo_script() {
		// TODO define your action method here
		$user_id = get_option('us_user_id');
		echo "<!--Start of UserSumo Script-->
		<script type=\"text/javascript\">
		(function() {
		    var _us = document.createElement('script'); _us.type = 'text/javascript'; _us.async = true;
		    _us.src = 'https://usersumo.com/widget/user/";
		 echo esc_attr( get_option('user_id') );
		 echo "';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(_us, s);
		})();
		</script>";
		
	}

	function filter_callback_method_name() {
		// TODO define your filter method here
	}
  
	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			$this->load_file( self::slug . '-admin-script', '/js/admin.js', true );
			$this->load_file( self::slug . '-admin-style', '/css/admin.css' );
		} else {
			$this->load_file( self::slug . '-script', '/js/widget.js', true );
			$this->load_file( self::slug . '-style', '/css/widget.css' );
		} // end if/else
	} // end register_scripts_and_styles
	
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') ); //depends on jquery
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	} // end load_file
  
} // end class
new UserSumo();

?>
