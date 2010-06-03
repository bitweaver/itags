<?php
$tables = array(
  'itags_image_areas' => "
	attachment_id I4 PRIMARY NOTNULL,
	comment_id I4 PRIMARY NOTNULL,
	itag_top I4 DEFAULT 0 NOTNULL ,
	itag_left I4 DEFAULT 0 NOTNULL ,
	itag_width I4 DEFAULT 100 NOTNULL ,
	itag_height I4 DEFAULT 100 NOTNULL 
	CONSTRAINT '
		, CONSTRAINT `lib_attachment_itag_id_ref`  FOREIGN KEY (`attachment_id`) REFERENCES `".BIT_DB_PREFIX."liberty_attachments` (`attachment_id`)
		, CONSTRAINT `lib_attachment_itag_cid_ref` FOREIGN KEY (`comment_id`)    REFERENCES `".BIT_DB_PREFIX."liberty_comments`    (`comment_id`) '
  "
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( ITAGS_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( ITAGS_PKG_NAME, array(
	'description' => "A Liberty Service that any package can use to add comments to areas of an image.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

$gBitInstaller->registerPreferences( ITAGS_PKG_NAME, array(
	array( ITAGS_PKG_NAME, 'itags_in_view', 'y' ),
) );

// ### Sequences
$sequences = array (
  'itags_tag_id_seq' => array( 'start' => 1 ),
);
$gBitInstaller->registerSchemaSequences( ITAGS_PKG_NAME, $sequences );


// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( ITAGS_PKG_NAME, array(
	array( 'p_itags_admin', 'Can admin tags', 'admin', ITAGS_PKG_NAME ),
	array( 'p_itags_create', 'Can create tags', 'registered', ITAGS_PKG_NAME ),
	array( 'p_itags_view', 'Can view tags', 'basic', ITAGS_PKG_NAME ),
) );

// Requirements
$gBitInstaller->registerRequirements( ITAGS_PKG_NAME, array(
    'liberty' => array( 'min' => '2.1.4' ),
));
