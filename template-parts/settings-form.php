<?php
/**
 * Plugin settings page template
 *
 * Place a copy of this in your theme under 'jam3-cookie-banner' subdirectory
 * to override this template
 */
?>
<?php if ( defined( 'JAM3_COOKIE_PLUGIN_LOADED' ) ): ?>
	<div class="wrap">
		<h1><?php echo esc_html( $page_title ); ?></h1>

		<form method="post" action="options.php">

			<?php settings_fields( $option_group ); ?>

			<?php do_settings_sections( $option_group ); ?>

			<table class="form-table">

				<tr valign="top">
					<th scope="row">
						<?php echo esc_html_x( 'Banner Status', 'form field title', 'jam3_cookie_banner' ); ?>
					</th>
					<td>
						<?php
						$status  = esc_attr( get_option( 'jam3_cookie_status', 'disabled' ) );
						$options = array(
							'enabled'  => 'Enabled',
							'disabled' => 'Disabled',
						);
						?>
						<select name="jam3_cookie_status">
							<?php
							foreach ( $options as $value => $title ) :
								$selected = null;
								if ( $value === $status ) {
									$selected = 'selected';
								}
								?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php echo esc_html_x( 'Theme HEX Code', 'form field title', 'jam3_cookie_banner' ); ?>
					</th>
					<td><input type="text"
							   name="jam3_cookie_theme_hex"
							   value="<?php echo esc_attr( get_option( 'jam3_cookie_theme_hex' ) ); ?>"/>
					</td>
				</tr>

				<?php if ( false === Jam3_Cookie_Settings::$disable_banner_content_field ) : ?>
					<tr valign="top">
						<th scope="row">
							<?php echo esc_html_x( 'Banner Content', 'form field title', 'jam3_cookie_banner' ); ?>
						</th>
						<td>
							<?php
							wp_editor(
								wp_kses_post( get_option( 'jam3_cookie_content' ) ),
								'jam3_cookie_content',
								array(
									'media_buttons' => false,
									'textarea_name' => 'jam3_cookie_content',
									'teeny'         => true,
								)
							);
							?>
						</td>
					</tr>
				<?php endif; ?>

			</table>

			<?php submit_button(); ?>

		</form>
	</div>
<?php endif; ?>
