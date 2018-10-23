<?php
/*
Plugin Name: Jam3 Cookie Banner Plugin
Description: Creates cookie based user notice banner
Author: Jam3
Version: 1.0
*/

/**
 * Current version of plugin
 */
define( 'JAM3_COOKIE_PLUGIN_VERSION', '1.0' );

/**
 * Filesystem path to plugin
 */
define( 'JAM3_COOKIE_PLUGIN_BASE_DIR', dirname( __FILE__ ) );
define( 'JAM3_COOKIE_PLUGIN_BASE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Define min WordPress Version
 */
define( 'JAM3_COOKIE_PLUGIN__MINIMUM_WP_VERSION', '4.0' );

/**
 * Define plugin text domain
 */
define( 'JAM3_COOKIE_TEXT_DOMAIN', 'jam3_cookie_banner' );

/**
 * jam3_cookie_boot_plugin
 *
 * CALLED ON ACTION 'after_setup_theme'
 *
 * Includes all class files for plugin, runs on 'after_theme_setup' to allows
 * themes to override some classes/functions
 *
 * @access public
 * @author Ben Moody
 */
add_action( 'plugins_loaded', 'jam3_cookie_boot_plugin' ); //Allows themes to override classes, functions
function jam3_cookie_boot_plugin() {

	//vars
	$includes_path = JAM3_COOKIE_PLUGIN_BASE_DIR . '/includes';

	//Include plugin core file
	jam3_cookie_include_file( "{$includes_path}/class-jam3-cookie-core.php" );

	//Include plugin settings file
	jam3_cookie_include_file( "{$includes_path}/class-jam3-cookie-settings.php" );

	define( 'JAM3_COOKIE_PLUGIN_LOADED', true );

}

/**
 * jam3_cookie_include_file
 *
 * Helper to test file include validation and include_once if safe
 *
 * @param    string    Path to include
 *
 * @return    mixed    Bool/WP_Error
 * @access    public
 * @author    Ben Moody
 */
function jam3_cookie_include_file( $path ) {

	//Check if a valid path for include
	if ( validate_file( $path ) > 0 ) {

		//Failed path validation
		return new WP_Error(
			'jam3_cookie_include_file',
			'File include path failed path validation',
			$path
		);

	}

	include_once( $path );

	return true;
}

/**
 * jam3_cookie_get_template_path
 *
 * Helper to return path to plugin template part file
 *
 * NOTE you can override any template file by adding a copy of the file from
 * the plugin 'template-parts' dir into your theme under the
 * 'jam3-cookie-banner' subdir
 *
 * @param string $slug - template part slug name
 * @param string $template_name - template part filename NO .php
 *
 * @return string $path
 * @access public
 * @author Ben Moody
 */
function jam3_cookie_get_template_path( $slug, $template_name ) {

	//vars
	$path = JAM3_COOKIE_PLUGIN_BASE_DIR . '/template-parts';

	$slug          = esc_attr( $slug );
	$template_name = esc_attr( $template_name );

	//Setup template filenames/paths
	$plugin_template_file_path = "{$path}/{$slug}-{$template_name}.php";
	$theme_template_filename   = "/jam3-cookie-banner/{$slug}-{$template_name}.php";

	//First try and get theme override template
	$theme_template_file_path = locate_template( array( $theme_template_filename ) );

	if ( '' !== $theme_template_file_path ) {

		$path = $theme_template_file_path;

	} else { //Fallback to plugin's version of template

		$path = $plugin_template_file_path;

	}

	return $path;
}
