# prj-wp-cookie-banner-plugin
WP plugin for a non-invasive cookie banner.

1. Install and activate plugin
2. In admin menu go to options > cookie banner
3. Set a theme base colour HEX code
4. Add banner content
5. Save settings

# Dev Notes
Cookie detection and creation is conducted via JS on the frontend as page caching such as batcache (used by WordPress VIP) prevents server side cookie detection and creation.

A small inline script conducts the init of the banner including the inital cookie detection ( see method /includes/class-jam3-cookie-core.php::render_inline_initialize_script() ). 

If inline script detects that banner should be rendered it fetches CSS and JS assets, which in turn continue the process of rendering the banner and handling any actions on it.

Cookie SECURE param isset if server detects SSL via WordPress is_ssl().

uncompressed dev version of main.js is loaded if WordPress debug mode is detected, so this should only occur in DEV environments.

# Templates
The plugin currently has 2 template parts found int /template-parts/. If these need to be cusomized for a specific project, simply copy them from the plugin into the active theme under the /jam3-cookie-banner/ subdirectory, do not edit them directly in the plugin.

# Filters

'jam3_cookie_disable_content_setting'
@param $disable_banner_content_field (defaults to false)

If you need to use a custom field for the banner content as opposed to the plugins setting field you can disable the content field in the plugin settings by returning true.


"jam3_cookie_filter_option__{$option_name}"
$option_name - slug/name of option used when requesting get_option() examples: jam3_cookie_theme_hex, jam3_cookie_content
@param $option - value of the requested settings option

Due to how some sites handle translations (polylang :( ) you may want to have a custom field handle the banner content. Here you can hook into the rendering of all plugin options output and override it with whatever you want.

