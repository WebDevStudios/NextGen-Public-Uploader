<?php

class add_nextgenPublicUpload_button {
	
	var $pluginname = "nextgenPublicUpload";
	
	function add_nextgenPublicUpload_button()  {
		add_filter('tiny_mce_version', array (&$this, 'change_tinymce_version') );
		
		add_action('init', array (&$this, 'addbuttons') );
	}

	function addbuttons() {
	
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;
		
		if ( get_user_option('rich_editing') == 'true') {
		 
			add_filter("mce_external_plugins", array (&$this, "add_tinymce_plugin" ), 5);
			add_filter('mce_buttons', array (&$this, 'register_button' ), 5);
		}
	}
	
	function register_button($buttons) {
	
		array_push($buttons, "separator", $this->pluginname );
	
		return $buttons;
	}
	
	function add_tinymce_plugin($plugin_array) {    
	
		$plugin_array[$this->pluginname] =  nextgenPublicUpload_URLPATH.'tinymce/editor_plugin.js';
		
		return $plugin_array;
	}
	
	function change_tinymce_version($version) {
		return ++$version;
	}
	
}

$tinymce_button = new add_nextgenPublicUpload_button ();

?>