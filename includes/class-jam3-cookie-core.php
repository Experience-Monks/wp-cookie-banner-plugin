<?php
/**
 * Jam3_Cookie_Core
 *
 * Handles all logic for core elements of plugin
 *
 * @author Ben Moody
 */

class Jam3_Cookie_Core {

	public static $plugin_closed_cookie_name;
	protected static $is_ssl;
	protected static $plugin_prefix;

	public function __construct() {

		//Is this a secure connection
		self::$is_ssl = is_ssl();

		//Set plugin prefix
		self::$plugin_prefix = 'jam3_cookie_';

		//Set the plugin browser cookie name
		self::$plugin_closed_cookie_name = 'jam3_cookie_closed';

		//Set textdomain
		add_action( 'after_setup_theme', array( $this, 'plugin_textdomain' ) );

		/**
		 * Render inline JS to boot the front end
		 * We have this small inline script to detect cookie which states user has acknowledged and closed the cookie banner
		 * We have to do this via JS due to caching such as WP VIP Batcache whihc prevents server side detection of cookies
		 * If the cookie is not detected, then we load the plugin CSS and JS assets via Javascript
		 */
		add_action( 'init',
			array(
				$this,
				'maybe_render_banner_detection_script',
			)
		);

	}

	/**
	 * render_banner_template_html
	 *
	 * @CALLED Action wp_footer
	 *
	 * Finds the banner html template file to render the banner in the DOM
	 *
	 * NOTE as with all plugin template files you can override them in your
	 *     theme see jam3_cookie_get_template_path function description
	 *
	 * @access public
	 * @author Ben Moody
	 */
	public function render_banner_template_html() {

		//vars
		$template_path = null;

		$template_path = jam3_cookie_get_template_path( 'part', 'banner' );

		//jam3_cookie_get_template_path performs validate_file() check
		if ( is_wp_error( $template_path ) ) {
			return;
		}

		require_once( $template_path );

		return;
	}

