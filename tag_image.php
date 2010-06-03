<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_itags/tag_image.php,v 1.3 2010/06/03 16:47:21 lsces Exp $
 * @package itags
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

global $gBitSystem, $gDebug;

include_once( ITAGS_PKG_PATH.'image_lookup_inc.php' );

$tag = new LibertyItag( $gContent->mInfo['image_file']['attachment_id'] );
		
if( !empty( $_REQUEST['mode'] ) ) {
	if ( !empty( $_REQUEST['save'] ) and $_REQUEST['save'] == 'yes' ) {
		// save itag record
		$tag->store( $_REQUEST );
	} 
	$gBitSmarty->assign( 'mode', $_REQUEST['mode'] );
}

if( !empty( $_REQUEST['delete'] ) ) {
	$tag->expunge_tag( $_REQUEST['delete'] );
}

$tag->load();

$gBitThemes->loadAjax( 'jquery' );
$gBitThemes->loadJavascript( UTIL_PKG_PATH.'javascript/libs/jquery/full/ui/jquery.ui.all.js', FALSE, 500, FALSE );
$gBitThemes->loadJavascript( ITAGS_PKG_PATH.'scripts/imagetag.js', FALSE, 500, FALSE );

// this will let LibertyMime know that we want to display the original image
$gContent->mInfo['image_file']['original'] = TRUE;
$gContent->mInfo['itags'] = $tag->mInfo['itags'];

if( !$gContent->isValid() ) {
	$gBitSystem->setHttpStatus( 404 );
	$gBitSystem->fatalError( tra( "No image exists with the given ID" ) ,'error.tpl', '' );
}

// $displayHash = array( 'perm_name' => 'p_fisheye_view' );
$gContent->invokeServices( 'content_display_function', $displayHash );

// Get the proper thumbnail size to display on this page
if( empty( $_REQUEST['size'] )) {
	$_REQUEST['size'] = 'original';
}

$gBitSystem->setBrowserTitle( $gContent->getTitle() );
$gBitSystem->display( 'bitpackage:itags/tag_image.tpl' , NULL, array( 'display_mode' => 'display' ));?>