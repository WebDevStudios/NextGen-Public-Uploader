<?php
/**
 * TinyMCE things.
 *
 * @package NextGenGallery Public Uploader
 */

/**
 * Class add_nextgenPublicUpload_button
 */
class add_nextgenPublicUpload_button {

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	public $pluginname = "nextgenPublicUpload";

	/**
	 * Our add_nextgenPublicUpload_button constructor.
	 */
	public function __construct() {
		add_filter( 'tiny_mce_version', array( $this, 'change_tinymce_version' ) );
		add_action( 'init', array( $this, 'addbuttons' ) );
	}

	/**
	 * Add buttons.
	 *
	 * @since unknown
	 */
	public function addbuttons() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ), 5 );
			add_filter( 'mce_buttons', array( $this, 'register_button' ), 5 );
		}
	}

	/**
	 * Buttons
	 *
	 * @since unknown
	 *
	 * @param array $buttons Registered buttons.
	 * @return mixed
	 */
	public function register_button( $buttons ) {
		array_push( $buttons, "separator", $this->pluginname );
		return $buttons;
	}

	/**
	 * Plugin addition.
	 *
	 * @since unknown
	 *
	 * @param array $plugin_array Array of things.
	 * @return mixed
	 */
	function add_tinymce_plugin($plugin_array) {
		$plugin_array[ $this->pluginname ] =  nextgenPublicUpload_URLPATH . 'tinymce/editor_plugin.js';
		return $plugin_array;
	}

	/**
	 * Change version number.
	 *
	 * @since unknown
	 *
	 * @param string $version current version.
	 * @return mixed
	 */
	function change_tinymce_version($version) {
		return ++$version;
	}

}
$tinymce_button = new add_nextgenPublicUpload_button();
