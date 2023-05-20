<?php
/*
SMF Gallery Pro Edition
Version 9.0
by: vbgamer45
https://www.smfhacks.com
Copyright 2006-2021 https://www.samsonsoftware.com

############################################
License Information:
SMF Gallery is NOT free software.
This software may not be redistributed.

The pro edition license is good for a single instance / install on a website.
You are allowed only one active install for each license purchase.

Links to https://www.smfhacks.com must remain unless
branding free option is purchased.
#############################################
*/

function template_mainview()
{
	global $scripturl, $txt, $context, $user_info, $modSettings, $subcats_linktree, $gallerySettings, $sourcedir;
	
	require_once($sourcedir . '/Music/artists_module.php');
	
	// Permissions
	$g_manage = allowedTo('smfgallery_manage');


	if ($g_manage)
	{
		// Warn the user if they are managing the gallery that it is not writable
		if (!is_writable($modSettings['gallery_path']))
			echo '<font color="#FF0000"><b>', $txt['gallery_write_error'], $modSettings['gallery_path'], '</b></font>';
	}

	ShowTopGalleryBar($txt['gallery_text_title']);

	$showstatstop = false;
	$showadminlinktop = false;
	if (isset($modSettings['gallery_index_showstatstop']))
		$showstatstop = $modSettings['gallery_index_showstatstop'];

	if (isset($modSettings['gallery_index_showadmintop']))
		$showadminlinktop = $modSettings['gallery_index_showadmintop'];

	// See if they are allowed to add catagories Main Index only
	if ($g_manage == true && $showadminlinktop == true)
	{
		gallery_showadminbar();
	}

	if ($showstatstop == true)
	{
		// Show stats link
		echo '
		<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_stats_title'], '
		</h3>
</div>
 <table class="table_grid">
	<tr class="windowbg2">
		<td align="center"><a href="' . $scripturl . '?action=gallery;sa=stats">', $txt['gallery_stats_viewstats'] ,'</a></td>
	</tr>
</table>
<br />';
	}

	//Show the index page blocks
	if ($modSettings['gallery_index_showtop'])
	{
		// Recent
		if (!empty($modSettings['gallery_index_recent']))
			Gallery_MainPageBlock($txt['gallery_main_recent'], 'recent');
		// Most Viewed
		if (!empty($modSettings['gallery_index_mostviewed']))
			Gallery_MainPageBlock($txt['gallery_main_viewed'], 'viewed');
		// Most commented images
		if (!empty($modSettings['gallery_index_mostcomments']))
			Gallery_MainPageBlock($txt['gallery_main_mostcomments'], 'mostcomments');
		// Top Rated
		if (!empty($modSettings['gallery_index_toprated']))
			Gallery_MainPageBlock($txt['gallery_main_toprated'], 'toprated');

		// Most Liked
		if (!empty($modSettings['gallery_index_mostliked']))
			Gallery_MainPageBlock($txt['gallery_main_mostliked'], 'mostliked');

		// Recent Comments
		if (!empty($gallerySettings['gallery_index_recentcomments']))
			Gallery_MainPageBlock($txt['gallery_main_recentcomment'], 'recentcomments');
		// Random
		if (!empty($gallerySettings['gallery_index_randomimages']))
			Gallery_MainPageBlock($txt['gallery_main_random'], 'random');

		if (!empty($gallerySettings['gallery_index_show_tag_cloud']))
			Gallery_ShowTagCloud();


	}

	// List all the categories


	echo '
<table border="0" cellspacing="1" cellpadding="4" class="table_grid"  align="center" width="100%">
<thead>
<tr class="title_bar">
		<th class="lefttext first_th">&nbsp;</th>
		<th class="lefttext">', $txt['gallery_text_galleryname'], '</th>
		<th class="centertext" align="center">', $txt['gallery_text_totalimages'], '</th>';

		$num_cols = 3;

		if (!empty($gallerySettings['gallery_set_show_cat_latest_pictures']))
		{
			echo '<th class="lefttext" align="center">', $txt['gallery_latest_posts'], '</th>';
			$num_cols++;
		}

	if ($g_manage)
	{
		echo '
		<th class="lefttext">', $txt['gallery_text_reorder'], '</th>
		<th class="lefttext last_th">', $txt['gallery_text_options'], '</th>';
		$num_cols = $num_cols + 2;
	}

	echo '
	</tr>
	</thead>
	';

	foreach($context['gallery_categorylist'] as $row)
	{
		$cat_url = '';

		// Check permission to show this category
		if ($row['view'] == '0')
			continue;

		// Check if category is normal or a User Category
		if ($row['redirect'] == 0)
		{
			$totalpics  = GetPictureTotals($row['id_cat'], $row['total']);
			$cat_url = $scripturl . '?action=gallery;cat=' . $row['id_cat'];
		}
		else
		{
			$subcats_linktree = '';
			// Check if they want the user galleries to show on the gallery index
			if ($modSettings['gallery_index_showusergallery'] == 0)
				continue;

			// User Gallery

			$cat_url = $scripturl . '?action=gallery;su=user;sa=userlist';

			$totalpics = $context['gallery_usercat_pictotal'];
		}

		echo '
	<tr>';

		if ($row['image'] == '' && $row['filename'] == '')
			echo '
		<td class="windowbg" width="10%"></td><td  class="windowbg2"><b><a href="' . $cat_url . '">' . parse_bbc($row['title']) . '</a></b>' . ((!empty($gallerySettings['gallery_enable_rss']) && ($row['redirect'] == 0)) ? ' <a href="' . $scripturl . '?action=gallery;sa=rss;cat=' . $row['id_cat'] . '"><img src="' . $modSettings['gallery_url'] . '/rss.png" alt="rss" /></a>' : '') .  '<br />' . parse_bbc($row['description']) . '</td>';
		else
		{
			if ($row['filename'] == '')
				echo '
		<td class="windowbg" width="10%"><a href="' . $cat_url . '"><img src="' . $row['image'] . '" alt=""  /></a></td>';
			else
				echo '
		<td class="windowbg" width="10%"><a href="' . $cat_url . '"><img src="' . $modSettings['gallery_url'] . 'catimgs/' . $row['filename'] . '" alt="" /></a></td>';


			echo '
		<td class="windowbg2"><b><a href="' . $cat_url . '">' . parse_bbc($row['title']) . '</a></b>' . ((!empty($gallerySettings['gallery_enable_rss']) && ($row['redirect'] == 0)) ? ' <a href="' . $scripturl . '?action=gallery;sa=rss;cat=' . $row['id_cat'] . '"><img src="' . $modSettings['gallery_url'] . '/rss.png" alt="rss" /></a>' : '') .  '<br />' . parse_bbc($row['description']) . '</td>';
		}

		// Show total pictures in the category
		echo '
		<td align="center" valign="middle" class="windowbg">', $totalpics, '</td>';

			// Show Last Post
			if (!empty($gallerySettings['gallery_set_show_cat_latest_pictures']))
			{
				echo '<td class="windowbg">';
				if (!empty($row['id_picture']))
				{

					// Disable member color link
					if (!empty($modSettings['gallery_disable_membercolorlink']))
						$row['online_color'] = '';

					echo '<span class="smalltext">' .  $txt['gallery_last_post'], ' <a href="',$scripturl,'?action=profile;u=',$row['id_member'],'"' . (!empty($row['online_color']) ? ' style="color: ' . $row['online_color'] . ';" ' :'' ) . '>',$row['real_name'],'</a><br />',

					'<a href="' . $scripturl . '?action=gallery;sa=view;id=',$row['id_picture'],'">',shorten_subject($row['pictitle'], 24) . '</a><br />',$txt['gallery_txt_on'],timeformat($row['date']) . '</span>';
				}

				echo '</td>';
			}

		// Show Edit Delete and Order category
		if ($g_manage)
		{
			echo '
		<td class="windowbg2"><a href="' . $scripturl . '?action=gallery;sa=catup;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_up'] . '</a>&nbsp;<a href="' . $scripturl . '?action=gallery;sa=catdown;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_down'] . '</a></td>
		<td class="windowbg"><a href="' . $scripturl . '?action=gallery;sa=editcat;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_edit'] . '</a>&nbsp;';

			if ($row['redirect'] == 0)
			{
				echo '
			<a href="' . $scripturl . '?action=gallery;sa=deletecat;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_delete'] . '</a>
			<br /><br />
			<a href="' . $scripturl . '?action=gallery;sa=catperm;cat=' . $row['id_cat'] . '">[' . $txt['gallery_text_permissions'] . ']</a>
			<br />
			<a href="' . $scripturl . '?action=gallery;sa=import;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_importpics'] . '</a>
			<br />
			<a href="' . $scripturl . '?action=gallery;sa=regen;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_regeneratethumbnails'] . '</a>';
			}

			echo '
		</td>';
		}

		echo '
	</tr>';

//		if ($subcats_linktree  != '')
//			echo '
//	<tr>
//		<td colspan="',$num_cols,'" class="windowbg3">
//			<span class="smalltext">',($subcats_linktree != '' ? '<b>' . $txt['gallery_sub_cats'] . '</b>' . $subcats_linktree : ''),'</span>
//		</td>
//	</tr>';
	}


	echo '
</table>
<br /><br />';

	// Show the index page blocks
	if (empty($modSettings['gallery_index_showtop']))
	{
		// Recent
		if (!empty($modSettings['gallery_index_recent']))
			Gallery_MainPageBlock($txt['gallery_main_recent'], 'recent');
		// Most Viewed
		if (!empty($modSettings['gallery_index_mostviewed']))
			Gallery_MainPageBlock($txt['gallery_main_viewed'], 'viewed');
		// Most commented images
		if (!empty($modSettings['gallery_index_mostcomments']))
			Gallery_MainPageBlock($txt['gallery_main_mostcomments'], 'mostcomments');
		// Top Rated
		if (!empty($modSettings['gallery_index_toprated']))
			Gallery_MainPageBlock($txt['gallery_main_toprated'], 'toprated');

		// Most Liked
		if (!empty($modSettings['gallery_index_mostliked']))
			Gallery_MainPageBlock($txt['gallery_main_mostliked'], 'mostliked');

		// Recent Comments
		if (!empty($gallerySettings['gallery_index_recentcomments']))
			Gallery_MainPageBlock($txt['gallery_main_recentcomment'], 'recentcomments');
		// Random
		if (!empty($gallerySettings['gallery_index_randomimages']))
			Gallery_MainPageBlock($txt['gallery_main_random'], 'random');
		// Show Tag Cloud
		if (!empty($gallerySettings['gallery_index_show_tag_cloud']))
			Gallery_ShowTagCloud();

	}

	if ($showstatstop == false)
	{
		// Show stats link
		echo '
<br />
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_stats_title'], '
		</h3>
</div>
 <table class="table_grid">
	<tr class="windowbg2">
		<td align="center"><a href="' . $scripturl . '?action=gallery;sa=stats">', $txt['gallery_stats_viewstats'] ,'</a></td>
	</tr>
</table><br />';
	}

	// See if they are allowed to add categories Main Index only
	if ($g_manage == true && $showadminlinktop == false)
	{
		gallery_showadminbar();
	}


}

function template_add_category()
{
	global $scripturl, $txt, $context, $settings, $modSettings, $sourcedir;
	
	require_once($sourcedir . '/Music/artists_module.php');
	
	ShowTopGalleryBar();

	@$parent  = (int) $_REQUEST['cat'];

	echo '<div class="tborder">';
	echo '
<form method="post" enctype="multipart/form-data" name="catform" id="catform" action="' . $scripturl . '?action=gallery;sa=addcat2" onsubmit="submitonce(this);">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_addcategory'], '
		</h3>
  </div>
  <div class="post_area">
<div class="roundframe noup">
<dl id="post_header">
	<dt><b>' . $txt['gallery_form_title'] .'</b>&nbsp;</dt>
	<dd><input type="text" name="title" size="64" maxlength="100" /></dd>


	<dt><b>' . $txt['gallery_text_parentcategory'] .'</b>&nbsp;</dt>
	<dd><select name="parent">
	<option value="0">' . $txt['gallery_text_catnone'] . '</option>
	';

	foreach ($context['gallery_cat'] as $i => $category)
		echo '<option value="' . $category['id_cat']  . '" ' . (($parent == $category['id_cat']) ? ' selected="selected"' : '') .'>' . $category['title'] . '</option>';

	echo '</select>

	</dd>
   <!------DD Edit------>
	<dt><b>' . $txt['gallery_artists'] .'</b>&nbsp;</dt>
	<dd>' . GetArtistList(0) .'</dd>
	
	<dt><b>' . $txt['gallery_artists_types'] .'</b>&nbsp;</dt>
	<dd>
		<select name=gallery_type value="">Gallery Type</option>
		<option value="1"selected="selected">Pictures</option>
		<option value="2">Music</option>
		<option value="3">Videos</option>
		<option value="4">Live General</option>
		<option value="5">Tour</option></select>
	</dd>
	';
	
	if (!function_exists('getLanguages'))
	{

			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '



										', template_control_richedit($context['post_box_name'], 'bbc'), '

								';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']))
			echo '

										', template_control_richedit($context['post_box_name'], 'smileys'), '
									';

		// Show BBC buttons, smileys and textbox.
		echo '

										', template_control_richedit($context['post_box_name'], 'message'), '

							';

	}
	else
	{


			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Show BBC buttons, smileys and textbox.
		echo '
					', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');



	}



	if ($context['show_spellchecking'])
		echo '
									<br /><input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'catform\', \'description\');" />';
	echo '

</dl>
<table width="100%" align="center">

<tr class="windowbg2">
	<td width="28%" align="right"><b>' . $txt['gallery_form_icon'] . '</b>&nbsp;</td>
	<td width="72%"><input type="text" name="image" size="64" maxlength="100" /></td>
</tr>
<tr class="windowbg2">
	<td width="28%" align="right"><b>' . $txt['gallery_form_uploadicon'] . '</b>&nbsp;</td>
	<td width="72%">';

	// Warn the user if they are category image path is not writable
	if (!is_writable($modSettings['gallery_path'] . 'catimgs'))
		echo '<font color="#FF0000"><b>' . $txt['gallery_write_catpatherror']  . $modSettings['gallery_path'] . 'catimgs' . '</b></font>';

	echo '
	<input type="file" size="48" name="picture" /></td>
</tr>
<tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_text_cat_disableratings'] . '</b>&nbsp;</td>
	<td width="72%"><input type="checkbox" name="disablerating" />	</td>
</tr>


<tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_text_lock_category'] . '</b>&nbsp;</td>
	<td width="72%"><input type="checkbox" name="lockcategory" />	</td>
</tr>

<tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_txt_tweetnewitems'] . '</b>&nbsp;</td>
	<td width="72%"><input type="checkbox" name="tweet_items" />	</td>
</tr>

<tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_txt_sortby']  . '</b>&nbsp;</td>
	<td width="72%"><select name="sortby">
		<option value="date">',$txt['gallery_txt_sort_date'],'</option>
		<option value="title">',$txt['gallery_txt_sort_title'],'</option>
		<option value="mostview">',$txt['gallery_txt_sort_mostviewed'],'</option>
		<option value="mostcom">',$txt['gallery_txt_sort_mostcomments'],'</option>
		<option value="mostrated">',$txt['gallery_txt_sort_mostrated'],'</option>
		</select>
	</td>
</tr>
<tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_txt_orderby'] . '</b>&nbsp;</td>
	<td width="72%"><select name="orderby">
		<option value="desc"' . (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'desc' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_desc'],'</option>
		<option value="asc"' . (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'asc' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_asc'],'</option>
		</select>
	</td>
</tr>
<tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_txt_category_display_mode']  . '</b>&nbsp;</td>
	<td width="72%"><select name="displaymode">
		<option value="0">',$txt['gallery_txt_display_mode0'] ,'</option>
		<option value="1">',$txt['gallery_txt_display_mode1'] ,'</option>
		<option value="2">',$txt['gallery_txt_display_mode2'] ,'</option>
		</select>
	</td>
</tr>


<tr class="windowbg2">
	<td colspan="2" align="center">
	<hr />

	</td>
</tr>
		<tr class="windowbg2">
			<td colspan="2" align="center"><b>'  . $txt['gallery_text_copyperm'] . '</b></td>
		</tr>
			  <tr class="windowbg2">
			  	<td align="right"><b>' . $txt['gallery_text_copyfrom'] . '</b>&nbsp;</td>
			  	<td><select name="copycat">
			  					<option value="0"></option>';

				foreach ($context['gallery_cat'] as $i => $category)
				{
					echo '<option value="' . $category['id_cat']  . '">' . $category['title'] . '</option>';
				}

				echo '</select>
				</td>
			  </tr>
			  <tr class="windowbg2">
			  	<td align="center" colspan="2">
				</td>
			  </tr>

<tr class="windowbg2">
	<td colspan="2" align="center">
	<b>' . $txt['gallery_text_postingoptions'] . '</b>
	<hr />
	' . $txt['gallery_postingoptions_info'] . '
	</td>
</tr>
<tr class="windowbg2">
	<td width="28%" align="right"><b>' . $txt['gallery_text_boardname'] . '</b>&nbsp;</td>
	<td width="72%">
		<select name="boardselect" id="boardselect">';

	foreach ($context['gallery_boards'] as $key => $option)
		 echo '
			<option value="' . $key . '">' . $option . '</option>';

	echo '
		</select>
	</td>
</tr>
		<tr class="windowbg2">
			<td width="28%" align="right"><b>'  . $txt['gallery_txt_topicid'] . '</b></td>
			<td width="72%"><input type="text" name="id_topic" size="8" value="0" /></td>
		  </tr>
<tr  class="windowbg2">
	<td colspan="2" align="center"><input type="checkbox" name="fullsize" checked="checked" /><b>' . $txt['gallery_posting_fullsize'] . '</b>&nbsp;
	<br />
	<input type="checkbox" name="showpostlink" checked="checked" /><b>' . $txt['gallery_posting_showlinktoimage'] . '</b>&nbsp;
	<br />
	<input type="checkbox" name="locktopic" /><b>' . $txt['gallery_posting_locktopic'] . '</b>&nbsp;
	</td>
  </tr>
   <tr class="windowbg2">
	<td colspan="2"><hr /></td>
  </tr>
  <tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<input type="submit" value="' . $txt['gallery_text_addcategory'] . '" name="submit" />
	</td>
  </tr>
</table>

	</div>

</div>
</form>';
	echo '</div>';

}

function template_edit_category()
{
	global $scripturl, $txt, $context, $settings, $context, $modSettings, $sourcedir;
	
	require_once($sourcedir . '/Music/artists_module.php');

	ShowTopGalleryBar();

	$cat = $context['catid'];

	$row = $context['gallery_edit_cat'];
	
	echo '
<form method="post" enctype="multipart/form-data" name="catform" id="catform" action="', $scripturl, '?action=gallery;sa=editcat2" onsubmit="submitonce(this);">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_editcategory'] , '
		</h3>
  </div>
  <div class="post_area">
<div class="roundframe noup">
<dl id="post_header">


	<dt><b>', $txt['gallery_form_title'], '</b>&nbsp;</dt>
	<dd><input type="text" name="title" size="64" maxlength="100" value="', $row['title'], '" /></dd>


	<dt><b>', $txt['gallery_text_parentcategory'], '</b>&nbsp;</dt>
	<dd><select name="parent">
	<option value="0">', $txt['gallery_text_catnone'], '</option>
	';

	if ($row['redirect'] == 0)
	{
		foreach ($context['gallery_cat'] as $i => $category)
		{
			if ($category['id_cat'] == $cat)
				continue;

			echo '<option value="' . $category['id_cat']  . '" ' . (($row['id_parent'] == $category['id_cat']) ? ' selected="selected"' : '') .'>' . $category['title'] . '</option>';
		}
	}

	echo '</select>
	</dd>
	 <!------DD Edit------>
	<dt><b>' . $txt['gallery_artists'] .'</b>&nbsp;</dt>
	<dd>' . GetArtistList($row['art_id']) .'</dd>
	<dt><b>' . $txt['gallery_artists_types'] .'</b>&nbsp;</dt>
	<dd>
		<select name=gallerytype value="0">Gallery Type</option>
		<option value="1"' . (($row['gallery_type'] == 1) ? ' selected="selected"' : '') . '>Pictures</option>
		<option value="2"' . (($row['gallery_type'] == 2) ? ' selected="selected"' : '') . '>Music</option>
		<option value="3"' . (($row['gallery_type'] == 3) ? ' selected="selected"' : '') . '>Videos</option>
		<option value="4"' . (($row['gallery_type'] == 4) ? ' selected="selected"' : '') . '>Live General</option>
		<option value="5"' . (($row['gallery_type'] == 5) ? ' selected="selected"' : '') . '>Tour</option></select>
	</dd>
	<!-----End DD Edit------->
   ';

	if (!function_exists('getLanguages'))
	{
	// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '

										', template_control_richedit($context['post_box_name'], 'bbc'), '
							';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']))
			echo '

										', template_control_richedit($context['post_box_name'], 'smileys'), '
									';

		// Show BBC buttons, smileys and textbox.
		echo '

										', template_control_richedit($context['post_box_name'], 'message'), '
								';
	}
	else
	{

			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Show BBC buttons, smileys and textbox.
		echo '
					', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');


	}


	if ($context['show_spellchecking'])
		echo '
									<br /><input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'catform\', \'description\');" />';

	echo '



  </dl>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
	<td width="28%" align="right"><b>' . $txt['gallery_form_icon'] . '</b>&nbsp;</td>
	<td width="72%"><input type="text" name="image" size="64" maxlength="100" value="' . $row['image'] . '" /></td>
  </tr>
   <tr class="windowbg2">
	<td width="28%" align="right"><b>' . $txt['gallery_form_uploadicon'] . '</b>&nbsp;</td>
	<td width="72%">';

	// Warn the user if they are category image path is not writable
	if (!is_writable($modSettings['gallery_path'] . 'catimgs'))
		echo '<font color="#FF0000"><b>' . $txt['gallery_write_catpatherror']  . $modSettings['gallery_path'] . 'catimgs' . '</b></font>';

	echo '
	<input type="file" size="48" name="picture" /></td>
  </tr>';

	if ($row['filename'] != '')
	echo '
  <tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_form_filenameicon'] . '</b>&nbsp;</td>
	<td width="72%">' . $row['filename'] .  '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=catimgdel;id=' . $row['id_cat'] . '">' . $txt['gallery_rep_deletepic'] . '</a></td>
  </tr>';

	// Hide option not for user galleries main category
	if ($row['redirect'] == 0)
	{
		$sortselect = '';
		$orderselect = '';

		switch ($row['sortby'])
		{
			case 'p.id_picture':
				$sortselect = '<option value="date">' . $txt['gallery_txt_sort_date'] . '</option>';

			break;
			case 'p.title':
				$sortselect = '<option value="title">' . $txt['gallery_txt_sort_title'] . '</option>';
			break;

			case 'p.views':
				$sortselect = '<option value="mostview">' . $txt['gallery_txt_sort_mostviewed']  . '</option>';
			break;

			case 'p.commenttotal':
				$sortselect = '<option value="mostcom">' . $txt['gallery_txt_sort_mostcomments'] . '</option>';
			break;

			case 'p.totallikes':
				$sortselect = '<option value="mostcom">' . $txt['gallery_txt_sort_mostliked'] . '</option>';
			break;

			case 'p.totalratings':
				$sortselect = '<option value="mostrated">' . $txt['gallery_txt_sort_mostrated'] . '</option>';
			break;

			default:
				$sortselect = '<option value="date">' . $txt['gallery_txt_sort_date'] . '</option>';
			break;
		}

		switch ($row['orderby'])
		{
			case 'ASC':
				$orderselect = '<option value="asc">' .$txt['gallery_txt_sort_asc'] .'</option>';
			break;

			case 'DESC':
				$orderselect = '<option value="desc">' . $txt['gallery_txt_sort_desc'] . '</option>';
			break;

			default:
				$orderselect = '<option value="DESC">' . $txt['gallery_txt_sort_desc'] .' </option>';
			break;
		}

		echo '
	  <tr class="windowbg2">
		<td width="28%" align="right"><b>' .   $txt['gallery_text_cat_disableratings'] . '</b>&nbsp;</td>
		<td width="72%"><input type="checkbox" name="disablerating" ' . ($row['disablerating'] ? ' checked="checked"' : '') . ' /></td>
	  </tr>
   <tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_text_lock_category'] . '</b>&nbsp;</td>
	<td width="72%"><input type="checkbox" name="lockcategory"  ' . ($row['locked'] ? ' checked="checked"' : '') . ' /></td>
  </tr>

<tr class="windowbg2">
	<td width="28%"  align="right"><b>' .   $txt['gallery_txt_tweetnewitems'] . '</b>&nbsp;</td>
	<td width="72%"><input type="checkbox" name="tweet_items" ' . ($row['tweet_items'] ? ' checked="checked"' : '') . '  /></td>
  </tr>


  <tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_txt_sortby']  . '</b>&nbsp;</td>
	<td width="72%"><select name="sortby">
	',$sortselect,'
		<option value="date">',$txt['gallery_txt_sort_date'],'</option>
		<option value="title">',$txt['gallery_txt_sort_title'],'</option>
		<option value="mostview">',$txt['gallery_txt_sort_mostviewed'],'</option>
		<option value="mostcom">',$txt['gallery_txt_sort_mostcomments'],'</option>
		<option value="mostrated">',$txt['gallery_txt_sort_mostrated'],'</option>
		</select></td>
  </tr>
  <tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_txt_orderby'] . '</b>&nbsp;</td>
	<td width="72%"><select name="orderby">
	',$orderselect,'
		<option value="desc">',$txt['gallery_txt_sort_desc'],'</option>
		<option value="asc">',$txt['gallery_txt_sort_asc'],'</option>
		</select></td>
  </tr>
<tr class="windowbg2">
	<td width="28%" align="right"><b>' .   $txt['gallery_txt_category_display_mode']  . '</b>&nbsp;</td>
	<td width="72%"><select name="displaymode">
		<option value="0" ' . ($row['displaymode'] == 0 ? ' selected="selected"' : '') . '>',$txt['gallery_txt_display_mode0'] ,'</option>
		<option value="1" ' . ($row['displaymode'] == 1 ? ' selected="selected"' : '') . '>',$txt['gallery_txt_display_mode1'] ,'</option>
		<option value="2" ' . ($row['displaymode'] == 2 ? ' selected="selected"' : '') . '>',$txt['gallery_txt_display_mode2'] ,'</option>
		</select>
	</td>
</tr>
	  <tr class="windowbg2">
		<td colspan="2" align="center">
		<b>' . $txt['gallery_text_postingoptions'] . '</b>
		<hr />
		' . $txt['gallery_postingoptions_info'] . '
		</td>
	  </tr>
	  <tr class="windowbg2">
		<td width="28%" align="right"><b>' . $txt['gallery_text_boardname'] . '</b>&nbsp;</td>
		<td width="72%">
		<select name="boardselect" id="boardselect">
	  ';

		foreach ($context['gallery_boards'] as $key => $option)
			 echo '<option value="' . $key . '"' . (($row['id_board']==$key) ? ' selected="selected"' : '') . '>' . $option . '</option>';

		echo '</select>
		</td>
	  </tr>
		<tr class="windowbg2">
			<td width="28%" align="right"><b>'  . $txt['gallery_txt_topicid'] . '</b></td>
			<td width="72%"><input type="text" name="id_topic" size="8" value="' . $row['id_topic'] . '" /></td>
		  </tr>
	   <tr class="windowbg2">
		<td colspan="2" align="center"><input type="checkbox" name="fullsize"' . (($row['postingsize']) ? 'checked="checked"' : '') . ' /><b>' . $txt['gallery_posting_fullsize'] . '</b>&nbsp;
	<br />
	<input type="checkbox" name="showpostlink" ' . ($row['showpostlink'] ? ' checked="checked"' : '') . ' /><b>' . $txt['gallery_posting_showlinktoimage'] . '</b>&nbsp;
	<br />
	<input type="checkbox" name="locktopic" ' . ($row['locktopic'] ? ' checked="checked"' : '') . ' /><b>' . $txt['gallery_posting_locktopic'] . '</b>&nbsp;

		</td>
	  </tr>';

	}

	echo '
   <tr class="windowbg2">
	<td colspan="2"><hr /></td>
  </tr>
  <tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<input type="hidden" value="' . $cat . '" name="catid" />
	<input type="submit" value="' . $txt['gallery_text_editcategory'] . '" name="submit" /></td>
  </tr>
</table>
</form>';

	// Hide option not for user galleries main category
	if ($row['redirect'] == 0)
	{
		echo '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr class="windowbg2">
<td>
	<hr />
  <div align="center">
  <b>',  $txt['gallery_custom_fields'],'</b><br />
	<form method="post" action="', $scripturl, '?action=gallery;sa=cusadd">
	', $txt['gallery_custom_title'], '<input type="text" name="title" />
	', $txt['gallery_custom_default_value'], '<input type="text" name="defaultvalue" />
	<input type="hidden" name="id" value="',$cat,'" />
	<input type="checkbox" name="required" />', $txt['gallery_custom_required'], '
	<input type="checkbox" name="globalfield" />', $txt['gallery_txt_global_field'], '
	<input type="submit" name="addfield" value="',$txt['gallery_custom_addfield'],'" />
	</form>
	</div><br />

	 <table cellspacing="0" cellpadding="4" border="0" align="center" class="tborder">
		<tr>
			<td class="titlebg">', $txt['gallery_custom_title'], '</td>
			<td class="titlebg">', $txt['gallery_custom_default_value'], '</td>
			<td class="titlebg">', $txt['gallery_custom_required'], '</td>
			<td class="titlebg">', $txt['gallery_txt_global_field'], '</td>
			<td class="titlebg">', $txt['gallery_text_options'], '</td>
		</tr>';

		// Get all the custom fields
		foreach($context['gallery_cat_cusfields'] as $row2)
		{
			echo '
		<tr class="windowbg">
			<td>', $row2['title'], '</td>
			<td>', $row2['defaultvalue'], '</td>
			<td>', ($row2['is_required'] ?  $txt['gallery_txt_yes'] :  $txt['gallery_txt_no']), '</td>
			<td>', ($row2['id_cat'] == 0 ?  $txt['gallery_txt_yes'] :  $txt['gallery_txt_no']), '</td>
			<td><a href="' . $scripturl . '?action=gallery;sa=cusup;id=' . $row2['id_custom'] . '">' . $txt['gallery_text_up'] . '</a>&nbsp;<a href="' . $scripturl . '?action=gallery;sa=cusdown;id=' . $row2['id_custom'] . '">' . $txt['gallery_text_down'] . '</a>
			&nbsp;&nbsp;<a href="' . $scripturl . '?action=gallery;sa=cusedit;id=' . $row2['id_custom'] . '&catid=' . $cat . '">' . $txt['gallery_text_edit'] . '</a>
			&nbsp;&nbsp;<a href="' . $scripturl . '?action=gallery;sa=cusdelete;id=' . $row2['id_custom'] . '">' . $txt['gallery_text_delete'] . '</a>
			</td>
		</tr>';
		}


		echo '
	</table>
	</div></div>
	<br />';
	}

	echo '
	<br />
	<div align="center">
	<a href="', $scripturl, '?action=gallery">', $txt['gallery_text_returngallery'], '</a>
	</div>
	</td>
	</tr>
	</table>
	';


}

function template_delete_category()
{
	global $context, $scripturl, $txt;

	ShowTopGalleryBar();

	echo '<div class="tborder">
	<form method="post" action="' . $scripturl . '?action=gallery;sa=deletecat2">
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_delcategory'] , '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<b>' . $txt['gallery_warn_category'] . '</b>
	<br />
	<i>' . $txt['gallery_text_galleryname'] . '&nbsp;"' . $context['cat_title'] . '"&nbsp;' . $txt['gallery_text_totalimages'] . '&nbsp;' . $context['totalpics'] . '</i>
	 <br />
	<input type="hidden" value="' . $context['catid'] . '" name="catid" />
	<input type="submit" value="' . $txt['gallery_text_delcategory'] . '" name="submit" /></td>
  </tr>
</table>
</form></div>';

}

function template_add_picture()
{
	global $scripturl, $modSettings, $txt, $context, $settings, $gallerySettings;

	ShowTopGalleryBar();


	echo '<div class="tborder">';
	echo '<form method="post" enctype="multipart/form-data" name="picform" id="picform" action="' . $scripturl . '?action=gallery;sa=add2" onsubmit="submitonce(this);">
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_form_addpicture'], '
		</h3>
  </div>';

  if (!empty($context['gallery_errors']))
  {
	echo '<div class="errorbox" id="errors">
						<dl>
							<dt>
								<strong style="" id="error_serious">' . $txt['gallery_errors_addpicture'] . '</strong>
							</dt>
							<dt class="error" id="error_list">';

							foreach($context['gallery_errors'] as $msg)
								echo $msg . '<br />';

							echo '
							</dt>
						</dl>
					</div>';
	}

echo '
<div class="information">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_title'] . '</b>&nbsp;</td>
	<td><input type="text" tabindex="1" size="80" name="title" value="' . $context['gallery_pic_title'] . '" /></td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_category'] . '</b>&nbsp;</td>
	<td><select name="cat" id="cat" onchange="changeCat(cat.options[cat.selectedIndex].value)">
	<option value="0">',$txt['gallery_text_choose_cat'],'</option>
	';

	foreach ($context['gallery_cat'] as $i => $category)
		echo '<option value="' . $category['id_cat']  . '" ' . (($context['gallery_cat_id'] == $category['id_cat']) ? ' selected="selected"' : '') .'>' . $category['title'] . '</option>';

	echo '</select>
	</td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_description'] . '</b>&nbsp;</td>
	<td><table>
   ';

	if (!function_exists('getLanguages'))
	{
	// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'bbc'), '
									</td>
								</tr>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']))
			echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'smileys'), '
									</td>
								</tr>';

		// Show BBC buttons, smileys and textbox.
		echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'message'), '
									</td>
								</tr>';
	}
	else
	{
		echo '
								<tr class="windowbg2">
		<td colspan="2">';
			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Show BBC buttons, smileys and textbox.
		echo '
					', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');


		echo '</td></tr>';
	}


   echo '</table>';

	if ($context['show_spellchecking'])
		echo '
			<br /><input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'picform\', \'description\');" />';

	echo '
	</td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_keywords'] . '</b>&nbsp;</td>
	<td><input type="text" name="keywords" maxlength="100" size="50" value="' . $context['gallery_pic_keywords'] . '" /></td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_uploadpic'] . '</b>&nbsp;</td>

	<td><input type="file" size="48" name="picture" />';

 	if (!empty($modSettings['gallery_max_filesize']))
    echo '
    <br />
    ' . $txt['gallery_set_filesize'] . ' ' .  gallery_format_size($modSettings['gallery_max_filesize'],2);



	if (empty($modSettings['gallery_resize_image']))
	{
		echo '<br />';
		if (!empty($modSettings['gallery_max_width']))
			echo $txt['gallery_form_maxwidth'] . $modSettings['gallery_max_width'] . $txt['gallery_form_pixels'];
		if (!empty($modSettings['gallery_max_height']))
			echo '&nbsp; ' . $txt['gallery_form_maxheight'] . $modSettings['gallery_max_height'] . $txt['gallery_form_pixels'];
	}

	echo '
	</td>
  </tr>';

   if (function_exists('imagerotate'))
   {
	 echo '<tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_text_rotate_image'] . '</b>&nbsp;</td>
  	<td><input type="text" name="degrees" size="5" maxlength="3" value="0" /><br />
  	<span class="smalltext">', $txt['gallery_text_rotate_image_desc'], '</span>
  	</td>
  </tr>';
   }


  echo '
  <tr class="windowbg2">
	<td colspan="2"><hr /></td>
  </tr>';
	if (empty($context['gallery_user_id']))
	{
		foreach($context['gallery_addpic_customfields'] as $row2)
		{
			echo '<tr class="windowbg2">
				<td align="right"><b>', $row2['title'], ($row2['is_required'] ? '<font color="#FF0000">*</font>' : ''), '</b></td>
				<td><input type="text" name="cus_', $row2['id_custom'],'" value="' , $row2['defaultvalue'], '" size="50" /></td>
			</tr>
		 ';
		}

	 }

	echo '
	   <tr class="windowbg2">
		<td align="right"><b>' . $txt['gallery_form_additionaloptions'] . '</b>&nbsp;</td>
		<td><input type="checkbox" name="sendemail" checked="checked" /><b>' . $txt['gallery_notify_title'] .'</b>';

	if ($modSettings['gallery_allow_mature_tag'])
	{
		echo '
	   <input type="checkbox" name="markmature" /><b>' .$txt['gallery_txt_mature'] .'</b>
  ';
	}

	if ($gallerySettings['gallery_set_allowratings'])
	{
		echo '<br />
  	   <input type="checkbox" name="allow_ratings" checked="checked" /><b>' .$txt['gallery_txt_allow_ratings'] .'</b>
  ';
	}

  if ($gallerySettings['gallery_set_allow_copy'])
  {
  	echo '<br />
	   <input type="checkbox" name="copyimage" />',$txt['gallery_txt_copy_item'],'
	  ';
  }


	echo '</td>
	  </tr>
  ';

	if ($modSettings['gallery_commentchoice'])
	{
		echo '
	<tr class="windowbg2">
		<td align="right">&nbsp;</td>
		<td><input type="checkbox" name="allowcomments" checked="checked" /><b>' . $txt['gallery_form_allowcomments'] .'</b></td>
	</tr>';
	}

	// Display the file quota information
	if ($context['quotalimit'] != 0)
	{
		echo '
	<tr class="windowbg2">
		<td align="right">',$txt['gallery_quotagrouplimit'],'&nbsp;</td>
		<td>', gallery_format_size($context['quotalimit'], 2), '</td>
	</tr>
	   <tr class="windowbg2">
		<td align="right">',$txt['gallery_quotagspaceused'],'&nbsp;</td>
		<td>', gallery_format_size($context['userspace'], 2), '</td>
	</tr>
	   <tr class="windowbg2">
		<td align="right">',$txt['gallery_quotaspaceleft'],'&nbsp;</td>
		<td><b>', gallery_format_size(($context['quotalimit']-$context['userspace']), 2), '</b></td>
	</tr>';
  }

