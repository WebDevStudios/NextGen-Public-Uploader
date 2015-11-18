<?php
/*
Is there any way we can handle the uploading more gracefully?
Does NGG rely on not uploading to the media library, making the functions available there not applicable?
 */

// Get NextGEN Gallery Functions
require_once ( NGGALLERY_ABSPATH . '/admin/functions.php' );

class UploaderNggAdmin extends nggAdmin {

	// Public Variables
	public $arrImageIds      = array();
	public $arrImageNames    = array();
	public $arrThumbReturn   = array();
	public $arrEXIF          = array();
	public $arrErrorMsg      = array();
	public $arrErrorMsg_widg = array();
	public $strFileName      = '';
	public $strGalleryPath   = '';
	public $blnRedirectPage  = false;

	function upload_images()
	{
		$storage        = C_Gallery_Storage::get_instance();
		$image_mapper   = C_Image_Mapper::get_instance();
		$gallery_mapper = C_Gallery_Mapper::get_instance();

		// Get Gallery ID
		$galleryID = (int)$_POST['galleryselect'];
		if ( $galleryID == 0 ) {
			if( get_option( 'npu_default_gallery' ) ) {
				$galleryID = get_option( 'npu_default_gallery' );
			} else {
				self::show_error( __( 'No gallery selected.', 'nextgen-public-uploader' ) );
				return;
			}
		}

		// Get the Gallery
		$gallery = $gallery_mapper->find($galleryID);
		if (!$gallery->path)
		{
			self::show_error(__('Failure in database, no gallery path set.', 'nextgen-public-uploader'));
			return;
		}

		// Read Image List
		foreach($_FILES as $key => $value) {
			if ($_FILES[$key]['error'] == 0)
			{
				try {
					if ($storage->is_image_file($_FILES[$key]['tmp_name']))
					{
						$image = $storage->object->upload_base64_image(
							$gallery,
							file_get_contents($_FILES[$key]['tmp_name']),
							$_FILES[$key]['name']
						);

						if (get_option('npu_exclude_select'))
						{
							$image->exclude = 1;
							$image_mapper->save($image);
						}

						// Add to Image and Dir List
						$this->arrImgNames[]  = $image->filename;
						$this->arrImageIds[]  = $image->id();
						$this->strGalleryPath = $gallery->path;
					}
					else {
						unlink($_FILES[$key]['tmp_name']);
						self::show_error('<strong>' . $_FILES[$key]['name'] . '</strong>' . __( 'is not a valid file.', 'nextgen-public-uploader'));
						continue;
					}
				}
				catch (E_NggErrorException $ex) {
					self::show_error('<strong>' . $ex->getMessage() . '</strong>');
					continue;
				}
				catch (Exception $ex) {
					self::show_error('<strong>' . $ex->getMessage() . '</strong>');
					continue;
				}
			}
		}
	} // End Function

	function upload_images_widget() {
		global $wpdb;
		// Image Array
		$imageslist = array();
		// Get Gallery ID
		$galleryID = (int)$_POST['galleryselect'];
		if ( $galleryID == 0 ) {
			if( get_option( 'npu_default_gallery' ) ) {
				$galleryID = get_option( 'npu_default_gallery' );
			} else {
				self::show_error( __( 'No gallery selected.', 'nextgen-public-uploader' ) );
				return;
			}
		}
		// Get Gallery Path
		$gallerypath = $wpdb->get_var( "SELECT path FROM $wpdb->nggallery WHERE gid = '$galleryID' " );
		if ( ! $gallerypath ) {
			self::show_error( __( 'Failure in database, no gallery path set.', 'nextgen-public-uploader' ) );
			return;
		}
		// Read Image List
		$dirlist = $this->scandir( ABSPATH . $gallerypath );
		foreach( $_FILES as $key => $value ) {
			if ( $_FILES[$key]['error'] == 0 ) {
				$temp_file = $_FILES[$key]['tmp_name'];
				$filepart = pathinfo ( strtolower( $_FILES[$key]['name'] ) );
				// Required Until PHP 5.2.0
				$filepart['filename'] = substr( $filepart["basename"], 0, strlen( $filepart["basename"] ) - ( strlen( $filepart["extension"] ) + 1 ) );
				$filename = sanitize_title( $filepart['filename'] ) . '.' . $filepart['extension'];
				// Allowed Extensions
				$ext = array( 'jpeg', 'jpg', 'png', 'gif' );
				if ( !in_array( $filepart['extension'], $ext ) || !@getimagesize( $temp_file ) ){
					self::show_error( '<strong>' . $_FILES[$key]['name'] . '</strong>' . __( 'is not a valid file.', 'nextgen-public-uploader' ) );
					continue;
				}
				// Check If File Exists
				$i = 0;
				while ( in_array( $filename, $dirlist ) ) {
					$filename = sanitize_title( $filepart['filename'] ) . '_' . $i++ . '.' . $filepart['extension'];
				}
				$dest_file = ABSPATH . $gallerypath . '/' . $filename;
				// Check Folder Permissions
				if ( !is_writeable( ABSPATH . $gallerypath ) ) {
					$message = sprintf( __( 'Unable to write to directory %s. Is this directory writable by the server?', 'nextgen-public-uploader' ), ABSPATH . $gallerypath );
					self::show_error( $message );
					return;
				}
				// Save Temporary File
				if ( ! @move_uploaded_file( $_FILES[$key]['tmp_name'], $dest_file ) ) {
					self::show_error( __( 'Error, the file could not moved to: ', 'nextgen-public-uploader' ) . $dest_file );
					$this->check_safemode( ABSPATH . $gallerypath );
					continue;
				}
				if ( ! $this->chmod( $dest_file ) ) {
					self::show_error( __( 'Error, the file permissions could not set.', 'nextgen-public-uploader' ) );
					continue;
				}
				// Add to Image and Dir List
				$imageslist[] = $filename;
				$dirlist[] = $filename;
			}
		}
		if ( count( $imageslist ) > 0 ) {
			if ( ! get_option( 'npu_exclude_select' ) ) {
				$npu_exclude_id = 0;
			} else {
				$npu_exclude_id = 1;
			}
			// Add Images to Database
			$image_ids = $this->add_Images( $galleryID, $imageslist );
			$this->arrThumbReturn = array();

			foreach ( $image_ids as $pid ) { //TODO: prepare
				$wpdb->query( "UPDATE $wpdb->nggpictures SET exclude = '$npu_exclude_id' WHERE pid = '$pid'" );
				$this->arrThumbReturn[] = $this->create_thumbnail( $pid );
			}

			$this->arrImageIds    = array();
			$this->arrImageIds    = $image_ids;
			$this->arrImageNames  = array();
			$this->arrImageNames  = $imageslist;
			$this->strGalleryPath = $gallerypath;
		}
		return;
	} // End Function

	public static function show_error( $msg ) {
		if ( is_user_logged_in() && apply_filters( 'uploader_ngg_admin_show_error', true ) ) {
			nggGallery::show_error( $msg );
		}
	}
}
