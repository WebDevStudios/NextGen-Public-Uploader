(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('nextgenPublicUpload');

	tinymce.create('tinymce.plugins.nextgenPublicUpload', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('mcenextgenPublicUpload', function() {
				ed.windowManager.open({
					file : url + '/window.php',
					width : 300 + ed.getLang('nextgenPublicUpload.delta_width', 0),
					height : 210 + ed.getLang('nextgenPublicUpload.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('nextgenPublicUpload', {
				title : 'nextgenPublicUpload.desc',
				cmd : 'mcenextgenPublicUpload',
				image : url + '/npu.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('nextgenPublicUpload', n.nodeName == 'IMG');
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
					longname  : 'NextGEN Public Uploader',
					author 	  : 'WebDevStudios',
					authorurl : 'http://webdevstudios.com',
					infourl   : 'http://webdevstudios.com/support',
					version   : "1.5"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('nextgenPublicUpload', tinymce.plugins.nextgenPublicUpload);
})();