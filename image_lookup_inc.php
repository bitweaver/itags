<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_itags/image_lookup_inc.php,v 1.1 2010/06/03 15:49:50 lsces Exp $
 * @package itags
 * @subpackage functions
 */

global $gContent, $gGallery;


if( $gContent = FisheyeImage::lookup( $_REQUEST ) ) {
	// nothing to do. ::lookup will do a full load
} else {
	$gContent = new FisheyeImage();
	$imageId = NULL;
}

// This user does not own this image and they have not been granted the permission to edit this image
$gContent->verifyViewPermission();

$gBitSmarty->assign_by_ref('gContent', $gContent);
$gBitSmarty->assign_by_ref('imageId', $gContent->mImageId );

?>
