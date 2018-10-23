<?php
/**
 * The cookie banner html template
 */
?>
<?php if ( defined( 'JAM3_COOKIE_PLUGIN_LOADED' ) ): ?>
	<div id="jam3-cookie-banner" style="display: none">

		<!-- The banner content !-->
		<?php echo wp_kses_post( Jam3_Cookie_Settings::get_option( 'jam3_cookie_content' ) ); ?>

		<button id="jam3-close-cookie-banner"></button>
	</div>

	<!-- Banner dynamic theme elements !-->
	<style type="text/css">
		#jam3-cookie-banner,
		#jam3-cookie-banner a {
			color: <?php echo esc_attr( Jam3_Cookie_Settings::get_option( 'jam3_cookie_theme_hex' ) ); ?>;
		}

		#jam3-close-cookie-banner:before,
		#jam3-close-cookie-banner:after {
		   background-color: <?php echo esc_attr( Jam3_Cookie_Settings::get_option( 'jam3_cookie_theme_hex' ) ); ?>;
	    }
	</style>
<?php endif; ?>
