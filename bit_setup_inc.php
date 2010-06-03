<?php
global $gBitSystem, $gBitUser, $gBitThemes;

define( 'LIBERTY_SERVICE_ITAGS', 'itags' );

$registerHash = array(
	'package_name' => 'itags',
	'package_path' => dirname( __FILE__ ).'/',
	'service' => LIBERTY_SERVICE_ITAGS,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'itags' ) && $gBitUser->hasPermission( 'p_itags_view' )) {
	// load css file
	$gBitThemes->loadCss( UTIL_PKG_PATH.'javascript/libs/jquery/themes/base/ui.all.css', TRUE );
$gBitThemes->loadCss( ITAGS_PKG_PATH.'css/imagetag.css' );

	require_once( ITAGS_PKG_PATH.'LibertyItag.php' );
/*
	$menuHash = array(
		'package_name'  => ITAGS_PKG_NAME,
		'index_url'     => ITAGS_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:itags/menu_itags.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );
*/
	$gLibertySystem->registerService( 
		LIBERTY_SERVICE_ITAGS, 
		ITAGS_PKG_NAME, 
		array(
			'content_display_function' 	=> 'itags_content_display',
			'content_list_sql_function' => 'itags_content_list_sql',
			'content_store_function'  	=> 'itags_content_store',
			'content_expunge_function'  => 'itags_content_expunge',
		),
		array( 
			'description' => tra( 'Enables the addition of images tags to any image content' ),	
		)
	);
}
?>