echo '
	<tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
	<input type="hidden" name="userid" value="'. $context['gallery_user_id'] . '" />
	<input type="hidden" name="u" value="'. $context['gallery_user_id'] . '" />
	<input type="submit" value="' . $txt['gallery_form_addpicture'] . '" name="submit" /><br />';

	if (!allowedTo('smfgallery_autoapprove'))
		echo $txt['gallery_form_notapproved'];

echo '
	</td>
  </tr>
</table>
    </div>
		</form>
</div>
<script type="text/javascript">
function changeCat(myCategory)
{
if (myCategory != ' . $context['gallery_cat_id'] . ')
	document.location = "', $scripturl, '?action=gallery;sa=add;cat=" + myCategory + "' . (empty($context['gallery_user_id']) ? '' : ';u=' . $context['gallery_user_id']) . '";

}
</script>

';
}

function template_edit_picture()
{
	global $scripturl, $modSettings, $txt, $context, $settings, $gallerySettings;

	$g_manage = allowedTo('smfgallery_manage');

	ShowTopGalleryBar();

	echo '<div class="tborder">';
	echo '<form method="post" enctype="multipart/form-data" name="picform" id="picform" action="' . $scripturl . '?action=gallery;sa=edit2" onsubmit="submitonce(this);">
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_form_editpicture'] , '
		</h3>
  </div>
  <div class="information">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_title'] . '</b>&nbsp;</td>
	<td><input type="text" name="title" tabindex="1" size="80" value="' . $context['gallery_pic']['title'] . '" /></td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_category'] . '</b>&nbsp;</td>
	<td><select name="cat" id="cat" onchange="changeCat(cat.options[cat.selectedIndex].value)">';

	foreach ($context['gallery_cat'] as $i => $category)
	{
		if (!isset($_REQUEST['cat']))
			echo '<option value="' . $category['id_cat']  . '" ' . (($context['gallery_pic']['id_cat'] == $category['id_cat']) ? ' selected="selected"' : '') .   (($context['gallery_pic']['user_id_cat'] == $category['id_cat']) ? ' selected="selected"' : '') .'>' . $category['title'] . '</option>';
		else
			echo '<option value="' . $category['id_cat']  . '" ' . (($_REQUEST['cat'] == $category['id_cat']) ? ' selected="selected"' : '') .  '>' . $category['title'] . '</option>';

	}


	echo '</select>
	</td>
  </tr>

';

 echo '

  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_description'] . '</b>&nbsp;</td>
	<td><table>
   ';

	if (!function_exists('getLanguages'))
	{
	// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'bbc'), '
									</td>
								</tr>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']))
			echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'smileys'), '
									</td>
								</tr>';

		// Show BBC buttons, smileys and textbox.
		echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'message'), '
									</td>
								</tr>';
	}
	else
	{
		echo '
								<tr class="windowbg2">
		<td colspan="2">';
			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Show BBC buttons, smileys and textbox.
		echo '
					', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');


		echo '</td></tr>';
	}


   echo '</table>';

	if ($context['show_spellchecking'])
		echo '
									<br /><input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'picform\', \'description\');" />';

	echo '
	</td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_keywords'] . '</b>&nbsp;</td>
	<td><input type="text" name="keywords" maxlength="100" size="50" value="' . $context['gallery_pic']['keywords'] . '" /></td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_uploadpic'] . '</b>&nbsp;</td>

	<td><input type="file" size="48" name="picture" />';


 	if (!empty($modSettings['gallery_max_filesize']))
    echo '
    <br />
    ' . $txt['gallery_set_filesize'] . ' ' .  gallery_format_size($modSettings['gallery_max_filesize'],2);

	if (empty($modSettings['gallery_resize_image']))
	{
		echo '<br />';
	  if (!empty($modSettings['gallery_max_width']))
		echo  $txt['gallery_form_maxwidth'] .  $modSettings['gallery_max_width'] . $txt['gallery_form_pixels'];
	  if (!empty($modSettings['gallery_max_height']))
		echo '&nbsp;' . $txt['gallery_form_maxheight'] .  $modSettings['gallery_max_height'] . $txt['gallery_form_pixels'];
	}

 echo '
	</td>
  </tr>';


  if (function_exists('imagerotate'))
  {
	echo '<tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_text_rotate_image'] . '</b>&nbsp;</td>
  	<td><input type="text" name="degrees" size="5" maxlength="3" value="0" /><br />
  	<span class="smalltext">', $txt['gallery_text_rotate_image_desc'], '</span>
  	</td>
  </tr>';
  }

  if (!empty($modSettings['gallery_image_editor']))
  {
  	if (file_exists($modSettings['gallery_path'] . "pixie/scripts.min.js"))
	{
	echo '<tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_customize_image'] . '</b>&nbsp;</td>
  	<td><a href="#" onclick="launchEditor();">' . $txt['gallery_customize_image2'] . '</a><br />
<script
			  src="https://code.jquery.com/jquery-3.6.0.min.js"
			  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
			  crossorigin="anonymous"></script>
<link rel="stylesheet" href="' . $modSettings['gallery_url'] . 'pixie/styles.min.css">
<script src="' . $modSettings['gallery_url'] . 'pixie/scripts.min.js"></script>';

	echo "<script type=\"text/javascript\">
    var pixieEditorLoaded = false;
    var pixie;

    function loadPixie() {
        pixie = new Pixie({
            image: '" . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . "',
            ui: {
                openImageDialog: false,
                toolbar: {
                    hideCloseButton: false,
                },
                mode: 'overlay'
            },
            baseUrl: '" . $modSettings['gallery_url'] . "pixie',
            onLoad: function() {
                console.log(\"Pixie Loaded!\");
            },
            onSave: function(data, name) {
                 console.log(\"Saving\");
               $('#preloader').show();
                pixie.http().post('" . $scripturl ."?action=gallery&sa=pixie', {id:'" . $context['gallery_pic']['id_picture']. "', name: name, data: data}).subscribe(function(response) {
                   $('#preloader').hide();
                    if (response.message == \"success\") {

                      var img = document.getElementById('editableimage1');
                       img.src = data;
                        pixie.close();
                    }
                    else {
                        alert(\"could not save the image.\");
                    }
                });
            },
        });
    }

    function launchEditor() {
        if (pixieEditorLoaded == false) {
            loadPixie();
            pixieEditorLoaded = true;
        }
        pixie.openEditorWithImage(\"" . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . "\");
        return false;
    }
</script>

";

	echo '

  	</td>
  </tr>';
	}
  }


  echo '
   <tr class="windowbg2">
	<td colspan="2"><hr /></td>
  </tr>';

	foreach($context['gallery_editpic_customfields'] as $row2)
	{
		echo '<tr class="windowbg2">
			<td align="right"><b>', $row2['title'], ($row2['is_required'] ? '<font color="#FF0000">*</font>' : ''), '</b></td>
			<td><input type="text" name="cus_', $row2['id_custom'],'" value="' , $row2['value'], '" size="50" /></td>

		</tr>
	 ';
	}


 echo '
	   <tr class="windowbg2">
		<td align="right"><b>' . $txt['gallery_form_additionaloptions'] . '</b>&nbsp;</td>
		<td><input type="checkbox" name="sendemail" ' . ($context['gallery_pic']['sendemail'] ? 'checked="checked"' : '' ) . ' /><b>' . $txt['gallery_notify_title'] .'</b>';


	if ($modSettings['gallery_allow_mature_tag'])
	{
	echo '
	   <input type="checkbox" name="markmature" ' . ($context['gallery_pic']['mature'] ? 'checked="checked"' : '' ) . ' /><b>' .$txt['gallery_txt_mature'] .'</b>

  ';
	}

	echo '</td>
	  </tr>';


	if ($context['is_usergallery'] == true)
	{
		echo '
	   <tr class="windowbg2">
		<td align="right">&nbsp;</td>
		<td><input type="checkbox" name="featured" ' . ($context['gallery_pic']['featured'] ? 'checked="checked"' : '' ) . ' /><b>',$txt['gallery_txt_featured_image'],'</b></td>
	  </tr>';
	}

  if ($modSettings['gallery_commentchoice'])
  {
	echo '
	   <tr class="windowbg2">
		<td align="right">&nbsp;</td>
		<td><input type="checkbox" name="allowcomments" ' . ($context['gallery_pic']['allowcomments'] ? 'checked="checked"' : '' ) . ' /><b>',$txt['gallery_form_allowcomments'],'</b></td>
	  </tr>';
  }

  	if ($gallerySettings['gallery_set_allowratings'])
	{
		echo ' <tr class="windowbg2">
		<td align="right">&nbsp;</td>
  	  	 <td><input type="checkbox" name="allow_ratings" ' . ($context['gallery_pic']['allowratings'] ? 'checked="checked"' : '' ) . ' /><b>' .$txt['gallery_txt_allow_ratings'] .'</b>
  	   	</td>
  	   </tr>
  ';
	}

  // If the user can manage the gallery give them the option to change the picture owner.
  if ($context['is_usergallery'] == false && $g_manage == true)
  {
	  echo '<tr class="windowbg2">
	  <td align="right">', $txt['gallery_text_changeowner'], '</td>
	  <td><input type="text" name="pic_postername" id="pic_postername" value="" />
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/members.png" alt="', $txt['find_members'], '" /></a>
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);">', $txt['find_members'], '</a>
	  </td>
	  </tr>
	  <tr class="windowbg2">
	  <td colspan="2" align="center">
	  ',$txt['gallery_txt_picturemoveoptions'],'<a href="',$scripturl,'?action=gallery;sa=changegallery;mv=touser;id=',$context['gallery_pic']['id_picture'], '">',$txt['gallery_movetousergallery'],'</a>
	  </td>
	  </tr>
	  ';
  }
  else
  {
	if ($g_manage == true)
	{
		echo '
<tr class="windowbg2">
	  <td align="right">', $txt['gallery_text_changeowner'], '</td>
	  <td><input type="text" name="pic_postername" id="pic_postername" value="" />
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/members.png" alt="', $txt['find_members'], '" /></a>
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);">', $txt['find_members'], '</a>
	  </td>
	  </tr>

		<tr class="windowbg2">
	  <td colspan="2" align="center">
	  ',$txt['gallery_txt_picturemoveoptions'],'<a href="',$scripturl,'?action=gallery;sa=changegallery;mv=togallery;id=',$context['gallery_pic']['id_picture'], '">',$txt['gallery_movetomaingallery'],'</a>
	  </td>
	  </tr>
	  ';
	}

  }


  if ($gallerySettings['gallery_set_allow_copy'])
  {
  	echo ' <tr class="windowbg2">
	  <td colspan="2" align="center">
	  ',$txt['gallery_txt_copy_item'],'<a href="',$scripturl,'?action=gallery;sa=copyimage;id=',$context['gallery_pic']['id_picture'], '">',$txt['gallery_txt_copy_item3'],'</a>
	  </td>
	  </tr> ';
  }

  // Display the file quota information
  if ($context['quotalimit'] != 0)
  {
	echo '
	   <tr class="windowbg2">
		<td align="right">',$txt['gallery_quotagrouplimit'],'&nbsp;</td>
		<td>', gallery_format_size($context['quotalimit'], 2), '</td>
	  </tr>
	   <tr class="windowbg2">
		<td align="right">',$txt['gallery_quotagspaceused'],'&nbsp;</td>
		<td>', gallery_format_size($context['userspace'], 2), '</td>
	  </tr>
	   <tr class="windowbg2">
		<td align="right">',$txt['gallery_quotaspaceleft'],'&nbsp;</td>
		<td><b>', gallery_format_size(($context['quotalimit']-$context['userspace']), 2), '</b></td>
	  </tr>

	  ';
  }

echo '
	<tr class="windowbg2">
		<td width="28%" colspan="2" align="center">
			<input type="hidden" name="id" value="' . $context['gallery_pic']['id_picture'] . '" />
			<input type="submit" value="' . $txt['gallery_form_editpicture'] . '" name="submit" /><br />';

	if (!allowedTo('smfgallery_autoapprove'))
		echo $txt['gallery_form_notapproved'];

echo '
			<div align="center">
				<br /><b>', $txt['gallery_text_oldpicture'], '</b><br />
				<a href="', $scripturl, '?action=gallery;sa=view;id=', $context['gallery_pic']['id_picture'], '" target="blank"><img src="', $modSettings['gallery_url'], $context['gallery_pic']['thumbfilename'], '" id="editableimage1" alt="" /></a><br />
				<span class="smalltext">', $txt['gallery_text_views'], $context['gallery_pic']['views'], '<br />
				', $txt['gallery_text_filesize'], gallery_format_size($context['gallery_pic']['filesize']), '<br />
				', $txt['gallery_text_date'], $context['gallery_pic']['date'], '<br />
			</div>
		</td>
	</tr>
</table>
</div>
		</form>

		<pixie-editor></pixie-editor>



</div>

<script type="text/javascript">
function changeCat(myCategory)
{
if (myCategory != ' . $context['gallery_pic']['id_cat'] . ')
	document.location = "', $scripturl, '?action=gallery;sa=edit;id=' . $context['gallery_pic']['id_picture'] . ';cat=" + myCategory;

}
</script>
';

}

