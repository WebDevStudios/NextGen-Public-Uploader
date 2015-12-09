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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NGGallery_Public_uploader {

	public $basename = '';
	public $directory_path = '';
	public $directory_url = '';

	/**
	 * Lets build some galleries
	 */
	public function __construct() {

		//Some useful properties
        $this->basename         = plugin_basename( __FILE__ );
        $this->directory_path   = plugin_dir_path( __FILE__ );
        $this->directory_url    = plugins_url( dirname( $this->basename ) );

        //And a registration hook
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		//Lets let everyone be able to read it, regardless of dialect
		load_plugin_textdomain( 'nextgen-public-uploader', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		//We need NextGen Gallery to work
		add_action( 'admin_notices', array( $this, 'maybe_disable_plugin' ) );

		//And our helper functions
		add_action( 'plugins_loaded', array( $this, 'includes' ) );

		//Here's how people will access the settings
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'plugin_settings' ) );

		//Or this way. Handy!
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'filter_plugin_actions' ) );

		add_action( 'npu_plugin_options_page_after_form', array( $this, 'shortcodes' ) );
		add_action( 'npu_plugin_options_page_after_form', array( $this, 'footer_text' ), 11 );
	}

	/**
	 * Checks if NextGen Gallery is available
	 *
	 * @since  1.9
	 *
	 * @return bool  whether the NGG base class exists.
	 */
	public static function meets_requirements() {

		if ( class_exists( 'C_NextGEN_Bootstrap' ) )
			return true;
		else
			return false;

	}

	/**
	 * Check if we meet requirements, and disable if we don't
	 *
	 * @since  1.9
	 *
	 * @return string  html message.
	 */
	public function maybe_disable_plugin() {

		if ( ! $this->meets_requirements() ) {
			// Display our error
			echo '<div id="message" class="error">';
			echo '<p>';
			echo sprintf(
				__( '%s NextGEN Public Uploader %s requires NextGEN Gallery in order to work. Please deactivate NextGEN Public Uploader or activate %s NextGEN Gallery %s', 'nextgen-public-uploader' ),
				'<p><strong>',
				'</strong>',
				'<a href="' . admin_url( '/plugin-install.php?tab=plugin-information&plugin=nextgen-gallery&TB_iframe=true&width=600&height=550' ) . '" target="_blank" class="thickbox onclick">',
				'</a>.</strong></p>'
			);
			echo '</p>';
			echo '</div>';

			// Deactivate our plugin
			deactivate_plugins( $this->basename );
		}

	}

	/**
	 * Load our resources if we meet requirements.
	 *
	 * @return void
	 */
	public function includes() {

		if ( $this->meets_requirements() ) {
			require_once( dirname (__FILE__) . '/inc/npu-upload.php');
			require_once( dirname (__FILE__) . '/tinymce/tinymce.php' );
		}

	}

	/**
	 * Set default option values if we don't have any.
	 */
	public function activate() {

		if ( $this->meets_requirements() ) {
			// If our settings don't already exist, load defaults into the database
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
			}
		}

	}

	/**
	 * Add our menu.
	 */
	public function menu() {

		//NOTE: Until I figure out how to make it a submenu, it's going as a main menu item
		/*add_submenu_page(
			NGGFOLDER,
			__( 'NextGEN Public Uploader', 'nextgen-public-uploader' ),
			__( 'Public Uploader', 'nextgen-public-uploader' ),
			'manage_options',
			'nextgen-public-uploader',
			array( $this, 'options_page' )
		);*/
		add_menu_page(
			__( 'NextGEN Public Uploader', 'nextgen-public-uploader' ),
			__( 'NextGEN Public Uploader', 'nextgen-public-uploader' ),
			'manage_options',
			'nextgen-public-uploader',
			array( $this, 'options_page' )
		);

	}

	/**
	 * Render our options page
	 *
	 * @return mixed  HTML
	 */
	public function options_page() { ?>
		<div class="wrap">

			<?php
			global $wp_version;

			//Only need the icon for 3.7 and down. 3.8 removed support.
			if ( version_compare( $wp_version, '3.7', '<' ) ) {
				screen_icon();
			}
			?>
			<h1><?php _e( 'NextGEN Public Uploader', 'nextgen-public-uploader' ); ?></h1>

			<?php if ( isset( $_GET['settings-updated'] ) ) { ?>
				<div class="updated"><p><?php _e( 'Settings saved.', 'nextgen-public-uploader' ); ?></p></div>
			<?php
			} ?>

			<?php do_action( 'npu_plugin_options_page_before_form' ); ?>

			<form action="options.php" method="post">

				<?php
					settings_fields( 'npu_settings' );
					do_settings_sections( 'nextgen-public-uploader' );
					submit_button();
				?>

			</form>

			<?php do_action( 'npu_plugin_options_page_after_form' ); ?>

		</div>

	<?php
	}

	/**
	 * Set up and register our settings
	 */
	public function plugin_settings() {

		// Register our settings section
		add_settings_section( 'npu_settings', __( 'Settings', 'nextgen-public-uploader' ), array( $this, 'settings_description' ), 'nextgen-public-uploader' );

		// Register all our settings
		register_setting( 'npu_settings', 'npu_default_gallery',			array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_user_role_select',			array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_image_description_select',	array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_exclude_select',				array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_notification_email',			array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_upload_button',				array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_no_file',					array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_description_text',			array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_notlogged',					array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_upload_success',				array( $this, 'settings_sanitization' ) );
		register_setting( 'npu_settings', 'npu_upload_failed',				array( $this, 'settings_sanitization' ) );

		// Setup the options for our gallery selector
		$gallery_options = array();

		include_once( NGGALLERY_ABSPATH . 'lib/ngg-db.php' );

		$nggdb = new nggdb();

		$gallerylist = $nggdb->find_all_galleries( 'gid', 'ASC' );

		foreach ( $gallerylist as $gallery ) {
			$name = !empty( $gallery->title ) ? $gallery->title : $gallery->name;
			$gallery_options[ $gallery->gid ] = $gallery->gid . ' &ndash; ' . $name;
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
		add_settings_field(
			'npu_default_gallery',
			__( 'Default Gallery:', 'nextgen-public-uploader' ),
			array( $this, 'settings_select' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_default_gallery',
				'description' => sprintf( __( 'The default gallery ID when using %s with no ID specified.', 'nextgen-public-uploader' ),
				'<code>[ngg_uploader]</code>' ),
				'options' => $gallery_options
			)
		);
		add_settings_field(
			'npu_user_role_select',
			__( 'Minimum User Role:', 'nextgen-public-uploader' ),
			array( $this, 'settings_select' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_user_role_select',
				'description' => __( 'The minimum user role required for image uploading.', 'nextgen-public-uploader' ),
				'options' => $role_options
			)
		);
		add_settings_field(
			'npu_exclude_select',
			__( 'Uploads Require Approval:', 'nextgen-public-uploader' ),
			array( $this, 'settings_checkbox' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_exclude_select',
				'description' => '',
				'value' => 'Enabled',
				'label' => __( 'Exclude images from appearing in galleries until they have been approved.', 'nextgen-public-uploader' )
			)
		);
		add_settings_field(
			'npu_image_description_select',
			__( 'Show Description Field:', 'nextgen-public-uploader' ),
			array( $this, 'settings_checkbox' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_image_description_select',
				'description' => '',
				'value' => 'Enabled',
				'label' => __( 'Enable the Image Description text field.', 'nextgen-public-uploader' )
			)
		);
		add_settings_field(
			'npu_description_text',
			__( 'Image Description Label:', 'nextgen-public-uploader' ),
			array( $this, 'settings_text' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_description_text',
				'description' => __( 'Default label shown for the image description textbox.', 'nextgen-public-uploader' )
			)
		);
		add_settings_field(
			'npu_notification_email',
			__( 'Notification Email:', 'nextgen-public-uploader' ),
			array( $this, 'settings_text' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_notification_email',
				'description' => __( 'The email address to be notified when a image has been submitted.', 'nextgen-public-uploader' )
			)
		);
		add_settings_field(
			'npu_upload_button',
			__( 'Upload Button Text:', 'nextgen-public-uploader' ),
			array( $this, 'settings_text' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_upload_button',
				'description' => __( 'Custom text for upload button.', 'nextgen-public-uploader' )
			)
		);
		add_settings_field(
			'npu_no_file',
			__( 'No File Selected Warning:', 'nextgen-public-uploader' ),
			array( $this, 'settings_text' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_no_file',
				'description' => __( 'Warning displayed when no file has been selected for upload.', 'nextgen-public-uploader' )
			)
		);
		add_settings_field(
			'npu_notlogged',
			__( 'Unauthorized Warning:', 'nextgen-public-uploader' ),
			array( $this, 'settings_text' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_notlogged',
				'description' => __( 'Warning displayed when a user does not have permission to upload.', 'nextgen-public-uploader' )
			)
		);
		add_settings_field(
			'npu_upload_success',
			__( 'Upload Success Message:', 'nextgen-public-uploader' ),
			array( $this, 'settings_text' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_upload_success',
				'description' => __( 'Message displayed when an image has been successfully uploaded.', 'nextgen-public-uploader' )
			)
		);
		add_settings_field(
			'npu_upload_failed',
			__( 'Upload Failed Message:', 'nextgen-public-uploader' ),
			array( $this, 'settings_text' ),
			'nextgen-public-uploader',
			'npu_settings',
			array(
				'ID' => 'npu_upload_failed',
				'description' => __( 'Message displayed when an image failed to upload.', 'nextgen-public-uploader' )
			)
		);
	}

	/**
	 * Description setting
	 *
	 * @return string html text
	 */
	public function settings_description() {
		echo '<p>' . __( 'Edit the settings below to control the default behaviors of this plugin. Shortcode example(s) available at the bottom of the page.', 'nextgen-public-uploader' ) . '</p>';
	}

	/**
	 * Echo a <select> input
	 *
	 * @param  array  $args array of arguments to use
	 *
	 * @return mixed        html select input with populated options
	 */
	public function settings_select( $args ) {

		$output = '<select name="' . $args['ID'] . '">';
		foreach ( $args['options'] as $value => $label ) {
			$output .= '<option ' . selected( $value, get_option($args['ID']), false ) . ' value="' . $value . '">' . $label . '</option>';
		}
		$output .= '</select>';

		if ( isset( $args['description'] ) )
			$output .= '<p><span class="description">' . $args['description'] . '</span></p>';

		echo $output;
	}

	/**
	 * Echo a checkbox input
	 *
	 * @param  array  $args array of arguments to use
	 *
	 * @return mixed        html checkbox input
	 */
	public function settings_checkbox( $args ) {

		$output = '';
		$output .= '<label for="' . $args['ID'] . '"><input type="checkbox" id="' . $args['ID'] . '" name="' . $args['ID'] . '" value="' . $args['value'] . '" ' . checked( get_option($args['ID']), $args['value'], false ) . ' /> ' . $args['label'] . '</label>';
		if ( isset( $args['description'] ) )
			$output .= '<p><span class="description">' . $args['description'] . '</span></p>';

		echo $output;
	}

	/**
	 * Echo a text input
	 *
	 * @param  array  $args array of arguments to use
	 *
	 * @return mixed        html text input
	 */
	public function settings_text( $args ) {

		$output = '';
		$output .= '<input type="text" class="regular-text" name="' . $args['ID'] . '" value="' . get_option($args['ID']) . '" />';
		if ( isset( $args['description'] ) )
			$output .= '<p><span class="description">' . $args['description'] . '</span></p>';
		echo $output;
	}

	/**
	 * Sanitize our settings
	 *
	 * @param  string  $input value to sanitize before saving
	 *
	 * @return string         sanitized value
	 */
	public function settings_sanitization( $input ) {
		$valid = esc_html( $input );
		return $valid;
	}

	/**
	 * Add our settings link to the plugins listing for our plugin.
	 *
	 * @param  array  $links Array of links already available
	 *
	 * @return array         Array of new links to use
	 */
	public function filter_plugin_actions( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=nextgen-public-uploader' ) . '">' . __( 'Settings', 'nextgen-public-uploader' ) . '</a>'
			),
			$links
		);
	}

	public function shortcodes() { ?>
		<h2><?php _e( 'Shortcode Examples', 'nextgen-public-uploader' ) ?></h2>
		<p><?php printf( __( 'To insert the public uploader into any content area, use %s or %s, where %s is the ID of the corresponding gallery.', 'nextgen-public-uploader' ), '<code>[ngg_uploader]</code>', '<code>[ngg_uploader id="1"]</code>', '<strong>1</strong>' ); ?></p>

		<?php do_action( 'npu_shortcodes' ); ?>
	<?php
	}

	public function footer_text() { ?>
		<p>
			<strong><?php _e('Current Version', 'nextgen-public-uploader') ?>:</strong> <?php $plugin_data = get_plugin_data( __FILE__, false ); echo $plugin_data['Version']; ?> |
			<a href="http://webdevstudios.com">WebDevStudios.com</a> |
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=3084056"><?php _e('Donate', 'nextgen-public-uploader' ) ?></a> |
			<a href="http://wordpress.org/plugins/nextgen-public-uploader/"><?php _e('Plugin Homepage', 'nextgen-public-uploader' ) ?></a> |
			<a href="http://wordpress.org/support/plugin/nextgen-public-uploader/"><?php _e('Support Forum', 'nextgen-public-uploader' ) ?></a>
		</p>
	<?php
	}

}
// Have a nice day!
$nggpu = new NGGallery_Public_uploader;
