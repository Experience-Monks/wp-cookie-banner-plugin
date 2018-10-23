<?php
/**
 * Jam3_Cookie_Settings
 *
 * Handles all logic for generating the plugins settings page, registered under
 * options
 *
 * @author Ben Moody
 */

class Jam3_Cookie_Settings extends Jam3_Cookie_Core {

	public static $disable_banner_content_field;
	private $page_title;
	private $menu_slug;
	private $menu_title;
	private $option_group;

	public function __construct() {

		//Setup globals for settings page
		$this->set_globals();

		// create custom plugin settings menu
		add_action( 'admin_menu', array( $this, 'create_settings_menu' ) );

	}

	/**
	 * set_globals
	 *
	 * @CALLED BY __construct()
	 *
	 * Setup any class globals for use in plugin code
	 *
	 * @access public
	 * @author Ben Moody
	 */
	public function set_globals() {

		//Set settings menu slug
		$this->menu_slug = self::$plugin_prefix . 'settings';

		//Set settings page title
		$this->page_title = esc_html_x( 'Jam3 Cookie Banner Settings', 'page title', 'jam3_cookie_banner' );

		//Set settings menu title
		$this->menu_title = esc_html_x( 'Cookie Banner', 'admin menu title', 'jam3_cookie_banner' );

		//Set option group slug
		$this->option_group = 'jam3-cookie-plugin-settings-group';

		/**
		 * Due to how some sites handle translations (polylang :( ) you may want to have a custom field handle the banner content.
		 * Disable the content settings field via this filter and then
		 * use the 'jam3_cookie_filter_option__jam3_cookie_content' filter to inject your custom fields content into the banner
		 *
		 * @since 1.0.0
		 *
		 * @param $disable_banner_content_field
		 */
		self::$disable_banner_content_field = apply_filters( 'jam3_cookie_disable_content_setting', false );

	}

	/**
	 * get_option
	 *
	 * Static helper method, used in front end template to get plugin options,
	 * allows for a handy custom filter for option data
	 * "jam3_cookie_filter_option__{$option_name}"
	 *
	 * @param string $option_name
	 *
	 * @return mixed $option
	 * @access public static
	 * @author Ben Moody
	 */
	public static function get_option( $option_name = null ) {

		//vars
		$option = null;

		if ( empty( $option_name ) ) {
			return false;
		}

		//Get option from WP
		$option = get_option( $option_name );

		/**
		 * Filter plugin options
		 *
		 * @since 1.0.0
		 *
		 * @param $option
		 */
		$option = apply_filters( "jam3_cookie_filter_option__{$option_name}", $option );

		return $option;
	}

	/**
	 * create_settings_menu
	 *
	 * @CALLED BY /ACTION 'admin_menu'
	 *
	 * Handles registering the options page with WP
	 *
	 * @access public
	 * @author Ben Moody
	 */
	public function create_settings_menu() {

		//Create new settings page in WP options menu
		add_options_page(
			$this->page_title,
			$this->menu_title,
			'manage_options',
			$this->menu_slug,
			array( $this, 'render_settings_page' )
		);

		//Register our plugin settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		return;
	}

	/**
	 * register_settings
	 *
	 * @CALLED BY /ACTION 'admin_init'
	 *
	 * Register the plugin option settings with WP
	 *
	 * @access public
	 * @author Ben Moody
	 */
	public function register_settings() {

		//Theme hex code
		register_setting(
			$this->option_group,
			'jam3_cookie_theme_hex',
			array(
				'type'              => 'string',
				'description'       => esc_html_x( 'HEX code used as the banner base theme', 'register setting description', 'jam3_cookie_banner' ),
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => false,
				'default'           => '#000000',
			)
		);

		//Banner Content
		if ( false === self::$disable_banner_content_field ) {

			register_setting(
				$this->option_group,
				'jam3_cookie_content',
				array(
					'type'              => 'string',
					'description'       => esc_html_x( 'Main banner content', 'register setting description', 'jam3_cookie_banner' ),
					'sanitize_callback' => 'wp_kses_post',
					'show_in_rest'      => false,
					'default'           => esc_html_x( 'Add banner content in Settings > Cookie Banner options', 'register setting description', 'jam3_cookie_banner' ),
				)
			);

		}

		return;
	}

	/**
	 * render_settings_page
	 *
	 * @CALLED callback for add_options_page()
	 *
	 * Finds the settings form template file to render the options page form.
	 *
	 * NOTE as with all plugin template files you can override them in your
	 *     theme see jam3_cookie_get_template_path function description
	 *
	 * @access public
	 * @author Ben Moody
	 */
	public function render_settings_page() {

		//vars
		$template_path = null;
		$page_title    = $this->page_title;
		$option_group  = $this->option_group;

		$template_path = jam3_cookie_get_template_path( 'settings', 'form' );

		require_once( $template_path );

		return;
	}

}

new Jam3_Cookie_Settings();