function template_view_picture()
{
	global $scripturl, $context, $txt, $user_info, $modSettings, $settings, $user_info, $gallerySettings;

	// Load permissions
	$g_manage = allowedTo('smfgallery_manage');
	$g_edit_own = allowedTo('smfgallery_edit');
	$g_delete_own = allowedTo('smfgallery_delete');
	$g_report = allowedTo('smfgallery_report');

	if (!empty($context['gallery_pic']['id_cat2']))
	{
		if ($g_edit_own == true)
			$g_edit_own = GetCatPermission($context['gallery_pic']['id_cat2'],'editpic',true);


		if ($g_delete_own == true)
			$g_delete_own = GetCatPermission($context['gallery_pic']['id_cat2'],'delpic',true);

	}


	$nextImage = $context['gallery_next_image'];
	$previousImage = $context['gallery_previous_image'];

	// Keywords
	$delimeter = ' ';
	if (substr_count($context['gallery_pic']['keywords'],',') > 0)
		$delimeter = ',';

	$keywords = explode($delimeter,$context['gallery_pic']['keywords']);
	$keywordscount = count($keywords);

	ShowTopGalleryBar();

	$extracat = ($context['gallery_user_id_cat'] == 0) ? '' : 'su=user;u=' . $context['gallery_pic']['user_member'] .';';

	if ($gallerySettings['gallery_allow_slideshow'])
		$context['gallery']['buttons_set']['slideshow'] =  array(
								'text' => 'gallery_txt_slideshow',
								'url' => $scripturl . '?action=gallery;sa=slideshow;id=' . $context['gallery_pic']['id_picture'],
								'lang' => true,
								'is_selected' => true,
						);


	// Watch user for updates
	if ($context['user']['is_guest'] == false)
	{
		$context['gallery']['buttons_set']['watchuser'] =  array(
								'text' => 'gallery_txt_follow_user',
								'url' => $scripturl . '?action=gallery;sa=watchuser;memid=' . $context['gallery_pic']['id_member'] . ';id=' . $context['gallery_pic']['id_picture'],
								'lang' => true,
								'is_selected' => true,
								'image' => 'GalleryWatchUser.gif',
						);
	}


	// Favorites
	if ($gallerySettings['gallery_set_allow_favorites'])
	{
		if (!($context['user']['is_guest']))
		{
			$context['gallery']['buttons_set']['favoriteimage'] =  array(
								'text' => (empty($context['gallery_pic']['favorite']) ? 'gallery_txt_addfavorites' : 'gallery_txt_unfavorite') ,
								'url' => $scripturl . '?action=gallery;sa=' . (empty($context['gallery_pic']['favorite']) ? 'addfavorite' : 'unfavorite') . ';id=' . $context['gallery_pic']['id_picture'],
								'lang' => true,
								'is_selected' => true,
								'image' => 'GalleryFavorite.gif',
						);
		}
	}


	// Top buttons above view picture
	if ($g_manage)
	{
  		if ($context['gallery_pic']['approved'] == 1)
		{
			$context['gallery']['buttons_set']['unapprove'] =  array(
								'text' => 'gallery_text_unapprove2',
								'url' => $scripturl . '?action=gallery;sa=unapprove;id=' . $context['gallery_pic']['id_picture'],
								'lang' => true,
								'is_selected' => true,
								'image' => 'GalleryUnapprove.gif',
						);
		}
		else
		{
			$context['gallery']['buttons_set']['approve'] =  array(
								'text' => 'gallery_text_approve2',
								'url' => $scripturl . '?action=gallery;sa=approve;id=' . $context['gallery_pic']['id_picture'],
								'lang' => true,
								'is_selected' => true,
								'image' => 'GalleryApprove.gif',
						);
		}
	}


	if ($g_manage || $g_edit_own && $context['gallery_pic']['id_member'] == $user_info['id'])
		$context['gallery']['buttons_set']['edit'] = array(
			'text' => 'gallery_text_edit2',
			'url' => $scripturl . '?action=gallery;sa=edit;id=' . $context['gallery_pic']['id_picture'],
			'lang' => true,
			'is_selected' => true,
		);

	if ($g_manage || $g_delete_own && $context['gallery_pic']['id_member'] == $user_info['id'])
		$context['gallery']['buttons_set']['delete'] = array(
			'text' => 'gallery_text_delete2',
			'url' => $scripturl . '?action=gallery;sa=delete;id=' . $context['gallery_pic']['id_picture'],
			'lang' => true,
			'is_selected' => true,
		);

	if ($g_report)
		$context['gallery']['buttons_set']['report'] = array(
			'text' => 'gallery_text_reportitem',
			'url' => $scripturl . '?action=gallery;sa=report;id=' . $context['gallery_pic']['id_picture'],
			'lang' => true,
			'is_selected' => true,
		);


	// Mark Unviewed
	if ($context['user']['is_guest'] == false)
	{
		$context['gallery']['buttons_set']['markunviewed'] =  array(
							'text' => 'gallery_txt_markunviewed',
							'url' => $scripturl . '?action=gallery;sa=markunviewed;id=' . $context['gallery_pic']['id_picture'],
							'lang' => true,
							'is_selected' => true,
							'image' => 'Markunviewed.gif',
					);

	}

	if ($g_manage || ($context['gallery_pic']['id_member'] == $user_info['id'] && !empty($context['gallery_pic']['user_id_cat'])))
	{
		$context['gallery']['buttons_set']['autothumb'] =  array(
							'text' => 'gallery_txt_set_category_thumbnail',
							'url' => $scripturl . '?action=gallery;sa=autothumb;id=' . $context['gallery_pic']['id_picture'],
							'lang' => true,
							'is_selected' => true,
							'image' => 'AutoThumb.gif',
					);
	}

if (!empty($context['gallery']['buttons_set']))
	echo '
<div id="moderationbuttons" class="margintop">
	', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
</div>';

if ($modSettings['gallery_set_img_title'])
echo '<br /><br /><br />
<div class="cat_bar">
		<h3 class="catbg centertext">
        ', $context['gallery_pic']['title'], '
        </h3>
</div>
';

	echo '<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">';

	// Show Mini thumbnails on view picture
	if ($gallerySettings['gallery_set_mini_prevnext_thumbs'])
	{
	echo '<tr class="windowbg2">
				<td align="center">';

				$miniPictures = $context['gallery_pic_minpics'];

				echo '<table align="center" border="0">
				<tr class="windowbg2">';

				if ($previousImage != $context['gallery_pic']['id_picture'])
				foreach($miniPictures as $pic)
				{
					if ($pic['id_picture'] == $previousImage)
						echo '<td  align="center"><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $pic['id_picture'] . '"><img src="' . $modSettings['gallery_url'] . $pic['thumbfilename'] . '" alt="' . htmlspecialchars($pic['title'],ENT_QUOTES) . '" /></a></td>';

				}
				foreach($miniPictures as $pic)
				{
					if ($pic['id_picture'] == $context['gallery_pic']['id_picture'])
					{
						echo '<td align="center"><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $pic['id_picture'] . '"><img src="' . $modSettings['gallery_url'] . $pic['thumbfilename'] . '" alt="' . htmlspecialchars($pic['title'],ENT_QUOTES) . '" /></a></td>';
						break;
					}
				}

				if ($nextImage != $context['gallery_pic']['id_picture'])
				foreach($miniPictures as $pic)
				{
					if ($pic['id_picture'] == $nextImage)
						echo '<td align="center"><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $pic['id_picture'] . '"><img src="' . $modSettings['gallery_url'] . $pic['thumbfilename'] . '" alt="' . htmlspecialchars($pic['title'],ENT_QUOTES) . '" /></a></td>';

				}
			echo '</tr>
			</table>
			</td>
			</tr>';
	}


	if (isset($modSettings['gallery_ad_seller']))
	{
		// Begin Ad Seller Pro Location - SMF Gallery - Top of picture

		global $sourcedir;
		include_once $sourcedir . "/adseller2.php";

		$adSellerAdData =  ShowAdLocation(103);

		// Check if any ads where found
		if ($adSellerAdData != false)
		{
			// Dispaly the advertising code
			echo '<tr class="windowbg2">
				<td align="center">';
			echo $adSellerAdData;

			echo '</td></tr>';
		}

		// End Ad Seller Pro Location - SMF Gallery - Top of picture
	}


	//  iosarian Aspect Fix
	if ($context['gallery_pic']['width'] >= $modSettings['gallery_set_disp_maxwidth'])
	{
		$thumbwidth = $modSettings['gallery_set_disp_maxwidth'];
		$thumbheight = $context['gallery_pic']['height'] * $modSettings['gallery_set_disp_maxwidth'] / $context['gallery_pic']['width'];
	}
	else
	{
		$thumbwidth = $context['gallery_pic']['width'];
		$thumbheight = $context['gallery_pic']['height'];
	}

	if ($thumbheight > $modSettings['gallery_set_disp_maxheight'])
	{
		$thumbheight = $modSettings['gallery_set_disp_maxheight'];
		$thumbwidth = $context['gallery_pic']['width'] * $modSettings['gallery_set_disp_maxheight'] / $context['gallery_pic']['height'];
	}

	// Show the main image
	if ($context['gallery_pic']['type'] == 0)
	{
		echo '
		<tr class="windowbg2">
			<td align="center">';

		if ($gallerySettings['gallery_set_allow_photo_tagging'])
			echo '<table align="center"><tr><td>
		<div class="Photo fn-container" id="PhotoContainer">';

			// Check if we want to use fancy highslide options
			if ($modSettings['gallery_set_nohighslide'] == 0)
			{
				if ($modSettings['gallery_make_medium'] == 0 || $context['gallery_pic']['mediumfilename'] == '' || !file_exists($modSettings['gallery_path'] . $context['gallery_pic']['mediumfilename']))
				echo '
				<a id="thumb1" href="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '" class="highslide" onclick="return hs.expand(this, {captionId: \'caption1\'})">
	<img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '" alt=""
		title="',$txt['gallery_click_enlarge'],'" height="',$thumbheight,'" width="',$thumbwidth,'" /></a><div class="highslide-caption" id="caption1"></div>';
				else
				{
					// Do medium image
			echo '
				<a id="thumb1" href="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '" class="highslide" onclick="return hs.expand(this, {captionId: \'caption1\'})">
	<img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['mediumfilename'] . '" alt=""
		title="',$txt['gallery_click_enlarge'],'"  /></a><div class="highslide-caption" id="caption1"></div>';

				}

			}
			else if ($modSettings['gallery_set_nohighslide'] == 1)
			{
				// No highslide
				if ($modSettings['gallery_make_medium'] == 0 || $context['gallery_pic']['mediumfilename'] == ''  || !file_exists($modSettings['gallery_path'] . $context['gallery_pic']['mediumfilename']))
					echo '<img height="' . $thumbheight  . '" width="' . $thumbwidth  . '" src="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename']  . '" alt="" />';
				else
					echo '<a href="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '"><img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['mediumfilename']  . '" alt="" /></a>';
			}
			else if ($modSettings['gallery_set_nohighslide'] == 2)
			{
				// Lightbox
				if ($modSettings['gallery_make_medium'] == 0 || $context['gallery_pic']['mediumfilename'] == ''  || !file_exists($modSettings['gallery_path'] . $context['gallery_pic']['mediumfilename']))
					echo '<a href="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '" rel="lightbox" title="' . $context['gallery_pic']['description'] . '"><img height="' . $thumbheight  . '" width="' . $thumbwidth  . '" src="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename']  . '" alt="" /></a>';
				else
					echo '<a href="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '" rel="lightbox" title="' . $context['gallery_pic']['description'] . '"><img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['mediumfilename']  . '" alt="" /></a>';

			}
			else if ($modSettings['gallery_set_nohighslide'] == 3)
			{
				// No highslide no fullsize
				if ($modSettings['gallery_make_medium'] == 0 || $context['gallery_pic']['mediumfilename'] == ''  || !file_exists($modSettings['gallery_path'] . $context['gallery_pic']['mediumfilename']))
					echo '<img height="' . $thumbheight  . '" width="' . $thumbwidth  . '" src="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename']  . '" alt="" />';
				else
					echo '<img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['mediumfilename']  . '" alt="" />';
			}


			if ($gallerySettings['gallery_set_allow_photo_tagging'])
		echo '</div></td></tr></table>';


			if ($gallerySettings['gallery_set_downloadimage'])
			{


				if (substr($context['gallery_pic']['videofile'],0,7) != 'http://' &&  substr($context['gallery_pic']['videofile'],0,8) != 'https://')
					echo '<br /><a href="' . $scripturl . '?action=gallery;sa=download&id=' . $context['gallery_pic']['id_picture'] . '">' . $txt['gallery_txt_download'] . '</a> ';

			}


			// Photo tagging
			if ($gallerySettings['gallery_set_allow_photo_tagging'])
			{

				if ($context['user']['is_guest'] == false)
					echo '<a href="#" onClick="AddNote()">' . $txt['gallery_txt_note_add_new_note'] . '</a>';


				echo "
			 <script>
			/* create the Photo Note Container */
			var allNotes = new Array();
			var container = document.getElementById('PhotoContainer');
			var notes = new PhotoNoteContainer(container);
			";

				foreach($context['gallery_phototags'] as $tagRow)
				{
					echo "
					var box = new PhotoNoteRect( " . $tagRow['xpos'] . "," . $tagRow['ypos'] . "," . $tagRow['width'] . "," . $tagRow['height'] . ");
				allNotes[" . $tagRow['id'] . "] = new PhotoNote('" . $tagRow['caption'] . "'," . $tagRow['id'] . ", box);
				allNotes[" . $tagRow['id'] . "].onsave = function (note) { return saveNoteDb(note); };
				allNotes[" . $tagRow['id'] . "].ondelete = function (note) { return deleteNoteDb(note);};
				notes.AddNote(allNotes[" . $tagRow['id'] . "]);
				";
				}




			echo "

			function AddNote()
			{
				var newNote = new PhotoNote('" . $txt['gallery_txt_note_add_note_here'] . "',-1,new PhotoNoteRect(10,10,50,50));
				newNote.onsave = function (note) { return saveNoteDb(note); };
				newNote.ondelete = function (note) { return deleteNoteDb(note); };
				notes.AddNote(newNote);
				newNote.Select();
			}
			</script>";
			}


		echo '
			</td>
		</tr>';
	}
	else
	{
		// Showing some other media type

			echo '
				<tr class="windowbg2">
					<td align="center">';

				showvideobox($context['gallery_pic']['videofile']);

			echo '
					</td>
				</tr>';

			// Download video option
			if ($modSettings['gallery_video_showdowloadlink'] == 1)
			{
				if (substr($context['gallery_pic']['videofile'],0,7) != 'http://' &&  substr($context['gallery_pic']['videofile'],0,8) != 'https://')
				{

					echo '
					<tr class="windowbg2">
						<td align="center">';

					if ($context['gallery_pic']['type'] == 1)
						echo '
							<a href="' . $modSettings['gallery_url'] . 'videos/'  . $context['gallery_pic']['videofile'] . '">' . $txt['gallery_video_dowloadlink'] . '</a>';
					else
						echo '
							<a href="' . $context['gallery_pic']['videofile'] . '">' . $txt['gallery_video_dowloadlink'] . '</a>';

					echo '
						</td>
					</tr>';

				}
			}

	}
	//<img height="' . $context['gallery_pic']['height']  . '" width="' . $context['gallery_pic']['width']  . '" src="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename']  . '" />


	if (isset($modSettings['gallery_ad_seller']))
	{
		// Begin Ad Seller Pro Location - SMF Gallery - Bottom of picture

		global $sourcedir;
		include_once $sourcedir . "/adseller2.php";

		$adSellerAdData =  ShowAdLocation(104);

		// Check if any ads where found
		if ($adSellerAdData != false)
		{
			// Dispaly the advertising code
			echo '<tr class="windowbg2">
				<td align="center">';
			echo $adSellerAdData;

			echo '</td></tr>';
		}

		// End Ad Seller Pro Location - SMF Gallery - Bottom of picture
	}

	// Show the previous and next links
	if ($modSettings['gallery_set_img_prevnext'])
	{
		echo '
			<tr class="windowbg2">
			<td align="center"><b>';
			$showSpacer = false;
			if ($previousImage != $context['gallery_pic']['id_picture'])
			{
				$showSpacer = true;
				echo '<a href="', $scripturl, '?action=gallery;sa=prev;id=', $context['gallery_pic']['id_picture'], '">', $txt['gallery_text_prev'], '</a>';
			}

			if ($nextImage  != $context['gallery_pic']['id_picture'])
			{
				if ($showSpacer == true)
					echo ' | ';
				echo '<a href="', $scripturl, '?action=gallery;sa=next;id=', $context['gallery_pic']['id_picture'], '">', $txt['gallery_text_next'], '</a>';

			}

				echo '</b>';

   if (empty($gallerySettings['gallery_set_quickreply_full']))
   {
echo "
<script>
				document.onkeydown = checkKey;

function checkKey(e) {

	e = e || window.event;

	if (e.keyCode == '37') {
		// left arrow
";
	   if ($previousImage != $context['gallery_pic']['id_picture'])
		echo  'document.location=\'' . $scripturl, '?action=gallery;sa=prev;id=', $context['gallery_pic']['id_picture'], '\';';

 echo "
	}
	else if (e.keyCode == '39') {
		// right
";
	   if ($nextImage  != $context['gallery_pic']['id_picture'])
		echo  'document.location=\'' . $scripturl, '?action=gallery;sa=next;id=', $context['gallery_pic']['id_picture'], '\';';

 echo "
	}
}
</script>";
}


				echo '
			</td>
			</tr>';
	}

	if (!empty($gallerySettings['gallery_set_picture_information_last']))
	{
		echo '</table>';
		ShowCommentsPicture();
		echo '<br /><table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="tborder">';

	}


	echo '
		<tr class="windowbg2">
			<td>';

	// Show image description
	if ($modSettings['gallery_set_img_desc'])
		echo '<b>' . $txt['gallery_form_description'] . ' </b>' . ( parse_bbc($context['gallery_pic']['description']) );

	echo '
		<hr />
		' . $txt['gallery_text_picstats'] . '<br />';
	if ($modSettings['gallery_set_img_views'])
		echo $txt['gallery_text_views'] . $context['gallery_pic']['views'] . ($g_manage ? ' <a href="' . $scripturl . '?action=gallery;sa=viewers;pic=' . $context['gallery_pic']['id_picture'] . '">' . $txt['gallery_txt_viewers'] . '</a>' : '') . '<br />';

			// Show total favorites and view favortied
			if (!empty($gallerySettings['gallery_set_allow_favorites']))
			{
				$favRow =  $context['gallery_pic_favdata'];
				echo $txt['gallery_txt_totalfavorites'] . $favRow['total'] . ' <a href="' . $scripturl . '?action=gallery;sa=whofavorited;pic=' . $context['gallery_pic']['id_picture'] . '">' . $txt['gallery_txt_viewfavorites'] . '</a>'  . '<br />';

			}

	if ($modSettings['gallery_set_img_showfilesize'])
		echo $txt['gallery_text_filesize'], gallery_format_size($context['gallery_pic']['filesize'],2), '<br />';

	if ($modSettings['gallery_set_img_size'])
		echo $txt['gallery_text_height'] . ' ' . $context['gallery_pic']['height']  . '  ' . $txt['gallery_text_width'] . ' ' . $context['gallery_pic']['width'] . '<br />';

	// Show Discussion link
	if (!empty($context['gallery_pic']['ID_TOPIC']))
		echo $txt['gallery_txt_dicussion_topic'] . '<a href="',$scripturl,'?topic=' . $context['gallery_pic']['ID_TOPIC'] . '">',$txt['gallery_txt_view_topic'],'</a><br />';


	if ($modSettings['gallery_set_img_keywords'])
		if ($context['gallery_pic']['keywords'] != '')
		{
			echo  $txt['gallery_form_keywords'] . ' ';

			for($i = 0; $i < $keywordscount;$i++)
			{
				if (strlen($keywords[$i]) > 2)
					echo '<a href="' . $scripturl . '?action=gallery;sa=search2;key=' . $keywords[$i] . '">' . $keywords[$i] . '</a>&nbsp;';
			}

			echo '<br />';
		}

	if ($modSettings['gallery_set_img_poster'])
	{

		// Disable member color link
		if (!empty($modSettings['gallery_disable_membercolorlink']))
			$context['gallery_pic']['online_color'] = '';

		if ($context['gallery_pic']['real_name'] != '')
			echo $txt['gallery_text_postedby'] . '<a href="' . $scripturl . '?action=profile;u=' . $context['gallery_pic']['id_member'] . '"' . (!empty($context['gallery_pic']['online_color']) ? ' style="color: ' . $context['gallery_pic']['online_color'] . ';" ' :'' ) . '>' . $context['gallery_pic']['real_name'] . '</a><a href="', $scripturl, '?action=gallery;sa=myimages;u=' . $context['gallery_pic']['id_member'] . '"><img src="'. $modSettings['gallery_url'].'filter.gif" valign="top" style="margin: 0 1ex;" alt="" /></a>&nbsp;';
		else
			echo $txt['gallery_text_postedby'] . ' ' . $txt['gallery_guest'] . '&nbsp;';
	}

	if ($modSettings['gallery_set_img_date'])
		echo $context['gallery_pic']['date'] . '<br />';

	// Check to show custom fields
	if ($context['gallery_user_id_cat'] == 0)
	{
		// Show Custom Fields
		foreach($context['gallery_pic_customfields'] as $row4)
		{
			// No reason to show empty custom fields on the display page
			if ($row4['value'] != '')
				echo $row4['title'], ':&nbsp;',$row4['value'], '<br />';
		}

	}

	echo '<br />';

	// Show Exif Data
	if ($gallerySettings['enable_exif_on_display'])
	{

		$exif_count = $context['gallery_exif_picdata_count'];
		$exifRow = $context['gallery_exif_picdata'];


		// If there is data then show it!
		if ($exif_count > 0)
		{

	 		foreach($exifRow as $ekey  => &$eRow)
		 		{
		 			$exifRow[strtolower($ekey)] = $eRow;
		 		}

			echo '<b>',$txt['gallery_txt_picexif'],'</b><br />';
			echo '
			<table>';

			if ($gallerySettings['show_idfo_ImageDescription'] && !empty($exifRow['idfo_imagedescription']))
				echo '
				<tr><td>',$txt['show_idfo_ImageDescription2'],'</td>
				<td>',$exifRow['idfo_imagedescription'],'</td>
				</tr>';

			if ($gallerySettings['show_exif_DateTimeOriginal'] && !empty($exifRow['exif_datetimeoriginal']))
			{
				$orgtime = strtotime($exifRow['exif_datetimeoriginal']);
				echo '

				<tr><td>',$txt['show_exif_DateTimeOriginal2'],'</td>
				<td>',date("F j, Y, g:i a",$orgtime),'</td>
				</tr>';

			}

			if ($gallerySettings['show_idfo_Model'] && !empty($exifRow['idfo_model']))
				echo '
				<tr><td>',$txt['show_idfo_Model2'],'</td>
				<td>',$exifRow['idfo_model'],'</td>
				</tr>';

			if ($gallerySettings['show_exif_FocalLength'] && !empty($exifRow['exif_focallength']))
				echo '
				<tr><td>',$txt['show_exif_FocalLength2'],'</td>
				<td>',exif_get_focal_length($exifRow['exif_fnumber'],$exifRow['exif_focallength']),'</td>
				</tr>';

			if ($gallerySettings['show_exif_FNumber'] && !empty($exifRow['exif_fnumber']))
				echo '
				<tr><td>',$txt['show_exif_FNumber2'],'</td>
				<td>',(!empty($exifRow['computed_aperturefnumber']) ? $exifRow['computed_aperturefnumber'] : $exifRow['exif_fnumber']) ,'</td>
				</tr>';

				// 	<td>',(!empty($exifRow['computed_aperturefnumber']) ? $exifRow['computed_aperturefnumber'] : $exifRow['exif_fnumber']) ,'</td>

			if ($gallerySettings['show_exif_ExposureTime'] && !empty($exifRow['exif_exposuretime']))
				echo '
				<tr><td>',$txt['show_exif_ExposureTime2'],'</td>
				<td>',$exifRow['exif_exposuretime'],'</td>
				</tr>';

			if ($gallerySettings['show_exif_ISOSpeedRatings'] && !empty($exifRow['exif_isospeedratings']))
				echo '
				<tr><td>',$txt['show_exif_ISOSpeedRatings2'],'</td>
				<td>',$exifRow['exif_isospeedratings'],'</td>
				</tr>';



			if ($gallerySettings['show_idfo_Make'] && !empty($exifRow['idfo_make']))
				echo '
				<tr><td>',$txt['show_idfo_Make2'],'</td>
				<td>',$exifRow['idfo_make'],'</td>
				</tr>';

			if ($gallerySettings['show_idfo_Orientation'] && !empty($exifRow['idfo_orientation']))
				echo '
				<tr><td>',$txt['show_idfo_Orientation2'],'</td>
				<td>',$exifRow['idfo_orientation'],'</td>
				</tr>';
			if ($gallerySettings['show_idfo_XResolution'] && !empty($exifRow['idfo_xresolution']))
				echo '
				<tr><td>',$txt['show_idfo_XResolution2'],'</td>
				<td>',$exifRow['idfo_xresolution'],'</td>
				</tr>';
			if ($gallerySettings['show_idfo_YResolution'] && !empty($exifRow['idfo_yresolution']))
				echo '
				<tr><td>',$txt['show_idfo_YResolution2'],'</td>
				<td>',$exifRow['idfo_yresolution'],'</td>
				</tr>';
			if ($gallerySettings['show_idfo_ResolutionUnit'] && !empty($exifRow['idfo_resolutionunit']))
				echo '
				<tr><td>',$txt['show_idfo_ResolutionUnit2'],'</td>
				<td>',$exifRow['idfo_resolutionunit'],'</td>
				</tr>';
			if ($gallerySettings['show_idfo_Software'] && !empty($exifRow['idfo_software']))
				echo '
				<tr><td>',$txt['show_idfo_Software2'],'</td>
				<td>',$exifRow['idfo_software'],'</td>
				</tr>';
			if ($gallerySettings['show_idfo_DateTime'] && !empty($exifRow['idfo_datetime']))
				echo '
				<tr><td>',$txt['show_idfo_DateTime2'],'</td>
				<td>',$exifRow['idfo_datetime'],'</td>
				</tr>';
			if ($gallerySettings['show_idfo_Artist'] && !empty($exifRow['idfo_artist']))
				echo '
				<tr><td>',$txt['show_idfo_Artist2'],'</td>
				<td>',$exifRow['idfo_artist'],'</td>
				</tr>';
			if ($gallerySettings['show_computed_Height'] && !empty($exifRow['computed_height']))
				echo '
				<tr><td>',$txt['show_computed_Height2'],'</td>
				<td>',$exifRow['computed_height'],'</td>
				</tr>';
			if ($gallerySettings['show_computed_Width'] && !empty($exifRow['computed_width']))
				echo '
				<tr><td>',$txt['show_computed_Width2'],'</td>
				<td>',$exifRow['computed_width'],'</td>
				</tr>';
			if ($gallerySettings['show_computed_IsColor'] && !empty($exifRow['computed_iscolor']))
				echo '
				<tr><td>',$txt['show_computed_IsColor2'],'</td>
				<td>',$exifRow['computed_iscolor'],'</td>
				</tr>';
			if ($gallerySettings['show_computed_CCDWidth'] && !empty($exifRow['computed_ccdwidth']))
				echo '
				<tr><td>',$txt['show_computed_CCDWidth2'],'</td>
				<td>',$exifRow['computed_ccdwidth'],'</td>
				</tr>';
			if ($gallerySettings['show_computed_ApertureFNumber'] && !empty($exifRow['computed_aperturefnumber']))
				echo '
				<tr><td>',$txt['show_computed_ApertureFNumber2'],'</td>
				<td>',$exifRow['computed_aperturefnumber'],'</td>
				</tr>';
			if ($gallerySettings['show_computed_Copyright'] && !empty($exifRow['computed_copyright']))
				echo '
				<tr><td>',$txt['show_computed_Copyright2'],'</td>
				<td>',$exifRow['computed_copyright'],'</td>
				</tr>';


			if ($gallerySettings['show_exif_ExposureProgram'] && !empty($exifRow['exif_exposureprogram']))
				echo '
				<tr><td>',$txt['show_exif_ExposureProgram2'],'</td>
				<td>',$exifRow['exif_exposureprogram'],'</td>
				</tr>';

			if ($gallerySettings['show_exif_ExifVersion'] && !empty($exifRow['exif_exifversion']))
				echo '
				<tr><td>',$txt['show_exif_ExifVersion2'],'</td>
				<td>',$exifRow['exif_exifversion'],'</td>
				</tr>';

			if ($gallerySettings['show_exif_DateTimeDigitized'] && !empty($exifRow['exif_datetimedigitized']))
				echo '
				<tr><td>',$txt['show_exif_DateTimeDigitized2'],'</td>
				<td>',$exifRow['exif_datetimedigitized'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_ShutterSpeedValue'] && !empty($exifRow['exif_shutterspeedvalue']))
				echo '
				<tr><td>',$txt['show_exif_ShutterSpeedValue2'],'</td>
				<td>',exif_get_shutter($exifRow['exif_shutterspeedvalue']),'</td>
				</tr>';
			if ($gallerySettings['show_exif_ApertureValue'] && !empty($exifRow['exif_aperturevalue']))
				echo '
				<tr><td>',$txt['show_exif_ApertureValue2'],'</td>
				<td>',$exifRow['exif_aperturevalue'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_ExposureBiasValue'] && !empty($exifRow['exif_exposurebiasvalue']))
				echo '
				<tr><td>',$txt['show_exif_ExposureBiasValue2'],'</td>
				<td>',$exifRow['exif_exposurebiasvalue'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_MaxApertureValue'] && !empty($exifRow['exif_maxAperturevalue']))
				echo '
				<tr><td>',$txt['show_exif_maxaperturevalue2'],'</td>
				<td>',$exifRow['exif_maxaperturevalue'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_MeteringMode'] && !empty($exifRow['exif_meteringmode']))
				echo '
				<tr><td>',$txt['show_exif_MeteringMode2'],'</td>
				<td>',$exifRow['exif_meteringmode'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_LightSource'] && !empty($exifRow['exif_lightsource']))
				echo '
				<tr><td>',$txt['show_exif_LightSource2'],'</td>
				<td>',$exifRow['exif_lightsource'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_Flash'] && !empty($exifRow['exif_flash']))
				echo '
				<tr><td>',$txt['show_exif_Flash2'],'</td>
				<td>',$exifRow['exif_flash'],'</td>
				</tr>';


			if ($gallerySettings['show_exif_ColorSpace'] && !empty($exifRow['exif_colorspace']))
				echo '
				<tr><td>',$txt['show_exif_ColorSpace2'],'</td>
				<td>',$exifRow['exif_colorspace'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_ExifImageWidth'] && !empty($exifRow['exif_exifimagewidth']))
				echo '
				<tr><td>',$txt['show_exif_ExifImageWidth2'],'</td>
				<td>',$exifRow['exif_exifimagewidth'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_ExifImageLength'] && !empty($exifRow['exif_exifimagelength']))
				echo '
				<tr><td>',$txt['show_exif_ExifImageLength2'],'</td>
				<td>',$exifRow['exif_exifimagelength'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_FocalPlaneXResolution'] && !empty($exifRow['exif_focalplanexresolution']))
				echo '
				<tr><td>',$txt['show_exif_FocalPlaneXResolution2'],'</td>
				<td>',$exifRow['exif_focalplanexresolution'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_FocalPlaneYResolution'] && !empty($exifRow['exif_focalplaneyresolution']))
				echo '
				<tr><td>',$txt['show_exif_FocalPlaneYResolution2'],'</td>
				<td>',$exifRow['exif_focalplaneyresolution'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_FocalPlaneResolutionUnit'] && !empty($exifRow['exif_focalplaneresolutionUnit']))
				echo '
				<tr><td>',$txt['show_exif_FocalPlaneResolutionUnit2'],'</td>
				<td>',$exifRow['exif_focalplaneresolutionUnit'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_CustomRendered'] && !empty($exifRow['exif_customrendered']))
				echo '
				<tr><td>',$txt['show_exif_CustomRendered2'],'</td>
				<td>',$exifRow['exif_customrendered'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_ExposureMode'] && !empty($exifRow['exif_exposuremode']))
				echo '
				<tr><td>',$txt['show_exif_ExposureMode2'],'</td>
				<td>',$exifRow['exif_exposuremode'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_WhiteBalance'] && !empty($exifRow['exif_whitebalance']))
				echo '
				<tr><td>',$txt['show_exif_WhiteBalance2'],'</td>
				<td>',$exifRow['exif_whitebalance'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_SceneCaptureType'] && !empty($exifRow['exif_scenecapturetype']))
				echo '
				<tr><td>',$txt['show_exif_SceneCaptureType2'],'</td>
				<td>',$exifRow['exif_scenecapturetype'],'</td>
				</tr>';


			if ($gallerySettings['show_exif_lenstype'] && !empty($exifRow['exif_lenstype']))
				echo '
				<tr><td>',$txt['show_exif_lenstype2'],'</td>
				<td>',$exifRow['exif_lenstype'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_lensinfo'] && !empty($exifRow['exif_lensinfo']))
				echo '
				<tr><td>',$txt['show_exif_lensinfo2'],'</td>
				<td>',$exifRow['exif_lensinfo'],'</td>
				</tr>';
			if ($gallerySettings['show_exif_lensid'] && !empty($exifRow['exif_lensid']))
				echo '
				<tr><td>',$txt['show_exif_lensid2'],'</td>
				<td>',$exifRow['exif_lensid'],'</td>
				</tr>';



			if ($gallerySettings['show_gps_latituderef'] && !empty($exifRow['gps_latituderef']))
				echo '
				<tr><td>',$txt['show_gps_latituderef2'],'</td>
				<td>',$exifRow['gps_latituderef'],'</td>
				</tr>';
			if ($gallerySettings['show_gps_latitude'] && !empty($exifRow['gps_latitude']))
				echo '
				<tr><td>',$txt['show_gps_latitude2'],'</td>
				<td>',$exifRow['gps_latitude'],'</td>
				</tr>';
			if ($gallerySettings['show_gps_longituderef'] && !empty($exifRow['gps_longituderef']))
				echo '
				<tr><td>',$txt['show_gps_longituderef2'],'</td>
				<td>',$exifRow['gps_longituderef'],'</td>
				</tr>';
			if ($gallerySettings['show_gps_longitude'] && !empty($exifRow['gps_longitude']))
				echo '
				<tr><td>',$txt['show_gps_longitude2'],'</td>
				<td>',$exifRow['gps_longitude'],'</td>
				</tr>';



			echo '
			</table>
			<br />';
		}
	}

	// END EXIF DATA

	// Show rating information
	if ($modSettings['gallery_set_img_showrating']  == true && $context['gallery_pic']['allowratings'] == true)
		if ($modSettings['gallery_show_ratings'] == true && $context['gallery_pic']['disablerating'] == 0)
		{
			$max_num_stars = 5;

			if ($context['gallery_pic']['totalratings'] == 0)
			{
				// Display message that no ratings are in yet
				echo $txt['gallery_form_rating'] . $txt['gallery_form_norating'];
			}
			else
			{
				//Compute the rating in %
				$rating =($context['gallery_pic']['rating'] / ($context['gallery_pic']['totalratings']* $max_num_stars) * 100);

				if ($gallerySettings['gallery_points_instead_stars'])
					echo $txt['gallery_form_rating'] . $context['gallery_pic']['rating'] . '';
				else
					echo $txt['gallery_form_rating'] . GetStarsByPercent($rating)  . ' ' . $txt['gallery_form_ratingby'] .$context['gallery_pic']['totalratings'] . $txt['gallery_form_ratingmembers'];
			}

			// If the user can manage the gallery let them see who voted for what and option to delete rating
				if ($g_manage)
					echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=viewrating;id=' . $context['gallery_pic']['id_picture'] . '">' . $txt['gallery_form_viewratings'] . '</a>';

			echo '<br />';


			if (allowedTo('smfgallery_ratepic'))
			{

				$found = $context['gallery_user_has_rated'];


				if ($found == false)
				{
					echo '<form method="post" action="' . $scripturl . '?action=gallery;sa=rate">';
						for($i = 1; $i <= $max_num_stars;$i++)
							echo '<input type="radio" name="rating" value="' . $i .'" />' . str_repeat('<img src="' . $settings['images_url'] . '/membericons/icon.png" alt="*" border="0" />', $i);

					echo '
						 <input type="hidden" name="id" value="' . $context['gallery_pic']['id_picture'] . '" />
						 <input type="submit" name="submit" value="' . $txt['gallery_form_ratepicture'] . '" />
					</form>';
				}


				echo '<br />';
			}
		}

		// Like System
		if ($gallerySettings['gallery_set_likesystem'] == 1)
		{
			$newLikeLine = 0;
			if ($modSettings['gallery_set_img_totallikes'])
			{
				// Show total likes
				echo $txt['gallery_txt_total_likes2']	 . ' <a href="' . $scripturl . '?action=gallery;sa=viewlikes;id=' . $context['gallery_pic']['id_picture'] . '">' . $context['gallery_pic']['totallikes'] . '</a>';

				$newLikeLine = 1;
			}


			if (empty($context['gallery_pic']['ID_LIKE']) && $user_info['is_guest'] == false)
			{
				// Show Like Link
				echo ' <a href="' . $scripturl . '?action=gallery;sa=like;id=' . $context['gallery_pic']['id_picture'] . '">' . $txt['gallery_txt_likes'] . '</a>';

				$newLikeLine = 1;

			}

			if ($newLikeLine == 1)
				echo '<br />';



		}


	// Show share buttons
	if ($gallerySettings['gallery_share_googleplus'] == true ||  $gallerySettings['gallery_share_facebooklike']   ||  $gallerySettings['gallery_share_facebook'] == true || $gallerySettings['gallery_share_twitter'] == true  || $gallerySettings['gallery_share_addthis'] == true
	|| $gallerySettings['gallery_share_pinterest'] == true || $gallerySettings['gallery_share_reddit'] == true
	)
	{
		echo '<table border="0">
			<tr>';


		if (!empty($gallerySettings['gallery_share_addthis']))
		{
				echo '<td valign="middle">';
				echo '<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style ">
<a href="https://www.addthis.com/bookmark.php?v=250&amp;pubid=xa-4d9e7cdd6b6966e1" class="addthis_button_compact">' . $txt['gallery_txt_share'] . '</a>
</div>
<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4d9e7cdd6b6966e1"></script>
<!-- AddThis Button END -->';
				echo '</td>';
		}

		if (!empty($gallerySettings['gallery_share_facebooklike']))
		{
			echo '<td valign="middle">';
			echo '<iframe src="//www.facebook.com/plugins/like.php?href=' . urlencode($scripturl . '?action=gallery&sa=view&id=' . $context['gallery_pic']['id_picture']) .'&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>';

			echo '</td>';
		}

		if ($gallerySettings['gallery_share_facebook'])
		{
				echo '<td valign="middle">';
				echo '<a name="fb_share" type="button" target="_blank" href="https://www.facebook.com/sharer.php?u=' . urlencode($scripturl . '?action=gallery&sa=view&id=' . $context['gallery_pic']['id_picture']). '&t=' . urlencode($context['gallery_pic']['title']) . '&i=' . urlencode($modSettings['gallery_url'] . $context['gallery_pic']['filename'])  . '"><img src="' . $modSettings['gallery_url'] . '/FacebookShare.png" alt="' . $txt['gallery_txt_share'] . '" /></a>';

				//echo '<a name="fb_share" type="button" href="https://www.facebook.com/sharer.php?u=' . $scripturl . '?action=gallery&sa=view&id=' . $context['gallery_pic']['id_picture']. '&t=' . $context['gallery_pic']['title'] . '">' . $txt['gallery_txt_share'] . '</a><script src="https://www.facebook.com/connect.php/js/FB.Share" type="text/javascript"></script>';

				echo '</td>';
		}

		if ($gallerySettings['gallery_share_twitter'])
		{
			echo '<td valign="middle">';
			echo '<a href="https://www.twitter.com/home?status=' . urlencode($context['gallery_pic']['title']) . '+URL+' . gallery_getTinyUrl($scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic']['id_picture']) . '"><img src="https://twitter-badges.s3.amazonaws.com/t_small-b.png" alt=""/></a>';
			echo '</td>';
		}




		if ($gallerySettings['gallery_share_pinterest'])
		{
			echo '<td valign="middle">';
			echo '<a href="https://www.pinterest.com/pin/create/button/?url=' . urlencode($scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic']['id_picture']) . '&media=' . urlencode($modSettings['gallery_url'] . $context['gallery_pic']['filename'])  . '&description=' . urlencode($context['gallery_pic']['title']) . '" data-pin-do="buttonPin" data-pin-config="beside" data-pin-color="red"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_20.png" /></a>
<!-- Please call pinit.js only once per page -->
<script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>';

			echo '</td>';
		}


		if (!empty($gallerySettings['gallery_share_reddit']))
		{
			echo '<td valign="middle">';
			echo '<script type="text/javascript" src="//www.redditstatic.com/button/button1.js?newwindow=1"></script>';
			echo '</td>';
		}




		echo '
			</tr>
			</table>';


		echo '<br />';
	}


	// Show image linking codes
	if ($context['gallery_pic']['type'] == 0)
	{
		if ($modSettings['gallery_set_showcode_bbc_image']  || $modSettings['gallery_set_showcode_directlink'] || $modSettings['gallery_set_showcode_htmllink'])
		{
			echo '<br /><b>',$txt['gallery_txt_image_linking'],'</b><br />
			<table border="0">';

			if ($modSettings['gallery_set_showcode_bbc_image'])
				echo '<tr><td width="30%">', $txt['gallery_txt_bbcimage'], '</td><td> <input type="text" id="cpy_text1" value="[img]' . $modSettings['gallery_url'] . $context['gallery_pic']['filename']  . '[/img]" size="75" /> <img src="' . $modSettings['gallery_url'] . '/page_copy.png" alt="" id="btn_cpy_text1" /><script>document.querySelector("#btn_cpy_text1").onclick = function() {document.querySelector("#cpy_text1").select();document.execCommand("copy");};</script></td></tr>';

			if ($gallerySettings['gallery_set_showcode_bbc_thumbnail'])
				echo '<tr><td width="30%">', $txt['gallery_txt_bbc_thumbnail_image'], '</td><td> <input type="text" id="cpy_text2" value="[url=' . $scripturl .  '?action=gallery;sa=view;id=' .  $context['gallery_pic']['id_picture'] . '][img]' . $modSettings['gallery_url'] . $context['gallery_pic']['thumbfilename']  . '[/img][/url]" size="75" /> <img src="' . $modSettings['gallery_url'] . '/page_copy.png" alt="" id="btn_cpy_text2" /><script>document.querySelector("#btn_cpy_text2").onclick = function() {document.querySelector("#cpy_text2").select();document.execCommand("copy");};</script></td></tr>';

			if ($gallerySettings['gallery_set_showcode_bbc_medium'] && $context['gallery_pic']['mediumfilename'] != '')
				echo '<tr><td width="30%">', $txt['gallery_txt_bbc_medium_image'], '</td><td> <input type="text" id="cpy_text3" value="[url=' . $scripturl .  '?action=gallery;sa=view;id=' .  $context['gallery_pic']['id_picture'] . '][img]' . $modSettings['gallery_url'] . $context['gallery_pic']['mediumfilename']  . '[/img][/url]" size="75" /> <img src="' . $modSettings['gallery_url'] . '/page_copy.png" alt="" id="btn_cpy_text3" /><script>document.querySelector("#btn_cpy_text3").onclick = function() {document.querySelector("#cpy_text3").select();document.execCommand("copy");};</script></td></tr>';


			if ($modSettings['gallery_set_showcode_directlink'])
				echo '<tr><td width="30%">', $txt['gallery_txt_directlink'], '</td><td> <input type="text" id="cpy_text4" value="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename']  . '" size="75" /> <img src="' . $modSettings['gallery_url'] . '/page_copy.png" alt="" id="btn_cpy_text4" /><script>document.querySelector("#btn_cpy_text4").onclick = function() {document.querySelector("#cpy_text4").select();document.execCommand("copy");};</script></td></tr>';
			if ($modSettings['gallery_set_showcode_htmllink'])
				echo '<tr><td width="30%">', $txt['gallery_txt_htmllink'], '</td><td> <input type="text" id="cpy_text5" value="<img src=&#34;' . $modSettings['gallery_url'] . $context['gallery_pic']['filename']  . '&#34; />" size="75" /> <img src="' . $modSettings['gallery_url'] . '/page_copy.png" alt="" id="btn_cpy_text5" /><script>document.querySelector("#btn_cpy_text5").onclick = function() {document.querySelector("#cpy_text5").select();document.execCommand("copy");};</script></td></tr>';
			echo '</table>';
		}
	}
	else
	{
		if ($modSettings['gallery_video_showbbclinks'])
		{
			echo '<br /><b>',$txt['gallery_txt_video_linking'],'</b><br />
				<table border="0">';


				echo '
				<tr><td width="30%">', $txt['gallery_video_htmllink'], '</td><td> <input type="text" id="cpy_text6" value="<a href=&#34;' . ($context['gallery_pic']['type'] == 1 ?  $modSettings['gallery_url'] . 'videos/' . $context['gallery_pic']['videofile'] : $context['gallery_pic']['videofile'] )  . '&#34>' . $context['gallery_pic']['title'] . '</a>" size="50"> <img src="' . $modSettings['gallery_url'] . '/page_copy.png" alt="" id="btn_cpy_text6" /><script>document.querySelector("#btn_cpy_text6").onclick = function() {document.querySelector("#cpy_text6").select();document.execCommand("copy");};</script></td></tr>
				';
			echo '</table>';
		}
	}

	echo '
		</td>
	</tr>';

	// Display who is viewing the picture.
	if (!empty($modSettings['gallery_who_viewing']))
	{
		echo '<tr>
		<td align="center" class="windowbg2"><span class="smalltext">';

		// Show just numbers...?
		// show the actual people viewing the topic?
		echo empty($context['view_members_list']) ? '0 ' . $txt['gallery_who_members'] : implode(', ', $context['view_members_list']) . (empty($context['view_num_hidden']) || $context['can_moderate_forum'] ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['gallery_who_hidden'] . ')');

		// Now show how many guests are here too.
		echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['gallery_who_viewpicture'], '</span></td></tr>';
	}



		// Show Related Images
		if (!empty($gallerySettings['gallery_set_relatedimagescount']))
		{
			$relatedPics = Gallery_ReturnRelatedPictures($context['gallery_pic']['id_picture']);

			if (!empty($relatedPics))
			{
				echo '
				<tr class="catbg">
					<td align="center">', $txt['gallery_txt_relatedimages'], '</td>
				</tr>

				<tr class="windowbg2">
					<td align="center"><table>';
		$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];

		$rowlevel = 0;
		foreach($relatedPics as $row)
		{

			if ($rowlevel == 0)
				echo '<tr class="windowbg2">';


			if ($row['mature'] == 1)
			{
				if (CanViewMature() == false)
					$row['thumbfilename'] = 'mature.gif';
			}


			echo '<td align="center">';
			if (!empty($modSettings['gallery_set_t_title']))
				echo $row['title'] . '<br />';

			echo '<a href="', $scripturl, '?action=gallery;sa=view;id=', $row['id_picture'], '"><img src="', $modSettings['gallery_url'], $row['thumbfilename'], '" alt="" /></a><br />';

			// Unread
			if (empty($row['unread'])) // && $gallerySettings['gallery_set_show_newicon'] == true)
			{
				echo '<img src="' . $modSettings['gallery_url'] . 'new.gif" alt="" /><br />';
			}


			echo '<span class="smalltext">';
			if (!empty($modSettings['gallery_set_t_rating']))
			{
				if ($gallerySettings['gallery_points_instead_stars'])
					echo $txt['gallery_form_rating'] . $row['rating'] . '<br />';
				else
					echo $txt['gallery_form_rating'] . GetStarsByPercent(($row['totalratings'] != 0) ? ($row['rating'] / ($row['totalratings']* 5) * 100) : 0) . '<br />';

			}

			if (!empty($modSettings['gallery_set_t_views']))
				echo $txt['gallery_text_views'] . $row['views'] . '<br />';
			if (!empty($modSettings['gallery_set_t_filesize']))
				echo $txt['gallery_text_filesize'] . gallery_format_size($row['filesize'], 2) . '<br />';
			if (!empty($modSettings['gallery_set_t_date']))
				echo $txt['gallery_text_date'] . timeformat($row['date']) . '<br />';
			if (!empty($modSettings['gallery_set_t_comment']))
				echo $txt['gallery_text_comments'] . ' (<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '">' . $row['commenttotal'] . '</a>)<br />';
			if (!empty($modSettings['gallery_set_t_username']))
			{

					// Disable member color link
					if (!empty($modSettings['gallery_disable_membercolorlink']))
						$row['online_color'] = '';

				if ($row['real_name'] != '')
					echo $txt['gallery_text_by'] . ' <a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '" ' . (!empty($row['online_color']) ? ' style="color: ' . $row['online_color'] . ';" ' :'' ) . '>'  . $row['real_name'] . '</a><br />';
				else
					echo $txt['gallery_text_by'] . ' ' . $txt['gallery_guest'] . '<br />';
			}

			if ($g_manage)
				echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=unapprove;id=' . $row['id_picture'] . '">' . $txt['gallery_text_unapprove'] . '</a>';
			if ($g_manage || $g_edit_own && $row['id_member'] == $user_info['id'])
				echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=edit;id=' . $row['id_picture'] . '">' . $txt['gallery_text_edit'] . '</a>';
			if ($g_manage || $g_delete_own && $row['id_member'] == $user_info['id'])
				echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=delete;id=' . $row['id_picture'] . '">' . $txt['gallery_text_delete'] . '</a>';

			echo '</span></td>';



		if ($rowlevel < ($maxrowlevel-1))
				$rowlevel++;
			else
			{
				echo '</tr>';
				$rowlevel = 0;
			}
		}
		if ($rowlevel != 0)
		{
			if (($maxrowlevel - $rowlevel) > 0)
				echo '<td colspan="' .($maxrowlevel - $rowlevel) .'"></td>';

			echo '</tr>';
		}


				echo '</table></td></tr>';

			}

		}


	echo '
		</table><br />';

	if (empty($gallerySettings['gallery_set_picture_information_last']))
		ShowCommentsPicture();



			     	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="' . $scripturl . '?action=gallery;' . $extracat . 'cat=' . $context['gallery_pic']['id_cat'] . '">' . $txt['gallery_text_returngallery'] . '</a>
            </div>
        </div>';




}

function ShowCommentsPicture()
{
	global $scripturl, $context, $txt, $user_info, $modSettings, $settings, $user_info, $gallerySettings, $memberContext;

	// Load permissions
	$g_manage = allowedTo('smfgallery_manage');


	$g_edit_comment = allowedTo('smfgallery_editcomment');
	$g_report = allowedTo('smfgallery_report');

	if (empty($context['gallery_pic']['user_id_cat']))
		$checkCatCommentPermission = GetCatPermission($context['gallery_pic']['id_cat'],'addcomment',true);
	else
		$checkCatCommentPermission = true;



	// Check if allowed to display comments for this picture
	if ($context['gallery_pic']['allowcomments'])
	{

	  	if (allowedTo('smfgallery_comment') && $checkCatCommentPermission  == true)
	  		$can_comment = true;
  		else
			$can_comment = false;

		// Display all user comments
		$comment_count = $context['gallery_comment_count'];

		// Show comments
		echo '
<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_text_comments'], ' (' . $comment_count . ') ' . (!empty($gallerySettings['gallery_enable_rss']) ? ' <a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic']['id_picture'] . ';showrss=1"><img src="' . $modSettings['gallery_url'] . '/rss.png" alt="rss" /></a>' : '') . '
		</h3>
  </div>
		<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">
			';

		if ($can_comment)
		{
			// Show Add Comment
			echo '
				<tr class="windowbg"><td colspan="2">
				<a href="' . $scripturl . '?action=gallery;sa=comment;id=' . $context['gallery_pic']['id_picture'] . '">' . $txt['gallery_text_addcomment']  . '</a></td>
				</tr>';
		}


		$context['allow_hide_email'] = !empty($modSettings['allow_hideEmail']) || ($user_info['is_guest'] && !empty($modSettings['guest_hideContacts']));
		$common_permissions = array('can_send_pm' => 'pm_send');

		foreach ($common_permissions as $contextual => $perm)
			$context[$contextual] = allowedTo($perm);

		$styleClass = 'windowbg2';
		foreach($context['gallery_comment_picture_list'] as $row)
		{
			$approvalText = '';
			if ($row['approved'] == 0)
			{
				$approvalText = '<strong>' . $txt['gallery_txt_waitingforapproval'] . '</strong> - ';
				$styleClass = 'approvetbg2';
			}


			echo '<tr class="' . $styleClass .'">';
			// Display member info
			echo '<td width="15%" valign="top"><a name="c' . $row['id_comment'] . '"></a>';

			if ($row['real_name'] != '')
			{
			 // Display the users avatar
				$memCommID = $row['id_member'];
				if ($row['real_name'])
				{
					$memCommID = $row['id_member'];

					if (!isset($memberContext[$memCommID]['link']))
					{
						loadMemberData($memCommID);
						loadMemberContext($memCommID);
					}

					ShowUserBox($memCommID);

				}
			}
			else
				echo $txt['gallery_guest'] . '<br />';

			echo '</td>';
			// Display the comment
			echo '<td width="85%" valign="top">' . $approvalText . '<span class="smalltext">' .  ($modSettings['gallery_ratings_require_comment'] == 1 ?  ' ' . $txt['gallery_form_rating'] . GetStarsByPercent((!empty($row['value'])) ? ($row['value'] / (1* 5) * 100) : 0) . ' ' : '') . timeformat($row['date']) . '</span><hr />';

			echo parse_bbc($row['comment']);

			if ($row['modified_id_member'] != 0 && empty($gallerySettings['gallery_set_hide_lastmodified_comment']))
			{

				echo '<br /><span class="smalltext"><i>' . $txt['gallery_text_commodifiedby']  . '<a href="' . $scripturl . '?action=profile;u=' . $row['modified_id_member'] . '">'  . $row['modified_real_name'] . '</a> ' . timeformat($row['lastmodified']) .  ' </i></span>';

			}



			if ($g_manage || $g_report || $can_comment || ($g_edit_comment && $row['id_member'] == $user_info['id']))
				echo '<br /><br />';

			if ($can_comment)
			{
				echo '<a href="' . $scripturl . '?action=gallery;sa=comment;id=' . $context['gallery_pic']['id_picture'] . ';comment=' . $row['id_comment'] . '">' . $txt['gallery_txt_quotecomment'] . '</a>';
			}

			// Check if they can edit the comment
			if ($g_manage || $g_edit_comment && $row['id_member'] == $user_info['id'])
				echo '<a href="' . $scripturl . '?action=gallery;sa=editcomment;id=' . $row['id_comment'] . '">' . $txt['gallery_text_edcomment'] .'</a>';

			if ($g_manage || $g_report)
				echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=reportcomment;id=' . $row['id_comment'] . '">' . $txt['gallery_text_repcomment'] .'</a>';

			if ($g_manage && $row['approved'] == 0)
				echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=apprcomment;id=' . $row['id_comment'] . '">' . $txt['gallery_text_approve']  . '</a>';

			// Check if the user is allowed to delete the comment.
			if ($g_manage)
				echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=delcomment;id=' . $row['id_comment'] . '">' . $txt['gallery_text_delcomment'] .'</a>';

			echo '</td>';
			echo '</tr>';

			if ($styleClass == 'windowbg')
				 $styleClass = 'windowbg2';
 			 else
	 			$styleClass = 'windowbg';


		}


		if (allowedTo('smfgallery_comment') && $comment_count != 0 && $checkCatCommentPermission  == true)
		{
			// Show Add Comment
			echo '
				<tr class="windowbg"><td colspan="2">
				<a href="' . $scripturl . '?action=gallery;sa=comment;id=' . $context['gallery_pic']['id_picture'] . '">' . $txt['gallery_text_addcomment'] . '</a></td>
				</tr>';
		}

		echo '</table><br />';

		// Quick Reply Option
		if (allowedTo('smfgallery_comment') && $modSettings['gallery_set_show_quickreply'] == true && $checkCatCommentPermission  == true)
		{

			echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_addcomment'], '
		</h3>
  </div>
		<form method="post" action="' . $scripturl . '?action=gallery;sa=comment2" id="cprofile" name="cprofile" onsubmit="submitonce(this);">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tborder" align="center">
		';

if (!empty($gallerySettings['gallery_set_quickreply_full']))
{
		if (!function_exists('getLanguages'))
		{
				// Showing BBC?
			if ($context['show_bbc'])
			{
				echo '
									<tr class="windowbg2">

										<td colspan="2" align="center">
											', template_control_richedit($context['post_box_name'], 'bbc'), '
										</td>
									</tr>';
			}

			// What about smileys?
			if (!empty($context['smileys']['postform']))
				echo '
									<tr class="windowbg2">

										<td colspan="2" align="center">
											', template_control_richedit($context['post_box_name'], 'smileys'), '
										</td>
									</tr>';

			// Show BBC buttons, smileys and textbox.
			echo '
									<tr class="windowbg2">

										<td colspan="2" align="center">
											', template_control_richedit($context['post_box_name'], 'message'), '
										</td>
									</tr>';

		}
		else
		{
			echo '
									<tr class="windowbg2">
			<td colspan="2">';
				// Showing BBC?
			if ($context['show_bbc'])
			{
				echo '
						<div id="bbcBox_message"></div>';
			}

			// What about smileys?
			if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
				echo '
						<div id="smileyBox_message"></div>';

			// Show BBC buttons, smileys and textbox.
			echo '
						', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');


			echo '</td></tr>';
		}

		}
		else
			  echo '
			  <tr>
				<td width="28%" valign="top" class="windowbg2" align="right"><b>' . $txt['gallery_form_comment'] . '</b>&nbsp;</td>
				<td width="72%" class="windowbg2"><textarea rows="6" name="comment" cols="54"></textarea></td>
			  </tr>';


	// Is visual verification enabled?
	if ($context['require_verification'])
	{
		echo '
							<tr class="windowbg2">
								<td align="right" valign="top"', !empty($context['post_error']['need_qr_verification']) ? ' style="color: red;"' : '', '>
									<b>', $txt['verification'], ':</b>
								</td>
								<td>
									', template_control_verification($context['visual_verification_id'], 'all'), '
								</td>
							</tr>';
	}


			echo '
			  <tr class="windowbg2">
				<td width="28%" colspan="2" align="center">
				<input type="hidden" name="id" value="' . $context['gallery_pic']['id_picture'] . '" />';

			if (allowedTo('smfgallery_autocomment') == false)
				echo $txt['gallery_text_commentwait'] . '<br />';

			echo '
				<input type="submit" value="', $txt['gallery_text_addcomment'], '" name="submit" />';

			if ($context['show_spellchecking'])
				echo '
					<input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'cprofile\', \'comment\');" />';

			echo '</td>
			  </tr>
			</table>
		</form>';

				}
	}

}

function template_delete_picture()
{
	global $scripturl, $modSettings, $txt, $context;

	ShowTopGalleryBar();

	echo '<div class="tborder">
	<form method="post" action="' . $scripturl . '?action=gallery;sa=delete2">
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_form_delpicture'] , '
		</h3>
  </div>
  <div class="information">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	' . $txt['gallery_warn_deletepicture'] . '
	<br />
<div align="center"><br /><b>' . $txt['gallery_text_delpicture'] . '</b><br />
<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic']['id_picture'] . '" target="blank"><img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['thumbfilename']  . '" alt="" /></a><br />
			<span class="smalltext">Views: ' . $context['gallery_pic']['views'] . '<br />
			' . $txt['gallery_text_filesize']  . gallery_format_size($context['gallery_pic']['filesize']) . '<br />
			' . $txt['gallery_text_date'] .  timeformat($context['gallery_pic']['date']) . '<br />
			' . $txt['gallery_text_comments'] . ' (<a href="' . $scripturl . '?action=gallery;sa=view;id=' .  $context['gallery_pic']['id_picture'] . '" target="blank">' .  $context['gallery_pic']['commenttotal'] . '</a>)<br />
	</div><br />
	<input type="hidden" name="id" value="' . $context['gallery_pic']['id_picture'] . '" />
	<input type="submit" value="' . $txt['gallery_form_delpicture'] . '" name="submit" /><br />
	</td>
  </tr>
</table>
</div>
		</form>
</div>';

}

