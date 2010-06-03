<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_itags/LibertyItag.php,v 1.3 2010/06/03 20:00:00 lsces Exp $
 * created 2006/08/01
 * @author Will <will@onnyturf.com>
 *
 * @package geo
 */

/**
 * Initialize
 */
require_once( KERNEL_PKG_PATH.'BitBase.php' );

/**
 * @package geo
 */
class LibertyItag extends LibertyBase {
	var $mAttachementId;

	function LibertyItag( $pAttachementId=NULL ) {
		LibertyBase::LibertyBase();
		$this->mAttachementId = $pAttachementId;
	}

	/**
	 * Load the data from the database
	 * @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	 **/
	function load() {
//		if( $this->isValid() ) {
			$query = "SELECT iia.`comment_id` as tag_no, lc.`title` as description, iia.*
			FROM `".BIT_DB_PREFIX."itags_image_areas` iia 
			JOIN `".BIT_DB_PREFIX."liberty_comments` lcm ON lcm.`comment_id` = iia.`comment_id`
			JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.`content_id` = lcm.`content_id`
			WHERE iia.`attachment_id` = ?";
			$this->mInfo['itags'] = $this->mDb->getAssoc( $query, array( $this->mAttachementId ) );
//		}
		return( count( $this->mInfo ) );
	}

	/**
	 * @param array pParams hash of values that will be used to store the page
	 * @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	 * @access public
	 **/
	function store( &$pParamHash ) {

		if( $this->verify( $pParamHash ) ) {
			$this->mDb->StartTrans();
			$storeComment = new LibertyComment( @BitBase::verifyId( $pParamHash['comment_id'] ) ? $pParamHash['comment_id'] : NULL );
			if ( $this->verifyId( $storeComment->mCommentId ) ) {
				$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."itags_image_areas` WHERE `attachment_id`=? and `comment_id`=?", array( $this->mAttachmentId, $storeComment->mCommentId ) );
			}
			if( $storeComment->storeComment( $pParamHash['comment'] )) {
				// store successful
				$storeComment->loadComment();
				$this->mDb->query( "INSERT INTO `".BIT_DB_PREFIX."itags_image_areas` ( `attachment_id`, `comment_id`, `itag_top`, `itag_left`, `itag_width`, `itag_height` )
					VALUES ( ?, ?, ?, ?, ?, ? )", 
					array( $this->mAttachementId, $storeComment->mCommentId, $pParamHash['top'], $pParamHash['left'], $pParamHash['width'], $pParamHash['height'] ) );
			}

			$this->mDb->CompleteTrans();
			$this->load();
		}
		return( count( $this->mErrors )== 0 );
	}

	/**
	 * Make sure the data is safe to store
	 * @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	 * @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	 * @access private
	 **/
	function verify( &$pParamHash ) {
		$pParamHash['itags_store'] = array();

		$pParamHash['comment']['content_id'] = $this->mAttachementId;
		$pParamHash['comment']['comments_parent_id'] = $this->mAttachementId;
		$pParamHash['comment']['comment_title'] = $pParamHash['description'];
		$pParamHash['comment']['comment_data'] = $pParamHash['description'];

		return( count( $this->mErrors )== 0 );
	}

	/**
	 * check if the mAttachemntId is set and valid
	 */
	function isValid() {
		return( @BitBase::verifyId( $this->mAttachemntId ) );
	}

	/**
	 * This function removes a set of image area tags
	 **/
	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$query = "DELETE FROM `".BIT_DB_PREFIX."itags_image_areas` WHERE `attachment_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mAttachemntId ) );
		}
		return $ret;
	}

	/**
	 * This function removes a single image area tag
	 **/
	function expunge_tag( $itagId ) {
		$ret = FALSE;
		$storeComment = new LibertyComment( @BitBase::verifyId( $itagId ) ? $itagId : NULL );
		$storeComment->mDb->StartTrans();

		if ( $storeComment->verifyId( $storeComment->mCommentId ) ) {
			$storeComment->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."itags_image_areas` WHERE `attachment_id`=? and `comment_id`=?"
			, array( $this->mAttachementId, $storeComment->mCommentId ) );
		}
		$storeComment->expunge();
		$storeComment->mDb->CompleteTrans();
		$this->load();

		return $ret;
	}

	/**
	* This function gets a list of tags
	**/
	function getList( &$pListHash ) {
		global $gBitUser, $gBitSystem;
		$this->prepGetList( $pListHash );

		$query = "SELECT DISTINCT iia.`attachment_id`
		FROM `".BIT_DB_PREFIX."itags_image_areas` iia";
		$ret = $this->mDb->getAssoc( $query );
		
		
	}
}

/********* SERVICE FUNCTIONS *********/
function itags_content_display( &$pObject ) {
	global $gBitSystem, $gBitSmarty, $gBitUser;
	
	if( method_exists( $pObject, 'getContentType' ) && $gBitSystem->isFeatureActive( 'tags_tag_'.$pObject->getContentType()) ){
		if ( $gBitSystem->isPackageActive( 'itags' ) ) {
			if( $gBitUser->hasPermission( 'p_itags_view' ) ) {
				$tag = new LibertyItag( $pObject->mAttachmentId );
				if( $tag->load() ) {
					$gBitSmarty->assign( 'itagData', !empty( $tag->mInfo['itags'] ) ? $tag->mInfo['itags'] : NULL );
				}
			}
		}
	}
}

function itags_content_load_sql() {
	global $gBitSystem;
	$ret = array();
	$ret['select_sql'] = " , itags.`comment_id` , itags.`itag_top`, itags.`itag_left`, itags.`itag_width`, itags.`itag_height`";
	$ret['join_sql'] = " LEFT JOIN `".BIT_DB_PREFIX."itags_image_areas` itags ON ( la.`attachment_id`=itags.`attachment_id` )";
	return $ret;
}
/**
 * @param $pParamHash['up']['lng'], $pParamHash['up']['lat'], $pParamHash['down']['lng'], $pParamHash['down']['lat']
 **/
function itags_content_list_sql( &$pObject, $pParamHash=NULL ) {
	global $gBitSystem;
	$ret = array();
	if ( !empty( $pObject->mInfo['attachment_id'] ) ) {
		$ret['select_sql'] = " , itags.`comment_id` , itags.`itag_top`, itags.`itag_left`, itags.`itag_width`, itags.`itag_height`";
		$ret['join_sql'] = " LEFT JOIN `".BIT_DB_PREFIX."itags_image_areas` itags ON ( itags.`attachment_id` = ".$pObject->mInfo['attachment_id'].")";
		$ret['where_sql'] = "";
	}
	return $ret;
}

function itags_content_store( &$pObject, &$pParamHash ) {
	global $gBitSystem;
	$errors = NULL;
	// If a content access system is active and we have geo in our store hash, let's call it
	if( $gBitSystem->isPackageActive( 'itags' ) && !empty( $pParamHash['itags'] ) ) {
		$itags = new LibertyItag( $pObject->mAttachmentId );

		// if both lat and lng fields are empty then the user is trying to clear geo data
		if( empty( $pParamHash['itags']['top'] ) && empty( $pParamHash['itags']['left'] ) ) {
			$geo->expunge();
		// store the geo data
		} elseif ( !$itags->store( $pParamHash ) ) {
			$errors=$itags->mErrors;
		}
	}
	return( $errors );
}

function itags_content_expunge( &$pObject ) {
//	$itags = new LibertyItag( $pObject->mAttachmentId );
//	$itags->expunge();
}
?>
