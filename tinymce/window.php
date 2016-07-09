<?php

require_once( 'tinymce-config.php' );

global $wpdb;

if ( ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
	wp_die( esc_html__( 'You are not allowed to be here', 'nextgen-public-uploader' ) );
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php esc_html_e( 'NextGEN Public Uploader', 'nextgen-public-uploader' ); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}

	function insertcetsHWLink() {

		var tagtext;

		var rss = document.getElementById('rss_panel');

		// who is active ?
		if (rss.className.indexOf('current') != -1) {
			var rssid = document.getElementById('rsstag').value;

			if (rssid != '' ) {
				tagtext = "[ngg_uploader id=" + rssid + "]";
			} else {
				tinyMCEPopup.close();
			}
		}

		if(window.tinyMCE) {
			window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
			//Peforms a clean up of the current editor HTML.
			//tinyMCEPopup.editor.execCommand('mceCleanup');
			//Repaints the editor. Sometimes the browser has graphic glitches.
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}
	}
	</script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('rsstag').focus();" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="nextgenPublicUpload" action="#">
	<div class="tabs">
		<ul>
			<li id="rss_tab" class="current"><span><a href="javascript:mcTabs.displayTab('rss_tab','rss_panel');" onmousedown="return false;"><?php esc_html_e( 'Gallery', 'nextgen-public-uploader' ); ?></a></span></li>
		</ul>
	</div>

	<div class="panel_wrapper">
		<div id="rss_panel" class="panel current">
		<br />
		<table border="0" cellpadding="4" cellspacing="0">
        <?php
        include_once ( NGGALLERY_ABSPATH . 'lib/ngg-db.php' );
        $nggdb = new nggdb();
        $gallerylist = $nggdb->find_all_galleries('gid', 'DESC');
		?>
		<tr>
			<td nowrap="nowrap"><label for="rsstag"><?php esc_html_e( 'Select Gallery:', 'nextgen-public-uploader' ); ?></label></td>
		</tr>
		<tr>
			<td>
        	<select id="rsstag" name="rsstag">
        		<?php
        		foreach ( $gallerylist as $gallery ) {
        			$name = ( empty( $gallery->title ) ) ? $gallery->name : $gallery->title;
					$galleryid = $gallery->gid . ': ';
				?>
        			<option value="<?php echo $gallery->gid; ?>"><?php echo 'ID: ' . $galleryid . ' &ndash; ' . $name; ?></option>
        		<?php } ?>
        	</select>
        	</td>
		</tr>
        </table>
		</div>

	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php esc_attr_e( 'Cancel', 'nextgen-public-uploader' ); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php esc_attr_e( 'Insert', 'nextgen-public-uploader'); ?>" onclick="insertcetsHWLink();" />
		</div>
	</div>
</form>
</body>
</html>