function template_add_comment()
{
	global $context, $scripturl, $txt, $settings, $modSettings;

	ShowTopGalleryBar();

	echo '
<div class="tborder">
<form method="post" name="cprofile" id="cprofile" action="' . $scripturl . '?action=gallery;sa=comment2" onsubmit="submitonce(this);">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_addcomment'], '
		</h3>
  </div>
  <div class="information">
<table border="0" cellpadding="3" cellspacing="1" width="100%">
	<tr class="windowbg2">
		<td colspan="2" align="center">
			<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic_id'] . '" target="blank"><img src="' . $modSettings['gallery_url'] . $context['gallery_pic_thumbfilename'] . '" alt="" /></a>
		</td>
	</tr>';

	if (!function_exists('getLanguages'))
	{
			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
								<tr class="windowbg2">
									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'bbc'), '
									</td>
								</tr>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']))
			echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'smileys'), '
									</td>
								</tr>';

		// Show BBC buttons, smileys and textbox.
		echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'message'), '
									</td>
								</tr>';

	}
	else
	{
		echo '
								<tr class="windowbg2">
		<td colspan="2">';
			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Show BBC buttons, smileys and textbox.
		echo '
					', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');


		echo '</td></tr>';
	}

	// Is visual verification enabled?
	if ($context['require_verification'])
	{
		echo '
							<tr class="windowbg2">
								<td align="right" valign="top"', !empty($context['post_error']['need_qr_verification']) ? ' style="color: red;"' : '', '>
									<b>', $txt['verification'], ':</b>
								</td>
								<td>
									', template_control_verification($context['visual_verification_id'], 'all'), '
								</td>
							</tr>';
	}



	echo '

	<tr class="windowbg2">
		<td width="28%" colspan="2" align="center">
			<input type="hidden" name="id" value="' . $context['gallery_pic_id'] . '" />';

	if (allowedTo('smfgallery_autocomment') == false)
		echo $txt['gallery_text_commentwait'] . '<br />';

	if ($context['show_spellchecking'])
		echo '
			<input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'cprofile\', \'comment\');" />';

	echo '
			<input type="submit" value="', $txt['gallery_text_addcomment'], '" name="submit" />
		</td>
	</tr>
</table>
</div>
</form>';

	echo '</div>';


}

function template_report_picture()
{
	global $scripturl, $context, $txt;

	ShowTopGalleryBar();


	echo '<div class="tborder">
<form method="post" name="cprofile" id="cprofile" action="' . $scripturl . '?action=gallery;sa=report2">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_form_reportpicture'] , '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
	<td width="28%" valign="top" align="right"><b>' . $txt['gallery_form_comment'] . '</b>&nbsp;</td>
	<td width="72%"><textarea rows="6" name="comment" cols="54"></textarea></td>
  </tr>
  <tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
	<input type="hidden" name="id" value="' . $context['gallery_pic_id'] . '" />
	<input type="submit" value="' . $txt['gallery_form_reportpicture'] . '" name="submit" /></td>
  </tr>
</table>
</form></div>';


}

function template_report_comment()
{
	global $scripturl, $context, $txt;

	echo '<div class="tborder">
<form method="post" name="cprofile" id="cprofile" action="' . $scripturl . '?action=gallery;sa=reportcomment2">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_reportcomment'] , '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
	<td width="28%" valign="top" align="right"><b>' . $txt['gallery_form_comment'] . '</b>&nbsp;</td>
	<td width="72%"><textarea rows="6" name="comment" cols="54"></textarea></td>
  </tr>
  <tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
	<input type="hidden" name="id" value="' . $context['gallery_comment_id'] . '" />
	<input type="submit" value="' . $txt['gallery_text_reportcomment'] . '" name="submit" /></td>
  </tr>
</table>
</form></div>';


}

function template_manage_cats()
{
	global $scripturl, $txt, $currentclass, $context, $cat_sep;

echo '

<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_form_managecats'], '
		</h3>
  </div>
  <div class="information">
		<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr>
		<td>
			<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr>
				<td colspan="3" align="center"><a href="' . $scripturl . '?action=gallery;sa=addcat">' . $txt['gallery_text_addcategory'] . '</a></td>
			</tr>
			<tr class="titlebg">
				<td>' . $txt['gallery_text_galleryname'] . '</td>
				<td>' . $txt['gallery_text_totalimages'] . '</td>
				<td>' . $txt['gallery_text_options'] . '</td>
				</tr>
			';

		$currentclass = "windowbg";


		foreach ($context['gallery_cat'] as $i => $category)
		{

			if ($category['id_parent'] == 0)
			{
					echo '<tr>
				<td>
				<a href="' . $scripturl . '?action=gallery;' . ($category['redirect'] == 0 ? 'cat=' . $category['id_cat'] :  'su=user;sa=userlist') . '">',$category['title'],'</a>
				</td>
				<td>' .  $category['total'] . '</td>
				<td><a href="' . $scripturl . '?action=gallery;sa=editcat;cat=' . $category['id_cat'] . '">' . $txt['gallery_text_edit'] . '</a>&nbsp;';

				if ($category['redirect'] == 0)
				echo '
				<a href="' . $scripturl . '?action=gallery;sa=deletecat;cat=' . $category['id_cat'] . '">' . $txt['gallery_text_delete'] . '</a>&nbsp;
				<a href="' . $scripturl . '?action=gallery;sa=catperm;cat=' . $category['id_cat'] . '">[' . $txt['gallery_text_permissions'] . ']</a><br />
				<a href="' . $scripturl . '?action=gallery;sa=import;cat=' .  $category['id_cat'] . '">' . $txt['gallery_text_importpics'] . '</a>&nbsp;
				<a href="' . $scripturl . '?action=gallery;sa=regen;cat=' .  $category['id_cat'] . '">' . $txt['gallery_text_regeneratethumbnails'] . '</a>';
				else
					echo '<a href="' . $scripturl . '?action=gallery;sa=regen;usercat=1">' . $txt['gallery_text_regeneratethumbnails'] . '</a>';

				echo '
				</td>

				</tr>

	<tr>
				<td colspan="3" align="center"><hr></td>
</tr>
				';

				if ($currentclass == "windowbg")
					$currentclass = "windowbg2";
				else
					$currentclass = "windowbg";
				$cat_sep = 1;
				GetManageSubCats($category['id_cat'],$context['gallery_cat']);
				$cat_sep = 0;
			}
		}



	echo '
		<tr>
				<td colspan="3" align="center"><a href="' . $scripturl . '?action=gallery;sa=addcat">' . $txt['gallery_text_addcategory'] . '</a></td>
			</tr>
	</table>
	</td>
	</tr>
</table>
</div>';
}

function template_settings()
{
	global $scripturl, $modSettings, $txt, $currentVersion, $context, $gallerySettings;
	// Settings Admin Tabs
	SettingsAdminTabs();

	echo '
<div id="moderationbuttons" class="margintop">
	', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
</div><br /><br /><br />';

	echo '
			<form method="post" action="' . $scripturl . '?action=gallery;sa=adminset2">
	<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_text_settings'], '
		</h3>
  </div>

	<div class="windowbg noup">

			',$txt['gallery_txt_yourversion'] , $currentVersion, '&nbsp;',$txt['gallery_txt_latestversion'],'<span id="lastgallery" name="lastgallery"></span>
			<br />

				<table  border="0" width="100%" cellspacing="0"  align="center" cellpadding="4">
				<tr><td width="30%">
				' . $txt['gallery_set_maxheight'] . '</td><td><input type="text" name="gallery_max_height" value="' .  $modSettings['gallery_max_height'] . '" /></td></tr>

				<tr><td width="30%">' . $txt['gallery_set_maxwidth'] . '</td><td><input type="text" name="gallery_max_width" value="' .  $modSettings['gallery_max_width'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_minheight'] . '</td><td><input type="text" name="gallery_min_height" value="' .  $modSettings['gallery_min_height'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_minwidth'] . '</td><td><input type="text" name="gallery_min_width" value="' .  $modSettings['gallery_min_width'] . '" /></td></tr>

				<tr><td width="30%">' . $txt['gallery_set_filesize'] . '</td><td><input type="text" name="gallery_max_filesize" value="' .  $modSettings['gallery_max_filesize'] . '" /> (bytes)</td></tr>

				<tr><td width="30%">' . $txt['gallery_upload_max_filesize'] . '</td><td><a href="https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize" target="_blank">' . @ini_get("upload_max_filesize") . '</a></td></tr>
				<tr><td width="30%">' . $txt['gallery_post_max_size'] . '</td><td><a href="https://www.php.net/manual/en/ini.core.php#ini.post-max-size" target="_blank">' . @ini_get("post_max_size") . '</a></td></tr>
				<tr><td colspan="2">',$txt['gallery_upload_limits_notes'] ,'</td></tr>

				<tr><td width="30%">' . $txt['gallery_set_path'] . '</td><td><input type="text" name="gallery_path" value="' .  $modSettings['gallery_path'] . '" size="50" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_url'] . '</td><td><input type="text" name="gallery_url" value="' .  $modSettings['gallery_url'] . '" size="50" /></td></tr>


				<tr><td width="30%" valign="top">' . $txt['gallery_set_disallow_extensions'] . '</td><td><input type="text" name="gallery_set_disallow_extensions" value="' .  $gallerySettings['gallery_set_disallow_extensions'] . '" /><br />' .$txt['gallery_set_disallow_extensions_more'] . '</td></tr>
				<tr><td width="30%">' . $txt['gallery_jpeg_compression'] . '</td><td><input type="text" name="gallery_jpeg_compression" value="' .  $modSettings['gallery_jpeg_compression'] . '" size="4" /></td></tr>

				<tr><td width="30%">' . $txt['gallery_set_batchadd_path'] . '</td><td><input type="text" name="gallery_set_batchadd_path" value="' .  $gallerySettings['gallery_set_batchadd_path'] . '" size="50" /></td></tr>

				<tr><td width="30%">' . $txt['gallery_set_images_per_page'] . '</td><td><input type="text" name="gallery_set_images_per_page" value="' .  $modSettings['gallery_set_images_per_page'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_images_per_row'] . '</td><td><input type="text" name="gallery_set_images_per_row" value="' .  (empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'])  . '" /></td></tr>

				<tr><td width="30%">' . $txt['gallery_set_thumb_width'] . '</td><td><input type="text" name="gallery_thumb_width" value="' .  $modSettings['gallery_thumb_width'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_thumb_height'] . '</td><td><input type="text" name="gallery_thumb_height" value="' .  $modSettings['gallery_thumb_height'] . '" /></td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_make_medium" ' . ($modSettings['gallery_make_medium'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_make_medium'] . '<br /></td></tr>
				<tr><td width="30%">' . $txt['gallery_medium_width'] . '</td><td><input type="text" name="gallery_medium_width" value="' .  $modSettings['gallery_medium_width'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_medium_height'] . '</td><td><input type="text" name="gallery_medium_height" value="' .  $modSettings['gallery_medium_height'] . '" /></td></tr>


				<tr><td width="30%">' . $txt['gallery_set_disp_maxwidth'] . '</td><td><input type="text" name="gallery_set_disp_maxwidth" value="' .  $modSettings['gallery_set_disp_maxwidth'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_disp_maxheight'] . '</td><td><input type="text" name="gallery_set_disp_maxheight" value="' .  $modSettings['gallery_set_disp_maxheight'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_cat_width'] . '</td><td><input type="text" name="gallery_set_cat_width" value="' .  $modSettings['gallery_set_cat_width'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_cat_height'] . '</td><td><input type="text" name="gallery_set_cat_height" value="' .  $modSettings['gallery_set_cat_height'] . '" /></td></tr>

				<tr><td width="30%">' . $txt['gallery_set_maxuploadperday'] . '</td><td><input type="text" name="gallery_set_maxuploadperday" value="' .  $gallerySettings['gallery_set_maxuploadperday'] . '" /></td></tr>

				<tr><td width="30%">' . $txt['gallery_set_relatedimagescount'] . '</td><td><input type="text" name="gallery_set_relatedimagescount" value="' .  $gallerySettings['gallery_set_relatedimagescount'] . '" /> <a href="' . $scripturl . '?action=gallery;sa=rebuildrelated">' . $txt['gallery_txt_rebuildindex'] .'</a></td></tr>


				<tr><td width="30%">' . $txt['gallery_set_nohighslide'] . '</td><td>
				<select name="gallery_set_nohighslide">
				<option value="' .$modSettings['gallery_set_nohighslide'] . '">';

				if ($modSettings['gallery_set_nohighslide'] == 0)
					echo 'HighSlide';
				if ($modSettings['gallery_set_nohighslide'] == 2)
					echo 'Lightbox';
				if ($modSettings['gallery_set_nohighslide'] == 1)
					echo 'Nothing';
				if ($modSettings['gallery_set_nohighslide'] == 3)
					echo 'Nothing/No Fullsize Image';


				echo '</option>

				<option value="0">HighSlide</option>
				<option value="2">Lightbox</option>
				<option value="1">Nothing</option>
				<option value="3">Nothing/No Fullsize Image</option>
				</select>
				</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_image_editor" ' . ($modSettings['gallery_image_editor'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_image_editor'] . ($context['image_editor_installed'] == 1 ? '' : $txt['gallery_image_editor_not_installed']) . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_who_viewing" ' . ($modSettings['gallery_who_viewing'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_whoonline'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_resize_image" ' . ($modSettings['gallery_resize_image'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_resize_image'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_count_child" ' . ($modSettings['gallery_set_count_child'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_count_child'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_show_subcategory_links" ' . ($gallerySettings['gallery_set_show_subcategory_links'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_show_subcategory_links'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_commentsnewest" ' . ($modSettings['gallery_set_commentsnewest'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_commentsnewest'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_show_quickreply" ' . ($modSettings['gallery_set_show_quickreply'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_show_quickreply'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_quickreply_full" ' . ($gallerySettings['gallery_set_quickreply_full'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_quickreply_full'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_show_ratings" ' . ($modSettings['gallery_show_ratings'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showratings'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_enable_multifolder" ' . ($modSettings['gallery_set_enable_multifolder'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_enable_multifolder'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_setviewscountonce" ' . ($modSettings['gallery_setviewscountonce'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_setviewscountonce'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_ratings_require_comment" ' . ($modSettings['gallery_ratings_require_comment'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_ratings_require_comment'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_commentchoice" ' . ($modSettings['gallery_commentchoice'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_commentschoice'] . '</td></tr>


				<tr><td colspan="2"><input type="checkbox" name="gallery_set_picturepostcount" ' . ($gallerySettings['gallery_set_picturepostcount'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_picturepostcount'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_commentspostcount" ' . ($gallerySettings['gallery_set_commentspostcount'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_commentspostcount'] . '</td></tr>


				<tr><td colspan="2"><input type="checkbox" name="gallery_set_allowratings" ' . ($gallerySettings['gallery_set_allowratings'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_allowratings'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_allow_mature_tag" ' . ($modSettings['gallery_allow_mature_tag'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_allow_mature_tag'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_points_instead_stars" ' . ($gallerySettings['gallery_points_instead_stars'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_points_instead_stars'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_enable_rss" ' . ($gallerySettings['gallery_enable_rss'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_enable_rss'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_allow_slideshow" ' . ($gallerySettings['gallery_allow_slideshow'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_allow_slideshow'] . '</td></tr>';


				echo '
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_allow_photo_tagging" ' . ($gallerySettings['gallery_set_allow_photo_tagging'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_allow_photo_tagging'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_multifile_upload_for_bulk" ' . ($gallerySettings['gallery_set_multifile_upload_for_bulk'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_multifile_upload_for_bulk'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_allow_copy" ' . ($gallerySettings['gallery_set_allow_copy'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_allow_copy'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_require_keyword" ' . ($gallerySettings['gallery_set_require_keyword'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_require_keyword'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_allow_favorites" ' . ($gallerySettings['gallery_set_allow_favorites'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_allow_favorites'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_disableremovetopic" ' . ($gallerySettings['gallery_set_disableremovetopic'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_disableremovetopic'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_redirectcategorydefault" ' . ($gallerySettings['gallery_set_redirectcategorydefault'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_redirectcategorydefault'] . '</td></tr>


				<tr><td colspan="2"><input type="checkbox" name="gallery_set_onlyregcanviewimage" ' . ($gallerySettings['gallery_set_onlyregcanviewimage'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_onlyregcanviewimage'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_searchenablefulltext" ' . ($gallerySettings['gallery_set_searchenablefulltext'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_searchenablefulltext'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_likesystem" ' . ($gallerySettings['gallery_set_likesystem'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_likesystem'] . '</td></tr>


				<tr><td colspan="2"><input type="checkbox" name="gallery_disable_membercolorlink" ' . ($modSettings['gallery_disable_membercolorlink'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_disable_membercolorlink'] . '</td></tr>





				<tr><td colspan="2"><b>' . $txt['gallery_usergallery_settings'] . '</b></td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_user_no_password" ' . ($modSettings['gallery_user_no_password'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_user_no_password'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_user_no_private" ' . ($modSettings['gallery_user_no_private'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_user_no_private'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_userlist_onlyuploaders" ' . ($gallerySettings['gallery_userlist_onlyuploaders'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_userlist_onlyuploaders'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_userlist_hideavatar" ' . ($gallerySettings['gallery_userlist_hideavatar'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_userlist_hideavatar'] . '</td></tr>

				<tr><td width="30%">' . $txt['gallery_userlist_usersperpage'] . '</td><td><input type="text" name="gallery_userlist_usersperpage" value="' .  $gallerySettings['gallery_userlist_usersperpage'] . '" /></td></tr>


				<tr><td width="30%">' .$txt['gallery_userlist_sortby']. '</td><td>
				<select name="gallery_userlist_sortby">
				<option value="' .$gallerySettings['gallery_userlist_sortby'] . '">';

				if ($gallerySettings['gallery_userlist_sortby'] == 'total')
					echo $txt['gallery_text_totalimages'];
				if ($gallerySettings['gallery_userlist_sortby'] == 'membername')
					echo $txt['gallery_app_membername'];

				echo '</option>

				<option value="total">' . $txt['gallery_text_totalimages'] . '</option>
				<option value="membername">' . $txt['gallery_app_membername'] . '</option>
				</select>
				</td></tr>

				<tr><td width="30%">' .$txt['gallery_userlist_orderby']. '</td><td>
				<select name="gallery_userlist_orderby">
				<option value="' .$gallerySettings['gallery_userlist_orderby'] . '">';

				if ($gallerySettings['gallery_userlist_orderby'] == 'DESC')
					echo 'DESC';
				if ($gallerySettings['gallery_userlist_orderby'] == 'ASC')
					echo 'ASC';

				echo '</option>

				<option value="DESC">DESC</option>
				<option value="ASC">ASC</option>
				</select>
				</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_createusercat" ' . ($gallerySettings['gallery_set_createusercat'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_createusercat'] . '</td></tr>

				<tr><td colspan="2"><b>' . $txt['gallery_watermark_settings'] . '</b></td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_water_enabled" ' . ($modSettings['gallery_set_water_enabled'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_water_enabled'] . '</td></tr>

				<tr><td width="30%">' . $txt['gallery_set_water_image'] . '</td><td><input type="text" name="gallery_set_water_image" value="' .  $modSettings['gallery_set_water_image'] . '" size="50" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_water_percent'] . '</td><td><input type="text" name="gallery_set_water_percent" value="' .  $modSettings['gallery_set_water_percent'] . '" size="50" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_water_text'] . '</td><td><input type="text" name="gallery_set_water_text" value="' .  $modSettings['gallery_set_water_text'] . '" size="50" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_water_textcolor'] . '</td><td><input type="text" name="gallery_set_water_textcolor" value="' .  $modSettings['gallery_set_water_textcolor'] . '" size="50" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_water_textfont'] . '</td><td><input type="text" name="gallery_set_water_textfont" value="' .  $modSettings['gallery_set_water_textfont'] . '" size="50" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_water_textsize'] . '</td><td><input type="text" name="gallery_set_water_textsize" value="' .  $modSettings['gallery_set_water_textsize'] . '" size="50" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_set_water_valign'] . '</td><td>
				<select name="gallery_set_water_valign">
				<option value="' .  $modSettings['gallery_set_water_valign'] . '" selected="selected">' .  $modSettings['gallery_set_water_valign'] . '</option>
				<option value="bottom">bottom</option>
				<option value="center">center</option>
				<option value="top">top</option>
				</select>
				</td></tr>
				<tr><td width="30%">' . $txt['gallery_set_water_halign'] . '</td><td>
				<select name="gallery_set_water_halign">
				<option value="' .  $modSettings['gallery_set_water_halign'] . '" selected="selected">' .  $modSettings['gallery_set_water_halign'] . '</option>
				<option value="right">right</option>
				<option value="center">center</option>
				<option value="left">left</option>
				</select>
				</td></tr>

				<tr class="windowbg2"><td colspan="2">' . $txt['gallery_shop_settings'] . '</td></tr>

				<tr class="windowbg2"><td width="30%">' . $txt['gallery_shop_picadd'] . '</td><td><input type="text" name="gallery_shop_picadd" value="' .  $modSettings['gallery_shop_picadd'] . '" /></td></tr>
				<tr class="windowbg2"><td width="30%">' . $txt['gallery_shop_commentadd'] . '</td><td><input type="text" name="gallery_shop_commentadd" value="' .  $modSettings['gallery_shop_commentadd'] . '" /></td></tr>

				<tr><td colspan="2"><b>' . $txt['gallery_txt_image_linking'] . '</b></td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_showcode_bbc_image" ' . ($modSettings['gallery_set_showcode_bbc_image'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showcode_bbc_image'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_showcode_bbc_thumbnail" ' . ($gallerySettings['gallery_set_showcode_bbc_thumbnail'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showcode_bbc_thumbnail'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_showcode_bbc_medium" ' . ($gallerySettings['gallery_set_showcode_bbc_medium'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showcode_bbc_medium'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_showcode_directlink" ' . ($modSettings['gallery_set_showcode_directlink'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showcode_directlink'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_showcode_htmllink" ' . ($modSettings['gallery_set_showcode_htmllink'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showcode_htmllink'] . '</td></tr>
				';

				if (!is_writable($modSettings['gallery_path']))
					echo '<tr class="windowbg"><td colspan="2"><font color="#FF0000"><b>' . $txt['gallery_write_error']  . $modSettings['gallery_path'] . '</b></font></td></tr>';

				echo '

				<tr><td colspan="2"><input type="submit" name="savesettings" value="' . $txt['gallery_save_settings'] . '" /></td></tr>
				</table>
				</div>
			</form>
					<script language="JavaScript" type="text/javascript" src="https://www.smfhacks.com/versions/gallery_version2.js?t=' . time() . '"></script>
			<script language="JavaScript" type="text/javascript">

			function GalleryCurrentVersion()
			{
				if (!window.galleryVersion)
					return;

				galleryspan = document.getElementById("lastgallery");

				if (window.galleryVersion != "' . $currentVersion . '")
				{
					setInnerHTML(galleryspan, "<b><font color=\"red\">" + window.galleryVersion + "</font>&nbsp;' . $txt['gallery_txt_version_outofdate'] . '</b>");
				}
				else
				{
					setInnerHTML(galleryspan, "' . $currentVersion . '")
				}
			}

			document.addEventListener(\'DOMContentLoaded\', function(event) {
				GalleryCurrentVersion();
			});

			</script>
			<br />
			<b>' . $txt['gallery_text_permissions'] . '</b><br/><span class="smalltext">' . $txt['gallery_set_permissionnotice'] . '</span>
			<br /><a href="' . $scripturl . '?action=admin;area=permissions">' . $txt['gallery_set_editpermissions']  . '</a>

';

}

function template_approvelist()
{
	global $scripturl, $modSettings, $txt, $context;

	ModerationAdminTabs();

	echo '
	<div id="moderationbuttons" class="margintop">
		', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
	</div><br /><br /><br />';


echo '
<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_form_approveimages'], '
		</h3>
  </div>
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="windowbg2">
			<td>
			<form method="post" action="', $scripturl, '?action=gallery;sa=bulkactions">
			<table cellspacing="0" cellpadding="10" border="0" class="table_grid" align="center" width="90%">
			<thead>
			<tr class="title_bar">
				<th class="lefttext first_th"><input type="checkbox" id="checkall" class="check" onclick="invertAll(this, this.form);"  /></th>
				<th class="lefttext">', $txt['gallery_app_image'], '</th>
				<th class="lefttext">', $txt['gallery_text_category'], '</th>
				<th class="lefttext">', $txt['gallery_app_title'], '</th>
				<th class="lefttext">', $txt['gallery_app_description'], '</th>
				<th class="lefttext">', $txt['gallery_app_date'], '</th>
				<th class="lefttext">', $txt['gallery_app_membername'], '</th>
				<th class="lefttext last_th">', $txt['gallery_text_options'], '</th>
				</tr>
				</thead>
				';


		$styleClass = 'windowbg2';
			foreach($context['gallery_pic_approvallist'] as $row)
			{
				echo '<tr class="' . $styleClass  . '">';
				echo '<td><input type="checkbox" name="pics[]" value="',$row['id_picture'],'" /></td>';

				echo '<td><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '"><img src="' . $modSettings['gallery_url'] . $row['thumbfilename']  . '" alt="" /></a></td>';
				echo '<td>' . (empty($row['catname']) ? $row['catname2'] : $row['catname']) . '</td>';
				echo '<td>' . $row['title'] . '</td>';
				echo '<td>' . $row['description'] . '</td>';
				echo '<td><span class="smalltext">' . timeformat($row['date']) . '</span></td>';
				if ($row['real_name'] != '')
					echo '<td><a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a></td>';
				else
					echo '<td>',$txt['gallery_guest'],'</td>';

				echo '<td><a href="' . $scripturl . '?action=gallery;sa=approve;id=' . $row['id_picture'] . '">' . $txt['gallery_text_approve']  . '</a><br /><a href="' . $scripturl . '?action=gallery;sa=edit;id=' . $row['id_picture'] . '">' . $txt['gallery_text_edit'] . '</a><br /><a href="' . $scripturl . '?action=gallery;sa=delete;id=' . $row['id_picture'] . '">' . $txt['gallery_text_delete'] . '</a></td>';
				echo '</tr>';

				if ($styleClass == 'windowbg')
					$styleClass = 'windowbg2';
				else
					$styleClass = 'windowbg2';

			}



		echo '<tr class="titlebg">
				<td align="left" colspan="8">
				';


				echo $context['page_index'];

			echo '<br /><br /><b>',$txt['gallery_text_withselected'],'</b>

			<select name="doaction">
			<option value="approve">',$txt['gallery_form_approveimages'],'</option>
			<option value="delete">',$txt['gallery_form_delpicture'],'</option>
			</select>
			<input type="submit" value="',$txt['gallery_text_performaction'],'" />
			</form>

				</td>
			</tr>
			</table>

			<br />
<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_form_reportimages'], '
		</h3>
  </div>

			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
				<thead>
			<tr class="title_bar">
				<th class="lefttext first_th">' . $txt['gallery_rep_piclink'] . '</th>
				<th class="lefttext">' . $txt['gallery_rep_comment']  . '</th>
				<th class="lefttext">' . $txt['gallery_app_date'] . '</th>
				<th class="lefttext">' . $txt['gallery_rep_reportby'] . '</th>
				<th class="lefttext last_th">' . $txt['gallery_text_options'] . '</th>
				</tr>
				</thead>
				';

			// List all reported pictures
			 $styleClass = 'windowbg2';
			foreach($context['gallery_report_piclist'] as $row)
			{

				echo '<tr class="' . $styleClass  . '">';
				echo '<td><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '">' . $txt['gallery_rep_viewpic'] .'</a></td>';
				echo '<td>' . $row['comment'] . '</td>';
				echo '<td><span class="smalltext">' . timeformat($row['date']) . '</span></td>';

				if ($row['real_name'] != '')
					echo '<td><a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a></td>';
				else
					echo '<td>',$txt['gallery_guest'],'</td>';

				echo '<td><a href="' . $scripturl . '?action=gallery;sa=delete;id=' . $row['id_picture'] . '">' . $txt['gallery_rep_deletepic']  . '</a>';
				echo '<br /><a href="' . $scripturl . '?action=gallery;sa=deletereport;id=' . $row['id'] . '">' . $txt['gallery_rep_delete'] . '</a></td>';
				echo '</tr>';

				if ($styleClass == 'windowbg')
					$styleClass = 'windowbg2';
				else
					$styleClass = 'windowbg2';

			}

echo '
			</table>
			</td>
		</tr>
</table>';


}

function template_comment_list()
{
	global $scripturl, $txt, $context;

	ModerationAdminTabs();

	echo '
	<div id="moderationbuttons" class="margintop">
		', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
	</div><br /><br /><br />';


echo '
<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_form_approvecomments'], '
		</h3>
  </div>
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="windowbg2">
			<td>
			<b>' . $txt['gallery_form_approvecomments'] . '</b><br />
			<form method="post" action="' . $scripturl . '?action=gallery;sa=apprcomall">
			<input type="submit" value="' . $txt['gallery_form_approveallcomments'] . '" />
			</form>
			<br />
			<form method="post" action="' . $scripturl . '?action=admin;area=gallery;sa=commentlist">
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
			<thead>
			<tr class="title_bar">
				<th class="lefttext first_th"><input type="checkbox" id="checkall" class="check" onclick="invertAll(this, this.form);"  /></th>
				<th class="lefttext">' . $txt['gallery_rep_piclink'] . '</th>
				<th class="lefttext">' . $txt['gallery_rep_comment']  . '</th>
				<th class="lefttext">' . $txt['gallery_app_date'] . '</th>
				<th class="lefttext">' . $txt['gallery_app_membername'] . '</th>
				<th class="lefttext last_th">' . $txt['gallery_text_options'] . '</th>
				</tr>
				</thead>
				';

			$styleClass = 'windowbg2';
			foreach($context['gallery_commnets_approvallist'] as $row)
			{

				echo '<tr class="' . $styleClass  . '">';
				echo '<td><input type="checkbox" name="comments[]" value="',$row['id_comment'],'" /></td>';
				echo '<td><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '">' . $txt['gallery_rep_viewpic'] .'</a></td>';
				echo '<td>' . $row['comment'] . '</td>';
				echo '<td>' . timeformat($row['date']) . '</td>';
				if ($row['real_name'] != '')
					echo '<td><a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a></td>';
				else
					echo '<td>'  . $txt['gallery_guest']. '</td>';
				echo '<td><a href="' . $scripturl . '?action=gallery;sa=apprcomment;id=' . $row['id_comment'] . '">' . $txt['gallery_text_approve']  . '</a>';
				echo '<br /><br /><a href="' . $scripturl . '?action=gallery;sa=delcomment;id=' . $row['id_comment'] . '">' . $txt['gallery_text_delcomment'] . '</a></td>';
				echo '</tr>';

				if ($styleClass == 'windowbg')
					$styleClass = 'windowbg2';
				else
					$styleClass = 'windowbg2';

			}

			echo '<tr class="titlebg">
					<td align="left" colspan="6">
					';

					echo $context['page_index'];

					echo '<br /><br /><b>',$txt['gallery_text_withselected'],'</b>

			<select name="doaction">
			<option value="approve">', $txt['gallery_text_approve2'],'</option>
			<option value="delete">',$txt['gallery_text_delete2'],'</option>
			</select>
			<input type="submit" value="',$txt['gallery_text_performaction'],'" />
			</form>

					</td>
				</tr>';


		echo '
			</table>

			<br />';

			echo '<b>' . $txt['gallery_form_reportedcomments'] . '</b><br />
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
				<thead>
			<tr class="title_bar">
				<th class="lefttext first_th">' . $txt['gallery_rep_piclink'] . '</th>
				<th class="lefttext">' . $txt['gallery_rep_org_comment']  . '</th>
				<th class="lefttext">' . $txt['gallery_rep_comment']  . '</th>
				<th class="lefttext">' . $txt['gallery_app_date'] . '</th>
				<th class="lefttext">' . $txt['gallery_rep_reportby'] . '</th>
				<th class="lefttext last_th">' . $txt['gallery_text_options'] . '</th>
				</tr>
				</thead>
				';

			// List all reported comments
			$styleClass = 'windowbg2';
			foreach($context['gallery_reported_commentlist'] as $row)
			{

				echo '<tr class="' . $styleClass  . '">';
				echo '<td><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '#c' . $row['id_comment'] . '">' . $txt['gallery_rep_viewpic'] .'</a></td>';
				echo '<td>' . $row['OringalComment'] . '</td>';
				echo '<td>' . $row['comment'] . '</td>';
				echo '<td>' . timeformat($row['date']) . '</td>';
				if (!empty($row['real_name']))
					echo '<td><a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a></td>';
				else
					echo '<td>'  . $txt['gallery_guest']. '</td>';

				echo '<td><a href="' . $scripturl . '?action=gallery;sa=deletecomment;id=' . $row['id_comment'] . ';ret=admin">' . $txt['gallery_text_delcomment'] . '</a>
				<br /><a href="' . $scripturl . '?action=gallery;sa=delcomreport;id=' . $row['id'] . '">' . $txt['gallery_rep_delete'] . '</a>
				</td>';
				echo '</tr>';

				if ($styleClass == 'windowbg')
					$styleClass = 'windowbg2';
				else
					$styleClass = 'windowbg2';

			}


echo '
			</table>
			</td>
		</tr>
</table>';

}

function template_search()
{
	global $scripturl, $txt, $context, $settings, $gallerySettings;

	ShowTopGalleryBar();

	echo '
<form method="post" action="' . $scripturl . '?action=gallery;sa=search2">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_search_pic'], '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tborder" align="center">
  <tr class="windowbg2">
	<td width="50%" align="right"><b>' . $txt['gallery_search_for'] . '</b>&nbsp;</td>
	<td width="50%"><input type="text" name="searchfor" size= "50" />
	</td>
  </tr>
  <tr class="windowbg2" align="center">
	<td colspan="2"><input type="checkbox" name="searchtitle" checked="checked" />' . $txt['gallery_search_title'] . '
	&nbsp;<input type="checkbox" name="searchdescription" checked="checked" />' . $txt['gallery_search_description'] . '&nbsp;
	<input type="checkbox" name="searchkeywords" />' . $txt['gallery_search_keyword'] . '<br />
	 <input type="checkbox" name="searchexifmake" />' . $txt['gallery_txt_searchexifmake'] . '<input type="checkbox" name="searchexifmodel" />' . $txt['gallery_txt_searchexifmodel']

	 . '</td>
  </tr>';

	/*. '
	&nbsp;<input type="checkbox" name="searchcustom" checked="checked" />' . $txt['gallery_txt_search_custom_fields'];
*/

	if ($gallerySettings['enable_exif_on_display'])
	{
		//echo '&nbsp;<input type="checkbox" name="searchexif" checked="checked" />' . $txt['gallery_txt_search_exif'];
	}



	if (count($context['gallery_cat_cusfields']) > 0)
	{
		echo '  <tr class="windowbg2">
  	<td align="right"><b>',$txt['gallery_custom_fields'], '</b></td>';
  	echo '<td><select name="customfields[]" multiple="multiple">
  	<option value="0">' . $txt['gallery_text_catnone'] . '</option>
	  ';

	  foreach($context['gallery_cat_cusfields'] as $custom)
	  {
	  	echo '<option value="' . $custom['ID_CUSTOM'] . '">' . $custom['title'] . '</option>';
	  }


	  echo '
	  </select></td></tr>';


	}


	echo '
  <tr class="windowbg2">
	<td colspan="2" align="center">
	<hr />
	<b>',$txt['gallery_search_advsearch'],'</b><br />
	<hr />
	</td>
  </tr>
	<tr class="windowbg2">
	<td width="30%" align="right">' . $txt['gallery_text_category'] . '&nbsp;</td>
	<td width="70%">
		<select name="cat">
	<option value="0">' . $txt['gallery_text_catnone'] . '</option>
	';

	foreach ($context['gallery_cat'] as $i => $category)
		echo '<option value="' . $category['id_cat']  . '">' . $category['title'] . '</option>';


	echo '</select></td>
	</tr>
	<tr class="windowbg2">
	<td width="30%" align="right">' . $txt['gallery_search_daterange']. '&nbsp;</td>
	<td width="70%">
		<select name="daterange">
	<option value="0">' . $txt['gallery_search_alltime']  . '</option>
	<option value="7">' . $txt['gallery_search_days7']   . '</option>
	<option value="30">' . $txt['gallery_search_days30']  . '</option>
	<option value="60">' . $txt['gallery_search_days60']  . '</option>
	<option value="90">' . $txt['gallery_search_days90']  . '</option>
	<option value="180">' . $txt['gallery_search_days180']  . '</option>
	<option value="365">' . $txt['gallery_search_days365']  . '</option>
</select></td>
	</tr>
	<tr class="windowbg2">
	<td width="30%" align="right">' . $txt['gallery_search_membername']. '&nbsp;</td>
	<td width="70%">
		<input type="text" name="pic_postername" id="pic_postername" value="" />
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/members.png" alt="', $txt['find_members'], '" /></a>
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);">', $txt['find_members'], '</a>
	  </td>
	</tr>


	<tr class="windowbg2">
		<td width="30%" align="right">' .$txt['gallery_search_txt_mediatype']. '&nbsp;</td>
		<td width="70%"><select name="mediatype">
		<option value="all">' . $txt['gallery_search_txt_all']  . '</option>
		<option value="pics">' . $txt['gallery_search_txt_onlypictures']  . '</option>
		<option value="videos">' . $txt['gallery_search_txt_onlyvideo']  . '</option>
		</select>
	  </td>
	</tr>
	<tr class="windowbg2">
		<td width="30%" align="right">' .$txt['gallery_search_txt_gallerytype']. '&nbsp;</td>
		<td width="70%"><select name="gallerytype">
		<option value="all">' . $txt['gallery_search_txt_all']  . '</option>
		<option value="main">' . $txt['gallery_search_gallery_onlymaincategories']   . '</option>
		<option value="user">' . $txt['gallery_search_gallery_onlusergalleries']  . '</option>
		</select>
	  </td>
	</tr>

		<tr class="windowbg2">
		<td width="30%" align="right">', $txt['gallery_txt_sortby'],'</td>
		<td width="70%"><select name="sortby">
	<option value="date">',$txt['gallery_txt_sort_date'],'</option>
	<option value="title">',$txt['gallery_txt_sort_title'],'</option>
	<option value="mostview">',$txt['gallery_txt_sort_mostviewed'],'</option>
	<option value="mostcom">',$txt['gallery_txt_sort_mostcomments'],'</option>
	<option value="mostrated">',$txt['gallery_txt_sort_mostrated'],'</option>
	</select>
	</td>
	</tr>
		<tr class="windowbg2">
		<td width="30%" align="right">',$txt['gallery_txt_orderby'],'</td>
		<td width="70%"><select name="orderby">
	<option value="desc">',$txt['gallery_txt_sort_desc'],'</option>
	<option value="asc">',$txt['gallery_txt_sort_asc'],'</option>
	</select>
	</td>
	</tr>



  <tr class="windowbg2">
	<td width="100%" colspan="2" align="center"><br />
	<input type="submit" value="' . $txt['gallery_search'] . '" name="submit" />
	<br /></td>
  </tr>
