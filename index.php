<?php
require_once( "../kernel/setup_inc.php" );
require_once( ITAGS_PKG_PATH."LibertyItag.php" );

$gBitSystem->verifyPackage( 'itags' );

$gBitSystem->verifyPermission('p_itags_view');

$itags = new LibertyItag();

$_REQUEST['max_records'] = !empty( $_REQUEST['max_records'] ) ? $_REQUEST['max_records'] : NULL;
$listHash = $_REQUEST;
$tagHash = $_REQUEST;

if( isset($_REQUEST['attachment']) ){
// display image with tags
}else{
	$listData = $itags->getList( $listHash );
	$gBitSmarty->assign( 'itag_attachments', $listData );
	$gBitSystem->display( 'bitpackage:itags/attachments.tpl', tra( 'Itags' ) , array( 'display_mode' => 'display' ));
	
}
?>
