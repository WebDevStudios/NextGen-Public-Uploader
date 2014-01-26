<?php
/*
Plugin Name: NextGEN Public Uploader
Plugin URI: http://webdevstudios.com/plugin/nextgen-public-uploader/
Description: NextGEN Public Uploader is an extension to NextGEN Gallery which allows frontend image uploads for your users.
Version: 1.9

Author: WebDevStudios
Author URI: http://webdevstudios.com
Text Domain: nextgen-public-uploader
Domain Path: /languages

Copyright 2009-2013 WebDevStudios  (email: contact@webdevstudios.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class NGGallery_Public_uploader {

// If NextGEN Gallery doesn't exist, or it's not active...
if ( ! class_exists( 'nggLoader' ) ) {

	add_action( 'plugins_loaded', 'npu_translate' );
	function npu_translate() {
		load_plugin_textdomain( 'nextgen-public-uploader', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	// Display Error Message
	add_action( 'admin_notices', 'npu_error_message' );

	function npu_error_message() {
		// Include thickbox support
		add_thickbox();

		// Generate our error message
		$output = '';
		$output .= '<div id="message" class="error">';
		$output .= sprintf(
			__( '%s NextGEN Public Uploader %s requires NextGEN Gallery in order to work. Please deactivate NextGEN Public Uploader or activate %s NextGEN Gallery %s', 'nextgen-public-uploader' ),
			'<p><strong>',
			'</strong>',
			'<a href="' . admin_url( '/plugin-install.php?tab=plugin-information&plugin=nextgen-gallery&TB_iframe=true&width=600&height=550' ) . '" target="_blank" class="thickbox onclick">',
			'</a>.</strong></p>'
		);
		$output .= '</div>';
		echo $output;

	}

	// Otherwise, continue like normal
	} else {

	// Register an activation hook for setting our default settings
	register_activation_hook( __FILE__, 'npu_plugin_activation' );
	function npu_plugin_activation() {

		// If our settings don't already exist, load them in to the database
		if ( ! get_option( 'npu_default_gallery' ) ) {
			update_option( 'npu_default_gallery', 			'1' );
			update_option( 'npu_user_role_select', 			'99' );
			update_option( 'npu_exclude_select', 			'Enabled' );
			update_option( 'npu_image_description_select', 	'Enabled' );
			update_option( 'npu_description_text', 			'' );
			update_option( 'npu_notification_email', 		get_option('admin_email') );
			update_option( 'npu_upload_button', 			__( 'Upload', 'nextgen-public-uploader' ) );
			update_option( 'npu_no_file', 					__( 'No file selected.', 'nextgen-public-uploader' ) );
			update_option( 'npu_notlogged', 				__( 'You are not authorized to upload an image.', 'nextgen-public-uploader' ) );
			update_option( 'npu_upload_success', 			__( 'Your image has been successfully uploaded.', 'nextgen-public-uploader' ) );
			update_option( 'npu_upload_failed', 			__( 'Your upload failed. Please try again.', 'nextgen-public-uploader' ) );
			update_option( 'npu_image_link_love', 			'' );
		}

	}

	// Upload Form Path
	require_once( dirname (__FILE__) . '/inc/npu-upload.php');

	// TinyMCE
	define( 'nextgenPublicUpload_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
	include_once( dirname (__FILE__) . '/tinymce/tinymce.php' );

	// Output NextGEN Public Uploader Link Love in footer
	$linklove = get_option( 'npu_image_link_love' );
	if ( !empty( $linklove ) ) {
		add_action('wp_footer', 'npu_link_love');
	}
	function npu_link_love() {
		echo sprintf(
			__( '%s NextGEN Public Uploader %s by %s WordPress Website Design and Development %s', 'nextgen-public-uploader' ),
			'<p><a href="http://wordpress.org/extend/plugins/nextgen-public-uploader/">',
			'</a>',
			'<a href="http://webdevstudios.com/" title="',
			'">WebDevStudios</a></p>'
		);
	}

	// Register our settings page as a submenu item of the NextGEN menu item
	add_action( 'admin_menu', 'npu_plugin_menu' );
	function npu_plugin_menu() {
		add_submenu_page(
			NGGFOLDER,
			__( 'NextGEN Public Uploader', 'nextgen-public-uploader' ),
			__( 'Public Uploader', 'nextgen-public-uploader' ),
			'manage_options',
			'nextgen-public-uploader',
			'npu_plugin_options_page'
		);
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'filter_plugin_actions' );
	}

	// Add "Settings" Link to Plugin on Plugins Page
	function filter_plugin_actions ( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=nextgen-public-uploader' ) . '">' . __( 'Settings', 'nextgen-public-uploader' ) . '</a>'
			),
			$links
		);
	}

	// Register all of our settings
	add_action( 'admin_init', 'npu_plugin_settings' );
	function npu_plugin_settings() {

		// Register our settings section
		add_settings_section( 'npu_settings', __( 'Plugin Settings', 'nextgen-public-uploader' ), 'npu_settings_description', 'nextgen-public-uploader' );

		// Register all our settings
		register_setting( 'npu_settings', 'npu_default_gallery',			'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_user_role_select',			'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_image_description_select',	'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_exclude_select',				'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_notification_email',			'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_upload_button',				'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_no_file',					'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_description_text',			'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_notlogged',					'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_upload_success',				'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_upload_failed',				'npu_settings_sanitization' );
		register_setting( 'npu_settings', 'npu_image_link_love',			'npu_settings_sanitization' );

		// Setup the options for our gallery selector
		$gallery_options = array();
		include_once( NGGALLERY_ABSPATH . 'lib/ngg-db.php' );
		$nggdb = new nggdb();
		$gallerylist = $nggdb->find_all_galleries( 'gid', 'DESC' );
		foreach ( $gallerylist as $gallery ) {
			$name = !empty( $gallery->title ) ? $gallery->title : $gallery->name;
			$gallery_options[ $gallery->gid ] = 'ID: ' . $gallery->gid . ' &ndash; ' . $name;
		}

		// Setup the options for our role selector
		$role_options = array( //are
			'99'	=> __( 'Visitor', 'nextgen-public-uploader' ),
			'0'		=> __( 'Subscriber', 'nextgen-public-uploader' ),
			'1'		=> __( 'Contributor', 'nextgen-public-uploader' ),
			'2'		=> __( 'Author', 'nextgen-public-uploader' ),
			'7'		=> __( 'Editor', 'nextgen-public-uploader' ),
			'10'	=> __( 'Admin', 'nextgen-public-uploader' )
		);

		// Add our settings fields
		add_settings_field( 'npu_default_gallery', 			__( 'Default Gallery:', 'nextgen-public-uploader' ),			'npu_settings_select', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_default_gallery',			'description' => sprintf( __( 'The default gallery ID when using %s with no ID specified.', 'nextgen-public-uploader' ), '<code>[ngg_uploader]</code>' ), 'options' => $gallery_options ) );
		add_settings_field( 'npu_user_role_select', 		__( 'Minimum User Role:', 'nextgen-public-uploader' ),			'npu_settings_select', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_user_role_select',			'description' => __( 'The minimum user role required for image uploading.', 'nextgen-public-uploader' ), 'options' => $role_options ) );
		add_settings_field( 'npu_exclude_select', 			__( 'Uploads Require Approval:', 'nextgen-public-uploader' ),	'npu_settings_checkbox', 	'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_exclude_select',			'description' => '',	'value' => 'Enabled', 'label' => __( 'Exclude images from appearing in galleries until they have been approved.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_image_description_select', __( 'Show Description Field:', 'nextgen-public-uploader' ),		'npu_settings_checkbox', 	'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_image_description_select',	'description' => '',	'value' => 'Enabled', 'label' => __( 'Enable the Image Description text field.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_description_text', 		__( 'Image Description Label:', 'nextgen-public-uploader' ),	'npu_settings_text', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_description_text',			'description' => __( 'Default label shown for the image description textbox.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_notification_email', 		__( 'Notification Email:', 'nextgen-public-uploader' ),			'npu_settings_text', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_notification_email',		'description' => __( 'The email address to be notified when a image has been submitted.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_upload_button', 			__( 'Upload Button Text:', 'nextgen-public-uploader' ),			'npu_settings_text', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_upload_button',				'description' => __( 'Custom text for upload button.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_no_file', 					__( 'No File Selected Warning:', 'nextgen-public-uploader' ),	'npu_settings_text', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_no_file',					'description' => __( 'Warning displayed when no file has been selected for upload.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_notlogged', 				__( 'Unauthorized Warning:', 'nextgen-public-uploader' ),		'npu_settings_text', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_notlogged',					'description' => __( 'Warning displayed when a user does not have permission to upload.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_upload_success', 			__( 'Upload Success Message:', 'nextgen-public-uploader' ),		'npu_settings_text', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_upload_success',			'description' => __( 'Message displayed when an image has been successfully uploaded.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_upload_failed', 			__( 'Upload Failed Message:', 'nextgen-public-uploader' ),		'npu_settings_text', 		'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_upload_failed',				'description' => __( 'Message displayed when an image failed to upload.', 'nextgen-public-uploader' ) ) );
		add_settings_field( 'npu_image_link_love', 			__( 'Link Love:', 'nextgen-public-uploader' ),					'npu_settings_checkbox', 	'nextgen-public-uploader',	'npu_settings',		array( 'ID' => 'npu_image_link_love',			'description' => '',	'value' => true, 'label' => __( 'Display link to this plugin in your site\'s footer (because you love us!)', 'nextgen-public-uploader' ) ) );

	}

	// Descriptive text for our settings section
	function npu_settings_description() {
		echo '<p>' . __( 'Edit the settings below to control the default behaviors of this plugin.', 'nextgen-public-uploader' ) . '</p>';
	}

	// Input for select options
	function npu_settings_select( $args ) {

		$output = '';
		$output .= '<select name="' . $args['ID'] . '">';
		foreach ( $args['options'] as $value => $label ) {
			$output .= '<option ' . selected( $value, get_option($args['ID']), false ) . ' value="' . $value . '">' . $label . '</option>';
		}
		$output .= '</select>';

		if ( isset( $args['description'] ) )
			$output .= ' <span class="description">' . $args['description'] . '</span>';

		echo $output;
	}

	// Input for checkbox options
	function npu_settings_checkbox( $args ) {

		$output = '';
		$output .= '<label for="' . $args['ID'] . '"><input type="checkbox" id="' . $args['ID'] . '" name="' . $args['ID'] . '" value="' . $args['value'] . '" ' . checked( get_option($args['ID']), $args['value'], false ) . ' /> ' . $args['label'] . '</label>';
		if ( isset( $args['description'] ) )
			$output .= ' <span class="description">' . $args['description'] . '</span>';

		echo $output;
	}

	// Input for text options
	function npu_settings_text( $args ) {

		$output = '';
		$output .= '<input type="text" class="regular-text" name="' . $args['ID'] . '" value="' . get_option($args['ID']) . '" />';
		if ( isset( $args['description'] ) )
			$output .= ' <span class="description">' . $args['description'] . '</span>';
		echo $output;
	}

	// Perform some rudimentary sanitization on all our options
	function npu_settings_sanitization( $input ) {
		$valid = esc_html( $input );
		return $valid;
	}

	// Create our Settings page
	function npu_plugin_options_page() {

	// If the user cannot manage options, bail here
	if ( ! current_user_can( 'manage_options' ) )
		return false;
	?>
		<div class="wrap">

			<?php screen_icon(); ?> <h2><?php _e( 'NextGEN Public Uploader', 'nextgen-public-uploader' ); ?></h2>

			<?php if ( isset($_GET['settings-updated']) ) echo '<div class="updated"><p><strong>' . __( 'Settings saved.', 'nextgen-public-uploader' ) . "</strong></p></div>\n"; ?>

			<p>
				<strong><?php _e('Current Version', 'nextgen-public-uploader') ?>:</strong> <?php $plugin_data = get_plugin_data( __FILE__, false ); echo $plugin_data['Version']; ?> |
				<a href="http://webdevstudios.com">WebDevStudios.com</a> |
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=3084056"><?php _e('Donate', 'nextgen-public-uploader' ) ?></a> |
				<a href="http://wordpress.org/extend/plugins/nextgen-public-uploader/"><?php _e('Plugin Homepage', 'nextgen-public-uploader' ) ?></a> |
				<a href="http://wordpress.org/support/plugin/nextgen-public-uploader/"><?php _e('Support Forum', 'nextgen-public-uploader' ) ?></a>
			</p>

			<h3><?php _e('Shortcode Examples', 'nextgen-public-uploader') ?></h3>
			<p><?php printf( __( 'To insert the public uploader into any content area, use %s or %s, where %s is the ID of the corresponding gallery.', 'nextgen-public-uploader' ), '<code>[ngg_uploader]</code>', '<code>[ngg_uploader id="1"]</code>', '<strong>1</strong>' ); ?></p>

			<?php do_action( 'npu_plugin_options_page_before_form' ); ?>

			<form action="options.php" method="post">

				<?php
					settings_fields( 'npu_settings' );
					do_settings_sections( 'nextgen-public-uploader' );
					submit_button();
				?>

			</form>

		</div>

	<?php
	}

}
// Have a nice day!
$nggpu = new NGGallery_Public_uploader;