</table>
</form>
<br />';

        	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="' . $scripturl . '?action=gallery">' . $txt['gallery_text_returngallery'] . '</a>
            </div>
        </div>';



}

function template_search_results()
{
	global $context, $user_info, $modSettings, $scripturl, $txt;

	// Get the permissions for the user
	$g_add = allowedTo('smfgallery_add');
	$g_manage = allowedTo('smfgallery_manage');
	$g_edit_own = allowedTo('smfgallery_edit');
	$g_delete_own = allowedTo('smfgallery_delete');


	ShowTopGalleryBar();

	$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];
	echo '<br />
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_searchresults'], '
		</h3>
  </div>
  <form method="post" action="', $scripturl, '?action=gallery&sa=search2">
	<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">
	<tr class="windowbg2">
	<td align="right" colspan="', $maxrowlevel, '">';

	echo $txt['gallery_txt_perpage'],'
	<select name="perpage">
	<option value="',$modSettings['orignal_set_images_per_page'],'"' . ( $modSettings['orignal_set_images_per_page'] == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'],'</option>
	<option value="',$modSettings['orignal_set_images_per_page'] * 2,'"' . ( $modSettings['orignal_set_images_per_page'] * 2  == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'] * 2,'</option>
	<option value="',$modSettings['orignal_set_images_per_page'] * 3,'"' . ( $modSettings['orignal_set_images_per_page'] * 3 == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'] * 3,'</option>
	</select> ';


	echo $txt['gallery_txt_sortby'],'
	<select name="sortby">
	<option value="date"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'date' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_date'],'</option>
	<option value="title"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'title' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_title'],'</option>
	<option value="mostview"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'mostview' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_mostviewed'],'</option>
	<option value="mostcom"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'mostcom' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_mostcomments'],'</option>
	<option value="mostrated"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'mostrated' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_mostrated'],'</option>
	</select>

	',$txt['gallery_txt_orderby'],'
	<select name="orderby">
	<option value="desc"' . (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'desc' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_desc'],'</option>
	<option value="asc"' . (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'asc' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_asc'],'</option>
	</select>
	<input type="hidden" name="q" value="' . $context['gallery_search_query_encoded'] . '" />
	<input type="submit" value="',$txt['gallery_txt_sort_go'] ,'" />
	<input type="hidden" name="start" value="',$context['start'],'" />
	</td>
	</tr>';



	$rowlevel = 0;

	foreach($context['gallery_search_results'] as $row)
	{
		if ($row['mature'] == 1)
			{
				if (CanViewMature() == false)
					$row['thumbfilename'] = 'mature.gif';
			}

		if ($rowlevel == 0)
			echo '<tr class="windowbg2">';

			$setupRow = array(
			'showhighslide' => 0,
			'showunreadicon' => 1,
			'g_manage' => $g_manage,
			'g_edit_own' => $g_edit_own,
			'g_delete_own' => $g_delete_own
			);

			ShowImageItem($row,$setupRow);


		if ($rowlevel < ($maxrowlevel-1))
			$rowlevel++;
		else
		{
			echo '</tr>';
			$rowlevel = 0;
		}
	}

	if ($rowlevel !=0)
	{
		if (($maxrowlevel - $rowlevel) > 0)
				echo '<td colspan="' .($maxrowlevel - $rowlevel) .'"></td>';

		echo '</tr>';
	}


		echo '<tr class="catbg2">
				<td align="left" colspan="' . $maxrowlevel . '">
				';

				echo $context['page_index'];

		echo '
				</td>
			</tr>';


	// Show return to gallery link and Show add picture if they can
	echo '

		</table></form><br />';


 	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';
			if ($g_add)
			echo '<a href="' . $scripturl . '?action=gallery;sa=add">' . $txt['gallery_text_addpicture'] . '</a>&nbsp; - &nbsp;';

			echo '
			<a href="' . $scripturl . '?action=gallery">' . $txt['gallery_text_returngallery'] . '</a>
            </div>
        </div>';

}

function template_myimages()
{
	global $context, $user_info, $modSettings, $scripturl, $txt, $gallerySettings;

	// Get the permissions for the user
	$g_add = allowedTo('smfgallery_add');
	$g_manage = allowedTo('smfgallery_manage');
	$g_edit_own = allowedTo('smfgallery_edit');
	$g_delete_own = allowedTo('smfgallery_delete');


	ShowTopGalleryBar();

	// Get userid
	$userid = $context['gallery_userid'];
	if ($user_info['id']== $userid)
	{
		$context['gallery']['buttons_set']['mywatchlist'] =  array(
								'text' => 'gallery_txt_mywatchlist',
								'url' => $scripturl . '?action=gallery;sa=mywatchlist',
								'lang' => true,
								'is_selected' => true,
						);

		$context['gallery']['buttons_set']['whowatchme'] =  array(
								'text' => 'gallery_txt_who_watch_me',
								'url' => $scripturl . '?action=gallery;sa=whowatchme',
								'lang' => true,
								'is_selected' => true,
						);


		echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="90%">
							<tr>
								<td style="padding-right: 1ex;" align="right">
								<table cellpadding="0" cellspacing="0" align="left">
										<tr>
										<td align="right">
							', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
								</td>
							</tr>
								</table>
					</td>
							</tr>
						</table>';
	}

	$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];


if (!empty($userid))
{
	loadMemberData($userid);
	loadMemberContext($userid);


	$context['allow_hide_email'] = !empty($modSettings['allow_hideEmail']) || ($user_info['is_guest'] && !empty($modSettings['guest_hideContacts']));
	$common_permissions = array('can_send_pm' => 'pm_send');

	foreach ($common_permissions as $contextual => $perm)
			$context[$contextual] = allowedTo($perm);

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $context['gallery_usergallery_name'], '
		</h3>
  </div>
  <table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">
  <tr class="windowbg2">
	<td valign="top">';

	 ShowUserBox($userid);

	echo '
	</td>
	<td valign="top">
		<table>
			<tr>
				<td>' .$txt['gallery_stats_totalpics']  . '</td>
				<td>' . number_format($context['user_total_pics']) . '</td>
			</tr>
			<tr>
				<td>' . $txt['gallery_stats_totalcomments'] . '</td>
				<td>' . number_format($context['user_total_comments']) . '</td>
			</tr>
			<tr>
				<td>' . $txt['gallery_stats_totalviews'] . '</td>
				<td>' . number_format($context['user_total_views']) . '</td>
			</tr>
			 <tr>
				<td>' . $txt['gallery_txt_total_photo_comments'] . '</td>
				<td>' . number_format($context['user_total_photo_comments']) . '</td>
			</tr>
			<tr>
				<td>' . $txt['gallery_txt_total_votes_comments'] . '</td>
				<td>' . number_format($context['user_total_photo_votes']) . '</td>
			</tr>
		</table>
	</td>

  </tr>
  </table><br />';
}

  echo '
	  <div class="cat_bar">
		<h3 class="catbg centertext">
		&nbsp;
		</h3>
  </div>
	<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">
';

	$rowlevel = 0;
	// Get userid


	// Show page listing
	if ($context['gallery_myimages_count'] > 0)
	{
		echo '<tr class="catbg2">
				<td align="left" colspan="' . $maxrowlevel . '">
				';

				echo $context['page_index'];

		echo '
				</td>
			</tr>';
	}

	// Check if it is the user ids gallery mainly to show unapproved pictures or not

	foreach($context['gallery_my_images_result'] as $row)
	{
		if ($rowlevel == 0)
			echo '<tr class="windowbg2">';

			$setupRow = array(
			'showhighslide' => 0,
			'approvaluserid' => $userid,
			'g_manage' => $g_manage,
			'g_edit_own' => $g_edit_own,
			'g_delete_own' => $g_delete_own
			);

			ShowImageItem($row,$setupRow);


		if ($rowlevel < ($maxrowlevel-1))
			$rowlevel++;
		else
		{
			echo '</tr>';
			$rowlevel = 0;
		}
	}

	if ($rowlevel !=0)
	{
		if (($maxrowlevel - $rowlevel) > 0)
				echo '<td colspan="' .($maxrowlevel - $rowlevel) .'"></td>';
		echo '</tr>';
	}


	// Show page listing
	if ($context['gallery_myimages_count'] > 0)
	{
		echo '<tr class="catbg2">
				<td align="left" colspan="' . $maxrowlevel . '">
				';

				echo $context['page_index'];

		echo '
				</td>
			</tr>';
	}



	echo '</table><br />';


 	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

	// If allowed to have a personal gallery images
	if (allowedTo('smfgallery_usergallery'))
		echo '
		<a href="', $scripturl, '?action=gallery;sa=add;u=', $user_info['id'], '">', $txt['gallery_text_adduserpicture'], '</a>&nbsp; - &nbsp; ';


	if ($g_add)
		echo '
		<a href="' . $scripturl . '?action=gallery;sa=add">' . $txt['gallery_text_addpicture'] . '</a>&nbsp; - &nbsp; ';

  // Check if allowed to add video
  if (allowedTo('smfgalleryvideo_add'))
		echo '<a href="' . $scripturl . '?action=gallery;sa=addvideo">' . $txt['gallery_form_addvideo'] .'</a>&nbsp; - &nbsp; ';

	echo '
		<a href="', $scripturl, '?action=gallery">', $txt['gallery_text_returngallery'], '</a>
            </div>
        </div>';

}

function template_edit_comment()
{
	global $context, $scripturl, $txt, $settings, $modSettings;

	ShowTopGalleryBar();

	echo '
<form method="post" name="cprofile" id="cprofile" action="', $scripturl, '?action=gallery;sa=editcomment2" onsubmit="submitonce(this);">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_editcomment'] , '
		</h3>
  </div>
<table border="0" class="bordercolor" cellpadding="3" cellspacing="1" width="100%">
	<tr class="windowbg2">
		<td colspan="2" align="center">
			<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_comment']['id_picture'] . '" target="blank"><img src="' . $modSettings['gallery_url'] . $context['gallery_pic_thumbfilename'] . '" alt="" /></a>
		</td>
	</tr>';

	if (!function_exists('getLanguages'))
	{
	// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'bbc'), '
									</td>
								</tr>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']))
			echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'smileys'), '
									</td>
								</tr>';

		// Show BBC buttons, smileys and textbox.
		echo '
								<tr class="windowbg2">

									<td colspan="2" align="center">
										', template_control_richedit($context['post_box_name'], 'message'), '
									</td>
								</tr>';

	}
	else
	{
		echo '
								<tr class="windowbg2">
		<td colspan="2">';
			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Show BBC buttons, smileys and textbox.
		echo '
					', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');


		echo '</td></tr>';
	}

	echo '
	<tr class="windowbg2">
		<td width="28%" colspan="2" align="center">
			<input type="hidden" name="id" value="' . $context['gallery_comment']['id_comment'] . '" />';

	// Check if comments are autoapproved
	if (allowedTo('smfgallery_autocomment') == false)
			echo $txt['gallery_text_commentwait'] . '<br />';

	if ($context['show_spellchecking'])
		echo '
			<input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'cprofile\', \'comment\');" />';

	echo '
			<input type="submit" value="' . $txt['gallery_text_editcomment'] . '" name="submit" />
		</td>
	</tr>
</table>
</form>';

}

function template_view_rating()
{
	global $settings, $scripturl, $txt, $context;

	ShowTopGalleryBar();

	echo '
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_form_viewratings'], '
		</h3>
  </div>
	<table cellspacing="0" cellpadding="5" border="0" align="center" width="100%" class="tborder">
				<tr class="titlebg">
					<td align="center">' . $txt['gallery_app_membername'] . '</td>
					<td align="center">' . $txt['gallery_text_rating'] . '</td>
					<td align="center">' . $txt['gallery_text_options'] . '</td>
				</tr>';

	foreach($context['gallery_pic_ratings'] as $row)
	{
		echo '<tr class="windowbg2">
				<td align="center"><a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a></td>
				<td align="center">';
		// Show the star images
		for($i=0; $i < $row['value']; $i++)
			echo '<img src="', $settings['images_url'], '/membericons/icon.png" alt="*" border="0" />';

		echo '</td>
			  <td align="center"><a href="' . $scripturl . '?action=gallery;sa=delrating;id=' . $row['id'] . '">'  . $txt['gallery_text_delete'] . '</a></td>
			  </tr>';
	}
	echo '
	</table>';



			     	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic_id'] . '">' . $txt['gallery_text_returnpicture'] . '</a>
            </div>
        </div>';


}

function template_stats()
{
	global $settings, $context, $txt, $scripturl;

	ShowTopGalleryBar();

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_stats_title'], '
		</h3>
  </div>
<table border="0" cellpadding="1" cellspacing="0" width="100%" align="center" class="tborder">
			<tr class="windowbg2">
				<td width="50%" valign="top">
						<dl class="stats">
						<dt>' . $txt['gallery_stats_totalpics'] . '</dt>
						<dd>' . comma_format($context['total_pictures']) . '</dd>
						<dt>' . $txt['gallery_stats_totalviews'] . '</dt>
						<dd>' . comma_format($context['total_views']) . '</dd>
						<dt>' . $txt['gallery_stats_totalcomments'] .'</dt>
						<dd>' .  comma_format($context['total_comments']). '</dd>
						<dt> '. $txt['gallery_stats_totalvotes'] . '</dt>
						<dd>' . comma_format($context['total_votes']). '</dd>
						</dl>

				</td>
				<td width="50%" valign="top">
				
				<dl class="stats">
				<dt>' . $txt['gallery_stats_avgpicvideosday'] . '</dt>
				<dd>' . comma_format($context['total_avgpicvideosday']). '</dd>
				<dt>' . $txt['gallery_stats_avgcommentsperday'] . '</dt>
				<dd>' . comma_format($context['total_avgcommentsperday']). '</dd>
				<dt>' . $txt['gallery_stats_totalcategories']  .'</dt>
				<dd>' . comma_format($context['total_categories']) . '</dd>
				<dt>' . $txt['gallery_stats_totalfize']  .'</dt>
				<dd>' . $context['total_filesize'] . '</dd>
				</dl>
				
			
				</td>
			</tr>
			<tr>
				<td width="50%">
					<div class="cat_bar">
					  <h3 class="catbg centertext">
				', $txt['gallery_stats_viewed'], ' <a href="',$scripturl,'?action=gallery;sa=listall;type=views">',$txt['gallery_stats_listall'],'</a>
						</h3>
					</div>
				</td>
				<td width="50%"><div class="cat_bar">
					  <h3 class="catbg centertext">', $txt['gallery_stats_toprated'], ' <a href="',$scripturl,'?action=gallery;sa=listall;type=toprated">',$txt['gallery_stats_listall'],'</a>
					   </h3>
					</div>
					  </td>
			</tr>
			<tr class="windowbg2">
				<td width="50%" valign="top">';

				foreach ($context['top_viewed'] as $picture)
				{
					echo '<dt>',$picture['link'], '</dt>
					<dd class="statsbar generic_bar righttext">
									<div class="bar" style="width: ' . $picture['percent']  . '%;"></div>
									<span>' .$picture['views'] . '</span>
								</dd>';
				}


	echo '
				
				</td>
				<td width="50%" valign="top">';


				foreach ($context['top_rating'] as $picture)
				{
					echo '<dt>',$picture['link'], '</dt>
					<dd class="statsbar generic_bar righttext">
									<div class="bar" style="width: ' . $picture['percent']  . '%;"></div>
									<span>' .$picture['rating'] . '</span>
								</dd>';
				}



	echo '
				
				</td>
			</tr>
			<tr>
				<td width="50%"><div class="cat_bar">
					  <h3 class="catbg centertext">', $txt['gallery_stats_mostcomments'], ' <a href="',$scripturl,'?action=gallery;sa=listall;type=comments">',$txt['gallery_stats_listall'],'</a>
					  </h3>
					</div>
					  </td>
				<td width="50%"><div class="cat_bar">
					  <h3 class="catbg centertext">',$txt['gallery_stats_last'], ' <a href="',$scripturl,'?action=gallery;sa=listall;type=recent">',$txt['gallery_stats_listall'],'</a>
					  </h3>
					</div>
					  </td>
			</tr>
			<tr class="windowbg2">
				<td width="50%" valign="top">';


				foreach ($context['most_comments']  as $picture)
				{
					echo '<dt>',$picture['link'], '</dt>
					<dd class="statsbar generic_bar righttext">
									<div class="bar" style="width: ' . $picture['percent']  . '%;"></div>
									<span>' . $picture['commenttotal']  . '</span>
								</dd>';
				}


	echo '
				
				</td>
				<td width="50%" valign="top">
					<table border="0" cellpadding="1" cellspacing="0" width="100%">';
						foreach ($context['last_upload'] as $picture)
						{
							echo '<tr>
									<td width="100%" colspan="3" valign="top">', $picture['link'], '</td>
								</tr>';
						}
	echo '
					</table>
				</td>
			</tr>


<tr>
				<td width="50%"><div class="cat_bar">
					  <h3 class="catbg centertext">', $txt['gallery_stats_topposters'], ' </h3>
					</div></td>
				<td width="50%"><div class="cat_bar">
					  <h3 class="catbg centertext">', $txt['gallery_stats_topcommenters'], ' </h3>
					</div></td>
			</tr>
			<tr class="windowbg2">
				<td width="50%" valign="top">';

				foreach ($context['top_posters'] as $picture)
				{
					echo '<dt>',$picture['link'], '</dt>
					<dd class="statsbar generic_bar righttext">
									<div class="bar" style="width: ' . $picture['percent']  . '%;"></div>
									<span>' . $picture['total']  . '</span>
								</dd>';
				}



	echo '
					
				</td>
				<td width="50%" valign="top">';

				foreach ($context['top_commenters'] as $picture)
				{
					echo '<dt>',$picture['link'], '</dt>
					<dd class="statsbar generic_bar righttext">
									<div class="bar" style="width: ' . $picture['percent']  . '%;"></div>
									<span>' . $picture['total']  . '</span>
								</dd>';
				}

				echo '
					
				</td>
			</tr>

<tr>
				<td width="50%"><div class="cat_bar">
					  <h3 class="catbg centertext">', $txt['gallery_stats_topcategories'], ' </h3>
					</div></td>
				<td width="50%"><div class="cat_bar">
					  <h3 class="catbg centertext">', $txt['gallery_stats_topraters'], ' </h3>
					</div></td>
			</tr>
			<tr class="windowbg2">
				<td width="50%" valign="top">';


				foreach ($context['top_categories'] as $picture)
				{
					echo '<dt>',$picture['link'], '</dt>
					<dd class="statsbar generic_bar righttext">
									<div class="bar" style="width: ' . $picture['percent']  . '%;"></div>
									<span>' . $picture['total']  . '</span>
								</dd>';
				}


				echo '
					
				</td>
				<td width="50%" valign="top">';

				foreach ($context['top_raters'] as $picture)
				{
					echo '<dt>',$picture['link'], '</dt>
					<dd class="statsbar generic_bar righttext">
									<div class="bar" style="width: ' . $picture['percent']  . '%;"></div>
									<span>' . $picture['total']  . '</span>
								</dd>';
				}



				echo '
					
				</td>
			</tr>
		</table>';

        	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="' . $scripturl . '?action=gallery">' . $txt['gallery_text_returngallery'] . '</a>
            </div>
        </div>';

}

function template_import()
{
	global $scripturl, $context, $txt, $modSettings;

	ShowTopGalleryBar();

	$modSettings['gallery_path'] .= 'import/';

	echo '<div class="tborder">
		<form method="post" action="' . $scripturl . '?action=gallery;sa=import2">
		<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_import'] , '
		</h3>
  </div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		  <tr class="windowbg2">
			<td width="50%" align="right"><b>' . $txt['gallery_form_category']. '</b>&nbsp;</td>
			<td width="50%">' . $context['gallery_cat_name']  . '</td>
		</tr>
		  <tr class="windowbg2">
			<td width="50%" align="right"><b>' . $txt['gallery_import_folder']. '</b>&nbsp;</td>
			<td width="50%"><input type="text" name="importfolder" size="50"  value="' . $modSettings['gallery_path'] . '" /></td>
		</tr>
		  <tr class="windowbg2">
			<td width="28%" colspan="2" align="center">
			' . $txt['gallery_import_notes'] . '
			</td>
		   </tr>
		<tr class="windowbg2">
			<td colspan="2" align="center">
			<b>',$txt['gallery_set_maxheight'],'</b> ',$modSettings['gallery_max_height'],'<br />
			<b>',$txt['gallery_set_maxwidth'],'</b> ',$modSettings['gallery_max_width'],'<br />
			<b>',$txt['gallery_set_filesize'],'</b> ',$modSettings['gallery_max_filesize'],'<br />

			</td>
		</tr>
	  <tr class="windowbg2">
			 <td colspan="2" align="center"><input type="checkbox" name="deleteimport" />
			<b>' . $txt['gallery_import_deletefiles']. '</b>&nbsp;<br /></td>
		</tr>
		<tr class="windowbg2">
		   <td width="28%" colspan="2" align="center">
			<input type="hidden" value="' . $context['catid'] . '" name="catid" />
			<input type="submit" value="' . $txt['gallery_text_import'] . '" name="submit" /></td>
		  </tr>
		</table>
		</form>
		</div>';
}

function template_bulk()
{
	global $scripturl, $txt, $context, $gallerySettings, $cookiename, $modSettings;

	// How many upload fields to show
	$bulk_fields = $gallerySettings['gallery_bulkuploadfields'];

	ShowTopGalleryBar();


	echo '
<div class="tborder">';
	if (empty($gallerySettings['gallery_set_multifile_upload_for_bulk']))
		echo '<form method="post" enctype="multipart/form-data" action="' . $scripturl . '?action=gallery;sa=bulk2;catid=' . $context['catid'] . ';usercatid=' . $context['usercatid'] .  ';sesc=', $context['session_id'] . ';procookie=' . urlencode(base64_encode($_COOKIE[$cookiename])),  ($gallerySettings['gallery_set_multifile_upload_for_bulk'] ? ';ajax=1' : '') . '" id="form-demo">';

echo '<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_bulkadd'], '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr class="windowbg2">
		<td width="50%" align="right"><b>' . $txt['gallery_form_category']. '</b>&nbsp;</td>
		<td width="50%"><a href="' . $context['gallery_bulk_cat_link'] . '">' . $context['gallery_cat_name']  . '</a></td>
	</tr>
	<tr class="windowbg2">
		<td colspan="2"><hr /></td>
	</tr>';

	if ($context['quotalimit'] != 0)
	{
		echo '
	<tr class="windowbg2">
		<td align="right">',$txt['gallery_quotagrouplimit'],'&nbsp;</td>
		<td>', gallery_format_size($context['quotalimit'], 2), '</td>
	</tr>
	<tr class="windowbg2">
		<td align="right">',$txt['gallery_quotagspaceused'],'&nbsp;</td>
		<td>', gallery_format_size($context['userspace'], 2),  '</td>
	</tr>
	<tr class="windowbg2">
		<td align="right">',$txt['gallery_quotaspaceleft'],'&nbsp;</td>
		<td><b>' . gallery_format_size(($context['quotalimit']-$context['userspace']) , 2) . '</b></td>
	</tr>';
  }

/*
  if ($gallerySettings['gallery_set_multifile_upload_for_bulk'])
  {
  echo '<tr class="windowbg2">
  <td colspan="2" align="center">	<div class="container">
		<div>


	<fieldset id="demo-fallback">
		<legend>' . $txt['gallery_txt_multi_filedupload'] . '</legend>
		<p>
			' . $txt['gallery_txt_multi_upload_failed_load']  . '
		</p>
		<label for="demo-photoupload">
			' . $txt['gallery_txt_multi_uploadaphoto'] . '
			<input type="file" name="picture[]" />
		</label>
	</fieldset>

	<div id="demo-status" class="hide">
		<p>
			<a href="#" id="demo-browse">' . $txt['gallery_txt_multi_browsefiles'] . '</a> |
			<a href="#" id="demo-clear">' . $txt['gallery_txt_multi_clearlist']  . '</a> |
			<a href="#" id="demo-upload">' . $txt['gallery_txt_multi_startupload'] . '</a>
		</p>
		<div>
			<strong class="overall-title"></strong><br />
			<img src="' .$modSettings['gallery_url'] . 'fupload/assets/progress-bar/bar.gif" class="progress overall-progress" />
		</div>
		<div>
			<strong class="current-title"></strong><br />
			<img src="' .$modSettings['gallery_url'] . 'fupload/assets/progress-bar/bar.gif" class="progress current-progress" />
		</div>
		<div class="current-text"></div>
	</div>

	<ul id="demo-list"></ul>
	</div>


	</div>
	</td>
	</tr>';
}
*/

	if (empty($gallerySettings['gallery_set_multifile_upload_for_bulk']))
	{
		for ($i=0; $i < $bulk_fields; $i++)
		{
			echo '
		<tr class="windowbg2">
			<td align="right" width="50%"><b>' . $txt['gallery_form_title'] . '</b>&nbsp;<input type="text" name="title[]" /></td>
			<td width="50%"><b>' . $txt['gallery_form_description'] . '</b>&nbsp;<input type="text" name="description[]" /></td>
		</tr>
		<tr class="windowbg2">
			<td align="center" colspan="2"><b>' . $txt['gallery_form_uploadpic'] . '</b>&nbsp;<input type="file" size="48" name="picture[]" /></td>
		</tr>';

	if (empty($context['usercatid']))
	{
		if (!empty($context['gallery_addpic_customfields']))
		foreach($context['gallery_addpic_customfields'] as $row2)
		{
			echo '<tr class="windowbg2">
				<td align="right"><b>', $row2['title'], ($row2['is_required'] ? '<font color="#FF0000">*</font>' : ''), '</b></td>
				<td><input type="text" name="cus_', $row2['id_custom'],'[]" value="' , $row2['defaultvalue'], '" size="50" /></td>
			</tr>
		 ';
		}

	 }


			echo '
		<tr class="windowbg2">
			<td colspan="2"><hr /></td>
		</tr>';
		}
	}

	echo '
	<tr class="windowbg2">
		<td width="28%" colspan="2" align="center">';

		if (empty($gallerySettings['gallery_set_multifile_upload_for_bulk']))
		echo '
			<input type="submit" value="' . $txt['gallery_text_bulkadd'] . '" name="submit" /><br />';


	echo '
		</td>
	</tr>
</table>';


if ($gallerySettings['gallery_set_multifile_upload_for_bulk'])
{
	echo '
<div class="dz-error-message"><b><font color="red"> <span data-dz-errormessage id="bulkerror"></span></font></b></div>
<form method="post" class="dropzone" enctype="multipart/form-data" action="' . $scripturl . '?action=gallery;sa=bulk2;catid=' . $context['catid'] . ';usercatid=' . $context['usercatid'] . ';sesc=', $context['session_id'] . ';procookie=' . urlencode(base64_encode($_COOKIE[$cookiename])), ($gallerySettings['gallery_set_multifile_upload_for_bulk'] ? ';ajax=1' : '') . '" id="myAwesomeDropzone">';


	if (empty($context['usercatid']))
	{
		if (!empty($context['gallery_addpic_customfields']))
		{
			echo '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
			foreach ($context['gallery_addpic_customfields'] as $row2)
			{
				echo '<tr class="windowbg2">
				<td align="right"><b>', $row2['title'], ($row2['is_required'] ? '<font color="#FF0000">*</font>' : ''), '</b></td>
				<td><input type="text" name="cus_', $row2['id_custom'], '[]" value="', $row2['defaultvalue'], '" size="50" /></td>
			</tr>
		 ';
			}
			echo '</table>';

		}

	}
}

echo '
</form>';

if ($gallerySettings['gallery_set_multifile_upload_for_bulk'])
	echo '
<script>
// dropzone JavaScript in html
Dropzone.options.myAwesomeDropzone = {
     paramName: "picture",
   error: function(file, response) {
         data = JSON.parse(response);
    //   console.log(response); // "Sorry"
       document.getElementById("bulkerror").innerHTML = data.error;
   }
 };
</script>';
echo '
</div>';

	echo '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr class="windowbg2">
		<td colspan="2" align="center">';
			echo '<a href="' . $context['gallery_bulk_cat_link'] . '">' . $txt['gallery_text_returngallery']  . '</a>';

	echo '
		</td>
	</tr>
</table>';

}

function template_filespace()
{
	global $scripturl, $txt, $context;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_filespace'], '
		</h3>
  </div>
    <div class="information">
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">

		<tr>
			<td>
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%">
				<tr>
					<td colspan="3" align="center"><b>' .$txt['gallery_filespace_groupquota_title'] . '</b></td>
				</tr>
			<tr class="titlebg">
				<td>' . $txt['gallery_filespace_groupname'] . '</td>
				<td>' .$txt['gallery_filespace_limit']  . '</td>
				<td>' .  $txt['gallery_text_options']  . '</td>
				</tr>';

	// Show the member groups
	$styleClass = 'titlebg';
	foreach($context['gallery_file_membergroupslist'] as $row)
	{
		echo '<tr class="' . $styleClass  . '">';
		echo '<td>', $row['group_name'], '</td>';
		echo '<td>', gallery_format_size($row['totalfilesize'], 2), '</td>';
		echo '<td><a href="' . $scripturl . '?action=gallery;sa=deletequota;id=' . $row['id_group'] . '">' . $txt['gallery_text_delete'] . '</a></td>';
		echo '</tr>';

		if ($styleClass == 'windowbg')
		  $styleClass = 'titlebg';
		else
		  $styleClass = 'titlebg';
	}

	// Show Regular members
	$styleClass = 'windowbg';
	foreach($context['gallery_file_regmem'] as $row)
	{
		echo '<tr class="' . $styleClass  . '">';
		echo '<td>', $txt['membergroups_members'], '</td>';
		echo '<td>', gallery_format_size($row['totalfilesize'], 2), '</td>';
		echo '<td><a href="' . $scripturl . '?action=gallery;sa=deletequota;id=' . $row['id_group'] . '">' . $txt['gallery_text_delete'] . '</a></td>';
		echo '</tr>';

		if ($styleClass == 'windowbg')
		  $styleClass = 'titlebg';
		else
		  $styleClass = 'titlebg';

	}


	echo '
				<tr class="titlebg">
					<td colspan="3" align="center">
						<form method="post" action="' . $scripturl . '?action=gallery;sa=addquota">
						' . $txt['gallery_filespace_groupname']  . '&nbsp;<select name="groupname">
								<option value="0">', $txt['membergroups_members'], '</option>';
								foreach ($context['groups'] as $group)
									echo '<option value="', $group['id_group'], '">', $group['group_name'], '</option>';

							echo '</select><br />' . $txt['gallery_filespace_limit'] . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="filelimit" /> (bytes)
							<br /><br />
						<input type="submit" value="' . $txt['gallery_filespace_addquota'] . '" />
						</form>
					</td>
				</tr>

				</table>
			</td>
		</tr>
		<tr class="titlebg">
			<td>
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%"  class="table_grid">
				<tr class="titlebg">
				<td>' . $txt['gallery_app_membername'] . '</td>
				<td>' . $txt['gallery_text_options'] . '</td>
				<td>' . $txt['gallery_filespace_filesize']  . '</td>
				</tr>';


	$styleClass = 'titlebg';
	foreach($context['gallery_filespace_admin'] as $row)
	{
		echo '<tr class="' . $styleClass  . '">';
		echo '<td><a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a></td>';
		echo '<td><a href="' . $scripturl . '?action=admin;area=gallery;sa=filelist;id=' . $row['id_member'] . '">'  . $txt['gallery_filespace_list'] . '</a></td>';
		echo '<td>', gallery_format_size($row['totalfilesize'], 2),  '</td>';
		echo '</tr>';


		if ($styleClass == 'windowbg')
		  $styleClass = 'titlebg';
		else
		  $styleClass = 'titlebg';

	}



	echo '<tr class="titlebg">
				<td align="left" colspan="3">
				';



				echo $context['page_index'];

		echo '
				</td>
			</tr>';


	echo '
			<tr class="titlebg">
					<td align="left" colspan="3">
					<form method="post" action="' . $scripturl . '?action=gallery;sa=recountquota">
					<input type="submit" value="' . $txt['gallery_filespace_recount'] . '" />
					</form>
					</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</div>';
}

function template_filelist()
{
	global $scripturl, $txt, $context, $modSettings;

	echo '
  <div class="information">
<table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
	<tr class="titlebg">
		<td>' . $txt['gallery_filespace_list_title'] . ' - ' . $context['gallery_filelist_realname'] . '</td>
	</tr>
	<tr class="titlebg">
		<td>
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
				<tr class="catbg">
					<td>' . $txt['gallery_app_image'] . '</td>
					<td>' . $txt['gallery_filespace_filesize']  . '</td>
					<td>' . $txt['gallery_text_options'] . '</td>
				</tr>';


	$styleClass = 'titlebg';
	foreach($context['gallery_user_filelist'] as $row)
	{
		echo '<tr class="' . $styleClass  . '">
					<td><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '"><img src="' . $modSettings['gallery_url'] . $row['thumbfilename']  . '" alt="" /></a></td>
					<td>', gallery_format_size($row['filesize'], 2),  '</td>
					<td><a href="' . $scripturl . '?action=gallery;sa=delete;id=' . $row['id_picture'] . '">' . $txt['gallery_text_delete'] . '</a></td>
				</tr>';
				if ($styleClass == 'windowbg')
					$styleClass = 'titlebg';
				else
					$styleClass = 'titlebg';

	}


	echo '
				<tr class="titlebg">
					<td align="left" colspan="3">
						';


	echo $context['page_index'];

	echo '
					</td>
				</tr>
				<tr class="titlebg">
					<td align="center" colspan="3">
						<a href="' . $scripturl . '?action=admin;area=gallery;sa=filespace">' . $txt['gallery_filespace'] . '</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>';
}

function template_catpermlist()
{
	global $scripturl, $txt, $context;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_text_catpermlist'], '
		</h3>
  </div>
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="windowbg2">
			<td>
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
			<thead>
			<tr class="title_bar">
				<th class="lefttext first_th">' . $txt['gallery_filespace_groupname'] . '</th>
				<th class="lefttext">' . $txt['gallery_text_category']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_view']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_add']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_edit']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_delete']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_addcomment']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_addvideo']  . '</th>
				<th class="lefttext last_th">' .  $txt['gallery_text_options']  . '</th>
				</tr>
				</thead>
				';