	/**
	 * render_inline_initialize_script
	 *
	 * @CALLED BY /ACTION 'wp_footer'
	 *
	 * Render the inline JS required to initiate the banner.
	 * We have to do this via JS as page caching such as VIP batcache will not
	 *     allow for server side cookie detection
	 *
	 * @access public
	 * @author Ben Moody
	 */
	public function render_inline_initialize_script() {

		//vars
		$plugin_js_file_path  = null;
		$plugin_css_file_path = null;

		//Production assets
		$plugin_js_file_path  = JAM3_COOKIE_PLUGIN_BASE_URL . 'js/main-min.js?v=1.1';
		$plugin_css_file_path = JAM3_COOKIE_PLUGIN_BASE_URL . 'css/main.css';

		if ( defined( 'WP_DEBUG' ) && ( true === WP_DEBUG ) ) {

			//Dev assets
			$plugin_js_file_path = JAM3_COOKIE_PLUGIN_BASE_URL . 'js/main.js';

		}


		?>
		<script type="text/javascript">
            //<![CDATA[
            /**
             * Jam3InitCookieBanner
             *
             * Class to handle initiating Jam3 Cookie Banner,
             * detects if current user has closed the banner or not already
             * and loads JS and CSS assets if banner should be rendered
             *
             * @access public
             * @author Ben Moody
             */
            var Jam3InitCookieBanner = function () {

                var pluginCookieName;
                var isHttps;

                /**
                 * init
                 *
                 * Constructor method, called on class instance
                 *
                 * Boots the process
                 *
                 * @access public
                 * @author Ben Moody
                 */
                this.init = function () {

                    //vars
                    var theBannerContainer = document.getElementById('jam3-cookie-banner');

                    //Cache cookiename
                    this.pluginCookieName = <?php echo wp_json_encode( self::$plugin_closed_cookie_name ); ?>;

                    //Cache if this is https
                    this.isHttps = <?php
						$is_https = ( self::$is_ssl ) ? 'true' : 'false';
						echo wp_json_encode( $is_https );
						?>;

                    //Has user already closed banner?
                    if (true === this.maybeRenderBanner()) {

                        //Load plugin assets, JS, CSS
                        this.loadAssets();

                    } else {

                        //remove banner from DOM
                        theBannerContainer.remove();
                    }

                };

                /**
                 * loadAssets
                 *
                 * Call method to dynamically load and assets, CSS or JS
                 *
                 * @access public
                 * @author Ben Moody
                 */
                this.loadAssets = function () {

                    //vars
                    var pluginJSFile = <?php echo wp_json_encode( $plugin_js_file_path ); ?>;
                    var pluginCSSFile = <?php echo wp_json_encode( $plugin_css_file_path ); ?>;

                    //Load JS file
                    this.loadJsCssFile(pluginJSFile, 'js');

                    //Load CSS file
                    this.loadJsCssFile(pluginCSSFile, 'css');

                };

                /**
                 * maybeRenderBanner
                 *
                 * Detect if current user has closed the banner by
                 * looking for a valid browser cookie.
                 *
                 * @access public
                 * @author Ben Moody
                 */
                this.maybeRenderBanner = function () {

                    var cookieValue = '';

                    //Does cookie exist
                    cookieValue = this.getCookie(this.pluginCookieName);

                    if ('true' === cookieValue) {
                        return false;
                    }

                    return true;
                };

                /**
                 * getCookie
                 *
                 * Helper to get a cookie value by cookie name
                 *
                 * @param string cname
                 * @access public
                 * @author Ben Moody
                 */
                this.getCookie = function (cname) {

                    var v = document.cookie.match('(^|;) ?' + cname + '=([^;]*)(;|$)');

                    return v ? v[2] : null;
                };

                /**
                 * loadJsCssFile
                 *
                 * Dynamically load a JS or CSS file into the DOM
                 *
                 * @param string filename
                 * @param string filetype
                 * @access public
                 * @author Ben Moody
                 */
                this.loadJsCssFile = function (filename, filetype) {

                    if (filetype == "js") { //if filename is a external JavaScript file
                        var fileref = document.createElement('script')
                        fileref.setAttribute("type", "text/javascript")
                        fileref.setAttribute("src", filename)
                    }
                    else if (filetype == "css") { //if filename is an external CSS file
                        var fileref = document.createElement("link")
                        fileref.setAttribute("rel", "stylesheet")
                        fileref.setAttribute("type", "text/css")
                        fileref.setAttribute("href", filename)
                    }
                    if (typeof fileref != "undefined")
                        document.getElementsByTagName("head")[0].appendChild(fileref)
                };

            };

            //Create instance of class to get things started
            var Jam3InitCookieBanner = new Jam3InitCookieBanner;
            window.onload = function () {
                Jam3InitCookieBanner.init();
            }
            //]]>
		</script>
		<?php

		return;
	}

	/**
	 * Setup plugin textdomain folder
	 *
	 * @public
	 */
	public function plugin_textdomain() {

		load_plugin_textdomain( JAM3_COOKIE_TEXT_DOMAIN, false, '/jam3-cookie-banner/languages/' );

		return;
	}

	/**
	 * maybe_render_banner_detection_script
	 *
	 * @CALLED BY __construct()
	 *
	 * Detect if we should render the inline JS to initate the cookie banner on
	 *     front end
	 *
	 * @access private
	 * @author Ben Moody
	 */
	public function maybe_render_banner_detection_script() {

		//Is the cookie banner going to render?
		if ( false === self::maybe_render_cookie_banner() ) {
			return;
		}

		//Render inline initialize script
		add_action(
			'wp_footer',
			array(
				$this,
				'render_inline_initialize_script',
			)
		);

		//Render the banner html template into footer
		add_action(
			'wp_footer',
			array(
				$this,
				'render_banner_template_html',
			)
		);

		return;
	}

	/**
	 * maybe_render_cookie_banner
	 *
	 * Helper to detect if cookie banner inline code should be rendered
	 *
	 * @access public
	 * @author Ben Moody
	 */
	public static function maybe_render_cookie_banner() {

		//Allow devs to override banner rendering via filter
		$disable_banner_override = apply_filters( 'jam3_cookie_disable_override', false );
		if( true === $disable_banner_override ) {
			return false;
		}

		if ( is_user_logged_in() ) {
			return false;
		}

		//Is banner active
		if ( false === Jam3_Cookie_Settings::is_banner_active() ) {
			return false;
		}

		return true;
	}

}

new Jam3_Cookie_Core();
