{strip}
<div class="display fisheye">
	{if !$liberty_preview}
		<div class="floaticon">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$gContent->mInfo}
			{* if $gContent->hasUpdatePermission() and CHECK FOR fisheye ... add TREASURY options }
				<a title="{tr}Edit{/tr}" href="{$smarty.const.FISHEYE_PKG_URL}edit_image.php?image_id={$gContent->mImageId}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="Edit Image"}</a>
				<a title="{tr}Delete{/tr}" href="{$smarty.const.FISHEYE_PKG_URL}edit_image.php?image_id={$gContent->mImageId}&amp;delete=1">{biticon ipackage="icons" iname="edit-delete" iexplain="Delete Image"}</a>
			{/if *}
		</div>
	{/if}

	{formfeedback hash=$feedback}

	<div class="header">
		<h1>{$gContent->getTitle()|default:$gContent->mInfo.filename|escape}</h1>
	</div>

    <div class="body">
		<div id="taggingArea">
			<img src="{$gContent->mInfo.source_url}" />
			{if $mode == 'edit' }
				<div id="drag" class="ui-widget-content"></div>
			{else}
				{if $gContent->mInfo.itags }
					{foreach from=$gContent->mInfo.itags item=resTags key=itemContentId}
						<div class=tag style="position:absolute;width:{$resTags.itag_width}px;height:{$resTags.itag_height}px;top:{$resTags.itag_top}px;left:{$resTags.itag_left}px;">
							{$resTags.description}
						</div>
					{/foreach}
				{/if}
			{/if}
		</div>

		{if $mode == 'edit' }
			<div id="formArea">
				<form method=post>
					<input type=hidden name=save value=yes>
					<input type=hidden name=pic value="{$gContent->getTitle()|default}">
					<input type=hidden name=width id=width>
					<input type=hidden name=height id=height>
					<input type=hidden name=top id=top>
					<input type=hidden name=left id=left>
					Description : <input type=text name=description>
					<input type=submit value=save>
				</form>
				{foreach from=$gContent->mInfo.itags item=resTags key=itemContentId}
					Tag # {$resTags.comment_id} - {$resTags.description} <a href="tag_image.php?image_id={$gContent->mImageId}&mode=edit&delete={$resTags.comment_id}">Delete</a><br />
				{/foreach}
				<a href="tag_image.php?image_id={$gContent->mImageId}">Go to view mode</a>
			</div>]
		{else}
			<div id="formArea">
				<a href="tag_image.php?image_id={$gContent->mImageId}&mode=edit">Go to edit mode</a>
			</div>
		{/if}

	</div>

	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$gContent->mInfo}
	</div>	<!-- end .fisheye -->
{/strip}