// Show the member groups
	$styleClass = 'windowbg2';
	foreach($context['gallery_catperm_memlist'] as $row)
	{
		echo '<tr class="' . $styleClass  . '">';
		echo '<td>'  . $row['group_name'] . '</td>';
		echo '<td><a href="' . $scripturl . '?action=gallery;sa=catperm;cat=' . $row['id_cat'] . '">'  . $row['catname'] . '</a></td>';
		echo '<td>' . ($row['view'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['editpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['delpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addcomment'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addvideo'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';

		echo '<td>
		<a href="' . $scripturl . '?action=gallery;sa=catpermedit;id=' . $row['id'] . '">' . $txt['gallery_text_edit'] . '</a>

		<a href="' . $scripturl . '?action=gallery;sa=catpermdelete;id=' . $row['id'] . '">' . $txt['gallery_text_delete'] . '</a></td>';
		echo '</tr>';

		if ($styleClass == 'windowbg')
		  $styleClass = 'windowbg2';
		else
		  $styleClass = 'windowbg2';

	}


	// Show Regular members/guests
	foreach($context['gallery_catperm_memguests'] as $row)
	{

		echo '<tr class="' . $styleClass  . '">';
		if ($row['id_group'] == 0)
			echo '<td>'  . $txt['membergroups_members'] . '</td>';
		if ($row['id_group'] == -1)
			echo '<td>'  . $txt['membergroups_guests'] . '</td>';


		echo '<td><a href="' . $scripturl . '?action=gallery;sa=catperm;cat=' . $row['id_cat'] . '">'  . $row['catname'] . '</a></td>';
		echo '<td>' . ($row['view'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['editpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['delpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addcomment'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addvideo'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';

		echo '<td>
		<a href="' . $scripturl . '?action=gallery;sa=catpermedit;id=' . $row['id'] . '">' . $txt['gallery_text_edit'] . '</a>

		<a href="' . $scripturl . '?action=gallery;sa=catpermdelete;id=' . $row['id'] . '">' . $txt['gallery_text_delete'] . '</a></td>';
		echo '</tr>';

		if ($styleClass == 'windowbg')
		  $styleClass = 'windowbg2';
		else
		  $styleClass = 'windowbg2';

	}



	echo '
			</table>
		</td>
	</tr>
</table>';
}

function template_catperm()
{
	global $scripturl, $txt, $context;

	ShowTopGalleryBar();

	echo '
 <div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_catperm'] . ' - ' . $context['gallery_cat_name'] , '
		</h3>
  </div>
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="windowbg2">
		<td>
		<form method="post" action="' . $scripturl . '?action=gallery;sa=catperm2">
		<table align="center" class="tborder">
		<tr class="titlebg">
			<td colspan="2">'  . $txt['gallery_text_addperm'] . '</td>
		</tr>
			  <tr class="windowbg2">
				<td align="right"><b>' . $txt['gallery_filespace_groupname'] . '</b>&nbsp;</td>
				<td><select name="groupname">
								<option value="-1">' . $txt['membergroups_guests'] . '</option>
								<option value="0">' . $txt['membergroups_members'] . '</option>
								';

								foreach ($context['groups'] as $group)
									echo '<option value="', $group['id_group'], '">', $group['group_name'], '</option>';

							echo '</select>
				</td>
			  </tr>
			  <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="view" checked="checked" /></td>
				<td><b>' . $txt['gallery_perm_view'] .'</b></td>
			  </tr>
			  <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="add" checked="checked" /></td>
				<td><b>' . $txt['gallery_perm_add'] .'</b></td>
			  </tr>
			  <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="edit" checked="checked" /></td>
				<td><b>' . $txt['gallery_perm_edit'] .'</b></td>
			  </tr>
			  <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="delete" checked="checked" /></td>
				<td><b>' . $txt['gallery_perm_delete'] .'</b></td>
			  </tr>
			  <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="addcomment" checked="checked" /></td>
				<td><b>' . $txt['gallery_perm_addcomment'] .'</b></td>
			  </tr>
			 <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="addvideo" checked="checked" /></td>
				<td><b>' . $txt['gallery_perm_addvideo'] .'</b></td>
			  </tr>

			 <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="autoapprove" checked="checked" /></td>
				<td><b>' . $txt['gallery_perm_autoapprove'] .'</b></td>
			  </tr>

			 <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="viewimagedetail" checked="checked" /></td>
				<td><b>' . $txt['gallery_perm_viewimagedetail'] .'</b></td>
			  </tr>

			  <tr class="windowbg2">
				<td align="center" colspan="2">
				<input type="hidden" name="cat" value="' . $context['gallery_cat_id'] . '" />
				<input type="submit" value="' . $txt['gallery_text_addperm'] . '" /></td>

			  </tr>
		</table>
		</form>
		<br />
<form method="post" action="' . $scripturl . '?action=gallery;sa=catpermcopy">
		<table align="center" class="tborder">
		<tr class="titlebg">
			<td colspan="2">'  . $txt['gallery_text_copyperm'] . '</td>
		</tr>
			  <tr class="windowbg2">
			  	<td align="right"><b>' . $txt['gallery_text_copyfrom'] . '</b>&nbsp;</td>
			  	<td><select name="copycat">
			  					<option value=""></option>';

				foreach ($context['gallery_cat'] as $i => $category)
				{
					if ($context['gallery_cat'] == $category['id_cat'])
						continue;

					echo '<option value="' . $category['id_cat']  . '">' . $category['title'] . '</option>';

				}

				echo '</select>
				</td>
			  </tr>
			  <tr class="windowbg2">
			  	<td align="center" colspan="2">
			  	<input type="hidden" name="cat" value="' . $context['gallery_cat_id'] . '" />
			  	<input type="submit" value="' . $txt['gallery_text_copyperm'] . '" /></td>
			  </tr>
		</table>
		</form>
		</td>
		</tr>

			<tr class="windowbg2">
			<td>
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
			<tr class="title_bar">
				<th class="lefttext first_th">' . $txt['gallery_filespace_groupname'] . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_view']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_add']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_edit']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_delete']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_addcomment']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_addvideo']  . '</th>
				<th class="lefttext">' .  $txt['gallery_perm_autoapprove']  . '</th>
				<th class="lefttext">' . $txt['gallery_perm_viewimagedetail'] . '</th>
				<th class="lefttext last_th">' .  $txt['gallery_text_options']  . '</th>
				</tr>';

	// Show the member groups
	$styleClass = 'windowbg2';
	foreach($context['catperm_memgroups'] as $row)
	{
		echo '<tr class="' . $styleClass  . '">';
		echo '<td>', $row['group_name'], '</td>';
		echo '<td>' . ($row['view'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['editpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['delpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addcomment'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addvideo'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['autoapprove'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['viewimagedetail'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';

		echo '<td>
		<a href="' . $scripturl . '?action=gallery;sa=catpermedit;id=' . $row['id'] . '">' . $txt['gallery_text_edit'] . '</a>

		<a href="' . $scripturl . '?action=gallery;sa=catpermdelete;id=' . $row['id'] . '">' . $txt['gallery_text_delete'] . '</a></td>';
		echo '</tr>';

				if ($styleClass == 'windowbg')
					$styleClass = 'windowbg2';
				else
					$styleClass = 'windowbg2';

	}


	// Show Regular members/Guests
	foreach($context['catperm_memgroups_guests'] as $row)
	{
		echo '<tr class="' . $styleClass  . '">';
		if ($row['id_group'] == 0)
			echo '<td>'  . $txt['membergroups_members'] . '</td>';
		if ($row['id_group'] == -1)
			echo '<td>'  . $txt['membergroups_guests'] . '</td>';

		echo '<td>' . ($row['view'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['editpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['delpic'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addcomment'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['addvideo'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['autoapprove'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';
		echo '<td>' . ($row['viewimagedetail'] ? $txt['gallery_perm_allowed'] : $txt['gallery_perm_denied']) . '</td>';

		echo '<td>
		<a href="' . $scripturl . '?action=gallery;sa=catpermedit;id=' . $row['id'] . '">' . $txt['gallery_text_edit'] . '</a>

		<a href="' . $scripturl . '?action=gallery;sa=catpermdelete;id=' . $row['id'] . '">' . $txt['gallery_text_delete'] . '</a></td>';
		echo '</tr>';

				if ($styleClass == 'windowbg')
					$styleClass = 'windowbg2';
				else
					$styleClass = 'windowbg2';
	}


	echo '
				</table>
			</td>
		</tr>
</table>';
}

function template_bulk2()
{
	global $context, $txt, $user_info, $scripturl;

	ShowTopGalleryBar();

	echo '<div class="tborder">
		<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_bulk_results'] , '
		</h3>
  </div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		  ';

	// Show the files that failed
	if ($context['bulk_errors'] != '')
		echo '
		  <tr class="windowbg2">
			 <td align="center"><font color="#FF0000"><b>', $txt['gallery_bulk_fileserror'], '</b></font></td>
		</tr>
		  <tr class="windowbg2">
			 <td align="center">', $context['bulk_errors'], '</td>
		</tr>';

		// Show the files that were uploaded
	if ($context['bulk_good'] != '')
		echo '
			  <tr class="windowbg2">
				 <td align="center"><font color="#00FF00"><b>', $txt['gallery_bulk_filesuploaded'], '</b></font></td>
			</tr>
			  <tr class="windowbg2">
				 <td align="center">', $context['bulk_good'], '</td>
			</tr>';

	echo '
		  <tr class="windowbg2">
			<td align="center">
			  <br />';

	if (!empty($context['bulk_catid']))
		echo '<a href="' . $scripturl .'?action=gallery;cat=' . $context['bulk_catid'] . '">' . $txt['gallery_bulk_returntocat'] . '</a><br />';

	echo '
			<br />
			<a href="' . $scripturl .'?action=gallery;sa=myimages;u=' . $user_info['id'] . '">' . $txt['gallery_text_myimages'] . '</a>
		  </tr>
		</table>
		</div>';
}

function template_regenerate()
{
	global $scripturl, $context, $txt, $modSettings;

	ShowTopGalleryBar();

	echo '<div class="tborder">
		<form method="post" action="' . $scripturl . '?action=gallery;sa=regen2">
		 <div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_regeneratethumbnails2'] , '
		</h3>
  </div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		  <tr class="windowbg2">
			<td width="50%" align="right"><b>' . $txt['gallery_form_category']. '</b>&nbsp;</td>
			<td width="50%">' . $context['gallery_cat_name']  . '</td>
		</tr>
		  <tr class="windowbg2">
			<td width="28%" colspan="2">
			' . $txt['gallery_regen_notes'] . '
				</td>
		  </tr>
		<tr class="windowbg2">
			<td width="28%" colspan="2"  align="center">
			<b>',$txt['gallery_set_thumb_height'],'</b> ',$modSettings['gallery_thumb_height'],'<br />
			<b>',$txt['gallery_set_thumb_width'],'</b> ',$modSettings['gallery_thumb_width'],'<br />
			 <br />
			<hr />
			<input type="checkbox" name="regenmedium" />' . $txt['gallery_regenerate_medium']. '<br />
		   	<b>',$txt['gallery_medium_height'],'</b> ',$modSettings['gallery_medium_height'],'<br />
			<b>',$txt['gallery_medium_width'],'</b> ',$modSettings['gallery_medium_width'],'<br />
			<br />
			<input type="hidden" value="' . $context['catid'] . '" name="id" />
			<input type="hidden" value="' . $context['usercat'] . '" name="usercat" />
			<input type="submit" value="' . $txt['gallery_text_regeneratethumbnails2'] . '" name="submit" />
			<br />
			</td>
		  </tr>
		</table>
		</form>
		</div>';
}

function template_regenerate2()
{
	global $scripturl, $context, $txt;

	if (empty($context['continue_countdown']))
		$context['continue_countdown'] = 3;

	if (empty($context['continue_get_data']))
		$context['continue_get_data'] ='';

	if (empty($context['continue_post_data']))
		$context['continue_post_data'] ='';


	echo '<b>' . $txt['gallery_text_regeneratethumbnails2']. '</b><br />';

		if (!empty($context['continue_percent']))
		echo '
					<div style="padding-left: 20%; padding-right: 20%; margin-top: 1ex;">
						<div style="font-size: 8pt; height: 12pt; border: 1px solid black; background-color: white; padding: 1px; position: relative;">
							<div style="padding-top: ', $context['browser']['is_webkit'] || $context['browser']['is_konqueror'] ? '2pt' : '1pt', '; width: 100%; z-index: 2; color: black; position: absolute; text-align: center; font-weight: bold;">', $context['continue_percent'], '%</div>
							<div style="width: ', $context['continue_percent'], '%; height: 12pt; z-index: 1; background-color: red;">&nbsp;</div>
						</div>
					</div>';

	echo '<form action="' . $scripturl . '?action=gallery;sa=regen2;' , $context['continue_get_data'], '" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;" name="autoSubmit" id="autoSubmit">
				<div style="margin: 1ex; text-align: right;"><input type="submit" name="cont" value="', $txt['gallery_txt_continue'], '" class="button_submit" /></div>
				', $context['continue_post_data'], '

			<input type="hidden" value="' . $context['gallery_regenmedium'] . '" name="gallery_regenmedium" />
			<input type="hidden" value="' . $context['catid'] . '" name="id" />
			 <input type="hidden" value="' . $context['usercat'] . '" name="usercat" />
			</form>

			<script type="text/javascript"><!-- // --><![CDATA[
		var countdown = ', $context['continue_countdown'], ';
		doAutoSubmit();

		function doAutoSubmit()
		{
			if (countdown == 0)
				document.forms.autoSubmit.submit();
			else if (countdown == -1)
				return;

			document.forms.autoSubmit.cont.value = "',$txt['gallery_txt_continue'] , ' (" + countdown + ")";
			countdown--;

			setTimeout("doAutoSubmit();", 1000);
		}
	// ]]></script>';


}

function template_ftp()
{
	global $txt, $modSettings, $scripturl, $context, $settings,  $gallerySettings;

	echo '
		<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_ftp'] , '
		</h3>
  </div>
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="windowbg2">
			<td>';

	if (!isset($_REQUEST['fpath']))
	{
		echo '<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="tborder">
		<tr class="windowbg2">
			<td align="center"><b>' . $gallerySettings['gallery_set_batchadd_path'] . '</b></td>
		</tr>';
		showfolders('','');
		echo '</table>';

		// Show any files in the folder
		$fpath = $gallerySettings['gallery_set_batchadd_path'];
		$fpath2 = '';

		echo '
		<form method="POST" name="ftpform" action="',$scripturl,'?action=gallery;sa=batchftp2">
		<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
		<tr class="windowbg2">
			<td colspan="3" align="center"><b>' . $fpath . '</b></td>
		</tr>
	<tr class="catbg">
		<td></td>
		<td>' .  $txt['gallery_ftp_filename']  . '</td>
		<td>' . $txt['gallery_app_image']  . '</td>
		</tr>';
		// Loop though all the images in the folder
		$dir = opendir($fpath);
		$count = 0;
		while ($file = readdir($dir))
		{
			$sizes = @getimagesize($fpath . '/' . $file);

			if ($sizes !== false)
			{
				echo '<tr>
			<td><input type="checkbox" name="fimage[]" checked="checked" value="',$file,'" /></td>
			<td>',$file,'</td>
			<td><a href="',$modSettings['gallery_url'], $fpath2 ,'/', $file,'" target="_blank"><img src="', $modSettings['gallery_url'], $fpath2, '/', $file,'" height="60" width="60" alt="" /></a></td>
			</tr>';

				$count++;
			}
		}
		closedir($dir);

		/*
		<tr>
			<td colspan="3">
			<a href="javascript:CheckAll();">',$txt['gallery_ftp_checkall'],'</a> <a href="javascript:UnCheckAll();">',$txt['gallery_ftp_uncheckall'],'</a>
			<hr />
			</td>

		</tr>
		*/

		echo '<tr>
		<td colspan="3" align="center">',$txt['gallery_text_totalimages'],' ', $count, '</td>
		</tr>

		<tr>
			<td>',$txt['gallery_text_category'],'</td>
			<td colspan="2"><select name="cat">
			<option value="0" selected="selected">',$txt['gallery_ftp_selectcategory'],'</option>';


		foreach ($context['gallery_cat'] as $i => $category)
		{
			echo '<option value="' . $category['id_cat']  . '">' . $category['title'] . '</option>';
		}

		echo '</select></td>
				</tr>
				<tr>
					<td>',$txt['gallery_user_title2'],'</td>
					<td colspan="2"><select name="usercat">
					<option value="0" selected="selected">',$txt['gallery_ftp_selectcategory'],'</option>';

		foreach ($context['gallery_cat2'] as $i => $category)
		{
			echo '<option value="' . $category['id_cat']  . '">' .$category['real_name'] . ' => ' . $category['title'] . '</option>';
		}

		echo '</select></td>
				</tr>
				<tr>
					<td align="right"><input type="checkbox" name="ignoresettings" /></td>
					<td colspan="2">',$txt['gallery_ftp_ignoresettings'],'</td>
				</tr>
				<tr>
					<td align="right"><input type="checkbox" name="deletefiles" /></td>
					<td colspan="2">',$txt['gallery_ftp_deletefiles'],'</td>
				</tr>
				<tr>
					<td>',$txt['gallery_ftp_usertopost'],'</td>
					<td colspan="2"><input type="text" name="pic_postername" id="pic_postername" value="" />
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/members.png" alt="', $txt['find_members'], '" alt="" /></a>
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);">', $txt['find_members'], '</a></td>
				</tr>
				<tr>
					<td colspan="3" align="center"><input type="hidden" name="fpath" value="',$fpath2,'" />
					<input type="submit" value="',$txt['gallery_text_import'],'" />

					</td>
				</tr>
				</table>
				</form>
	<script language="JavaScript" type="text/javascript">
		function CheckAll()
		{
				for (i = 0; i < document.ftpform.fimage.length; i++)
				document.ftpform.fimage[i].checked = true;
		}
		function UnCheckAll()
		{
				for (i = 0; i < document.ftpform.fimage.length; i++)
					document.ftpform.fimage[i].checked = false;
		}
	</script>

				';
	} // End check for directory
	else
	{
		$fpath = $gallerySettings['gallery_set_batchadd_path']. htmlspecialchars(urldecode($_REQUEST['fpath']),ENT_QUOTES);
		$fpath2 = htmlspecialchars(urldecode($_REQUEST['fpath']),ENT_QUOTES);

		echo '
		<form method="post" name="ftpform" action="',$scripturl,'?action=gallery;sa=batchftp2">
		<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
		<tr class="windowbg2">
			<td colspan="3" align="center"><b>' . $fpath . '</b></td>
		</tr>
		<tr class="catbg">
		<td></td>
		<td>' .  $txt['gallery_ftp_filename']  . '</td>
		<td>' . $txt['gallery_app_image']  . '</td>
		</tr>';
		// Loop though all the images in the folder
		$dir = opendir($fpath);
		$count = 0;
		while ($file = readdir($dir))
		{
			$sizes = @getimagesize($fpath . '/' . $file);

			if ($sizes !== false)
			{
				echo '<tr>
				<td><input type="checkbox" name="fimage[]" checked="checked" value="',$file,'" /></td>
				<td>',$file,'</td>
				<td><a href="',$modSettings['gallery_url'], $fpath2 ,'/', $file,'" target="_blank"><img src="', $modSettings['gallery_url'], $fpath2, '/', $file,'" height="60" width="60" alt="" /></a></td>
				</tr>';

				$count++;
			}
		}
		closedir($dir);

		/*
		<tr>
			<td colspan="3">
			<a href="javascript:CheckAll();">',$txt['gallery_ftp_checkall'],'</a> <a href="javascript:UnCheckAll();">',$txt['gallery_ftp_uncheckall'],'</a>
			<hr />
			</td>

		</tr>
		*/

		echo '
		<tr>
		<td colspan="3" align="center">',$txt['gallery_text_totalimages'],' ', $count, '</td>
		</tr>

		<tr>
			<td>',$txt['gallery_text_category'],'</td>
			<td colspan="2"><select name="cat">
			<option value="0" selected="selected">',$txt['gallery_ftp_selectcategory'],'</option>';

		foreach ($context['gallery_cat'] as $i => $category)
		{
			echo '<option value="' . $category['id_cat']  . '">' . $category['title'] . '</option>';
		}

		echo '</select></td>
				</tr>
				<tr>
					<td>',$txt['gallery_user_title2'],'</td>
					<td colspan="2"><select name="usercat">
					<option value="0" selected="selected">',$txt['gallery_ftp_selectcategory'],'</option>';

		foreach ($context['gallery_cat2'] as $i => $category)
		{
			echo '<option value="' . $category['id_cat']  . '">' .$category['real_name'] . ' => ' . $category['title'] . '</option>';
		}

		echo '</select></td>
				</tr>
				<tr>
					<td align="right"><input type="checkbox" name="ignoresettings" /></td>
					<td colspan="2">',$txt['gallery_ftp_ignoresettings'],'</td>
				</tr>
				<tr>
					<td align="right"><input type="checkbox" name="deletefiles" /></td>
					<td colspan="2">',$txt['gallery_ftp_deletefiles'],'</td>
				</tr>
				<tr>
					<td>',$txt['gallery_ftp_usertopost'],'</td>
					<td colspan="2"><input type="text" name="pic_postername" id="pic_postername" value="" />
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/members.png" alt="', $txt['find_members'], '" /></a>
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);">', $txt['find_members'], '</a></td>
				</tr>
				<tr>
					<td colspan="3" align="center"><input type="hidden" name="fpath" value="',$fpath2,'" />
					<input type="submit" value="',$txt['gallery_text_import'],'" />

					</td>
				</tr>
				</table>
				</form>
	<script language="JavaScript" type="text/javascript">
		function CheckAll()
		{
				for (i = 0; i < document.ftpform.fimage.length; i++)
				document.ftpform.fimage[i].checked = true;
		}
		function UnCheckAll()
		{
				for (i = 0; i < document.ftpform.fimage.length; i++)
					document.ftpform.fimage[i].checked = false;
		}
	</script>';
	}


	echo '
			</td>
		</tr>
	</table>';
}

function showfolders($folder, $ident)
{
	global $txt, $scripturl, $settings, $modSettings, $gallerySettings;

	$filepath =  $gallerySettings['gallery_set_batchadd_path'] . $folder;

	if (!is_readable($filepath))
		return;

	$dir = opendir($filepath);

	while ($file = readdir($dir))
	{
		if (is_dir( $gallerySettings['gallery_set_batchadd_path']  . $folder . $file) && substr($file,0,1) != "." &&  strpos($file,"'") == FALSE)
		{
			$next_dir = $folder . $file;
			$filepath =  $gallerySettings['gallery_set_batchadd_path'] . $folder . $file;
			$warnings  = '';

			if (!is_readable($filepath))
			$warnings = $txt['gallery_ftp_err_read'];

			if ($warnings)
			$warnings = '&nbsp;&nbsp;&nbsp;<font color="#FF0000">' . $warnings . '</font>';

			echo '<tr>
						<td>
							 ',$ident,'<img src="', $settings['images_url'], '/board.png" border="0" />&nbsp;<a href="',$scripturl,'?action=gallery;sa=batchftp;fpath=',$next_dir,'"">',$file,'</a>
							 ',$warnings,'
						</td>
				 </tr>';

		   showfolders($folder . $file . '/', $ident . '&nbsp;&nbsp;&nbsp;&nbsp;');
		}
	}
	closedir($dir);
}

function template_ftp2()
{
	global $txt, $context;

	echo '
	<table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="titlebg">
			<td>' . $txt['gallery_ftp'] . '</td>
		</tr>
		<tr class="windowbg">
			<td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				  <tr class="catbg">
					<td align="center">
					<b>' . $txt['gallery_ftp_goodimages']. '</b></td>
				  </tr>
				  <tr class="windowbg2">
					<td>' .$context['gallery_ftp_goodpics']. '</td>
				  </tr>
				</table>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				  <tr class="catbg">
					<td align="center">
					<b>' . $txt['gallery_ftp_failedimages']. '</b></td>
				  </tr>
				  <tr class="windowbg2">
					 <td>' .$context['gallery_ftp_failedpics']. '</td>
				  </tr>
				</table>
			</td>
		</tr>
	</table>';
}

function template_change_gallery()
{
	global $txt, $context, $scripturl;

	ShowTopGalleryBar();

echo '
<form method="post" name="ftpform" action="',$scripturl,'?action=gallery;sa=changegallery2">
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_changepiclocation'], '
		</h3>
  </div>
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
';

// Moving picture to main gallery
 if ($context['gallery_mv'] == 'togallery')
 {
	echo '
	<tr class="windowbg2">
						<td align="right">',$txt['gallery_text_category'],'</td>
						<td><select name="cat">
						<option value="0" selected="selected">',$txt['gallery_ftp_selectcategory'],'</option>';


		foreach ($context['gallery_cat'] as $i => $category)
		{
			echo '<option value="' . $category['id_cat']  . '">' . $category['title'] . '</option>';
		}

	 echo '</select></td>
					</tr>';
 }

 // Moving Picture to User Gallery
 if ($context['gallery_mv'] == 'touser')
 {
	 echo '
					<tr class="windowbg2">
						<td align="right">',$txt['gallery_user_title2'],'</td>
						<td><select name="usercat">
						<option value="0" selected="selected">',$txt['gallery_ftp_selectcategory'],'</option>';

		foreach ($context['gallery_cat2'] as $i => $category)
		{
			echo '<option value="' . $category['id_cat']  . '">' .$category['real_name'] . ' => ' . $category['title'] . '</option>';
		}

	 echo '</select>
	 </td>
	</tr>';
 }


 echo '
<tr class="windowbg2">
	<td colspan="2" align="center">
	<input type="hidden" name="id" value="',$context['gallery_pic_id'],'" />
	<input type="submit" value="',$txt['gallery_txt_changegallery'],'" />
	</td>
</tr>
</table>
</form>';


}

function template_listall()
{
	global $modSettings, $settings, $txt, $context, $user_info, $scripturl;

	$g_manage = allowedTo('smfgallery_manage');
	$g_edit_own = allowedTo('smfgallery_edit');
	$g_delete_own = allowedTo('smfgallery_delete');

	ShowTopGalleryBar();

	$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];

	echo '
<br />
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $context['gallery_stat_title'], '
		</h3>
  </div>

<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">

	<tr>
	<td align="right" colspan="', $maxrowlevel, '">
	<form method="post" action="', $scripturl, '?action=gallery;sa=listall;type=', $_REQUEST['type'],'">';


	echo $txt['gallery_txt_perpage'],'
	<select name="perpage">
	<option value="',$modSettings['orignal_set_images_per_page'],'"' . ( $modSettings['orignal_set_images_per_page'] == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'],'</option>
	<option value="',$modSettings['orignal_set_images_per_page'] * 2,'"' . ( $modSettings['orignal_set_images_per_page'] * 2  == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'] * 2,'</option>
	<option value="',$modSettings['orignal_set_images_per_page'] * 3,'"' . ( $modSettings['orignal_set_images_per_page'] * 3 == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'] * 3,'</option>
	</select> ';

	echo '
	',$txt['gallery_txt_orderby'],'
	<select name="orderby">
	<option value="desc"' . (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'desc' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_desc'],'</option>
	<option value="asc"' . (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'asc' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_asc'],'</option>
	</select>
	<input type="submit" value="',$txt['gallery_txt_sort_go'] ,'" />
	<input type="hidden" name="start" value="',$context['start'],'" />
	</form></td>
	</tr>';

	$rowlevel = 0;
	foreach ($context['picture_list'] as $row)
	{
		if ($row['mature'] == 1)
			{
				if (CanViewMature() == false)
					$row['thumbfilename'] = 'mature.gif';
			}

		if ($rowlevel == 0)
			echo '<tr class="windowbg2">';

			$setupRow = array(
			'showhighslide' => 0,
			'displaycheckbox' => 0,
			'showunreadicon' => 1,
			'g_manage' => $g_manage,
			'g_edit_own' => $g_edit_own,
			'g_delete_own' => $g_delete_own
			);

			ShowImageItem($row,$setupRow);


		if ($rowlevel < ($maxrowlevel-1))
			$rowlevel++;
		else
		{
			echo '</tr>';
			$rowlevel = 0;
		}
	}
	if ($rowlevel !=0)
	{
		if (($maxrowlevel - $rowlevel) > 0)
				echo '<td colspan="' .($maxrowlevel - $rowlevel) .'"></td>';
		echo '</tr>';
	}

	echo '<tr class="catbg2">
				<td align="left" colspan="' . $maxrowlevel . '">',
				$context['page_index'],'
				</td>
			</tr>';



	echo '
</table><br />';


 	echo '
                    <div class="tborder">
            <div class="roundframe centertext">
            <a href="', $scripturl, '?action=gallery">', $txt['gallery_text_returngallery'], '</a>
            </div>
        </div>';


}

function GetManageSubCats($id_parent,$categories)
{
	global $currentclass, $cat_sep, $scripturl, $txt;

	foreach ($categories as $i => $category)
	{
		if ($category['id_parent'] == $id_parent)
		{
			echo '
	<tr class="',$currentclass ,'">
		<td>
			<a href="' . $scripturl . '?action=gallery;cat=' . $category['id_cat'] . '">',str_repeat('-',$cat_sep),$category['title'],'</a>
		</td>
		<td>' .  $category['total'] . '</td>
		<td><a href="' . $scripturl . '?action=gallery;sa=editcat;cat=' . $category['id_cat'] . '">' . $txt['gallery_text_edit'] . '</a>&nbsp;
			<a href="' . $scripturl . '?action=gallery;sa=deletecat;cat=' . $category['id_cat'] . '">' . $txt['gallery_text_delete'] . '</a>&nbsp;
			<a href="' . $scripturl . '?action=gallery;sa=catperm;cat=' . $category['id_cat'] . '">[' . $txt['gallery_text_permissions'] . ']</a><br />
			<a href="' . $scripturl . '?action=gallery;sa=import;cat=' .  $category['id_cat'] . '">' . $txt['gallery_text_importpics'] . '</a>&nbsp;
			<a href="' . $scripturl . '?action=gallery;sa=regen;cat=' .  $category['id_cat'] . '">' . $txt['gallery_text_regeneratethumbnails'] . '</a>
		</td>
	</tr>';

			if ($currentclass == "windowbg")
				$currentclass = "windowbg2";
			else
				$currentclass = "windowbg";
			$cat_sep++;
			GetManageSubCats($category['id_cat'],$categories);
			$cat_sep--;
		}
	}
}

function template_gallery_layout()
{
	global $txt, $modSettings, $scripturl, $context, $gallerySettings;

	// Settings Admin Tabs
	SettingsAdminTabs();

	echo '
<div id="moderationbuttons" class="margintop">
	', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
</div><br /><br /><br />';

echo '
<form method="post" action="' . $scripturl . '?action=gallery;sa=savelayout">
<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_txt_layout_settings'], '
		</h3>
  </div>



          <div class="windowbg noup">
				<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
				<tr><td colspan="2"><b>' . $txt['gallery_txt_index_settings'] . '</b></td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_index_showusergallery" ' . ($modSettings['gallery_index_showusergallery'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_showusergallery'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_show_cat_latest_pictures" ' . ($gallerySettings['gallery_set_show_cat_latest_pictures'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_show_cat_latest_pictures'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_index_toprated" ' . ($modSettings['gallery_index_toprated'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_toprated'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_index_recent" ' . ($modSettings['gallery_index_recent'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_recent'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_index_mostviewed" ' . ($modSettings['gallery_index_mostviewed'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_mostviewed'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_index_mostcomments" ' . ($modSettings['gallery_index_mostcomments'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_mostcomments'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_index_randomimages" ' . ($gallerySettings['gallery_index_randomimages'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_randomimages'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_index_recentcomments" ' . ($gallerySettings['gallery_index_recentcomments'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_recentcomments'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_index_mostliked" ' . ($modSettings['gallery_index_mostliked'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_mostliked'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_index_showtop" ' . ($modSettings['gallery_index_showtop'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_showtop'] . '</td></tr>

				<tr><td width="30%">' . $txt['gallery_index_images_to_show'] . '</td><td><input type="text" name="gallery_index_images_to_show" value="' .  $gallerySettings['gallery_index_images_to_show'] . '" /></td></tr>
				<tr><td colspan="2"><b>',$txt['gallery_txt_tag_cloud'],'</b></td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_index_show_tag_cloud" ' . ($gallerySettings['gallery_index_show_tag_cloud'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_index_show_tag_cloud'] . '</td></tr>
				<tr><td width="30%">',$txt['gallery_set_cloud_tags_to_show'],'</td><td><input type="text" name="gallery_set_cloud_tags_to_show" value="',$gallerySettings['gallery_set_cloud_tags_to_show'], '" /></td></tr>
				<tr><td width="30%">',$txt['gallery_set_cloud_tags_per_row'],'</td><td><input type="text" name="gallery_set_cloud_tags_per_row" value="',$gallerySettings['gallery_set_cloud_tags_per_row'], '" /></td></tr>
				<tr><td width="30%">',$txt['gallery_set_cloud_max_font_size_precent'],'</td><td><input type="text" name="gallery_set_cloud_max_font_size_precent" value="',$gallerySettings['gallery_set_cloud_max_font_size_precent'], '" /></td></tr>
				<tr><td width="30%">',$txt['gallery_set_cloud_min_font_size_precent'],'</td><td><input type="text" name="gallery_set_cloud_min_font_size_precent" value="',$gallerySettings['gallery_set_cloud_min_font_size_precent'], '" /></td></tr>

				<tr><td colspan="2"><b>' . $txt['gallery_catthumb_settings'] . '</b></td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_t_views" ' . ($modSettings['gallery_set_t_views'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_t_views'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_t_filesize" ' . ($modSettings['gallery_set_t_filesize'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_t_filesize'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_t_date" ' . ($modSettings['gallery_set_t_date'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_t_date'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_t_comment" ' . ($modSettings['gallery_set_t_comment'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_t_comment'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_t_username" ' . ($modSettings['gallery_set_t_username'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_t_username'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_t_rating" ' . ($modSettings['gallery_set_t_rating'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_t_rating'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_t_title" ' . ($modSettings['gallery_set_t_title'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_title'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_t_totallikes" ' . ($modSettings['gallery_set_t_totallikes'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_t_totallikes'] . '</td></tr>



				<tr><td colspan="2"><b>' . $txt['gallery_image_settings'] . '</b></td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_size" ' . ($modSettings['gallery_set_img_size'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_size'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_prevnext" ' . ($modSettings['gallery_set_img_prevnext'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_prevnext'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_desc" ' . ($modSettings['gallery_set_img_desc'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_desc'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_title" ' . ($modSettings['gallery_set_img_title'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_title'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_views" ' . ($modSettings['gallery_set_img_views'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_views'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_poster" ' . ($modSettings['gallery_set_img_poster'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_poster'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_date" ' . ($modSettings['gallery_set_img_date'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_date'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_showfilesize" ' . ($modSettings['gallery_set_img_showfilesize'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_showfilesize'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_showrating" ' . ($modSettings['gallery_set_img_showrating'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_showrating'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_keywords" ' . ($modSettings['gallery_set_img_keywords'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_keywords'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_img_totallikes" ' . ($modSettings['gallery_set_img_totallikes'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_img_totallikes'] . '</td></tr>


				<tr><td colspan="2"><input type="checkbox" name="gallery_set_mini_prevnext_thumbs" ' . ($gallerySettings['gallery_set_mini_prevnext_thumbs'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_mini_prevnext_thumbs'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_set_picture_information_last" ' . ($gallerySettings['gallery_set_picture_information_last'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_picture_information_last'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_set_hide_lastmodified_comment" ' . ($gallerySettings['gallery_set_hide_lastmodified_comment'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_hide_lastmodified_comment'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_share_facebook" ' . ($gallerySettings['gallery_share_facebook'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_share_facebook'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_share_twitter" ' . ($gallerySettings['gallery_share_twitter'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_share_twitter'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_share_addthis" ' . ($gallerySettings['gallery_share_addthis'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_share_addthis'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_share_facebooklike" ' . ($gallerySettings['gallery_share_facebooklike'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_share_facebooklike'] . '</td></tr>

				<tr><td colspan="2"><input type="checkbox" name="gallery_share_pinterest" ' . ($gallerySettings['gallery_share_pinterest'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_share_pinterest'] . '</td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_share_reddit" ' . ($gallerySettings['gallery_share_reddit'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_share_reddit'] . '</td></tr>


				<tr><td colspan="2"><input type="checkbox" name="gallery_set_downloadimage" ' . ($gallerySettings['gallery_set_downloadimage'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_downloadimage'] . '</td></tr>

				<tr><td colspan="2"><b>' . $txt['gallery_other_settings'] . '</b></td></tr>

				<tr><td width="30%">' . $txt['gallery_bulkuploadfields'] . '</td><td><input type="text" name="gallery_bulkuploadfields" value="' .  $gallerySettings['gallery_bulkuploadfields'] . '" /></td></tr>
				<tr><td colspan="2"><input type="checkbox" name="gallery_show_unviewed_items" ' . ($modSettings['gallery_show_unviewed_items'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_show_unviewed_items'] . '</td></tr>


				<tr><td colspan="2"><input type="submit" name="savesettings" value="' . $txt['gallery_save_settings'] . '" /></td></tr>
				</table>
				</div>
			</form>


';
}

function template_image_listing()
{
	global $scripturl, $settings, $txt, $context, $user_info, $modSettings, $subcats_linktree, $gallerySettings;

	// Permissions
	$g_manage = allowedTo('smfgallery_manage');
	$g_add = allowedTo('smfgallery_add');
	$g_bulk = allowedTo('smfgallery_bulk');

	$g_video = allowedTo('smfgalleryvideo_add');

	$cat = (int) $_REQUEST['cat'];

	// Permissions if they are allowed to edit or delete their own gallery pictures.
	$g_edit_own = allowedTo('smfgallery_edit');
	$g_delete_own = allowedTo('smfgallery_delete');

	$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];

	ShowTopGalleryBar();

	// Show sub categories
	Gallery_ShowSubCats($cat,$g_manage);

	echo '<br />
	<form method="post" action="', $scripturl, '?action=gallery;cat=', $cat,'">
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', @$context['gallery_cat_name'], ' ' . ((!empty($gallerySettings['gallery_enable_rss']) && ( $context['gallery_cat_redirect']  == 0)) ? ' <a href="' . $scripturl . '?action=gallery;sa=rss;cat=' . $cat . '"><img src="' . $modSettings['gallery_url'] . '/rss.png" alt="rss" /></a>' : '')  .'
		</h3>
  </div>
 <table class="table_grid">
		';



	// Do Sorting form here
	echo '<tr class="windowbg2">';
	if ($g_manage)
	{
		echo '<td align="right" colspan="', $maxrowlevel, '"><div align="left"><input type="checkbox" id="checkall" class="check" onclick="invertAll(this, this.form);"  /></div>';
	}
	else
	{
		echo '<td align="right" colspan="', $maxrowlevel, '">';
	}

	echo $txt['gallery_txt_perpage'],'
	<select name="perpage">
	<option value="',$modSettings['orignal_set_images_per_page'],'"' . ( $modSettings['orignal_set_images_per_page'] == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'],'</option>
	<option value="',$modSettings['orignal_set_images_per_page'] * 2,'"' . ( $modSettings['orignal_set_images_per_page'] * 2  == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'] * 2,'</option>
	<option value="',$modSettings['orignal_set_images_per_page'] * 3,'"' . ( $modSettings['orignal_set_images_per_page'] * 3 == $modSettings['gallery_set_images_per_page'] ? ' selected="selected"' : ''). '>',$modSettings['orignal_set_images_per_page'] * 3,'</option>
	</select> ';


	echo $txt['gallery_txt_sortby'],'
	<select name="sortby">
	<option value="date"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'date' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_date'],'</option>
	<option value="title"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'title' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_title'],'</option>
	<option value="mostview"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'mostview' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_mostviewed'],'</option>
	<option value="mostcom"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'mostcom' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_mostcomments'],'</option>
	<option value="mostrated"' . (isset($_REQUEST['sortby']) ? ($_REQUEST['sortby'] == 'mostrated' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_mostrated'],'</option>
	</select>

	',$txt['gallery_txt_orderby'],'
	<select name="orderby">
	<option value="desc"' . (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'desc' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_desc'],'</option>
	<option value="asc"' . (isset($_REQUEST['orderby']) ? ($_REQUEST['orderby'] == 'asc' ? ' selected="selected"' : '') : '') .  '>',$txt['gallery_txt_sort_asc'],'</option>
	</select>
	<input type="submit" value="',$txt['gallery_txt_sort_go'] ,'" />
	<input type="hidden" name="start" value="',$context['start'],'" />
	</td>
	</tr>';

	if (isset($modSettings['gallery_ad_seller']))
	{
    	// Begin Ad Seller Pro Location - SMF Gallery - Top of category View

    	global $sourcedir;
    	include_once $sourcedir . "/adseller2.php";

    	$adSellerAdData =  ShowAdLocation(101);

    	// Check if any ads where found
    	if ($adSellerAdData != false)
    	{
    		// Dispaly the advertising code
    		echo '<tr class="windowbg">
    			<td align="center" colspan="', $maxrowlevel, '">';
    		echo $adSellerAdData;

    		echo '</td></tr>';
    	}

    	// End Ad Seller Pro Location - SMF Gallery - Top of category View
	}

	// Show the pictures


	$rowlevel = 0;


	if (!isset($context['gallery_cat_norate']))
		$context['gallery_cat_norate'] = 0;

	foreach($context['gallery_image_listing_data'] as $row)
	{
			if ($row['mature'] == 1)
			{
				if (CanViewMature() == false)
					$row['thumbfilename'] = 'mature.gif';
			}
		if ($rowlevel == 0)
			echo '<tr class="windowbg2">';

			$setupRow = array(
			'showhighslide' => 0,
			'displaycheckbox' => 1,
			'showunreadicon' => 1,
			'g_manage' => $g_manage,
			'g_edit_own' => $g_edit_own,
			'g_delete_own' => $g_delete_own
			);

			ShowImageItem($row,$setupRow);


		if ($rowlevel < ($maxrowlevel-1))
			$rowlevel++;
		else
		{
			echo '</tr>';
			$rowlevel = 0;
		}
	}

	if ($rowlevel != 0)
	{
		if (($maxrowlevel - $rowlevel) > 0)
				echo '<td colspan="' .($maxrowlevel - $rowlevel) .'"></td>';

		echo '</tr>';
	}




	if (isset($modSettings['gallery_ad_seller']))
	{
    	// Begin Ad Seller Pro Location - SMF Gallery - Bottom of category View

    	global $sourcedir;
    	include_once $sourcedir . "/adseller2.php";

    	$adSellerAdData =  ShowAdLocation(102);

    	// Check if any ads where found
    	if ($adSellerAdData != false)
    	{
    		// Dispaly the advertising code
    		echo '<tr class="windowbg">
    			<td align="center" colspan="', $maxrowlevel, '">';
    		echo $adSellerAdData;

    		echo '</td></tr>';
    	}

    	// End Ad Seller Pro Location - SMF Gallery - Bottom of category View
	}

	// Display who is viewing the gallery.
	if (!empty($modSettings['gallery_who_viewing']))
	{
		echo '<tr class="windowbg2">
		<td align="center" colspan="', $maxrowlevel, '"><span class="smalltext">';

		// Show just numbers...?
		// show the actual people viewing the topic?
		echo empty($context['view_members_list']) ? '0 ' . $txt['gallery_who_members'] : implode(', ', $context['view_members_list']) . (empty($context['view_num_hidden']) || $context['can_moderate_forum'] ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['gallery_who_hidden'] . ')');

		// Now show how many guests are here too.
		echo $txt['who_and'], @$context['view_num_guests'], ' ', @$context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['gallery_who_viewgallery'], '</span></td></tr>';
	}

	// Show return to gallery link and Show add picture if they can
	echo '<tr class="catbg2">
			<td align="left" colspan="' . $maxrowlevel . '">
			';


	echo $context['page_index'];

	echo '
			</td>
		</tr>';

	if ($g_manage)
	{

		echo '<tr class="catbg2"><td align="center" colspan="', $maxrowlevel, '">';

		echo '<b>',$txt['gallery_text_withselected'],'</b>
	<select name="movecat" id="moveItTo">
	';

	foreach ($context['gallery_cat'] as $i => $category)
		echo '<option value="' . $category['id_cat']  . '">' . $category['title'] . '</option>';


	echo '</select>

			<select name="doaction" onchange="this.form.moveItTo.disabled = (this.options[this.selectedIndex].value != \'move\');">
			<option value="move">', $txt['gallery_movepicture'],'</option>
			<option value="unapprove">',$txt['gallery_text_unapprove2'],'</option>
			<option value="approve">',$txt['gallery_form_approveimages'],'</option>
			<option value="delete">',$txt['gallery_form_delpicture'],'</option>
			</select>
			<input type="submit" value="',$txt['gallery_text_performaction'],'" />';
		echo '</td>
		</tr>';
	}



	echo '
		</table>
		</form><br />';


 	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

	if ($g_bulk && GetCatPermission($cat,'addpic',true) === true)
		echo '<a href="', $scripturl, '?action=gallery;sa=bulk;cat=', $cat, '">' , $txt['gallery_text_bulkadd'], '</a>&nbsp; - &nbsp;';

	if ($g_manage)
		echo '<a href="', $scripturl, '?action=gallery;sa=addcat;cat=', $cat, '">', $txt['gallery_text_addsubcat'], '</a>&nbsp; - &nbsp;';

	if ($g_add && GetCatPermission($cat,'addpic',true) === true)
		echo '<a href="', $scripturl, '?action=gallery;sa=add;cat=', $cat, '">', $txt['gallery_text_addpicture'], '</a>&nbsp; - &nbsp;';

	if (GetCatPermission($cat,'addvideo',true) === true)
		if ($g_video)
			echo '<a href="', $scripturl, '?action=gallery;sa=addvideo;cat=', $cat, '">', $txt['gallery_form_addvideo'], '</a>&nbsp; - &nbsp; ';

	echo '
			<a href="', $scripturl, '?action=gallery">', $txt['gallery_text_returngallery'], '</a>
            </div>
        </div>';

}

function template_gallery_exif()
{
	global $txt, $scripturl, $gallerySettings, $context;

	// Settings Admin Tabs
	SettingsAdminTabs();

	echo '
<div id="moderationbuttons" class="margintop">
	', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
</div><br /><br /><br />';


	echo '<form method="post" action="' . $scripturl . '?action=gallery;sa=exifsettings2">
 <div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_txt_exif_settings'], '
		</h3>
  </div>';

	// Check if EXIF is installed
	if (!function_exists('exif_read_data'))
		echo '<font color="#FF0000"><b>', $txt['gallery_txt_exif_notinstalled'], '</b></font><br />';

echo '
<div class="windowbg noup">
    <table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
<tr>
	<td>
			<input type="checkbox" name="enable_exif_on_display" ' . ( $gallerySettings['enable_exif_on_display'] ? ' checked="checked" ' : '') . ' />' . $txt['enable_exif_on_display'] . '<br />
			<br />

			<b>' . $txt['gallery_idf0_section']  . '</b><br />
			<input type="checkbox" name="show_idfo_ImageDescription" ' . ( $gallerySettings['show_idfo_ImageDescription'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_ImageDescription'] . '<br />
			<input type="checkbox" name="show_idfo_Make" ' . ( $gallerySettings['show_idfo_Make'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_Make'] . '<br />
			<input type="checkbox" name="show_idfo_Model" ' . ( $gallerySettings['show_idfo_Model'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_Model'] . '<br />
			<input type="checkbox" name="show_idfo_Orientation" ' . ( $gallerySettings['show_idfo_Orientation'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_Orientation'] . '<br />
			<input type="checkbox" name="show_idfo_XResolution" ' . ( $gallerySettings['show_idfo_XResolution'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_XResolution'] . '<br />
			<input type="checkbox" name="show_idfo_YResolution" ' . ( $gallerySettings['show_idfo_YResolution'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_YResolution'] . '<br />
			<input type="checkbox" name="show_idfo_ResolutionUnit" ' . ( $gallerySettings['show_idfo_ResolutionUnit'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_ResolutionUnit'] . '<br />
			<input type="checkbox" name="show_idfo_Software" ' . ( $gallerySettings['show_idfo_Software'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_Software'] . '<br />
			<input type="checkbox" name="show_idfo_DateTime" ' . ( $gallerySettings['show_idfo_DateTime'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_DateTime'] . '<br />
			<input type="checkbox" name="show_idfo_Artist" ' . ( $gallerySettings['show_idfo_Artist'] ? ' checked="checked" ' : '') . ' />' . $txt['show_idfo_Artist'] . '<br />

			<input type="checkbox" name="show_exif_lenstype" ' . ( $gallerySettings['show_exif_lenstype'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_lenstype'] . '<br />
			<input type="checkbox" name="show_exif_lensinfo" ' . ( $gallerySettings['show_exif_lensinfo'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_lensinfo'] . '<br />
			<input type="checkbox" name="show_exif_lensid" ' . ( $gallerySettings['show_exif_lensid'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_lensid'] . '<br />

			<b>' . $txt['gallery_computed_section']   . '</b><br />

			<input type="checkbox" name="show_computed_Height" ' . ( $gallerySettings['show_computed_Height'] ? ' checked="checked" ' : '') . ' />' . $txt['show_computed_Height'] . '<br />
			<input type="checkbox" name="show_computed_Width" ' . ( $gallerySettings['show_computed_Width'] ? ' checked="checked" ' : '') . ' />' . $txt['show_computed_Width'] . '<br />
			<input type="checkbox" name="show_computed_IsColor" ' . ( $gallerySettings['show_computed_IsColor'] ? ' checked="checked" ' : '') . ' />' . $txt['show_computed_IsColor'] . '<br />
			<input type="checkbox" name="show_computed_CCDWidth" ' . ( $gallerySettings['show_computed_CCDWidth'] ? ' checked="checked" ' : '') . ' />' . $txt['show_computed_CCDWidth'] . '<br />
			<input type="checkbox" name="show_computed_ApertureFNumber" ' . ( $gallerySettings['show_computed_ApertureFNumber'] ? ' checked="checked" ' : '') . ' />' . $txt['show_computed_ApertureFNumber'] . '<br />
			<input type="checkbox" name="show_computed_Copyright" ' . ( $gallerySettings['show_computed_Copyright'] ? ' checked="checked" ' : '') . ' />' . $txt['show_computed_Copyright'] . '<br />

			<b>' . $txt['gallery_exif_section']   . '</b><br />

			<input type="checkbox" name="show_exif_ExposureTime" ' . ( $gallerySettings['show_exif_ExposureTime'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ExposureTime'] . '<br />
			<input type="checkbox" name="show_exif_FNumber" ' . ( $gallerySettings['show_exif_FNumber'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_FNumber'] . '<br />
			<input type="checkbox" name="show_exif_ExposureProgram" ' . ( $gallerySettings['show_exif_ExposureProgram'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ExposureProgram'] . '<br />
			<input type="checkbox" name="show_exif_ISOSpeedRatings" ' . ( $gallerySettings['show_exif_ISOSpeedRatings'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ISOSpeedRatings'] . '<br />
			<input type="checkbox" name="show_exif_ExifVersion" ' . ( $gallerySettings['show_exif_ExifVersion'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ExifVersion'] . '<br />
			<input type="checkbox" name="show_exif_DateTimeOriginal" ' . ( $gallerySettings['show_exif_DateTimeOriginal'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_DateTimeOriginal'] . '<br />
			<input type="checkbox" name="show_exif_DateTimeDigitized" ' . ( $gallerySettings['show_exif_DateTimeDigitized'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_DateTimeDigitized'] . '<br />
			<input type="checkbox" name="show_exif_ShutterSpeedValue" ' . ( $gallerySettings['show_exif_ShutterSpeedValue'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ShutterSpeedValue'] . '<br />
			<input type="checkbox" name="show_exif_ApertureValue" ' . ( $gallerySettings['show_exif_ApertureValue'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ApertureValue'] . '<br />
			<input type="checkbox" name="show_exif_ExposureBiasValue" ' . ( $gallerySettings['show_exif_ExposureBiasValue'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ExposureBiasValue'] . '<br />
			<input type="checkbox" name="show_exif_MaxApertureValue" ' . ( $gallerySettings['show_exif_MaxApertureValue'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_MaxApertureValue'] . '<br />
			<input type="checkbox" name="show_exif_MeteringMode" ' . ( $gallerySettings['show_exif_MeteringMode'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_MeteringMode'] . '<br />
			<input type="checkbox" name="show_exif_LightSource" ' . ( $gallerySettings['show_exif_LightSource'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_LightSource'] . '<br />
			<input type="checkbox" name="show_exif_Flash" ' . ( $gallerySettings['show_exif_Flash'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_Flash'] . '<br />
			<input type="checkbox" name="show_exif_FocalLength" ' . ( $gallerySettings['show_exif_FocalLength'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_FocalLength'] . '<br />
			<input type="checkbox" name="show_exif_ColorSpace" ' . ( $gallerySettings['show_exif_ColorSpace'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ColorSpace'] . '<br />
			<input type="checkbox" name="show_exif_ExifImageWidth" ' . ( $gallerySettings['show_exif_ExifImageWidth'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ExifImageWidth'] . '<br />
			<input type="checkbox" name="show_exif_ExifImageLength" ' . ( $gallerySettings['show_exif_ExifImageLength'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ExifImageLength'] . '<br />
			<input type="checkbox" name="show_exif_FocalPlaneXResolution" ' . ( $gallerySettings['show_exif_FocalPlaneXResolution'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_FocalPlaneXResolution'] . '<br />
			<input type="checkbox" name="show_exif_FocalPlaneYResolution" ' . ( $gallerySettings['show_exif_FocalPlaneYResolution'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_FocalPlaneYResolution'] . '<br />
			<input type="checkbox" name="show_exif_FocalPlaneResolutionUnit" ' . ( $gallerySettings['show_exif_FocalPlaneResolutionUnit'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_FocalPlaneResolutionUnit'] . '<br />
			<input type="checkbox" name="show_exif_CustomRendered" ' . ( $gallerySettings['show_exif_CustomRendered'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_CustomRendered'] . '<br />
			<input type="checkbox" name="show_exif_ExposureMode" ' . ( $gallerySettings['show_exif_ExposureMode'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_ExposureMode'] . '<br />
			<input type="checkbox" name="show_exif_WhiteBalance" ' . ( $gallerySettings['show_exif_WhiteBalance'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_WhiteBalance'] . '<br />
			<input type="checkbox" name="show_exif_SceneCaptureType" ' . ( $gallerySettings['show_exif_SceneCaptureType'] ? ' checked="checked" ' : '') . ' />' . $txt['show_exif_SceneCaptureType'] . '<br />

			<b>' . $txt['gallery_gps_section']   . '</b><br />
			<input type="checkbox" name="show_gps_latituderef" ' . ( $gallerySettings['show_gps_latituderef'] ? ' checked="checked" ' : '') . ' />' . $txt['show_gps_latituderef'] . '<br />
			<input type="checkbox" name="show_gps_latitude" ' . ( $gallerySettings['show_gps_latitude'] ? ' checked="checked" ' : '') . ' />' . $txt['show_gps_latitude'] . '<br />
			<input type="checkbox" name="show_gps_longituderef" ' . ( $gallerySettings['show_gps_longituderef'] ? ' checked="checked" ' : '') . ' />' . $txt['show_gps_longituderef'] . '<br />
			<input type="checkbox" name="show_gps_longitude" ' . ( $gallerySettings['show_gps_longitude'] ? ' checked="checked" ' : '') . ' />' . $txt['show_gps_longitude'] . '<br />


			<input type="submit" name="savesettings" value="' . $txt['gallery_save_settings'] . '" />
			</div>
				</td>
</tr>
</table>
		</form>';


	echo '<br />
<br />
<br />
		<form method="post" action="' . $scripturl . '?action=gallery;sa=doallexif">
			<input type="submit" value="' . $txt['gallery_txt_redo_exif'] . '" />
		</form>
';

}

function ShowUserBox($memCommID)
{
	global $memberContext, $settings, $modSettings, $txt, $context, $scripturl, $options;

	if (!$memberContext[$memCommID]['is_guest']  && empty($memberContext[$memCommID]['name']))
		return;

	echo '
	<b>', $memberContext[$memCommID]['link'], '</b>
	<div class="smalltext">';

	// Show the member's custom title, if they have one.
	if (isset($memberContext[$memCommID]['title']) && $memberContext[$memCommID]['title'] != '')
		echo '
		', $memberContext[$memCommID]['title'], '<br />';

	// Show the member's primary group (like 'Administrator') if they have one.
	if (isset($memberContext[$memCommID]['group']) && $memberContext[$memCommID]['group'] != '')
		echo '
		', $memberContext[$memCommID]['group'], '<br />';

	// Don't show these things for guests.
	if (!$memberContext[$memCommID]['is_guest'])
	{
		// Show the post group if and only if they have no other group or the option is on, and they are in a post group.
		if ((empty($settings['hide_post_group']) || $memberContext[$memCommID]['group'] == '') && $memberContext[$memCommID]['post_group'] != '')
			echo '
		', $memberContext[$memCommID]['post_group'], '<br />';



		// Show online and offline buttons?
		if (!empty($modSettings['onlineEnable']) && !$memberContext[$memCommID]['is_guest'])
		{
			echo '<span id="userstatus">
				', $context['can_send_pm'] ? '<a href="' . $memberContext[$memCommID]['online']['href'] . '" title="' . $memberContext[$memCommID]['online']['text'] . '" rel="nofollow">' : '', $settings['use_image_buttons'] ? '<span class="' . ($memberContext[$memCommID]['online']['is_online'] == 1 ? 'on' : 'off') . '" title="' . $memberContext[$memCommID]['online']['text'] . '"></span>' : $memberContext[$memCommID]['online']['label'], $context['can_send_pm'] ? '</a>' : '', $settings['use_image_buttons'] ? '<span class="smalltext"> ' . $memberContext[$memCommID]['online']['label'] . '</span>' : '';
			echo '</span>';
		}


		// Show the member's gender icon?
		if (!empty($settings['show_gender']) && $memberContext[$memCommID]['gender']['image'] != '')
			echo '
		', $txt['gender'], ': ', $memberContext[$memCommID]['gender']['image'], '<br />';

		// Show how many posts they have made.
		echo '
		', $txt['member_postcount'], ': ', $memberContext[$memCommID]['posts'], '<br />
		<br />';

		// Show avatars, images, etc.?
		if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && !empty($memberContext[$memCommID]['avatar']['image']))
			echo '
		<div style="overflow: hidden; width: 100%;">', $memberContext[$memCommID]['avatar']['image'], '</div><br />';

		// Show their personal text?
		if (!empty($settings['show_blurb']) && $memberContext[$memCommID]['blurb'] != '')
			echo '
		', $memberContext[$memCommID]['blurb'], '<br />
		<br />';

	}
	// Otherwise, show the guest's email.
	elseif (empty($memberContext[$memCommID]['hide_email']))
		echo '
		<br />
		<br />
		<a href="mailto:', $memberContext[$memCommID]['email'], '">', ($settings['use_image_buttons'] ? '<img src="' . $modSettings['gallery_url'] . 'email_sm.gif" alt="' . $txt['email'] . '" title="' . $txt['email'] . '" border="0" />' : $txt['email']), '</a>';

	// Done with the information about the poster... on to the post itself.
	echo '
	</div>';
}

function SettingsAdminTabs()
{
	global $context, $scripturl;



	 $context['gallery']['buttons_set']['copyright'] =  array(
							'text' => 'gallery_txt_copyright',
							'url' => $scripturl . '?action=admin;area=gallery;sa=copyright' ,
							'lang' => true,
					);

	$context['gallery']['buttons_set']['twitter'] =  array(
							'text' => 'gallery_twitter',
							'url' => $scripturl . '?action=admin;area=gallery;sa=twitter' ,
							'lang' => true,
					);

	$context['gallery']['buttons_set']['videoaddon'] = array(
			'text' => 'gallery_text_videosettings',
			'url' => $scripturl . '?action=admin;area=gallery;sa=videoset',
			'lang' => true,
		);


	$context['gallery']['buttons_set']['exif'] = array(
		'text' => 'gallery_txt_exif_settings2',
		'url' => $scripturl . '?action=admin;area=gallery;sa=exifsettings',
		'lang' => true,
	);

	$context['gallery']['buttons_set']['layout'] = array(
		'text' => 'gallery_txt_layout_settings2',
		'url' => $scripturl . '?action=admin;area=gallery;sa=viewlayout',
		'lang' => true,
	);


	$context['gallery']['buttons_set']['settings'] = array(
		'text' => 'gallery_text_features',
		'url' => $scripturl . '?action=admin;area=gallery;sa=adminset',
		'lang' => true,
		'is_selected' => true,
	);




}

function template_unviewed()
{
	global $txt, $context, $scripturl, $modSettings, $gallerySettings, $user_info;

	// Permissions
	$g_manage = allowedTo('smfgallery_manage');
	// Permissions if they are allowed to edit or delete their own gallery pictures.
	$g_edit_own = allowedTo('smfgallery_edit');
	$g_delete_own = allowedTo('smfgallery_delete');

	$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];


	ShowTopGalleryBar();


echo '<br />';

  		$context['gallery']['buttons_set']['markallviewed'] =  array(
								'text' => 'gallery_txt_markallviewed',
								'url' => $scripturl . '?action=gallery;sa=markallviewed',
								'lang' => true,
								'is_selected' => true,
						);



		echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="90%">
							<tr>
								<td style="padding-right: 1ex;" align="right">
								<table cellpadding="0" cellspacing="0" align="left">
										<tr>
										<td align="right">
							', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
								</td>
							</tr>
								</table>
					</td>
							</tr>
						</table>';


		echo '
		<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_unviewed_images'], '
		</h3>
  </div>
		<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">
';

		$rowlevel = 0;

		if (!isset($context['gallery_cat_norate']))
			$context['gallery_cat_norate'] = 0;

		foreach($context['gallery_pics'] as $row)
		{

			if ($row['mature'] == 1)
			{
				if (CanViewMature() == false)
					$row['thumbfilename'] = 'mature.gif';
			}

			if ($rowlevel == 0)
				echo '<tr class="windowbg2">';


			$setupRow = array(
			'showhighslide' => 0,
			'g_manage' => $g_manage,
			'g_edit_own' => $g_edit_own,
			'g_delete_own' => $g_delete_own
			);

			ShowImageItem($row,$setupRow);



			if ($rowlevel < ($maxrowlevel-1))
				$rowlevel++;
			else
			{
				echo '</tr>';
				$rowlevel = 0;
			}
		}

		if ($rowlevel != 0)
		{

			if (($maxrowlevel - $rowlevel) > 0)
				echo '<td colspan="' .($maxrowlevel - $rowlevel) .'"></td>';

			echo '</tr>';
		}

		// Show return to gallery link and Show add picture if they can
			echo '<tr class="catbg2">
					<td align="left" colspan="' . $maxrowlevel . '">
					';

					echo $context['page_index'];

			echo '
					</td>
				</tr>';

		echo '
			</table><br />';

echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="', $scripturl, '?action=gallery">', $txt['gallery_text_returngallery'], '</a>
            </div>
        </div>';

}

function template_viewers()
{
	global $scripturl, $txt, $context;

	ShowTopGalleryBar();

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_viewers'], '
		</h3>
  </div>
	<table cellspacing="0" cellpadding="5" border="0" align="center" width="100%" class="tborder">
				<tr class="windowbg">
					<td>' . $context['gallery_viewers'] . '</td>
				</tr>
	</table>';

	     	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic_id'] . '">' . $txt['gallery_text_returnpicture'] . '</a>
            </div>
        </div>';


}

function template_mature_content()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_maturecontent_warning'], '
		</h3>
  </div>
	<table cellspacing="0" cellpadding="5" border="0" align="center" width="100%" class="tborder">
				<tr class="windowbg">
					<td align="center">' . $txt['gallery_txt_maturecontent_warning2'] . '
					<form method="post" action="' . $scripturl . '?action=gallery;sa=mature">
					<input type="hidden" name="id" value="',$context['gallery_pic_id'],'" />
					<input type="submit" name="submit_yes" value="',$txt['gallery_txt_yes'],'" /> <input type="submit" value="',$txt['gallery_txt_no'],'" />
					</form>

					</td>

				</tr>
	</table>';
}

function template_slideshow()
{
	global $scripturl, $txt, $context,  $user_info, $modSettings, $subcats_linktree, $gallerySettings;

	// Permissions
	$g_manage = allowedTo('smfgallery_manage');

	// Permissions if they are allowed to edit or delete their own gallery pictures.
	$g_edit_own = allowedTo('smfgallery_edit');
	$g_delete_own = allowedTo('smfgallery_delete');


	$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];


	ShowTopGalleryBar();

		echo '<br />
		<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_slideshow'], '
		</h3>
  </div>
		<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">

		<tr  class="windowbg">
			<td align="left" colspan="' . $maxrowlevel . '"><form method="post" action="' . $scripturl . '?action=gallery;sa=slideshow;id=' . $context['gallery_pic_id'] . '">
			' . $txt['gallery_txt_slideshow'] . '
			<select name="interval">
			<option value="5000">' . $txt['gallery_txt_slideshow_interval_5'] . '</option>
			<option value="10000">' . $txt['gallery_txt_slideshow_interval_10'] . '</option>
			<option value="15000">' . $txt['gallery_txt_slideshow_interval_15'] . '</option>
			<option value="30000">' . $txt['gallery_txt_slideshow_interval_30']  .'</option>
			</select>
			<input type="submit" value="' .$txt['gallery_txt_slideshow_change_interval'] . '" />
			</form>
			</td>
		</tr>
		<tr  class="windowbg">
			<td align="center" colspan="' . $maxrowlevel . '"><b>' . $txt['gallery_txt_slideshow_click']. '</b></td>
		</tr>
';

		$rowlevel = 0;

		if (!isset($context['gallery_cat_norate']))
			$context['gallery_cat_norate'] = 0;

		foreach($context['gallery_pics'] as $row)
		{
			if ($row['mature'] == 1)
			{
				if (CanViewMature() == false)
					$row['thumbfilename'] = 'mature.gif';
			}


			if ($rowlevel == 0)
				echo '<tr class="windowbg">';

			$setupRow = array(
			'showhighslide' => 1,
			'g_manage' => $g_manage,
			'g_edit_own' => $g_edit_own,
			'g_delete_own' => $g_delete_own
			);

			ShowImageItem($row,$setupRow);


			if ($rowlevel < ($maxrowlevel-1))
				$rowlevel++;
			else
			{
				echo '</tr>';
				$rowlevel = 0;
			}
		}

		if ($rowlevel != 0)
		{
			if (($maxrowlevel - $rowlevel) > 0)
				echo '<td colspan="' .($maxrowlevel - $rowlevel) .'"></td>';

			echo '</tr>';
		}

		// Show return to gallery link and Show add picture if they can

		echo '
			</table><br />';

			     	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="', $scripturl, '?action=gallery">', $txt['gallery_text_returngallery'], '</a>
            </div>
        </div>';


}

function template_gallery_above()
{
}

function template_gallery_below()
{

	echo '
<br />';

	// Do NOT CHANGE THIS CODE UNLESS you have COPYRIGHT Link Removal
	//http://www.smfhacks.com/gallery-linkremoval.php

	//Copyright link must remain. To remove you need to purchase link removal at smfhacks.com
	$showInfo = GalleryCheckInfo();

	if ($showInfo == true)
	echo '<div align="center"><!--Link required unless copyright removal purchase.--><span class="smalltext">Powered by <a href="https://www.smfhacks.com/smf-gallery-pro.php" target="blank">SMF Gallery Pro</a></span><!--End Copyright link--></div>';


}

function template_catpermedit()
{
	global $txt, $scripturl, $context;

	ShowTopGalleryBar();


	echo '
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="titlebg">
			<td>' .$txt['gallery_text_catperm'] . '</td>
		</tr>
		<tr class="windowbg2">
		<td>
		<form method="post" action="' . $scripturl . '?action=gallery;sa=catpermedit2">
		<table align="center" class="tborder">
		<tr class="titlebg">
			<td colspan="2">'  . $txt['gallery_text_edit_permissions'] . '</td>
		</tr>
	   <tr class="windowbg2">
			  	<td align="right"><input type="checkbox" name="view"  ' . ($context['gallery_catperm_edit']['view'] == 1 ? ' checked="checked" ' : '') .  '/></td>
			  	<td><b>' . $txt['gallery_perm_view'] .'</b></td>
			  </tr>
			  <tr class="windowbg2">
			  	<td align="right"><input type="checkbox" name="add" ' . ($context['gallery_catperm_edit']['addpic'] == 1 ? ' checked="checked" ' : '') .  ' /></td>
			  	<td><b>' . $txt['gallery_perm_add'] .'</b></td>
			  </tr>
			  <tr class="windowbg2">
			  	<td align="right"><input type="checkbox" name="edit" ' . ($context['gallery_catperm_edit']['editpic'] == 1 ? ' checked="checked" ' : '') .  ' /></td>
			  	<td><b>' . $txt['gallery_perm_edit'] .'</b></td>
			  </tr>
			  <tr class="windowbg2">
			  	<td align="right"><input type="checkbox" name="delete" ' . ($context['gallery_catperm_edit']['delpic'] == 1 ? ' checked="checked" ' : '') .  ' /></td>
			  	<td><b>' . $txt['gallery_perm_delete'] .'</b></td>
			  </tr>
			  <tr class="windowbg2">
			  	<td align="right"><input type="checkbox" name="addcomment" ' . ($context['gallery_catperm_edit']['addcomment'] == 1 ? ' checked="checked" ' : '') .  ' /></td>
			  	<td><b>' . $txt['gallery_perm_addcomment'] .'</b></td>
			  </tr>
			 <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="addvideo" ' . ($context['gallery_catperm_edit']['addvideo'] == 1 ? ' checked="checked" ' : '') .  ' /></td>
				<td><b>' . $txt['gallery_perm_addvideo'] .'</b></td>
			  </tr>

			 <tr class="windowbg2">
				<td align="right"><input type="checkbox" name="viewimagedetail" ' . ($context['gallery_catperm_edit']['viewimagedetail'] == 1 ? ' checked="checked" ' : '') .  ' /></td>
				<td><b>' . $txt['gallery_perm_viewimagedetail'] .'</b></td>
			  </tr>

			  <tr class="windowbg2">
			  	<td align="center" colspan="2">
			  	<input type="hidden" name="permid" value="' . $context['gallery_perm_id'] . '" />
			  	<input type="submit" value="' . $txt['gallery_text_edit_permissions'] . '" /></td>

			  </tr>
		</table>
		</form>
		</td>
		</tr>
	</table>
		';

}

function template_follow_member()
{
	global $scripturl, $context;

	ShowTopGalleryBar();

			echo '<div class="tborder">
		<form method="post" action="' . $scripturl . '?action=gallery;sa=watchuser">
		<table border="0" cellpadding="0" cellspacing="0"  width="100%">
		  <tr class="catbg">
			<td width="50%" align="center"><b>' . $context['page_title']. '</b></td>
		  </tr>
		  <tr class="windowbg2">
			<td align="center"><b>' . $context['gallery_watch_message'] . '</b>&nbsp;</td>

		  </tr>
		  <tr>
			<td align="center" class="windowbg2">
			<input type="hidden" name="author" value="' . $context['gallery_author_id'] . '" />
			<input type="hidden" name="id" value="' . $context['gallery_return_id'] . '" />
			<input type="submit" value="' . $context['page_title'] . '" name="submit" /></td>
		  </tr>
		</table>
		</form></div>';
}

function template_mywatchlist()
{
	global $txt, $context, $user_info, $scripturl;

	ShowTopGalleryBar();

		$context['gallery']['buttons_set']['mywatchlist'] =  array(
								'text' => 'gallery_txt_mywatchlist',
								'url' => $scripturl . '?action=gallery;sa=mywatchlist',
								'lang' => true,
								'is_selected' => true,
						);

		$context['gallery']['buttons_set']['whowatchme'] =  array(
								'text' => 'gallery_txt_who_watch_me',
								'url' => $scripturl . '?action=gallery;sa=whowatchme',
								'lang' => true,
								'is_selected' => true,
						);


		echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
							<tr>
								<td style="padding-right: 1ex;" align="right">
								<table cellpadding="0" cellspacing="0" align="left">
										<tr>
										<td align="right">
							', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
								</td>
							</tr>
								</table>
					</td>
							</tr>
						</table>';



	echo '<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_mywatchlist'], '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tborder" align="center">
				<tr class="titlebg">
					<td align="center">' . $txt['gallery_app_membername'] . '</td>
					<td align="center">' . $txt['gallery_text_options'] . '</td>
				</tr>';

	foreach($context['gallery_mywatchlist'] as $row)
	{
		echo '<tr class="windowbg2">
				<td align="center"><a href="' . $scripturl . '?action=profile;u=' . $row['id_author'] . '">'  . $row['real_name'] . '</a></td>
					  <td align="center"><a href="' . $scripturl . '?action=gallery;sa=watchuser;memid=' . $row['id_author'] . '">'  . $txt['gallery_txt_unfollow_user'] . '</a></td>
			  </tr>';
	}
	echo '
		</table>';



}

function template_whowatchme()
{
	global $txt, $context, $user_info, $scripturl;

	ShowTopGalleryBar();

		$context['gallery']['buttons_set']['mywatchlist'] =  array(
								'text' => 'gallery_txt_mywatchlist',
								'url' => $scripturl . '?action=gallery;sa=mywatchlist',
								'lang' => true,
								'is_selected' => true,
						);

		$context['gallery']['buttons_set']['whowatchme'] =  array(
								'text' => 'gallery_txt_who_watch_me',
								'url' => $scripturl . '?action=gallery;sa=whowatchme',
								'lang' => true,
								'is_selected' => true,
						);


		echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
							<tr>
								<td style="padding-right: 1ex;" align="right">
								<table cellpadding="0" cellspacing="0" align="left">
										<tr>
										<td align="right">
							', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
								</td>
							</tr>
								</table>
					</td>
							</tr>
						</table>';



	echo '<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_who_watch_me'], '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tborder" align="center">
				<tr class="titlebg">
					<td align="center">' . $txt['gallery_app_membername'] . '</td>
				</tr>';

	foreach($context['gallery_whowwatchme'] as $row)
	{
		echo '<tr class="windowbg2">
				<td align="center"><a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a></td>
			  </tr>';
	}
	echo '
		</table>';



}

function template_copy_image()
{
	global $txt, $context, $scripturl;

echo '<form method="post" name="ftpform" action="',$scripturl,'?action=gallery;sa=copyimage2">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_copy_item'], '
		</h3>
  </div>
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
	<tr>
						<td align="right">',$txt['gallery_text_category'],'</td>
						<td><select name="cat">
						<option value="0">',$txt['gallery_ftp_selectcategory'],'</option>';
						//DD Edit Removed selected="selected"

	 	foreach ($context['gallery_cat'] as $i => $category)
		{
			echo '<option value="' . $category['id_cat']  . '">' . $category['title'] . '</option>';
		}

	 echo '</select></td>
					</tr>
					<tr>
						<td align="right">',$txt['gallery_user_title2'],'</td>
						<td><select name="usercat">
						<option value="0" selected="selected">',$txt['gallery_ftp_selectcategory'],'</option>';

	 	foreach ($context['gallery_cat2'] as $i => $category)
		{
			echo '<option value="' . $category['ID_CAT']  . '">' .$category['real_name'] . ' => ' . $category['title'] . '</option>';
		}

	 echo '</select>
	 </td>
	</tr>
<tr>
	<td colspan="2" align="center">
	<input type="hidden" name="id" value="',$context['gallery_pic_id'],'" />
	<input type="submit" value="',$txt['gallery_txt_copy_item2'],'" />
	</td>
</tr>
</table>
</form>';

}

function template_myfavorities()
{
	global $txt, $scripturl, $user_info, $modSettings, $context;

	$g_add = allowedTo('smfgallery_add');
	$g_manage = allowedTo('smfgallery_manage');
	$g_edit_own = allowedTo('smfgallery_edit');
	$g_delete_own = allowedTo('smfgallery_delete');

	ShowTopGalleryBar();

	$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_myfavorites'], '
		</h3>
  </div>
	<table cellspacing="0" cellpadding="10" border="0" align="center" width="100%" class="tborder">
';

	$rowlevel = 0;


	// Show page listing
	if (count($context['gallery_myfavorites']) > 0)
	{
		echo '<tr class="catbg2">
				<td align="left" colspan="' . $maxrowlevel . '">
				' ;

				echo $context['page_index'];

		echo '
				</td>
			</tr>';
	}

	// Check if it is the user ids gallery mainly to show unapproved pictures or not

	foreach($context['gallery_myfavorites'] as $row)
	{
		if ($rowlevel == 0)
			echo '<tr class="windowbg">';

		echo '<td align="center"><a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '"><img src="' . $modSettings['gallery_url'] . $row['thumbfilename']  . '" alt="" /></a><br />';


		echo '<span class="smalltext">' . $txt['gallery_text_views'] . $row['views'] . '<br />';
		echo $txt['gallery_text_filesize'] . gallery_format_size($row['filesize'], 2) . '<br />';
		echo $txt['gallery_text_date'] . timeformat($row['date']) . '<br />';
		echo $txt['gallery_text_comments'] . ' (<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '">' . $row['commenttotal'] . '</a>)<br />';


					// Disable member color link
					if (!empty($modSettings['gallery_disable_membercolorlink']))
						$row['online_color'] = '';

		echo $txt['gallery_text_by'] . ' <a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '"' . (!empty($row['online_color']) ? ' style="color: ' . $row['online_color'] . ';" ' :'' ) . '>'  . $row['real_name'] . '</a><br />';
		if ($g_manage)
			if ($row['approved'] == 1)
				echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=unapprove;id=' . $row['id_picture'] . '">' . $txt['gallery_text_unapprove'] . '</a>';
			else
				echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=approve;id=' . $row['id_picture'] . '">' . $txt['gallery_text_approve'] . '</a>';


		if ($g_manage || $g_edit_own && $row['id_member'] == $user_info['id'])
			echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=edit;id=' . $row['id_picture'] . '">' . $txt['gallery_text_edit'] . '</a>';
		if ($g_manage || $g_delete_own && $row['id_member'] == $user_info['id'])
			echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=delete;id=' . $row['id_picture'] . '">' . $txt['gallery_text_delete'] . '</a>';

		if ($g_manage  && $row['id_member'] == $user_info['id'])
			echo '&nbsp;<a href="' . $scripturl . '?action=gallery;sa=unfavorite;id=' . $row['id_picture'] . '">' . $txt['gallery_txt_unfavorite2'] . '</a>';

		echo '</span></td>';


		if ($rowlevel < ($maxrowlevel-1))
			$rowlevel++;
		else
		{
			echo '</tr>';
			$rowlevel = 0;
		}
	}

	if ($rowlevel !=0)
	{
		if (($maxrowlevel - $rowlevel) > 0)
			echo '<td colspan="' .($maxrowlevel - $rowlevel) .'"></td>';

		echo '</tr>';
	}


	// Show page listing

		echo '<tr class="catbg2">
				<td align="left" colspan="' . $maxrowlevel . '">
				';

				echo $context['page_index'];

		echo '
				</td>
			</tr>';



	echo '</table><br />';

 	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

	// If allowed to have a personal gallery images
	if (allowedTo('smfgallery_usergallery'))
		echo '
		<a href="', $scripturl, '?action=gallery;sa=add;u=', $user_info['id'], '">', $txt['gallery_text_adduserpicture'], '</a>&nbsp; - &nbsp; ';


	if ($g_add)
		echo '
		<a href="' . $scripturl . '?action=gallery;sa=add">' . $txt['gallery_text_addpicture'] . '</a>&nbsp; - &nbsp; ';


  // Check if allowed to add video
  if (allowedTo('smfgalleryvideo_add'))
		echo '<a href="' . $scripturl . '?action=gallery;sa=addvideo">' . $txt['gallery_form_addvideo'] .'</a>&nbsp; - &nbsp; ';


	echo '
		<a href="', $scripturl, '?action=gallery">', $txt['gallery_text_returngallery'], '</a>
            </div>
        </div>';


}

function template_relatedindex()
{
	global $scripturl, $context, $txt;

	if (empty($context['continue_countdown']))
		$context['continue_countdown'] = 3;

	if (empty($context['continue_get_data']))
		$context['continue_get_data'] ='';

	if (empty($context['continue_post_data']))
		$context['continue_post_data'] ='';


	echo '<b>' . $txt['gallery_txt_rebuildindex']. '</b><br />';

		if (!empty($context['continue_percent']))
		echo '
					<div style="padding-left: 20%; padding-right: 20%; margin-top: 1ex;">
						<div style="font-size: 8pt; height: 12pt; border: 1px solid black; background-color: white; padding: 1px; position: relative;">
							<div style="padding-top: ',  $context['browser']['is_konqueror'] ? '2pt' : '1pt', '; width: 100%; z-index: 2; color: black; position: absolute; text-align: center; font-weight: bold;">', $context['continue_percent'], '%</div>
							<div style="width: ', $context['continue_percent'], '%; height: 12pt; z-index: 1; background-color: red;">&nbsp;</div>
						</div>
					</div>';

	echo '<form action="' . $scripturl . '?action=gallery;sa=rebuildrelated;' , $context['continue_get_data'], '" method="post" accept-charset="', $context['character_set'], '" style="margin: 0;" name="autoSubmit" id="autoSubmit">
				<div style="margin: 1ex; text-align: right;"><input type="submit" name="cont" value="', $txt['gallery_txt_continue'], '" class="button_submit" /></div>
				', $context['continue_post_data'], '


			</form>

			<script type="text/javascript"><!-- // --><![CDATA[
		var countdown = ', $context['continue_countdown'], ';
		doAutoSubmit();

		function doAutoSubmit()
		{
			if (countdown == 0)
				document.forms.autoSubmit.submit();
			else if (countdown == -1)
				return;

			document.forms.autoSubmit.cont.value = "',$txt['gallery_txt_continue'] , ' (" + countdown + ")";
			countdown--;

			setTimeout("doAutoSubmit();", 1000);
		}
	// ]]></script>';

}

function template_selectcat()
{
	global $context, $txt, $scripturl;

	ShowTopGalleryBar();

	echo '<form method="post" action="' . $scripturl . '?action=gallery&sa=selectcat2">
		<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_text_title'], '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_category'] . '</b>&nbsp;</td>
  	<td><select name="cat" id="cat">
  	<option value="0">',$txt['gallery_text_choose_cat'],'</option>
  	';

 	foreach ($context['gallery_cat'] as $i => $category)
		echo '<option value="' . $category['ID_CAT']  . '" ' .'>' . $category['title'] . '</option>';


 echo '</select>
  	</td>
  </tr>
    <tr class="windowbg2">
    	<td colspan="2" align="center">
    	' . $txt['gallery_txt_upload_option'] . '<input type="radio" name="addmode" value="add" checked="checked"> ' . ($context['video_select'] == 0 ? $txt['gallery_txt_upload_option1'] : $txt['gallery_txt_upload_option_media1'])  . ' <input type="radio" name="addmode" value="bulk"> ' .  ($context['video_select'] == 0 ? $txt['gallery_txt_upload_option2'] : $txt['gallery_txt_upload_option_media2']) . '
    	</td>
  </tr>

   <tr class="windowbg2">
	<td colspan="2" align="center">
	<input type="hidden" name="video" value="' . $context['video_select'] . '" />
	<input type="submit" value="' . ($context['video_select'] == 0 ? $txt['gallery_form_addpicture'] : $txt['gallery_form_addvideo']) . '" name="submit" />
	</td>
	</tr>

  </table></form><br />';


 if (!empty($context['usergallery_memid']))
 {
	echo '<form method="post" action="' . $scripturl . '?action=gallery&sa=selectcat2">
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_user_title2'], '
		</h3>
  </div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_category'] . '</b>&nbsp;</td>
  	<td><select name="cat" id="cat">
  	<option value="-1">',$txt['gallery_text_choose_cat'],'</option>
  	';

 	foreach ($context['gallery_user_cat'] as $i => $category)
		echo '<option value="' . $category['ID_CAT']  . '" ' .'>' . $category['title'] . '</option>';


 echo '</select>
  	</td>
  </tr>
    <tr class="windowbg2">
    	<td colspan="2" align="center">
    	' . $txt['gallery_txt_upload_option'] . '<input type="radio" name="addmode" value="add" checked="checked"> ' . ($context['video_select'] == 0 ? $txt['gallery_txt_upload_option1'] : $txt['gallery_txt_upload_option_media1']) . ' <input type="radio" name="addmode" value="bulk"> ' . ($context['video_select'] == 0 ? $txt['gallery_txt_upload_option2'] : $txt['gallery_txt_upload_option_media2']) . '
    	</td>
  </tr>
   <tr class="windowbg2">
	<td  colspan="2" align="center">
	<input type="hidden" name="video" value="' . $context['video_select'] . '" />
	<input type="hidden" name="u" value="'. $context['usergallery_memid'] . '" />
	<input type="submit" value="' . ($context['video_select'] == 0 ? $txt['gallery_form_addpicture'] : $txt['gallery_form_addvideo']) . '" name="submit" />
	</td>
	</tr>
  </table></form>';
 }


}

function template_whofavorited()
{
	global $txt, $context, $user_info, $scripturl;

	ShowTopGalleryBar();


	echo '
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $context['gallery_picture_title'], '
		</h3>
  </div>
	<table cellspacing="0" cellpadding="5" border="0" align="center" width="100%" class="tborder">
	';

	foreach($context['gallery_whofavorited'] as $row)
	{
		echo '<tr class="windowbg2">
				<td align="center"><a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a></td>
			  </tr>';
	}
	echo '
	</table>';

			     	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_picture_id'] . '">' . $txt['gallery_text_returnpicture'] . '</a>
            </div>
        </div>';



}

function template_twittersettings()
{
	global $txt, $scripturl,  $context, $modSettings;

	// Settings Admin Tabs
	SettingsAdminTabs();

		echo '
	<div id="moderationbuttons" class="margintop">
		', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
	</div><br /><br /><br />';


		echo '
	<form method="post" action="',$scripturl,'?action=gallery;sa=twitter2">
	<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_twitter'], '
		</h3>
  </div>
  <div class="windowbg noup">
	<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
	<tr>
			<td colspan="2">',$txt['gallery_twitter_step1'],'
			</td>
		</tr>

	<tr>
	<td colspan="2">' . $txt['gallery_twitter_step2']  . (!empty($modSettings['gallery_oauth_token']) ? "<b>" . $txt['gallery_twitter_step2_part2'] . "</b> " : '') . $txt['gallery_twitter_signinwithtwitter'] . '
	<a href="',$scripturl,'?action=gallery;sa=twittersignin"><img src="' . $modSettings['gallery_url'] . '/lighter.png" alt="' . $txt['gallery_twitter_signinwithtwitter'] . '"/></a>
	</td>
	</tr>
	<tr>
		<td>',$txt['gallery_consumer_key'],'</td>
		<td><input type="text" name="gallery_consumer_key" size="50" value="' . $modSettings['gallery_consumer_key'] . '" /></td>
	</tr>
	<tr>
		<td>',$txt['gallery_consumer_secret'],'</td>
		<td><input type="text" name="gallery_consumer_secret" size="50" value="' . $modSettings['gallery_consumer_secret'] . '" /></td>
	</tr>
		<tr>
		<td valign="top" colspan="2" align="center"><input type="submit" value="' . $txt['gallery_save_settings'] . '" />
		</td>
		</tr>
	</table>
	</div>
	</form>';

}

function template_gallerycopyright()
{
	global $txt, $scripturl, $context, $boardurl, $modSettings;

	// Settings Admin Tabs
	SettingsAdminTabs();

		echo '
	<div id="moderationbuttons" class="margintop">
		', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
	</div><br /><br /><br />';

	$modID = 1;

	$urlBoardurl = urlencode(base64_encode($boardurl));

		echo '
	<form method="post" action="',$scripturl,'?action=admin;area=gallery;sa=copyright;save=1">
	<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_txt_copyrightremoval'], '
		</h3>
  </div>
  <div class="windowbg noup">
<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
	<tr>
		<td valign="top" align="right">',$txt['gallery_txt_copyrightkey'],'</td>
		<td><input type="text" name="gallery_copyrightkey" size="50" value="' . $modSettings['gallery_copyrightkey'] . '" />
		<br />
		<a href="https://www.smfhacks.com/copyright_removal.php?mod=' . $modID .  '&board=' . $urlBoardurl . '" target="_blank">' . $txt['gallery_txt_ordercopyright'] . '</a>
		</td>
	</tr>
	<tr>
		<td colspan="2">' . $txt['gallery_txt_copyremovalnote'] . '</td>
	</tr>
	<tr>
		<td valign="top" colspan="2" align="center"><input type="submit" value="' . $txt['gallery_save_settings'] . '" />
		</td>
		</tr>
	</table>
	</div>
	</form>
	';

}

function template_addvideo()
{
	global $scripturl, $modSettings, $txt, $context, $settings, $gallerySettings;

	ShowTopGalleryBar();

	// Load the spell checker?
	if ($context['show_spellchecking'])
		echo '<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/scripts/spellcheck.js"></script>';

	echo '<form method="post" enctype="multipart/form-data" name="picform" id="picform" action="' . $scripturl . '?action=gallery&sa=addvideo2"  onsubmit="submitonce(this);">
<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_form_addvideo'], '
		</h3>
  </div>
';

  if (!empty($context['gallery_errors']))
  {
	echo '<div class="errorbox" id="errors">
						<dl>
							<dt>
								<strong style="" id="error_serious">' . $txt['gallery_errors_addpicture'] . '</strong>
							</dt>
							<dt class="error" id="error_list">';

							foreach($context['gallery_errors'] as $msg)
								echo $msg . '<br />';

							echo '
							</dt>
						</dl>
					</div>';
    }

echo '
<div class="information">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_title'] . '</b>&nbsp;</td>
  	<td><input type="text" name="title" tabindex="1" size="80" /></td>
  </tr>
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_category'] . '</b>&nbsp;</td>
  	<td><select name="cat" id="cat" onchange="changeCat(cat.options[cat.selectedIndex].value)">';

 	foreach ($context['gallery_cat'] as $i => $category)
		echo '<option value="' . $category['ID_CAT']  . '" ' . (($context['gallery_cat_id'] == $category['ID_CAT']) ? ' selected="selected"' : '') .'>' . $category['title'] . '</option>';

 echo '</select>
  	</td>
  </tr>
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_description'] . '</b>&nbsp;</td>
  	<td><table>
   ';

 	if (!function_exists('getLanguages'))
	{
// Showing BBC?
	if ($context['show_bbc'])
	{
		echo '
							<tr class="windowbg2">

								<td colspan="2" align="center">
									', template_control_richedit($context['post_box_name'], 'bbc'), '
								</td>
							</tr>';
	}

	// What about smileys?
	if (!empty($context['smileys']['postform']))
		echo '
							<tr class="windowbg2">

								<td colspan="2" align="center">
									', template_control_richedit($context['post_box_name'], 'smileys'), '
								</td>
							</tr>';

	// Show BBC buttons, smileys and textbox.
	echo '
							<tr class="windowbg2">

								<td colspan="2" align="center">
									', template_control_richedit($context['post_box_name'], 'message'), '
								</td>
							</tr>';
	}
	else
	{
		echo '
								<tr class="windowbg2">
		<td colspan="2">';
			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Show BBC buttons, smileys and textbox.
		echo '
					', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');


		echo '</td></tr>';
	}

   echo '</table>';

	 	if ($context['show_spellchecking'])
	 		echo '
	 									<br /><input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'picform\', \'description\');" />';

echo '
  	</td>
  </tr>
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_keywords'] . '</b>&nbsp;</td>
  	<td><input type="text" name="keywords" size="75" maxlength="100" /></td>
  </tr>
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_previewpic'] . '</b>&nbsp;</td>
	<td><input type="file" size="48" name="picture" /></td>
  </tr>';


    if (empty($modSettings['gallery_video_disableupload']))
    {
	   echo '<tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_videofile'] . '</b>&nbsp;</td>
	<td><input type="file" size="48" name="video" />';

    if (!empty($modSettings['gallery_video_maxfilesize']))
    echo '
    <br />
    ' . $txt['gallery_video_maxfilesize'] . ' ' .  gallery_format_size($modSettings['gallery_video_maxfilesize'],2);

    echo '</td>
  </tr>';
  }

  if ($modSettings['gallery_video_allowlinked'])
  echo '
  <tr class="windowbg2">
  	<td valign="top" align="right"><b>' . $txt['gallery_form_videourl']  . '</b>&nbsp;</td>
	<td><input type="text" size="75" name="videourl" />
	<br />',$txt['gallery_form_videourl2'],'
	</td>
  </tr>';

  echo '
  <tr class="windowbg2">
  	<td colspan="2"><hr /></td>
  </tr>';

    if (empty($context['gallery_user_id']))
    {
    	foreach($context['gallery_addvideo_customfields'] as $row2)
    	{
    		echo '<tr class="windowbg2">
     	 		<td align="right"><b>', $row2['title'], ($row2['is_required'] ? '<font color="#FF0000">*</font>' : ''), '</b></td>
     	 		<td><input type="text" name="cus_', $row2['ID_CUSTOM'],'" value="' , $row2['defaultvalue'], '" /></td>
     	 	</tr>';
    	}

     }

	echo '
	   <tr class="windowbg2">
		<td align="right"><b>' . $txt['gallery_form_additionaloptions'] . '</b>&nbsp;</td>
		<td><input type="checkbox" name="sendemail" checked="checked" /><b>' . $txt['gallery_notify_title'] .'</b>';

	if ($modSettings['gallery_allow_mature_tag'])
	{
		echo '
	   <input type="checkbox" name="markmature" /><b>' .$txt['gallery_txt_mature'] .'</b>
  ';
	}

	if ($gallerySettings['gallery_set_allowratings'])
	{
		echo '<br />
  	   <input type="checkbox" name="allow_ratings" checked="checked" /><b>' .$txt['gallery_txt_allow_ratings'] .'</b>
  ';
	}

  if ($gallerySettings['gallery_set_allow_copy'])
  {
  	echo '<br />
	   <input type="checkbox" name="copyimage" />',$txt['gallery_txt_copy_item'],'
	  ';
  }


	echo '</td>
	  </tr>
  ';

	if ($modSettings['gallery_commentchoice'])
	{
		echo '
	<tr class="windowbg2">
		<td align="right">&nbsp;</td>
		<td><input type="checkbox" name="allowcomments" checked="checked" /><b>' . $txt['gallery_form_allowcomments'] .'</b></td>
	</tr>';
	}


echo '
  <tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<input type="hidden" name="userid" value="'. $context['gallery_user_id'] . '" />
	<input type="submit" value="' .$txt['gallery_form_addvideo'] . '" name="submit" /><br />';

  	if (!allowedTo('smfgallery_autoapprove'))
  		echo $txt['gallery_form_notapproved'];

echo '
	</td>
  </tr>
</table>
</div>
</form>
<script type="text/javascript">
function changeCat(myCategory)
{
if (myCategory != ' . $context['gallery_cat_id'] . ')
	document.location = "', $scripturl, '?action=gallery;sa=addvideo;cat=" + myCategory + "' . (empty($context['gallery_user_id']) ? '' : ';u=' . $context['gallery_user_id']) . '";

}
</script>
';

	if ($context['show_spellchecking'])
		echo '<form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value="" /></form>';

}

function template_editvideo()
{
	global $scripturl, $modSettings, $txt, $context, $settings, $gallerySettings;

	$g_manage = allowedTo('smfgallery_manage');

	ShowTopGalleryBar();

	// Load the spell checker?
	if ($context['show_spellchecking'])
		echo '
									<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/scripts/spellcheck.js"></script>';

	echo '<form method="post" enctype="multipart/form-data" name="picform" id="picform" action="' . $scripturl . '?action=gallery&sa=editvideo2" onsubmit="submitonce(this);">
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_form_editvideo'], '
		</h3>
  </div>
<div class="information">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_title'] . '</b>&nbsp;</td>
  	<td><input type="text" tabindex="1" size="80" name="title" value="' . $context['gallery_pic']['title'] . '" /></td>
  </tr>
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_category'] . '</b>&nbsp;</td>
  	<td><select name="cat" id="cat" onchange="changeCat(cat.options[cat.selectedIndex].value)">';

 	foreach ($context['gallery_cat'] as $i => $category)
		echo '<option value="' . $category['ID_CAT']  . '" ' . (($context['gallery_pic']['selected_ID_CAT'] == $category['ID_CAT']) ? ' selected="selected"' : '') .'>' . $category['title'] . '</option>';

 echo '</select>
  	</td>
  </tr>
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_description'] . '</b>&nbsp;</td>
  	<td><table>
   ';

 	if (!function_exists('getLanguages'))
	{
// Showing BBC?
	if ($context['show_bbc'])
	{
		echo '
							<tr class="windowbg2">

								<td colspan="2" align="center">
									', template_control_richedit($context['post_box_name'], 'bbc'), '
								</td>
							</tr>';
	}

	// What about smileys?
	if (!empty($context['smileys']['postform']))
		echo '
							<tr class="windowbg2">

								<td colspan="2" align="center">
									', template_control_richedit($context['post_box_name'], 'smileys'), '
								</td>
							</tr>';

	// Show BBC buttons, smileys and textbox.
	echo '
							<tr class="windowbg2">

								<td colspan="2" align="center">
									', template_control_richedit($context['post_box_name'], 'message'), '
								</td>
							</tr>';
	}
		else
	{
		echo '
								<tr class="windowbg2">
		<td colspan="2">';
			// Showing BBC?
		if ($context['show_bbc'])
		{
			echo '
					<div id="bbcBox_message"></div>';
		}

		// What about smileys?
		if (!empty($context['smileys']['postform']) || !empty($context['smileys']['popup']))
			echo '
					<div id="smileyBox_message"></div>';

		// Show BBC buttons, smileys and textbox.
		echo '
					', template_control_richedit($context['post_box_name'], 'smileyBox_message', 'bbcBox_message');


		echo '</td></tr>';
	}

   echo '</table>';

	 	if ($context['show_spellchecking'])
	 		echo '
	 									<br /><input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'picform\', \'description\');" />';

echo '</td>
  </tr>
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_keywords'] . '</b>&nbsp;</td>
  	<td><input type="text" name="keywords" maxlength="100" size="75" value="' . $context['gallery_pic']['keywords'] . '" /></td>
  </tr>
  <tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_previewpic'] . '</b>&nbsp;</td>
	<td><input type="file" size="48" name="picture" /></td>
  </tr>';


if (empty($modSettings['gallery_video_disableupload']))
{
  echo '<tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_videofile'] . '</b>&nbsp;</td>
	<td><input type="file" size="48" name="video" />';

    if (!empty($modSettings['gallery_video_maxfilesize']))
    echo '
    <br />
    ' . $txt['gallery_video_maxfilesize'] . ' ' .  gallery_format_size($modSettings['gallery_video_maxfilesize'],2);

    echo '</td>
  </tr>';

}

if ($modSettings['gallery_video_allowlinked'])
echo '
  <tr class="windowbg2">
  	<td valign="top" align="right"><b>' . $txt['gallery_form_videourl']  . '</b>&nbsp;</td>
	<td><input type="text" size="75" name="videourl"  value="' . ($context['gallery_pic']['type'] == 1 ? '' : $context['gallery_pic']['videofile']) . '" />
	<br />',$txt['gallery_form_videourl2'],'
	</td>
  </tr>';


echo '<tr>
  	<td colspan="2" class="windowbg2"><hr /></td>
  </tr>';


	foreach($context['gallery_video_customfields'] as $row2)
	{
		echo '<tr class="windowbg2">
 	 		<td align="right"><b>', $row2['title'], ($row2['is_required'] ? '<font color="#FF0000">*</font>' : ''), '</b></td>
 	 		<td><input type="text" name="cus_', $row2['ID_CUSTOM'],'" value="' , $row2['value'], '" /></td>
 	 	</tr>';
	}


 echo '
   	   <tr class="windowbg2">
		<td align="right"><b>' . $txt['gallery_form_additionaloptions'] . '</b>&nbsp;</td>
		<td><input type="checkbox" name="sendemail" ' . ($context['gallery_pic']['sendemail'] ? 'checked="checked"' : '' ) . ' /><b>' . $txt['gallery_notify_title'] .'</b>';

 	if ($modSettings['gallery_allow_mature_tag'])
		echo '<input type="checkbox" name="markmature" ' . ($context['gallery_pic']['mature'] ? 'checked="checked"' : '' ) . ' /><b>' .$txt['gallery_txt_mature'] .'</b>';


 echo '</td>
	  </tr>';

	if ($context['is_usergallery'] == true)
	{
		echo '
	   <tr class="windowbg2">
		<td align="right">&nbsp;</td>
		<td><input type="checkbox" name="featured" ' . ($context['gallery_pic']['featured'] ? 'checked="checked"' : '' ) . ' /><b>',$txt['gallery_txt_featured_image'],'</b></td>
	  </tr>';
	}

  if ($modSettings['gallery_commentchoice'])
  {
	echo '
	   <tr class="windowbg2">
		<td align="right">&nbsp;</td>
		<td><input type="checkbox" name="allowcomments" ' . ($context['gallery_pic']['allowcomments'] ? 'checked="checked"' : '' ) . ' /><b>',$txt['gallery_form_allowcomments'],'</b></td>
	  </tr>';
  }

  	if ($gallerySettings['gallery_set_allowratings'])
	{
		echo ' <tr class="windowbg2">
		<td align="right">&nbsp;</td>
  	  	 <td><input type="checkbox" name="allow_ratings" ' . ($context['gallery_pic']['allowratings'] ? 'checked="checked"' : '' ) . ' /><b>' .$txt['gallery_txt_allow_ratings'] .'</b>
  	   	</td>
  	   </tr>
  ';
	}


  // If the user can manage the gallery give them the option to change the picture owner.
  if ($context['is_usergallery'] == false && $g_manage == true)
  {
	  echo '<tr class="windowbg2">
	  <td align="right">', $txt['gallery_text_changeowner'], '</td>
	  <td><input type="text" name="pic_postername" id="pic_postername" value="" />
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/members.png" alt="', $txt['find_members'], '" /></a>
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);">', $txt['find_members'], '</a>
	  </td>
	  </tr>
	  <tr class="windowbg2">
	  <td colspan="2" align="center">
	  ',$txt['gallery_txt_picturemoveoptions'],'<a href="',$scripturl,'?action=gallery;sa=changegallery;mv=touser;id=',$context['gallery_pic']['id_picture'], '">',$txt['gallery_movetousergallery'],'</a>
	  </td>
	  </tr>
	  ';
  }
  else
  {
  	if ($g_manage == true)
  	{
		echo ' <tr class="windowbg2">
	  <td colspan="2" align="center">
	  ',$txt['gallery_txt_picturemoveoptions'],'<a href="',$scripturl,'?action=gallery;sa=changegallery;mv=togallery;id=',$context['gallery_pic']['id_picture'], '">',$txt['gallery_movetomaingallery'],'</a>
	  </td>
	  </tr>';
  	}

  }

echo '
  <tr class="windowbg2">
	<td width="28%" colspan="2" align="center" class="windowbg2">
	<input type="hidden" name="id" value="' . $context['gallery_pic']['id_picture'] . '" />
	<input type="submit" value="' . $txt['gallery_form_editvideo'] . '" name="submit" /><br />';

  	if (!allowedTo('smfgallery_autoapprove'))
  		echo $txt['gallery_form_notapproved'];

// Show Old Video
echo '<div align="center"><br /><b>' .  $txt['gallery_video_oldvideo'] . '</b><br />';
	// Show the video box
	showvideobox($context['gallery_pic']['videofile']);
echo '</div>';

echo '<div align="center"><br /><b>' . $txt['gallery_text_oldpicture'] . '</b><br />
<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic']['id_picture'] . '" target="blank"><img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['thumbfilename']  . '" /></a><br />
			<span class="smalltext">' . $txt['gallery_text_views']  . $context['gallery_pic']['views'] . '<br />
			' . $txt['gallery_text_filesize']  . gallery_format_size($context['gallery_pic']['filesize'],2) . '<br />
			' . $txt['gallery_text_date'] . $context['gallery_pic']['date'] . '<br />
	</div>
	</td>
  </tr>
</table>
</div>
		</form>
<script type="text/javascript">
function changeCat(myCategory)
{
if (myCategory != ' . $context['gallery_pic']['ID_CAT'] . ')
	document.location = "', $scripturl, '?action=gallery;sa=editvideo;id=' . $context['gallery_pic']['id_picture'] . ';cat=" + myCategory;

}
</script>

		';

	if ($context['show_spellchecking'])
		echo '<form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value="" /></form>';

}

function template_video_settings()
{
	global $scripturl, $modSettings, $txt, $context;

	// Settings Admin Tabs
	SettingsAdminTabs();

		echo '
	<div id="moderationbuttons" class="margintop">
		', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
	</div><br /><br /><br />';

echo '
<form method="post" action="' . $scripturl . '?action=gallery;sa=videoset2">
<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_text_videosettings'], '
		</h3>
  </div>


                <div class="windowbg noup">
				<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4" class="tborder">
				<tr><td width="30%">' . $txt['gallery_video_maxfilesize'] . '</td><td><input type="text" name="gallery_video_maxfilesize" value="' .  $modSettings['gallery_video_maxfilesize'] . '" /> (bytes)</td></tr>
				<tr><td width="30%">' . $txt['gallery_video_playerheight'] . '</td><td><input type="text" name="gallery_video_playerheight" value="' .  $modSettings['gallery_video_playerheight'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_video_playerwidth'] . '</td><td><input type="text" name="gallery_video_playerwidth" value="' .  $modSettings['gallery_video_playerwidth'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_video_filetypes'] . '</td><td><input type="text" name="gallery_video_filetypes" value="' .  $modSettings['gallery_video_filetypes'] . '" size="50" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_txt_embed_default_height'] . '</td><td><input type="text" name="mediapro_default_height" value="' .  $modSettings['mediapro_default_height'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['gallery_txt_embed_default_width'] . '</td><td><input type="text" name="mediapro_default_width" value="' .  $modSettings['mediapro_default_width'] . '" /></td></tr>

				</table>
				<input type="checkbox" name="gallery_video_allowlinked" ' . ($modSettings['gallery_video_allowlinked'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_video_allowlinked'] . '<br />
				<input type="checkbox" name="gallery_video_disableupload" ' . ($modSettings['gallery_video_disableupload'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_video_disableupload'] . '<br />

				<input type="checkbox" name="gallery_video_showdowloadlink" ' . ($modSettings['gallery_video_showdowloadlink'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_video_showdowloadlink'] . '<br />
				<input type="checkbox" name="gallery_video_showbbclinks" ' . ($modSettings['gallery_video_showbbclinks'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_video_showbbclinks'] . '<br />
				';

				if (!is_writable($modSettings['gallery_path'] . 'videos/'))
					echo '<font color="#FF0000"><b>' . $txt['gallery_video_write_error']  . $modSettings['gallery_path'] . 'videos/' . '</b></font>';

				echo '
				<input type="submit" name="savesettings" value="' . $txt['gallery_save_settings'] . '" />

</div>
</form>';

}


function template_modlog()
{
	global $scripturl, $txt, $modSettings, $context;

ModerationAdminTabs();

echo '
<div id="moderationbuttons" class="margintop">
	', DoToolBarStrip($context['gallery']['buttons_set'], 'bottom'), '
</div><br /><br /><br />';


echo '<div class="cat_bar">
		<h3 class="catbg">
		', $txt['gallery_txt_moderationlog'], '
		</h3>
  </div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tborder" align="center">
		<tr class="windowbg2">
			<td>

			<form method="post" action="' . $scripturl . '?action=gallery;sa=emptymodlog">
			<input type="submit" value="' . $txt['gallery_txt_emptymoderationlog'] . '" />
			</form>
			<br />
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="table_grid">
			<thead>
			<tr class="title_bar">
				<th class="lefttext firs_th">' . $txt['gallery_txt_action'] . '</th>
				<th class="lefttext">' . $txt['gallery_app_date'] . '</th>
				<th class="lefttext">' . $txt['gallery_app_membername'] . '</th>
				<th class="lefttext last_th">' . $txt['gallery_txt_ipaddress'] . '</th>
				</tr>
			</thead>';


		$styleClass = 'windowbg2';
			foreach($context['gallery_mod_log_entries'] as $row)
			{

				echo '<tr class="' . $styleClass  . '">';

				// Action Handler
				echo '<td>' ;
				$displayText = $txt['gallery_act_' . $row['action']];

				$displayText = str_replace("%item",'<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['ID_PICTURE'] . '">'  . $row['itemtitle'] . '</a>',$displayText);


				echo $displayText;

				echo '</td>';
				echo '<td>', timeformat($row['logdate']), '</td>';

				if ($row['real_name'] != '')
					echo '<td><a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '">'  . $row['real_name'] . '</a></td>';
				else
					echo '<td>'  . $txt['gallery_guest']. '</td>';

				echo '<td>', $row['ipaddress'], '</td>';

				echo '</tr>';

				if ($styleClass == 'windowbg')
				  $styleClass = 'windowbg2';
				else
				  $styleClass = 'windowbg2';

			}


			echo '<tr class="titlebg">
					<td align="left" colspan="6">
					';



					echo $context['page_index'];

			echo '

					</td>
				</tr>';


		echo '
			</table>
			<br />

			</td>
		</tr>
</table>
			';

}

function ModerationAdminTabs()
{
	global $context, $scripturl;

	// Picture list
	$context['gallery']['buttons_set']['approvelist'] =  array(
							'text' => 'gallery_txt_imagemoderation',
							'url' => $scripturl . '?action=admin;area=gallery;sa=approvelist',
							'lang' => true,
							'is_selected' => true,
					);

	 // Comment List
	 $context['gallery']['buttons_set']['commentlist'] =  array(
							'text' => 'gallery_txt_commentmoderation',
							'url' => $scripturl . '?action=admin;area=gallery;sa=commentlist',
							'lang' => true,
							'is_selected' => true,
					);

	// Moderation Log
	$context['gallery']['buttons_set']['modlog'] =  array(
			'text' => 'gallery_txt_moderationlog',
			'url' => $scripturl . '?action=admin;area=gallery;sa=modlog',
			'lang' => true,

		);

}

function template_customedit()
{
	global $txt, $context, $scripturl;

	ShowTopGalleryBar();

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_custom_editfield'], '
		</h3>
  </div>
<form method="post" enctype="multipart/form-data" name="catform" id="catform" action="', $scripturl, '?action=gallery;sa=cusedit2">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="windowbg2">
	<td width="28%" align="right"><b>', $txt['gallery_custom_title'], '</b>&nbsp;</td>
	<td width="72%"><input type="text" name="title" size="64" maxlength="100" value="', $context['gallery_customfield']['title'], '" /></td>
  </tr>
  <tr class="windowbg2">
	<td width="28%" align="right"><b>',$txt['gallery_custom_default_value'], '</b>&nbsp;</td>
	<td width="72%"><input type="text" name="defaultvalue" size="64" maxlength="100" value="',  $context['gallery_customfield']['defaultvalue'], '" /></td>
  </tr>
  <tr class="windowbg2">
	<td width="28%" align="right"><b>', $txt['gallery_custom_required'], '</b>&nbsp;</td>
	<td width="72%"><input type="checkbox" name="required" ' . ($context['gallery_customfield']['is_required'] == 1 ? ' checked="checked" ' : '') . ' /></td>
  </tr>
  <tr class="windowbg2">
	<td width="28%" align="right"><b>', $txt['gallery_txt_global_field'], '</b>&nbsp;</td>
	<td width="72%"><input type="checkbox" name="globalfield" ' . ($context['gallery_customfield']['ID_CAT'] == 0 ? ' checked="checked" ' : '') . ' /></td>
  </tr>
  <tr class="windowbg2">
	<td colspan="2" align="center"><input type="submit" value="' . $txt['gallery_custom_editfield'] . '" />

	<input type="hidden" name="id" value="',$context['gallery_customfield']['ID_CUSTOM'],'" />
	<input type="hidden" name="catid" value="',$context['gallery_customfield_catid'],'" />

	</td>
  </tr>
  </table>
  </form>';


}

function template_viewlikes()
{
	global $scripturl, $txt, $context;

	ShowTopGalleryBar();

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $txt['gallery_txt_view_likes'], '
		</h3>
  </div>
	<table cellspacing="0" cellpadding="5" border="0" align="center" width="100%" class="tborder">
				<tr class="windowbg">
					<td>' . $context['gallery_likes'] . '</td>
				</tr>
	</table>';

		     	echo '
                    <div class="tborder">
            <div class="roundframe centertext">';

				echo '
				<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic_id'] . '">' . $txt['gallery_text_returnpicture'] . '</a>
            </div>
        </div>';

}

?>