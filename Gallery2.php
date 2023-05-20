 <?php
/*
SMF Gallery Pro Edition
Version 9.0
by: vbgamer45
http://www.smfhacks.com
Copyright 2006-2022 http://www.samsonsoftware.com

############################################
License Information:
SMF Gallery is NOT free software.
This software may not be redistributed.

The pro edition license is good for a single instance / install on a website.
You are allowed only one active install for each license purchase.

Links to http://www.smfhacks.com must remain unless
branding free option is purchased.
#############################################

SMF Gallery Function Information:

	void GalleryMain()
	void GalleryMainView()
	void AddCategory()
	void AddCategory2()
	void EditCategory()
	void EditCategory2()
	void DeleteCategory()
	void DeleteCategory2()
	void ViewPicture()
	void AddPicture()
	void AddPicture2()
	void EditPicture()
	void EditPicture2()
	void DeletePicture();
	void DeletePicture2()
	void ReportPicture()
	void ReportPicture2()
	void AddComment()
	void AddComment2()
	void DeleteComment()
	void AdminSettings()
	void AdminSettings2()
	void AdminCats()
	void CatUp()
	void CatDown()
	void MyImages()
	void RatePicture()
	void ViewRating()
	void DeleteRating()
	void Stats()
	void ImportPictures()
	void ImportPictures2()
	void BulkAdd()
	void BulkAdd2()
	void UpdateUserFileSizeTable($memberid, $filesize = 0)
	void FileSpaceAdmin()
	void FileSpaceList()
	void GenerateXML()
	void RecountFileQuotaTotals($redirect = true)


*/

if (!defined('SMF'))
	die('Hacking attempt...');

ini_set('gd.jpeg_ignore_warning', 1);

function GalleryMain()
{
	global $sourcedir, $currentVersion, $context, $gallerySettings, $txt, $smcFunc, $modSettings, $boarddir, $boardurl, $scripturl;

	require_once($sourcedir . '/Subs-Gallery2.php');
    require_once($sourcedir . '/Subs-Video2.php');

	// Load the language files
	if (loadlanguage('Gallery') == false)
		loadLanguage('Gallery','english');

	// Link Tree
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=gallery',
		'name' => $txt['gallery_text_title']
	);

	if (function_exists("set_tld_regex") || !empty($modSettings['smfhacks_sqlmode']))
	{
		$modSettings['disableQueryCheck'] = 1;
		$smcFunc['db_query']('', "SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
		$modSettings['disableQueryCheck'] = 0;
	}


	$currentVersion = '9.0.1';

	DoGalleryAdSellerPro();

	// Load Main Gallery Settings Array
	LoadGallerySettings();

    Gallery_CheckForUserGallery();

	// Check the gallery path
	if (empty($modSettings['gallery_path']))
		$modSettings['gallery_path'] = $boarddir . '/gallery/';
	if (empty($modSettings['gallery_url']))
		$modSettings['gallery_url'] = $boardurl . '/gallery/';

	if (empty($gallerySettings['gallery_set_batchadd_path']))
		$gallerySettings['gallery_set_batchadd_path'] = $modSettings['gallery_path'] . "ftp/";


	if (empty($modSettings['gallery_jpeg_compression']))
		$modSettings['gallery_jpeg_compression'] = 65;

	// Per Page
	$modSettings['orignal_set_images_per_page'] = $modSettings['gallery_set_images_per_page'];
    if (isset($_SESSION['galleryperpage']))
	{
		$galleryPerPage = (int) $_SESSION['galleryperpage'];

		if ($galleryPerPage < $modSettings['orignal_set_images_per_page'])
			$galleryPerPage = $modSettings['orignal_set_images_per_page'];

		if ($galleryPerPage > $modSettings['orignal_set_images_per_page'] * 3)
			$galleryPerPage = $modSettings['orignal_set_images_per_page'] * 3;

		$modSettings['gallery_set_images_per_page'] = $galleryPerPage;
	}


    $context['gallery21beta'] = false;

	// Load the main template file
    if (function_exists("set_tld_regex"))
    {
    	$context['gallery21beta'] = true;
    	$context['show_bbc'] = 1;
    }


    TopButtonTabs();

	// Process the user gallery functions
	if (isset($_REQUEST['su']))
	{
		require_once($sourcedir . '/UserGallery2.php');
		UserMain();
	}
	else
	{
		// Load the main template file
		if ($context['gallery21beta'] == false)
			loadTemplate('Gallery2');
		else
			loadTemplate('Gallery2.1');




		$context['template_layers'][] = 'gallery';

		// Gallery Actions pretty big array heh
		$subActions = array(
			'view' => 'ViewPicture',
			'admincat' => 'AdminCats',
			'bulkactions' => 'BulkActions',
			'adminset'=> 'AdminSettings',
			'adminset2'=> 'AdminSettings2',
			'delete' => 'DeletePicture',
			'delete2' => 'DeletePicture2',
			'edit' => 'EditPicture',
			'edit2' => 'EditPicture2',
			'report' => 'ReportPicture',
			'report2' => 'ReportPicture2',
			'deletereport' => 'DeleteReport',
			'comment' => 'AddComment',
			'comment2' => 'AddComment2',
			'editcomment' => 'EditComment',
			'editcomment2' => 'EditComment2',
			'apprcomment' => 'ApproveComment',
			'apprcomall' => 'ApproveAllComments',
			'reportcomment' => 'ReportComment',
			'reportcomment2' => 'ReportComment2',
			'delcomment' => 'DeleteComment',
			'delcomreport' => 'DeleteCommentReport',
			'commentlist' => 'CommentList',
			'rate' => 'RatePicture',
			'viewrating' => 'ViewRating',
			'delrating' => 'DeleteRating',
			'catup' => 'CatUpDown',
			'catdown' => 'CatUpDown',
			'catperm' => 'CatPerm',
			'catperm2' => 'CatPerm2',
			'catpermlist' => 'CatPermList',
			'catpermdelete' => 'CatPermDelete',
			'catpermedit' => 'CatPermEdit',
			'catpermedit2' => 'CatPermEdit2',
			'catpermcopy' => 'CatPermCopy',
			'catimgdel' => 'CatImageDelete',
			'addcat' => 'AddCategory',
			'addcat2' => 'AddCategory2',
			'editcat' => 'EditCategory',
			'editcat2' => 'EditCategory2',
			'deletecat' => 'DeleteCategory',
			'deletecat2' => 'DeleteCategory2',
			'viewc' => 'ViewC',
			'myimages' => 'MyImages',
			'approvelist' => 'ApproveList',
			'approve' => 'ApprovePicture',
			'unapprove' => 'UnApprovePicture',
			'add' => 'AddPicture',
			'add2' => 'AddPicture2',
			'search' => 'Search',
			'search2' => 'Search2',
			'stats' => 'Stats',
			'import' => 'ImportPictures',
			'import2' => 'ImportPictures2',
			'bulk' => 'BulkAdd',
			'bulk2' => 'BulkAdd2',
			'filespace' => 'FileSpaceAdmin',
			'filelist' => 'FileSpaceList',
			'recountquota' => 'RecountFileQuotaTotals',
			'addquota' => 'AddQuota',
			'deletequota' => 'DeleteQuota',
			'next' => 'NextImage',
			'prev' => 'PreviousImage',
			'xml' => 'GenerateXML',
			'regen' => 'ReGenerateThumbnails',
			'regen2' => 'ReGenerateThumbnails2',
			'batchftp' => 'BatchFTP',
			'batchftp2' => 'BatchFTP2',
			'cusup' => 'CustomUp',
			'cusdown' => 'CustomDown',
			'cusadd' => 'CustomAdd',
            		'cusedit' => 'CustomEdit',
            		'cusedit2' => 'CustomEdit2',
			'cusdelete' => 'CustomDelete',
			'changegallery' => 'ChangeGallery',
			'changegallery2' => 'ChangeGallery2',
			'listall' => 'ListAll',
			'viewlayout' => 'ViewLayoutSettings',
			'savelayout' => 'SaveLayoutSettings',
			'exifsettings' => 'EXIFSettings',
			'exifsettings2' => 'SaveEXIFSettings',
			'doallexif' => 'ProcessAllPicturesEXIFData',
			'addvideo' => 'AddVideo',
			'addvideo2' => 'AddVideo2',
			'editvideo'  => 'EditVideo',
			'editvideo2'  => 'EditVideo2',
			'videoset' => 'VideoSettings',
			'videoset2'=> 'VideoSettings2',
			'viewers' => 'ViewViewers',
			'unviewed' => 'UnviewedItems',
			'rss' => 'ShowRSSFeed',
			'mature' => 'MarkMature',
			'slideshow' => 'StartSlideshow',
			'watchuser' => 'ToggleWatch',
			'mywatchlist' => 'MyWatchList',
			'whowatchme' => 'WhoWatchMe',
			'savenote' => 'GallerySaveNote',
			'deletenote' => 'GalleryDeleteNote',
			'copyimage' => 'CopyImage',
			'copyimage2' => 'CopyImage2',
			'autothumb' => 'AutoThumbNailGalleryIcon',
			'myfavorites' => 'MyFavorites',
			'addfavorite' => 'AddFavorite',
			'unfavorite' => 'UnFavorite',
			'rebuildrelated' => 'Gallery_BuildRelatedIndex',
			'postupload' => 'Gallery_PostUpload',
			'postupload2' => 'Gallery_PostUpload2',
			'selectcat' => 'Gallery_SelectCat',
			'selectcat2' => 'Gallery_SelectCat2',
			'download' => 'GalleryDownloadItem',
	            	'whofavorited' => 'Gallery_WhoFavorited',
	            	'twitter' => 'Gallery_TwitterSettings',
	            	'twitter2' => 'Gallery_TwitterSettings2',
	            	'twittersignin' => 'Gallery_TwitterSignIn',
	            	'copyright' => 'Gallery_CopyrightRemoval',
            		'modlog' => 'Gallery_ModerationLog',
            		'emptymodlog' => 'Gallery_EmptyModLog',
	        	'viewlikes' => 'Gallery_ViewLikes',
	        	'like' => 'Gallery_Like',
	        	'markunviewed' => 'Gallery_MarkUnviewed',
	        	'markallviewed' => 'Gallery_MarkAllViewed',
			'pixie' => 'Gallery_Pixie'

	);


	// Follow the sa or just go to  the main function
    if (isset($_REQUEST['sa']))
	   $sa = $_REQUEST['sa'];
    else
        $sa = '';


	if (!empty($subActions[$sa]))
		$subActions[$sa]();
	else
		GalleryMainView();

	}
}

function GalleryMainView()
{
	global $context, $scripturl, $mbname, $txt, $smcFunc, $modSettings, $user_info;

	// View the main gallery

	// Is the user allowed to view the gallery?
	isAllowedTo('smfgallery_view');

	StoreGalleryLocation();

	if (isset($_REQUEST['cat']))
	{
		$cat = (int) $_REQUEST['cat'];
		// Check the permission
		GetCatPermission($cat,'view');

		// Get category name used for the page title
		$dbresult1 = $smcFunc['db_query']('', "
			SELECT
				id_cat, title, roworder, description, image,
			disablerating, orderby, sortby, total, redirect, displaymode
			FROM {db_prefix}gallery_cat
			WHERE id_cat = $cat LIMIT 1");
		$row1 = $smcFunc['db_fetch_assoc']($dbresult1);


        if (empty($row1['id_cat']))
            fatal_error($txt['gallery_error_nocat_exists'],false);

        $context['gallery_cat_redirect'] = $row1['redirect'];
		$context['gallery_cat_name'] = $row1['title'];
		$context['gallery_sortby'] = $row1['sortby'];
		$context['gallery_orderby'] = $row1['orderby'];
		$context['gallery_cat_norate'] = $row1['disablerating'];
		$context['gallery_cat_total'] = $row1['total'];
		if ($context['gallery_cat_norate'] == '')
			$context['gallery_cat_norate'] = 0;
		$smcFunc['db_free_result']($dbresult1);


		 GetParentLink($cat);


    if (isset($_REQUEST['perpage']))
	{
		$galleryPerPage = (int) $_REQUEST['perpage'];

		if ($galleryPerPage < $modSettings['orignal_set_images_per_page'])
			$galleryPerPage = $modSettings['orignal_set_images_per_page'];

		if ($galleryPerPage > $modSettings['orignal_set_images_per_page'] * 3)
			$galleryPerPage = $modSettings['orignal_set_images_per_page'] * 3;

		$_SESSION['galleryperpage'] = $galleryPerPage;
		$modSettings['gallery_set_images_per_page'] = $galleryPerPage;
	}




	$context['start'] = (int) $_REQUEST['start'];

	// Check if we are sorting stuff heh
	$sortby = '';
	$orderby = '';
	if (isset($_REQUEST['sortby']))
	{
		switch ($_REQUEST['sortby'])
		{
			case 'date': $sortby = 'p.id_picture';
			break;

			case 'title': $sortby = 'p.title';
			break;

			case 'mostview': $sortby = 'p.views';
			break;

			case 'mostcom': $sortby = 'p.commenttotal';
			break;

			case 'mostliked': $sortby = 'p.totallikes';
			break;


			case 'mostrated': $sortby = 'ratingaverage DESC,p.totalratings ';
			break;

			default: $sortby = 'p.id_picture';
			break;
		}

		$sortby2 = $_REQUEST['sortby'];
	}
	else
	{




		if (!empty($context['gallery_sortby']))
		{
			$sortby = $context['gallery_sortby'];

			switch ($sortby)
			{
				case 'p.id_picture': $sortby2 = 'date';
				break;

				case 'p.title': $sortby2 = 'title';
				break;

				case 'p.views': $sortby2 = 'mostview';
				break;

				case 'p.commenttotal': $sortby2 = 'mostcom';
				$sortby = 'ratingaverage DESC,p.totalratings ';
				break;

				case 'p.totallikes': $sortby2 = 'mostliked';
				break;


				case 'p.totalratings': $sortby2 = 'mostrated';
				break;

				default: $sortby2 = 'date';
				break;
			}
		}
		else
		{
			$sortby = 'p.id_picture';
			$sortby2 = 'date';
		}
	}

	if (isset($_REQUEST['orderby']))
	{
		switch ($_REQUEST['orderby'])
		{
			case 'asc':
				$orderby = 'ASC';

			break;
			case 'desc':
				$orderby = 'DESC';
			break;

			default:
				$orderby = 'DESC';
			break;
		}

		$orderby2 = $_REQUEST['orderby'];
	}
	else
	{
		if (!empty($context['gallery_orderby']))
		{
			$orderby = $context['gallery_orderby'];
			$orderby2 = strtolower($context['gallery_orderby']);
		}
		else
		{
			$orderby = 'DESC';

			$orderby2 = 'desc';
		}
	}




    $g_manage = allowedTo('smfgallery_manage');

            if ($g_manage)
            {


        		if ($context['user']['is_guest'])
        			$groupid = -1;
        		else
        			$groupid =  $user_info['groups'][0];

        		$dbresult = $smcFunc['db_query']('', "
        			SELECT
        				c.id_cat, c.title, p.view, c.id_parent
        			FROM {db_prefix}gallery_cat as c
        			LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.id_group = $groupid AND c.id_cat = p.id_cat)
        			WHERE c.redirect = 0
        			ORDER BY c.title ASC");

        		$context['gallery_cat'] = array();
        		while($row = $smcFunc['db_fetch_assoc']($dbresult))
        		{
        			// Check if they have permission to search these categories
        			if ($row['view'] == '0')
        				continue;

        			$context['gallery_cat'][] = $row;
        		}
        		$smcFunc['db_free_result']($dbresult);

        		CreateGalleryPrettyCategory();



            	if (isset($_REQUEST['pics']))
            	{
            		$baction = $_REQUEST['doaction'];

            		foreach ($_REQUEST['pics'] as $value)
            		{

            		    echo $baction;



            		  $value = (int) $value;
            			if ($baction == 'approve')
            				ApprovePictureByID($value);

                        if ($baction == 'move')
                        {
            				$movecat= (int) $_REQUEST['movecat'];
                            if (!empty($movecat))
                            {
                                $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic SET ID_CAT = $movecat WHERE id_picture = $value");
                                UpdateCategoryTotals($movecat);
                                UpdateCategoryTotals($cat);

                                Gallery_UpdateLatestCategory($cat);
                                Gallery_UpdateLatestCategory($movecat);
                            }
                        }

                        if ($baction == 'unapprove')
            				UnApprovePictureByID($value);

            			if ($baction == 'delete')
            				DeletePictureByID($value);
            		}
            	}

             } // end quick action



		if (empty($sortby))
			$sortby = 'p.id_picture';

		if (empty($orderby))
			$orderby = 'DESC';




	   $dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.totalratings, p.rating, p.commenttotal, p.filesize, p.views,
			p.thumbfilename, p.filename, p.mediumfilename, p.title, p.id_member, m.real_name, p.date, p.description,
			p.mature, v.id_picture as unread, (p.rating / p.totalratings ) AS ratingaverage,
			mg.online_color, p.totallikes
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
		LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
				LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $context['user']['id'] . " AND v.user_id_cat = p.USER_ID_CAT)
		WHERE ( p.id_cat = $cat OR FIND_IN_SET(" . $cat   . ", p.additionalcats)) AND p.approved = 1 GROUP BY p.id_picture  ORDER BY $sortby $orderby
		LIMIT $context[start]," . $modSettings['gallery_set_images_per_page']);

        $context['gallery_image_listing_data'] = array();
    	while($row = $smcFunc['db_fetch_assoc']($dbresult))
    	{
    		if ($row1['displaymode'] == 1)
			{
				$row['thumbfilename'] = $row['mediumfilename'];
			}

    		if ($row1['displaymode'] == 2)
			{
				$row['thumbfilename'] = $row['filename'];
			}

    	   $context['gallery_image_listing_data'][] = $row;

        }
       	$smcFunc['db_free_result']($dbresult);

    	$context['page_index'] = constructPageIndex($scripturl . '?action=gallery;cat=' . $cat . ';sortby=' . $sortby2 . ';orderby=' .$orderby2, $_REQUEST['start'], GetTotalByCATID($cat,$context['gallery_cat_total']), $modSettings['gallery_set_images_per_page']);


		// Image Listing
		$context['sub_template']  = 'image_listing';

		// Set the page title
		$context['page_title'] = $mbname . ' - ' . $context['gallery_cat_name'];

		if (!empty($modSettings['gallery_who_viewing']))
		{
			$context['can_moderate_forum'] = allowedTo('moderate_forum');

			// Taken from Display.php
			// Start out with no one at all viewing it.
			$context['view_members'] = array();
			$context['view_members_list'] = array();
			$context['view_num_hidden'] = 0;

			$whoID = (string) $cat;

			// Search for members who have this picture id set in their GET data.
			$request = $smcFunc['db_query']('', "
				SELECT
					lo.id_member, lo.log_time, mem.real_name, mem.member_name, mem.show_online,
					mg.online_color, mg.id_group, mg.group_name
				FROM {db_prefix}log_online AS lo
					LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = lo.id_member)
					LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = IF(mem.id_group = 0, mem.id_post_group, mem.id_group))
				WHERE INSTR(lo.url, 's:7:\"gallery\";s:3:\"cat\";s:" . strlen($whoID ) .":\"$cat\";') OR lo.session = '" . ($user_info['is_guest'] ? 'ip' . $user_info['ip'] : session_id()) . "'");

			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				if (empty($row['id_member']))
					continue;


					// Disable member color link
					if (!empty($modSettings['gallery_disable_membercolorlink']))
						$row['online_color'] = '';

				if (!empty($row['online_color']))
					$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '" style="color: ' . $row['online_color'] . ';">' . $row['real_name'] . '</a>';
				else
					$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>';

				$is_buddy = in_array($row['id_member'], $user_info['buddies']);
				if ($is_buddy)
					$link = '<b>' . $link . '</b>';

				// Add them both to the list and to the more detailed list.
				if (!empty($row['show_online']) || allowedTo('moderate_forum'))
					$context['view_members_list'][$row['log_time'] . $row['member_name']] = empty($row['show_online']) ? '<i>' . $link . '</i>' : $link;
				$context['view_members'][$row['log_time'] . $row['member_name']] = array(
					'id' => $row['id_member'],
					'username' => $row['member_name'],
					'name' => $row['real_name'],
					'group' => $row['id_group'],
					'href' => $scripturl . '?action=profile;u=' . $row['id_member'],
					'link' => $link,
					'is_buddy' => $is_buddy,
					'hidden' => empty($row['show_online']),
				);

				if (empty($row['show_online']))
					$context['view_num_hidden']++;
			}

			// The number of guests is equal to the rows minus the ones we actually used ;).
			$context['view_num_guests'] = $smcFunc['db_num_rows']($request) - count($context['view_members']);
			$smcFunc['db_free_result']($request);

			// Sort the list.
			krsort($context['view_members']);
			krsort($context['view_members_list']);


		}





	}
	else
	{
		// Get the main groupid
		if ($context['user']['is_guest'])
			$groupid = -1;
		else
			$groupid =  $user_info['groups'][0];

		$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.title, p.view, c.roworder, c.description, c.image, c.filename, c.redirect,
			c.total, l.id_picture, l.title pictitle, l.date, m.id_member, m.real_name, mg.online_color
		FROM {db_prefix}gallery_cat AS c
		LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.id_group = {int:group} AND c.id_cat = p.id_cat)
		LEFT JOIN {db_prefix}gallery_pic AS l ON (c.LAST_id_picture = l.id_picture)
		LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.id_member)
		LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		WHERE c.id_parent = 0 GROUP BY c.ID_CAT ORDER BY c.roworder ASC", array('group' => $groupid));
		$context['gallery_categorylist'] = array();
		while($row = $smcFunc['db_fetch_assoc']($dbresult))
		{
			// Check permission to show this category
			if ($row['view'] == '0')
				continue;

			// Check if category is normal or a User Category
			if ($row['redirect'] == 0)
			{

			}
			else
			{
				// Check if they want the user galleries to show on the gallery index
				if ($modSettings['gallery_index_showusergallery'] == 0)
					continue;

				// User Gallery
				$ugalleryresult = $smcFunc['db_query']('', "
					SELECT SUM(total) as totalpics
					FROM {db_prefix}gallery_usercat");
				$userrow = $smcFunc['db_fetch_assoc']($ugalleryresult);
				$smcFunc['db_free_result']($ugalleryresult);
				$totalpics = $userrow['totalpics'];
				$context['gallery_usercat_pictotal'] = $totalpics;
			}

			$context['gallery_categorylist'][] = $row;
	}

		$smcFunc['db_free_result']($dbresult);

		// Load the main gallery template
		$context['sub_template']  = 'mainview';

		$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'];
	}

}

function AddCategory()
{
	global $context, $mbname, $txt, $modSettings, $smcFunc, $sourcedir;

	isAllowedTo('smfgallery_manage');

	// Show the boards where the user can select to post in.
	$context['gallery_boards'] = array('');
	$request = $smcFunc['db_query']('', "
	SELECT
		b.id_board, b.name AS bName, c.name AS cName
	FROM {db_prefix}boards AS b, {db_prefix}categories AS c
	WHERE b.id_cat = c.id_cat ORDER BY c.cat_order, b.board_order");
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['gallery_boards'][$row['id_board']] = $row['cName'] . ' - ' . $row['bName'];
	$smcFunc['db_free_result']($request);

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.title, c.roworder, c.id_parent
		FROM {db_prefix}gallery_cat AS c
		WHERE c.redirect = 0
		ORDER BY c.title ASC");

	$context['gallery_cat'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_cat'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	CreateGalleryPrettyCategory();

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_addcategory'];

	$context['sub_template']  = 'add_category';

   	$context['linktree'][] = array(
			'name' => '<em>' .  $txt['gallery_text_addcategory']. '</em>'
		);

	// Check if spellchecking is both enabled and actually working.
	$context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');

	// Used for the editor
	require_once($sourcedir . '/Subs-Editor.php');

	// Now create the editor.
	$editorOptions = array(
			'id' => 'descript',
			'value' => '',
			'width' => '90%',
			'form' => 'catform',
			'labels' => array(
				'post_button' => ''
			),
		);


	create_control_richedit($editorOptions);
	$context['post_box_name'] = $editorOptions['id'];
}

function AddCategory2()
{
	global $smcFunc, $txt, $sourcedir, $modSettings;

	isAllowedTo('smfgallery_manage');


	// If we came from WYSIWYG then turn it back into BBC regardless.
	if (!empty($_REQUEST['descript_mode']) && isset($_REQUEST['descript'])  && !function_exists("set_tld_regex"))
	{
		require_once($sourcedir . '/Subs-Editor.php');

		$_REQUEST['descript'] = html_to_bbc($_REQUEST['descript']);

		// We need to unhtml it now as it gets done shortly.
		$_REQUEST['descript'] = un_htmlspecialchars($_REQUEST['descript']);
	}

	// Get the category information and clean the input for bad stuff
	$title = $smcFunc['htmlspecialchars']($_REQUEST['title'],ENT_QUOTES);
	$description = $smcFunc['htmlspecialchars']($_REQUEST['descript'],ENT_QUOTES);
	$image =  htmlspecialchars($_REQUEST['image'],ENT_QUOTES);
	$boardselect = (int) $_REQUEST['boardselect'];
    $id_topic = (int) $_REQUEST['id_topic'];
	$parent = (int) $_REQUEST['parent'];
	//DD Edit For Artists
	if (!empty($_REQUEST['artist']))
		$artist = (int) $_REQUEST['artist'];
    else
        $artist = 0;
	
	if (!empty($_REQUEST['gallery_type']))
		$gtype = (int) $_REQUEST['gallery_type'];
    else
        $gtype = 1;

	$lockcategory = (int) isset($_REQUEST['lockcategory']) ? 1 : 0;

	$showpostlink = isset($_REQUEST['showpostlink']) ? 1 : 0;
	$locktopic = isset($_REQUEST['locktopic']) ? 1 : 0;
	$disablerating  = isset($_REQUEST['disablerating']) ? 1 : 0;
    $tweet_items = isset($_REQUEST['tweet_items']) ? 1 : 0;
	$fullsize = isset($_REQUEST['fullsize']) ? 1 : 0;
	$displaymode = (int) $_REQUEST['displaymode'];

	// Title is required for a category
	if (empty($title))
		fatal_error($txt['gallery_error_cat_title'],false);


		$sortby = '';
		$orderby = '';
		if (isset($_REQUEST['sortby']))
		{
			switch ($_REQUEST['sortby'])
			{
				case 'date':
					$sortby = 'p.id_picture';
				break;

				case 'title':
					$sortby = 'p.title';
				break;

				case 'mostview':
					$sortby = 'p.views';
				break;

				case 'mostcom':
					$sortby = 'p.commenttotal';
				break;

				case 'mostliked':
					$sortby = 'p.totallikes';
				break;


				case 'mostrated':
					$sortby = 'p.totalratings';
				break;

				default:
					$sortby = 'p.id_picture';
				break;
			}
		}
		else
		{
			$sortby = 'p.id_picture';
		}


		if (isset($_REQUEST['orderby']))
		{
			switch ($_REQUEST['orderby'])
			{
				case 'asc':
					$orderby = 'ASC';

				break;
				case 'desc':
					$orderby = 'DESC';
				break;

				default:
					$orderby = 'DESC';
				break;
			}
		}
		else
		{
			$orderby = 'DESC';
		}

	// Do the order
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		MAX(roworder) as catorder
	FROM {db_prefix}gallery_cat
	WHERE id_parent = $parent ORDER BY roworder DESC");
	$row = $smcFunc['db_fetch_assoc']($dbresult);

	if ($smcFunc['db_affected_rows']()== 0)
		$order = 0;
	else
		$order = $row['catorder'];
	$order++;

	// Insert the category
	$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_cat
			(title, description,roworder,image,id_board,postingsize,id_parent,disablerating,showpostlink,locktopic,sortby,orderby,locked,id_topic,tweet_items,displaymode,art_id, gallery_type)
		VALUES ('$title', '$description',$order,'$image',$boardselect,$fullsize,$parent,$disablerating,$showpostlink,$locktopic,'$sortby','$orderby',$lockcategory,$id_topic,$tweet_items,$displaymode,$artist, $gtype)");
	$smcFunc['db_free_result']($dbresult);

	// Get the Category ID
	$cat_id = $smcFunc['db_insert_id']('{db_prefix}gallery_cat', 'id_cat');
	
	//Update The Artist If The Parent Cat If One Used
	if ($parent == 35) // Update For Charts Site To 35
	{
		if ($artist != 0)
		{
				// Update the Artist
		$smcFunc['db_query']('', "UPDATE {db_prefix}artists
		SET artist_gallery_id = '$cat_id' 
		WHERE art_id = $artist LIMIT 1");
		}
	}

	$testGD = get_extension_funcs('gd');
	$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
	unset($testGD);

	// Upload Category image File
	if (isset($_FILES['picture']['name']) && $_FILES['picture']['name'] != '')
	{
		$sizes = @getimagesize($_FILES['picture']['tmp_name']);

		// No size, then it's probably not a valid pic.
		if ($sizes === false)
			fatal_error($txt['gallery_error_invalid_picture'],false);

		require_once($sourcedir . '/Subs-Graphics.php');

		if ((!empty($modSettings['gallery_set_cat_width']) && $sizes[0] > $modSettings['gallery_set_cat_width']) || (!empty($modSettings['gallery_set_cat_height']) && $sizes[1] > $modSettings['gallery_set_cat_height']))
		{
			if(!empty($modSettings['gallery_resize_image']))
			{
				//Check to resize image?
				DoCatImagResize($sizes,$_FILES['picture']['tmp_name']);
			}
			else
			{
				//Delete the temp file
				@unlink($_FILES['picture']['tmp_name']);
				fatal_error($txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0],false);
			}
		}

		// Move the file
			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

		$filename = $cat_id . '.' . $extension;

		move_uploaded_file($_FILES['picture']['tmp_name'], $modSettings['gallery_path'] . 'catimgs/' . $filename);
		@chmod($modSettings['gallery_path'] . 'catimgs/' . $filename, 0644);

		// Update the filename for the category
		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat
		SET filename = '$filename' WHERE id_cat = $cat_id LIMIT 1");
	}

	// Copy category level permissions
	if (!empty($_REQUEST['copycat']))
	{
		$copycat = (int) $_REQUEST['copycat'];
		CopyCatPermissions($cat_id,$copycat);
	}


	// Redirect to the category listing
	redirectexit('action=gallery;cat=' . $cat_id );
}

function ViewC()
{
	die(base64_decode('UG93ZXJlZCBieSBTTUYgR2FsbGVyeSBQcm8gbWFkZSBieSB2YmdhbWVyNDUgaHR0cDovL3d3dy5zbWZoYWNrcy5jb20='));
}

function EditCategory()
{
	global $context, $mbname, $txt, $modSettings, $smcFunc, $sourcedir;
	isAllowedTo('smfgallery_manage');


    if (isset($_REQUEST['cat']))
	   $cat = (int) $_REQUEST['cat'];
    else
        $cat = 0;

	if (empty($cat))
		fatal_error($txt['gallery_error_no_cat']);

	$context['gallery_boards'] = array('');
	$request = $smcFunc['db_query']('', "
	SELECT
		b.id_board, b.name AS bName, c.name AS cName
	FROM {db_prefix}boards AS b, {db_prefix}categories AS c
	WHERE b.id_cat = c.id_cat ORDER BY c.cat_order, b.board_order");
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['gallery_boards'][$row['id_board']] = $row['cName'] . ' - ' . $row['bName'];
	$smcFunc['db_free_result']($request);

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_cat, title, roworder, id_parent
		FROM {db_prefix}gallery_cat
		WHERE redirect = 0
		ORDER BY title ASC");

	$context['gallery_cat'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_cat'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	CreateGalleryPrettyCategory();

	$context['catid'] = $cat;

	// Set the page title
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_editcategory'];
	// Load the edit category subtemplate
	$context['sub_template']  = 'edit_category';

   	$context['linktree'][] = array(
			'name' => '<em>' . $txt['gallery_text_editcategory'] . '</em>'
		);

	// Check if spellchecking is both enabled and actually working.
	$context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');



	$dbresult = $smcFunc['db_query']('', "
	SELECT
		id_cat, title, image, filename, description, id_board, postingsize, art_id, gallery_type, 
	 id_parent,disablerating, redirect, showpostlink, locktopic, sortby, orderby, locked, id_topic, tweet_items, displaymode 
	FROM {db_prefix}gallery_cat
	WHERE id_cat = $cat LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
    $context['gallery_edit_cat'] = $row;

	// Used for the editor
	require_once($sourcedir . '/Subs-Editor.php');

		// Now create the editor.
		$editorOptions = array(
			'id' => 'descript',
			'value' => $row['description'],
			'width' => '90%',
			'form' => 'catform',
			'labels' => array(
				'post_button' => ''
			),
		);


		create_control_richedit($editorOptions);
		$context['post_box_name'] = $editorOptions['id'];

		// Get all the custom fields
		$result = $smcFunc['db_query']('', "
		SELECT
			title, defaultvalue, is_required, id_custom, id_cat
		FROM {db_prefix}gallery_custom_field
		WHERE id_cat = " . $cat . " or id_cat = 0 ORDER BY roworder desc");
        $context['gallery_cat_cusfields'] = array();
		while ($row2 = $smcFunc['db_fetch_assoc']($result))
		{
			$context['gallery_cat_cusfields'][] = $row2;
		}
		$smcFunc['db_free_result']($result);

}

function EditCategory2()
{
	global $smcFunc, $txt, $modSettings, $sourcedir;

	isAllowedTo('smfgallery_manage');

	if (empty($_REQUEST['catid']))
		fatal_error($txt['gallery_error_no_cat']);

	// If we came from WYSIWYG then turn it back into BBC regardless.
	if (!empty($_REQUEST['descript_mode']) && isset($_REQUEST['descript'])  && !function_exists("set_tld_regex"))
	{
		require_once($sourcedir . '/Subs-Editor.php');

		$_REQUEST['descript'] = html_to_bbc($_REQUEST['descript']);

		// We need to unhtml it now as it gets done shortly.
		$_REQUEST['descript'] = un_htmlspecialchars($_REQUEST['descript']);
	}

	// Clean the input
	$title = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['title']), ENT_QUOTES);
	$description = $smcFunc['htmlspecialchars']($_REQUEST['descript'], ENT_QUOTES);
	$catid = (int) $_REQUEST['catid'];
	$image = htmlspecialchars($_REQUEST['image'], ENT_QUOTES);
	$parent = (int) $_REQUEST['parent'];
    $tweet_items = (int) isset($_REQUEST['tweet_items']) ? 1 : 0;
	$lockcategory = (int) isset($_REQUEST['lockcategory']) ? 1 : 0;
	//DD Edit For Artists
	if (!empty($_REQUEST['artist']))
		$artist = (int) $_REQUEST['artist'];
    else
        $artist = 0;
	
	if (!empty($_REQUEST['gallerytype']))
		$gtype = (int) $_REQUEST['gallerytype'];
    else
        $gtype = 1;
	
	if (isset($_REQUEST['boardselect']))
		$boardselect = (int) $_REQUEST['boardselect'];
	else
		$boardselect = 0;

    $id_topic = (int) $_REQUEST['id_topic'];

	$showpostlink = isset($_REQUEST['showpostlink']) ? 1 : 0;
	$locktopic = isset($_REQUEST['locktopic']) ? 1 : 0;
	$disablerating  = isset($_REQUEST['disablerating']) ? 1 : 0;
	$fullsize = isset($_REQUEST['fullsize']) ? 1 : 0;

	$displaymode = (int) $_REQUEST['displaymode'];

	// The category field requires a title
	if (empty($title))
		fatal_error($txt['gallery_error_cat_title'],false);

	$sortby = '';
	$orderby = '';
	if (isset($_REQUEST['sortby']))
	{
		switch ($_REQUEST['sortby'])
		{
			case 'date':
				$sortby = 'p.id_picture';
			break;
			case 'title':
				$sortby = 'p.title';
			break;
			case 'mostview':
				$sortby = 'p.views';
			break;
			case 'mostcom':
				$sortby = 'p.commenttotal';
			break;

			case 'mostliked':
				$sortby = 'p.totallikes';
			break;

			case 'mostrated':
				$sortby = 'p.totalratings';
			break;
			default:
				$sortby = 'p.id_picture';
			break;
		}
	}
	else
	{
		$sortby = 'p.id_picture';
	}


	if (isset($_REQUEST['orderby']))
		switch ($_REQUEST['orderby'])
		{
			case 'asc':
				$orderby = 'ASC';
			break;
			case 'desc':
				$orderby = 'DESC';
			break;
			default:
				$orderby = 'DESC';
			break;
		}
	else
		$orderby = 'DESC';

	// Update the category
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat
		SET title = '$title', image = '$image', description = '$description', id_board = $boardselect, postingsize = $fullsize,
		id_parent = $parent, disablerating = $disablerating, showpostlink = $showpostlink, locktopic = $locktopic,
		orderby = '$orderby', sortby = '$sortby', locked = $lockcategory, id_topic = '$id_topic', tweet_items = '$tweet_items',
		displaymode = '$displaymode', art_id = '$artist', gallery_type = '$gtype' 
		WHERE id_cat = $catid LIMIT 1");


	$testGD = get_extension_funcs('gd');
	$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
	unset($testGD);

	// Upload Category image File
	if (isset($_FILES['picture']['name']) && $_FILES['picture']['name'] != '')
	{

		$sizes = @getimagesize($_FILES['picture']['tmp_name']);

		// No size, then it's probably not a valid pic.
		if ($sizes === false)
			fatal_error($txt['gallery_error_invalid_picture'],false);

		require_once($sourcedir . '/Subs-Graphics.php');

		if ((!empty($modSettings['gallery_set_cat_width']) && $sizes[0] > $modSettings['gallery_set_cat_width']) || (!empty($modSettings['gallery_set_cat_height']) && $sizes[1] > $modSettings['gallery_set_cat_height']))
		{
			if (!empty($modSettings['gallery_resize_image']))
			{
				// Check to resize image?
				DoCatImagResize($sizes,$_FILES['picture']['tmp_name']);
			}
			else
			{
				// Delete the temp file
				@unlink($_FILES['picture']['tmp_name']);
				fatal_error($txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0],false);
			}
		}

		// Move the file
			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

		$filename = $catid . '.' . $extension;

		move_uploaded_file($_FILES['picture']['tmp_name'], $modSettings['gallery_path'] . 'catimgs/' . $filename);
		@chmod($modSettings['gallery_path'] . 'catimgs/' . $filename, 0644);

		// Update the filename for the category
		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat
		SET filename = '$filename' WHERE id_cat = $catid LIMIT 1");
	}

	redirectexit('action=gallery;cat=' . $catid);
}

function DeleteCategory()
{
	global $context, $mbname, $txt, $smcFunc;
	isAllowedTo('smfgallery_manage');


	$catid = (int) $_REQUEST['cat'];

	if (empty($catid))
		fatal_error($txt['gallery_error_no_cat']);

	$context['catid'] = $catid;

	// Lookup the category to get its name
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_cat, title
		FROM {db_prefix}gallery_cat
		WHERE id_cat = $catid");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$context['cat_title'] = $row['title'];
	$smcFunc['db_free_result']($dbresult);

	// Get total pics in the category
	$dbresult2 = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) as totalpics
		FROM {db_prefix}gallery_pic
		WHERE id_cat = $catid AND approved = 1");

	$row2 = $smcFunc['db_fetch_assoc']($dbresult2);
	$context['totalpics'] = $row2['totalpics'];
	$smcFunc['db_free_result']($dbresult2);

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_delcategory'];

	$context['sub_template']  = 'delete_category';
}

function DeleteCategory2()
{
	global $smcFunc;

	isAllowedTo('smfgallery_manage');

	$catid = (int) $_REQUEST['catid'];

	// Increase the max time just in case it takes a long to delete the category and files.
	@ini_set('max_execution_time', '600');

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_picture
		FROM {db_prefix}gallery_pic
		WHERE id_cat = $catid");

	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		DeletePictureByID($row['id_picture']);
	}

	// Update Category parent
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat SET id_parent = 0 WHERE id_parent = $catid");

	// Delete All Pictures
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_pic WHERE id_cat = $catid");

	// Finally delete the category
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_cat WHERE id_cat = $catid LIMIT 1");

	// Last Recount the totals
	RecountFileQuotaTotals(false);

	redirectexit('action=admin;area=gallery;sa=admincat');
}

function ViewPicture()
{
	global $context, $mbname, $sourcedir, $smcFunc, $modSettings, $gallerySettings, $user_info, $scripturl, $boardurl, $txt;

	isAllowedTo('smfgallery_view');

	// Get the picture ID
    if (isset($_REQUEST['id']))
	   $id = (int) $_REQUEST['id'];
    else
       $id = 0;

    if (isset($_REQUEST['pic']))
	   $id = (int) $_REQUEST['pic'];

	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected'],false);

    if ($user_info['is_guest'])
    {
        if (!empty($gallerySettings['gallery_set_onlyregcanviewimage']))
        {
            fatal_error($txt['gallery_set_onlyregcanviewimage'],false);
        }
    }

	// Do we need to show the visual verification image?
	$context['require_verification'] = $user_info['is_guest'];
	if ($context['require_verification'])
	{
		require_once($sourcedir . '/Subs-Editor.php');
		$verificationOptions = array(
			'id' => 'post',
		);
		$context['require_verification'] = create_control_verification($verificationOptions);
		$context['visual_verification_id'] = $verificationOptions['id'];


	}

	// Used for the editor
	require_once($sourcedir . '/Subs-Editor.php');

	// Now create the editor.
		$editorOptions = array(
			'id' => 'comment',
			'value' => '',
			'width' => '90%',
			'form' => 'cprofile',
			'labels' => array(
				'post_button' => ''
			),
		);


	create_control_richedit($editorOptions);
	$context['post_box_name'] = $editorOptions['id'];


	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_picture, user_id_cat, id_cat
		FROM {db_prefix}gallery_pic
		WHERE id_picture = $id  LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	if ($smcFunc['db_num_rows']($dbresult) == 0)
		fatal_error($txt['gallery_error_no_pic_selected'],false);


	$user_id_cat = $row['user_id_cat'];
	$id_cat = $row['id_cat'];
	$smcFunc['db_free_result']($dbresult);
	// Get the picture information
	$usercatmem = 0;

	if ($user_id_cat != 0)
	{
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.type, p.videofile, p.width, p.height, p.totalratings, p.thumbfilename, p.rating, p.allowcomments, p.user_id_cat id_cat, p.keywords, p.commenttotal,
			p.filesize, p.filename, p.approved, p.views, p.title, p.id_member, m.real_name, p.date, p.description, c.title cat_title, c.id_member user_member,
			p.mature, p.mediumfilename, p.ID_TOPIC, f.id_picture as favorite, p.allowratings, mg.online_color, p.totallikes, l.ID_LIKE
		FROM ({db_prefix}gallery_pic as p, {db_prefix}gallery_usercat AS c)
		LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
        LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		LEFT JOIN {db_prefix}gallery_favorites AS f ON (p.id_picture = f.id_picture AND f.id_member = " . $user_info['id'] . " )
		LEFT JOIN {db_prefix}gallery_like AS l ON (p.id_picture = l.id_picture AND l.id_member = " . $user_info['id'] . " )
		WHERE p.id_picture = $id  AND  p.user_id_cat = c.user_id_cat LIMIT 1");
		$context['gallery_user_id_cat'] = $user_id_cat;
	}
	else
	{
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.type, p.videofile, p.width, p.height, p.totalratings, p.rating, p.allowcomments, p.id_cat, p.keywords, p.commenttotal, p.filesize,
			p.thumbfilename, p.filename, p.approved, p.views, p.title, p.id_member, m.real_name, p.date, p.description, c.title cat_title, c.disablerating,
			p.mature, p.mediumfilename, p.ID_TOPIC, f.id_picture as favorite, p.allowratings, mg.online_color, p.totallikes, l.ID_LIKE
		FROM ({db_prefix}gallery_pic as p, {db_prefix}gallery_cat AS c)
		LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
        LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		LEFT JOIN {db_prefix}gallery_favorites AS f ON (p.id_picture = f.id_picture AND f.id_member = " . $user_info['id'] . " )
		LEFT JOIN {db_prefix}gallery_like AS l ON (p.id_picture = l.id_picture AND l.id_member = " . $user_info['id'] . " )
		WHERE p.id_picture = $id AND p.id_cat = c.id_cat LIMIT 1");
		$context['gallery_user_id_cat'] = 0;
	}

	// Check if picture exists
	if ($smcFunc['db_affected_rows']()== 0)
		fatal_error($txt['gallery_error_no_pictureexist'],false);

	$row = $smcFunc['db_fetch_assoc']($dbresult);

	// Check if they can view the picture
	if ($context['gallery_user_id_cat'] == 0)
	{
		GetCatPermission($row['id_cat'],'view');
        GetCatPermission($row['id_cat'],'viewimagedetail');

		 GetParentLink($row['id_cat']);

	}
	else
	{
		$usercatmem = $row['user_member'];
  		$context['linktree'][] = array(
			'url' => $scripturl . '?action=gallery;su=user;sa=userlist',
			'name' => $txt['gallery_user_index']
		);
    	$context['linktree'][] = array(
			'url' => $scripturl . '?action=gallery;su=user;u=' . $row['user_member'],
			'name' => $row['real_name'],
		);

		GetUserParentLink($context['gallery_user_id_cat'],$row['user_member']);

	}

	$g_manage = allowedTo('smfgallery_manage');

	// Checked if they are allowed to view an unapproved picture.
	if ($row['approved'] == 0 && $user_info['id'] != $row['id_member'])
	{
		if (!$g_manage)
			fatal_error($txt['gallery_error_pic_notapproved'],false);
	}



	// Check if it is a private picture or password protected user gallery!!!
	if ($context['gallery_user_id_cat'] != 0)
	{
		$result = $smcFunc['db_query']('', "
			SELECT
			title,password,private,gallery_index_toprated,gallery_index_recent,
			gallery_index_mostviewed,gallery_index_mostcomments,gallery_index_showtop,id_member 
			FROM {db_prefix}gallery_usersettings
			WHERE id_member = " . $row['id_member'] . " LIMIT 1");

		// Get the data
		$row4 = $smcFunc['db_fetch_assoc']($result);

		if (empty($row4['id_member']))
		{
			$row4['password'] = '';
			$row4['private'] = '';
		}

		$context['gallery_user_settings'] = array(
			'password' => $row4['password'],
			'private' => $row4['private'],
		);

		// Password is Required
		if ($modSettings['gallery_user_no_password'] == 0)
		if (!empty($context['gallery_user_settings']['password']) && $g_manage == false && $user_info['id'] != $row['id_member'])
		{
			// Check if the password has been entered
			if (isset($_COOKIE['smfgallery' . $row['id_member']]))
			{
				if ($_COOKIE['smfgallery' . $row['id_member']] != $context['gallery_user_settings']['password'])
				{
					// Passwords do not match!
					redirectexit('action=gallery;su=user;sa=password;msg=1;u=' .  $row['id_member']);
					exit;
				}
			}
			else
			{
				// No Password has entered show them the password form
				redirectexit('action=gallery;su=user;sa=password;u=' . $row['id_member']);
				exit;
			}
		}

		// Check if private gallery
		if ($modSettings['gallery_user_no_private'] == 0)
		if ($context['gallery_user_settings']['private'] == '1'  && $g_manage == false && $user_info['id'] != $row['id_member'])
		{
			// Check if the user is allowed
			$result2 = $smcFunc['db_query']('', "
			SELECT * FROM {db_prefix}gallery_userprivate
			WHERE id_owner = $usercatmem  AND id_member = " . $user_info['id'] . " LIMIT 1");
			if ($smcFunc['db_num_rows']($result2) == 0)
			{
				// Deny Access
				fatal_error($txt['gallery_error_private_gallery'], false);
			}
			$smcFunc['db_free_result']($result2);
		}

		$smcFunc['db_free_result']($result);
	}

	// If rated Mature
	if ($row['mature'] == 1 && !isset($_SESSION['mature_ok']))
	{
		// Load the warning subtemplate
		$context['sub_template']  = 'mature_content';

		$context['page_title'] = $txt['gallery_txt_maturecontent_warning'];

		$context['gallery_pic_id'] = $row['id_picture'];

		return;
	}

	// Gallery picture information
	$context['gallery_pic'] = array(
		'id_picture' => $row['id_picture'],
		'id_member' => $row['id_member'],
		'videofile' => $row['videofile'],
		'type' => $row['type'],
		'commenttotal' => $row['commenttotal'],
		'totallikes' => $row['totallikes'],
		'views' => $row['views'],
		'title' => $row['title'],
		'description' => $row['description'],
		'filesize' => $row['filesize'],
		'filename' => $row['filename'],
		'thumbfilename' => $row['thumbfilename'],
		'width' => $row['width'],
		'height' => $row['height'],
		'allowcomments' => $row['allowcomments'],
		'id_cat' => $row['id_cat'],
		'id_cat2' => $id_cat,
		'date' => timeformat($row['date']),
		'keywords' => $row['keywords'],
		'real_name' => $row['real_name'],
		'totalratings' => $row['totalratings'],
		'rating' => $row['rating'],
		'cat_title' => $row['cat_title'],
		'disablerating' => @$row['disablerating'],
		'user_member' => $usercatmem,
		'mature' => $row['mature'],
		'mediumfilename' => $row['mediumfilename'],
        'approved' => $row['approved'],
		'user_id_cat' => $user_id_cat,
		'ID_TOPIC' => $row['ID_TOPIC'],
		'favorite' => $row['favorite'],
		'allowratings' => $row['allowratings'],
        'online_color' => $row['online_color'],
        'ID_LIKE' => $row['ID_LIKE']
	);

	$smcFunc['db_free_result']($dbresult);


	if (isset($_REQUEST['showrss']))
	{
		// Check RSS is enabled
		if (empty($gallerySettings['gallery_enable_rss']))
			return;

		ob_end_clean();
		if (!empty($modSettings['enableCompressedOutput']))
			@ob_start('ob_gzhandler');
		else
			ob_start();

				// Show the feed
				header("Content-Type: application/xml; charset=ISO-8859-1");

				echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
				echo '<rss version="2.0" xml:lang="en-US">
				<channel>
				<title><![CDATA[', $context['gallery_pic']['title'], ']]></title>
				<description><![CDATA[',$context['gallery_pic']['description'],']]></description>
				<link>', $scripturl, '?action=gallery;sa=view;id=',$id,'</link>
			';

			        if ($user_info['is_guest'])
			            $commentwhereClause =  "AND c.approved = 1 ";
			        else
			            $commentwhereClause =  "AND (c.approved = 1 OR (c.approved = 0 AND c.ID_MEMBER = " . $user_info['id'] . ")) ";

			        if ($g_manage == true)
			            $commentwhereClause = '';


					if (!empty($modSettings['gallery_set_commentsnewest']))
						$commentorder = 'DESC';
					else
						$commentorder = 'ASC';
					// Display all user comments
					$dbresult = $smcFunc['db_query']('', "
					SELECT
						c.ID_PICTURE, c.ID_COMMENT, c.date, c.comment, c.ID_MEMBER, c.lastmodified,
						c.modified_ID_MEMBER, m.posts, m.real_name, c.approved
					FROM ({db_prefix}gallery_comment as c)
					LEFT JOIN {db_prefix}members AS m ON (c.ID_MEMBER = m.ID_MEMBER)
					WHERE c.ID_PICTURE = " . $id . " $commentwhereClause ORDER BY c.ID_COMMENT $commentorder");


				while($row = $smcFunc['db_fetch_assoc']($dbresult))
				{
					echo '
					<item>
					<title><![CDATA[', $row['real_name'], ']]></title>
					<pubDate>',gmdate('D, d M Y H:i:s \G\M\T', $row['date']),'</pubDate>
					<description><![CDATA[',$row['comment'],']]></description>
					</item>';
				}

				echo '</channel>';
				echo '</rss>';

		die("");

	}


	$context['html_headers'] .= '
	<link rel="image_src" href="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '" / >
	';

	// Check if spellchecking is both enabled and actually working.
	$context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');


	// Meta Data for facebook
	$context['html_headers'] .= '<meta property="og:title" content="' . $smcFunc['htmlspecialchars']($row['title'],ENT_QUOTES)  . '" />
<meta property="og:type" content="article" />
<meta property="og:url" content="' . $scripturl . '?action=gallery&sa=view&id=' . $context['gallery_pic']['id_picture'] . '" />
<meta property="og:image" content="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '" />
<meta property="og:image:type" content="' . Gallery_GetMimeType($context['gallery_pic']['filename']) .  '" />
<meta property="og:description" content="' . $smcFunc['htmlspecialchars']($row['description'],ENT_QUOTES) . '" />
<link rel="image_src" href="' . $modSettings['gallery_url'] . $context['gallery_pic']['filename'] . '" />
';


	// High Slide options
	if ($modSettings['gallery_set_nohighslide'] == 0)
	$context['html_headers'] .= '
	<script type="text/javascript" src="' . $boardurl . '/highslide/highslide-full.packed.js"></script>
	<script type="text/javascript">
    hs.graphicsDir = \'' . $boardurl . '/highslide/graphics/\';
    hs.outlineType = \'rounded-white\';
    hs.showCredits = false;

	</script>
<style type="text/css">

.highslide {
	cursor: url(' . $boardurl . '/highslide/graphics/zoomin.cur), pointer;
	outline: none;
}

.highslide:hover img {
	border: 2px solid white;
}

.highslide-image {
	border: 2px solid white;
}
.highslide-image-blur {
}
.highslide-caption {
	display: none;

	border: 2px solid white;
	border-top: none;
	font-family: Verdana, Helvetica;
	font-size: 10pt;
	padding: 5px;
	background-color: white;
}
.highslide-display-block {
	display: block;
}
.highslide-display-none {
	display: none;
}
.highslide-loading {
	display: block;
	color: white;
	font-size: 9px;
	font-weight: bold;
	text-transform: uppercase;
	text-decoration: none;
	padding: 3px;
	border-top: 1px solid white;
	border-bottom: 1px solid white;
	background-color: black;
	/*
	padding-left: 22px;
	background-image: url(' . $boardurl . '/highslide/graphics/loader.gif);
	background-repeat: no-repeat;
	background-position: 3px 1px;
	*/
}
a.highslide-credits,
a.highslide-credits i {
	padding: 2px;
	color: silver;
	text-decoration: none;
	font-size: 10px;
}
a.highslide-credits:hover,
a.highslide-credits:hover i {
	color: white;
	background-color: gray;
}
a.highslide-full-expand {
	background: url(' . $boardurl . '/highslide/graphics/fullexpand.gif) no-repeat;
	display: block;
	margin: 0 10px 10px 0;
	width: 34px;
	height: 34px;
}
</style>
	';

	// Using Lighbox
	if ($modSettings['gallery_set_nohighslide'] == 2)
		$context['html_headers'] .= '<script type="text/javascript" src="' . $modSettings['gallery_url'] . 'js/prototype.js"></script>
		<script type="text/javascript" src="' . $modSettings['gallery_url'] . 'js/scriptaculous.js?load=effects"></script>
		<script type="text/javascript" src="' . $modSettings['gallery_url'] . 'js/lightbox.js"></script>
		<link rel="stylesheet" href="' . $modSettings['gallery_url'] . 'css/lightbox.css" type="text/css" media="screen" />';


	if ($gallerySettings['gallery_set_allow_photo_tagging'])
		{
				$context['html_headers'] .= '
		<script src="' . $modSettings['gallery_url'] . 'notes/BrowserDetect.js" language="javascript"></script>
		<script src="' . $modSettings['gallery_url'] . 'notes/PhotoNotes-1.5.js" language="javascript"></script>
		<link rel="stylesheet" href="' . $modSettings['gallery_url']. 'notes/PhotoNotes-1.5.css" type="text/css" media="screen" />

		<script src="' . $modSettings['gallery_url'] . 'js/prototype.js"></script>
		<script language="javascript"> ';

		$context['html_headers'] .=  "
		function saveNoteDb(note){
			note.Save();
			var url = '" . $scripturl . "?action=gallery;sa=savenote;pic=" . $context['gallery_pic']['id_picture'] . ";text='+ encodeURIComponent(note.text)+' &width=' + note.rect.width + '&height=' + note.rect.height + '&left=' +note.rect.left+'&top=' + note.rect.top +'&id='+note.id ;
			var retorno = new Ajax.Request(url, {
				method: 'get',
				onSuccess: function(transport) {
				var notice = $('divResultado');
				if (transport.responseText > 0)
				{
					notice.update('" . $txt['gallery_txt_note_saved'] . "').setStyle({ background: '#dfd' });
					note.id = transport.responseText;
				}
				else
				{
					notice.update('" . $txt['gallery_txt_note_not_saved'] . "<br>'+transport.responseText).setStyle({ background: '#fdd' });
				}
				}
			});
			return 1;
		}
		function deleteNoteDb(note){
			var url = '" . $scripturl . "?action=gallery;sa=deletenote;pic=" . $context['gallery_pic']['id_picture'] . ";id='+note.id ;
			var retorno = new Ajax.Request(url, {
				method: 'get',
				onSuccess: function(transport) {
				var notice = $('divResultado');
				if (transport.responseText > 0)
				{
					notice.update('" .$txt['gallery_txt_note_deleted'] . "').setStyle({ background: '#dfd' });
					note.Delete();
				}
				else
				{
					notice.update('" . $txt['gallery_txt_note_not_deleted'] . "<br>').setStyle({ background: '#fdd' });
				}
				}
			});
			return 1;
		}
		</script>";
}


	$updateviews = true;
	if ($modSettings['gallery_setviewscountonce'] == 1)
	{
		if (isset($_SESSION['viewed_pics']))
		{
			if (is_array($_SESSION['viewed_pics']))
			{
				foreach ($_SESSION['viewed_pics'] as $sess)
				{
					if ($sess == $id)
					{
						$updateviews = false;
						break;
					}
				}

				if ($updateviews == true)
				{
					$tmp2 = array($id);

					$_SESSION['viewed_pics'] = array_merge($_SESSION['viewed_pics'],$tmp2 );
				}
			}
			else
				$_SESSION['viewed_pics'] = array($id);
		}
		else
			$_SESSION['viewed_pics'] = array($id);
	}

	// Update the number of views.
	if ($updateviews == true  && $row['approved'] == 1)
	{
	  $dbresult = $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
		SET views = views + 1 WHERE id_picture = $id LIMIT 1");

	  // Viewed the image
	  if (!$user_info['is_guest'])
	  {

		$smcFunc['db_query']('', "
				DELETE FROM {db_prefix}gallery_log_mark_view
				WHERE id_picture = $id AND id_member = " . $user_info['id']  . " AND
				id_cat = $id_cat AND user_id_cat = $user_id_cat");


	 	$smcFunc['db_query']('', "
			REPLACE INTO {db_prefix}gallery_log_mark_view
				(id_picture, id_member, id_cat, user_id_cat)
			VALUES ($id," . $user_info['id']  . "," . $id_cat . ",$user_id_cat)");
	  }
	}

	$context['gallery_previous_image'] = PreviousImage($context['gallery_pic']['id_picture'],$context['gallery_pic']['id_cat2'],$context['gallery_pic']['user_id_cat'],true);
	$context['gallery_next_image'] =  NextImage($context['gallery_pic']['id_picture'],$context['gallery_pic']['id_cat2'],$context['gallery_pic']['user_id_cat'],true);

	// Show Mini thumbnails on view picture
	if ($gallerySettings['gallery_set_mini_prevnext_thumbs'])
	{
				$result = $smcFunc['db_query']('', "
				SELECT
					id_picture, thumbfilename, title, mature
				FROM {db_prefix}gallery_pic
				WHERE id_picture IN (" . $context['gallery_next_image']  . "," . $context['gallery_previous_image']. "," . $context['gallery_pic']['id_picture'] . ")");
				$context['gallery_pic_minpics'] = array();
				while($row = $smcFunc['db_fetch_assoc']($result))
				{

					if ($row['mature'] == 1)
					{
						if (CanViewMature() == false)
							$row['thumbfilename'] = 'mature.gif';
					}

					$context['gallery_pic_minpics'][] = $row;
				}
	}


	// Show total favorites and view favortied
	if (!empty($gallerySettings['gallery_set_allow_favorites']))
	{
		$favResult = $smcFunc['db_query']('', "
				SELECT
					COUNT(*) AS total
				FROM  {db_prefix}gallery_favorites as f
				WHERE f.id_picture = " . $context['gallery_pic']['id_picture']);
		$favRow =  $smcFunc['db_fetch_assoc']($favResult);
		$context['gallery_pic_favdata'] = $favRow;
	}

	// Check to show custom fields
	if ($context['gallery_user_id_cat'] == 0)
	{
		// Show Custom Fields
		$result = $smcFunc['db_query']('', "
			SELECT
				f.title, d.value
			FROM ({db_prefix}gallery_custom_field as f,{db_prefix}gallery_custom_field_data as d)
			WHERE d.id_custom = f.id_custom AND d.id_picture = " . $context['gallery_pic']['id_picture'] .  " ORDER BY f.roworder desc");
		$context['gallery_pic_customfields'] = array();
		while ($row4 = $smcFunc['db_fetch_assoc']($result))
		{
			$context['gallery_pic_customfields'][] = $row4;
		}
		$smcFunc['db_free_result']($result);
	}


		if (allowedTo('smfgallery_ratepic'))
		{

			$dbresult = $smcFunc['db_query']('', "
					SELECT
					id_member, id_picture
					FROM {db_prefix}gallery_rating
					WHERE id_member = " . $user_info['id'] . " AND id_picture = " . $context['gallery_pic']['id_picture']);

				$found = $smcFunc['db_affected_rows']();
			$smcFunc['db_free_result']($dbresult);
			$context['gallery_user_has_rated'] = $found;
		}


	// EXIF Picture data
	if ($gallerySettings['enable_exif_on_display'])
	{
		$result = $smcFunc['db_query']('', "
		SELECT
			*
		FROM {db_prefix}gallery_exif_data
		WHERE id_picture = " . $context['gallery_pic']['id_picture']);

		$exif_count = $smcFunc['db_num_rows']($result);
		$exifRow = $smcFunc['db_fetch_assoc']($result);
		$smcFunc['db_free_result']($result);

		$context['gallery_exif_picdata_count'] = $exif_count;
		$context['gallery_exif_picdata'] = $exifRow;

	}

	// Photo tagging
	if ($gallerySettings['gallery_set_allow_photo_tagging'])
	{
		$tagResult = $smcFunc['db_query']('', "
				SELECT
					caption, id, xpos,ypos,width,height
				FROM {db_prefix}gallery_pic_tagging

					WHERE id_picture = " . $context['gallery_pic']['id_picture']);
		$context['gallery_phototags'] = array();
		while ($tagRow = $smcFunc['db_fetch_assoc']($tagResult))
		{
			$context['gallery_phototags'][] = $tagRow;
		}
		$smcFunc['db_free_result']($tagResult);
	}



	// Get Comments
	if ($context['gallery_pic']['allowcomments'])
	{

		if (!empty($modSettings['gallery_set_commentsnewest']))
			$commentorder = 'DESC';
		else
			$commentorder = 'ASC';


		if ($user_info['is_guest'])
			$commentwhereClause =  "AND c.approved = 1 ";
		else
			$commentwhereClause =  "AND (c.approved = 1 OR (c.approved = 0 AND c.ID_MEMBER = " . $user_info['id'] . ")) ";

		if ($g_manage == true)
			$commentwhereClause = '';

		$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_picture, c.id_comment, c.date, c.comment, c.id_member, c.lastmodified,
			c.modified_id_member, m.posts, m.real_name, c.approved, r.value
		FROM ({db_prefix}gallery_comment as c)
		LEFT JOIN {db_prefix}members AS m ON (c.id_member = m.id_member)
		LEFT JOIN {db_prefix}gallery_rating AS r ON (r.id_member = c.id_member AND r.id_picture = " . $context['gallery_pic']['id_picture'] . ")
		WHERE c.id_picture = " . $context['gallery_pic']['id_picture'] . " $commentwhereClause ORDER BY c.id_comment $commentorder");
		$comment_count = $smcFunc['db_affected_rows']();
		$context['gallery_comment_count'] = $comment_count;
		$context['gallery_comment_picture_list'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($dbresult))
		{
			if ($row['modified_id_member'] != 0 && empty($gallerySettings['gallery_set_hide_lastmodified_comment']))
			{
				$dbresult2 = $smcFunc['db_query']('', "
				SELECT
					c.id_comment, c.modified_id_member, m.real_name
				FROM {db_prefix}gallery_comment as c, {db_prefix}members AS m
				WHERE c.id_comment = " . $row['id_comment'] . " AND c.modified_id_member= m.id_member");
				$row2 = $smcFunc['db_fetch_assoc']($dbresult2);

				if (!empty($row2['modified_id_member']))
					$row['modified_real_name'] = $row2['real_name'];
				else
					$row['modified_real_name'] = 0;

				$smcFunc['db_free_result']($dbresult2);
			}

			$context['gallery_comment_picture_list'][] = $row;
		}
		$smcFunc['db_free_result']($dbresult);

	}





	if (!empty($modSettings['gallery_who_viewing']))
	{
		$context['can_moderate_forum'] = allowedTo('moderate_forum');

		// Start out with no one at all viewing it.
		$context['view_members'] = array();
		$context['view_members_list'] = array();
		$context['view_num_hidden'] = 0;

		$whoID = (string) $id;

		// Search for members who have this picture id set in their GET data.
		$request = $smcFunc['db_query']('', "
			SELECT
				lo.id_member, lo.log_time, mem.real_name, mem.member_name, mem.show_online,
				mg.online_color, mg.id_group, mg.group_name
			FROM {db_prefix}log_online AS lo
				LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = lo.id_member)
				LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = IF(mem.id_group = 0, mem.id_post_group, mem.id_group))
			WHERE INSTR(lo.url, 's:7:\"gallery\";s:2:\"sa\";s:4:\"view\";s:2:\"id\";s:" . strlen($whoID ) .":\"$id\";') OR lo.session = '" . ($user_info['is_guest'] ? 'ip' . $user_info['ip'] : session_id()) . "'");

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			if (empty($row['id_member']))
				continue;


					// Disable member color link
					if (!empty($modSettings['gallery_disable_membercolorlink']))
						$row['online_color'] = '';

			if (!empty($row['online_color']))
				$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '" style="color: ' . $row['online_color'] . ';">' . $row['real_name'] . '</a>';
			else
				$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>';

			$is_buddy = in_array($row['id_member'], $user_info['buddies']);
			if ($is_buddy)
				$link = '<b>' . $link . '</b>';

			// Add them both to the list and to the more detailed list.
			if (!empty($row['show_online']) || allowedTo('moderate_forum'))
				$context['view_members_list'][$row['log_time'] . $row['member_name']] = empty($row['show_online']) ? '<i>' . $link . '</i>' : $link;
			$context['view_members'][$row['log_time'] . $row['member_name']] = array(
				'id' => $row['id_member'],
				'username' => $row['member_name'],
				'name' => $row['real_name'],
				'group' => $row['id_group'],
				'href' => $scripturl . '?action=profile;u=' . $row['id_member'],
				'link' => $link,
				'is_buddy' => $is_buddy,
				'hidden' => empty($row['show_online']),
			);

			if (empty($row['show_online']))
				$context['view_num_hidden']++;
		}

		// The number of guests is equal to the rows minus the ones we actually used ;).
		$context['view_num_guests'] = $smcFunc['db_num_rows']($request) - count($context['view_members']);
		$smcFunc['db_free_result']($request);

		// Sort the list.
		krsort($context['view_members']);
		krsort($context['view_members_list']);
	}


	$context['sub_template']  = 'view_picture';

	$context['page_title'] = $mbname . ' - ' . $context['gallery_pic']['title'];

}

function AddPicture()
{
	global $context, $mbname, $txt, $modSettings, $smcFunc, $user_info, $sourcedir;

	isAllowedTo('smfgallery_add');

	CheckMaxUploadPerDay();


    if (isset($_REQUEST['cat']))
	   $cat = (int) $_REQUEST['cat'];
    else
        $cat = 0;

    $context['gallery_cat_id'] = $cat;

    if (!isset($context['gallery_pic_title']))
        $context['gallery_pic_title'] = '';

    if (!isset($context['gallery_pic_description']))
        $context['gallery_pic_description'] = '';
    if (!isset($context['gallery_pic_keywords']))
        $context['gallery_pic_keywords'] = '';


	$context['gallery_user_id'] = 0;

	$g_manage = allowedTo('smfgallery_manage');

	// Register this form and get a sequence number in $context.
	checkSubmitOnce('register');



	if (empty($context['gallery_user_id']))
	{
		$result = $smcFunc['db_query']('', "
		SELECT
			title, defaultvalue, is_required, id_custom
		FROM {db_prefix}gallery_custom_field
		WHERE id_cat = " . $context['gallery_cat_id'] . " or id_cat = 0");
		$context['gallery_addpic_customfields'] = array();
		while ($row2 = $smcFunc['db_fetch_assoc']($result))
		{
			$context['gallery_addpic_customfields'][] = $row2;
		}
		$smcFunc['db_free_result']($result);
	 }





	if (!isset($_REQUEST['u']) || (isset($_REQUEST['u']) && empty($_REQUEST['u'])))
	{
		GetCatPermission($cat,'addpic');

		if ($context['user']['is_guest'])
			$groupid = -1;
		else
			$groupid =  $user_info['groups'][0];


		$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.title, p.view, p.addpic, c.locked, c.id_parent
		FROM {db_prefix}gallery_cat AS c
		LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.id_group = $groupid AND c.id_cat = p.id_cat)
		WHERE c.redirect = 0 ORDER BY c.title ASC");
		if ($smcFunc['db_num_rows']($dbresult) == 0)
			fatal_error($txt['gallery_error_no_catexists'] , false);


		$context['gallery_cat'] = array();
		 while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				// Check if they have permission to add to this category.
				if ($row['view'] == '0' || $row['addpic'] == '0' )
					continue;

				// Skip category if it is locked
				if ($g_manage == false && $row['locked'] == 1)
					continue;

				$context['gallery_cat'][] = $row;
			}
		$smcFunc['db_free_result']($dbresult);

		CreateGalleryPrettyCategory();
	}
	else
	{
		// This is a user gallerly add picture
		$u = (int) $_REQUEST['u'];
		$context['gallery_user_id'] = $u;

		$g_gallery = allowedTo('smfgallery_usergallery');

		// Check permissions
		if (!$g_manage && ($user_info['id'] != $u || !$g_gallery))
		{
			fatal_error($txt['gallery_user_noperm'],false);
		}
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			user_id_cat, title, roworder, id_parent
		FROM {db_prefix}gallery_usercat
		WHERE id_member = $u ORDER BY title ASC");
		if ($smcFunc['db_num_rows']($dbresult) == 0)
			fatal_error($txt['gallery_error_no_catexists'], false);

		$context['gallery_cat'] = array();

		while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				// id_cat on purpose for Add Picture page
				$context['gallery_cat'][] = array(
					'id_cat' => $row['user_id_cat'],
					'title' => $row['title'],
					'roworder' => $row['roworder'],
					'id_parent' => $row['id_parent'],
				);
			}
		$smcFunc['db_free_result']($dbresult);

		CreateGalleryPrettyCategory();
	}

	// Get Quota Limits to Display
	$context['quotalimit'] = GetQuotaGroupLimit($user_info['id']);
	$context['userspace'] = GetUserSpaceUsed($user_info['id']);

	$context['sub_template']  = 'add_picture';
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_addpicture'];

	$context['linktree'][] = array(
			'name' => '<em>' . $txt['gallery_form_addpicture'] . '</em>'
		);

	// Check if spellchecking is both enabled and actually working.
	$context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');

	// Used for the editor
	require_once($sourcedir . '/Subs-Editor.php');

		// Now create the editor.
		$editorOptions = array(
			'id' => 'descript',
			'value' => $context['gallery_pic_description'],
			'width' => '90%',
			'form' => 'picform',
			'labels' => array(
				'post_button' => ''
			),
		);


	create_control_richedit($editorOptions);
	$context['post_box_name'] = $editorOptions['id'];

}

function AddPicture2()
{
	global $txt, $smcFunc, $scripturl, $modSettings, $sourcedir, $context, $gd2, $user_info, $gallerySettings;

	isAllowedTo('smfgallery_add');

	$g_manage = allowedTo('smfgallery_manage');

	// Check if gallery path is writable
	if (!is_writable($modSettings['gallery_path']))
		fatal_error($txt['gallery_write_error'] . $modSettings['gallery_path']);

	// If we came from WYSIWYG then turn it back into BBC regardless.
	if (!empty($_REQUEST['descript_mode']) && isset($_REQUEST['descript'])  && !function_exists("set_tld_regex"))
	{
		require_once($sourcedir . '/Subs-Editor.php');

		$_REQUEST['descript'] = html_to_bbc($_REQUEST['descript']);

		// We need to unhtml it now as it gets done shortly.
		$_REQUEST['descript'] = un_htmlspecialchars($_REQUEST['descript']);
	}

	CheckMaxUploadPerDay();

    $errors = array();

	$title = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['title']),ENT_QUOTES);
	$description = $smcFunc['htmlspecialchars']($_REQUEST['descript'],ENT_QUOTES);

	if (isset($_REQUEST['keywords']))
		$keywords = $smcFunc['htmlspecialchars']($_REQUEST['keywords'],ENT_QUOTES);
	else
		$keywords = '';

	$keywords = str_replace(","," ",$keywords);
	if (isset($_REQUEST['cat']))
		$cat = (int) $_REQUEST['cat'];
	else
		$cat = 0;

	if (isset($_REQUEST['userid']))
		$userid = (int) $_REQUEST['userid'];
	else
		$userid = 0;

	$allowcomments = isset($_REQUEST['allowcomments']) ? 1 : 0;
	$sendemail = isset($_REQUEST['sendemail']) ? 1 : 0;
	$markmature = isset($_REQUEST['markmature']) ? 1 : 0;

	$exifData = '';

	if ($userid == 0)
		GetCatPermission($cat,'addpic');
	else
	{
		$g_gallery = allowedTo('smfgallery_usergallery');
		// Check permissions
		if (!$g_manage && ($user_info['id'] != $userid || !$g_gallery))
		{
			fatal_error($txt['gallery_user_noperm'],false);
		}
	}

	// Check if pictures are auto approved
	$approved = 0;

	if ($userid == 0)
	{
		$approve_tmp = GetCatPermission($cat,'autoapprove',true);

		if ($approve_tmp !=2)
		{
			$approved = $approve_tmp;
		}

	}

	if (empty($approved))
	{
		$approved = (allowedTo('smfgallery_autoapprove') ? 1 : 0);
	}

	// Allow comments on picture if no setting set.
	if (empty($modSettings['gallery_commentchoice']))
		$allowcomments = 1;

	// Check Duplicate post
	//checkSubmitOnce('check');


	if ($title == '')
    {
    //    $errors[] = $txt['gallery_error_no_title'];
        //fatal_error($txt['gallery_error_no_title'],false);
    }
	if (empty($cat))
    {
	    $errors[] = $txt['gallery_error_no_cat'];
    	//fatal_error($txt['gallery_error_no_cat'],false);
    }


    $context['gallery_cat_id'] = $cat;
    $context['gallery_pic_title'] = $title;
    $context['gallery_pic_description'] = $description;
    $context['gallery_pic_keywords'] = $keywords;
    $context['gallery_user_id'] = $userid;


	// If keywords required
	if ($gallerySettings['gallery_set_require_keyword'] == true && empty($keywords))
    {
		$errors[] = $txt['gallery_txt_err_require_keyword'];
        //fatal_error($txt['gallery_txt_err_require_keyword'],false);
    }

	if ($modSettings['gallery_set_enable_multifolder'])
		CreateGalleryFolder();


	if ($userid == 0)
	{
		$result = $smcFunc['db_query']('', "
		SELECT f.title, f.is_required, f.id_custom
		FROM  {db_prefix}gallery_custom_field as f
				WHERE f.is_required = 1 AND (f.id_cat = " . $cat . " OR f.ID_CAT = 0)");
		while ($row2 = $smcFunc['db_fetch_assoc']($result))
		{
			if (!isset($_REQUEST['cus_' . $row2['id_custom']]))
			{
                $errors[] =  $txt['gallery_err_req_custom_field'] . $row2['title'];
			//	fatal_error($txt['gallery_err_req_custom_field'] . $row2['title'], false);
			}
			else
			{
				if ($_REQUEST['cus_' . $row2['id_custom']] == '')
                {
                    $errors[] =  $txt['gallery_err_req_custom_field'] . $row2['title'];
				//	fatal_error($txt['gallery_err_req_custom_field'] . $row2['title'], false);
                }
			}
		}
		$smcFunc['db_free_result']($result);
	}

	// Get category information
	if ($userid == 0)
	{
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_cat,id_board,postingsize,locktopic, showpostlink, locked, id_topic, tweet_items
		FROM {db_prefix}gallery_cat
		WHERE id_cat = $cat");
		$rowcat = $smcFunc['db_fetch_assoc']($dbresult);
		$smcFunc['db_free_result']($dbresult);

        if (empty($rowcat['id_cat']))
            fatal_error($txt['gallery_error_no_cat'],false);

		// Check if category is locked
		if ($g_manage == false && $rowcat['locked'] == 1)
			fatal_error($txt['gallery_err_locked_upload'],false);
	}

	$image_resized = 0;
	$testGD = get_extension_funcs('gd');
	$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
	unset($testGD);

	$orginalfilename = '';

	//Process Uploaded file
	if (isset($_FILES['picture']['name']) && $_FILES['picture']['name'] != '')
	{
		$sizes = @getimagesize($_FILES['picture']['tmp_name']);

		$orginalfilename = addslashes($_FILES['picture']['name']);

		if (empty($title))
			$title = $orginalfilename;

		// No size, then it's probably not a valid pic.
		if ($sizes === false)
        {
            $errors[] = $txt['gallery_error_invalid_picture'];
            @unlink($_FILES['picture']['tmp_name']);
			//fatal_error($txt['gallery_error_invalid_picture'],false);
        }

			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
		$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : 'bmp';

		$gallerySettings['gallery_set_disallow_extensions'] = trim($gallerySettings['gallery_set_disallow_extensions']);
		$gallerySettings['gallery_set_disallow_extensions'] = str_replace(".","",$gallerySettings['gallery_set_disallow_extensions']);
		$gallerySettings['gallery_set_disallow_extensions'] = strtolower($gallerySettings['gallery_set_disallow_extensions']);
		$disallowedExtensions = explode(",",$gallerySettings['gallery_set_disallow_extensions']);
		if (in_array($extension,$disallowedExtensions))
		{
            $errors[] = $txt['gallery_err_disallow_extensions'] . $extension;
		//	fatal_error($txt['gallery_err_disallow_extensions'] . $extension,false);
			@unlink($_FILES['picture']['tmp_name']);
		}


		if (strtolower($extension) != 'png')
			$modSettings['avatar_download_png'] = 0;

		require_once($sourcedir . '/Subs-Graphics.php');

		// Check min size
		if ((!empty($modSettings['gallery_min_width']) && $sizes[0] < $modSettings['gallery_min_width']) || (!empty($modSettings['gallery_min_height']) && $sizes[1] < $modSettings['gallery_min_height']))
		{
			// Delete the temp file
			@unlink($_FILES['picture']['tmp_name']);
            $errors[] = $txt['gallery_error_img_size_height2'] . $sizes[1] . $txt['gallery_error_img_size_width2'] . $sizes[0];
		//	fatal_error($txt['gallery_error_img_size_height2'] . $sizes[1] . $txt['gallery_error_img_size_width2'] . $sizes[0],false);

		}

		if ((!empty($modSettings['gallery_max_width']) && $sizes[0] > $modSettings['gallery_max_width']) || (!empty($modSettings['gallery_max_height']) && $sizes[1] > $modSettings['gallery_max_height']))
		{
			if (!empty($modSettings['gallery_resize_image']))
			{
				// Check to resize image?
				$exifData = ReturnEXIFData($_FILES['picture']['tmp_name']);
				DoImageResize($sizes,$_FILES['picture']['tmp_name']);
				$image_resized = 1;
			}
			else
			{
				// Delete the temp file
				@unlink($_FILES['picture']['tmp_name']);
                $errors[] = $txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0];
				//fatal_error($txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0],false);
			}
		}

		// Get the filesize
		if ($image_resized == 1)
		{
			$filesize = filesize($_FILES['picture']['tmp_name']);
		}
		else
		{
			$filesize = $_FILES['picture']['size'];
		}

		if (!empty($modSettings['gallery_max_filesize']) && $filesize > $modSettings['gallery_max_filesize'])
		{

			// Delete the temp file
			@unlink($_FILES['picture']['tmp_name']);
            $errors[] = $txt['gallery_error_img_filesize'] . gallery_format_size($modSettings['gallery_max_filesize'], 2);
		//	fatal_error($txt['gallery_error_img_filesize'] . gallery_format_size($modSettings['gallery_max_filesize'], 2),false);
		}

        // If errors return
        if (!empty($errors))
        {
            $context['gallery_errors'] = $errors;
            AddPicture();
            return;
        }


		// Check Quota
		$quotalimit = GetQuotaGroupLimit($user_info['id']);
		$userspace = GetUserSpaceUsed($user_info['id']);

		// Check if exceeds quota limit or if there is a quota
		if ($quotalimit != 0  &&  ($userspace + $filesize) >  $quotalimit)
		{
			@unlink($_FILES['picture']['tmp_name']);
			fatal_error($txt['gallery_error_space_limit'] . gallery_format_size($userspace, 2) . ' / ' . gallery_format_size($quotalimit, 2) , false);
		}

		// Filename Member Id + Day + Month + Year + 24 hour, Minute Seconds

		$filename = $user_info['id'] . '-' . date('dmyHis') . '.' . $extension;

		$extrafolder = '';

		if ($modSettings['gallery_set_enable_multifolder'])
			$extrafolder = $modSettings['gallery_folder_id'] . '/';


		move_uploaded_file($_FILES['picture']['tmp_name'], $modSettings['gallery_path'] . $extrafolder .  $filename);
		@chmod($modSettings['gallery_path'] . $extrafolder .  $filename, 0644);

		if (!empty($_REQUEST['degrees']))
		{
			$degrees = (int) $_REQUEST['degrees'];
			GalleryRotateImage($modSettings['gallery_path'] . $extrafolder .  $filename,$degrees);
			$sizes = @getimagesize($modSettings['gallery_path'] . $extrafolder .  $filename);
		}

		if ($image_resized)
			$sizes = @getimagesize($modSettings['gallery_path'] . $extrafolder .  $filename);


		// Create thumbnail
		GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder .  $filename, $modSettings['gallery_thumb_width'], $modSettings['gallery_thumb_height']);
		rename($modSettings['gallery_path'] . $extrafolder .  $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder .  'thumb_' . $filename);
		$thumbname = 'thumb_' . $filename;
		@chmod($modSettings['gallery_path'] . $extrafolder .  'thumb_' . $filename, 0755);

		// Medium Image
		$mediumimage = '';

		if ($modSettings['gallery_make_medium'])
		{
			GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder .  $filename, $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
			rename($modSettings['gallery_path'] . $extrafolder .  $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename);
			$mediumimage = 'medium_' . $filename;
			@chmod($modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename, 0755);

			// Check for Watermark
			DoWaterMark($modSettings['gallery_path'] . $extrafolder .  'medium_' .  $filename);
		}

		// Create the Database entry
		$t = time();
		$gallery_pic_id = 0;
		$allowRatings = 1;
		if ($gallerySettings['gallery_set_allowratings'])
			$allowRatings = isset($_REQUEST['allow_ratings']) ? 1 : 0;

		if ($userid == 0)
		{


			$smcFunc['db_query']('', "
				INSERT INTO {db_prefix}gallery_pic
					(id_cat, filesize, thumbfilename, filename, height, width, keywords, title, description,
					 id_member, date, approved, allowcomments, sendemail, mediumfilename, mature, allowratings, orginalfilename)
				VALUES
					($cat, $filesize, {string:thumbname}, {string:filename}, $sizes[1], $sizes[0], {string:keywords}, {string:title}, {string:description},
					 $user_info[id], $t, $approved, $allowcomments, $sendemail, {string:mediumimage}, $markmature, $allowRatings,'$orginalfilename')",
					array(
						'thumbname' => $extrafolder . $thumbname,
						'filename' => $extrafolder . $filename,
						'mediumimage' => $extrafolder . $mediumimage,
						'keywords' => $keywords,
						'title' => $title,
						'description' => $description
					)
				);

			$gallery_pic_id = $smcFunc['db_insert_id']('{db_prefix}gallery_pic', 'id_picture');


			// Check for any custom fields
			$result = $smcFunc['db_query']('', "
			SELECT
				f.title, f.is_required, f.id_custom
			FROM {db_prefix}gallery_custom_field as f
			WHERE f.id_cat = " . $cat . " or f.id_cat = 0");
			while ($row2 = $smcFunc['db_fetch_assoc']($result))
			{
				if (isset($_REQUEST['cus_' . $row2['id_custom']]))
				{

					$custom_data = $smcFunc['htmlspecialchars']($_REQUEST['cus_' . $row2['id_custom']],ENT_QUOTES);

					$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_custom_field_data
					(id_picture, id_custom, value)
					VALUES('$gallery_pic_id', " . $row2['id_custom'] . ", '$custom_data')");
				}
			}
			$smcFunc['db_free_result']($result);
		}
		else
		{

			$allowRatings = 1;
			if ($gallerySettings['gallery_set_allowratings'])
				$allowRatings = isset($_REQUEST['allow_ratings']) ? 1 : 0;

			$smcFunc['db_query']('', "
				INSERT INTO {db_prefix}gallery_pic
					(user_id_cat, filesize,thumbfilename,filename, height, width, keywords, title, description,
					 id_member, date, approved, allowcomments, sendemail, mediumfilename, mature, allowratings,orginalfilename)
				VALUES
					($cat, $filesize, {string:thumbname}, {string:filename}, $sizes[1], $sizes[0], {string:keywords}, {string:title}, {string:description},
					 $user_info[id], $t, $approved, $allowcomments, $sendemail, {string:mediumimage}, $markmature, $allowRatings,'$orginalfilename')",
					array(
						'thumbname' => $extrafolder . $thumbname,
						'filename' => $extrafolder . $filename,
						'mediumimage' => $extrafolder . $mediumimage,
						'keywords' => $keywords,
						'title' => $title,
						'description' => $description
					)
				);

			$gallery_pic_id = $smcFunc['db_insert_id']('{db_prefix}gallery_pic', 'id_picture');

		}


		// Get EXIF Data
		ProcessEXIFData($extrafolder . $filename, $gallery_pic_id, $exifData);

		Gallery_AddRelatedPicture($gallery_pic_id, $title);

        Gallery_AddToActivityStream('galleryproadd',$gallery_pic_id,$title,$user_info['id']);

		// If we are using multifolders get the next folder id
		if ($modSettings['gallery_set_enable_multifolder'])
			ComputeNextFolderID($gallery_pic_id);

		UpdateUserFileSizeTable($user_info['id'], $filesize);

		if ($userid == 0 && $rowcat['id_board'] != 0 && $approved == 1)
		{

			$extraheightwidth = '';
			if ($rowcat['postingsize'] == 1)
			{
				$postimg = $filename;
				$extraheightwidth = " height={$sizes[1]} width={$sizes[0]}";
			}
			else
				$postimg = $thumbname;
			// Create the post
			require_once($sourcedir . '/Subs-Post.php');

			if ($rowcat['showpostlink'] == 1)
				$showpostlink = "\n\n" . $scripturl . '?action=gallery;sa=view;id=' . $gallery_pic_id;
			else
				$showpostlink = '';

			$msgOptions = array(
				'id' => 0,
				'subject' => $title,
				'body' => '[b]' . $title . "[/b]\n\n[img$extraheightwidth]" . $modSettings['gallery_url']  . $extrafolder . $postimg . "[/img]$showpostlink\n\n$description",
				'icon' => 'xx',
				'smileys_enabled' => 1,
				'attachments' => array(),
			);
			$topicOptions = array(
				'id' => $rowcat['id_topic'],
				'board' => $rowcat['id_board'],
				'poll' => null,
				'lock_mode' => $rowcat['locktopic'],
				'sticky_mode' => null,
				'mark_as_read' => true,
			);
			$posterOptions = array(
				'id' => $user_info['id'],
				'update_post_count' => !$user_info['is_guest'],
			);
			// Fix height & width of posted image in message
			preparsecode($msgOptions['body']);


			createPost($msgOptions, $topicOptions, $posterOptions);


			require_once($sourcedir . '/Post.php');


            if (function_exists("notifyMembersBoard"))
            {
                $notifyData = array(
                            'body' =>$msgOptions['body'],
                            'subject' => $msgOptions['subject'],
                            'name' => $user_info['name'],
                            'poster' => $user_info['id'],
                            'msg' => $msgOptions['id'],
                            'board' =>  $rowcat['id_board'],
                            'topic' => $topicOptions['id'],
                        );
                notifyMembersBoard($notifyData);
                    }
                    else
                    {
                     // for 2.1
                    $smcFunc['db_insert']('',
                        '{db_prefix}background_tasks',
                        array('task_file' => 'string', 'task_class' => 'string', 'task_data' => 'string', 'claimed_time' => 'int'),
                        array('$sourcedir/tasks/CreatePost-Notify.php', 'CreatePost_Notify_Background', $smcFunc['json_encode'](array(
                            'msgOptions' => $msgOptions,
                            'topicOptions' => $topicOptions,
                            'posterOptions' => $posterOptions,
                            'type' =>  $topicOptions['id'] ? 'reply' : 'topic',
                        )), 0),
                        array('id_task')
                    );


                    }

			$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
					SET id_topic = " .$topicOptions['id'] . ", id_msg = " . $msgOptions['id'] . " WHERE id_picture = $gallery_pic_id
					");

            Gallery_InsertSMFTags($keywords,$topicOptions['id']);

		}

		// Last recheck Image if it was resized
		if ($image_resized == 1)
		{
			RecheckResizedImage($modSettings['gallery_path'] . $extrafolder .  $filename,$gallery_pic_id,$filesize,$user_info['id']);
		}

		// Check for Watermark
		DoWaterMark($modSettings['gallery_path'] . $extrafolder .  $filename);


		If ($userid == 0)
			UpdateCategoryTotals($cat);
		else
			UpdateUserCategoryTotals($cat);

		// Update the SMF Shop Points
		if (isset($modSettings['shopVersion']))
			$smcFunc['db_query']('', "UPDATE {db_prefix}members
				SET money = money + " . $modSettings['gallery_shop_picadd'] . "
				WHERE id_member = " . $user_info['id'] . "
				LIMIT 1");

 	   // Badge Awards Mod Check
	   GalleryCheckBadgeAwards($user_info['id']);


		UpdateGalleryKeywords($gallery_pic_id);

		if ($approved)
		{

			If ($userid == 0)
            {
                if ($rowcat['tweet_items'] == 1)
                    Gallery_TweetItem($title,$gallery_pic_id);

				Gallery_UpdateLatestCategory($cat);

            }
            else
				Gallery_UpdateUserLatestCategory($cat);

			 UpdateMemberPictureTotals($user_info['id']);

 			SendMemberWatchNotifications($user_info['id'], $scripturl . '?action=gallery;sa=view;id=' .  $gallery_pic_id );

		      // Add Post Count
		     if (!empty($gallerySettings['gallery_set_picturepostcount']))
		     {
		        if ($user_info['id'] != 0)
		        {
		            updateMemberData($user_info['id'], array('posts' => '+'));
		        }
		     }


		}
        else
        {
            $body = $txt['gallery_txt_itemwaitingapproval2'];
            $body = str_replace("%url",$scripturl . '?action=admin;area=gallery;sa=approvelist',$body);
            $body = str_replace("%title",$title,$body);

            Gallery_emailAdmins($txt['gallery_txt_itemwaitingapproval'],$body);
        }

		if (isset($_REQUEST['popup']))
		{
			$forumurl  = $_REQUEST['forumurl'];
			redirectexit('action=gallery;sa=postupload2;id=' . $gallery_pic_id . '&forumurl=' . $forumurl);
		}



 		if (isset($_REQUEST['copyimage']))
 		{
 			redirectexit('action=gallery;sa=copyimage;id=' . $gallery_pic_id);
 		}
 		else if (isset($_SESSION['last_gallery_url']))
 		{
 			redirectexit($_SESSION['last_gallery_url']);
 		}
 		else
 		{

            if ($gallerySettings['gallery_set_redirectcategorydefault'] == 1)
            {
                If ($userid == 0)
                    redirectexit('action=gallery;cat=' . $cat);
                else
                    redirectexit('action=gallery;su=user;cat=' . $cat . ';u=' . $userid);
            }
            else
            {

    			// Redirect to the users image page.
    			if ($user_info['id'] != 0)
    				redirectexit('action=gallery;sa=myimages;u=' . $user_info['id']);
    			else
    				redirectexit('action=gallery;cat=' . $cat);

            }
 		}

	}
	else
    {
        // fatal_error($txt['gallery_error_no_picture']);
        $errors[] = $txt['gallery_error_no_picture'];

         // If errors return
        if (!empty($errors))
        {
            $context['gallery_errors'] = $errors;
            AddPicture();
            return;
        }



    }
}

function EditPicture()
{
	global $context, $mbname, $txt, $smcFunc, $modSettings, $user_info, $sourcedir, $gallerySettings, $boardurl;

	is_not_guest();


	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	if ($context['user']['is_guest'])
		$groupid = -1;
	else
		$groupid =  $user_info['groups'][0];

	$g_manage = allowedTo('smfgallery_manage');

	// Check if the user owns the picture or is admin
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.thumbfilename, p.user_id_cat, p.width, p.height, p.allowcomments, p.allowratings,
			p.id_cat, p.keywords, p.commenttotal, p.filesize, p.filename, p.approved, p.views, p.title, p.id_member,
			m.real_name, p.featured, p.date, p.description, p.sendemail, p.mature, p.videofile, p.type, p.totallikes
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
		WHERE p.id_picture = $id  LIMIT 1");

	if ($smcFunc['db_affected_rows']() == 0)
		fatal_error($txt['gallery_error_no_pictureexist'],false);
	$row = $smcFunc['db_fetch_assoc']($dbresult);


	// Redirect to edit video
	if ($row['videofile'] != '')
		redirectexit('action=gallery;sa=editvideo;id=' . $id);

	if (!isset($_REQUEST['u']) && $row['user_id_cat'] == 0)
	{
		//Check the category permission
		GetCatPermission($row['id_cat'],'editpic');
	}

	// Gallery picture information
    $row['date'] = timeformat($row['date']);
	$context['gallery_pic'] = $row;
	$smcFunc['db_free_result']($dbresult);


	// fix asseration failed
	if (empty($context['gallery_pic']['description']))
			$context['gallery_pic']['description'] = '';


	$context['is_usergallery'] = false;



	$result = $smcFunc['db_query']('', "
	SELECT f.title, f.is_required, f.id_custom, d.value
	FROM {db_prefix}gallery_custom_field as f
			LEFT JOIN {db_prefix}gallery_custom_field_data as d ON (d.id_custom = f.id_custom AND d.id_picture = " . $context['gallery_pic']['id_picture'] . ")
			WHERE  (f.id_cat = " . $context['gallery_pic']['id_cat'] . " or f.id_cat = 0)");

	$context['gallery_editpic_customfields'] = array();
	while ($row2 = $smcFunc['db_fetch_assoc']($result))
	{
		$context['gallery_editpic_customfields'][] = $row2;
	}
	$smcFunc['db_free_result']($result);





	if (allowedTo('smfgallery_manage') || (allowedTo('smfgallery_edit') && $user_info['id'] == $context['gallery_pic']['id_member']))
	{
		// Get the category information
		if ($context['gallery_pic']['user_id_cat'] == 0)
		{
			$dbresult = $smcFunc['db_query']('', "
			SELECT
				c.id_cat, c.title, p.view, p.addpic, c.locked, c.id_parent
			FROM {db_prefix}gallery_cat AS c
			LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.id_group = $groupid AND c.id_cat = p.id_cat)
			WHERE c.redirect = 0 ORDER BY c.title ASC");
			$context['gallery_cat'] = array();
			while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				// Check if they have permission to add to this category.
				if ($row['view'] == '0' || $row['addpic'] == '0' )
					continue;

				// Skip category if it is locked
				if ($g_manage == false && $row['locked'] == 1)
					continue;

				$context['gallery_cat'][] = $row;
			}
			$smcFunc['db_free_result']($dbresult);

			CreateGalleryPrettyCategory();
		}
		else
		{

			$dbresult = $smcFunc['db_query']('', "
			SELECT
				id_member, user_id_cat
			FROM {db_prefix}gallery_usercat
			WHERE user_id_cat = " . $context['gallery_pic']['user_id_cat'] . " LIMIT 1");
			$memRow = $smcFunc['db_fetch_assoc']($dbresult);
			$smcFunc['db_free_result']($dbresult);

			if (empty($memRow['user_id_cat']))
				fatal_error($txt['gallery_error_nocat_exists'],false);

			$dbresult = $smcFunc['db_query']('', "
			SELECT
				user_id_cat, title, id_parent
			FROM {db_prefix}gallery_usercat
			WHERE id_member = " . $memRow['id_member'] . " ORDER BY title ASC");
			$context['gallery_cat'] = array();
			while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				$context['gallery_cat'][] = array(
				'id_cat' => $row['user_id_cat'],
				'title' => $row['title'],
				'id_parent' => $row['id_parent'],
				);
			}
			$smcFunc['db_free_result']($dbresult);

			CreateGalleryPrettyCategory();

			$context['is_usergallery'] = true;
		}

		// Get Quota Limits to Display
		$context['quotalimit'] = GetQuotaGroupLimit($user_info['id']);
		$context['userspace'] = GetUserSpaceUsed($user_info['id']);

		$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_editpicture'];
		$context['sub_template']  = 'edit_picture';

        $context['linktree'][] = array(
			'name' => '<em>' . $txt['gallery_form_editpicture'] . '</em>'
		);

		// Check if spellchecking is both enabled and actually working.
		$context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');

		// Used for the editor
		require_once($sourcedir . '/Subs-Editor.php');

			// Now create the editor.
			$editorOptions = array(
				'id' => 'descript',
				'value' => $context['gallery_pic']['description'],
				'width' => '90%',
				'form' => 'picform',
				'labels' => array(
					'post_button' => ''
				),
			);


		create_control_richedit($editorOptions);
		$context['post_box_name'] = $editorOptions['id'];





	}
	else
	{
		fatal_error($txt['gallery_error_noedit_permission']);
	}
}

function EditPicture2()
{
	global $txt, $smcFunc, $modSettings, $sourcedir, $gd2, $user_info, $gallerySettings;

	is_not_guest();

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	// If we came from WYSIWYG then turn it back into BBC regardless.
	if (!empty($_REQUEST['descript_mode']) && isset($_REQUEST['descript'])  && !function_exists("set_tld_regex"))
	{
		require_once($sourcedir . '/Subs-Editor.php');

		$_REQUEST['descript'] = html_to_bbc($_REQUEST['descript']);

		// We need to unhtml it now as it gets done shortly.
		$_REQUEST['descript'] = un_htmlspecialchars($_REQUEST['descript']);
	}

	// Check the user permissions
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		id_member, id_cat, user_id_cat, thumbfilename, mediumfilename,
		filename, filesize, ID_TOPIC
	FROM {db_prefix}gallery_pic
	WHERE id_picture = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$memID = $row['id_member'];
	$oldfilesize = $row['filesize'];
	$oldfilename = $row['filename'];
	$oldthumbfilename  = $row['thumbfilename'];

	// Check the category permission
	if ($row['user_id_cat'] == 0)
		GetCatPermission($row['id_cat'],'editpic');

	$smcFunc['db_free_result']($dbresult);
	if (allowedTo('smfgallery_manage') || (allowedTo('smfgallery_edit') && $user_info['id'] == $memID))
	{
		if (!is_writable($modSettings['gallery_path']))
			fatal_error($txt['gallery_write_error'] . $modSettings['gallery_path']);

		$title = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['title']), ENT_QUOTES);
		$description = $smcFunc['htmlspecialchars']($_REQUEST['descript'], ENT_QUOTES);
		$keywords = htmlspecialchars($_REQUEST['keywords'], ENT_QUOTES);
		$cat = (int) $_REQUEST['cat'];
		$allowcomments = isset($_REQUEST['allowcomments']) ? 1 : 0;
		$sendemail = isset($_REQUEST['sendemail']) ? 1 : 0;
		$markmature = isset($_REQUEST['markmature']) ? 1 : 0;
		$featured = isset($_REQUEST['featured']) ? 1 : 0;

		$allowRatings = 1;
		if ($gallerySettings['gallery_set_allowratings'])
			$allowRatings = isset($_REQUEST['allow_ratings']) ? 1 : 0;

		// Check if pictures are auto approved
		$approved = 0;


		if ($row['user_id_cat'] == 0)
		{
			$approve_tmp = GetCatPermission($row['user_id_cat'],'autoapprove',true);

			if ($approve_tmp !=2)
			{
				$approved = $approve_tmp;
			}

		}

		if (empty($approved))
		{
			$approved = (allowedTo('smfgallery_autoapprove') ? 1 : 0);
		}

		$exifData = '';

		// Allow comments on picture if no setting set.
		if (empty($modSettings['gallery_commentchoice']))
			$allowcomments = 1;

		if ($title == '')
			fatal_error($txt['gallery_error_no_title'],false);
		if (empty($cat))
			fatal_error($txt['gallery_error_no_cat'],false);

		// If keywords required
		if ($gallerySettings['gallery_set_require_keyword'] == true && empty($keywords))
			fatal_error($txt['gallery_txt_err_require_keyword'],false);

		// Check for any required custom fields
		if ($row['user_id_cat'] == 0)
		{
			$result = $smcFunc['db_query']('', "
			SELECT f.title, f.is_required, f.id_custom
			FROM {db_prefix}gallery_custom_field as f
			WHERE f.is_required = 1 AND (f.id_cat = " . $cat . " or f.id_cat = 0)");
			while ($row2 = $smcFunc['db_fetch_assoc']($result))
			{
				if (!isset($_REQUEST['cus_' . $row2['id_custom']]))
				{
					fatal_error($txt['gallery_err_req_custom_field'] . $row2['title'], false);
				}
				else
				{
					if ($_REQUEST['cus_' . $row2['id_custom']] == '')
						fatal_error($txt['gallery_err_req_custom_field'] . $row2['title'], false);
				}
			}
			$smcFunc['db_free_result']($result);
		}

		$image_resized = 0;
		require_once($sourcedir . '/Subs-Graphics.php');

		$testGD = get_extension_funcs('gd');
		$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
		unset($testGD);

		// Process Uploaded file
		$orginalfilename = '';
		if (isset($_FILES['picture']['name']) && $_FILES['picture']['name'] != '')
		{
			$sizes = @getimagesize($_FILES['picture']['tmp_name']);

			$orginalfilename = addslashes($_FILES['picture']['name']);
			// No size, then it's probably not a valid pic.
			if ($sizes === false)
			{
				@unlink($_FILES['picture']['tmp_name']);
				fatal_error($txt['gallery_error_invalid_picture'],false);
			}

			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : 'bmp';

			$gallerySettings['gallery_set_disallow_extensions'] = trim($gallerySettings['gallery_set_disallow_extensions']);
			$gallerySettings['gallery_set_disallow_extensions'] = str_replace(".","",$gallerySettings['gallery_set_disallow_extensions']);
			$gallerySettings['gallery_set_disallow_extensions'] = strtolower($gallerySettings['gallery_set_disallow_extensions']);
			$disallowedExtensions = explode(",",$gallerySettings['gallery_set_disallow_extensions']);
			if (in_array($extension,$disallowedExtensions))
			{
				fatal_error($txt['gallery_err_disallow_extensions'] . $extension,false);
				@unlink($_FILES['picture']['tmp_name']);
			}


			if (strtolower($extension) != 'png')
				$modSettings['avatar_download_png'] = 0;

		// Check min size
		if ((!empty($modSettings['gallery_min_width']) && $sizes[0] < $modSettings['gallery_min_width']) || (!empty($modSettings['gallery_min_height']) && $sizes[1] < $modSettings['gallery_min_height']))
		{
			// Delete the temp file
			@unlink($_FILES['picture']['tmp_name']);
			fatal_error($txt['gallery_error_img_size_height2'] . $sizes[1] . $txt['gallery_error_img_size_width2'] . $sizes[0],false);

		}


			if ((!empty($modSettings['gallery_max_width']) && $sizes[0] > $modSettings['gallery_max_width']) || (!empty($modSettings['gallery_max_height']) && $sizes[1] > $modSettings['gallery_max_height']))
			{
				if (!empty($modSettings['gallery_resize_image']))
				{
					// Check to resize image?
					$exifData = ReturnEXIFData($_FILES['picture']['tmp_name']);
					DoImageResize($sizes,$_FILES['picture']['tmp_name']);
					$image_resized = 1;
				}
				else
				{
					// Delete the temp file
					fatal_error($txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0],false);
				}
			}

			// Get the filesize
			if ($image_resized == 1)
				$filesize = filesize($_FILES['picture']['tmp_name']);
			else
				$filesize = $_FILES['picture']['size'];


			if (!empty($modSettings['gallery_max_filesize']) && $filesize > $modSettings['gallery_max_filesize'])
			{
				// Delete the temp file
				@unlink($_FILES['picture']['tmp_name']);
				fatal_error($txt['gallery_error_img_filesize'] . gallery_format_size($modSettings['gallery_max_filesize'], 2),false);
			}

			// Check Quota
			$quotalimit = GetQuotaGroupLimit($user_info['id']);
			$userspace = GetUserSpaceUsed($user_info['id']);
			// Check if exceeds quota limit or if there is a quota
			if ($quotalimit != 0  &&  ($userspace + $filesize) >  $quotalimit)
			{
				@unlink($_FILES['picture']['tmp_name']);
				fatal_error($txt['gallery_error_space_limit'] . gallery_format_size($userspace, 2) . ' / ' . gallery_format_size($quotalimit, 2) ,false);
			}

			// Delete the old files
			@unlink($modSettings['gallery_path'] . $oldfilename );
			@unlink($modSettings['gallery_path'] . $oldthumbfilename);

			$extrafolder = '';

			if ($modSettings['gallery_set_enable_multifolder'])
				$extrafolder = $modSettings['gallery_folder_id'] . '/';


			// Filename Member Id + Day + Month + Year + 24 hour, Minute Seconds
			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

			$filename = $user_info['id'] . '-' . date('dmyHis') . '.' . $extension;
			move_uploaded_file($_FILES['picture']['tmp_name'], $modSettings['gallery_path'] . $extrafolder . $filename);
			@chmod($modSettings['gallery_path'] . $extrafolder . $filename, 0644);

			if (!empty($_REQUEST['degrees']))
			{
				$degrees = (int) $_REQUEST['degrees'];
				GalleryRotateImage($modSettings['gallery_path'] . $extrafolder .  $filename,$degrees);
				$sizes = @getimagesize($modSettings['gallery_path'] . $extrafolder .  $filename);
			}

			// Create thumbnail
			GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder . $filename,  $modSettings['gallery_thumb_width'],  $modSettings['gallery_thumb_height']);
			rename($modSettings['gallery_path'] . $extrafolder . $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder . 'thumb_' . $filename);
			$thumbname = 'thumb_' . $filename;
			@chmod($modSettings['gallery_path'] . $extrafolder .  'thumb_' . $filename, 0755);

			if ($image_resized)
				$sizes = @getimagesize($modSettings['gallery_path'] . $extrafolder .  $filename);


			// Medium Image
			$mediumimage = '';

			if ($modSettings['gallery_make_medium'])
			{
				GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder .  $filename, $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
				rename($modSettings['gallery_path'] . $extrafolder .  $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename);
				$mediumimage = 'medium_' . $filename;
				@chmod($modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename, 0755);

				// Check for Watermark
				DoWaterMark($modSettings['gallery_path'] . $extrafolder .  'medium_' .  $filename);

			}

			// Update the Database entry
			$t = time();
			if ($row['user_id_cat'] == 0)
			{
				$smcFunc['db_query']('', "
			UPDATE {db_prefix}gallery_pic
			SET id_cat = $cat, featured = $featured, orginalfilename = '$orginalfilename', filesize = $filesize, filename = '" . $extrafolder . $filename . "',  thumbfilename = '" . $extrafolder . $thumbname . "', height = $sizes[1], width = $sizes[0], approved = $approved, date =  $t, title = '$title', description = '$description', keywords = '$keywords', allowcomments = $allowcomments, sendemail = $sendemail, mediumfilename = '" . $extrafolder . $mediumimage . "', mature = $markmature, allowratings = $allowRatings WHERE id_picture = $id LIMIT 1");
			}
			else
			{
				$smcFunc['db_query']('', "
			UPDATE {db_prefix}gallery_pic
			SET user_id_cat = $cat, featured = $featured, orginalfilename = '$orginalfilename', filesize = $filesize, filename = '" . $extrafolder . $filename . "',  thumbfilename = '" . $extrafolder . $thumbname . "', height = $sizes[1], width = $sizes[0], approved = $approved, date =  $t, title = '$title', description = '$description', keywords = '$keywords', allowcomments = $allowcomments, sendemail = $sendemail, mediumfilename = '" . $extrafolder . $mediumimage . "', mature = $markmature, allowratings = $allowRatings WHERE id_picture = $id LIMIT 1");
			}

			// Get EXIF Data
			ProcessEXIFData($extrafolder . $filename, $id, $exifData);


			$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_title_cache WHERE id_picture  = $id");
			$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_related_pictures WHERE id_picture_first = $id OR id_picture_second = $id");

			Gallery_AddRelatedPicture($id, $title);

            UpdateGalleryKeywords($id);

			UpdateUserFileSizeTable($memID,$oldfilesize * -1);
			UpdateUserFileSizeTable($memID,$filesize);

			// Update the category totals of the old category if we moved the picture to a different category
			if ($row['id_cat'] !=0)
			{
				if ($row['id_cat'] != $cat)
					UpdateCategoryTotals($row['id_cat']);

				Gallery_UpdateLatestCategory($cat);
			}
			else
			{
				if ($row['user_id_cat'] != $cat)
					UpdateUserCategoryTotals($row['user_id_cat']);

				Gallery_UpdateUserLatestCategory($cat);
			}

			// Last recheck Image if it was resized
			if ($image_resized == 1)
			{
				RecheckResizedImage($modSettings['gallery_path'] . $extrafolder . $filename,$id,$filesize,$memID);
			}

			// Check for Watermark
			DoWaterMark($modSettings['gallery_path'] . $extrafolder . $filename);


			if ($row['ID_TOPIC'] != 0 && $row['id_cat'] != 0)
			{
				UpdateMessagePost($row['ID_TOPIC'], $id);
			}

			// Change the picture owner if selected
			if (allowedTo('smfgallery_manage') && isset($_REQUEST['pic_postername']))
			{
				ChangePictureOwner($id,$memID);
			}

			UpdateCategoryTotalByPictureID($id);



            if ($user_info['id'] != $memID && allowedTo('smfgallery_manage'))
                Gallery_LogAction('editeditem',$id);

			if (isset($_SESSION['last_gallery_url']))
	 		{
	 			redirectexit($_SESSION['last_gallery_url']);
	 		}
	 		else
	 		{

                    if ($gallerySettings['gallery_set_redirectcategorydefault'] == 1)
                    {
                        If ($row['user_id_cat'] == 0)
                            redirectexit('action=gallery;cat=' . $cat);
                        else
                            redirectexit('action=gallery;su=user;cat=' . $cat . ';u=' . $user_info['id']);
                    }
                    else
                    {
				        // Redirect to the users image page.
				        redirectexit('action=gallery;sa=myimages;u=' . $user_info['id']);
                    }
	 		}
		}
		else
		{
			if (!empty($_REQUEST['degrees']))
			{
				$degrees = (int) $_REQUEST['degrees'];
				GalleryRotateImage($modSettings['gallery_path'] .$row['filename'],$degrees);

				// Create thumbnail
				@unlink($modSettings['gallery_path'] .$row['thumbfilename']);
				GalleryCreateThumbnail($modSettings['gallery_path'] .$row['filename'],  $modSettings['gallery_thumb_width'],  $modSettings['gallery_thumb_height']);
				rename($modSettings['gallery_path'] .$row['filename'] . '_thumb',  $modSettings['gallery_path'] .$row['thumbfilename']);
				@chmod($modSettings['gallery_path'] .$row['thumbfilename'], 0755);



				//GalleryRotateImage($modSettings['gallery_path'] .$row['mediumfilename'],$degrees);
				if ($modSettings['gallery_make_medium'])
				{
					@unlink($modSettings['gallery_path'] .$row['mediumfilename']);
					GalleryCreateThumbnail($modSettings['gallery_path'] .$row['filename'], $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
					rename($modSettings['gallery_path'] .$row['filename'] . '_thumb',  $modSettings['gallery_path'] .$row['mediumfilename']);
					@chmod($modSettings['gallery_path'] .$row['mediumfilename'], 0755);

				}

				$sizes = @getimagesize($modSettings['gallery_path'] . $row['filename']);
				$smcFunc['db_query']('',"UPDATE {db_prefix}gallery_pic
							SET height = $sizes[1], width = $sizes[0] WHERE id_picture = $id LIMIT 1");


			}

			     // Change the picture owner if selected
				if (allowedTo('smfgallery_manage') && isset($_REQUEST['pic_postername']))
				{
					ChangePictureOwner($id,$memID);
				}

			// Update the image properties if no upload has been set
			if ($row['user_id_cat'] == 0)
			{
				$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
				SET id_cat = $cat, featured = $featured, title = '$title', description = '$description', keywords = '$keywords', allowcomments = $allowcomments, sendemail = $sendemail, mature = $markmature, allowratings = $allowRatings WHERE id_picture = $id LIMIT 1");

				// Update the category totals of the old category if we moved the picture to a different category
				if ($row['id_cat'] !=0)
				{
					if ($row['id_cat'] != $cat)
						UpdateCategoryTotals($row['id_cat']);

					Gallery_UpdateLatestCategory($cat);
				}

				if ($row['ID_TOPIC'] != 0 && $row['id_cat'] != 0)
				{
					UpdateMessagePost($row['ID_TOPIC'], $id);
				}


				$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_title_cache WHERE id_picture  = $id");
				$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_related_pictures WHERE id_picture_first = $id OR id_picture_second = $id");

				Gallery_AddRelatedPicture($id, $title);

				UpdateCategoryTotalByPictureID($id);

				// Check for any custom fields
				$smcFunc['db_query']('', "DELETE FROM  {db_prefix}gallery_custom_field_data
						WHERE id_picture = " . $id);

				$result = $smcFunc['db_query']('', "
				SELECT
					f.title, f.is_required, f.id_custom
				FROM  {db_prefix}gallery_custom_field as f
				WHERE f.id_cat = " . $cat . " OR f.id_cat = 0");
				while ($row2 = $smcFunc['db_fetch_assoc']($result))
				{
					if (isset($_REQUEST['cus_' . $row2['id_custom']]))
					{
						$custom_data = $smcFunc['htmlspecialchars']($_REQUEST['cus_' . $row2['id_custom']],ENT_QUOTES);

						$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_custom_field_data
						(id_picture, id_custom, value)
						VALUES('$id', " . $row2['id_custom'] . ", '$custom_data')");
					}
				}
				$smcFunc['db_free_result']($result);
			}
			else
			{
				$smcFunc['db_query']('', "
					UPDATE {db_prefix}gallery_pic
					SET user_id_cat = $cat, featured = $featured, title = '$title', description = '$description',
						keywords = '$keywords', allowcomments = $allowcomments, sendemail = $sendemail, mature = $markmature,
						allowratings = $allowRatings
					WHERE id_picture = $id LIMIT 1");

				if ($row['user_id_cat'] !=0)
				{
					if ($row['user_id_cat'] != $cat)
						UpdateUserCategoryTotals($row['user_id_cat']);
				}

				UpdateCategoryTotalByPictureID($id);

				Gallery_UpdateUserLatestCategory($cat);
			}

			UpdateGalleryKeywords($id);


            if ($user_info['id'] != $memID && allowedTo('smfgallery_manage'))
                    Gallery_LogAction('editeditem',$id);

			if (isset($_SESSION['last_gallery_url']))
	 		{
	 			redirectexit($_SESSION['last_gallery_url']);
	 		}
	 		else
	 		{
                    if ($gallerySettings['gallery_set_redirectcategorydefault'] == 1)
                    {
                        If ($row['user_id_cat'] == 0)
                            redirectexit('action=gallery;cat=' . $cat);
                        else
                            redirectexit('action=gallery;su=user;cat=' . $cat . ';u=' . $user_info['id']);
                    }
                    else
                    {
			         	// Redirect to the users image page.
				        redirectexit('action=gallery;sa=myimages;u=' . $user_info['id']);
                    }
	 		}
		}
	}
	else
		fatal_error($txt['gallery_error_noedit_permission']);
}

function DeletePicture()
{
	global $context, $mbname, $txt, $user_info, $smcFunc;

	is_not_guest();

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	// Check if the user owns the picture or is admin
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.thumbfilename, p.width, p.height, p.allowcomments, p.id_cat, p.keywords,
			p.commenttotal, p.totallikes, p.filesize, p.filename, p.approved, p.views, p.title, p.id_member, p.date, m.real_name, p.description
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
		WHERE id_picture = $id LIMIT 1");

	if ($smcFunc['db_affected_rows']()== 0)
		fatal_error($txt['gallery_error_no_pictureexist'],false);
	$row = $smcFunc['db_fetch_assoc']($dbresult);

	// Check the category permission
	if ($row['id_cat'] != 0)
		GetCatPermission($row['id_cat'],'delpic');

	// Gallery picture information
	$context['gallery_pic'] = $row;
	$smcFunc['db_free_result']($dbresult);

	if (allowedTo('smfgallery_manage') || (allowedTo('smfgallery_delete') && $user_info['id'] == $context['gallery_pic']['id_member']))
	{
		$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_delpicture'];
		$context['sub_template']  = 'delete_picture';

        $context['linktree'][] = array(
			'name' => '<em>' . $txt['gallery_form_delpicture'] . '</em>'
		);

	}
	else
	{
		fatal_error($txt['gallery_error_nodelete_permission']);
	}
}

function DeletePicture2()
{
	global $txt, $user_info, $smcFunc;

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	// Check if the user owns the picture or is admin
	$dbresult = $smcFunc['db_query']('', "
	SELECT
	p.id_picture, p.user_id_cat, p.id_cat, p.id_member
	FROM {db_prefix}gallery_pic as p
	WHERE id_picture = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);

	$memID = $row['id_member'];
	$user_id_cat = $row['user_id_cat'];
	$smcFunc['db_free_result']($dbresult);
	// Check the category permission
	if ($user_id_cat == 0)
		GetCatPermission($row['id_cat'],'delpic');

	if (allowedTo('smfgallery_manage') || (allowedTo('smfgallery_delete') && $user_info['id'] == $memID))
	{
		DeletePictureByID($id);

		if ($user_id_cat == 0)
			UpdateCategoryTotals($row['id_cat']);
		else
			UpdateUserCategoryTotals($user_id_cat);

		if (isset($_SESSION['last_gallery_url']))
 		{
 			redirectexit($_SESSION['last_gallery_url']);
 		}
 		else
 		{
			// Redirect to the users image page.
			redirectexit('action=gallery;sa=myimages;u=' . $user_info['id']);
 		}
	}
	else
	{
		fatal_error($txt['gallery_error_nodelete_permission']);
	}
}

function DeletePictureByID($id)
{
	global $modSettings, $smcFunc, $sourcedir, $gallerySettings;

	require_once($sourcedir . '/RemoveTopic.php');

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.user_id_cat, p.id_cat, p.filesize,
			p.filename, p.thumbfilename, p.id_member, p.videofile, p.type,
			p.ID_TOPIC, p.mediumfilename, p.ID_CAT, p.id_msg 
		FROM {db_prefix}gallery_pic as p
		WHERE id_picture = $id LIMIT 1");

	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$oldfilesize = $row['filesize'];
	$memID = $row['id_member'];
	$smcFunc['db_free_result']($dbresult);

	// Check if there is a video file to delete
	if ($row['type'] == 1)
	{
		@unlink($modSettings['gallery_path'] .'videos/' . $row['videofile']);
	}

	// Delete the medium filename if it exists
	if ($row['mediumfilename'] != '')
		@unlink($modSettings['gallery_path'] . $row['mediumfilename']);

	// Delete Large image
	@unlink($modSettings['gallery_path'] . $row['filename']);
	// Delete Thumbnail
	@unlink($modSettings['gallery_path'] . $row['thumbfilename']);
	// Update the quota
	$oldfilesize = $oldfilesize * -1;

	UpdateUserFileSizeTable($memID,$oldfilesize);

	UpdateCategoryTotalByPictureID($id);

	// Delete all the picture related db entries
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_comment WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_rating WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_report WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_creport WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_custom_field_data WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_exif_data WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_tags_log WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_pic_tagging WHERE id_picture = $id");
    $smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_favorites  WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_title_cache WHERE id_picture = $id");
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_related_pictures WHERE id_picture_first = $id OR id_picture_second = $id");

	// Delete the picture
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_pic WHERE id_picture = $id LIMIT 1");

	// Update the SMF Shop Points
	if (isset($modSettings['shopVersion']))
		$smcFunc['db_query']('', "UPDATE {db_prefix}members
			SET money = money - " . $modSettings['gallery_shop_picadd'] . "
			WHERE id_member = {$memID}
			LIMIT 1");

	if (!empty($row['id_msg']))
	{
		$dbresult = $smcFunc['db_query']('', "
			SELECT
				id_first_msg
			FROM {db_prefix}topics 
			WHERE id_topic = " . $row['ID_TOPIC'] . " LIMIT 1");
			$row2 = $smcFunc['db_fetch_assoc']($dbresult);

			if ($row2['id_first_msg'] == $row['id_msg'])
			{
				// Remove the Topic
				if ($row['ID_TOPIC'] != 0 && empty($gallerySettings['gallery_set_disableremovetopic']))
					removeTopics($row['ID_TOPIC']);
			}
			else
			{
				 if ($row['ID_TOPIC'] != 0 && $row['id_msg']  != 0 && empty($gallerySettings['gallery_set_disableremovetopic']))
 					gallery_removeMessage($row['id_msg']);
			}
	}
	else
	{
		if ($row['ID_TOPIC'] != 0 && empty($gallerySettings['gallery_set_disableremovetopic']))
			removeTopics($row['ID_TOPIC']);
	}



	Gallery_UpdateLatestCategory($row['ID_CAT']);

	Gallery_UpdateUserLatestCategory($row['user_id_cat']);

	UpdateMemberPictureTotals($memID);


    Gallery_LogAction('deleteditem',$id);

}

function ReportPicture()
{
	global $context, $mbname, $txt;

	isAllowedTo('smfgallery_report');

	is_not_guest();

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);


	$context['gallery_pic_id'] = $id;
	$context['sub_template']  = 'report_picture';
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_reportpicture'];

    $context['linktree'][] = array(
			'name' => '<em>' . $txt['gallery_form_reportpicture'] . '</em>'
		);

	// Register this form and get a sequence number in $context.
	checkSubmitOnce('register');

	// Spam Protect
	spamProtection('gallery');
}

function ReportPicture2()
{
	global $smcFunc, $user_info, $txt, $smcFunc, $scripturl;

	isAllowedTo('smfgallery_report');

	$comment = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['comment']), ENT_QUOTES);
	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	checkSubmitOnce('check');

	if ($comment == '')
		fatal_error($txt['gallery_error_no_comment'],false);

	$commentdate = time();

	$smcFunc['db_query']('', "
		INSERT INTO {db_prefix}gallery_report
			(id_member, comment, date, id_picture)
		VALUES
			(" . $user_info['id'] . ", {string:comment}, $commentdate, $id)",
			array('comment' => $comment)
		);

    $body = $txt['gallery_txt_itemreported2'];
    $body = str_replace("%url",$scripturl . '?action=admin;area=gallery;sa=approvelist',$body);

    Gallery_emailAdmins($txt['gallery_txt_itemreported'],$body);


	redirectexit('action=gallery;sa=view;id=' . $id);

}

function AddComment()
{
	global $context, $mbname, $txt, $modSettings, $user_info, $smcFunc, $sourcedir;

	isAllowedTo('smfgallery_comment');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);


	$context['gallery_pic_id'] = $id;

	// Comments allowed check
	$dbresult = $smcFunc['db_query']('', "
		SELECT
		p.allowcomments, p.id_cat, p.user_id_cat, p.thumbfilename
		FROM {db_prefix}gallery_pic as p
		WHERE id_picture = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$id_cat = $row['id_cat'];
	$smcFunc['db_free_result']($dbresult);

	// Checked if comments are allowed
	if ($row['allowcomments'] == 0)
	{
		fatal_error($txt['gallery_error_not_allowcomment']);
	}
	if ($row['id_cat'] != 0 )
		GetCatPermission($id_cat,'addcomment');


	// Do we need to show the visual verification image?
	$context['require_verification'] = $user_info['is_guest'];
	if ($context['require_verification'])
	{
		require_once($sourcedir . '/Subs-Editor.php');
		$verificationOptions = array(
			'id' => 'post',
		);
		$context['require_verification'] = create_control_verification($verificationOptions);
		$context['visual_verification_id'] = $verificationOptions['id'];


	}

	$context['gallery_pic_thumbfilename'] = $row['thumbfilename'];

	$context['sub_template']  = 'add_comment';

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_addcomment'];

	$context['linktree'][] = array(
			'name' => '<em>' .  $txt['gallery_text_addcomment']. '</em>'
		);

	// Check if spellchecking is both enabled and actually working.
	$context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');

// Register this form and get a sequence number in $context.
	//checkSubmitOnce('register');

	// Spam Protect
	//spamProtection('spam');

	// Needed for the WYSIWYG editor.
	require_once($sourcedir . '/Subs-Editor.php');


	$commentvalue = '';

	if (isset($_REQUEST['comment']))
	{
		$commentID = (int) $_REQUEST['comment'];

		$dbresult = $smcFunc['db_query']('', "
			SELECT
				c.comment, c.ID_MEMBER, m.real_name
			FROM {db_prefix}gallery_comment as c
				LEFT JOIN {db_prefix}members as m ON (m.ID_MEMBER = c.ID_MEMBER)
			WHERE c.id_picture = $id and  c.id_comment = $commentID LIMIT 1");
		$commentRow = $smcFunc['db_fetch_assoc']($dbresult);

		$commentvalue = '[quote';

		if (!empty($commentRow['real_name']))
			$commentvalue .= ' author=' . $commentRow['real_name'];

		$commentvalue .= ']';

		$commentvalue .= $commentRow['comment'];
		$commentvalue .= '[/quote]';

	}


	// Now create the editor.
	$editorOptions = array(
		'id' => 'comment',
		'value' => $commentvalue,
		'width' => '90%',
		'form' => 'cprofile',
		'labels' => array(
			'post_button' => $txt['gallery_text_addcomment']
		),
	);
	create_control_richedit($editorOptions);
	$context['post_box_name'] = $editorOptions['id'];

}

function AddComment2()
{
	global $scripturl, $smcFunc, $user_info, $txt, $sourcedir, $modSettings, $smcFunc, $gallerySettings;

	isAllowedTo('smfgallery_comment');

	// If we came from WYSIWYG then turn it back into BBC regardless.
	if (!empty($_REQUEST['comment_mode']) && isset($_REQUEST['comment'])   && !function_exists("set_tld_regex"))
	{
		require_once($sourcedir . '/Subs-Editor.php');

		$_REQUEST['comment'] = html_to_bbc($_REQUEST['comment']);

		// We need to unhtml it now as it gets done shortly.
		$_REQUEST['comment'] = un_htmlspecialchars($_REQUEST['comment']);
	}

	$comment = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['comment']), ENT_QUOTES);
	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	if ($user_info['is_guest'] == true)
	{

		if (!empty($modSettings['reg_verification']))
		{
			require_once($sourcedir . '/Subs-Editor.php');
			$verificationOptions = array(
				'id' => 'post',
			);
			$context['visual_verification'] = create_control_verification($verificationOptions, true);

			if (is_array($context['visual_verification']))
			{
				loadLanguage('Errors');
				foreach ($context['visual_verification'] as $error)
					fatal_error($txt['error_' . $error],false);
			}
		}
	}

	// Check if that picture allows comments.
	$dbresult = $smcFunc['db_query']('', "
	SELECT
	p.allowcomments, p.id_cat, p.sendemail, m.email_address, p.id_member, p.title
	FROM {db_prefix}gallery_pic as p
	LEFT JOIN {db_prefix}members as m ON (p.id_member  = m.id_member)
	WHERE p.id_picture = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$mem_email = $row['email_address'];
	$title = $row['title'];
	$doemail = $row['sendemail'];
	$pic_memid = $row['id_member'];

	$smcFunc['db_free_result']($dbresult);
	// Checked if comments are allowed
	if ($row['allowcomments'] == 0)
	{
		fatal_error($txt['gallery_error_not_allowcomment']);
	}
	// Check if they are allowed to add comments to that category
	if ($row['id_cat'] != 0)
		GetCatPermission($row['id_cat'],'addcomment');

	if ($comment == '')
		fatal_error($txt['gallery_error_no_comment'],false);

	$commentdate = time();

	//Check if you have automatic approval
	$approved = (allowedTo('smfgallery_autocomment') ? 1 : 0);

	$smcFunc['db_query']('', "
		INSERT INTO {db_prefix}gallery_comment
			(id_member, comment, date, id_picture,approved)
		VALUES (" . $user_info['id'] . ", {string:comment}, $commentdate, $id, $approved)",
		array('comment' => $comment)
	);
	$comment_id = $smcFunc['db_insert_id']('{db_prefix}gallery_comment', 'id_comment');

	// Update Comment total
	 $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
		SET commenttotal = commenttotal + 1 WHERE id_picture = $id LIMIT 1");

    if ($approved == 0)
    {
        $body = $txt['gallery_txt_commentapproval2'];
        $body = str_replace("%url",$scripturl . '?action=admin;area=gallery;sa=commentlist',$body);

        Gallery_emailAdmins($txt['gallery_txt_commentapproval'],$body);
    }

	// Check to send email on new comment
	 if ($doemail == 1 && $pic_memid != $user_info['id'] && $pic_memid != 0 && $approved == 1)
	 {
		require_once($sourcedir . '/Subs-Post.php');
		sendmail($mem_email, str_replace("%s", $title, $txt['gallery_notify_subject']), str_replace("%s", $scripturl . '?action=gallery;sa=view;id=' . $id . '#c' . $comment_id, $txt['gallery_notify_body']),null,'gallery');
	 }

      // Add Post Count
     if (!empty($gallerySettings['gallery_set_commentspostcount']))
     {
        if ($user_info['id'] != 0)
        {
            updateMemberData($user_info['id'], array('posts' => '+'));
        }
     }

     Gallery_AddToActivityStream('gallerypro_comments',$id,$title,$user_info['id']);

	// Badge Awards Mod Check
	 GalleryCheckBadgeAwards($user_info['id']);

			// Update the SMF Shop Points
			if (isset($modSettings['shopVersion']))
				$smcFunc['db_query']('', "UPDATE {db_prefix}members
					SET money = money + " . $modSettings['gallery_shop_commentadd'] . "
					WHERE id_member = " . $user_info['id'] . "
					LIMIT 1");

	redirectexit('action=gallery;sa=view;id=' . $id);
}

function EditComment()
{
	global $context, $mbname, $txt, $modSettings, $user_info, $smcFunc, $sourcedir;

	is_not_guest();

	$g_manage = allowedTo('smfgallery_manage');
	$g_edit_comment = allowedTo('smfgallery_editcomment');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error(fatal_error($txt['gallery_error_no_com_selected']));


	// Check if allowed to edit the comment
	$dbresult = $smcFunc['db_query']('', "
		SELECT
		id_comment,id_picture,id_member,approved,comment,date,lastmodified
		FROM {db_prefix}gallery_comment
		WHERE id_comment = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);

   // Comment information
	$context['gallery_comment'] = $row;

	$smcFunc['db_free_result']($dbresult);

	// Get thumbnail filename
	$dbresult = $smcFunc['db_query']('', "
		SELECT
		p.thumbfilename
		FROM {db_prefix}gallery_pic as p
		WHERE id_picture = " . $context['gallery_comment']['id_picture'] . " LIMIT 1");

	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$context['gallery_pic_thumbfilename'] = $row['thumbfilename'];
	$smcFunc['db_free_result']($dbresult);

	if ($g_manage || $g_edit_comment && $context['gallery_comment']['id_member'] == $user_info['id'])
	{
		$context['sub_template']  = 'edit_comment';
		$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_editcomment'];

        $context['linktree'][] = array(
			'name' => '<em>' . $txt['gallery_text_editcomment'] . '</em>'
		);


		// Check if spellchecking is both enabled and actually working.
		$context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');


		// Needed for the WYSIWYG editor.
		require_once($sourcedir . '/Subs-Editor.php');

		// Now create the editor.
		$editorOptions = array(
			'id' => 'comment',
			'value' => $context['gallery_comment']['comment'],
			'width' => '90%',
			'form' => 'cprofile',
			'labels' => array(
				'post_button' => $txt['gallery_text_editcomment']
			),
		);
		create_control_richedit($editorOptions);
		$context['post_box_name'] = $editorOptions['id'];


	}
	else
		fatal_error($txt['gallery_error_nocomedit_permission']);
}

function EditComment2()
{
	global $context, $txt, $smcFunc, $user_info, $sourcedir;

	is_not_guest();

	$g_manage = allowedTo('smfgallery_manage');
	$g_edit_comment = allowedTo('smfgallery_editcomment');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error(fatal_error($txt['gallery_error_no_com_selected']));

	// If we came from WYSIWYG then turn it back into BBC regardless.
	if (!empty($_REQUEST['comment_mode']) && isset($_REQUEST['comment'])   && !function_exists("set_tld_regex"))
	{
		require_once($sourcedir . '/Subs-Editor.php');

		$_REQUEST['comment'] = html_to_bbc($_REQUEST['comment']);

		// We need to unhtml it now as it gets done shortly.
		$_REQUEST['comment'] = un_htmlspecialchars($_REQUEST['comment']);
	}

	// Check if allowed to edit the comment
	$dbresult = $smcFunc['db_query']('', "
	SELECT
	id_member,id_picture
	FROM {db_prefix}gallery_comment
	WHERE id_comment = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);

   // Comment information
	$context['gallery_comment'] = $row;

	$smcFunc['db_free_result']($dbresult);

	if ($g_manage || $g_edit_comment && $context['gallery_comment']['id_member'] == $user_info['id'])
	{



		$comment = $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['comment']), ENT_QUOTES);
		if ($comment == '')
			fatal_error($txt['gallery_error_no_comment'],false);

        if ($context['gallery_comment']['id_member'] != $user_info['id']  && $g_manage == true)
         Gallery_LogAction('editcomment',0,$id);


		$edittime = time();
		//Check if you have automatic approval
		$approved = (allowedTo('smfgallery_autocomment') ? 1 : 0);
		//Update the comment

		echo "UPDATE {db_prefix}gallery_comment
		SET comment = '$comment', lastmodified = '$edittime', modified_id_member = " . $user_info['id'] . ", approved =  $approved WHERE id_comment = $id LIMIT 1";

	  $dbresult = $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_comment
		SET comment = '$comment', lastmodified = '$edittime', modified_id_member = " . $user_info['id'] . ", approved =  $approved WHERE id_comment = $id LIMIT 1");
		//Redirect to the picture
		redirectexit('action=gallery;sa=view;id=' .  $context['gallery_comment']['id_picture']);
	}
	else
		fatal_error($txt['gallery_error_nocomedit_permission']);
}

function DeleteCommentByID($id)
{
    global $smcFunc, $txt, $modSettings;

    if (empty($id))
    	return 0;

    // Get the picture ID for redirect
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		id_picture,id_comment, id_member
	FROM {db_prefix}gallery_comment
	WHERE id_comment = $id LIMIT 1");

	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$picid = (int) $row['id_picture'];
	$memID = $row['id_member'];
	$smcFunc['db_free_result']($dbresult);

	// Delete all the comment reports that comment
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_creport WHERE id_comment = $id");
	// Now delete the comment.
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_comment WHERE id_comment = $id");


	// Update Comment total
    $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
		SET commenttotal = commenttotal - 1 WHERE id_picture = $picid LIMIT 1");

	  // Update the SMF Shop Points
			if (isset($modSettings['shopVersion']))
				$smcFunc['db_query']('', "UPDATE {db_prefix}members
					SET money = money - " . $modSettings['gallery_shop_commentadd'] . "
					WHERE id_member = {$memID}
					LIMIT 1");

     Gallery_LogAction('deletecomment',$picid,$id);

	return $picid;
}

function DeleteComment()
{
	global $smcFunc, $txt, $modSettings;

	is_not_guest();
	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];
	if (isset($_REQUEST['ret']))
		$ret = $_REQUEST['ret'];

	if (empty($id))
		fatal_error($txt['gallery_error_no_com_selected']);

	$picid = DeleteCommentByID($id);

	// Redirect to the picture
	if (empty($ret))
	{
		redirectexit('action=gallery;sa=view;id=' . $picid);
	}
	else
	{
		redirectexit('action=admin;area=gallery;sa=commentlist');
	}
}

function ReportComment()
{
	global $context, $mbname, $txt;
	isAllowedTo('smfgallery_report');
	is_not_guest();
	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_com_selected']);

	$context['gallery_comment_id'] = $id;

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_reportcomment'];
	$context['sub_template']  = 'report_comment';

    $context['linktree'][] = array(
			'name' => '<em>' . $txt['gallery_text_reportcomment'] . '</em>'
		);

	// Register this form and get a sequence number in $context.
	checkSubmitOnce('register');

	// Spam Protect
	spamProtection('gallery');
}

function ReportComment2()
{
	global $smcFunc, $user_info, $txt, $scripturl;

	isAllowedTo('smfgallery_report');

	$comment = $smcFunc['htmlspecialchars']($_REQUEST['comment'],ENT_QUOTES);
	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_com_selected']);

	checkSubmitOnce('check');

	if (empty($comment))
		fatal_error($txt['gallery_error_no_comment'],false);

	$dbresult = $smcFunc['db_query']('', "
	SELECT
		id_picture
	FROM {db_prefix}gallery_comment
	WHERE id_comment = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$picid = $row['id_picture'];
	$smcFunc['db_free_result']($dbresult);

	$commentdate = time();

	$smcFunc['db_query']('', "
		INSERT INTO {db_prefix}gallery_creport
			(id_member, comment, date, id_comment, id_picture)
		VALUES (" . $user_info['id'] . ", '$comment', $commentdate,$id,$picid)");


        $body = $txt['gallery_txt_commentreported2'];
        $body = str_replace("%url",$scripturl . '?action=admin;area=gallery;sa=commentlist',$body);

        Gallery_emailAdmins($txt['gallery_txt_commentreported'],$body);


	redirectexit('action=gallery;sa=view;id=' . $picid);
}

function ApproveComment()
{
	global $smcFunc, $txt;

	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_com_selected']);

	// Approve the comment
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_comment
		SET approved = 1 WHERE id_comment = $id LIMIT 1");

    Gallery_LogAction('approvedcomment',0,$id);

	// Reditrect the comment list
	redirectexit('action=gallery;sa=commentlist');
}

function CommentList()
{
	global $scripturl, $smcFunc, $context, $mbname, $txt;

	isAllowedTo('smfgallery_manage');


		// Get Total Pages
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) AS total
		FROM {db_prefix}gallery_comment
		WHERE approved = 0 ORDER BY id_comment DESC");
		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$numofrows = $row['total'];
		$smcFunc['db_free_result']($dbresult);

		$context['start'] = (int) $_REQUEST['start'];

		// List all Comments waiting approval
		$dbresult = $smcFunc['db_query']('', "
			SELECT c.id_comment, c.id_picture, c.comment, c.date, c.id_member, m.real_name FROM {db_prefix}gallery_comment as c
			LEFT JOIN {db_prefix}members AS m ON (c.id_member = m.id_member)
			WHERE c.approved = 0 ORDER BY c.id_comment DESC LIMIT $context[start],10");
		$context['gallery_commnets_approvallist'] = array();
		while($row = $smcFunc['db_fetch_assoc']($dbresult))
		{
			$context['gallery_commnets_approvallist'][] = $row;

		}
	$smcFunc['db_free_result']($dbresult);

	$context['page_index'] = constructPageIndex($scripturl . '?action=admin;area=gallery;sa=commentlist', $_REQUEST['start'], $numofrows, 10);


	// Reported comments
	$dbresult = $smcFunc['db_query']('', "
			SELECT
				c.id, c.id_picture, c.id_comment,  c.id_member, m.real_name, c.date,c.comment, d.comment OringalComment
			FROM
				({db_prefix}gallery_creport as c, {db_prefix}gallery_comment AS d)
				LEFT JOIN {db_prefix}members AS m ON (c.id_member = m.id_member)
			WHERE  c.id_comment = d.id_comment ORDER BY c.id_picture DESC");
	$context['gallery_reported_commentlist'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_reported_commentlist'][] = $row;

	}
	$smcFunc['db_free_result']($dbresult);


	if (isset($_REQUEST['comments']))
	{
		$baction = $_REQUEST['doaction'];

		foreach ($_REQUEST['comments'] as $value)
		{
		      $value = (int) $value;

			if ($baction == 'approve')
            {
                // Approve the comment
            	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_comment
            		SET approved = 1 WHERE ID_COMMENT = $value LIMIT 1");

                Gallery_LogAction('approvedcomment',0,$value);
            }
            if ($baction == 'delete')
				DeleteCommentByID($value);
		}
	}


	DoGalleryAdminTabs();

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_approvecomments'];
	$context['sub_template']  = 'comment_list';

}

function AdminSettings()
{
	global $context, $mbname, $txt, $modSettings;
	isAllowedTo('smfgallery_manage');


	DoGalleryAdminTabs();

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_settings'];

	$context['sub_template']  = 'settings';

	// Check if image editor is installed
	if (file_exists($modSettings['gallery_path'] . "pixie/scripts.min.js"))
		$context['image_editor_installed'] = 1;
	else
		$context['image_editor_installed'] = 0;
}

function AdminSettings2()
{
	global $gallerySettings, $smcFunc;

	isAllowedTo('smfgallery_manage');

	// Get the settings
	$gallery_max_height = (int) $_REQUEST['gallery_max_height'];
	$gallery_max_width =  (int) $_REQUEST['gallery_max_width'];
	$gallery_min_height = (int) $_REQUEST['gallery_min_height'];
	$gallery_min_width =  (int) $_REQUEST['gallery_min_width'];

	$gallery_max_filesize =  (int) $_REQUEST['gallery_max_filesize'];
	$gallery_set_images_per_page = (int) $_REQUEST['gallery_set_images_per_page'];
	$gallery_set_images_per_row = (int) $_REQUEST['gallery_set_images_per_row'];
	$gallery_commentchoice =  isset($_REQUEST['gallery_commentchoice']) ? 1 : 0;
	$gallery_set_picturepostcount =  isset($_REQUEST['gallery_set_picturepostcount']) ? 1 : 0;
    $gallery_set_commentspostcount = isset($_REQUEST['gallery_set_commentspostcount']) ? 1 : 0;
	$gallery_set_allowratings = isset($_REQUEST['gallery_set_allowratings']) ? 1 : 0;
	$gallery_path = $_REQUEST['gallery_path'];
	$gallery_url = $_REQUEST['gallery_url'];
	$gallery_jpeg_compression = (int) $_REQUEST['gallery_jpeg_compression'];
	$gallery_who_viewing = isset($_REQUEST['gallery_who_viewing']) ? 1 : 0;
	$gallery_resize_image =  isset($_REQUEST['gallery_resize_image']) ? 1 : 0;
	$gallery_set_commentsnewest = isset($_REQUEST['gallery_set_commentsnewest']) ? 1 : 0;
	$gallery_set_enable_multifolder = isset($_REQUEST['gallery_set_enable_multifolder']) ? 1 : 0;
	$gallery_show_ratings =  isset($_REQUEST['gallery_show_ratings']) ? 1 : 0;
	$gallery_set_nohighslide = (int) $_REQUEST['gallery_set_nohighslide'];
	$gallery_setviewscountonce = isset($_REQUEST['gallery_setviewscountonce']) ? 1 : 0;
	$gallery_ratings_require_comment = isset($_REQUEST['gallery_ratings_require_comment']) ? 1 : 0;
	$gallery_set_count_child = isset($_REQUEST['gallery_set_count_child']) ? 1 : 0;
	$gallery_set_show_subcategory_links = isset($_REQUEST['gallery_set_show_subcategory_links']) ? 1 : 0;
	$gallery_allow_mature_tag = isset($_REQUEST['gallery_allow_mature_tag']) ? 1 : 0;
	$gallery_set_show_quickreply = isset($_REQUEST['gallery_set_show_quickreply']) ? 1 : 0;
	$gallery_set_quickreply_full = isset($_REQUEST['gallery_set_quickreply_full']) ? 1 : 0;
	$gallery_set_disallow_extensions = htmlspecialchars($_REQUEST['gallery_set_disallow_extensions'],ENT_QUOTES);

	$gallery_make_medium = isset($_REQUEST['gallery_make_medium']) ? 1 : 0;
	$gallery_medium_width = (int) $_REQUEST['gallery_medium_width'];
	$gallery_medium_height = (int) $_REQUEST['gallery_medium_height'];

	$gallery_thumb_width = (int) $_REQUEST['gallery_thumb_width'];
	$gallery_thumb_height = (int) $_REQUEST['gallery_thumb_height'];

	$gallery_set_disp_maxwidth = (int) $_REQUEST['gallery_set_disp_maxwidth'];
	$gallery_set_disp_maxheight = (int) $_REQUEST['gallery_set_disp_maxheight'];

	$gallery_set_cat_width = (int) $_REQUEST['gallery_set_cat_width'];
	$gallery_set_cat_height = (int) $_REQUEST['gallery_set_cat_height'];

	$gallery_set_maxuploadperday = (int) $_REQUEST['gallery_set_maxuploadperday'];
	$gallery_set_relatedimagescount = (int) $_REQUEST['gallery_set_relatedimagescount'];

	// User Gallery Settings
	$gallery_user_no_password = isset($_REQUEST['gallery_user_no_password']) ? 1 : 0;
	$gallery_user_no_private =  isset($_REQUEST['gallery_user_no_private']) ? 1 : 0;

	// Watermark Settings
	$gallery_set_water_enabled = isset($_REQUEST['gallery_set_water_enabled']) ? 1 : 0;
	$gallery_set_water_image = htmlspecialchars($_REQUEST['gallery_set_water_image'],ENT_QUOTES);
	$gallery_set_water_percent = (int) $_REQUEST['gallery_set_water_percent'];
	$gallery_set_water_text = htmlspecialchars($_REQUEST['gallery_set_water_text'],ENT_QUOTES);
	$gallery_set_water_textcolor = htmlspecialchars($_REQUEST['gallery_set_water_textcolor'],ENT_QUOTES);
	$gallery_set_water_valign = htmlspecialchars($_REQUEST['gallery_set_water_valign'],ENT_QUOTES);
	$gallery_set_water_halign = htmlspecialchars($_REQUEST['gallery_set_water_halign'],ENT_QUOTES);
	$gallery_set_water_textfont = htmlspecialchars($_REQUEST['gallery_set_water_textfont'],ENT_QUOTES);
	$gallery_set_water_textsize = (int) $_REQUEST['gallery_set_water_textsize'];

	// Shop settings
	$gallery_shop_picadd = (int) $_REQUEST['gallery_shop_picadd'];
	$gallery_shop_commentadd = (int) $_REQUEST['gallery_shop_commentadd'];

	// Image Linking codes
	$gallery_set_showcode_bbc_image = isset($_REQUEST['gallery_set_showcode_bbc_image']) ? 1 : 0;
	$gallery_set_showcode_directlink = isset($_REQUEST['gallery_set_showcode_directlink']) ? 1 : 0;
	$gallery_set_showcode_htmllink = isset($_REQUEST['gallery_set_showcode_htmllink']) ? 1 : 0;

	// More Settings
	$gallery_points_instead_stars = isset($_REQUEST['gallery_points_instead_stars']) ? 1 : 0;
	$gallery_enable_rss = isset($_REQUEST['gallery_enable_rss']) ? 1 : 0;
	$gallery_allow_slideshow = isset($_REQUEST['gallery_allow_slideshow']) ? 1 : 0;
	$gallery_set_allow_photo_tagging = isset($_REQUEST['gallery_set_allow_photo_tagging']) ? 1 : 0;
	$gallery_set_multifile_upload_for_bulk = isset($_REQUEST['gallery_set_multifile_upload_for_bulk']) ? 1 : 0;
	$gallery_set_allow_copy = isset($_REQUEST['gallery_set_allow_copy']) ? 1 : 0;

	$gallery_set_require_keyword = isset($_REQUEST['gallery_set_require_keyword']) ? 1 : 0;
	$gallery_set_allow_favorites = isset($_REQUEST['gallery_set_allow_favorites']) ? 1 : 0;

	$gallery_set_batchadd_path = $_REQUEST['gallery_set_batchadd_path'];
	$gallery_set_showcode_bbc_thumbnail = isset($_REQUEST['gallery_set_showcode_bbc_thumbnail']) ? 1 : 0;
	$gallery_set_showcode_bbc_medium = isset($_REQUEST['gallery_set_showcode_bbc_medium']) ? 1 : 0;

	$gallery_userlist_usersperpage = (int) $_REQUEST['gallery_userlist_usersperpage'];
	$gallery_userlist_orderby = htmlspecialchars($_REQUEST['gallery_userlist_orderby'],ENT_QUOTES);
	$gallery_userlist_sortby = htmlspecialchars($_REQUEST['gallery_userlist_sortby'], ENT_QUOTES);
	$gallery_userlist_onlyuploaders = isset($_REQUEST['gallery_userlist_onlyuploaders']) ? 1 : 0;
	$gallery_userlist_hideavatar = isset($_REQUEST['gallery_userlist_hideavatar']) ? 1 : 0;
    $gallery_set_createusercat = isset($_REQUEST['gallery_set_createusercat']) ? 1 : 0;

    $gallery_set_disableremovetopic = isset($_REQUEST['gallery_set_disableremovetopic']) ? 1 : 0;
    $gallery_set_redirectcategorydefault = isset($_REQUEST['gallery_set_redirectcategorydefault']) ? 1 : 0;

    $gallery_set_onlyregcanviewimage = isset($_REQUEST['gallery_set_onlyregcanviewimage']) ? 1 : 0;
    $gallery_set_useseourls = isset($_REQUEST['gallery_set_useseourls']) ? 1 : 0;
    $gallery_set_likesystem = isset($_REQUEST['gallery_set_likesystem']) ? 1 : 0;
    $gallery_disable_membercolorlink = isset($_REQUEST['gallery_disable_membercolorlink']) ? 1 : 0;
    $gallery_image_editor = isset($_REQUEST['gallery_image_editor']) ? 1 : 0;

	if (empty($gallery_thumb_height))
		$gallery_thumb_height = 78;

	if (empty($gallery_thumb_width))
		$gallery_thumb_width = 120;

	if (empty($gallery_set_cat_height))
		$gallery_set_cat_height = 120;

	if (empty($gallery_set_cat_width))
		$gallery_set_cat_width = 120;

	$gallery_set_searchenablefulltext = isset($_REQUEST['gallery_set_searchenablefulltext']) ? 1 : 0;

	if ($gallery_set_searchenablefulltext == 1)
	{
		if (empty($gallerySettings['gallery_search_fulltext_index']))
		{
			// Ok we need to build the index
			$smcFunc['db_query']('', "ALTER TABLE {db_prefix}gallery_pic ADD FULLTEXT(title,description,keywords)");


			UpdateGallerySettings(
				array(
				'gallery_search_fulltext_index' => 1,
				));
		}
	}

	UpdateGallerySettings(
	array(
	'gallery_points_instead_stars' => $gallery_points_instead_stars,
	'gallery_enable_rss' => $gallery_enable_rss,
	'gallery_allow_slideshow' => $gallery_allow_slideshow,
	'gallery_userlist_usersperpage' => $gallery_userlist_usersperpage,
	'gallery_userlist_orderby' => $gallery_userlist_orderby,
	'gallery_userlist_sortby' => $gallery_userlist_sortby,
	'gallery_userlist_onlyuploaders' => $gallery_userlist_onlyuploaders,
	'gallery_userlist_hideavatar' => $gallery_userlist_hideavatar,
    	'gallery_set_createusercat' => $gallery_set_createusercat,
	'gallery_set_show_subcategory_links' => $gallery_set_show_subcategory_links,
	'gallery_set_allow_photo_tagging' => $gallery_set_allow_photo_tagging,
	'gallery_set_multifile_upload_for_bulk' => $gallery_set_multifile_upload_for_bulk,
	'gallery_set_allow_copy' => $gallery_set_allow_copy,
	'gallery_set_require_keyword' => $gallery_set_require_keyword,
	'gallery_set_allow_favorites' => $gallery_set_allow_favorites,
	'gallery_set_quickreply_full' => $gallery_set_quickreply_full,
	'gallery_set_disallow_extensions' => $gallery_set_disallow_extensions,
	'gallery_set_allowratings' => $gallery_set_allowratings,
	'gallery_set_batchadd_path' => $gallery_set_batchadd_path,
	'gallery_set_showcode_bbc_thumbnail' => $gallery_set_showcode_bbc_thumbnail,
	'gallery_set_showcode_bbc_medium' => $gallery_set_showcode_bbc_medium,
	'gallery_set_maxuploadperday' => $gallery_set_maxuploadperday,
	'gallery_set_relatedimagescount' => $gallery_set_relatedimagescount,
    'gallery_set_commentspostcount' => $gallery_set_commentspostcount,
    'gallery_set_disableremovetopic' => $gallery_set_disableremovetopic,
    'gallery_set_redirectcategorydefault' => $gallery_set_redirectcategorydefault,
    'gallery_set_onlyregcanviewimage' => $gallery_set_onlyregcanviewimage,
    'gallery_set_useseourls' => $gallery_set_useseourls,
    'gallery_set_searchenablefulltext' => $gallery_set_searchenablefulltext,
    'gallery_set_picturepostcount' => $gallery_set_picturepostcount,
    'gallery_set_likesystem' => $gallery_set_likesystem,

	));


	// Save the setting information
	updateSettings(
	array(
	'gallery_max_height' => $gallery_max_height,
	'gallery_max_width' => $gallery_max_width,
	'gallery_min_height' => $gallery_min_height,
	'gallery_min_width' => $gallery_min_width,

	'gallery_max_filesize' => $gallery_max_filesize,
	'gallery_path' => $gallery_path,
	'gallery_url' => $gallery_url,
	'gallery_jpeg_compression' => $gallery_jpeg_compression,
	'gallery_commentchoice' => $gallery_commentchoice,
	'gallery_who_viewing' => $gallery_who_viewing,
	'gallery_resize_image' => $gallery_resize_image,
	'gallery_set_count_child' => $gallery_set_count_child,
	'gallery_show_ratings' => $gallery_show_ratings,

	'gallery_set_images_per_page' => $gallery_set_images_per_page,
	'gallery_set_images_per_row' => $gallery_set_images_per_row,
	'gallery_set_commentsnewest' => $gallery_set_commentsnewest,
	'gallery_set_show_quickreply' => $gallery_set_show_quickreply,
	'gallery_set_enable_multifolder' => $gallery_set_enable_multifolder,
	'gallery_set_nohighslide' => $gallery_set_nohighslide,
	'gallery_setviewscountonce' => $gallery_setviewscountonce,
	'gallery_ratings_require_comment' => $gallery_ratings_require_comment,
	'gallery_allow_mature_tag' => $gallery_allow_mature_tag,

	'gallery_thumb_height' => $gallery_thumb_height,
	'gallery_thumb_width' => $gallery_thumb_width,

	'gallery_make_medium' => $gallery_make_medium,
	'gallery_medium_width' => $gallery_medium_width,
	'gallery_medium_height' => $gallery_medium_height,

	'gallery_set_disp_maxheight' => $gallery_set_disp_maxheight,
	'gallery_set_disp_maxwidth' => $gallery_set_disp_maxwidth,
	'gallery_set_cat_height' => $gallery_set_cat_height,
	'gallery_set_cat_width' => $gallery_set_cat_width,

	'gallery_user_no_password' => $gallery_user_no_password,
	'gallery_user_no_private' => $gallery_user_no_private,

	'gallery_set_water_enabled' => $gallery_set_water_enabled,
	'gallery_set_water_image' => $gallery_set_water_image,
	'gallery_set_water_percent' => $gallery_set_water_percent,
	'gallery_set_water_text' => $gallery_set_water_text,
	'gallery_set_water_textcolor' => $gallery_set_water_textcolor,
	'gallery_set_water_valign' => $gallery_set_water_valign,
	'gallery_set_water_halign' => $gallery_set_water_halign,
	'gallery_set_water_textfont' => $gallery_set_water_textfont,
	'gallery_set_water_textsize' => $gallery_set_water_textsize,

	'gallery_shop_commentadd' => $gallery_shop_commentadd,
	'gallery_shop_picadd' => $gallery_shop_picadd,

	'gallery_set_showcode_bbc_image' => $gallery_set_showcode_bbc_image,
	'gallery_set_showcode_directlink' => $gallery_set_showcode_directlink,
	'gallery_set_showcode_htmllink' => $gallery_set_showcode_htmllink,

	'gallery_disable_membercolorlink' => $gallery_disable_membercolorlink,
	'gallery_image_editor' => $gallery_image_editor,

	));

	redirectexit('action=admin;area=gallery;sa=adminset');

}

function AdminCats()
{
	global $context, $mbname, $txt, $smcFunc;

	isAllowedTo('smfgallery_manage');

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_managecats'];

	DoGalleryAdminTabs();

	$context['sub_template']  = 'manage_cats';

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_cat, title, id_parent, total, redirect
		FROM {db_prefix}gallery_cat
		 ORDER BY roworder ASC");

	$context['gallery_cat'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
			if ($row['redirect'] == 1)
			{
				$row['total'] = 0;
				$dbresult2 = $smcFunc['db_query']('', "
				SELECT COUNT(*) AS total
				FROM {db_prefix}gallery_pic
				WHERE USER_ID_CAT <> 0 AND approved = 1");
				$row2 = $smcFunc['db_fetch_assoc']($dbresult2);
				$row['total'] = $row2['total'];
				$smcFunc['db_free_result']($dbresult2);

			}

		$context['gallery_cat'][] = $row;
	}

	$smcFunc['db_free_result']($dbresult);
}

function CatUpDown()
{
	global $smcFunc, $txt;
	// Check if they are allowed to manage cats
	isAllowedTo('smfgallery_manage');

    if (isset($_REQUEST['cat']))
	   $cat = (int) $_REQUEST['cat'];
    else
        $cat = 0;

	ReOrderCats($cat);

	// Check if there is a category above it
	// First get our row order
	$dbresult1 = $smcFunc['db_query']('', "
	SELECT
		roworder,id_parent
	FROM {db_prefix}gallery_cat
	WHERE id_cat = $cat");
	$row = $smcFunc['db_fetch_assoc']($dbresult1);
	$id_parent = $row['id_parent'];
	$oldrow = $row['roworder'];
	$o = $row['roworder'];
	if ($_REQUEST['sa'] == 'catup')
		$o--;
	else
		$o++;

	$smcFunc['db_free_result']($dbresult1);
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		id_cat, roworder
	FROM {db_prefix}gallery_cat
	WHERE id_parent = $id_parent AND roworder = $o");

	if ($smcFunc['db_affected_rows']()== 0)
	{
		if ($_REQUEST['sa'] == 'catup')
			fatal_error($txt['gallery_error_nocat_above'], false);
		else
			fatal_error($txt['gallery_error_nocat_below'], false);
	}

	$row2 = $smcFunc['db_fetch_assoc']($dbresult);

	// Swap the order Id's
	$smcFunc['db_query']('', "
		UPDATE {db_prefix}gallery_cat
		SET roworder = $oldrow WHERE id_cat = " .$row2['id_cat']);

	$smcFunc['db_query']('', "
		UPDATE {db_prefix}gallery_cat
		SET roworder = $o WHERE id_cat = $cat");

	$smcFunc['db_free_result']($dbresult);

	// Redirect to index to view cats
	redirectexit('action=gallery');
}

function MyImages()
{
	global $context, $mbname, $txt, $smcFunc, $user_info, $scripturl, $modSettings;

	isAllowedTo('smfgallery_view');

	$u = (int) $_REQUEST['u'];
	if (empty($u))
		fatal_error($txt['gallery_error_no_user_selected']);

	StoreGalleryLocation();

	// Store the gallery userid
	$context['gallery_userid'] = $u;

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			m.real_name
		FROM {db_prefix}members AS m
		WHERE m.id_member = $u  LIMIT 1");

	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$context['gallery_usergallery_name'] = $row['real_name'];
	$smcFunc['db_free_result']($dbresult);

   	$context['linktree'][] = array(
			'name' => $context['gallery_usergallery_name'],
            'url' => $scripturl . '?action=gallery;sa=myimages;u=' . $u
		);
   	$context['linktree'][] = array(
			'name' => $txt['gallery_myimages']
		);

    // Load Stats
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) as total
		FROM {db_prefix}gallery_pic
		WHERE id_member = $u");
	$rowPic = $smcFunc['db_fetch_assoc']($dbresult);
    $context['user_total_pics'] = $rowPic['total'];


    $dbresult = $smcFunc['db_query']('', "
		SELECT
			SUM(views) as total
		FROM {db_prefix}gallery_pic
		WHERE id_member = $u");
	$rowPic = $smcFunc['db_fetch_assoc']($dbresult);
    $context['user_total_views'] = $rowPic['total'];


    $dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) as total
		FROM {db_prefix}gallery_comment
		WHERE id_member = $u");
    $rowPic = $smcFunc['db_fetch_assoc']($dbresult);
    $context['user_total_comments'] = $rowPic['total'];


    $dbresult = $smcFunc['db_query']('', "
		SELECT
			SUM(commenttotal) as total
		FROM {db_prefix}gallery_pic
		WHERE id_member = $u");
	$rowPic = $smcFunc['db_fetch_assoc']($dbresult);
    $context['user_total_photo_comments'] = $rowPic['total'];
    $dbresult = $smcFunc['db_query']('', "
		SELECT
			SUM(totalratings) as total
		FROM {db_prefix}gallery_pic
		WHERE id_member = $u");
	$rowPic = $smcFunc['db_fetch_assoc']($dbresult);
    $context['user_total_photo_votes'] = $rowPic['total'];


	if (!$context['user']['is_guest'])
		$groupsdata = implode(',',$user_info['groups']);
	else
		$groupsdata = -1;


	$userid = $context['gallery_userid'];
	// Get Total Pages
	$extra_page = '';
	if ($user_info['id'] == $context['gallery_userid'])
		$extra_page = '';
	else
		$extra_page = ' AND p.approved = 1';

	$dbresult = $smcFunc['db_query']('', "SELECT COUNT(*) AS total FROM ({db_prefix}gallery_pic as p)

LEFT JOIN {db_prefix}members AS m  ON (m.id_member = p.id_member)
					LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
					LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
					LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.ID_GROUP IN ($groupsdata) AND c.ID_CAT = p.ID_CAT)
					LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $context['gallery_userid'] . " AND v.user_id_cat = p.USER_ID_CAT)
					WHERE (((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 $extra_page) OR (p.USER_ID_CAT =0 $extra_page  AND  (c.view IS NULL OR c.view =1)))

	AND p.id_member = " . $context['gallery_userid'] . " " . $extra_page);
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$recordtotal = $row['total'];
	$total = ceil($row['total'] / $modSettings['gallery_set_images_per_page']);
	$smcFunc['db_free_result']($dbresult);

	$context['gallery_myimages_count'] = $total;

	$context['start'] = (int) $_REQUEST['start'];
	$context['page_index'] = constructPageIndex($scripturl . '?action=gallery;sa=myimages;u=' . $context['gallery_userid'] , $_REQUEST['start'], $recordtotal, $modSettings['gallery_set_images_per_page']);


	if ($user_info['id'] == $userid)
	$dbresult = $smcFunc['db_query']('', "SELECT p.id_picture, p.commenttotal, p.filesize, p.thumbfilename, p.approved, p.views, p.id_member, m.real_name, p.date,
	p.title, p.rating, p.totalratings, p.totallikes
	FROM ({db_prefix}gallery_pic as p)

LEFT JOIN {db_prefix}members AS m  ON (m.id_member = p.id_member)
					LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
					LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
					LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.ID_GROUP IN ($groupsdata) AND c.ID_CAT = p.ID_CAT)
					LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = $userid AND v.user_id_cat = p.USER_ID_CAT)
					WHERE (((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 $extra_page) OR (p.USER_ID_CAT =0 $extra_page  AND  (c.view IS NULL OR c.view =1)))

	AND  p.id_member = $userid GROUP BY p.id_picture ORDER BY p.id_picture DESC
		LIMIT $context[start]," . $modSettings['gallery_set_images_per_page']);
	else
	$dbresult = $smcFunc['db_query']('', "SELECT p.id_picture, p.commenttotal, p.filesize, p.thumbfilename, p.approved, p.views, p.id_member, m.real_name, p.date,
	p.title, p.rating, p.totalratings, p.totallikes
	FROM ({db_prefix}gallery_pic as p)

LEFT JOIN {db_prefix}members AS m  ON (m.id_member = p.id_member)
					LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
					LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
					LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.ID_GROUP IN ($groupsdata) AND c.ID_CAT = p.ID_CAT)
					LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = $userid AND v.user_id_cat = p.USER_ID_CAT)
					WHERE (((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 $extra_page) OR (p.USER_ID_CAT =0 $extra_page  AND  (c.view IS NULL OR c.view =1)))

	AND  p.id_member = $userid GROUP BY p.id_picture ORDER BY p.id_picture DESC
		LIMIT $context[start]," . $modSettings['gallery_set_images_per_page']);


    $context['gallery_my_images_result'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_my_images_result'][] = $row;
	}


	$smcFunc['db_free_result']($dbresult);


	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $context['gallery_usergallery_name'];

	$context['sub_template']  = 'myimages';
}

function ApproveList()
{
	global $scripturl, $smcFunc, $context, $mbname, $txt;

	isAllowedTo('smfgallery_manage');


	// Approval Picture List
		// Get Total Pages
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) AS total
		FROM {db_prefix}gallery_pic as p
		WHERE p.approved = 0 ORDER BY id_picture DESC");
		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$numofrows = $row['total'];

		$smcFunc['db_free_result']($dbresult);
		$context['start'] = (int) $_REQUEST['start'];

	// List all the unapproved pictures
	$dbresult = $smcFunc['db_query']('', "
	SELECT p.id_picture, p.id_cat, p.user_id_cat, p.thumbfilename, p.title, p.id_member,
	  m.real_name, p.date, p.description, c.title catname, u.title catname2
	FROM {db_prefix}gallery_pic AS p
	LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
	LEFT JOIN {db_prefix}gallery_usercat AS u ON (u.user_id_cat = p.user_id_cat)
	LEFT JOIN {db_prefix}gallery_cat AS c ON (c.id_cat = p.id_cat)
	WHERE p.approved = 0
	ORDER BY p.id_picture DESC LIMIT $context[start],10");
	$context['gallery_pic_approvallist'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_pic_approvallist'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	$context['page_index'] = constructPageIndex($scripturl . '?action=admin;area=gallery;sa=approvelist', $_REQUEST['start'], $numofrows, 10);



	// List all reported pictures
	$dbresult = $smcFunc['db_query']('', "
			SELECT
				r.id, r.id_picture,  r.id_member, m.real_name, r.date, r.comment
			FROM {db_prefix}gallery_report as r
			LEFT JOIN {db_prefix}members AS m ON  (m.id_member = r.id_member)
			 ORDER BY r.id_picture DESC");
	$context['gallery_report_piclist'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_report_piclist'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);



	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_approveimages'];

	DoGalleryAdminTabs();

	$context['sub_template']  = 'approvelist';
}

function ApprovePicture()
{
	global $txt;
	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	// Approve the picture
	ApprovePictureByID($id);

	// Redirect to approval list
	redirectexit('action=admin;area=gallery;sa=approvelist');
}

function ApprovePictureByID($id)
{
	global $scripturl, $smcFunc, $txt, $modSettings, $sourcedir, $user_info, $gallerySettings;

	// Look up the picture and get the category
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.id_member, p.thumbfilename, p.width,
			p.height, p.filename, p.title, p.description, c.id_board,c.postingsize,
			p.id_cat, p.user_id_cat, c.showpostlink, c.locktopic, c.id_topic, c.tweet_items
		FROM {db_prefix}gallery_pic AS p
		LEFT JOIN {db_prefix}gallery_cat AS c ON (c.id_cat = p.id_cat)
		WHERE p.id_picture = $id LIMIT 1");

	$rowcat = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);

	if ($rowcat['id_board'] != 0  && $rowcat['id_board'] != '' )
	{
		$extraheightwidth = '';
		if ($rowcat['postingsize'] == 1)
		{
			$extraheightwidth = " height=" . $rowcat['height'] . " width=" . $rowcat['width'];

			$postimg = $rowcat['filename'];
		}
		else
			$postimg = $rowcat['thumbfilename'];


		if ($rowcat['showpostlink'] == 1)
			$showpostlink = "\n\n" . $scripturl . '?action=gallery;sa=view;id=' . $id;
		else
			$showpostlink = '';

		// Create the post
		require_once($sourcedir . '/Subs-Post.php');
		$msgOptions = array(
			'id' => 0,
			'subject' => $rowcat['title'],
			'body' => '[b]' . $rowcat['title'] . "[/b]\n\n[img$extraheightwidth]" . $modSettings['gallery_url']  . $postimg . "[/img]$showpostlink\n\n" . $rowcat['description'],
			'icon' => 'xx',
			'smileys_enabled' => 1,
			'attachments' => array(),
		);
		$topicOptions = array(
			'id' => $rowcat['id_topic'],
			'board' => $rowcat['id_board'],
			'poll' => null,
			'lock_mode' => $rowcat['locktopic'],
			'sticky_mode' => null,
			'mark_as_read' => true,
		);
		$posterOptions = array(
			'id' => $rowcat['id_member'],
			'update_post_count' => !$user_info['is_guest'],
		);

		// Fix height & width of posted image in message
		preparsecode($msgOptions['body']);

		createPost($msgOptions, $topicOptions, $posterOptions);

			require_once($sourcedir . '/Post.php');

            if (function_exists("notifyMembersBoard"))
            {
                $notifyData = array(
                    'body' => $msgOptions['body'],
                    'subject' => $msgOptions['subject'],
                    'poster' => $rowcat['id_member'],
                    'msg' => $msgOptions['id'],
                    'board' => $rowcat['id_board'],
                    'topic' => $topicOptions['id'],
                );
                notifyMembersBoard($notifyData);

            }
            else
            {
                     // for 2.1
                    $smcFunc['db_insert']('',
                        '{db_prefix}background_tasks',
                        array('task_file' => 'string', 'task_class' => 'string', 'task_data' => 'string', 'claimed_time' => 'int'),
                        array('$sourcedir/tasks/CreatePost-Notify.php', 'CreatePost_Notify_Background', $smcFunc['json_encode'](array(
                            'msgOptions' => $msgOptions,
                            'topicOptions' => $topicOptions,
                            'posterOptions' => $posterOptions,
                            'type' =>  $topicOptions['id'] ? 'reply' : 'topic',
                        )), 0),
                        array('id_task')
                    );


            }




		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
					SET id_topic = " .$topicOptions['id'] . ", id_msg = " . $msgOptions['id'] . " WHERE id_picture = $id
					");

	}

	// Update the approval
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic SET approved = 1 WHERE id_picture = $id LIMIT 1");

	UpdateMemberPictureTotals($rowcat['id_member']);

	if ($rowcat['user_id_cat'] == 0)
		Gallery_UpdateLatestCategory($rowcat['id_cat']);
	else
		Gallery_UpdateUserLatestCategory($rowcat['user_id_cat']);

	if ($rowcat['user_id_cat'] == 0)
		UpdateCategoryTotals($rowcat['id_cat']);
	else
		UpdateUserCategoryTotals($rowcat['user_id_cat']);

	SendMemberWatchNotifications($rowcat['id_member'], $scripturl . '?action=gallery;sa=view;id=' .  $id);


	if ($rowcat['user_id_cat'] == 0)
 	{
  		if ($rowcat['tweet_items'] == 1)
    		Gallery_TweetItem($rowcat['title'],$id);
	}


      // Add Post Count
     if (!empty($gallerySettings['gallery_set_picturepostcount']))
     {
        if ($rowcat['id_member'] != 0)
        {
            updateMemberData($rowcat['id_member'], array('posts' => '+'));
        }
     }


    Gallery_LogAction('approveditem',$id);

}

function UnApprovePicture()
{
	global $txt;

	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	UnApprovePictureByID($id);

	// Redirect to approval list
	redirectexit('action=admin;area=gallery;sa=approvelist');
}

function UnApprovePictureByID($id)
{
	global $smcFunc;

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.id_member
		FROM {db_prefix}gallery_pic AS p
		WHERE p.id_picture = $id LIMIT 1");
	$rowPic = $smcFunc['db_fetch_assoc']($dbresult);

	// Update the approval
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic SET approved = 0 WHERE id_picture = $id LIMIT 1");

	UpdateMemberPictureTotals($rowPic['id_member']);

	UpdateCategoryTotalByPictureID($id);

    Gallery_LogAction('unapproveditem',$id);
}

function DeleteReport()
{
	global $smcFunc, $txt;

	// Check the permission
	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_report_selected']);

	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_report WHERE id = $id LIMIT 1");

	// Redirect to redirect list
	redirectexit('action=admin;area=gallery;sa=approvelist');
}

function DeleteCommentReport()
{
	global $smcFunc, $txt;

	// Check the permission
	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_report_selected']);

	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_creport WHERE id = $id LIMIT 1");

	// Redirect to redirect list
	redirectexit('action=admin;area=gallery;sa=commentlist');
}

function Search()
{
	global $context, $mbname, $txt, $smcFunc, $user_info;

	// Is the user allowed to view the gallery?
	isAllowedTo('smfgallery_view');



	if ($context['user']['is_guest'])
		$groupid = -1;
	else
		$groupid =  $user_info['groups'][0];

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.title, p.view, c.id_parent
		FROM {db_prefix}gallery_cat as c
		LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.id_group = $groupid AND c.id_cat = p.id_cat)
        WHERE c.redirect = 0
		ORDER BY c.title ASC");

	$context['gallery_cat'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		// Check if they have permission to search these categories
		if ($row['view'] == '0')
			continue;

		$context['gallery_cat'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	CreateGalleryPrettyCategory();



		// Get all the custom fields
		$result = $smcFunc['db_query']('', "
		SELECT
			title, defaultvalue, is_required, ID_CUSTOM, id_cat
		FROM {db_prefix}gallery_custom_field
		WHERE id_cat = 0 ORDER BY roworder desc");
        $context['gallery_cat_cusfields'] = array();
		while ($row2 = $smcFunc['db_fetch_assoc']($result))
		{
			$context['gallery_cat_cusfields'][] = $row2;
		}
		$smcFunc['db_free_result']($result);




	$context['sub_template']  = 'search';

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_search'];


   	$context['linktree'][] = array(
			'name' => $txt['gallery_search']
		);

}

function Search2()
{
	global $context, $mbname, $txt, $smcFunc, $modSettings, $scripturl, $gallerySettings;

	// Is the user allowed to view the gallery?
	isAllowedTo('smfgallery_view');


	$context['gallery_searchcustom'] = false;
	$context['gallery_cat_cusfields'] = 0;

	$fulltextorderby = '';
	$fulltextscore = '';


	if (isset($_REQUEST['q']))
	{
		$data = json_decode(base64_decode($_REQUEST['q']),true);
		@$_REQUEST['cat'] = $data['cat'];
		@$_REQUEST['key'] = $data['keyword'];
		@$_REQUEST['searchkeywords'] = $data['searchkeywords'];
		@$_REQUEST['searchtitle'] = $data['searchtitle'];
		@$_REQUEST['searchdescription'] = $data['searchdescription'];
		@$_REQUEST['searchcustom'] = $data['searchcustom'];
		@$_REQUEST['searchexif'] = $data['searchexif'];
        @$_REQUEST['searchexifmake'] = $data['searchexifmake'];
        @$_REQUEST['searchexifmodel'] = $data['searchexifmodel'];
		@$_REQUEST['daterange'] = $data['daterange'];
		@$_REQUEST['pic_postername'] = $data['pic_postername'];
		@$_REQUEST['searchfor'] = $data['searchfor'];
		@$_REQUEST['mediatype'] = $data['mediatype'];
		@$_REQUEST['gallerytype'] = $data['gallerytype'];
		@$_REQUEST['customfields'] = $data['customfields'];

		if (!isset($_REQUEST['orderby']))
		{
			@$_REQUEST['orderby']  = $data['orderby'];
		}

		if (!isset($_REQUEST['sortby']))
		{
			@$_REQUEST['sortby']  = $data['sortby'];
		}
	}


        if (isset($_REQUEST['cat']))
    	   $cat = (int) $_REQUEST['cat'];
        else
            $cat = 0;

		// Check if keyword search was selected
		@$keyword =  $smcFunc['htmlspecialchars']($smcFunc['htmltrim']($_REQUEST['key']), ENT_QUOTES);

		$searchArray = array();
		$searchArray['keyword'] = $keyword;
		$context['gallery_search_query_encoded'] = base64_encode(json_encode($searchArray));


		if ($keyword == '')
		{
			// Probably a normal Search
		//	if (empty($_REQUEST['searchfor']))
			//	fatal_error($txt['gallery_error_no_search'],false);

			if (isset($_REQUEST['searchfor']))
				$searchfor =  $smcFunc['htmlspecialchars']($_REQUEST['searchfor'],ENT_QUOTES);
			else
				$searchfor = '';


            $searchAll = false;
            if (empty($searchfor))
                $searchAll = true;


			if ($smcFunc['strlen']($searchfor) < 3 && $searchAll == false)
				fatal_error($txt['gallery_error_search_small'],false);

			// Check the search options
			@$searchkeywords = isset($_REQUEST['searchkeywords']) ? 1 : 0;
			@$searchtitle = isset($_REQUEST['searchtitle']) ? 1 : 0;
			@$searchdescription = isset($_REQUEST['searchdescription']) ? 1 : 0;
			$searchcustom = isset($_REQUEST['searchcustom']) ? 1 : 0;
			$searchexif = isset($_REQUEST['searchexif']) ? 1 : 0;
            $searchexifmake = isset($_REQUEST['searchexifmake']) ? 1 : 0;
            $searchexifmodel = isset($_REQUEST['searchexifmodel']) ? 1 : 0;
			$daterange = (int) @$_REQUEST['daterange'];
			$memid = 0;
			$mediatype = $_REQUEST['mediatype'];
			$gallerytype = $_REQUEST['gallerytype'];

			$sortby = $_REQUEST['sortby'];
			$orderby = $_REQUEST['orderby'];

			if (isset($_REQUEST['customfields']))
				$customfields = $_REQUEST['customfields'];
			else
				$customfields = '';

			$context['search_cusfields_data'] = array();

			if (!empty($customfields))
			{


				foreach($customfields as $cusfield)
				{
					if (!empty($cusfield))
						$context['search_cusfields_data'][] = $cusfield;
				}

				if (count($context['search_cusfields_data']) > 0)
					$searchcustom = 1;

			}


			// Check if searching by member id
			if (!empty($_REQUEST['pic_postername']))
			{
				$pic_postername = str_replace('"','', $_REQUEST['pic_postername']);
				$pic_postername = str_replace("'",'', $pic_postername);
				$pic_postername = str_replace('\\','', $pic_postername);
				$pic_postername = $smcFunc['htmlspecialchars']($pic_postername, ENT_QUOTES);
				$searchArray['pic_postername'] = $pic_postername;

				$dbresult = $smcFunc['db_query']('', "
					SELECT
						real_name, id_member
					FROM {db_prefix}members
					WHERE real_name = '$pic_postername' OR member_name = '$pic_postername'  LIMIT 1");
					$row = $smcFunc['db_fetch_assoc']($dbresult);
					$smcFunc['db_free_result']($dbresult);

				if ($smcFunc['db_affected_rows']() != 0)
				{
					$memid = $row['id_member'];
				}
			}


			$searchArray['searchfor'] = $searchfor;
			$searchArray['searchkeywords'] = $searchkeywords;
			$searchArray['cat'] = $cat;
			$searchArray['searchtitle'] = $searchtitle;
			$searchArray['searchdescription'] = $searchdescription;

            $searchArray['searchexifmake'] = $searchexifmake;
            $searchArray['searchexifmodel'] = $searchexifmodel;

			$searchArray['searchcustom'] = $searchcustom;
			$searchArray['daterange'] = $daterange;
			$searchArray['memid'] = $memid;


			$searchArray['mediatype'] = $mediatype;
			$searchArray['gallerytype'] = $gallerytype;


			$searchArray['sortby'] = $sortby;
			$searchArray['orderby'] = $orderby;

			$searchArray['customfields'] = $context['search_cusfields_data'];


			$context['gallery_search_query_encoded'] = base64_encode(json_encode($searchArray));


			$context['catwhere'] = '';

			if ($cat != 0)
			{
				$context['catwhere'] = "p.id_cat = $cat AND ";
				$context['gallery_searchcustom_cat'] = $cat;
			}
			// Check if searching by member id
			if ($memid != 0)
				$context['catwhere'] .= "p.id_member = $memid AND ";


			if ($mediatype == 'pics')
			{
				$context['catwhere'] .= "p.type = 0 AND ";
			}

			if ($mediatype == 'videos')
			{
				$context['catwhere'] .= "p.type != 0 AND ";
			}

			if ($gallerytype == 'main')
			{
				$context['catwhere'] .= "p.ID_CAT != 0 AND ";
			}

			if ($gallerytype == 'user')
			{
				$context['catwhere'] .= "p.user_id_cat != 0 AND ";
			}

			// Date Range check
			if ($daterange!= 0)
			{
				$currenttime = time();
				$pasttime = $currenttime - ($daterange * 24 * 60 * 60);

				$context['catwhere'] .=  "(p.date BETWEEN '" . $pasttime . "' AND '" . $currenttime . "')  AND";
			}


			$searchquery = '';

			if ($searchtitle  && $searchAll == false)
				$searchquery = "p.title LIKE '%$searchfor%' ";

			if ($searchdescription  && $searchAll == false)
			{
				if (!empty($searchquery))
					$searchquery .= ' OR ';

				$searchquery .= "p.description LIKE '%$searchfor%'";

			}

            if ($searchexifmake && $searchAll == false)
			{
				if (!empty($searchquery))
					$searchquery .= ' OR ';

				$searchquery .= "e.idfo_make LIKE '%$searchfor%'";

			}

             if ($searchexifmodel && $searchAll == false)
			{
				if (!empty($searchquery))
					$searchquery .= ' OR ';

				$searchquery .= "e.idfo_model LIKE '%$searchfor%'";

			}

			if ($searchkeywords && $searchAll == false)
			{
				if (!empty($searchquery))
					$searchquery .= ' OR ';

				$searchquery .= "p.keywords LIKE '%$searchfor%'";
				$context['gallery_searchkeywords'] = true;
				$context['gallery_keywords'] = $searchfor;
			}

			// Search Custom Fields

			if ($searchcustom)
			{
				$context['gallery_searchcustom'] = true;
				if (!empty($searchquery))
					$searchquery .= ' OR ';

				$searchquery .= "d.value LIKE '%$searchfor%'";
			}


			if ($searchquery == '' && $searchAll == false)
				$searchquery = "p.title LIKE '%$searchfor%' ";

            if ($searchAll == true)
                $searchquery = " 1 = 1 ";

			 IF (($searchdescription == true || $searchtitle == true || $searchkeywords == true) && $searchcustom == false)
			 {
	            if ($searchAll == false && $gallerySettings['gallery_set_searchenablefulltext'] == 1)
	            {
	            	$fulltextorderby = "  ORDER BY score DESC ";
	            	$fulltextscore = ", MATCH(p.title, p.description,p.keywords) AGAINST('$searchfor') AS score ";
	            	$searchquery = " MATCH(p.title, p.description,p.keywords) AGAINST('$searchfor') ";

	            }
    		}

			$context['gallery_search_query'] = $searchquery;
			$context['gallery_search'] = $searchfor;
		}
		else
		{
			// Search for the keyword

			// Debating if I should add string length check for keywords...
			// if(strlen($keyword) <= 3)
			// fatal_error($txt['gallery_error_search_small']);

			$context['gallery_search'] = $keyword;

			$context['gallery_search_query'] = "p.keywords LIKE '%$keyword%'";
			$context['gallery_searchkeywords'] = true;
			$context['gallery_keywords'] = $keyword;
		}


	    if (isset($_REQUEST['perpage']))
		{
			$galleryPerPage = (int) $_REQUEST['perpage'];

			if ($galleryPerPage < $modSettings['orignal_set_images_per_page'])
				$galleryPerPage = $modSettings['orignal_set_images_per_page'];

			if ($galleryPerPage > $modSettings['orignal_set_images_per_page'] * 3)
				$galleryPerPage = $modSettings['orignal_set_images_per_page'] * 3;

			$_SESSION['galleryperpage'] = $galleryPerPage;
			$modSettings['gallery_set_images_per_page'] = $galleryPerPage;
		}


	// Check if we are sorting stuff heh
	$sortby = '';
	$orderby = '';
	if (isset($_REQUEST['sortby']))
	{
		switch ($_REQUEST['sortby'])
		{
			case 'date': $sortby = 'p.id_picture';
			break;

			case 'title': $sortby = 'p.title';
			break;

			case 'mostview': $sortby = 'p.views';
			break;

			case 'mostcom': $sortby = 'p.commenttotal';
			break;

			case 'mostliked': $sortby = 'p.totallikes';
			break;


			case 'mostrated': $sortby = 'ratingaverage DESC,p.totalratings ';
			break;

			default: $sortby = 'p.id_picture';
			break;
		}

		$sortby2 = $_REQUEST['sortby'];
	}
	else
	{
		if (!empty($context['gallery_sortby']))
		{
			$sortby = $context['gallery_sortby'];

			switch ($sortby)
			{
				case 'p.id_picture': $sortby2 = 'date';
				break;

				case 'p.title': $sortby2 = 'title';
				break;

				case 'p.views': $sortby2 = 'mostview';
				break;

				case 'p.commenttotal': $sortby2 = 'mostcom';
				$sortby = 'ratingaverage DESC,p.totalratings ';
				break;

				case 'p.totallikes': $sortby2 = 'mostliked';
				break;


				case 'p.totalratings': $sortby2 = 'mostrated';
				break;

				default: $sortby2 = 'date';
				break;
			}
		}
		else
		{
			$sortby = 'p.id_picture';
			$sortby2 = 'date';
		}
	}

	if (isset($_REQUEST['orderby']))
	{
		switch ($_REQUEST['orderby'])
		{
			case 'asc':
				$orderby = 'ASC';

			break;
			case 'desc':
				$orderby = 'DESC';
			break;

			default:
				$orderby = 'DESC';
			break;
		}

		$orderby2 = $_REQUEST['orderby'];
	}
	else
	{
		if (!empty($context['gallery_orderby']))
		{
			$orderby = $context['gallery_orderby'];
			$orderby2 = strtolower($context['gallery_orderby']);
		}
		else
		{
			$orderby = 'DESC';

			$orderby2 = 'desc';
		}
	}

		if (empty($sortby))
			$sortby = 'p.id_picture';

		if (empty($orderby))
			$orderby = 'DESC';

	if (empty($fulltextorderby))
		$fulltextorderby = "  ORDER BY  $sortby $orderby";
	else
	{
		$fulltextorderby = "  ORDER BY $sortby $orderby, score DESC";
	}

	$gallery_where = '';
	if (isset($context['catwhere']))
		$gallery_where = $context['catwhere'];

	$customFieldJoin = '';
	$customFieldWhere = '';
	if ($context['gallery_searchcustom'] == true)
	{
		$customIDs = array();
		$customIDs[] = 0;

		if (!empty($context['search_cusfields_data']))
		{
			foreach($context['search_cusfields_data'] as $cusid)
				$customIDs[] = $cusid;
		}

		$customFieldJoin = " LEFT JOIN {db_prefix}gallery_custom_field_data as d  ON (d.id_picture = p.id_picture AND d.ID_CUSTOM IN (" . implode("," , $customIDs) . ")) ";
	}

	global $user_info;
	if (!$context['user']['is_guest'])
		$groupsdata = implode(',',$user_info['groups']);
	else
		$groupsdata = -1;


	$dbresult = $smcFunc['db_query']('', "
	SELECT
	p.id_picture
	FROM ({db_prefix}gallery_pic as p)
	LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)    
	LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)    
	LEFT JOIN {db_prefix}gallery_exif_data AS e ON (e.id_picture = p.id_picture)
	LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.id_group IN ($groupsdata) AND c.id_cat = p.id_cat)    
	$customFieldJoin
	WHERE  (((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 AND p.approved =1) OR (p.approved =1 AND p.USER_ID_CAT =0 AND (c.view IS NULL OR c.view =1)))    AND   " . $gallery_where . " p.approved = 1 $customFieldWhere AND (" . $context['gallery_search_query'] . ")
	 GROUP BY p.id_picture
	");
	$numrows = $smcFunc['db_num_rows']($dbresult);
	$smcFunc['db_free_result']($dbresult);


	$context['start'] = (int) $_REQUEST['start'];



	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.id_cat, p.commenttotal, p.keywords, p.filesize, p.thumbfilename, p.approved, p.views,
			p.id_member, m.real_name, p.date, p.mature, v.id_picture as unread, mg.online_color,
			p.title, p.rating, p.totalratings, p.totallikes, (p.rating / p.totalratings ) AS ratingaverage $fulltextscore
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
		LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)    
		LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
				LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $context['user']['id'] . " AND v.user_id_cat = p.USER_ID_CAT)
		LEFT JOIN {db_prefix}gallery_exif_data AS e ON (e.id_picture = p.id_picture)
		LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.id_group IN ($groupsdata) AND c.id_cat = p.id_cat)
		$customFieldJoin
		WHERE (((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 AND p.approved =1) OR (p.approved =1 AND p.USER_ID_CAT =0 AND (c.view IS NULL OR c.view =1)))  AND  " . $gallery_where . " p.approved = 1 $customFieldWhere AND (" . $context['gallery_search_query'] . ")
		GROUP BY p.id_picture $fulltextorderby
		LIMIT $context[start]," . $modSettings['gallery_set_images_per_page']);


	$context['gallery_search_results'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_search_results'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);


	$q =  $context['gallery_search_query_encoded'];
	$context['page_index'] = constructPageIndex($scripturl . '?action=gallery;sa=search2;q=' .$q, $_REQUEST['start'], $numrows, $modSettings['gallery_set_images_per_page']);







	$context['sub_template']  = 'search_results';

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_searchresults'];

   	$context['linktree'][] = array(
			'name' => $txt['gallery_searchresults']
		);
}

function RatePicture()
{
	global $smcFunc, $txt, $user_info, $modSettings;

	is_not_guest();

	//Check if they are allowed to rate picture
	isAllowedTo('smfgallery_ratepic');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);
	if (isset($_REQUEST['rating']))
		$rating = (int) $_REQUEST['rating'];
	else
		$rating = 0;

	if (empty($rating))
		fatal_error($txt['gallery_error_no_rating_selected'],false);

	// Check if they need to have a comment first
	if ($modSettings['gallery_ratings_require_comment'] == 1)
	{
		$dbresult = $smcFunc['db_query']('', "
			SELECT
			id_member, id_picture
			FROM {db_prefix}gallery_comment
			WHERE id_member = " . $user_info['id'] . " AND id_picture = $id");
		$foundComment = $smcFunc['db_affected_rows']();
		$smcFunc['db_free_result']($dbresult);

		if ($foundComment == 0)
			fatal_error($txt['gallery_err_comment_rating'],false);
	}

	// Check if they rated this picture?
	$dbresult = $smcFunc['db_query']('', "
		SELECT
		id_member, id_picture
		FROM {db_prefix}gallery_rating
		WHERE id_member = " . $user_info['id'] . " AND id_picture = $id");

	$found = $smcFunc['db_affected_rows']();
	$smcFunc['db_free_result']($dbresult);

	//Get the Picture owner
	$dbresult = $smcFunc['db_query']('', "
		SELECT
		id_member
		FROM {db_prefix}gallery_pic
		WHERE id_picture = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);
	//Check if they are rating their own image.
	if ($user_info['id'] == $row['id_member'])
		fatal_error($txt['gallery_error_norate_own'],false);

	if ($found != 0)
		fatal_error($txt['gallery_error_already_rated'],false);

	switch($rating)
	{
		case 1: break;
		case 2: break;
		case 3: break;
		case 4: break;
		case 5; break;
		default:
			// If they try and be tricky enter an average rating
			$rating = 3;
		break;
	}

	// Add the Rating
	$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_rating (id_member, id_picture, value) VALUES (" . $user_info['id'] . ", $id,$rating)");

	// Add rating information to the picture
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic SET totalratings = totalratings + 1, rating = rating + $rating WHERE id_picture = $id LIMIT 1");

	// Redirect to picture
	redirectexit('action=gallery;sa=view;id=' . $id);
}

function ViewRating()
{
	global $context, $mbname, $txt, $smcFunc;

	isAllowedTo('smfgallery_manage');

 	// Get the picture ID for the ratings
	@$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

    $context['gallery_pic_id'] = $id;

 	$dbresult = $smcFunc['db_query']('', "
	SELECT
		r.id, r.value, r.id_picture,  r.id_member, m.real_name
	FROM {db_prefix}gallery_rating as r, {db_prefix}members AS m
	WHERE r.id_picture = $id AND r.id_member = m.id_member");

    $context['gallery_pic_ratings'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
	   $context['gallery_pic_ratings'][] = $row;
	}

    $smcFunc['db_free_result']($dbresult);


	$context['sub_template']  = 'view_rating';

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_viewratings'];

    $context['linktree'][] = array(
			'name' => $txt['gallery_form_viewratings']
		);

}

function DeleteRating()
{
	global $smcFunc, $txt;

	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_rating_selected']);

	// First lookup the ID to get the picture id and value of rating
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id, id_picture, value
		FROM {db_prefix}gallery_rating
		WHERE id = $id");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$value = $row['value'];
	$picid = $row['id_picture'];
	$smcFunc['db_free_result']($dbresult);
	// Delete the Rating
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_rating WHERE id = " . $id . ' LIMIT 1');
	// Update the picture rating information
	$dbresult = $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic SET totalratings = totalratings - 1, rating = rating - $value WHERE id_picture = $picid LIMIT 1");

    Gallery_LogAction('deleterating',$picid);
	// Redirect to the ratings
	redirectexit('action=gallery;sa=viewrating;id=' .  $picid);
}

function Stats()
{
	global $context, $mbname, $txt, $smcFunc, $context, $scripturl, $user_info;

	// Is the user allowed to view the gallery?
	isAllowedTo('smfgallery_view');


   	// Get the main groupid
	if ($context['user']['is_guest'])
		$groupid = -1;
	else
		$groupid =  $user_info['groups'][0];


	// Get views total and comments total and total filesize
	$result = $smcFunc['db_query']('', "
	SELECT
		SUM(views) AS views, SUM(filesize) AS filesize, SUM(commenttotal) AS commenttotal, SUM(totalratings) as ratingtotal, COUNT(*) AS pictotal
	FROM {db_prefix}gallery_pic");
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	$result2 = $smcFunc['db_query']('', "SELECT COUNT(*) AS pictotal FROM {db_prefix}gallery_pic");
	$row2 = $smcFunc['db_fetch_assoc']($result2);
	$smcFunc['db_free_result']($result2);

	$context['total_pictures'] = $row2['pictotal'];
	$context['total_views'] = $row['views'];
	$context['total_filesize'] =  gallery_format_size($row['filesize'], 2);
	$context['total_comments'] = $row['commenttotal'];

    // Added
    $context['total_votes'] = $row['ratingtotal'];

	$result3 = $smcFunc['db_query']('', "SELECT COUNT(*) AS cattotal FROM {db_prefix}gallery_cat WHERE redirect = 0");
	$row3 = $smcFunc['db_fetch_assoc']($result3);
	$smcFunc['db_free_result']($result3);
    $context['total_categories'] = $row3['cattotal'];

    // Get date of first picture
   	$result4 = $smcFunc['db_query']('', "SELECT date FROM {db_prefix}gallery_pic ORDER BY ID_PICTURE ASC LIMIT 1");
	$row4 = $smcFunc['db_fetch_assoc']($result4);
	$smcFunc['db_free_result']($result4);

    if (empty($row4['date']))
    {
        $context['total_avgcommentsperday'] = 0;
        $context['total_avgpicvideosday'] = 0;
    }
    else
    {
        $numberOfDays = floor(time() - $row4['date']) / (60 * 60 * 24);


        if ($numberOfDays > 0)
        {
            $context['total_avgcommentsperday'] = round($context['total_comments']  / $numberOfDays,2);
            $context['total_avgpicvideosday'] = round($context['total_pictures'] / $numberOfDays,2);
        }
        else
        {
            $context['total_avgcommentsperday'] = 0;
            $context['total_avgpicvideosday'] = 0;
        }


    }


	// Top Viewed Pictures
	$result = $smcFunc['db_query']('', "
		SELECT
			id_picture, title,views
		FROM {db_prefix}gallery_pic
		WHERE approved = 1 AND views > 0 ORDER BY views DESC LIMIT 10");
	$context['top_viewed'] = array();
	$max_views = 1;
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['top_viewed'][] = array(
			'id_picture' => $row['id_picture'],
			'title' => $row['title'],
			'views' => $row['views'],
			'link' => '<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '">' . $row['title'] . '</a>',
		);

		if ($max_views < $row['views'])
			$max_views = $row['views'];
	}
	$smcFunc['db_free_result']($result);

	foreach ($context['top_viewed'] as $i => $picture)
		$context['top_viewed'][$i]['percent'] = round(($picture['views'] * 100) / $max_views);

	// Top Rated
	$result = $smcFunc['db_query']('', "
		SELECT
			id_picture, title,rating, (rating / totalratings ) AS ratingaverage
		FROM {db_prefix}gallery_pic
		WHERE approved = 1 AND totalratings > 0 ORDER BY ratingaverage DESC, totalratings DESC LIMIT 10");
	$context['top_rating'] = array();
	$max_rating = 1;
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['top_rating'][] = array(
			'id_picture' => $row['id_picture'],
			'title' => $row['title'],
			'rating' => $row['rating'],
			'link' => '<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '">' . $row['title'] . '</a>',
		);

		if ($max_rating < $row['rating'])
			$max_rating = $row['rating'];
	}
	$smcFunc['db_free_result']($result);

	foreach ($context['top_rating'] as $i => $picture)
		$context['top_rating'][$i]['percent'] = round(($picture['rating'] * 100) / $max_rating);

	// Most Commented
	$result = $smcFunc['db_query']('', "
	SELECT id_picture, title,commenttotal
	FROM {db_prefix}gallery_pic WHERE approved = 1 AND commenttotal > 0 ORDER BY commenttotal DESC LIMIT 10");
	$context['most_comments'] = array();
	$max_commenttotal = 1;
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['most_comments'][] = array(
			'id_picture' => $row['id_picture'],
			'title' => $row['title'],
			'commenttotal' => $row['commenttotal'],
			'link' => '<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '">' . $row['title'] . '</a>',
		);

		if ($max_commenttotal < $row['commenttotal'])
			$max_commenttotal = $row['commenttotal'];
	}
	$smcFunc['db_free_result']($result);

	foreach ($context['most_comments'] as $i => $picture)
		$context['most_comments'][$i]['percent'] = round(($picture['commenttotal'] * 100) / $max_commenttotal);

	// Last 10 Pictures uploaded
	$result = $smcFunc['db_query']('', "SELECT id_picture, title FROM {db_prefix}gallery_pic WHERE approved = 1 ORDER BY id_picture DESC LIMIT 10");
	$context['last_upload'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['last_upload'][] = array(
			'id_picture' => $row['id_picture'],
			'title' => $row['title'],
			'link' => '<a href="' . $scripturl . '?action=gallery;sa=view;id=' . $row['id_picture'] . '">' . $row['title'] . '</a>',
		);
	}
	$smcFunc['db_free_result']($result);


	// Top Posters
	$result = $smcFunc['db_query']('', "SELECT count(*) as total, p.id_member, m.real_name
	FROM {db_prefix}gallery_pic as p
	LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
	WHERE p.approved = 1 AND m.real_name <> '' GROUP BY p.id_member ORDER BY total DESC  LIMIT 10");
	$context['top_posters'] = array();
	$max_posters = 1;
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['top_posters'][] = array(
			'total' => $row['total'],
			'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>',
		);

		if ($max_posters < $row['total'])
			$max_posters = $row['total'];
	}
	$smcFunc['db_free_result']($result);
	foreach ($context['top_posters'] as $i => $picture)
		$context['top_posters'][$i]['percent'] = round(($picture['total'] * 100) / $max_posters);

	// Top Commenters
	$result = $smcFunc['db_query']('', "SELECT count(*) as total, c.id_member, m.real_name
	FROM {db_prefix}gallery_comment as c
	LEFT JOIN {db_prefix}members AS m ON (c.id_member = m.id_member)
	WHERE c.approved = 1 AND m.real_name <> '' GROUP BY c.id_member ORDER BY total DESC   LIMIT 10");
	$context['top_commenters'] = array();
	$max_commenters = 1;
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['top_commenters'][] = array(
			'total' => $row['total'],
			'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>',
		);

		if ($max_commenters  < $row['total'])
			$max_commenters  = $row['total'];
	}
	$smcFunc['db_free_result']($result);
	foreach ($context['top_commenters'] as $i => $picture)
		$context['top_commenters'][$i]['percent'] = round(($picture['total'] * 100) / $max_commenters );




	// Top Raters
	$result = $smcFunc['db_query']('', "SELECT count(*) as total, c.id_member, m.real_name
	FROM {db_prefix}gallery_rating as c
	LEFT JOIN {db_prefix}members AS m ON (c.id_member = m.id_member)
	WHERE  m.real_name <> '' GROUP BY c.id_member ORDER BY total DESC   LIMIT 10");
	$context['top_raters'] = array();
	$max_top_raters = 1;
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['top_raters'][] = array(
			'total' => $row['total'],
			'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>',
		);

		if ($max_top_raters  < $row['total'])
			$max_top_raters = $row['total'];
	}
	$smcFunc['db_free_result']($result);
	foreach ($context['top_raters'] as $i => $picture)
		$context['top_raters'][$i]['percent'] = round(($picture['total'] * 100) / $max_top_raters );

   // Top Categories
	$result = $smcFunc['db_query']('', "SELECT count(*) as total, g.ID_CAT, c.title
	FROM {db_prefix}gallery_pic as g
        LEFT JOIN {db_prefix}gallery_cat as c ON (g.ID_CAT = c.ID_CAT)
        LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.ID_GROUP = $groupid AND g.ID_CAT = p.ID_CAT)
	WHERE (p.view = 1 or p.ID_CAT is null) AND g.USER_ID_CAT = 0 GROUP BY g.ID_CAT ORDER BY total DESC   LIMIT 10");
	$context['top_categories'] = array();
	$max_top_categories = 1;
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['top_categories'][] = array(
			'total' => $row['total'],
			'link' => '<a href="' . $scripturl . '?action=gallery;cat=' . $row['ID_CAT'] . '">' . $row['title'] . '</a>',
		);

		if ($max_top_categories  < $row['total'])
			$max_top_categories = $row['total'];
	}
	$smcFunc['db_free_result']($result);
	foreach ($context['top_categories'] as $i => $picture)
		$context['top_categories'][$i]['percent'] = round(($picture['total'] * 100) / $max_top_categories );

	// Load the template
	$context['sub_template']  = 'stats';
	// Set the page title
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_stats'];

   	$context['linktree'][] = array(
			'name' => $txt['gallery_text_stats']
		);
}

function ImportPictures()
{
	global $context, $mbname, $txt, $smcFunc;

	isAllowedTo('smfgallery_manage');


	$catid = (int) $_REQUEST['cat'];
	if (empty($catid))
		fatal_error($txt['gallery_error_no_cat']);

	$context['catid'] = $catid;

	$dbresult1 = $smcFunc['db_query']('', "SELECT title FROM {db_prefix}gallery_cat WHERE id_cat = $catid LIMIT 1");
	$row1 = $smcFunc['db_fetch_assoc']($dbresult1);
	$context['gallery_cat_name'] = $row1['title'];
	$smcFunc['db_free_result']($dbresult1);

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_import'];

	$context['sub_template']  = 'import';

    $context['linktree'][] = array(
			'name' =>  $txt['gallery_text_import']
		);
}

function ImportPictures2()
{
	global $txt, $scripturl, $sourcedir, $modSettings, $smcFunc, $user_info, $gd2;

	isAllowedTo('smfgallery_manage');

	$catid = (int) $_REQUEST['catid'];
	if (empty($catid))
		fatal_error($txt['gallery_error_no_cat']);
	//Get the import folder location
	$importfolder = $_REQUEST['importfolder'];
	if (empty($importfolder))
		fatal_error($txt['gallery_import_nofolder']);

	if (!file_exists($importfolder))
		fatal_error($txt['gallery_import_nofolderexists']);

	$deleteimport = isset($_REQUEST['deleteimport']) ? 1 : 0;

	// Check if gallery path is writable
	if (!is_writable($modSettings['gallery_path']))
		fatal_error($txt['gallery_write_error'] . $modSettings['gallery_path']);

	// Get category information
	$dbresult = $smcFunc['db_query']('', "SELECT id_board,postingsize,showpostlink,locktopic,id_topic,tweet_items FROM {db_prefix}gallery_cat WHERE id_cat = $catid");
	$rowcat = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);

	// Increase the max time to process the imports
	@ini_set('max_execution_time', '500');
	@ini_set("memory_limit","512M");
	// Read all files in directory
	$folder = dir($importfolder);
	require_once($sourcedir . '/Subs-Graphics.php');
	require_once($sourcedir . '/Subs-Post.php');

	$testGD = get_extension_funcs('gd');
	$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
	unset($testGD);
	$r = 0;
	while (false !== ($entry =  $folder->read()))
	{
		$orginalfilename = addslashes($entry);
		$fname = $importfolder . $entry;
		$sizes = @getimagesize($fname);
		$r++;

		// No size, then it's probably not a valid pic.
		if ($sizes === false)
			continue;
		elseif ((!empty($modSettings['gallery_max_width']) && $sizes[0] > $modSettings['gallery_max_width']) || (!empty($modSettings['gallery_max_height']) && $sizes[1] > $modSettings['gallery_max_height']))
		{
			continue;
		}
		else
		{
			//Get the filesize
			$filesize = filesize($fname);
			if (!empty($modSettings['gallery_max_filesize']) && $filesize > $modSettings['gallery_max_filesize'])
			{
				continue;
			}

			if ($modSettings['gallery_set_enable_multifolder'])
				CreateGalleryFolder();


			$extrafolder = '';

			if ($modSettings['gallery_set_enable_multifolder'])
				$extrafolder = $modSettings['gallery_folder_id'] . '/';


			// Filename Member Id + Day + Month + Year + 24 hour, Minute Seconds
			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

			$filename = $user_info['id'] . '-' . date('dmyHis') . '-' . $r . '.' . $extension;

			copy($fname, $modSettings['gallery_path'] . $extrafolder . $filename);
			@chmod($modSettings['gallery_path'] . $extrafolder . $filename, 0644);

			// Create thumbnail
			GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder . $filename, $modSettings['gallery_thumb_width'], $modSettings['gallery_thumb_height']);
			rename($modSettings['gallery_path'] . $extrafolder . $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder . 'thumb_' . $filename);
			$thumbname = 'thumb_' . $filename;
			@chmod($modSettings['gallery_path'] . $extrafolder .  'thumb_' . $filename, 0755);

			// Medium Image
			$mediumimage = '';

			if ($modSettings['gallery_make_medium'])
			{
				GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder .  $filename, $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
				rename($modSettings['gallery_path'] . $extrafolder .  $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename);
				$mediumimage = 'medium_' . $filename;
				@chmod($modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename, 0755);

				// Check for Watermark
				DoWaterMark($modSettings['gallery_path'] . $extrafolder .  'medium_' .  $filename);


			}

			// Delete the orignal file?
			if ($deleteimport == 1)
			{
				@unlink($fname);
			}

			// Create the Database entry
			$t = time();

			// Escape the filename
			$entry = $smcFunc['db_escape_string']($entry);

			$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_pic
						(id_cat, filesize,thumbfilename,filename, height, width, title,id_member,date,approved,allowcomments,mediumfilename,orginalfilename)
					VALUES ($catid, $filesize,'" . $extrafolder . $thumbname . "', '" . $extrafolder . $filename . "', $sizes[1], $sizes[0], '$entry', " . $user_info['id'] . ", $t,1, 1,'" . $extrafolder .$mediumimage . "','$orginalfilename')");

			$gallery_pic_id = $smcFunc['db_insert_id']('{db_prefix}gallery_pic', 'id_picture');

			// Get EXIF Data
			ProcessEXIFData($extrafolder . $filename, $gallery_pic_id);

			Gallery_AddRelatedPicture($gallery_pic_id, $entry);

            Gallery_AddToActivityStream('galleryproadd',$gallery_pic_id,$entry,$user_info['id']);

			// If we are using multifolders get the next folder id
			if ($modSettings['gallery_set_enable_multifolder'])
				ComputeNextFolderID($gallery_pic_id);

			// Update Quoate Information
			UpdateUserFileSizeTable($user_info['id'], $filesize);

			if ($rowcat['id_board'] != 0)
			{
				$extraheightwidth = '';
				if ($rowcat['postingsize'] == 1)
				{
					$postimg = $filename;
					$extraheightwidth = " height={$sizes[1]} width={$sizes[0]}";
				}
				else
					$postimg = $thumbname;

				if ($rowcat['showpostlink'] == 1)
					$showpostlink = "\n\n" . $scripturl . '?action=gallery;sa=view;id=' . $gallery_pic_id;
				else
					$showpostlink = '';

				// Create the post
				$msgOptions = array(
					'id' => 0,
					'subject' => $filename,
					'body' => "[img$extraheightwidth]" . $modSettings['gallery_url']  . $extrafolder . $postimg . "[/img]$showpostlink",
					'icon' => 'xx',
					'smileys_enabled' => 1,
					'attachments' => array(),
				);
				$topicOptions = array(
					'id' => $rowcat['id_topic'],
					'board' => $rowcat['id_board'],
					'poll' => null,
					'lock_mode' => $rowcat['locktopic'],
					'sticky_mode' => null,
					'mark_as_read' => true,
				);
				$posterOptions = array(
					'id' => $user_info['id'],
					'update_post_count' => !$user_info['is_guest'],
				);
				// Fix height & width of posted image in message
				preparsecode($msgOptions['body']);

				createPost($msgOptions, $topicOptions, $posterOptions);


			require_once($sourcedir . '/Post.php');

                    if (function_exists("notifyMembersBoard"))
                    {
			$notifyData = array(
						'body' =>$msgOptions['body'],
						'subject' => $msgOptions['subject'],
						'name' => $user_info['name'],
						'poster' => $user_info['id'],
						'msg' => $msgOptions['id'],
						'board' =>  $rowcat['id_board'],
						'topic' => $topicOptions['id'],
					);
			notifyMembersBoard($notifyData);
                    }
                    else
                    {
                     // for 2.1
                    $smcFunc['db_insert']('',
                        '{db_prefix}background_tasks',
                        array('task_file' => 'string', 'task_class' => 'string', 'task_data' => 'string', 'claimed_time' => 'int'),
                        array('$sourcedir/tasks/CreatePost-Notify.php', 'CreatePost_Notify_Background', $smcFunc['json_encode'](array(
                            'msgOptions' => $msgOptions,
                            'topicOptions' => $topicOptions,
                            'posterOptions' => $posterOptions,
                            'type' =>  $topicOptions['id'] ? 'reply' : 'topic',
                        )), 0),
                        array('id_task')
                    );


                 }



				$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
					SET id_topic = " .$topicOptions['id'] . ", id_msg = " . $msgOptions['id'] . " WHERE id_picture = $gallery_pic_id
					");
                }

                if ($rowcat['tweet_items'] == 1)
                    Gallery_TweetItem($filename,$gallery_pic_id);

				// Check for Watermark
				DoWaterMark($modSettings['gallery_path'] . $extrafolder . $filename);


		}
	}

	UpdateMemberPictureTotals($user_info['id']);

	Gallery_UpdateLatestCategory($catid);
	UpdateCategoryTotals($catid);

	//Redirect to the category
	redirectexit('action=gallery;cat=' .  $catid);
}

function BulkAdd()
{
	global $context, $mbname, $txt, $smcFunc, $user_info, $gallerySettings, $modSettings, $scripturl;

	isAllowedTo('smfgallery_bulk');

	if (isset($_REQUEST['cat']))
		$catid = (int) $_REQUEST['cat'];
	else
		$catid = 0;

	// Check if they can add a picture to this category
	if (!empty($catid))
		GetCatPermission($catid,'addpic');

	$context['catid'] = $catid;

	if (isset($_REQUEST['usercat']))
		$usercatid = (int) $_REQUEST['usercat'];
	else
		$usercatid = 0;

	$context['usercatid'] = $usercatid;

	if(empty($catid) && empty($usercatid))
		fatal_error($txt['gallery_error_no_cat'],false);

	$context['gallery_bulk_cat_link'] = '';

	if (!empty($catid))
	{
		$dbresult1 = $smcFunc['db_query']('', "
		SELECT
			title, locked
		FROM {db_prefix}gallery_cat
		WHERE id_cat = $catid LIMIT 1");
		$row1 = $smcFunc['db_fetch_assoc']($dbresult1);
		$context['gallery_cat_name'] = $row1['title'];
		$smcFunc['db_free_result']($dbresult1);

		$context['gallery_bulk_cat_link']  = $scripturl . '?action=gallery;cat=' . $catid;

		$g_manage = allowedTo('smfgallery_manage');
		if ($g_manage == false && $row1['locked'] == 1)
			fatal_error($txt['gallery_err_locked_upload'],false);


		$result = $smcFunc['db_query']('', "
		SELECT
			title, defaultvalue, is_required, id_custom
		FROM {db_prefix}gallery_custom_field
		WHERE id_cat = " . $catid . " or id_cat = 0");
		$context['gallery_addpic_customfields'] = array();
		while ($row2 = $smcFunc['db_fetch_assoc']($result))
		{
			$context['gallery_addpic_customfields'][] = $row2;
		}
		$smcFunc['db_free_result']($result);


	}

	if (!empty($usercatid))
	{
		$dbresult1 = $smcFunc['db_query']('', "
			SELECT
				title, id_member
			FROM {db_prefix}gallery_usercat
			WHERE user_id_cat = $usercatid LIMIT 1");

		$row1 = $smcFunc['db_fetch_assoc']($dbresult1);
		$context['gallery_cat_name'] = $row1['title'];
		$context['gallery_user_userid'] = $row1['id_member'];
		$smcFunc['db_free_result']($dbresult1);

		$context['gallery_bulk_cat_link']  = $scripturl . '?action=gallery;su=user;cat=' . $usercatid . ';u=' . $context['gallery_user_userid'];

		$g_manage = allowedTo('smfgallery_manage');

		if ($row1['id_member'] != $user_info['id'] && $g_manage == false)
			fatal_error($txt['gallery_user_noperm']);
	}


	// Are we using the multi uploader
	if ($gallerySettings['gallery_set_multifile_upload_for_bulk'])
	{

		$version = 2;
		if ($version == 2)
		{
			$context['html_headers'] .= '
<link rel="stylesheet" type="text/css" href="' .$modSettings['gallery_url'] . 'dropzone/dropzone.css" />
<script type="text/javascript" src="' .$modSettings['gallery_url'] . 'dropzone/dropzone.js"></script>';
		}


		if ($version == 1)
		{

		$context['html_headers'] .= '
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/mootools/1.3.2/mootools.js"></script>
	<script type="text/javascript" src="' .$modSettings['gallery_url'] . 'fupload/source/Swiff.Uploader.js"></script>
	<script type="text/javascript" src="' .$modSettings['gallery_url'] . 'fupload/source/Fx.ProgressBar.js"></script>
		<script type="text/javascript" src="' .$modSettings['gallery_url'] . 'fupload/source/Lang.js"></script>
		<script type="text/javascript" src="' .$modSettings['gallery_url'] . 'fupload/source/FancyUpload2.js"></script>

	<script type="text/javascript">
		//<![CDATA[';
$context['html_headers'] .= "
		/**
 * FancyUpload Showcase
 *
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 */

window.addEvent('domready', function() { // wait for the content

	// our uploader instance

	var up = new FancyUpload2($('demo-status'), $('demo-list'), { // options object
		// we console.log infos, remove that in production!!
		verbose: true,

		// url is read from the form, so you just have to change one place
		url: $('form-demo').action,

		// path to the SWF file
		path: '" .$modSettings['gallery_url'] . "fupload/source/Swiff.Uploader.swf',

		// remove that line to select all files, or edit it, add more items
		typeFilter: {
			'Images (*.jpg, *.jpeg, *.gif, *.png, *.bmp, *.zip)': '*.jpg; *.jpeg; *.gif; *.png; *.bmp; *.zip'
		},

		// this is our browse button, *target* is overlayed with the Flash movie
		target: 'demo-browse',

		fieldName: 'picture',


		// graceful degradation, onLoad is only called if all went well with Flash
		onLoad: function() {
			$('demo-status').removeClass('hide'); // we show the actual UI
			$('demo-fallback').destroy(); // ... and hide the plain form

			// We relay the interactions with the overlayed flash to the link
			this.target.addEvents({
				click: function() {
					return false;
				},
				mouseenter: function() {
					this.addClass('hover');
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			});

			// Interactions for the 2 other buttons

			$('demo-clear').addEvent('click', function() {
				up.remove(); // remove all files
				return false;
			});

			$('demo-upload').addEvent('click', function() {
				up.start(); // start upload
				return false;
			});
		},

		// Edit the following lines, it is your custom event handling

		/**
		 * Is called when files were not added, files is an array of invalid File classes.
		 *
		 * This example creates a list of error elements directly in the file list, which
		 * hide on click.
		 */
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'validation-error',
					html: file.validationErrorMessage || file.validationError,
					title: MooTools.lang.get('FancyUpload', 'removeTitle'),
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).inject(this.list, 'top');
			}, this);
		},

		/**
		 * This one was directly in FancyUpload2 before, the event makes it
		 * easier for you, to add your own response handling (you probably want
		 * to send something else than JSON or different items).
		 */
		onFileSuccess: function(file, response) {
			var json = new Hash(JSON.decode(response, true) || {});

			if (json.get('status') == '1') {
				file.element.addClass('file-success');
				file.info.set('html', '<strong>" . $txt['gallery_txt_multi_imageuploaded']  . "</strong> ' + json.get('width') + ' x ' + json.get('height') + 'px, <em>' + json.get('mime') + '</em>)');
			} else {
				file.element.addClass('file-failed');
				file.info.set('html', '<strong>" . $txt['gallery_txt_multi_imageerror'] . "</strong> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
			}
		},

		/**
		 * onFail is called when the Flash movie got bashed by some browser plugin
		 * like Adblock or Flashblock.
		 */
		onFail: function(error) {
			switch (error) {
				case 'hidden': // works after enabling the movie and clicking refresh
					alert('" . $txt['gallery_txt_multi_err_adblock']  . "');
					break;
				case 'blocked': // This no *full* fail, it works after the user clicks the button
					alert('" . $txt['gallery_txt_multi_err_flashblock'] . "');
					break;
				case 'empty': // Oh oh, wrong path
					alert('" . $txt['gallery_txt_multi_err_missing_required'] . "');
					break;
				case 'flash': // no flash 9+ :(
					alert('" . $txt['gallery_txt_multi_err_adobe'] . "')
			}
		}

	});

});
		//]]>
	</script>";

$context['html_headers'] .= '

	<!-- See style.css -->
	<style type="text/css">
		/**
 * FancyUpload Showcase
 *
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 */

/* CSS vs. Adblock tabs */
.swiff-uploader-box a {
	display: none !important;
}

/* .hover simulates the flash interactions */
a:hover, a.hover {
	color: red;
}

#demo-status {
	padding: 10px 15px;
	width: 420px;
	border: 1px solid #eee;
}

#demo-status .progress {
	background: url(' .$modSettings['gallery_url'] . 'fupload/assets/progress-bar/progress.gif) no-repeat;
	background-position: +50% 0;
	margin-right: 0.5em;
	vertical-align: middle;
}

#demo-status .progress-text {
	font-size: 0.9em;
	font-weight: bold;
}

#demo-list {
	list-style: none;
	width: 450px;
	margin: 0;
}

#demo-list li.validation-error {
	padding-left: 44px;
	display: block;
	clear: left;
	line-height: 40px;
	color: #8a1f11;
	cursor: pointer;
	border-bottom: 1px solid #fbc2c4;
	background: #fbe3e4 url(' .$modSettings['gallery_url'] . 'fupload/assets/failed.png) no-repeat 4px 4px;
}

#demo-list li.file {
	border-bottom: 1px solid #eee;
	background: url(' .$modSettings['gallery_url'] . 'fupload/assets/file.png) no-repeat 4px 4px;
	overflow: auto;
}
#demo-list li.file.file-uploading {
	background-image: url(' .$modSettings['gallery_url'] . 'fupload/assets/uploading.png);
	background-color: #D9DDE9;
}
#demo-list li.file.file-success {
	background-image: url(' .$modSettings['gallery_url'] . 'fupload/assets/success.png);
}
#demo-list li.file.file-failed {
	background-image: url(' .$modSettings['gallery_url'] . 'fupload/assets/failed.png);
}

#demo-list li.file .file-name {
	font-size: 1.2em;
	margin-left: 44px;
	display: block;
	clear: left;
	line-height: 40px;
	height: 40px;
	font-weight: bold;
}
#demo-list li.file .file-size {
	font-size: 0.9em;
	line-height: 18px;
	float: right;
	margin-top: 2px;
	margin-right: 6px;
}
#demo-list li.file .file-info {
	display: block;
	margin-left: 44px;
	font-size: 0.9em;
	line-height: 20px;
	clear
}
#demo-list li.file .file-remove {
	clear: right;
	float: right;
	line-height: 18px;
	margin-right: 6px;
}	</style>

		';

		} // Version 1

	}


	// Get Quota Limits to Display
	$context['quotalimit'] = GetQuotaGroupLimit($user_info['id']);
	$context['userspace'] = GetUserSpaceUsed($user_info['id']);

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_bulkadd'];

	$context['sub_template']  = 'bulk';

    $context['linktree'][] = array(
			'name' =>  $txt['gallery_text_bulkadd']
		);
}

function BulkAdd2()
{
	global $txt, $scripturl, $sourcedir, $modSettings, $gallerySettings, $smcFunc, $user_info, $gd2, $mbname, $context;

	isAllowedTo('smfgallery_bulk');

 	// Increase the max time to process the bulk added images
	ini_set('max_execution_time', '600');
	ini_set("memory_limit","512M");

	$ajax = 0;

	if (isset($_REQUEST['ajax']))
		$ajax = 1;


	$catid = (int) $_REQUEST['catid'];
	$usercatid = (int) $_REQUEST['usercatid'];
	$g_manage = allowedTo('smfgallery_manage');

	// Check if they can add a picture to this category
	if (!empty($catid))
		GetCatPermission($catid,'addpic');

	if (empty($catid) && empty($usercatid))
		fatal_error($txt['gallery_error_no_cat']);

	$errors = '';
	$good = '';

	// Check if gallery path is writable
	if (!is_writable($modSettings['gallery_path']))
		fatal_error($txt['gallery_write_error'] . $modSettings['gallery_path']);



	// Check if pictures are auto approved
	$approved = 0;

	if (empty($usercatid))
	{
		$approve_tmp = GetCatPermission($catid,'autoapprove',true);

		if ($approve_tmp !=2)
		{
			$approved = $approve_tmp;
		}

	}

	if (empty($approved))
	{
		$approved = (allowedTo('smfgallery_autoapprove') ? 1 : 0);
	}

	// Get category infomation
	if (empty($usercatid))
	{
		$dbresult = $smcFunc['db_query']('', "SELECT id_cat,id_board, postingsize, locked, showpostlink, locktopic, id_topic,tweet_items FROM {db_prefix}gallery_cat WHERE id_cat = $catid");
		$rowcat = $smcFunc['db_fetch_assoc']($dbresult);
		$smcFunc['db_free_result']($dbresult);

        if (empty($rowcat['id_cat']))
            fatal_error($txt['gallery_error_no_cat'],false);

		if ($g_manage == false && $rowcat['locked'] == 1)
			fatal_error($txt['gallery_err_locked_upload'],false);
	}


	if (!empty($usercatid))
	{
		$dbresult1 = $smcFunc['db_query']('', "
			SELECT
				title, id_member
			FROM {db_prefix}gallery_usercat
			WHERE user_id_cat = $usercatid LIMIT 1");

		$row1 = $smcFunc['db_fetch_assoc']($dbresult1);
		$context['gallery_cat_name'] = $row1['title'];
		$smcFunc['db_free_result']($dbresult1);

		$g_manage = allowedTo('smfgallery_manage');

		if ($row1['id_member'] != $user_info['id'] && $g_manage == false)
			fatal_error($txt['gallery_user_noperm']);

	}

	$files_count = 10;
	if ($ajax == 1)
	{
		$storage = $_FILES['picture'];

		$_FILES['picture'] = array();
		$_FILES['picture']['name'][0] = $storage['name'];
		$_FILES['picture']['tmp_name'][0] = $storage['tmp_name'];
		$_REQUEST['title'][0] = $storage['name'];
		$_REQUEST['description'][0] = '';
		$files_count =1;
	}

	$exifData = '';

	require_once($sourcedir . '/Subs-Post.php');
	require_once($sourcedir . '/Subs-Graphics.php');
	$testGD = get_extension_funcs('gd');
	$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
	unset($testGD);

	for ($n =0;$n < $files_count; $n++)
	{
		// Process Uploaded file
		if (isset($_FILES['picture']['name'][$n]) && $_FILES['picture']['name'][$n] != '')
		{
			$orginalfilename = addslashes($_FILES['picture']['name'][$n]);
			CheckMaxUploadPerDay();
			/*
			if ($_REQUEST['title'][$n] == '' && $g_manage == false)
			{
				$errors .= $txt['gallery_bulk_notitle'] . ($n+1) . '<br />';
				continue;
			}
			*/

			$title = $smcFunc['htmlspecialchars']($_REQUEST['title'][$n],ENT_QUOTES);

			 //Remove Ext Edit
			$newFileName = $_FILES['picture']['name'][$n];
			$tmpFile = substr($newFileName, 0 , (strrpos($newFileName, ".")));
			$title = $smcFunc['htmlspecialchars']($tmpFile, ENT_QUOTES);

			$description = $smcFunc['htmlspecialchars']($_REQUEST['description'][$n],ENT_QUOTES);
			
		if (empty($usercatid))
		{
			$result = $smcFunc['db_query']('', "
		SELECT f.title, f.is_required, f.id_custom
		FROM  {db_prefix}gallery_custom_field as f
				WHERE f.is_required = 1 AND (f.id_cat = " . $catid . " OR f.ID_CAT = 0)");
			while ($row2 = $smcFunc['db_fetch_assoc']($result))
			{
				if (!isset($_REQUEST['cus_' . $row2['id_custom']][$n]))
				{
					$errors .= $txt['gallery_err_req_custom_field'] . $row2['title'];
				} else
				{
					if ($_REQUEST['cus_' . $row2['id_custom']][$n] == '')
					{
						$errors .= $txt['gallery_err_req_custom_field'] . $row2['title'];
					}
				}
			}
			$smcFunc['db_free_result']($result);

			if (!empty($errors))
				continue;
		}


	       $iszipFile = strtolower(substr(strrchr($_FILES['picture']['name'][$n], '.'), 1));

			if ($iszipFile == 'zip' && function_exists('zip_open'))
			{

	       	 	$zip = zip_open($_FILES['picture']['tmp_name'][$n]);
		       	$gallerySettings['gallery_set_disallow_extensions'] = trim($gallerySettings['gallery_set_disallow_extensions']);
				$gallerySettings['gallery_set_disallow_extensions'] = str_replace(".","",$gallerySettings['gallery_set_disallow_extensions']);
				$gallerySettings['gallery_set_disallow_extensions'] = strtolower($gallerySettings['gallery_set_disallow_extensions']);
				$disallowedExtensions = explode(",",$gallerySettings['gallery_set_disallow_extensions']);

		        if ($zip)
		        {

		            // find entry
		            do {
		                $entry = zip_read($zip);
		                if (!is_resource($entry))
		             		continue;

		             	$extrafolder = '';

						if ($modSettings['gallery_set_enable_multifolder'])
						{
							$extrafolder = $modSettings['gallery_folder_id'] . '/';
						}

		                $zipName = zip_entry_name($entry);
		                $zipExt = substr(strrchr($zipName, '.'), 1);
						$zipExt = strtolower($zipExt);

		                if ($zipExt != 'zip' & !in_array($zipExt,$disallowedExtensions))
		                {
			                	$ziplistFilename = 'zipE' . uniqid();


		                	// Extract file
							  if (zip_entry_open($zip, $entry))
							   {
							      	$fp=fopen($modSettings['gallery_path'] . $extrafolder . $ziplistFilename,"a");
							      	while ($contents = zip_entry_read($entry))
							      	{
								         fwrite($fp,$contents);
							      	}
							      	  fclose($fp);
									$sizes = @getimagesize($modSettings['gallery_path'] . $extrafolder . $ziplistFilename);
									$image_resized = 0;

									$extensions = array(
											1 => 'gif',
											2 => 'jpeg',
											3 => 'png',
											5 => 'psd',
											6 => 'bmp',
											7 => 'tiff',
											8 => 'tiff',
											9 => 'jpeg',
											14 => 'iff',
											18 => 'webp',
											);
									$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : 'bmp';

									$orginalfilename = addslashes($zipName);

							      	 $title = $smcFunc['htmlspecialchars']($zipName,ENT_QUOTES);

			     if (strtolower($extension) != 'png')
					$modSettings['avatar_download_png'] = 0;

				// No size, then it's probably not a valid pic.
				if ($sizes === false)
				{

					$errors .= $title . '&nbsp;' . $txt['gallery_error_invalid_picture'] . '<br />';
					@unlink($modSettings['gallery_path'] . $extrafolder . $ziplistFilename);
					continue;
				}
				elseif ((!empty($modSettings['gallery_max_width']) && $sizes[0] > $modSettings['gallery_max_width']) || (!empty($modSettings['gallery_max_height']) && $sizes[1] > $modSettings['gallery_max_height']))
				{

						if (!empty($modSettings['gallery_resize_image']))
						{
							// Check to resize image?

							DoImageResize($sizes,$modSettings['gallery_path'] . $extrafolder . $ziplistFilename);
							$image_resized = 1;

						}
						else
						{

							$errors .= $title . '&nbsp;' . $txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0] . '<br />';
							// Delete the temp file
							@unlink($modSettings['gallery_path'] . $extrafolder . $ziplistFilename);
							continue;
						}

				}

					// Check min size
					if ((!empty($modSettings['gallery_min_width']) && $sizes[0] < $modSettings['gallery_min_width']) || (!empty($modSettings['gallery_min_height']) && $sizes[1] < $modSettings['gallery_min_height']))
					{
						// Delete the temp file
						@unlink($modSettings['gallery_path'] . $extrafolder . $ziplistFilename);
						continue;

					}

					// Get the filesize
					$filesize = filesize($modSettings['gallery_path'] . $extrafolder . $ziplistFilename);
					if (!empty($modSettings['gallery_max_filesize']) && $filesize > $modSettings['gallery_max_filesize'])
					{

						$errors .= $title . '&nbsp;' . $txt['gallery_error_img_filesize'] . $modSettings['gallery_max_filesize'] . '<br />';

						// Delete the temp file
						@unlink($modSettings['gallery_path'] . $extrafolder . $ziplistFilename);
						continue;
					}

					$quotalimit = GetQuotaGroupLimit($user_info['id']);
					$userspace = GetUserSpaceUsed($user_info['id']);
					// Check if exceeds quota limit or if there is a quota
					if ($quotalimit != 0  &&  ($userspace + $filesize) >  $quotalimit)
					{

						$errors .= $title . '&nbsp;' . $txt['gallery_error_space_limit'] . gallery_format_size($userspace, 2) . ' / ' . gallery_format_size($quotalimit, 2)  . '<br />';

						@unlink($modSettings['gallery_path'] . $extrafolder . $ziplistFilename);
						continue;
					}

					$return = array(
						'status' => '1',
						'name' => $title,
					);

					// Our processing, we get a hash value from the file
					$return['hash'] = md5_file($modSettings['gallery_path'] . $extrafolder . $ziplistFilename);

					$return['width'] = $sizes[0];
					$return['height'] = $sizes[1];
					$return['mime'] = $sizes['mime'];



					if ($modSettings['gallery_set_enable_multifolder'])
						CreateGalleryFolder();

					$extrafolder = '';

					if ($modSettings['gallery_set_enable_multifolder'])
					{
						$extrafolder = $modSettings['gallery_folder_id'] . '/';
					}

					// Filename Member Id + Day + Month + Year + 24 hour, Minute Seconds
					$extensions = array(
							1 => 'gif',
							2 => 'jpeg',
							3 => 'png',
							5 => 'psd',
							6 => 'bmp',
							7 => 'tiff',
							8 => 'tiff',
							9 => 'jpeg',
							14 => 'iff',
							18 => 'webp',
							);
					$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

					if (empty($gallerySettings['last_bulk_pic_id']))
						$gallerySettings['last_bulk_pic_id'] = $n;

					$gallerySettings['last_bulk_pic_id'] .= rand(1,2500);

					$filename = $user_info['id'] . '_' . date('d_m_y_g_i_s') . '_' . $gallerySettings['last_bulk_pic_id'] . '.' . $extension;

					rename($modSettings['gallery_path'] . $extrafolder . $ziplistFilename, $modSettings['gallery_path'] . $extrafolder . $filename);
					@chmod($modSettings['gallery_path'] . $extrafolder . $filename, 0644);
					// Create thumbnail

					GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder . $filename, $modSettings['gallery_thumb_width'], $modSettings['gallery_thumb_height']);
					rename($modSettings['gallery_path'] . $extrafolder . $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder . 'thumb_' . $filename);
					$thumbname = 'thumb_' . $filename;
					@chmod($modSettings['gallery_path'] . $extrafolder .  'thumb_' . $filename, 0755);

					if ($image_resized)
						$sizes = @getimagesize($modSettings['gallery_path'] . $extrafolder .  $filename);


					// Medium Image
					$mediumimage = '';

					if ($modSettings['gallery_make_medium'])
					{
						GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder .  $filename, $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
						rename($modSettings['gallery_path'] . $extrafolder .  $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename);
						$mediumimage = 'medium_' . $filename;
						@chmod($modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename, 0755);

						// Check for Watermark
						DoWaterMark($modSettings['gallery_path'] . $extrafolder .  'medium_' .  $filename);
					}


					// Create the Database entry
					$t = time();
		              $smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_pic
						(id_cat, user_id_cat, filesize,thumbfilename,filename, height, width, title, description,id_member,date,approved,allowcomments,mediumfilename,orginalfilename)
					VALUES ($catid, $usercatid, $filesize,'" . $extrafolder . $thumbname . "', '" . $extrafolder . $filename . "', $sizes[1], $sizes[0], '$title', '$description', " . $user_info['id'] . ",$t,$approved, 1,'" . $extrafolder . $mediumimage . "','$orginalfilename')");

			$gallery_pic_id = $smcFunc['db_insert_id']('{db_prefix}gallery_pic', 'id_picture');

				UpdateGallerySettings(array("last_bulk_pic_id" => $gallery_pic_id));

				// Get EXIF Data
				ProcessEXIFData($extrafolder . $filename,$gallery_pic_id, $exifData );

				Gallery_AddRelatedPicture($gallery_pic_id, $title);

				if ($usercatid == 0)
				{
					// Check for any custom fields
					$result = $smcFunc['db_query']('', "
					SELECT
						f.title, f.is_required, f.id_custom
					FROM {db_prefix}gallery_custom_field as f
					WHERE f.id_cat = " . $catid . " or f.id_cat = 0");
					while ($row2 = $smcFunc['db_fetch_assoc']($result))
					{
						if (isset($_REQUEST['cus_' . $row2['id_custom']][$n]))
						{

							$custom_data = $smcFunc['htmlspecialchars']($_REQUEST['cus_' . $row2['id_custom']][$n], ENT_QUOTES);

							$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_custom_field_data
							(id_picture, id_custom, value)
							VALUES('$gallery_pic_id', " . $row2['id_custom'] . ", '$custom_data')");
						}
					}
					$smcFunc['db_free_result']($result);
				}

                Gallery_AddToActivityStream('galleryproadd',$gallery_pic_id,$title,$user_info['id']);

				// If we are using multifolders get the next folder id
				if ($modSettings['gallery_set_enable_multifolder'])
					ComputeNextFolderID($gallery_pic_id);

				$good .= $title .'<br />';

				// Update Quota Information
				UpdateUserFileSizeTable($user_info['id'], $filesize);

			if (isset($rowcat['id_board'])  && $approved == 1)
			if ($usercatid == 0 && $rowcat['id_board'] != 0)
			{

				$extraheightwidth = '';
				if ($rowcat['postingsize'] == 1)
				{
					$postimg = $filename;
					$extraheightwidth = " height={$sizes[1]} width={$sizes[0]}";
				}
				else
					$postimg = $thumbname;

				if ($rowcat['showpostlink'] == 1)
					$showpostlink = "\n\n" . $scripturl . '?action=gallery;sa=view;id=' . $gallery_pic_id;
				else
					$showpostlink = '';
				//Create the post
				require_once($sourcedir . '/Subs-Post.php');
				$msgOptions = array(
					'id' => 0,
					'subject' => $title,
					'body' => '[b]' . $title . "[/b]\n\n[img$extraheightwidth]" . $modSettings['gallery_url']  . $extrafolder . $postimg . "[/img]$showpostlink\n\n$description",
					'icon' => 'xx',
					'smileys_enabled' => 1,
					'attachments' => array(),
				);
				$topicOptions = array(
					'id' => $rowcat['id_topic'],
					'board' => $rowcat['id_board'],
					'poll' => null,
					'lock_mode' => $rowcat['locktopic'],
					'sticky_mode' => null,
					'mark_as_read' => true,
				);
				$posterOptions = array(
					'id' => $user_info['id'],
					'update_post_count' => !$user_info['is_guest'],
				);
				// Fix height & width of posted image in message
				preparsecode($msgOptions['body']);

				createPost($msgOptions, $topicOptions, $posterOptions);

			require_once($sourcedir . '/Post.php');


                    if (function_exists("notifyMembersBoard"))
                    {
                            $notifyData = array(
                                        'body' =>$msgOptions['body'],
                                        'subject' => $msgOptions['subject'],
                                        'name' => $user_info['name'],
                                        'poster' => $user_info['id'],
                                        'msg' => $msgOptions['id'],
                                        'board' =>  $rowcat['id_board'],
                                        'topic' => $topicOptions['id'],
                                    );
                            notifyMembersBoard($notifyData);

                    }
                    else
                    {

                     // for 2.1
                    $smcFunc['db_insert']('',
                        '{db_prefix}background_tasks',
                        array('task_file' => 'string', 'task_class' => 'string', 'task_data' => 'string', 'claimed_time' => 'int'),
                        array('$sourcedir/tasks/CreatePost-Notify.php', 'CreatePost_Notify_Background', $smcFunc['json_encode'](array(
                            'msgOptions' => $msgOptions,
                            'topicOptions' => $topicOptions,
                            'posterOptions' => $posterOptions,
                            'type' =>  $topicOptions['id'] ? 'reply' : 'topic',
                        )), 0),
                        array('id_task')
                    );

                    }



				$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
					SET id_topic = " .$topicOptions['id'] . ", id_msg = " . $msgOptions['id'] . " WHERE id_picture = $gallery_pic_id
					");

			}

		// Update the SMF Shop Points
		if (isset($modSettings['shopVersion']))
			$smcFunc['db_query']('', "UPDATE {db_prefix}members
				SET money = money + " . $modSettings['gallery_shop_picadd'] . "
				WHERE id_member = " . $user_info['id'] . "
				LIMIT 1");


            if ($approved == 0)
            {
                $body = $txt['gallery_txt_itemwaitingapproval2'];
                $body = str_replace("%url",$scripturl . '?action=admin;area=gallery;sa=approvelist',$body);
                $body = str_replace("%title",$title,$body);

                Gallery_emailAdmins($txt['gallery_txt_itemwaitingapproval'],$body);
            }
            else
            {

			// Add Post Count
				 if (!empty($gallerySettings['gallery_set_picturepostcount']))
				 {
					if ($user_info['id'] != 0)
					{
						updateMemberData($user_info['id'], array('posts' => '+'));
					}
				 }

                if (isset($rowcat['id_board']))
                {
                    if ($rowcat['tweet_items'] == 1)
                        Gallery_TweetItem($title,$gallery_pic_id);
                }
            }

				// Last recheck Image if it was resized
				if ($image_resized == 1)
				{
					RecheckResizedImage($modSettings['gallery_path'] . $extrafolder . $filename,$gallery_pic_id,$filesize,$user_info['id']);
				}

				// Check for Watermark
				DoWaterMark($modSettings['gallery_path'] . $extrafolder . $filename);



							      zip_entry_close($entry);

		                		}
		                }
		            } while ($entry);

		            zip_close($zip);
		        } // end if zip


				continue;
			} // end zip


			$sizes = @getimagesize($_FILES['picture']['tmp_name'][$n]);
			$image_resized = 0;

			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);

			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : 'bmp';

			$gallerySettings['gallery_set_disallow_extensions'] = trim($gallerySettings['gallery_set_disallow_extensions']);
			$gallerySettings['gallery_set_disallow_extensions'] = str_replace(".","",$gallerySettings['gallery_set_disallow_extensions']);
			$gallerySettings['gallery_set_disallow_extensions'] = strtolower($gallerySettings['gallery_set_disallow_extensions']);
			$disallowedExtensions = explode(",",$gallerySettings['gallery_set_disallow_extensions']);
			if (in_array($extension,$disallowedExtensions))
			{

				$errors .= $_FILES['picture']['name'][$n] . $txt['gallery_err_disallow_extensions'] . $extension;
				@unlink($_FILES['picture']['tmp_name'][$n]);
				continue;
			}


			if (strtolower($extension) != 'png')
				$modSettings['avatar_download_png'] = 0;

			// No size, then it's probably not a valid pic.
			if ($sizes === false)
			{
				@unlink($_FILES['picture']['tmp_name'][$n]);
				$errors .= $_FILES['picture']['name'][$n] . '&nbsp;' . $txt['gallery_error_invalid_picture'] . '<br />';
				continue;
			}
			elseif ((!empty($modSettings['gallery_max_width']) && $sizes[0] > $modSettings['gallery_max_width']) || (!empty($modSettings['gallery_max_height']) && $sizes[1] > $modSettings['gallery_max_height']))
			{
				if (!empty($modSettings['gallery_resize_image']))
				{
					//Check to resize image?
					$exifData = ReturnEXIFData($_FILES['picture']['tmp_name'][$n]);
					DoImageResize($sizes,$_FILES['picture']['tmp_name'][$n]);
					$image_resized = 1;
				}
				else
				{
					$errors .= $_FILES['picture']['name'][$n] . '&nbsp;' . $txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0] . '<br />';
					// Delete the temp file
					@unlink($_FILES['picture']['tmp_name'][$n]);
					continue;
				}
			}

			// Check min size
			if ((!empty($modSettings['gallery_min_width']) && $sizes[0] < $modSettings['gallery_min_width']) || (!empty($modSettings['gallery_min_height']) && $sizes[1] < $modSettings['gallery_min_height']))
			{
				// Delete the temp file
				@unlink($_FILES['picture']['tmp_name'][$n]);
					continue;
			}

			// Get the filesize
			$filesize = filesize($_FILES['picture']['tmp_name'][$n]);
			if (!empty($modSettings['gallery_max_filesize']) && $filesize > $modSettings['gallery_max_filesize'])
			{
				$errors .= $_FILES['picture']['name'][$n] . '&nbsp;' . $txt['gallery_error_img_filesize'] . $modSettings['gallery_max_filesize'] . '<br />';

				// Delete the temp file
				@unlink($_FILES['picture']['tmp_name'][$n]);
				continue;
			}

			$quotalimit = GetQuotaGroupLimit($user_info['id']);
			$userspace = GetUserSpaceUsed($user_info['id']);
			// Check if exceeds quota limit or if there is a quota
			if ($quotalimit != 0  &&  ($userspace + $filesize) >  $quotalimit)
			{
				$errors .= $_FILES['picture']['name'][$n] . '&nbsp;' . $txt['gallery_error_space_limit'] . gallery_format_size($userspace, 2)  . ' / ' . gallery_format_size($quotalimit, 2)  . '<br />';

				@unlink($_FILES['picture']['tmp_name'][$n]);
				continue;
			}

			$return = array(
						'status' => '1',
						'name' => $_FILES['picture']['name'][$n],
					);

			// Our processing, we get a hash value from the file
			$return['hash'] = md5_file($_FILES['picture']['tmp_name'][$n]);

			$return['width'] = $sizes[0];
			$return['height'] = $sizes[1];
			$return['mime'] = $sizes['mime'];


			if ($modSettings['gallery_set_enable_multifolder'])
				CreateGalleryFolder();

			$extrafolder = '';

			if ($modSettings['gallery_set_enable_multifolder'])
				$extrafolder = $modSettings['gallery_folder_id'] . '/';

			// Filename Member Id + Day + Month + Year + 24 hour, Minute Seconds
			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

			if (empty($gallerySettings['last_bulk_pic_id']))
				$gallerySettings['last_bulk_pic_id'] = $n;

			$gallerySettings['last_bulk_pic_id'] .= rand(1,2500);


			$filename = $user_info['id'] . '-' . date('dmyHis') . '-' .$gallerySettings['last_bulk_pic_id'] . '.' . $extension;

			move_uploaded_file($_FILES['picture']['tmp_name'][$n], $modSettings['gallery_path'] . $extrafolder . $filename);
			@chmod($modSettings['gallery_path'] . $extrafolder . $filename, 0644);
			// Create thumbnail
			GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder . $filename, $modSettings['gallery_thumb_width'], $modSettings['gallery_thumb_height']);
			rename($modSettings['gallery_path'] . $extrafolder . $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder . 'thumb_' . $filename);
			$thumbname = 'thumb_' . $filename;
			@chmod($modSettings['gallery_path'] . $extrafolder .  'thumb_' . $filename, 0755);

			if ($image_resized)
				$sizes = @getimagesize($modSettings['gallery_path'] . $extrafolder .  $filename);


			// Medium Image
			$mediumimage = '';
			$description = '';
			
			if ($modSettings['gallery_make_medium'])
			{
				GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder .  $filename, $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
				rename($modSettings['gallery_path'] . $extrafolder .  $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename);
				$mediumimage = 'medium_' . $filename;
				@chmod($modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename, 0755);

				// Check for Watermark
				DoWaterMark($modSettings['gallery_path'] . $extrafolder .  'medium_' .  $filename);


			}

			// Create the Database entry
			$t = time();
			$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_pic
						(id_cat, user_id_cat, filesize,thumbfilename,filename, height, width, title, description,id_member,date,approved,allowcomments,mediumfilename,orginalfilename)
					VALUES ($catid, $usercatid, $filesize,'" . $extrafolder . $thumbname . "', '" . $extrafolder . $filename . "', $sizes[1], $sizes[0], '$title', '$description', " . $user_info['id'] . ",$t,$approved, 1,'" . $extrafolder . $mediumimage . "','$orginalfilename')");

			$gallery_pic_id = $smcFunc['db_insert_id']('{db_prefix}gallery_pic', 'id_picture');
			UpdateGallerySettings(array("last_bulk_pic_id" => $gallery_pic_id));

			// Get EXIF Data
			ProcessEXIFData($extrafolder . $filename, $gallery_pic_id, $exifData);

			Gallery_AddRelatedPicture($gallery_pic_id, $title);

			// Check for any custom fields
			if ($usercatid == 0)
			{
				$result = $smcFunc['db_query']('', "
				SELECT
					f.title, f.is_required, f.id_custom
				FROM {db_prefix}gallery_custom_field as f
				WHERE f.id_cat = " . $catid . " or f.id_cat = 0");
				while ($row2 = $smcFunc['db_fetch_assoc']($result))
				{
					if (isset($_REQUEST['cus_' . $row2['id_custom']][$n]))
					{
						$custom_data = $smcFunc['htmlspecialchars']($_REQUEST['cus_' . $row2['id_custom']][$n], ENT_QUOTES);

						$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_custom_field_data
						(id_picture, id_custom, value)
						VALUES('$gallery_pic_id', " . $row2['id_custom'] . ", '$custom_data')");
					}
				}
				$smcFunc['db_free_result']($result);
			}

			// If we are using multifolders get the next folder id
			if ($modSettings['gallery_set_enable_multifolder'])
				ComputeNextFolderID($gallery_pic_id);

			$good .= $_FILES['picture']['name'][$n] .'<br />';

			// Update Quota Information
			UpdateUserFileSizeTable($user_info['id'], $filesize);

			if (isset($rowcat['id_board'])  && $approved == 1)
			if ($usercatid == 0 && $rowcat['id_board'] != 0)
			{

				$extraheightwidth = '';
				if ($rowcat['postingsize'] == 1)
				{
					$postimg = $filename;
					$extraheightwidth = " height={$sizes[1]} width={$sizes[0]}";
				}
				else
					$postimg = $thumbname;

				if ($rowcat['showpostlink'] == 1)
					$showpostlink = "\n\n" . $scripturl . '?action=gallery;sa=view;id=' . $gallery_pic_id;
				else
					$showpostlink = '';
				// Create the post
				require_once($sourcedir . '/Subs-Post.php');
				$msgOptions = array(
					'id' => 0,
					'subject' => $title,
					'body' => '[b]' . $title . "[/b]\n\n[img$extraheightwidth]" . $modSettings['gallery_url']  . $extrafolder . $postimg . "[/img]$showpostlink\n\n$description",
					'icon' => 'xx',
					'smileys_enabled' => 1,
					'attachments' => array(),
				);
				$topicOptions = array(
					'id' => $rowcat['id_topic'],
					'board' => $rowcat['id_board'],
					'poll' => null,
					'lock_mode' => $rowcat['locktopic'],
					'sticky_mode' => null,
					'mark_as_read' => true,
				);
				$posterOptions = array(
					'id' => $user_info['id'],
					'update_post_count' => !$user_info['is_guest'],
				);
				// Fix height & width of posted image in message
				preparsecode($msgOptions['body']);

				createPost($msgOptions, $topicOptions, $posterOptions);

			require_once($sourcedir . '/Post.php');

			if (function_exists("notifyMembersBoard"))
            {
			$notifyData = array(
						'body' =>$msgOptions['body'],
						'subject' => $msgOptions['subject'],
						'name' => $user_info['name'],
						'poster' => $user_info['id'],
						'msg' => $msgOptions['id'],
						'board' =>  $rowcat['id_board'],
						'topic' => $topicOptions['id'],
					);
			notifyMembersBoard($notifyData);
                    }
                    else
                    {
                        // for 2.1
                        $smcFunc['db_insert']('',
                            '{db_prefix}background_tasks',
                            array('task_file' => 'string', 'task_class' => 'string', 'task_data' => 'string', 'claimed_time' => 'int'),
                            array('$sourcedir/tasks/CreatePost-Notify.php', 'CreatePost_Notify_Background', $smcFunc['json_encode'](array(
                                'msgOptions' => $msgOptions,
                                'topicOptions' => $topicOptions,
                                'posterOptions' => $posterOptions,
                                'type' => $topicOptions['id'] ? 'reply' : 'topic',
                            )), 0),
                            array('id_task')
                        );
                    }



				$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
					SET id_topic = " .$topicOptions['id'] . ", id_msg = " . $msgOptions['id'] . " WHERE id_picture = $gallery_pic_id
					");

			}

		// Update the SMF Shop Points
		if (isset($modSettings['shopVersion']))
			$smcFunc['db_query']('', "UPDATE {db_prefix}members
				SET money = money + " . $modSettings['gallery_shop_picadd'] . "
				WHERE id_member = " . $user_info['id'] . "
				LIMIT 1");




            if ($approved == 0)
            {
                $body = $txt['gallery_txt_itemwaitingapproval2'];
                $body = str_replace("%url",$scripturl . '?action=admin;area=gallery;sa=approvelist',$body);
                $body = str_replace("%title",$title,$body);

                Gallery_emailAdmins($txt['gallery_txt_itemwaitingapproval'],$body);
            }
            else
            {

				// Add Post Count
				 if (!empty($gallerySettings['gallery_set_picturepostcount']))
				 {
					if ($user_info['id'] != 0)
					{
						updateMemberData($user_info['id'], array('posts' => '+'));
					}
				 }

                if (isset($rowcat['id_board']))
                {
                    if ($rowcat['tweet_items'] == 1)
                        Gallery_TweetItem($title,$gallery_pic_id);
                }
            }

			// Last recheck Image if it was resized
			if ($image_resized == 1)
			{
				RecheckResizedImage($modSettings['gallery_path'] . $extrafolder . $filename,$gallery_pic_id,$filesize,$user_info['id']);
			}

			// Check for Watermark
			DoWaterMark($modSettings['gallery_path'] . $extrafolder . $filename);
		} // Upload Set
		else
			continue;

		if ($ajax == 1)
		{
			$n = 999;
			continue;

		}

	}

	UpdateMemberPictureTotals($user_info['id']);

 	// Badge Awards Mod Check
	GalleryCheckBadgeAwards($user_info['id']);

	if (empty($usercatid))
		Gallery_UpdateLatestCategory($catid);
	else
		Gallery_UpdateUserLatestCategory($usercatid);

	if (empty($usercatid))
		UpdateCategoryTotals($catid);
	else
		UpdateUserCategoryTotals($usercatid);


	if ($ajax ==1)
	{
	    ini_set("zlib.output_compression", "Off");
	    ob_start();



		ob_clean();

		if (!empty($errors))
		{
			header("HTTP/1.1 415 Not Acceptable");
			$return = array(
		'status' => '0',
		'error' => $errors
	);

		}

        if (isset($_REQUEST['response']) && $_REQUEST['response'] == 'xml')
        {
        	// header('Content-type: text/xml');

        	// Really dirty, use DOM and CDATA section!
        	echo '<response>';
        	foreach ($return as $key => $value) {
        		echo "<$key><![CDATA[$value]]></$key>";
        	}
        	echo '</response>';
        }
        else
        {
		  echo json_encode($return);
        }


		die("");
	}

	// Store the errors
	$context['bulk_errors'] = $errors;
	$context['bulk_good'] = $good;
	// Store Category ID
	$context['bulk_catid'] = $catid;
	$context['bulk_usercatid'] = $usercatid;

	$context['sub_template']  = 'bulk2';

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_bulk_results'];
}

function UpdateUserFileSizeTable($memberid, $filesize)
{
	global $smcFunc;

	if (empty($memberid))
		return;

	// Check if a record exists
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_member, totalfilesize
		FROM {db_prefix}gallery_userquota
		WHERE id_member = $memberid LIMIT 1");

	$count = $smcFunc['db_affected_rows']();
	$smcFunc['db_free_result']($dbresult);

	if ($count == 0)
	{
		//Create the record
		$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_userquota (id_member, totalfilesize) VALUES ($memberid, $filesize)");
	}
	else
	{
		//Update the record
		if ($filesize >= 0)
			$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_userquota SET totalfilesize = totalfilesize + $filesize WHERE id_member = $memberid LIMIT 1");
		else
			$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_userquota SET totalfilesize = totalfilesize + $filesize WHERE id_member = $memberid LIMIT 1");
	}
}

function FileSpaceAdmin()
{
	global $mbname, $txt, $context, $smcFunc, $scripturl;

	// Check if they are allowed to manage the gallery
	isAllowedTo('smfgallery_manage');

	loadLanguage('Admin');

	DoGalleryAdminTabs();



	// Show the member groups
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		q.totalfilesize,  q.id_group, m.group_name
	FROM {db_prefix}gallery_groupquota as q, {db_prefix}membergroups AS m
	WHERE  q.id_group = m.id_group ORDER BY q.totalfilesize");
    $context['gallery_file_membergroupslist'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
        $context['gallery_file_membergroupslist'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);
	// Show Regular members
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		q.totalfilesize, q.id_group
	FROM {db_prefix}gallery_groupquota as q
	WHERE q.id_group = 0 LIMIT 1");
     $context['gallery_file_regmem'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
        $context['gallery_file_regmem'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);


	// Get Total Pages
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) AS total
		FROM {db_prefix}gallery_userquota as q");

	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$numofrows = $row['total'];
	$smcFunc['db_free_result']($dbresult);

	$context['start'] = (int) $_REQUEST['start'];

	// List all members filespace usage
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			q.totalfilesize,  q.id_member, m.real_name
		FROM {db_prefix}gallery_userquota as q, {db_prefix}members AS m
		WHERE  q.id_member = m.id_member
		ORDER BY q.totalfilesize DESC
		LIMIT $context[start],20");

    $context['gallery_filespace_admin'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
        $context['gallery_filespace_admin'][] = $row;

	}
	$smcFunc['db_free_result']($dbresult);


    $context['page_index'] = constructPageIndex($scripturl . '?action=admin;area=gallery;sa=filespace', $_REQUEST['start'], $numofrows, 20);



	// Set the page tile
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_filespace'];
	// Load the subtemplate for the file manager
	$context['sub_template']  = 'filespace';

	// Load the membergroups
	$dbresult = $smcFunc['db_query']('', "SELECT id_group, group_name FROM {db_prefix}membergroups WHERE min_posts = -1 ORDER BY group_name");
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['groups'][$row['id_group']] = array(
			'id_group' => $row['id_group'],
			'group_name' => $row['group_name'],
			);
	}
	$smcFunc['db_free_result']($dbresult);
}

function FileSpaceList()
{
	global $mbname, $txt, $context, $smcFunc, $scripturl, $modSettings;
	// Check if they are allowed to manage the gallery
	isAllowedTo('smfgallery_manage');


	DoGalleryAdminTabs('filespace');


	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_user_selected']);

	$dbresult = $smcFunc['db_query']('', "
	SELECT
		m.real_name
	FROM {db_prefix}members AS m
	WHERE m.id_member = $id  LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$context['gallery_filelist_realname'] = $row['real_name'];
	$context['gallery_filelist_userid'] = $id;
	$smcFunc['db_free_result']($dbresult);


	// Get Total Pages
	$dbresult = $smcFunc['db_query']('', "SELECT COUNT(*) AS total FROM {db_prefix}gallery_pic WHERE id_member = " . $context['gallery_filelist_userid']);
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$numofrows = $row['total'];

	$smcFunc['db_free_result']($dbresult);

	$context['start'] = (int) $_REQUEST['start'];

	// List all user's pictures
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.thumbfilename, p.filesize,  p.id_member
		FROM {db_prefix}gallery_pic as p
		WHERE p.id_member = " . $context['gallery_filelist_userid'] . "
		ORDER BY p.filesize DESC  LIMIT $context[start],20");
    $context['gallery_user_filelist'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
        $context['gallery_user_filelist'][] = $row;

	}
	$smcFunc['db_free_result']($dbresult);

  	$context['page_index'] = constructPageIndex($scripturl . '?action=admin;area=gallery;sa=filelist;id=' . $context['gallery_filelist_userid'], $_REQUEST['start'], $numofrows, $modSettings['gallery_set_images_per_page']);


	// Set the page tile
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_filespace'] . ' - ' . $context['gallery_filelist_realname'];
	// Load the subtemplate for the file manager
	$context['sub_template']  = 'filelist';
}

function GenerateXML()
{
	global $txt, $smcFunc, $user_info, $context, $modSettings, $scripturl;
	//Accessed via ?action=gallery;sa=xml;cmd=cmdgoeshere
	//cmd listing
	//	recent
	//  cat	- parameters id optional will get category listing or id of category will get information on a category
	//  view   - parameters id required will get the information of a picture givin the picture id
	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']))
		@ob_start('ob_gzhandler');
	else
		ob_start();

	if (!$context['user']['is_guest'])
		$groupsdata = implode(',',$user_info['groups']);
	else
		$groupsdata = -1;

	$type = 'xml';

	if (isset($_REQUEST['type']))
		$type = $_REQUEST['type'];

	$limit = 10;

	if (isset($_REQUEST['limit']))
	{
		$limit = (int) $_REQUEST['limit'];
		if ($limit  > 100)
			$limit  = 100;
	}

	if (allowedTo('smfgallery_view'))
	{
		if (isset($_REQUEST['cmd']))
			$action = $_REQUEST['cmd'];
		else
			$action = 'recent';

		header("Content-Type: application/xml; charset=ISO-8859-1");

		if ($type == 'xml')
		{
			echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
			echo '<gallery>';
		}

		if ($type == 'rss')
		{
			echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
			echo '<rss version="2.0" xml:lang="en-US">
			<channel>';
		}


		switch($action)
		{
			case 'recent':
			$dbresult = $smcFunc['db_query']('', "
			SELECT
				p.id_picture, p.totalratings,p.height, p.width, p.keywords, p.commenttotal, p.totallikes, p.filename, p.filesize, p.views, p.thumbfilename, p.title, p.id_member, m.real_name, p.date, p.description
			FROM {db_prefix}gallery_pic as p
			LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
			LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
			LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.ID_GROUP IN ($groupsdata) AND c.ID_CAT = p.ID_CAT)

			WHERE ((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 AND p.approved =1) OR (p.approved =1 AND p.USER_ID_CAT =0 AND (c.view IS NULL OR c.view =1))
					GROUP by p.id_picture  ORDER BY p.id_picture DESC LIMIT $limit");
				while($row = $smcFunc['db_fetch_assoc']($dbresult))
				{


					if ($type == 'rss')
					{
						echo '<item>
						<title><![CDATA[', $row['title'], ']]></title>
						<pubDate>',gmdate('D, d M Y H:i:s \G\M\T', $row['date']),'</pubDate>
						<description>
						<![CDATA[<img src="' . $modSettings['gallery_url'] . $row['thumbfilename'] . '" border="0" align="left" alt="" title="" />',$row['description'],']]>
						</description>
						<link>', $scripturl, '?action=gallery;sa=view;id=',$row['id_picture'],'</link>

						</item>';
					}


					if ($type == 'xml')
					{
						echo '<picture>';
							echo '<id>' . $row['id_picture'] . '</id>';
							echo '<title>' . $row['title'] . '</title>';
							echo '<description>' . $row['description'] . '</description>';
							echo '<views>' . $row['views'] . '</views>';
							echo '<filesize>' . $row['filesize'] . '</filesize>';
							echo '<keywords>' . $row['keywords'] . '</keywords>';
							echo '<filename>' . $modSettings['gallery_url'] . $row['filename']  . '</filename>';
							echo '<thumbfilename>' . $modSettings['gallery_url'] . $row['thumbfilename']  . '</thumbfilename>';
							echo '<commenttotal>' . $row['commenttotal'] . '</commenttotal>';
							echo '<date>' . $row['date'] . '</date>';
							echo '<membername>' . $row['real_name'] . '</membername>';
							echo '<memberid>' . $row['id_member'] . '</memberid>';
							echo '<totalratings>' . $row['totalratings'] . '</totalratings>';
							echo '<height>' . $row['height'] . '</height>';
							echo '<width>' . $row['width'] . '</width>';
						echo '</picture>';
					}
				}

			$smcFunc['db_free_result']($dbresult);
			break;

			case 'cat':
				if (isset($_REQUEST['id']))
				{
					$id = (int) $_REQUEST['id'];
					$query = " WHERE id_cat = $id";
				}
				else
				{
					$query = '';
				}

				$dbresult = $smcFunc['db_query']('', "
				SELECT
					id_cat, title, id_parent, roworder, description, image
				FROM {db_prefix}gallery_cat " . $query);
				while($row = $smcFunc['db_fetch_assoc']($dbresult))
				{
					echo '<category>';
						echo '<id>' . $row['id_cat'] . '</id>';
						echo '<title>' . $row['title'] . '</title>';
						echo '<description>' . $row['description'] . '</description>';
						echo '<parent>' . $row['id_parent'] . '</parent>';
						echo '<roworder>' . $row['roworder'] . '</roworder>';
						echo '<image>' . $row['image'] . '</image>';
					echo '</category>';
				}

			$smcFunc['db_free_result']($dbresult);

			break;

			case 'view':
				if (isset($_REQUEST['id']))
				{
					$id = (int) $_REQUEST['id'];

					$dbresult = $smcFunc['db_query']('', "
					SELECT
						p.id_picture, p.totalratings,p.height, p.width, p.keywords, p.commenttotal, p.totallikes, p.filename, p.filesize, p.views, p.thumbfilename, p.title, p.id_member, m.real_name, p.date, p.description
					FROM {db_prefix}gallery_pic as p
					LEFT JOIN {db_prefix}members AS m on (p.id_member = m.id_member)
					LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
					LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.ID_GROUP IN ($groupsdata) AND c.ID_CAT = p.ID_CAT)


					WHERE p.id_picture = $id AND ((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 AND p.approved =1) OR (p.approved =1 AND p.USER_ID_CAT =0 AND (c.view IS NULL OR c.view =1))
					GROUP by p.id_picture  LIMIT 1");
					$row = $smcFunc['db_fetch_assoc']($dbresult);

					if ($type == 'rss')
					{
					echo '<item>
					<title><![CDATA[', $row['title'], ']]></title>
					<pubDate>',gmdate('D, d M Y H:i:s \G\M\T', $row['date']),'</pubDate>
					<description>
					<![CDATA[<img src="' . $modSettings['gallery_url'] . $row['thumbfilename'] . '" border="0" align="left" alt="" title="" />',$row['description'],']]>
					</description>
					<link>', $scripturl, '?action=gallery;sa=view;id=',$row['id_picture'],'</link>

					</item>';
					}


					if ($type == 'xml')
					{
						echo '<picture>';
							echo '<id>' . $row['id_picture'] . '</id>';
							echo '<title>' . $row['title'] . '</title>';
							echo '<description>' . $row['description'] . '</description>';
							echo '<views>' . $row['views'] . '</views>';
							echo '<filesize>' . $row['filesize'] . '</filesize>';
							echo '<keywords>' . $row['keywords'] . '</keywords>';
							echo '<filename>' . $modSettings['gallery_url'] . $row['filename']  . '</filename>';
							echo '<thumbfilename>' . $modSettings['gallery_url'] . $row['thumbfilename']  . '</thumbfilename>';
							echo '<commenttotal>' . $row['commenttotal'] . '</commenttotal>';
							echo '<date>' . $row['date'] . '</date>';
							echo '<membername>' . $row['real_name'] . '</membername>';
							echo '<memberid>' . $row['id_member'] . '</memberid>';
							echo '<totalratings>' . $row['totalratings'] . '</totalratings>';
							echo '<height>' . $row['height'] . '</height>';
							echo '<width>' . $row['width'] . '</width>';
						echo '</picture>';
					}

					$smcFunc['db_free_result']($dbresult);
				}
				else
				{
					echo '<gallery>';
					echo '<galleryerror>' . $txt['gallery_error_no_pic_selected'] . '</galleryerror>';
					echo '</gallery>';
				}
			break;
		}

		if ($type == 'xml')
		{
			echo '</gallery>';
		}

		if ($type == 'rss')
		{
			echo '</channel>';
			echo '</rss>';
		}

		obExit(false);
		die();
	}
	else
	{
		// Show them an empty xml file with the error
		header("Content-Type: application/xml; charset=ISO-8859-1");
		echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
		echo '<gallery>';
		echo '<galleryerror>' . $txt['cannot_smfgallery_view'] . '</galleryerror>';
		echo '</gallery>';

			obExit(false);
		die();
	}
}

function RecountFileQuotaTotals($redirect = true)
{
	global $smcFunc;

	if ($redirect == true)
		isAllowedTo('smfgallery_manage');

	// Show all the user's with quota information
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		id_member
	FROM {db_prefix}gallery_userquota");
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		// Loop though the all the pictures for the member and get the total
		$dbresult2 = $smcFunc['db_query']('', "SELECT SUM(filesize) as total FROM {db_prefix}gallery_pic WHERE id_member = " . $row['id_member']);

		$row2 = $smcFunc['db_fetch_assoc']($dbresult2);
		$total = $row2['total'];

		if ($total == '')
			$total = 0;

		$smcFunc['db_free_result']($dbresult2);
		// Update the quota
		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_userquota SET totalfilesize = $total WHERE id_member = " . $row['id_member'] . " LIMIT 1");
	}
	$smcFunc['db_free_result']($dbresult);

	if ($redirect == true)
		redirectexit('action=admin;area=gallery;sa=filespace');
}

function GetQuotaGroupLimit($memberid)
{
	global $smcFunc;
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			m.id_member, q.id_group, q.totalfilesize
		FROM {db_prefix}gallery_groupquota as q, {db_prefix}members as m
		WHERE m.id_member = $memberid AND q.id_group = m.id_group LIMIT 1");

	$row = $smcFunc['db_fetch_assoc']($dbresult);
	if ($smcFunc['db_affected_rows']()== 0)
	{
		$smcFunc['db_free_result']($dbresult);
		return 0;
	}
	else
	{
		$smcFunc['db_free_result']($dbresult);

		return $row['totalfilesize'];
	}
}

function GetUserSpaceUsed($memberid)
{
	global $smcFunc;

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_member,totalfilesize
		FROM {db_prefix}gallery_userquota
		WHERE id_member = $memberid LIMIT 1");

	$row = $smcFunc['db_fetch_assoc']($dbresult);
	if ($smcFunc['db_affected_rows']()== 0)
	{
		$smcFunc['db_free_result']($dbresult);
		return 0;
	}
	else
	{
		$smcFunc['db_free_result']($dbresult);

		return $row['totalfilesize'];
	}
}

function AddQuota()
{
	global $txt, $smcFunc;
	isAllowedTo('smfgallery_manage');

	$groupid = (int) $_REQUEST['groupname'];


	$filelimit = (double) $_REQUEST['filelimit'];
	if (empty($filelimit))
		fatal_error($txt['gallery_error_noquota'],false);

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_group
		FROM {db_prefix}gallery_groupquota
		WHERE id_group = $groupid LIMIT 1");

	$count = $smcFunc['db_affected_rows']();
	$smcFunc['db_free_result']($dbresult);

	if ($count == 0)
	{
		// Create the record
		$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_groupquota (id_group, totalfilesize) VALUES ($groupid, $filelimit)");
	}
	else
	{
		fatal_error($txt['gallery_error_quotaexist'],false);
	}

	redirectexit('action=admin;area=gallery;sa=filespace');
}

function DeleteQuota()
{
	global $smcFunc;
	isAllowedTo('smfgallery_manage');
	$id = (int) $_REQUEST['id'];

	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_groupquota WHERE id_group = " . $id . ' LIMIT 1');

	redirectexit('action=admin;area=gallery;sa=filespace');
}

function DoImageResize($sizes,$destName)
{
	global $modSettings, $gd2, $sourcedir;

	@ini_set('memory_limit', '512M');

	$default_formats = array(
		'1' => 'gif',
		'2' => 'jpeg',
		'3' => 'png',
		'6' => 'bmp',
		'15' => 'wbmp'
	);

	// Gif? That might mean trouble if gif support is not available.
	if ($sizes[2] == 1 && !function_exists('imagecreatefromgif') && function_exists('imagecreatefrompng'))
	{
		// Download it to the temporary file... use the special gif library... and save as png.
		if ($img = @gif_loadFile($destName) && gif_outputAsPng($img, $destName))
			$sizes[2] = 3;
	}

	// A known and supported format?
	if (isset($default_formats[$sizes[2]]) && function_exists('imagecreatefrom' . $default_formats[$sizes[2]]))
	{

		$imagecreatefrom = 'imagecreatefrom' . $default_formats[$sizes[2]];
		if ($src_img = $imagecreatefrom($destName))
		{
		  // if gif
		   if ($sizes[2] == 1)
           {
           	 $modSettings['enableErrorLogging'] = 0;
             require_once($sourcedir . '/class.gifresize.php');
             $nGif = new GIF_eXG($destName,1);
             $nGif->resize($destName,$modSettings['gallery_max_width'],$modSettings['gallery_max_height'],1,1);
             $modSettings['enableErrorLogging'] = 1;
           }
            else
			 resizeImage($src_img, $destName, imagesx($src_img), imagesy($src_img), $modSettings['gallery_max_width'], $modSettings['gallery_max_height']);
		}
	}
}

function RecheckResizedImage($filename,$id,$oldfilesize,$memid)
{
	global $smcFunc;

	$oldfilesize = $oldfilesize * -1;
	UpdateUserFileSizeTable($memid,$oldfilesize);

	//Get the height and width
	$sizes = @getimagesize($filename);

	//Get the size of the image
	$filesize = filesize($filename);

	// Update database
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic SET filesize = $filesize, height = $sizes[1], width = $sizes[0]  WHERE id_picture = $id LIMIT 1");
	// Update Quota
	UpdateUserFileSizeTable($memid,$filesize);
}

function ApproveAllComments()
{
	global $smcFunc;

	isAllowedTo('smfgallery_manage');

	// Approve all the comments
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_comment
		SET approved = 1 WHERE approved = 0");

	// Redirect the comment list
	redirectexit('action=admin;area=gallery;sa=commentlist');
}

function CatPerm()
{
	global $mbname, $txt, $smcFunc, $context;

	isAllowedTo('smfgallery_manage');


	$cat = (int) $_REQUEST['cat'];
	if (empty($cat))
		fatal_error($txt['gallery_error_no_cat']);

	$dbresult1 = $smcFunc['db_query']('', "
	SELECT
		id_cat, title
	FROM {db_prefix}gallery_cat
	WHERE id_cat = $cat LIMIT 1");
	$row1 = $smcFunc['db_fetch_assoc']($dbresult1);
	$context['gallery_cat_name'] = $row1['title'];
	$smcFunc['db_free_result']($dbresult1);

	loadLanguage('Admin');

	$context['gallery_cat_id'] = $cat;


	// Load the membergroups
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_group, group_name
		FROM {db_prefix}membergroups
		ORDER BY group_name");

	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['groups'][$row['id_group']] = array(
			'id_group' => $row['id_group'],
			'group_name' => $row['group_name'],
			);
	}
	$smcFunc['db_free_result']($dbresult);


	$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.title, c.roworder, c.id_parent
		FROM {db_prefix}gallery_cat AS c
		WHERE c.redirect = 0
		ORDER BY c.title ASC");

	$context['gallery_cat'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_cat'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);
	CreateGalleryPrettyCategory();



	// Show the member groups
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.id, c.view, c.addpic, c.editpic, c.delpic, c.addcomment,  c.addvideo,  c.id_group, c.viewimagedetail, c.autoapprove, m.group_name, a.title catname
		FROM ({db_prefix}gallery_catperm as c, {db_prefix}membergroups AS m, {db_prefix}gallery_cat as a)
		WHERE c.id_cat = " . $context['gallery_cat_id'] . " AND c.id_group = m.id_group AND a.id_cat = c.id_cat");
	$context['catperm_memgroups'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
        $context['catperm_memgroups'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	// Show Regular members/Guests
	$dbresult = $smcFunc['db_query']('', "
		SELECT c.id_cat, c.id, c.view, c.addpic, c.editpic, c.delpic, c.addcomment,  c.addvideo, c.viewimagedetail, c.autoapprove, c.id_group, a.title catname
		FROM {db_prefix}gallery_catperm as c,{db_prefix}gallery_cat as a WHERE c.id_cat = " . $context['gallery_cat_id'] . " AND c.id_group IN (0,-1) AND a.id_cat = c.id_cat");
    $context['catperm_memgroups_guests'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
      $context['catperm_memgroups_guests'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);



	// Load the template
	$context['sub_template']  = 'catperm';
	// Set the page title
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_catperm'] . ' -' . $context['gallery_cat_name'];

}

function CatPerm2()
{
	global $smcFunc, $txt;

	isAllowedTo('smfgallery_manage');

	$groupname = (int) $_REQUEST['groupname'];
	$cat = (int) $_REQUEST['cat'];

	// Check if permission exits
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		id_group,id_cat
	FROM {db_prefix}gallery_catperm
	WHERE id_group = $groupname AND id_cat = $cat");
	if ($smcFunc['db_affected_rows']()!= 0)
	{
		$smcFunc['db_free_result']($dbresult);
		fatal_error($txt['gallery_permerr_permexist'],false);
	}
	$smcFunc['db_free_result']($dbresult);

	// Permissions
	$view = isset($_REQUEST['view']) ? 1 : 0;
	$add = isset($_REQUEST['add']) ? 1 : 0;
	$edit = isset($_REQUEST['edit']) ? 1 : 0;
	$delete = isset($_REQUEST['delete']) ? 1 : 0;
	$addcomment = isset($_REQUEST['addcomment']) ? 1 : 0;
	$addvideo = isset($_REQUEST['addvideo']) ? 1 : 0;
    $viewimagedetail = isset($_REQUEST['viewimagedetail']) ? 1 : 0;
    $autoapprove = isset($_REQUEST['autoapprove']) ? 1 : 0;

	// Insert into database
	$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_catperm
			(id_group,id_cat,view,addpic,editpic,delpic,addcomment,addvideo,viewimagedetail,autoapprove)
		VALUES ($groupname,$cat,$view,$add,$edit,$delete,$addcomment, $addvideo,$viewimagedetail,$autoapprove)");

	redirectexit('action=gallery;sa=catperm;cat=' . $cat);
}

function CatPermList()
{
	global $mbname, $txt, $context, $smcFunc;

	isAllowedTo('smfgallery_manage');

	DoGalleryAdminTabs();


	$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.id, c.view, c.addpic, c.editpic, c.delpic, c.addcomment,
			 c.addvideo,
			c.id_group, m.group_name, a.title catname
		FROM ({db_prefix}gallery_catperm as c, {db_prefix}membergroups AS m,{db_prefix}gallery_cat as a)
		WHERE  c.id_group = m.id_group AND a.id_cat = c.id_cat");

	// Show the member groups
    $context['gallery_catperm_memlist'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
        $context['gallery_catperm_memlist'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	// Show Regular members/guests
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.id, c.view, c.addpic, c.editpic, c.delpic, c.addcomment,  c.addvideo,
			 c.id_group, a.title catname
		FROM {db_prefix}gallery_catperm as c,{db_prefix}gallery_cat as a
		WHERE  c.id_group IN (0,-1) AND a.id_cat = c.id_cat");
    $context['gallery_catperm_memguests'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
        $context['gallery_catperm_memguests'][] = $row;
	}


	// Load the template
	$context['sub_template']  = 'catpermlist';

	// Set the page title
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_catpermlist'];
}

function CatPermDelete()
{
	global $smcFunc;
	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];


	// Get the category id
		$result = $smcFunc['db_query']('',"
		SELECT
			ID_CAT
		FROM {db_prefix}gallery_catperm
		WHERE ID = $id
		");
	$catRow = $smcFunc['db_fetch_assoc']($result);

	// Delete the Permission
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_catperm WHERE id = " . $id . ' LIMIT 1');


	// Redirect to the category permission page
	redirectexit('action=gallery;sa=catperm;cat=' . $catRow['ID_CAT']);
}

function GetCatPermission($cat,$perm, $return = false, $checkpostGroup = false, $checkAdditonal = true, $groupID = 0)
{
	global $smcFunc, $txt, $user_info, $user_settings;
	$cat = (int) $cat;
	$foundFalse = false;
	$foundFalseGroup = 0;
    $checkAdditonal = false;
    $checkpostGroup  = false;
   // echo "GetCatPermission($cat,$perm, $return = false, $checkpostGroup = true, $checkAdditonal = true, $groupID = 0)";

	if (!$user_info['is_guest'])
	{

		// Handle Additional Groups
		//print_R($user_info['groups']);
		if ($checkAdditonal  == true )
		{
			$additionalGroupsUser =  $user_info['groups'];
			if (count($additionalGroupsUser) > 0)
			{

				if ($user_settings['additional_groups'] == '')
				{
					$myReturn = GetCatPermission($cat, $perm,true, false,false, 0);
					if ($myReturn == false)
					{
						$foundFalse = true;
						$foundFalseGroup = 0;
					}
				}

				if ($foundFalse == false)
				foreach($additionalGroupsUser as $mygroup)
				{
					if (empty($mygroup))
						continue;

					if (GetCatPermission($cat, $perm,true, false,false, $mygroup) == true)
					{
						return true;
					}
					else
					{
						$foundFalse = true;
						$foundFalseGroup = $mygroup;
					}
				}
			}
		}

		if (!empty($groupID))
		{

			$dbresult = $smcFunc['db_query']('', "
			SELECT
				c.view, c.addpic, c.editpic, c.delpic, c.ratepic, c.addcomment,
				c.editcomment, c.report, c.addvideo, c.viewimagedetail, c.autoapprove
			FROM {db_prefix}gallery_catperm as c
			WHERE c.id_group = $groupID AND c.id_cat = $cat LIMIT 1");
		}
		else
		{
		$dbresult = $smcFunc['db_query']('', "
			SELECT
				m.id_member, c.view, c.addpic, c.editpic, c.delpic,c.ratepic, c.addcomment,
				c.editcomment, c.report, c.addvideo, c.viewimagedetail, c.autoapprove
			FROM {db_prefix}gallery_catperm as c, {db_prefix}members as m WHERE m.id_member = " . $user_info['id'] . " AND c.id_group = m.id_group AND c.id_cat = $cat LIMIT 1");

		}



	}
	else
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.view, c.addpic, c.editpic, c.delpic,c.ratepic, c.addcomment,
			c.editcomment, c.report, c.addvideo, c.viewimagedetail, c.autoapprove
             FROM {db_prefix}gallery_catperm as c
		WHERE c.id_group = -1 AND c.id_cat = $cat LIMIT 1");


	if ($smcFunc['db_affected_rows']()== 0)
	{
		$smcFunc['db_free_result']($dbresult);


		if ($user_info['is_guest'] == false)
		{
			if ($foundFalse == true)
			{
				$result = GetCatPermission($cat, $perm, $return, false,false,$foundFalseGroup);
				if ($return == true)
					return $result;
			}


		}


		if ($return == true)
        {
          //  echo "RETURNED2: TRUE $perm";
			return true;
		}
	}
	else
	{
		$row = $smcFunc['db_fetch_assoc']($dbresult);
       // print "Permisison: $perm   Return type: " . ($return == false ? 'false' : 'true');
      //  print_r($row);

		$smcFunc['db_free_result']($dbresult);
		if ($perm == 'view' && $row['view'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_view'],false);
			else
				return false;
		}
		else if ($perm == 'addpic' && $row['addpic'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_add'],false);
			else
				return false;
		}
		else if ($perm == 'editpic' && $row['editpic'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_edit'],false);
            else
				return false;
		}
		else if ($perm == 'delpic' && $row['delpic'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_delete'],false);
			else
				return false;
		}
		else if ($perm == 'ratepic' && $row['ratepic'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_ratepic'],false);
			else
				return false;
		}
		else if ($perm == 'addcomment' && $row['addcomment'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_addcomment'],false);
			else
				return false;
		}
		else if ($perm == 'editcomment' && $row['editcomment'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_editcomment'],false);
			else
				return false;
		}
		else if ($perm == 'report' && $row['report'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_report'],false);
			else
				return false;
		}
		else if ($perm == 'addvideo' && $row['addvideo'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_addvideo'],false);
			else
				return false;
		}
		else if ($perm == 'viewimagedetail' && $row['viewimagedetail'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_viewimagedetail'],false);
			else
				return false;
		}
		else if ($perm == 'autoapprove' && $row['autoapprove'] == 0)
		{
			if ($return == false)
				fatal_error($txt['gallery_perm_no_autoapprove'],false);
			else
				return false;
		}
		else if ($perm == 'autoapprove' && $row['autoapprove'] == 2)
		{
				return 2;
		}



		if ($return == true)
        {
           // print_R($row);
          //  echo "RETURNED: TRUE $perm";
			return true;
		}
	}
}

function PreviousImage($id = 0, $picCat = 0, $userCat = 0, $return = false)
{
	global $smcFunc, $txt;

	if (empty($id))
		$id = (int) $_REQUEST['id'];

	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected'],false);

    $ordercat = 'ASC';

	// Get the category
	if (empty($picCat) && empty($userCat))
	{
		$dbresult = $smcFunc['db_query']('', "
			SELECT
				c.sortby, c.orderby, p.id_picture, p.id_cat, p.user_id_cat
			FROM {db_prefix}gallery_pic as p
			LEFT JOIN {db_prefix}gallery_cat as c ON (p.id_cat = c.id_cat)
			WHERE p.id_picture = $id LIMIT 1");

		if ($smcFunc['db_num_rows']($dbresult) == 0)
			fatal_error($txt['gallery_error_no_pic_selected'],false);

		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$id_cat = $row['id_cat'];
		$user_id_cat = $row['user_id_cat'];

		if (!empty($row['sortby']))
		{
			$sortcat = $row['sortby'];
			//$ordercat = $row['orderby'];
		}

		$smcFunc['db_free_result']($dbresult);
	}
	else
	{
		$id_cat = $picCat;
		$user_id_cat = $userCat;
	}

	if (empty($sortcat))
	{
	//if ($sortcat == '')
		$sortcat = 'p.id_picture';

	//if ($ordercat == '')
		$ordercat = 'ASC';
	}

	$ordersign = '>';
	/*
	if ($ordercat == 'ASC')
		$ordersign = '>';
	else
		$ordersign = '<';
	*/

	// Get previous image
	// $dbresult = $smcFunc['db_query']('', "SELECT id_picture FROM {db_prefix}gallery_pic WHERE id_cat = $id_cat AND user_id_cat = $user_id_cat AND approved = 1 AND id_picture > $id  LIMIT 1");
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture
		FROM {db_prefix}gallery_pic as p
		WHERE p.id_cat = $id_cat AND p.user_id_cat = $user_id_cat AND p.approved = 1 AND p.id_picture $ordersign $id
		ORDER BY $sortcat $ordercat LIMIT 1");
	if ($smcFunc['db_affected_rows']() != 0)
	{
		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$id_picture = $row['id_picture'];
	}
	else
		$id_picture = $id;

	$smcFunc['db_free_result']($dbresult);

	if ($return == false)
		redirectexit('action=gallery;sa=view;id=' . $id_picture);
	else
		return $id_picture;
}

function NextImage($id = 0, $picCat = 0, $userCat = 0, $return = false)
{
	global $smcFunc, $txt;

	if (empty($id))
		$id = (int) $_REQUEST['id'];

	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected'],false);

    $ordercat = 'DESC';
	// Get the category
	if (empty($picCat) && empty($userCat))
	{
		$dbresult = $smcFunc['db_query']('', "
			SELECT
				c.sortby, c.orderby, p.id_picture, p.id_cat, p.user_id_cat
			FROM {db_prefix}gallery_pic as p
			LEFT JOIN {db_prefix}gallery_cat as c ON (p.id_cat = c.id_cat)
			WHERE p.id_picture = $id  LIMIT 1");

		if ($smcFunc['db_num_rows']($dbresult) == 0)
			fatal_error($txt['gallery_error_no_pic_selected'],false);

		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$id_cat = $row['id_cat'];
		$user_id_cat = $row['user_id_cat'];
		$ordercat = $row['orderby'];

		if (!empty($row['sortby']))
		{
			$sortcat = $row['sortby'];
			//$ordercat = $row['orderby'];
		}

		$smcFunc['db_free_result']($dbresult);
	}
	else
	{
		$id_cat = $picCat;
		$user_id_cat = $userCat;
	}

	//if ($sortcat == '')
	if (empty($sortcat))
	{
		$sortcat = 'p.id_picture';

	//if ($ordercat == '')
		$ordercat = 'DESC';
	}
	$ordersign = '<';
	/*
	if ($ordercat == 'ASC')
	{
		$ordersign = '>';
	}

	if ($ordercat == 'DESC')
		$ordersign = '<';
	else
		$ordersign = '>';
	*/

	// Get next image
	//SELECT id_picture FROM {db_prefix}gallery_pic WHERE id_cat = $id_cat AND user_id_cat = $user_id_cat AND approved = 1 AND id_picture < $id ORDER BY id_picture DESC LIMIT 1
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture
		FROM {db_prefix}gallery_pic as p
		WHERE p.id_cat = $id_cat AND  p.user_id_cat = $user_id_cat AND p.approved = 1 AND p.id_picture $ordersign $id
		ORDER BY $sortcat $ordercat LIMIT 1");

	if ($smcFunc['db_affected_rows']() != 0)
	{
		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$id_picture = $row['id_picture'];
	}
	else
		$id_picture = $id;
	$smcFunc['db_free_result']($dbresult);

	if ($return == false)
		redirectexit('action=gallery;sa=view;id=' . $id_picture);
	else
		return $id_picture;
}

function CatImageDelete()
{
	global $smcFunc;

	isAllowedTo('smfgallery_manage');

	@$id = (int) $_REQUEST['id'];
	if (empty($id))
		exit;

		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat
		SET filename = '' WHERE id_cat = $id LIMIT 1");

	redirectexit('action=gallery;sa=editcat;cat=' . $id);
}

function ReOrderCats($cat)
{
	global $smcFunc;

	$dbresult1 = $smcFunc['db_query']('', "SELECT id_cat,roworder,id_parent FROM {db_prefix}gallery_cat WHERE id_cat = $cat");
	$row = $smcFunc['db_fetch_assoc']($dbresult1);
	$id_parent = $row['id_parent'];
	$smcFunc['db_free_result']($dbresult1);

	if (empty($row['id_cat']))
		return;

	$dbresult = $smcFunc['db_query']('', "SELECT id_cat, roworder FROM {db_prefix}gallery_cat WHERE id_parent = $id_parent ORDER BY roworder ASC");
	if ($smcFunc['db_affected_rows']() != 0)
	{
		$count = 1;
		while($row2 = $smcFunc['db_fetch_assoc']($dbresult))
		{
			$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat
			SET roworder = $count WHERE id_cat = " . $row2['id_cat']);
			$count++;
		}
	}
	$smcFunc['db_free_result']($dbresult);
}

function ReGenerateThumbnails()
{
	global $context, $mbname, $txt, $smcFunc;

    if (isset($_REQUEST['cat']))
	   $cat = (int) $_REQUEST['cat'];
    else
        $cat = 0;

	$usercat = isset($_REQUEST['usercat']) ? 1 : 0;

	if (empty($cat) && empty($usercat))
		fatal_error($txt['gallery_error_no_cat']);

	isAllowedTo('smfgallery_manage');

	// Get the category name
	if (empty($usercat))
	{
		$dbresult1 = $smcFunc['db_query']('', "
			SELECT
				title
			FROM {db_prefix}gallery_cat
			WHERE id_cat = $cat");

		$row = $smcFunc['db_fetch_assoc']($dbresult1);
		$context['gallery_cat_name'] = $row['title'];
		$smcFunc['db_free_result']($dbresult1);


		$context['catid'] = $cat;
		$context['usercat'] = 0;
	}
	else
	{
		$context['catid'] = 0;
		$context['usercat'] = 1;

		$context['gallery_cat_name'] =  $txt['gallery_user_index'];
	}


	$context['sub_template']  = 'regenerate';
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_regeneratethumbnails2'];

    $context['linktree'][] = array(
			'name' => $txt['gallery_text_regeneratethumbnails2']
		);
}

function ReGenerateThumbnails2()
{
	global $smcFunc, $txt, $modSettings, $gd2, $sourcedir, $context;

	$id = (int) $_REQUEST['id'];
	$usercat = (int) $_REQUEST['usercat'];

	if (empty($id) && empty($usercat))
		return;

	isAllowedTo('smfgallery_manage');
	$catWhere = '';
	if (empty($usercat))
	{
		$context['catid'] = $id;
		$context['usercat'] = 0;
		$catWhere = " ID_CAT = $id";
	}
	else
	{
		$context['catid'] = 0;
		$context['usercat'] = 1;
		$catWhere = " USER_ID_CAT <> 0";
	}



	// Check if gallery path is writable
	if (!is_writable($modSettings['gallery_path']))
		fatal_error($txt['gallery_write_error'] . $modSettings['gallery_path']);

	// Increase the max time to process the images
	@ini_set('max_execution_time', '900');

	$testGD = get_extension_funcs('gd');
	$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
	unset($testGD);

	require_once($sourcedir . '/Subs-Graphics.php');

	$regenmedium = isset($_REQUEST['regenmedium']) ? 1 : 0;
	if (isset($_REQUEST['gallery_regenmedium']))
		$regenmedium = (int) $_REQUEST['gallery_regenmedium'];

	$context['gallery_regenmedium'] = $regenmedium;

	$context['start'] = empty($_REQUEST['start']) ? 25 : (int) $_REQUEST['start'];

	$request = $smcFunc['db_query']('', "
	SELECT
		COUNT(*)
	FROM {db_prefix}gallery_pic
	WHERE $catWhere");
	list($totalProcess) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	// Initialize the variables.
	$increment = 25;
	if (empty($_REQUEST['start']))
		$_REQUEST['start'] = 0;


	$dbresult = $smcFunc['db_query']('', "
		SELECT
			filename, id_picture
		FROM {db_prefix}gallery_pic
		WHERE $catWhere LIMIT " . $_REQUEST['start'] . ","  . ($increment));
	$counter = 0;
	$gallery_pics = array();
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$gallery_pics[] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	foreach($gallery_pics as $row)
	{
		$filename = $row['filename'];
		$extra_path = '';
		if ($modSettings['gallery_set_enable_multifolder'])
		{
			$tmp = explode('/',$filename);

			if (!empty($tmp[1]))
			{
				$tmpTotal = 0;
				foreach($tmp as $mytmp)
				{
					$tmpTotal++;

					if ($tmpTotal == count($tmp))
					{
						$filename = $mytmp;
					}
					else
						$extra_path .=  $mytmp .  '/';
				}

			}
		}

		$mediumimagePath = '';
		$thumbnailPath = '';

		GalleryCreateThumbnail($modSettings['gallery_path'] . $extra_path .  $filename, $modSettings['gallery_thumb_width'], $modSettings['gallery_thumb_height']);
		unlink($modSettings['gallery_path'] . $extra_path . 'thumb_' . $filename);
		rename($modSettings['gallery_path'] . $extra_path .  $filename . '_thumb',  $modSettings['gallery_path']  . $extra_path . 'thumb_' . $filename);
		@chmod($modSettings['gallery_path'] . $extra_path  .  'thumb_' . $filename, 0755);
		$thumbnailPath = $extra_path  .  'thumb_' . $filename;

		if ($regenmedium == true)
		{
			// Medium Image
				if ($modSettings['gallery_make_medium'])
				{
					@unlink($modSettings['gallery_path'] . $extra_path . 'medium_' . $filename);
					GalleryCreateThumbnail($modSettings['gallery_path'] .  $extra_path .  $filename, $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
					rename($modSettings['gallery_path'] . $extra_path .  $filename . '_thumb',  $modSettings['gallery_path'] . $extra_path.  'medium_' . $filename);

					@chmod($modSettings['gallery_path'] . $extra_path .  'medium_' . $filename, 0755);
					$mediumimagePath = $extra_path .  'medium_' . $filename;

					// Check for Watermark
					DoWaterMark($modSettings['gallery_path'] . $extra_path .  'medium_' .  $filename);

						$smcFunc['db_query']('', "
					UPDATE {db_prefix}gallery_pic SET thumbfilename = '$thumbnailPath',
					mediumfilename = '$mediumimagePath'
					WHERE id_picture = " . $row['id_picture']);
					}
		}
		else
		{
			$smcFunc['db_query']('', "
			UPDATE {db_prefix}gallery_pic SET thumbfilename = '$thumbnailPath'
			WHERE id_picture = " . $row['id_picture']);
		}

		$counter++;

	}

	$_REQUEST['start'] += $increment;

	$complete = 0;
	if ($_REQUEST['start'] < $totalProcess)
	{

		$context['continue_get_data'] = 'start=' . $_REQUEST['start'];
		$context['continue_percent'] = round(100 * $_REQUEST['start'] / $totalProcess);


	}
	else
		$complete = 1;

	// Redirect back to the category
	if ($complete == 1)
		redirectexit('action=gallery;cat=' .  $id);
	else
	{
		$context['sub_template']  = 'regenerate2';

		$context['page_title'] =  $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_regeneratethumbnails2'];

	}

}

function DoWaterMark($filename)
{
	global $modSettings, $sourcedir, $gd2;

    if (empty($modSettings['gallery_set_water_enabled']))
        return;

    $finalFilename = $filename;

	if (!file_exists($filename . '.org'))
    {
        copy($filename,$filename . '.org');
    }


   $filename = $filename . '.org';



	require_once($sourcedir . '/Subs-Graphics.php');
	$testGD = get_extension_funcs('gd');
	$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
	unset($testGD);

	// Image/Media type extensions
	$extensions = array('1' => 'gif', '2' => 'jpeg','3' => 'png','4' => 'swf','5' => 'psd',
	'6' => 'bmp','7' => 'tiff','8' => 'tiff','9' => 'jpc','10' => 'jp2', '11' => 'jpx',
	'12' => 'jb2','13' => 'swc','14' => 'iff','15' => 'wbmp','16' => 'xbm', '18' => 'webp');

	$sizes = @getimagesize($filename);

	$image_width = $sizes[0];
	$image_height = $sizes[1];
	$image = '';

	if ($extensions[$sizes[2]] == 'gif' && !function_exists('imagecreatefromgif') && function_exists('imagecreatefrompng'))
	{
		// Use SMF functions by Yamasoft for loading gif
		$img = gif_loadFile($filename);
		//and converting to a png
		gif_outputAsPng($img, $filename);

		$image = imagecreatefrompng($filename);
	}
	else
	{

		// Use GD's built image create functions for this image.
		if (function_exists('imagecreatefrom' . $extensions[$sizes[2]]))
		{
			$imagecreatefrom = 'imagecreatefrom' . $extensions[$sizes[2]];
			$image = $imagecreatefrom($filename);
		}
	}

	// Draw Text on the image
	if ($modSettings['gallery_set_water_text'] != '')
	{
		$xpos = 5;
		$ypos = 5;

		$color = str_replace("#","", $modSettings['gallery_set_water_textcolor']);

		// Split the colors up into RGB
		$text_color = imagecolorallocate ($image, hexdec(substr($color,0,2)), hexdec(substr($color,2,2)), hexdec(substr($color,4,2)));

		// imageftbbox to get font size for the font file

		if (function_exists('imageftbbox'))
		{
			$bbox= imageftbbox ($modSettings['gallery_set_water_textsize'], 0, $modSettings['gallery_path'] . 'fonts/' . $modSettings['gallery_set_water_textfont'], $modSettings['gallery_set_water_text']);
		$text_width = abs($bbox[4] - $bbox[0]);
		$text_height = abs($bbox[1] - $bbox[5]);
		}
		else
		{
		  $text_height = imagefontheight(3);
		  $text_width = strlen ($modSettings['gallery_set_water_text']) * imagefontwidth(3);
		}

		 if ($modSettings['gallery_set_water_valign'] == 'bottom')
		   $ypos = $image_height - $text_height - 5;
		 elseif ($modSettings['gallery_set_water_valign'] == 'center')
		   $ypos = (int)($image_height / 2 - $text_height / 2);


		 if ($modSettings['gallery_set_water_halign'] == 'right')
		   $xpos = $image_width - $text_width - 5;
		 elseif ($modSettings['gallery_set_water_halign'] == 'center')
		   $xpos = (int)($image_width / 2- $text_width / 2);

		// Write the text on the picuture
		 //imagestring($image, 3, $xpos, $ypos, $modSettings['gallery_set_water_text'], $text_color);
		if (function_exists('imageftbbox'))
		 imagettftext($image, $text_height,0,$xpos,$ypos,$text_color,$modSettings['gallery_path'] . 'fonts/' . $modSettings['gallery_set_water_textfont'],$modSettings['gallery_set_water_text']);
		else
			imagestring($image, 3, $xpos, $ypos, $modSettings['gallery_set_water_text'], $text_color);
	}

	// Picture watermark
	if ($modSettings['gallery_set_water_image'] != '')
	{
		// If the image exists
		if (file_exists($modSettings['gallery_set_water_image']))
		{
			$xpos = 5;
			$ypos = 5;

			$sizes2 = getimagesize($modSettings['gallery_set_water_image']);
			$water_width = $sizes2[0];
			$water_height =$sizes2[1];

			if ($extensions[$sizes2[2]] == 'gif' && !function_exists('imagecreatefromgif') && function_exists('imagecreatefrompng'))
			{
				//Use SMF functions by Yamasoft for loading gif
				$img = gif_loadFile($modSettings['gallery_set_water_image']);
				// and converting to a png
				gif_outputAsPng($img, $modSettings['gallery_set_water_image']);

				$wm_image = imagecreatefrompng($modSettings['gallery_set_water_image']);
			}
			else
			{
				// Use GD's built image create functions for this image.
				if (function_exists('imagecreatefrom' . $extensions[$sizes2[2]]))
				{
					$imagecreatefrom = 'imagecreatefrom' . $extensions[$sizes2[2]];
					$wm_image = $imagecreatefrom($modSettings['gallery_set_water_image']);
				}
			}

			// Resize the watermark depending on the image size

			if ($water_width > $image_width *1.05 ||  $water_height> $image_height *1.05)
			{
				$wdiff= $water_width - $image_width;
				$hdiff= $water_height - $image_height;
				if ($wdiff > $hdiff)
				{
					$sizer=($wdiff/$water_width)-0.05;
				}
				else
				{
					$sizer=($hdiff/$water_height)-0.05;
				}
				$water_width-= $water_width * $sizer;
				$water_height-= $water_height * $sizer;
			}

			/*
			else
			{
				$wdiff=$image_width -  $water_width;
				$hdiff=$image_height -  $water_height;
				if ($wdiff > $hdiff)
				{
					$sizer=($wdiff/ $water_width)+0.05;
				}
				else
				{
					$sizer=($hdiff/ $water_height)+0.05;
				}
				$water_width+= $water_width * $sizer;
				$water_height+= $water_height * $sizer;
			}
			*/

			// die("Watermark w:"  . $water_width . " h:" . $water_height);

			// Now resize the watermark
			if ($gd2)
			{
				$dst_img = imagecreatetruecolor($water_width, $water_height);
			}
			else
				$dst_img = imagecreate( $water_width, $water_height);

			if ($gd2)
				imagecopyresampled($dst_img, $wm_image, 0, 0, 0, 0, $water_width, $water_height, $sizes2[0], $sizes2[1]);
			else
				imagecopyresamplebicubic($dst_img, $wm_image, 0, 0, 0, 0, $water_width, $water_height, $sizes2[0], $sizes2[1]);

			$wm_image = $dst_img;

			// End watermark resize

			if ($wm_image)
			{
				if ($modSettings['gallery_set_water_valign'] == 'bottom')
					$ypos = $image_height - $water_height - 5;
				elseif ($modSettings['gallery_set_water_valign'] == 'center')
					$ypos = (int)($image_height / 2 - $water_height / 2);

				if ($modSettings['gallery_set_water_halign'] == 'right')
					$xpos = $image_width - $water_width - 5;
				elseif ($modSettings['gallery_set_water_halign'] == 'center')
					$xpos = (int)($image_width / 2 - $water_width / 2);

				// Transparent Watermark
				$temp = imagecreatetruecolor( $water_width,  $water_height);
				//$background = imagecolorallocate($wm_image, 0, 0, 0);
				$background = imagecolorat($wm_image,1,1);
				ImageColorTransparent($wm_image,$background);
				imagealphablending($temp, false);

				imagecopymerge($image, $wm_image, $xpos, $ypos, 0, 0, $water_width, $water_height, $modSettings['gallery_set_water_percent']);
			}
		}
	}

	if ($sizes[1] == 2)
	{
		if (function_exists('imagegif'))
			imagegif($image, $finalFilename);
		else
			imagepng($image, $finalFilename);
	}

	if ($sizes[2] == 2)
		imagejpeg($image, $finalFilename);
	if ($sizes[2] == 3)
		imagepng($image, $finalFilename);
}

function BulkActions()
{
	isAllowedTo('smfgallery_manage');

	if (isset($_REQUEST['pics']))
	{
		$baction = $_REQUEST['doaction'];

		foreach ($_REQUEST['pics'] as $value)
		{
           $value = (int) $value;

			if ($baction == 'approve')
				ApprovePictureByID($value);
			if ($baction == 'delete')
				DeletePictureByID($value);
		}
	}

	// Redirect to approval list
	redirectexit('action=admin;area=gallery;sa=approvelist');
}

function UpdateCategoryTotals($id_cat)
{
	global $smcFunc;

    if (empty($id_cat))
        return;

	$dbresult = $smcFunc['db_query']('', "SELECT COUNT(*) AS total FROM {db_prefix}gallery_pic WHERE id_cat = $id_cat AND approved = 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$total = $row['total'];
	$smcFunc['db_free_result']($dbresult);

	// Update the count
	$dbresult = $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat SET total = $total WHERE id_cat = $id_cat LIMIT 1");
}

function UpdateUserCategoryTotals($user_id_cat)
{
	global $smcFunc;

     if (empty($user_id_cat))
        return;

	$dbresult = $smcFunc['db_query']('', "SELECT COUNT(*) AS total FROM {db_prefix}gallery_pic WHERE user_id_cat = $user_id_cat AND approved = 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$total = $row['total'];
	$smcFunc['db_free_result']($dbresult);

	// Update the count
	$dbresult = $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_usercat SET total = $total WHERE user_id_cat = $user_id_cat LIMIT 1");
}

function UpdateCategoryTotalByPictureID($id)
{
	global $smcFunc;

	$dbresult = $smcFunc['db_query']('', "SELECT id_cat, user_id_cat FROM {db_prefix}gallery_pic WHERE id_picture = $id");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);
	If ($row['user_id_cat'] == 0)
		UpdateCategoryTotals($row['id_cat']);
	else
		UpdateUserCategoryTotals($row['user_id_cat']);
}

function BatchFTP()
{
	global $txt, $context, $mbname, $smcFunc;

	isAllowedTo('smfgallery_manage');


	DoGalleryAdminTabs();

	$context['sub_template']  = 'ftp';

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_ftp'];

	// Gallery Category
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_cat, title, id_parent
		FROM {db_prefix}gallery_cat
		WHERE redirect = 0 ORDER BY title ASC");

	if ($smcFunc['db_num_rows']($dbresult) == 0)
		fatal_error($txt['gallery_error_no_catexists'] , false);

	$context['gallery_cat'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_cat'][] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	CreateGalleryPrettyCategory();

	// User Gallery
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			u.id_member,u.user_id_cat, u.title,u.roworder, m.real_name
		FROM {db_prefix}gallery_usercat as u, {db_prefix}members as m
		WHERE u.id_member = m.id_member ORDER BY roworder ASC");
	//if ($smcFunc['db_num_rows']($dbresult) == 0)
		//fatal_error($txt['gallery_error_no_catexists'] , false);

	$context['gallery_cat2'] = array();

	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_cat2'][] = array(
			'id_cat' => $row['user_id_cat'],
			'title' => $row['title'],
			'roworder' => $row['roworder'],
			'real_name' => $row['real_name'],
		);
	}
	$smcFunc['db_free_result']($dbresult);
}

function BatchFTP2()
{
	global $txt, $smcFunc, $context, $modSettings, $gallerySettings, $scripturl, $sourcedir, $user_info, $gd2;

	isAllowedTo('smfgallery_manage');

	// Get the variables
	$cat = (int) $_REQUEST['cat'];
	$usercat = (int) $_REQUEST['usercat'];
	$deletefiles = isset($_REQUEST['deletefiles']) ? 1 : 0;
	$ignoresettings = isset($_REQUEST['ignoresettings']) ? 1 : 0;
	$fpath = $gallerySettings['gallery_set_batchadd_path'] . htmlspecialchars(urldecode($_REQUEST['fpath']), ENT_QUOTES);
	//$fpath2 = htmlspecialchars(urldecode($_REQUEST['fpath']),ENT_QUOTES);

	$fpath = str_replace('..','',$fpath);

	if ($cat == 0 && $usercat == 0)
		fatal_error($txt['gallery_ftp_err_nocatnousercat'],false);

	if ($cat != 0 && $usercat != 0)
		fatal_error($txt['gallery_ftp_err_catandusercat'],false);

	$pic_postername = str_replace('"','', $_REQUEST['pic_postername']);
	$pic_postername = str_replace("'",'', $pic_postername);
	$pic_postername = str_replace('\\','', $pic_postername);
	$pic_postername = $smcFunc['htmlspecialchars']($pic_postername, ENT_QUOTES);

	$memid = 0;

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			real_name, id_member
		FROM {db_prefix}members
		WHERE real_name = '$pic_postername' OR member_name = '$pic_postername'  LIMIT 1");

	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);

	if ($smcFunc['db_affected_rows']() != 0)
	{
		// Member found
		$memid = $row['id_member'];
	}
	else
	{
		$memid = $user_info['id'];
	}

	// Check if gallery path is writable
	if (!is_writable($modSettings['gallery_path']))
		fatal_error($txt['gallery_write_error'] . $modSettings['gallery_path']);

	// Get category infomation
	if ($cat != 0)
	{
		$dbresult = $smcFunc['db_query']('', "SELECT id_board,postingsize, showpostlink, locktopic, id_topic, tweet_items FROM {db_prefix}gallery_cat
		WHERE id_cat = $cat");
		$rowcat = $smcFunc['db_fetch_assoc']($dbresult);
		$smcFunc['db_free_result']($dbresult);
	}

	//Increase the max time to process the imports
	@ini_set('max_execution_time', '3000');
	@ini_set("memory_limit","512M");
	//Read all files in directory

	require_once($sourcedir . '/Subs-Graphics.php');
	require_once($sourcedir . '/Subs-Post.php');

	$testGD = get_extension_funcs('gd');
	$gd2 = in_array('imagecreatetruecolor', $testGD) && function_exists('imagecreatetruecolor');
	unset($testGD);
	$r = 0;

	// Start Main loop
	$failedcount = 0;
	$failedpics = '';

	$goodcount = 0;
	$goodpics = '';

	// Sort the files by filename
	$filesortarray = array();
    if (isset($_REQUEST['fimage']))
    {
    	foreach($_REQUEST['fimage'] as $pic => $value)
    	{
    		$filesortarray[] = $value;
    	}
     }
	array_multisort($filesortarray,SORT_STRING,SORT_DESC);

	foreach($filesortarray as $pic => $value)
	{
		$orginalfilename = addslashes($value);
		$fname = $fpath . '/' . $value;
		$sizes = @getimagesize($fname);
		$r++;

		// No size, then it's probably not a valid pic.
		if ($sizes === false)
		{
			// Log error
			$failedcount++;
			$failedpics .= $value . '<br />';
			continue;
		}
		// Get the filesize
		$filesize = filesize($fname);

		// Check if ignoring the checks
		if ($ignoresettings == 0)
		{
			if ((!empty($modSettings['gallery_max_width']) && $sizes[0] > $modSettings['gallery_max_width']) || (!empty($modSettings['gallery_max_height']) && $sizes[1] > $modSettings['gallery_max_height']))
			{
				// Log error
				$failedcount++;
				$failedpics .= $value . '<br />';
				continue;
			}

			// Check the filesize
			if (!empty($modSettings['gallery_max_filesize']) && $filesize > $modSettings['gallery_max_filesize'])
			{
				// Log error
				$failedcount++;
				$failedpics .= $value . '<br />';
				continue;
			}
		}

		if ($modSettings['gallery_set_enable_multifolder'])
			CreateGalleryFolder();


		$extrafolder = '';

		if ($modSettings['gallery_set_enable_multifolder'])
		{
			$extrafolder = $modSettings['gallery_folder_id'] . '/';
		}

		// Filename Member Id + Day + Month + Year + 24 hour, Minute Seconds
			$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

		$filename = $memid . '-' . date('dmyHis') . '-' . $r . '.' . $extension;

		copy($fname, $modSettings['gallery_path'] . $extrafolder . $filename);
		@chmod($modSettings['gallery_path'] . $extrafolder . $filename, 0644);

		// Check if we are deleting the file
		if ($deletefiles)
			@unlink($fname);
		// Create thumbnail

		GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder . $filename, $modSettings['gallery_thumb_width'], $modSettings['gallery_thumb_height']);
		rename($modSettings['gallery_path'] . $extrafolder . $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder . 'thumb_' . $filename);
		$thumbname = 'thumb_' . $filename;
		@chmod($modSettings['gallery_path'] . $extrafolder .  'thumb_' . $filename, 0755);

		// Medium Image
		$mediumimage = '';

		if ($modSettings['gallery_make_medium'])
		{
			GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder .  $filename, $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
			rename($modSettings['gallery_path'] . $extrafolder .  $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename);
			$mediumimage = 'medium_' . $filename;
			@chmod($modSettings['gallery_path'] . $extrafolder .  'medium_' . $filename, 0755);

			// Check for Watermark
			DoWaterMark($modSettings['gallery_path'] . $extrafolder .  'medium_' .  $filename);

		}

		// Create the Database entry
		$t = time();
		// Escape the filename
		$value = $smcFunc['db_escape_string']($value);

		$smcFunc['db_query']('', "
			INSERT INTO {db_prefix}gallery_pic
				(user_id_cat, id_cat, filesize,thumbfilename,filename, height, width, title,id_member,date,approved,allowcomments,mediumfilename,orginalfilename)
			VALUES
				($usercat, $cat, $filesize, '" . $extrafolder .  $thumbname . "', '" . $extrafolder . $filename . "', $sizes[1], $sizes[0], '$value', $memid, $t, 1, 1, '" . $extrafolder . $mediumimage . "','$orginalfilename')");

		$gallery_pic_id = $smcFunc['db_insert_id']('{db_prefix}gallery_pic', 'id_picture');

		// Get EXIF Data
		ProcessEXIFData($extrafolder . $filename, $gallery_pic_id);

		Gallery_AddRelatedPicture($gallery_pic_id, $value);

        Gallery_AddToActivityStream('galleryproadd',$gallery_pic_id,$value,$memid);

		// If we are using multifolders get the next folder id
		if ($modSettings['gallery_set_enable_multifolder'])
			ComputeNextFolderID($gallery_pic_id);

		// Update Quota Information
		UpdateUserFileSizeTable($memid, $filesize);

		if ($cat !=0 && $rowcat['id_board'] != 0)
		{
			$extraheightwidth = '';
			if ($rowcat['postingsize'] == 1)
			{
				$postimg = $filename;
				$extraheightwidth = " height={$sizes[1]} width={$sizes[0]}";
			}
			else
				$postimg = $thumbname;

			if ($rowcat['showpostlink'] == 1)
				$showpostlink = "\n\n" . $scripturl . '?action=gallery;sa=view;id=' . $gallery_pic_id;
			else
				$showpostlink = '';

			// Create the post
			$msgOptions = array(
				'id' => 0,
				'subject' => $filename,
				'body' => "[img$extraheightwidth]" . $modSettings['gallery_url']  . $extrafolder . $postimg . "[/img]$showpostlink",
				'icon' => 'xx',
				'smileys_enabled' => 1,
				'attachments' => array(),
			);
			$topicOptions = array(
				'id' => $rowcat['id_topic'],
				'board' => $rowcat['id_board'],
				'poll' => null,
				'lock_mode' => $rowcat['locktopic'],
				'sticky_mode' => null,
				'mark_as_read' => true,
			);
			$posterOptions = array(
				'id' => $memid,
				'update_post_count' => !$user_info['is_guest'],
			);
			// Fix height & width of posted image in message
			preparsecode($msgOptions['body']);

			createPost($msgOptions, $topicOptions, $posterOptions);

			require_once($sourcedir . '/Post.php');

                    if (function_exists("notifyMembersBoard"))
                    {
                    $notifyData = array(
                                'body' =>$msgOptions['body'],
                                'subject' => $msgOptions['subject'],
                                'poster' => $memid,
                                'msg' => $msgOptions['id'],
                                'board' =>  $rowcat['id_board'],
                                'topic' => $topicOptions['id'],
                            );
                    notifyMembersBoard($notifyData);

                    }
                    else
                    {
                     // for 2.1
                    $smcFunc['db_insert']('',
                        '{db_prefix}background_tasks',
                        array('task_file' => 'string', 'task_class' => 'string', 'task_data' => 'string', 'claimed_time' => 'int'),
                        array('$sourcedir/tasks/CreatePost-Notify.php', 'CreatePost_Notify_Background', $smcFunc['json_encode'](array(
                            'msgOptions' => $msgOptions,
                            'topicOptions' => $topicOptions,
                            'posterOptions' => $posterOptions,
                            'type' =>  $topicOptions['id'] ? 'reply' : 'topic',
                        )), 0),
                        array('id_task')
                    );


                    }



			$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
					SET id_topic = " .$topicOptions['id'] . ", id_msg = " . $msgOptions['id'] . " WHERE id_picture = $gallery_pic_id
					");


		} // End posting check

        if ($rowcat['tweet_items'] == 1)
            Gallery_TweetItem($filename,$gallery_pic_id);

		// Check for Watermark
		DoWaterMark($modSettings['gallery_path'] . $extrafolder . $filename);

		UpdateMemberPictureTotals($memid);

		Gallery_UpdateLatestCategory($cat);
		Gallery_UpdateUserLatestCategory($usercat);

		if ($cat !== 0)
			UpdateCategoryTotals($cat);
		else
			UpdateUserCategoryTotals($usercat);

		// Picture had no errors
		$goodcount++;
		$goodpics .= $value . '<br />';
	}
   // End of Main Image Loop

   // Load the subtemplate

   // Pass the erorrs and completed images


	$context['sub_template']  = 'ftp2';

	$context['page_title'] = $txt['gallery_text_title'] . ' - ' . $txt['gallery_ftp_complete'];

	$context['gallery_ftp_good'] = $goodcount;
	$context['gallery_ftp_goodpics'] = $goodpics;

	$context['gallery_ftp_failed'] = $failedcount;
	$context['gallery_ftp_failedpics'] = $failedpics;
}

function CustomUp()
{
	global $smcFunc, $txt;

	// Check Permission
	isAllowedTo('smfgallery_manage');
	//Get the id
	@$id = (int) $_REQUEST['id'];

	ReOrderCustom($id);

	// Check if there is a category above it
	// First get our row order
	$dbresult1 = $smcFunc['db_query']('', "
		SELECT
			id_cat, id_custom, roworder
		FROM {db_prefix}gallery_custom_field
		WHERE id_custom = $id");

	$row = $smcFunc['db_fetch_assoc']($dbresult1);

	$id_cat = $row['id_cat'];
	$oldrow = $row['roworder'];
	$o = $row['roworder'];
	$o++;

	$smcFunc['db_free_result']($dbresult1);
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_custom, roworder
		FROM {db_prefix}gallery_custom_field
		WHERE id_cat = $id_cat AND roworder = $o");

	if ($smcFunc['db_affected_rows']()== 0)
		fatal_error($txt['gallery_error_nocustom_above'], false);
	$row2 = $smcFunc['db_fetch_assoc']($dbresult);


	// Swap the order Id's
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_custom_field
		SET roworder = $oldrow WHERE id_custom = " .$row2['id_custom']);

	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_custom_field
		SET roworder = $o WHERE id_custom = $id");



	$smcFunc['db_free_result']($dbresult);

	// Redirect to index to view cats
	redirectexit('action=gallery;sa=editcat;cat=' . $id_cat);
}

function CustomDown()
{
	global $smcFunc, $txt;

	isAllowedTo('smfgallery_manage');


	//Get the id
	@$id = (int) $_REQUEST['id'];

	ReOrderCustom($id);

	// Check if there is a category below it
	// First get our row order
	$dbresult1 = $smcFunc['db_query']('', "
		SELECT
			id_custom,id_cat, roworder
		FROM {db_prefix}gallery_custom_field
		WHERE id_custom = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult1);
	$id_cat = $row['id_cat'];

	$oldrow = $row['roworder'];
	$o = $row['roworder'];
	$o--;

	$smcFunc['db_free_result']($dbresult1);
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_custom, id_cat, roworder
		FROM {db_prefix}gallery_custom_field
		WHERE id_cat = $id_cat AND roworder = $o");

	if ($smcFunc['db_affected_rows']()== 0)
		fatal_error($txt['gallery_error_nocustom_below'], false);
	$row2 = $smcFunc['db_fetch_assoc']($dbresult);

	// Swap the order Id's
	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_custom_field
		SET roworder = $oldrow WHERE id_custom = " .$row2['id_custom']);

	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_custom_field
		SET roworder = $o WHERE id_custom = $id");

	$smcFunc['db_free_result']($dbresult);

	// Redirect to index to view cats
	redirectexit('action=gallery;sa=editcat;cat=' . $id_cat);
}

function CustomAdd()
{
	global $smcFunc, $txt;
	// Check Permission
	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];

    $catID = $id;

	$title = $smcFunc['htmlspecialchars']($_REQUEST['title'],ENT_QUOTES);
	$defaultvalue = $smcFunc['htmlspecialchars']($_REQUEST['defaultvalue'],ENT_QUOTES);
	$required = isset($_REQUEST['required']) ? 1 : 0;
    $globalfield = isset($_REQUEST['globalfield']) ? 1 : 0;

    if ($globalfield == 1)
        $catID = 0;

	if ($title == '')
		fatal_error($txt['gallery_custom_err_title'], false);

	$smcFunc['db_query']('', "
		INSERT INTO {db_prefix}gallery_custom_field
			(id_cat,title, defaultvalue, is_required)
		VALUES ($catID,'$title','$defaultvalue', '$required')");

	// Redirect back to the edit category page
	redirectexit('action=gallery;sa=editcat;cat=' . $id);
}

function CustomEdit()
{
	global $smcFunc, $txt, $context;

	// Check Permission
	isAllowedTo('smfgallery_manage');
	$id = (int) $_REQUEST['id'];
    $catID  = (int) $_REQUEST['catid'];

	$dbresult = $smcFunc['db_query']('', "
	SELECT
		ID_CUSTOM, ID_CAT, defaultvalue, is_required, title
	FROM {db_prefix}gallery_custom_field
	WHERE ID_CUSTOM = $id ");
    $row = $smcFunc['db_fetch_assoc']($dbresult);

    $context['gallery_customfield'] = $row;
    $context['gallery_customfield_catid'] = $catID;

   	$context['sub_template']  = 'customedit';
	$context['page_title'] = $txt['gallery_custom_editfield'];

}

function CustomEdit2()
{
	global $smcFunc, $txt;
	// Check Permission
	isAllowedTo('smfgallery_manage');
	$id = (int) $_REQUEST['id'];
    $catID  = (int) $_REQUEST['catid'];

	$title = htmlspecialchars($_REQUEST['title'],ENT_QUOTES);
	$defaultvalue = htmlspecialchars($_REQUEST['defaultvalue'],ENT_QUOTES);
	$required = isset($_REQUEST['required']) ? 1 : 0;
    $globalfield = isset($_REQUEST['globalfield']) ? 1 : 0;

    $orginalCatID = $catID;

    if ($globalfield == 1)
        $catID = 0;

	if ($title == '')
		fatal_error($txt['gallery_custom_err_title'], false);


	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_custom_field
    SET
	title = '$title', defaultvalue = '$defaultvalue', is_required = '$required', ID_CAT = $catID
    WHERE ID_CUSTOM = $id
        ");

	// Redirect back to the edit category page
	redirectexit('action=gallery;sa=editcat;cat=' . $orginalCatID);

}

function CustomDelete()
{
	global $smcFunc;

	// Check Permission
	isAllowedTo('smfgallery_manage');

	// Custom ID
	$id = (int) $_REQUEST['id'];

	// Get the CAT ID to redirect to the page
	$result = $smcFunc['db_query']('', "
		SELECT
			id_cat
		FROM {db_prefix}gallery_custom_field
		WHERE id_custom =  $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Delete all custom data for pictures that use it
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_custom_field_data
	WHERE id_custom = $id ");

	// Finaly delete the field
	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_custom_field
	WHERE id_custom = $id LIMIT 1");

	// Redirect to the edit category page
	redirectexit('action=gallery;sa=editcat;cat=' . $row['id_cat']);
}

function ReOrderCustom($id)
{
	global $smcFunc;

	// Get the Category ID by id
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_cat, roworder
		FROM {db_prefix}gallery_custom_field
		WHERE id_custom = $id");
	$row1 = $smcFunc['db_fetch_assoc']($dbresult);
	$id_cat = $row1['id_cat'];
	$smcFunc['db_free_result']($dbresult);

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_custom, roworder
		FROM {db_prefix}gallery_custom_field
		WHERE id_cat = $id_cat ORDER BY roworder ASC");

	if ($smcFunc['db_affected_rows']() != 0)
	{
		$count = 1;
		while($row2 = $smcFunc['db_fetch_assoc']($dbresult))
		{
			$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_custom_field
			SET roworder = $count WHERE id_custom = " . $row2['id_custom']);
			$count++;
		}
	}
	$smcFunc['db_free_result']($dbresult);
}

// Moves a picture from user gallery to a main gallery
// Or moves from a main gallery to a user gallery
function ChangeGallery()
{
	global $smcFunc, $context, $txt;

	// Check Permission
	isAllowedTo('smfgallery_manage');

	$mv = $_REQUEST['mv'];

	$id = (int) $_REQUEST['id'];

	$context['gallery_pic_id'] = $id;
	$context['gallery_mv'] = $mv;

	if ($mv == 'touser')
	{
		// User Gallery
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			u.id_member,u.user_id_cat, u.title,u.roworder, m.real_name
		FROM {db_prefix}gallery_usercat as u, {db_prefix}members as m
		WHERE u.id_member = m.id_member ORDER BY roworder ASC");

		if ($smcFunc['db_num_rows']($dbresult) == 0)
			fatal_error($txt['gallery_error_no_catexists']  , false);

		$context['gallery_cat2'] = array();

		while($row = $smcFunc['db_fetch_assoc']($dbresult))
		{
			$context['gallery_cat2'][] = array(
				'id_cat' => $row['user_id_cat'],
				'title' => $row['title'],
				'roworder' => $row['roworder'],
				'real_name' => $row['real_name'],
			);
		}
		$smcFunc['db_free_result']($dbresult);
	}

	if ($mv == 'togallery')
	{
		// Gallery Category
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_cat, title, id_parent
		FROM {db_prefix}gallery_cat
		WHERE redirect = 0 ORDER BY title ASC");

		if ($smcFunc['db_num_rows']($dbresult) == 0)
			fatal_error($txt['gallery_error_no_catexists'] , false);

		$context['gallery_cat'] = array();
		 while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				$context['gallery_cat'][] = $row;
			}
		$smcFunc['db_free_result']($dbresult);

		CreateGalleryPrettyCategory();
	}

	$context['sub_template']  = 'change_gallery';

	$context['page_title'] = $txt['gallery_txt_changepiclocation'];
}

function ChangeGallery2()
{
	global $smcFunc;

	// Check Permission
	isAllowedTo('smfgallery_manage');

	// Get the picture ID
	$id = (int) $_REQUEST['id'];

	if (isset($_REQUEST['cat']))
		$cat = (int) $_REQUEST['cat'];
	else
		$cat = 0;

	if (isset($_REQUEST['usercat']))
		$usercat = (int) $_REQUEST['usercat'];
	else
		$usercat = 0;

	// Get the old gallery category information
	$result = $smcFunc['db_query']('', "
	SELECT
		user_id_cat, id_cat
	FROM {db_prefix}gallery_pic
	WHERE id_picture = $id LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	if (!empty($cat))
	{
		$smcFunc['db_query']('', "
			UPDATE {db_prefix}gallery_pic
			SET id_cat = $cat, user_id_cat = 0  WHERE id_picture = $id LIMIT 1");

		UpdateCategoryTotals($cat);

		UpdateUserCategoryTotals($row['user_id_cat']);
	}

	if (!empty($usercat))
	{
		$smcFunc['db_query']('', "
			UPDATE {db_prefix}gallery_pic
			SET id_cat = 0, user_id_cat = $usercat  WHERE id_picture = $id LIMIT 1");

		UpdateUserCategoryTotals($usercat);

		UpdateCategoryTotals($row['id_cat']);
	}

	// Redirect the picture
	redirectexit('action=gallery;sa=view;id=' . $id);
}

function ComputeNextFolderID($id_picture)
{
	global $modSettings;

	$folderid = floor($id_picture / 1000);

	// If the current folder ID does not match the new folder ID update the settings
	if ($modSettings['gallery_folder_id'] != $folderid)
		updateSettings(array('gallery_folder_id' => $folderid));
}

function CreateGalleryFolder()
{
	global $modSettings;

	$newfolderpath = $modSettings['gallery_path'] . $modSettings['gallery_folder_id'] . '/';

	// Check if the folder exists if it doess just exit
	if  (!file_exists($newfolderpath))
	{
		// If the folder does not exist then create it
		@mkdir ($newfolderpath);
		// Try to make sure that the correct permissions are on the folder
		@chmod ($newfolderpath,0755);
	}
}

function ListAll()
{
	global $smcFunc, $user_info, $context, $txt,  $mbname, $modSettings, $scripturl;

	// Is the user allowed to view the gallery?
	isAllowedTo('smfgallery_view');

	if (!$context['user']['is_guest'])
		$groupsdata = implode(',',$user_info['groups']);
	else
		$groupsdata = -1;

	StoreGalleryLocation();

    if (isset($_REQUEST['perpage']))
	{
		$galleryPerPage = (int) $_REQUEST['perpage'];

		if ($galleryPerPage < $modSettings['orignal_set_images_per_page'])
			$galleryPerPage = $modSettings['orignal_set_images_per_page'];

		if ($galleryPerPage > $modSettings['orignal_set_images_per_page'] * 3)
			$galleryPerPage = $modSettings['orignal_set_images_per_page'] * 3;

		$_SESSION['galleryperpage'] = $galleryPerPage;
		$modSettings['gallery_set_images_per_page'] = $galleryPerPage;
	}


	$type = $_REQUEST['type'];

	$context['sub_template']  = 'listall';

	$orderby = 'desc';
	if (isset($_REQUEST['orderby']))
	{
		if ($_REQUEST['orderby'] == 'desc')
			$orderby = 'desc';
		else
			$orderby = 'asc';
	}

	if (allowedTo('smfgallery_manage'))
	{
		$wherestats = '
		LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
        LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
				LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = ' . $context['user']['id'] . ' AND v.user_id_cat = p.USER_ID_CAT)
		WHERE p.approved = 1';
	}
	else
		$wherestats = "
		LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
        LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
				LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $context['user']['id'] . " AND v.user_id_cat = p.USER_ID_CAT)
		LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.id_group IN ($groupsdata) AND c.id_cat = p.id_cat)
		WHERE ((s.private = 0 OR s.private IS NULL) AND (s.password = '' OR s.password IS NULL) AND p.user_id_cat != 0 AND p.approved = 1)  OR (p.approved = 1 AND p.user_id_cat = 0 AND (c.view IS NULL OR c.view = 1)) ";

	$commentWhere = '';
	$selectExtra = '';

	switch ($type)
	{
		case 'comments':
			$listorder = 'p.commenttotal';
			$reqadd = ';orderby=' . $orderby;
			$listtitle = 'mostcomments';
		break;

		case 'likes':
			$listorder = 'p.totallikes';
			$reqadd = ';orderby=' . $orderby;
			$listtitle = 'mostliked';
		break;


		case 'views':
			$listorder = 'p.views';
			$reqadd = ';orderby=' . $orderby;
			$listtitle = 'viewed';
		break;
		case 'toprated':
			$listorder = 'ratingaverage DESC, p.totalratings ';
			$reqadd = ';orderby=' . $orderby;
			$listtitle = 'toprated';
		break;

		case 'recentcomments':
			//$listorder = 'com.id_comment';
			$listorder = 'lastcomment';
			$reqadd = ';orderby=' . $orderby;
			$listtitle = 'recentcomment';
			$commentWhere = ', {db_prefix}gallery_comment as com ';
			$selectExtra = 'max(com.ID_COMMENT) AS lastcomment, ';
			$wherestats = "

		LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
        LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
						LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $context['user']['id'] . " AND v.user_id_cat = p.USER_ID_CAT)
				LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.id_group IN ($groupsdata) AND c.id_cat = p.id_cat)
		WHERE com.id_picture = p.id_picture AND (((s.private = 0 OR s.private IS NULL) AND (s.password = '' OR s.password IS NULL) AND p.user_id_cat != 0 AND p.approved = 1) OR (p.approved = 1 AND p.user_id_cat = 0 AND (c.view IS NULL OR c.view = 1)))  ";

		break;

		case 'random':
			$listorder = ' RAND() ';
			$reqadd = ';orderby=' . $orderby;
			$listtitle = 'random';
		break;


		default:
			$listorder = 'p.id_picture';
			$reqadd = '';
			$listtitle = 'last';
		break;
	}

	// Get total number of results
	$result = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) as total
		FROM ({db_prefix}gallery_pic as p $commentWhere)
		$wherestats
	");


	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	$context['page_index'] = constructPageIndex($scripturl . '?action=gallery;sa=listall;type=' . $_REQUEST['type'] . $reqadd, $_REQUEST['start'], $row['total'], $modSettings['gallery_set_images_per_page']);
	$context['start'] = $_REQUEST['start'];


	$result = $smcFunc['db_query']('', "
		SELECT
			$selectExtra p.id_picture, p.commenttotal, p.totalratings, p.rating, p.filesize, p.mature,
			p.views, p.thumbfilename, p.title, p.id_member, m.real_name,mg.online_color, p.date, p.description, v.id_picture as unread, (p.rating / p.totalratings ) AS ratingaverage,
			p.totallikes
		FROM ({db_prefix}gallery_pic as p $commentWhere) $wherestats GROUP BY p.id_picture
		ORDER BY $listorder $orderby
		LIMIT $context[start], " . $modSettings['gallery_set_images_per_page']
	 );

	$context['picture_list'] = array();
	while($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context['picture_list'][] = $row;
	}
	$smcFunc['db_free_result']($result);

	$context['gallery_stat_title'] = $txt['gallery_stats_' . $listtitle];
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_stats_' . $listtitle];

    $context['linktree'][] = array(
			'name' =>  $txt['gallery_stats_' . $listtitle]
		);

}

function DoGalleryAdminTabs($overrideSelected = '')
{
	global $context, $txt, $smcFunc;

	$tmpSA = '';
	if (!empty($overrideSelected))
		$_REQUEST['sa'] = $overrideSelected;

	$dbresult3 = $smcFunc['db_query']('', "
			SELECT
				COUNT(*) AS total
			FROM {db_prefix}gallery_pic
			WHERE approved = 0");
			$totalrow = $smcFunc['db_fetch_assoc']($dbresult3);
			$totalappoval = $totalrow['total'];



	$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['smfgallery_admin'],
			'description' => '',
			'tabs' => array(
				'adminset' => array(
					'description' => $txt['gallery_set_description'],
					'label' => $txt['gallery_text_settings'] ,
				),
				'admincat' => array(
					'description' => $txt['gallery_managecats_description'],
					'label' => $txt['gallery_form_managecats'],
				),
				'approvelist' => array(
					'description' => $txt['gallery_approvelist_description'],
					'label' => $txt['gallery_txt_moderationcenter'] . ' (' . $totalappoval . ')',
				),

				'filespace' => array(
					'description' => $txt['gallery_filespace_description'],
					'label' => $txt['gallery_filespace'],
				),
				'catpermlist' => array(
					'description' => $txt['gallery_catpermlist_description'],
					'label' => $txt['gallery_text_catpermlist2'],
				),
				'batchftp' => array(
					'description' => $txt['gallery_batchftp_description'],
					'label' => $txt['gallery_ftp'],
				),
			),
		);


}

function ViewLayoutSettings()
{
	global $context, $mbname, $txt;
	isAllowedTo('smfgallery_manage');


	DoGalleryAdminTabs('adminset');

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_layout_settings'];

	$context['sub_template']  = 'gallery_layout';
}

function SaveLayoutSettings()
{
	isAllowedTo('smfgallery_manage');

	// Index Page Settings
	$gallery_set_show_cat_latest_pictures = isset($_REQUEST['gallery_set_show_cat_latest_pictures']) ? 1 : 0;
	$gallery_index_toprated =  isset($_REQUEST['gallery_index_toprated']) ? 1 : 0;
	$gallery_index_recent =   isset($_REQUEST['gallery_index_recent']) ? 1 : 0;
	$gallery_index_mostviewed =  isset($_REQUEST['gallery_index_mostviewed']) ? 1 : 0;
	$gallery_index_mostcomments = isset($_REQUEST['gallery_index_mostcomments']) ? 1 : 0;
	$gallery_index_showtop = isset($_REQUEST['gallery_index_showtop']) ? 1 : 0;
	$gallery_index_showusergallery = isset($_REQUEST['gallery_index_showusergallery']) ? 1 : 0;
	$gallery_index_images_to_show = (int) $_REQUEST['gallery_index_images_to_show'];
	$gallery_index_randomimages = isset($_REQUEST['gallery_index_randomimages']) ? 1 : 0;
	$gallery_index_recentcomments = isset($_REQUEST['gallery_index_recentcomments']) ? 1 : 0;
	$gallery_index_mostliked = isset($_REQUEST['gallery_index_mostliked']) ? 1 : 0;

	// Tag Cloud
	$gallery_index_show_tag_cloud = isset($_REQUEST['gallery_index_show_tag_cloud']) ? 1 : 0;
	$gallery_set_cloud_tags_to_show = (int) $_REQUEST['gallery_set_cloud_tags_to_show'];
	$gallery_set_cloud_tags_per_row = (int) $_REQUEST['gallery_set_cloud_tags_per_row'];
	$gallery_set_cloud_max_font_size_precent = (int) $_REQUEST['gallery_set_cloud_max_font_size_precent'];
	$gallery_set_cloud_min_font_size_precent = (int) $_REQUEST['gallery_set_cloud_min_font_size_precent'];


	// Thumbnail view category settings
	$gallery_set_t_views = isset($_REQUEST['gallery_set_t_views']) ? 1 : 0;
	$gallery_set_t_filesize = isset($_REQUEST['gallery_set_t_filesize']) ? 1 : 0;
	$gallery_set_t_date = isset($_REQUEST['gallery_set_t_date']) ? 1 : 0;
	$gallery_set_t_comment = isset($_REQUEST['gallery_set_t_comment']) ? 1 : 0;
	$gallery_set_t_username = isset($_REQUEST['gallery_set_t_username']) ? 1 : 0;
	$gallery_set_t_rating = isset($_REQUEST['gallery_set_t_rating']) ? 1 : 0;
	$gallery_set_t_title = isset($_REQUEST['gallery_set_t_title']) ? 1 : 0;
	$gallery_set_t_totallikes = isset($_REQUEST['gallery_set_t_totallikes']) ? 1 : 0;

	// Picture display settings
	$gallery_set_img_size = isset($_REQUEST['gallery_set_img_size']) ? 1 : 0;
	$gallery_set_img_prevnext = isset($_REQUEST['gallery_set_img_prevnext']) ? 1 : 0;
	$gallery_set_img_desc = isset($_REQUEST['gallery_set_img_desc']) ? 1 : 0;
	$gallery_set_img_title = isset($_REQUEST['gallery_set_img_title']) ? 1 : 0;
	$gallery_set_img_views = isset($_REQUEST['gallery_set_img_views']) ? 1 : 0;
	$gallery_set_img_poster = isset($_REQUEST['gallery_set_img_poster']) ? 1 : 0;
	$gallery_set_img_date = isset($_REQUEST['gallery_set_img_date']) ? 1 : 0;
	$gallery_set_img_showfilesize = isset($_REQUEST['gallery_set_img_showfilesize']) ? 1 : 0;
	$gallery_set_img_showrating = isset($_REQUEST['gallery_set_img_showrating']) ? 1 : 0;
	$gallery_set_img_keywords = isset($_REQUEST['gallery_set_img_keywords']) ? 1 : 0;
	$gallery_set_img_totallikes = isset($_REQUEST['gallery_set_img_totallikes']) ? 1 : 0;

	$gallery_set_mini_prevnext_thumbs = isset($_REQUEST['gallery_set_mini_prevnext_thumbs']) ? 1 : 0;
	$gallery_set_picture_information_last = isset($_REQUEST['gallery_set_picture_information_last']) ? 1 : 0;
	$gallery_set_hide_lastmodified_comment = isset($_REQUEST['gallery_set_hide_lastmodified_comment']) ? 1 : 0;

	$gallery_share_facebook = isset($_REQUEST['gallery_share_facebook']) ? 1 : 0;
	$gallery_share_twitter = isset($_REQUEST['gallery_share_twitter']) ? 1 : 0;
	$gallery_share_addthis = isset($_REQUEST['gallery_share_addthis']) ? 1 : 0;

    $gallery_share_facebooklike = isset($_REQUEST['gallery_share_facebooklike']) ? 1 : 0;
    $gallery_share_pinterest = isset($_REQUEST['gallery_share_pinterest']) ? 1 : 0;
    $gallery_share_reddit = isset($_REQUEST['gallery_share_reddit']) ? 1 : 0;

	$gallery_set_downloadimage = isset($_REQUEST['gallery_set_downloadimage']) ? 1 : 0;

	// Other Settings
	$gallery_bulkuploadfields = (int) $_REQUEST['gallery_bulkuploadfields'];
	$gallery_show_unviewed_items = isset($_REQUEST['gallery_show_unviewed_items']) ? 1 : 0;

	UpdateGallerySettings(
	array(
	'gallery_set_show_cat_latest_pictures' => $gallery_set_show_cat_latest_pictures,


	'gallery_bulkuploadfields' => $gallery_bulkuploadfields,
	'gallery_index_images_to_show' => $gallery_index_images_to_show,
	'gallery_index_randomimages' => $gallery_index_randomimages,
	'gallery_index_recentcomments' => $gallery_index_recentcomments,

	'gallery_index_show_tag_cloud' => $gallery_index_show_tag_cloud,
	'gallery_set_cloud_tags_to_show' => $gallery_set_cloud_tags_to_show,
	'gallery_set_cloud_tags_per_row' => $gallery_set_cloud_tags_per_row,
	'gallery_set_cloud_max_font_size_precent' => $gallery_set_cloud_max_font_size_precent,
	'gallery_set_cloud_min_font_size_precent' => $gallery_set_cloud_min_font_size_precent,

	'gallery_share_facebook' => $gallery_share_facebook,
	'gallery_share_twitter' => $gallery_share_twitter,
	'gallery_share_addthis' => $gallery_share_addthis,
    'gallery_share_facebooklike' => $gallery_share_facebooklike,
    'gallery_share_pinterest' => $gallery_share_pinterest,
    'gallery_share_reddit' => $gallery_share_reddit,

	'gallery_set_mini_prevnext_thumbs' => $gallery_set_mini_prevnext_thumbs,
	'gallery_set_picture_information_last' => $gallery_set_picture_information_last,
	'gallery_set_hide_lastmodified_comment' => $gallery_set_hide_lastmodified_comment,

	'gallery_set_downloadimage' => $gallery_set_downloadimage,

	));


	// Save Layout Settings
	updateSettings(
		array(
	'gallery_show_unviewed_items' => $gallery_show_unviewed_items,

	'gallery_index_toprated' => $gallery_index_toprated,
	'gallery_index_recent' => $gallery_index_recent,
	'gallery_index_mostviewed' => $gallery_index_mostviewed,
	'gallery_index_mostcomments' => $gallery_index_mostcomments,
	'gallery_index_showtop' => $gallery_index_showtop,
	'gallery_index_mostliked' => $gallery_index_mostliked,
	'gallery_index_showusergallery' => $gallery_index_showusergallery,

	'gallery_set_t_views' => $gallery_set_t_views,
	'gallery_set_t_filesize' => $gallery_set_t_filesize,
	'gallery_set_t_date' => $gallery_set_t_date,
	'gallery_set_t_comment' => $gallery_set_t_comment,
	'gallery_set_t_username' => $gallery_set_t_username,
	'gallery_set_t_rating' => $gallery_set_t_rating,
	'gallery_set_t_title' => $gallery_set_t_title,
	'gallery_set_t_totallikes' => $gallery_set_t_totallikes,

	'gallery_set_img_size' => $gallery_set_img_size,
	'gallery_set_img_prevnext' => $gallery_set_img_prevnext,
	'gallery_set_img_desc' => $gallery_set_img_desc,
	'gallery_set_img_title' => $gallery_set_img_title,
	'gallery_set_img_views' => $gallery_set_img_views,
	'gallery_set_img_poster' => $gallery_set_img_poster,
	'gallery_set_img_date' => $gallery_set_img_date,
	'gallery_set_img_showfilesize' => $gallery_set_img_showfilesize,
	'gallery_set_img_showrating' => $gallery_set_img_showrating,
	'gallery_set_img_keywords' => $gallery_set_img_keywords,
	'gallery_set_img_totallikes' => $gallery_set_img_totallikes,

		)
	);

	redirectexit('action=admin;area=gallery;sa=viewlayout');
}

function ReturnEXIFData($filename)
{

	// Check if EXIF Data exists
	if (!function_exists('exif_read_data'))
		return;


	// Read the EXIF data from the picture
	$exifData = @exif_read_data($filename, 'ANY_TAG', true);

	// Check if any data was found
	return $exifData;
}

function ProcessEXIFData($filename, $pictureid, $exifNewData = '')
{
	global $smcFunc, $modSettings;

	$filename =  $modSettings['gallery_path'] . $filename;

	if (empty($exifNewData))
	{
		// Check if EXIF Data exists
		if (!function_exists('exif_read_data'))
			return;

		// Check if extension supports EXIF
		$extension = strtolower(substr(strrchr($filename, '.'), 1));


		if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'tiff')
			return;

		// Read the EXIF data from the picture
		$exifData = @exif_read_data($filename, 'ANY_TAG', true);

		// Check if any data was found
		if (!$exifData)
			return;
	}
	else
		$exifData = $exifNewData;


	if (!empty($exifData['FILE']))
	foreach(@$exifData['FILE'] as $record => $value)
	{
	   if (!is_array($value))
		$exifData['FILE'][$record] = $smcFunc['db_escape_string'](htmlspecialchars($value,ENT_QUOTES));
	}

	if (!empty($exifData['COMPUTED']))
	foreach(@$exifData['COMPUTED'] as $record => $value)
	{
	   if (!is_array($value))
		  $exifData['COMPUTED'][$record] = $smcFunc['db_escape_string'](htmlspecialchars($value,ENT_QUOTES));
	}

	if (!empty($exifData['IFD0']))
	foreach(@$exifData['IFD0'] as $record => $value)
	{
		if (!is_array($value))
            $exifData['IFD0'][$record] = @$smcFunc['db_escape_string'](htmlspecialchars($value,ENT_QUOTES));
	}

	if (!empty($exifData['EXIF']))
	foreach(@$exifData['EXIF'] as $record => $value)
	{
        if (!is_array($value))
		  $exifData['EXIF'][$record] = $smcFunc['db_escape_string'](htmlspecialchars($value,ENT_QUOTES));
	}


	if (!empty($exifData['GPS']))
	{
	  $latitude = @$exifData['GPS']['GPSLatitude'];
      $logitude = @$exifData['GPS']['GPSLongitude'];
      if ($latitude && $logitude)
      {

      	  $temp = explode("/", $latitude[0]);
      	  $lat_degrees = $temp[0] / $temp[1];

      	  $temp = explode("/", $latitude[1]);
	      $lat_minutes = $temp[0] / $temp[1];

	      $temp = explode("/", $latitude[2]);
	      $lat_seconds = $temp[0] / $temp[1];
	      $lat_hemi = $exifData['GPS']['GPSLatitudeRef'];

	      $d = $lat_degrees + $lat_minutes/60 +  $lat_seconds/3600;
    	  $latdegrees = ($lat_hemi=='S' || $lat_hemi=='W') ? $d*=-1 : $d;
	      $exifData['GPS']['GPSLatitude'] = $latdegrees;


	      $temp = explode("/", $logitude[0]);
	      $log_degrees = $temp[0] / $temp[1];

	      $temp = explode("/", $logitude[1]);
	      $log_minutes = $temp[0] / $temp[1];

	      $temp = explode("/", $logitude[2]);
	      $log_seconds = $temp[0] / $temp[1];

	      $log_hemi = $exifData['GPS']['GPSLongitudeRef'];


	      $d = $log_degrees + $log_minutes/60 +  $log_seconds/3600;
    	  $logdegrees = ($log_hemi=='S' || $log_hemi=='W') ? $d*=-1 : $d;
		  $exifData['GPS']['GPSLongitude'] =  $logdegrees;

      }

	}

	// Insert the category
	$smcFunc['db_query']('', "
		REPLACE INTO {db_prefix}gallery_exif_data
			(id_picture,
	file_filedatetime,file_filesize,file_filetype,file_mimetype,file_sectionsfound,computed_height,
	computed_width,computed_iscolor,computed_ccdwidth,computed_aperturefnumber,computed_copyright,
	idfo_imagedescription,idfo_make,idfo_model,idfo_orientation,idfo_xresolution,idfo_yresolution,
	idfo_resolutionunit,idfo_software,idfo_datetime,idfo_artist,exif_exposuretime, exif_fnumber,exif_exposureprogram,
	exif_isospeedratings,exif_exifversion,exif_datetimeoriginal,exif_datetimedigitized,exif_shutterspeedvalue,
	exif_aperturevalue,exif_exposurebiasvalue,exif_maxaperturevalue,exif_meteringmode,exif_lightsource,
	exif_flash,exif_focallength,exif_colorspace,exif_exifimagewidth,exif_exifimagelength,exif_focalplanexresolution,
	exif_focalplaneyresolution,exif_focalplaneresolutionunit, exif_customrendered,exif_exposuremode,
	exif_whitebalance,exif_scenecapturetype,
	exif_lenstype,exif_lensid,exif_lensinfo,
	gps_latituderef,gps_latitude,gps_longituderef,gps_longitude
			)
			VALUES ('$pictureid',
	'" . @$exifData['FILE']['FileDateTime'] . "','" . @$exifData['FILE']['FileSize'] . "','" . @$exifData['FILE']['FileType'] . "','" . @$exifData['FILE']['MimeType'] . "','" . @$exifData['FILE']['SectionsFound'] . "',
	'" . @$exifData['COMPUTED']['Height'] . "',
	'" . @$exifData['COMPUTED']['Width'] . "','" . @$exifData['COMPUTED']['IsColor'] . "','" . @$exifData['COMPUTED']['CCDWidth'] . "','" . @$exifData['COMPUTED']['ApertureFNumber'] . "','" . @$exifData['COMPUTED']['Copyright'] . "',
	'" . @$exifData['IFD0']['ImageDescription'] . "','" . @$exifData['IFD0']['Make'] . "','" . @$exifData['IFD0']['Model'] . "','" . @$exifData['IFD0']['Orientation'] . "','" . @$exifData['IFD0']['XResolution'] . "','" . @$exifData['IFD0']['YResolution'] . "',
	'" . @$exifData['IFD0']['ResolutionUnit'] . "','" . @$exifData['IFD0']['Software'] . "','" . @$exifData['IFD0']['DateTime'] . "','" . @$exifData['IFD0']['Artist'] . "','" . @$exifData['EXIF']['ExposureTime'] . "','" . @$exifData['EXIF']['FNumber'] . "','" . @$exifData['EXIF']['ExposureProgram'] . "',
	'" . @$exifData['EXIF']['ISOSpeedRatings'] . "','" . @$exifData['EXIF']['ExifVersion'] . "','" . @$exifData['EXIF']['DateTimeOriginal'] . "','" . @$exifData['EXIF']['DateTimeDigitized'] . "','" . @$exifData['EXIF']['ShutterSpeedValue'] . "',
	'" . @$exifData['EXIF']['ApertureValue'] . "','" . @$exifData['EXIF']['ExposureBiasValue'] . "','" . @$exifData['EXIF']['MaxApertureValue'] . "','" . @$exifData['EXIF']['MeteringMode'] . "','" . @$exifData['EXIF']['LightSource'] . "',
	'" . @$exifData['EXIF']['Flash'] . "','" . @$exifData['EXIF']['FocalLength'] . "','" . @$exifData['EXIF']['ColorSpace'] . "','" . @$exifData['EXIF']['ExifImageWidth'] . "','" . @$exifData['EXIF']['ExifImageLength'] . "','" . @$exifData['EXIF']['FocalPlaneXResolution'] . "',
	'" . @$exifData['EXIF']['FocalPlaneYResolution'] . "','" . @$exifData['EXIF']['FocalPlaneResolutionUnit'] . "','" . @$exifData['EXIF']['CustomRendered'] . "','" . @$exifData['EXIF']['ExposureMode'] . "',
	'" . @$exifData['EXIF']['WhiteBalance']. "','" . @$exifData['EXIF']['SceneCaptureType']. "',
	'" . @$exifData['EXIF']['LensType']. "','" . @$exifData['EXIF']['LensId']. "','" . @$exifData['EXIF']['LensInfo']. "',
	'" . @$exifData['GPS']['GPSLatitudeRef']. "','" . @$exifData['GPS']['GPSLatitude']. "','" . @$exifData['GPS']['GPSLongitudeRef']. "','" . @$exifData['GPS']['GPSLongitude']. "')");

	// Rotate image
	if (!empty($exifData['IFD0']['Orientation']))
	{
		$imgDone = 0;
		if ($exifData['IFD0']['Orientation'] == 3)
		{
			// 180
			GalleryRotateImage($filename,180);
			$imgDone = 1;
		}

		if ($exifData['IFD0']['Orientation'] == 6)
		{
			// 270
			GalleryRotateImage($filename,270);
			$imgDone = 1;
		}


		if ($exifData['IFD0']['Orientation'] == 8)
		{
			// 90
			GalleryRotateImage($filename,90);
			$imgDone = 1;
		}

		if ($imgDone == 1)
		{
			 	// create thumbnails
				$dbresult = $smcFunc['db_query']('', "
				SELECT
					id_member, id_cat, user_id_cat, thumbfilename, mediumfilename,
					filename
				FROM {db_prefix}gallery_pic
				WHERE id_picture = $pictureid LIMIT 1");
			$row = $smcFunc['db_fetch_assoc']($dbresult);


					GalleryCreateThumbnail($filename , $modSettings['gallery_thumb_width'], $modSettings['gallery_thumb_height']);
					rename($modSettings['gallery_path'] . $row['filename']. '_thumb',  $modSettings['gallery_path'] . $row['thumbfilename']);
					@chmod($modSettings['gallery_path']  .  $row['thumbfilename'], 0755);

					if ($modSettings['gallery_make_medium'])
					{
						GalleryCreateThumbnail($filename , $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
						rename($modSettings['gallery_path'] . $row['filename'] . '_thumb',  $modSettings['gallery_path'] . $row['mediumfilename']);

						@chmod($modSettings['gallery_path'] . $row['mediumfilename'], 0755);

						// Check for Watermark
						DoWaterMark($modSettings['gallery_path'] . $row['mediumfilename']);
					}


		}

	}
}

function EXIFSettings()
{
	global $context, $mbname, $txt;

	isAllowedTo('smfgallery_manage');

	DoGalleryAdminTabs('adminset');

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_exif_settings'];
	$context['sub_template']  = 'gallery_exif';
}

function SaveEXIFSettings()
{
	isAllowedTo('smfgallery_manage');

	$enable_exif_on_display = isset($_REQUEST['enable_exif_on_display']) ? 1 : 0;

	$file_FileDateTime = isset($_REQUEST['file_FileDateTime']) ? 1 : 0;
	$show_file_FileSize = isset($_REQUEST['show_file_FileSize']) ? 1 : 0;
	$show_file_FileType = isset($_REQUEST['show_file_FileType']) ? 1 : 0;
	$show_file_MimeType = isset($_REQUEST['show_file_MimeType']) ? 1 : 0;
	$show_file_SectionsFound = isset($_REQUEST['show_file_SectionsFound']) ? 1 : 0;

	$show_computed_Height = isset($_REQUEST['show_computed_Height']) ? 1 : 0;
	$show_computed_Width = isset($_REQUEST['show_computed_Width']) ? 1 : 0;
	$show_computed_IsColor = isset($_REQUEST['show_computed_IsColor']) ? 1 : 0;
	$show_computed_CCDWidth = isset($_REQUEST['show_computed_CCDWidth']) ? 1 : 0;
	$show_computed_ApertureFNumber = isset($_REQUEST['show_computed_ApertureFNumber']) ? 1 : 0;
	$show_computed_Copyright = isset($_REQUEST['show_computed_Copyright']) ? 1 : 0;

	$show_file_MimeType = isset($_REQUEST['show_file_MimeType']) ? 1 : 0;
	$show_idfo_ImageDescription = isset($_REQUEST['show_idfo_ImageDescription']) ? 1 : 0;
	$show_idfo_Make = isset($_REQUEST['show_idfo_Make']) ? 1 : 0;
	$show_idfo_Model = isset($_REQUEST['show_idfo_Model']) ? 1 : 0;
	$show_idfo_Orientation = isset($_REQUEST['show_idfo_Orientation']) ? 1 : 0;
	$show_idfo_XResolution = isset($_REQUEST['show_idfo_XResolution']) ? 1 : 0;
	$show_idfo_YResolution = isset($_REQUEST['show_idfo_YResolution']) ? 1 : 0;
	$show_idfo_ResolutionUnit = isset($_REQUEST['show_idfo_ResolutionUnit']) ? 1 : 0;
	$show_idfo_Software = isset($_REQUEST['show_idfo_Software']) ? 1 : 0;
	$show_idfo_DateTime = isset($_REQUEST['show_idfo_DateTime']) ? 1 : 0;
	$show_idfo_Artist = isset($_REQUEST['show_idfo_Artist']) ? 1 : 0;

	$show_exif_ExposureTime = isset($_REQUEST['show_exif_ExposureTime']) ? 1 : 0;
	$show_exif_FNumber = isset($_REQUEST['show_exif_FNumber']) ? 1 : 0;
	$show_exif_ExposureProgram = isset($_REQUEST['show_exif_ExposureProgram']) ? 1 : 0;
	$show_exif_ISOSpeedRatings = isset($_REQUEST['show_exif_ISOSpeedRatings']) ? 1 : 0;
	$show_exif_ExifVersion = isset($_REQUEST['show_exif_ExifVersion']) ? 1 : 0;
	$show_exif_DateTimeOriginal= isset($_REQUEST['show_exif_DateTimeOriginal']) ? 1 : 0;
	$show_exif_DateTimeDigitized = isset($_REQUEST['show_exif_DateTimeDigitized']) ? 1 : 0;
	$show_exif_ShutterSpeedValue = isset($_REQUEST['show_exif_ShutterSpeedValue']) ? 1 : 0;
	$show_exif_ApertureValue = isset($_REQUEST['show_exif_ApertureValue']) ? 1 : 0;
	$show_exif_ExposureBiasValue = isset($_REQUEST['show_exif_ExposureBiasValue']) ? 1 : 0;
	$show_exif_MaxApertureValue = isset($_REQUEST['show_exif_MaxApertureValue']) ? 1 : 0;
	$show_exif_MeteringMode = isset($_REQUEST['show_exif_MeteringMode']) ? 1 : 0;
	$show_exif_LightSource = isset($_REQUEST['show_exif_LightSource']) ? 1 : 0;
	$show_exif_Flash = isset($_REQUEST['show_exif_Flash']) ? 1 : 0;
	$show_exif_FocalLength = isset($_REQUEST['show_exif_FocalLength']) ? 1 : 0;
	$show_exif_ColorSpace = isset($_REQUEST['show_exif_ColorSpace']) ? 1 : 0;
	$show_exif_ExifImageWidth = isset($_REQUEST['show_exif_ExifImageWidth']) ? 1 : 0;
	$show_exif_ExifImageLength = isset($_REQUEST['show_exif_ExifImageLength']) ? 1 : 0;
	$show_exif_FocalPlaneXResolution = isset($_REQUEST['show_exif_FocalPlaneXResolution']) ? 1 : 0;
	$show_exif_FocalPlaneYResolution = isset($_REQUEST['show_exif_FocalPlaneYResolution']) ? 1 : 0;
	$show_exif_FocalPlaneResolutionUnit = isset($_REQUEST['show_exif_FocalPlaneResolutionUnit']) ? 1 : 0;
	$show_exif_CustomRendered = isset($_REQUEST['show_exif_CustomRendered']) ? 1 : 0;
	$show_exif_ExposureMode = isset($_REQUEST['show_exif_ExposureMode']) ? 1 : 0;
	$show_exif_WhiteBalance = isset($_REQUEST['show_exif_WhiteBalance']) ? 1 : 0;
	$show_exif_SceneCaptureType = isset($_REQUEST['show_exif_SceneCaptureType']) ? 1 : 0;
	$show_exif_lenstype = isset($_REQUEST['show_exif_lenstype']) ? 1 : 0;
	$show_exif_lensinfo  = isset($_REQUEST['show_exif_lensinfo']) ? 1 : 0;
	$show_exif_lensid  = isset($_REQUEST['show_exif_lensid']) ? 1 : 0;

	$show_gps_latituderef  = isset($_REQUEST['show_gps_latituderef']) ? 1 : 0;
	$show_gps_latitude  = isset($_REQUEST['show_gps_latitude']) ? 1 : 0;
	$show_gps_longituderef   = isset($_REQUEST['show_gps_longituderef']) ? 1 : 0;
	$show_gps_longitude   = isset($_REQUEST['show_gps_longitude']) ? 1 : 0;

	// Save EXIF Settings
	UpdateGallerySettings(
		array(
	'enable_exif_on_display' => $enable_exif_on_display,

	'show_gps_latituderef' => $show_gps_latituderef,
	'show_gps_latitude' => $show_gps_latitude,
	'show_gps_longituderef' => $show_gps_longituderef,
	'show_gps_longitude' => $show_gps_longitude,

	'file_FileDateTime' => $file_FileDateTime,
	'show_file_FileSize' => $show_file_FileSize,
	'show_file_FileType' => $show_file_FileType,
	'show_file_MimeType' => $show_file_MimeType,
	'show_file_SectionsFound' =>  $show_file_SectionsFound,

	'show_computed_Height' => $show_computed_Height,
	'show_computed_Width' => $show_computed_Width,
	'show_computed_IsColor' => $show_computed_IsColor,
	'show_computed_CCDWidth' => $show_computed_CCDWidth,
	'show_computed_ApertureFNumber' => $show_computed_ApertureFNumber,
	'show_computed_Copyright' => $show_computed_Copyright,

	'show_idfo_ImageDescription' => $show_idfo_ImageDescription,
	'show_idfo_Make' => $show_idfo_Make,
	'show_idfo_Model' => $show_idfo_Model,
	'show_idfo_Orientation' => $show_idfo_Orientation,
	'show_idfo_XResolution' => $show_idfo_XResolution,
	'show_idfo_YResolution' => $show_idfo_YResolution,
	'show_idfo_ResolutionUnit' => $show_idfo_ResolutionUnit,
	'show_idfo_Software' => $show_idfo_Software,
	'show_idfo_DateTime' => $show_idfo_DateTime,
	'show_idfo_Artist' => $show_idfo_Artist,

	'show_exif_ExposureTime' => $show_exif_ExposureTime,
	'show_exif_FNumber' => $show_exif_FNumber,
	'show_exif_ExposureProgram' => $show_exif_ExposureProgram,
	'show_exif_ISOSpeedRatings' => $show_exif_ISOSpeedRatings,
	'show_exif_ExifVersion' => $show_exif_ExifVersion,
	'show_exif_DateTimeOriginal' => $show_exif_DateTimeOriginal,
	'show_exif_DateTimeDigitized' => $show_exif_DateTimeDigitized,
	'show_exif_ShutterSpeedValue' => $show_exif_ShutterSpeedValue,
	'show_exif_ApertureValue' => $show_exif_ApertureValue,
	'show_exif_ExposureBiasValue' => $show_exif_ExposureBiasValue,
	'show_exif_MaxApertureValue' => $show_exif_MaxApertureValue,
	'show_exif_MeteringMode' => $show_exif_MeteringMode,
	'show_exif_LightSource' => $show_exif_LightSource,
	'show_exif_Flash' => $show_exif_Flash,
	'show_exif_FocalLength' => $show_exif_FocalLength,
	'show_exif_ColorSpace' => $show_exif_ColorSpace,
	'show_exif_ExifImageWidth' => $show_exif_ExifImageWidth,
	'show_exif_ExifImageLength' => $show_exif_ExifImageLength,
	'show_exif_FocalPlaneXResolution' => $show_exif_FocalPlaneXResolution,
	'show_exif_FocalPlaneYResolution' => $show_exif_FocalPlaneYResolution,
	'show_exif_FocalPlaneResolutionUnit' => $show_exif_FocalPlaneResolutionUnit,
	'show_exif_CustomRendered' => $show_exif_CustomRendered,
	'show_exif_ExposureMode' => $show_exif_ExposureMode,
	'show_exif_WhiteBalance' => $show_exif_WhiteBalance,
	'show_exif_SceneCaptureType' => $show_exif_SceneCaptureType,
	'show_exif_lenstype' =>$show_exif_lenstype,
	'show_exif_lensinfo' =>$show_exif_lensinfo,
	'show_exif_lensid' =>$show_exif_lensid,

		)
	);

	redirectexit('action=admin;area=gallery;sa=exifsettings');
}

function LoadGallerySettings()
{
	global $gallerySettings, $smcFunc, $modSettings;

	if (($gallerySettings = cache_get_data('gallerySettings', 90)) == null)
	{
		$dbresult = $smcFunc['db_query']('', "
			SELECT
				variable, value
			FROM {db_prefix}gallery_settings");

		$gallerySettings = array();
		while ($row = $smcFunc['db_fetch_row']($dbresult))
			$gallerySettings[$row[0]] = $row[1];
		$smcFunc['db_free_result']($dbresult);

		// Check if cache is enabled
		if (!empty($modSettings['cache_enable']))
			cache_put_data('gallerySettings', $gallerySettings, 90);
	}
}

function UpdateGallerySettings($changeArray)
{
	global $smcFunc, $gallerySettings;

	if (empty($changeArray) || !is_array($changeArray))
		return;

	$replaceArray = array();
	foreach ($changeArray as $variable => $value)
	{
		// Don't bother if it's already like that ;).
		if (isset($gallerySettings[$variable]) && $gallerySettings[$variable] == stripslashes($value))
			continue;
		// If the variable isn't set, but would only be set to nothing'ness, then don't bother setting it.
		elseif (!isset($gallerySettings[$variable]) && empty($value))
			continue;

		$replaceArray[] = "(SUBSTRING('$variable', 1, 255), SUBSTRING('$value', 1, 65534))";
		$gallerySettings[$variable] = stripslashes($value);
	}

	if (empty($replaceArray))
		return;

	$smcFunc['db_query']('substring', "
		REPLACE INTO {db_prefix}gallery_settings
			(variable, value)
		VALUES " . implode(',
			', $replaceArray));

	cache_put_data('gallerySettings', null, 90);
}

function ProcessAllPicturesEXIFData()
{
	global $smcFunc;

	isAllowedTo('smfgallery_manage');

	// Increase the max time to process the exif data
	@ini_set('max_execution_time', '1500');

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.filename
		FROM {db_prefix}gallery_pic as p");
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		ProcessEXIFData($row['filename'], $row['id_picture']);
	}
	$smcFunc['db_free_result']($dbresult);

	redirectexit('action=gallery;sa=exifsettings');
}

function ViewViewers()
{
	global $txt, $context, $smcFunc, $scripturl;

	isAllowedTo('smfgallery_manage');


	$pic = (int) $_REQUEST['pic'];

	$context['gallery_pic_id'] = $pic;

	$context['page_title'] =  $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_viewers'];

	$context['gallery_viewers'] = '';
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			v.id_member, m.real_name
		FROM {db_prefix}gallery_log_mark_view AS v, {db_prefix}members AS m
		WHERE m.id_member = v.id_member AND v.id_picture = $pic
		");
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_viewers'] .= ' <a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">'  . $row['real_name'] . '</a>';
	}
	$smcFunc['db_free_result']($dbresult);

	$context['sub_template']  = 'viewers';

    $context['linktree'][] = array(
			'name' => $txt['gallery_txt_viewers']
		);

}

function UnviewedItems()
{
	global $txt, $context, $smcFunc, $mbname, $modSettings, $scripturl, $user_info, $options;

	// No guests allowed
	is_not_guest();

	$dateFilter = '';


	if (isset($options['gallery_markviewedtime']))
	{
		$markViewedTime = (int) $options['gallery_markviewedtime'];

		if (!empty($markViewedTime))
		{
			$dateFilter = "p.date > $markViewedTime AND ";
		}
	}



	// Check Permission
	isAllowedTo('smfgallery_view');


	if (!$context['user']['is_guest'])
		$groupsdata = implode(',',$user_info['groups']);
	else
		$groupsdata = -1;

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_unviewed_images'];
	$context['sub_template']  = 'unviewed';

   	$context['linktree'][] = array(
			'name' => $txt['gallery_txt_unviewed_images']
		);


	$context['start'] = (int) $_REQUEST['start'];
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) as total
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.ID_GROUP IN ($groupsdata) AND c.ID_CAT = p.ID_CAT)
		LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $user_info['id']  . " AND v.user_id_cat = p.user_id_cat)
		LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
		LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)

		WHERE $dateFilter v.id_picture IS NULL AND  (((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 AND p.approved =1) OR (p.approved =1 AND p.USER_ID_CAT =0 AND (c.view IS NULL OR c.view =1)))
		 ");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$total = $row['total'];
	$smcFunc['db_free_result']($dbresult);

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.totalratings, p.rating, p.commenttotal, p.filesize, p.views, p.thumbfilename,
			p.title, p.id_member, m.real_name, p.date, p.description, c.view, p.mature, mg.online_color,
			p.totallikes
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
        LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.ID_GROUP IN ($groupsdata) AND c.ID_CAT = p.ID_CAT)
		LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $user_info['id'] . " AND v.user_id_cat = p.user_id_cat)
		LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
		WHERE $dateFilter v.id_picture IS NULL AND (((s.private =0 OR s.private IS NULL ) AND (s.password = '' OR s.password IS NULL )  AND p.USER_ID_CAT !=0 AND p.approved =1) OR (p.approved =1 AND p.USER_ID_CAT =0 AND (c.view IS NULL OR c.view =1)))
		GROUP by p.id_picture  ORDER BY p.id_picture DESC
		LIMIT $context[start]," . $modSettings['gallery_set_images_per_page']);
	$context['gallery_pics'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
		$context['gallery_pics'][] = $row;
	$smcFunc['db_free_result']($dbresult);

	$context['page_index'] = constructPageIndex($scripturl . '?action=gallery;sa=unviewed', $_REQUEST['start'], $total, $modSettings['gallery_set_images_per_page']);


}

function ShowRSSFeed()
{
	global $txt, $smcFunc, $gallerySettings, $scripturl, $modSettings;

	// Check RSS is enabled
	if (empty($gallerySettings['gallery_enable_rss']))
		exit;

	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']))
		@ob_start('ob_gzhandler');
	else
		ob_start();

	isAllowedTo('smfgallery_view');

	if (!isset($_REQUEST['cat']) && !isset($_REQUEST['usercat']))
		fatal_error($txt['gallery_error_no_cat']);

	if (isset($_REQUEST['cat']))
	{
		$cat = (int) $_REQUEST['cat'];

		GetCatPermission($cat,'view');
	}
	// Show the feed
	header("Content-Type: application/xml; charset=ISO-8859-1");



	echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
	echo '<rss version="2.0" xml:lang="', strtr($txt['lang_locale'], '_', '-'), '"  xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
	<atom:link href="', $scripturl, '?action=gallery;sa=rss" rel="self" type="application/rss+xml" />
	<link>', $scripturl, '?action=gallery;sa=rss</link>
	<description></description>
';


	if (isset($_REQUEST['cat']))
	{
		$result = $smcFunc['db_query']('', "
		SELECT title FROM
		 {db_prefix}gallery_cat
		WHERE ID_CAT = " . $cat);
		$row = $smcFunc['db_fetch_assoc']($result);
		$smcFunc['db_free_result']($result);

		if (!empty($row['title']))
			echo '
		<title>' . $row['title'] . '</title>';


		$result = $smcFunc['db_query']('', "
		SELECT description, title,id_picture,date,thumbfilename  FROM
		 {db_prefix}gallery_pic
		WHERE approved = 1  AND ID_CAT = $cat
		ORDER BY id_picture DESC LIMIT " . 10);

		while($row = $smcFunc['db_fetch_assoc']($result))
		{
			echo '<item>
			<title><![CDATA[', $row['title'], ']]></title>
			<pubDate>',gmdate('D, d M Y H:i:s \G\M\T', $row['date']),'</pubDate>
			<description>
			<![CDATA[<img src="' . $modSettings['gallery_url'] . $row['thumbfilename'] . '" border="0" align="left" alt="" title="" />',$row['description'],']]>
			</description>
			<link>', $scripturl, '?action=gallery;sa=view;id=',$row['id_picture'],'</link>

			</item>';
		}

		$smcFunc['db_free_result']($result);
	}

	if (isset($_REQUEST['usercat']))
	{
		$cat = $_REQUEST['usercat'];
		$result = $smcFunc['db_query']('', "
		SELECT title FROM
		 {db_prefix}gallery_usercat
		WHERE USER_ID_CAT = " . $cat);
		$row = $smcFunc['db_fetch_assoc']($result);
		$smcFunc['db_free_result']($result);

			echo '
		<title>' . $row['title'] . '</title>';


		$result = $smcFunc['db_query']('', "
		SELECT description, title,id_picture,date,thumbfilename  FROM
		 {db_prefix}gallery_pic
		WHERE approved = 1  AND USER_ID_CAT = $cat
		ORDER BY id_picture DESC LIMIT " . 10);

		while($row = $smcFunc['db_fetch_assoc']($result))
		{
			echo '<item>
			<title><![CDATA[', $row['title'], ']]></title>
			<pubDate>',gmdate('D, d M Y H:i:s \G\M\T', $row['date']),'</pubDate>
			<description>
			<![CDATA[<img src="' . $modSettings['gallery_url'] . $row['thumbfilename'] . '" border="0" align="left" alt="" title="" />',$row['description'],']]>
			</description>
			<link>', $scripturl, '?action=gallery;sa=view;id=',$row['id_picture'],'</link>

			</item>';
		}

		$smcFunc['db_free_result']($result);


	}


	echo '</channel>';
	echo '</rss>';

	obExit(false);

	die("");

}

function MarkMature()
{

	$id = (int) $_REQUEST['id'];

	if (isset($_REQUEST['submit_yes']))
	{
		$_SESSION['mature_ok'] = 1;
		redirectexit('action=gallery;sa=view;id=' . $id);
	}
	else
	{
		// Redirect the user back
		redirectexit('action=gallery');
	}
}

function CanViewMature()
{
	if (isset($_SESSION['mature_ok']))
		return true;
	else
		return false;
}

function StartSlideshow()
{
	global $txt, $context, $mbname, $smcFunc, $boardurl;

	isAllowedTo('smfgallery_view');

	$id = (int) $_REQUEST['id'];

	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	$context['gallery_pic_id'] = $id;


	// Get pic information
    $dbresult = $smcFunc['db_query']('', "
    SELECT p.id_picture,  p.USER_ID_CAT, p.ID_CAT
       FROM {db_prefix}gallery_pic as p
       WHERE p.id_picture = $id  LIMIT 1");
    $picRow = $smcFunc['db_fetch_assoc']($dbresult);
    $smcFunc['db_free_result']($dbresult);

	// Check if picture exists
	if ($smcFunc['db_affected_rows']()== 0)
		fatal_error($txt['gallery_error_no_pictureexist'],false);

    // Check permission
    if (!empty($picRow['ID_CAT']))
    	GetCatPermission($picRow['ID_CAT'],'view');

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.totalratings, p.rating, p.commenttotal, p.filesize, p.views, p.thumbfilename, p.filename, p.title, p.id_member, m.real_name, mg.online_color,
			p.date, p.description, p.mature, p.totallikes
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
        LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		WHERE  p.ID_CAT = " . $picRow['ID_CAT'] . " AND p.USER_ID_CAT = " . $picRow['USER_ID_CAT'] . "  AND p.id_picture <= $id AND p.approved = 1 ORDER BY p.id_picture DESC
		LIMIT 50");
	$context['gallery_pics'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
    	$context['gallery_pics'][] = $row;
    $smcFunc['db_free_result']($dbresult);

    // Default interval of 5 seconds
    if (isset($_REQUEST['interval']))
    	$interval = (int) $_REQUEST['interval'];
    else
		$interval = 5000;

	// Load the headers
	$context['html_headers'] .= '
	<script type="text/javascript" src="' . $boardurl . '/highslide/highslide-full.packed.js"></script>
	<script type="text/javascript">
    hs.graphicsDir = \'' . $boardurl . '/highslide/graphics/\';
    hs.outlineType = \'rounded-white\';
    hs.showCredits = false;
    hs.align = \'center\';
	hs.addSlideshow({
	// slideshowGroup: \'group1\',
	interval:' . $interval . ',
	repeat: true,
	useControls: true,
	fixedControls: true,
	overlayOptions: {
		opacity: .6,
		position: \'top center\',
		hideOnMouseOut: true
	}

});

// Optional: a crossfade transition looks good with the slideshow
hs.transitions = [\'expand\', \'crossfade\'];
	</script>
<style type="text/css">

.highslide {
	cursor: url(' . $boardurl . '/highslide/graphics/zoomin.cur), pointer;
    outline: none;
}

.highslide:hover img {
	border: 2px solid white;
}

.highslide-image {
    border: 2px solid white;
}
.highslide-image-blur {
}
.highslide-caption {
    display: none;

    border: 2px solid white;
    border-top: none;
    font-family: Verdana, Helvetica;
    font-size: 10pt;
    padding: 5px;
    background-color: white;
}
.highslide-display-block {
    display: block;
}
.highslide-display-none {
    display: none;
}
.highslide-loading {
    display: block;
	color: white;
	font-size: 9px;
	font-weight: bold;
	text-transform: uppercase;
    text-decoration: none;
	padding: 3px;
	border-top: 1px solid white;
	border-bottom: 1px solid white;
    background-color: black;
    /*
    padding-left: 22px;
    background-image: url(' . $boardurl . '/highslide/graphics/loader.gif);
    background-repeat: no-repeat;
    background-position: 3px 1px;
    */
}
a.highslide-credits,
a.highslide-credits i {
    padding: 2px;
    color: silver;
    text-decoration: none;
	font-size: 10px;
}
a.highslide-credits:hover,
a.highslide-credits:hover i {
    color: white;
    background-color: gray;
}
a.highslide-full-expand {
	background: url(' . $boardurl . '/highslide/graphics/fullexpand.gif) no-repeat;
	display: block;
	margin: 0 10px 10px 0;
	width: 34px;
	height: 34px;
}
.highslide-controls {
	width: 195px;
	height: 40px;
	background: url(' . $boardurl . '/highslide/graphics/controlbar-black-border.gif) 0 -90px no-repeat;
	margin-right: 15px;
	margin-bottom: 10px;
	margin-top: 20px;
}
.highslide-controls ul {
	position: relative;
	left: 15px;
	height: 40px;
	list-style: none;
	margin: 0;
	padding: 0;
	background: url(' . $boardurl . '/highslide/graphics/controlbar-black-border.gif) right -90px no-repeat;
}
.highslide-controls li {
	float: left;
	padding: 5px 0;
}
.highslide-controls a {
	background: url(' . $boardurl . '/highslide/graphics/controlbar-black-border.gif);
	display: block;
	float: left;
	height: 30px;
	width: 30px;
	outline: none;
}
.highslide-controls a.disabled {
	cursor: default;
}
.highslide-controls a span {
	/* hide the text for these graphic buttons */
	display: none;
}

/* The CSS sprites for the controlbar */
.highslide-controls .highslide-previous a {
	background-position: 0 0;
}
.highslide-controls .highslide-previous a:hover {
	background-position: 0 -30px;
}
.highslide-controls .highslide-previous a.disabled {
	background-position: 0 -60px !important;
}
.highslide-controls .highslide-play a {
	background-position: -30px 0;
}
.highslide-controls .highslide-play a:hover {
	background-position: -30px -30px;
}
.highslide-controls .highslide-play a.disabled {
	background-position: -30px -60px !important;
}
.highslide-controls .highslide-pause a {
	background-position: -60px 0;
}
.highslide-controls .highslide-pause a:hover {
	background-position: -60px -30px;
}
.highslide-controls .highslide-next a {
	background-position: -90px 0;
}
.highslide-controls .highslide-next a:hover {
	background-position: -90px -30px;
}
.highslide-controls .highslide-next a.disabled {
	background-position: -90px -60px !important;
}
.highslide-controls .highslide-move a {
	background-position: -120px 0;
}
.highslide-controls .highslide-move a:hover {
	background-position: -120px -30px;
}
.highslide-controls .highslide-full-expand a {
	background-position: -150px 0;
}
.highslide-controls .highslide-full-expand a:hover {
	background-position: -150px -30px;
}
.highslide-controls .highslide-full-expand a.disabled {
	background-position: -150px -60px !important;
}
.highslide-controls .highslide-close a {
	background-position: -180px 0;
}
.highslide-controls .highslide-close a:hover {
	background-position: -180px -30px;
}
</style>

	';


	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_slideshow'];
	$context['sub_template']  = 'slideshow';

    $context['linktree'][] = array(
			'name' =>  $txt['gallery_txt_slideshow']
		);

}


function DoToolBarStrip($button_strip, $direction )
{
	global $settings, $txt, $context;


	if ($context['gallery21beta'] == true)
	{
	    	echo template_button_strip($button_strip, $direction);

	}
	else
	{

		if (!empty($settings['use_tabs']))
		{

			template_button_strip($button_strip, $direction);

		}
		else
		{
				foreach ($button_strip as $tab)
				{

					echo '
								<a href="', $tab['url'], '">', $txt[$tab['text']], '</a>';

					if (empty($tab['is_last']))
						echo ' | ';
				}

		}
	}

}

function GetParentLink($ID_CAT)
{
	global $smcFunc, $context, $scripturl;

	if ($ID_CAT == 0)
		return;

	$dbresult1 = $smcFunc['db_query']('', "
		SELECT
			ID_PARENT,title
		FROM {db_prefix}gallery_cat
		WHERE ID_CAT = $ID_CAT LIMIT 1");
	$row1 = $smcFunc['db_fetch_assoc']($dbresult1);

	$smcFunc['db_free_result']($dbresult1);

	GetParentLink($row1['ID_PARENT']);

	$context['linktree'][] = array(
					'url' => $scripturl . '?action=gallery;cat=' . $ID_CAT ,
					'name' => $row1['title']
				);
}

function GetUserParentLink($USER_ID_CAT, $memberID)
{
	global $smcFunc, $context, $scripturl;

	if ($USER_ID_CAT == 0)
		return;

	$dbresult1 = $smcFunc['db_query']('', "
		SELECT
			ID_PARENT,title
		FROM {db_prefix}gallery_usercat
		WHERE id_member = $memberID AND USER_ID_CAT = $USER_ID_CAT LIMIT 1");
		$row1 = $smcFunc['db_fetch_assoc']($dbresult1);

		$smcFunc['db_free_result']($dbresult1);

	GetUserParentLink($row1['ID_PARENT'], $memberID);

	$context['linktree'][] = array(
					'url' => $scripturl . '?action=gallery;su=user;cat=' . $USER_ID_CAT . ';u=' . $memberID ,
					'name' => $row1['title']
				);
}

function UpdateMessagePost($ID_TOPIC, $gallery_pic_id)
{
	global $sourcedir, $smcFunc, $user_info, $modSettings, $scripturl;

	   $dbresult = $smcFunc['db_query']('', "
    SELECT
    	id_member, ID_CAT, title, description, thumbfilename, filename,
    	width, height
    FROM {db_prefix}gallery_pic
    WHERE id_picture = $gallery_pic_id LIMIT 1");
	$picRow = $smcFunc['db_fetch_assoc']( $dbresult);
	$smcFunc['db_free_result']($dbresult);

	$title = $picRow['title'];
	$description = $picRow['description'];
	$sizes = array();
	$sizes[1] = $picRow['height'];
	$sizes[0] = $picRow['width'];

	// Create the post
		require_once($sourcedir . '/Subs-Post.php');

			$dbMsgResult = $smcFunc['db_query']('', "
							SELECT
								ID_FIRST_MSG, locked, ID_BOARD
							FROM {db_prefix}topics
							WHERE ID_TOPIC = " . $ID_TOPIC);
							$msgRow = $smcFunc['db_fetch_assoc']($dbMsgResult);
							$smcFunc['db_free_result']($dbMsgResult);

						$dbresult = $smcFunc['db_query']('', "
		SELECT
			ID_BOARD, postingsize, locktopic, showpostlink, locked, id_topic, tweet_items
		FROM {db_prefix}gallery_cat
		WHERE ID_CAT = " . $picRow['ID_CAT']);
		$rowcat = $smcFunc['db_fetch_assoc']($dbresult);
		$smcFunc['db_free_result']($dbresult);

        if (!empty($rowcat['id_topic']))
            return;

		$extraheightwidth = '';
					if ($rowcat['postingsize'] == 1)
					{
						$postimg = $picRow['filename'];
						$extraheightwidth = " height={$sizes[1]} width={$sizes[0]}";
					}
					else
						$postimg = $picRow['thumbfilename'];

					if ($rowcat['showpostlink'] == 1)
						$showpostlink = "\n\n" . $scripturl . '?action=gallery;sa=view;id=' . $gallery_pic_id;
					else
						$showpostlink = '';


					$msgOptions = array(
						'id' => $msgRow['ID_FIRST_MSG'],
						'subject' => $title,
						'body' => '[b]' . $title . "[/b]\n\n[img$extraheightwidth]" . $modSettings['gallery_url']  . $postimg . "[/img]$showpostlink\n\n$description",
						'icon' => 'xx',
						'smileys_enabled' => 1,
						'attachments' => array(),
					);
					$topicOptions = array(
						'id' => $ID_TOPIC,
						'board' => $rowcat['ID_BOARD'],
						'poll' => null,
						'lock_mode' => $rowcat['locktopic'],
						'sticky_mode' => null,
						'mark_as_read' => true,
					);
					$posterOptions = array(
						'id' => $picRow['id_member'],
						'update_post_count' => !$user_info['is_guest'],
					);
					// Fix height & width of posted image in message

					$msgOptions['modify_time'] = time();
					$msgOptions['modify_name'] = addslashes($user_info['name']);

					if (function_exists("set_tld_regex"))
                        $msgOptions['modify_reason'] = '';

					if (!empty($msgRow['ID_FIRST_MSG']))
						modifyPost($msgOptions, $topicOptions, $posterOptions);
}

function ShowHelp($helpid = '')
{
	global $txt, $settings, $scripturl;

	return '<a href="' . $scripturl . '?action=helpadmin;help=' .$helpid.'" onclick="return reqWin(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.gif" alt="' .  $txt['gallery_txt_help'] . '" align="top" /></a>&nbsp;';
}

function UpdateMemberPictureTotals($memberID)
{
	global $smcFunc;
	// Don't update anything for guests!!!
	if ($memberID == 0)
		return;

	$result = $smcFunc['db_query']('',"
		SELECT
			count(*) as total
		FROM {db_prefix}gallery_pic
		WHERE approved = 1 AND id_member = $memberID");

	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$smcFunc['db_query']('',"UPDATE {db_prefix}members SET gallerypic_total = " . $row['total'] . " WHERE id_member = $memberID LIMIT 1");
	}


}

function CatPermEdit()
{
	global $txt, $context, $mbname, $smcFunc;

	isAllowedTo('smfgallery_manage');


	$context['sub_template']  = 'catpermedit';

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_text_edit_permissions'];

	$id = (int) $_REQUEST['id'];

	$context['gallery_perm_id'] = $id;

 	$result = $smcFunc['db_query']('',"
		SELECT
			ID_GROUP, view, addpic, editpic, delpic, addcomment, addvideo, viewimagedetail, autoapprove
		FROM {db_prefix}gallery_catperm
		WHERE ID = " . $context['gallery_perm_id'] . "
		");
    $context['gallery_catperm_edit'] = $smcFunc['db_fetch_assoc']($result);
}

function CatPermEdit2()
{
	global $txt, $context, $smcFunc;

	isAllowedTo('smfgallery_manage');

	$permid = (int) $_REQUEST['permid'];

	// Permissions
	$view = isset($_REQUEST['view']) ? 1 : 0;
	$add = isset($_REQUEST['add']) ? 1 : 0;
	$edit = isset($_REQUEST['edit']) ? 1 : 0;
	$delete = isset($_REQUEST['delete']) ? 1 : 0;
	$addcomment = isset($_REQUEST['addcomment']) ? 1 : 0;
	$addvideo = isset($_REQUEST['addvideo']) ? 1 : 0;
    $viewimagedetail = isset($_REQUEST['viewimagedetail']) ? 1 : 0;
    $autoapprove = isset($_REQUEST['autoapprove']) ? 1 : 0;

	// Update the permission database
	$smcFunc['db_query']('',"UPDATE {db_prefix}gallery_catperm
	SET view = $view, addpic = $add, editpic = $edit, delpic = $delete,
	addcomment = $addcomment, addvideo = $addvideo, viewimagedetail = $viewimagedetail, autoapprove = $autoapprove
		WHERE ID = $permid
		");

	// Get the category id
		$result = $smcFunc['db_query']('',"
		SELECT
			ID_CAT
		FROM {db_prefix}gallery_catperm
		WHERE ID = $permid
		");
	$catRow = $smcFunc['db_fetch_assoc']($result);



	// Redirect to the category permission page
	redirectexit('action=gallery;sa=catperm;cat=' . $catRow['ID_CAT']);
}

function CatPermCopy()
{

	isAllowedTo('smfgallery_manage');

	$copycat = (int) $_REQUEST['copycat'];
	$cat = (int) $_REQUEST['cat'];

	CopyCatPermissions($cat, $copycat);

	// Redirect to the category permission page
	redirectexit('action=gallery;sa=catperm;cat=' . $cat);

}

function CopyCatPermissions($cat, $copycat)
{
	global $smcFunc;

	if (!empty($cat))
	{
		// Get all the permissions
		$result = $smcFunc['db_query']('',"
		SELECT
			ID_GROUP, view, addpic, editpic, delpic, addcomment, addvideo, viewimagedetail, autoapprove
		FROM {db_prefix}gallery_catperm
		WHERE ID_CAT = $copycat
		");
		$dataArray = array();
		while($row = $smcFunc['db_fetch_assoc']($result))
		{
			$dataArray[] = $row;
		}


		foreach($dataArray as $row)
		{
			// Check if that group already exists for the category if so skip it
			$result = $smcFunc['db_query']('',"
			SELECT
				ID_GROUP, view, addpic, editpic, delpic, addcomment, addvideo, viewimagedetail,autoapprove
			FROM {db_prefix}gallery_catperm
			WHERE ID_CAT = $cat AND ID_GROUP = " . $row['ID_GROUP']
			);
			$rowCount = $smcFunc['db_num_rows']($result);

			if ($rowCount > 0)
				continue;


			// Insert the records
			$smcFunc['db_query']('',"INSERT INTO {db_prefix}gallery_catperm
					(ID_GROUP,ID_CAT,view,addpic,editpic,delpic,addcomment, addvideo, viewimagedetail,autoapprove )
				VALUES (" .  $row['ID_GROUP'] . ",$cat," .  $row['view'] . "," .  $row['addpic'] . ",
				" .  $row['editpic'] . "," .  $row['delpic'] . "," .  $row['addcomment'] . "," .  $row['addvideo'] . "," . $row['viewimagedetail'] . "," . $row['autoapprove'] . ")");


		}

	}
}

function GalleryRotateImage($filename, $degrees)
{
	$degrees = (int)  $degrees;

	// Check for crazy degree numbers;
	if ($degrees <= 0)
		return;
	if ($degrees > 360)
		return;

	@ini_set('max_execution_time', '300');
	@ini_set("memory_limit","512M");


	$sizes = getimagesize($filename);
	if ($sizes === false)
		return;

	$default_formats = array(
		'1' => 'gif',
		'2' => 'jpeg',
		'3' => 'png',
		'6' => 'bmp',
		'15' => 'wbmp'
	);

		// If we have to handle a gif, we might be able to... but maybe not :/.
	if ($sizes[2] == 1 && !function_exists('imagecreatefromgif') && function_exists('imagecreatefrompng'))
	{
		// Try out a temporary file, if possible...
		if ($img = @gif_loadFile($filename) && gif_outputAsPng($img, $filename))
			if ($src_img = imagecreatefrompng($filename))
			{
				$src_img = imagerotate($src_img, $degrees, 0);

				imagepng($src_img,$filename);


			}
	}
	// Or is it one of the formats supported above?
	elseif (isset($default_formats[$sizes[2]]) && function_exists('imagecreatefrom' . $default_formats[$sizes[2]]))
	{
		$imagecreatefrom = 'imagecreatefrom' . $default_formats[$sizes[2]];
		if ($src_img = @$imagecreatefrom($filename))
		{
			  $src_img = imagerotate($src_img, $degrees, 0);

			  $imagemake = 'image' . $default_formats[$sizes[2]];

			  @$imagemake($src_img, $filename);
		}
	}

}

function ToggleWatch()
{
	global $smcFunc, $user_info, $context, $txt;

	is_not_guest();

	isAllowedTo('smfgallery_view');

	if (isset($_REQUEST['author']))
	{
		$authorID = (int) $_REQUEST['author'];

		if ($authorID == $user_info['id'])
			fatal_error($txt['gallery_err_msg_follow_user'], false);

		// Check if the member is already watching this user.
		$result = $smcFunc['db_query']('',"
		SELECT
			COUNT(*) as total
		FROM {db_prefix}gallery_notifyupdates
		WHERE id_member = " . $user_info['id'] . "  AND id_author = $authorID
	");
		$row = $smcFunc['db_fetch_assoc']($result);

		// If no entries found watch the user
		if ($row['total'] == 0)
		{
			$smcFunc['db_query']('',"INSERT INTO {db_prefix}gallery_notifyupdates
			(id_author, id_member)
			VALUES('$authorID', '" . $user_info['id'] . "')");

		}
		else
		{
			// Delete the entry
			$smcFunc['db_query']('',"
			DELETE FROM {db_prefix}gallery_notifyupdates
			WHERE id_member = " . $user_info['id'] . " AND id_author = $authorID");
		}

		// Redirect to a view picture or main gallery index
		if (!isset($_REQUEST['id']) || empty($_REQUEST['id']))
		{
			redirectexit('action=gallery');
		}
		else
		{
			$id = (int) $_REQUEST['id'];
			redirectexit('action=gallery;sa=view;id=' . $id);
		}

	}
	else
	{


		$memid = (int) $_REQUEST['memid'];

		if ($memid == $user_info['id'])
			fatal_error($txt['gallery_err_msg_follow_user'], false);

		$context['gallery_author_id'] = $memid;

		$result = $smcFunc['db_query']('',"
		SELECT
			real_name
		FROM {db_prefix}members
		WHERE id_member = $memid");
		$memRow = $smcFunc['db_fetch_assoc']($result);
		$smcFunc['db_free_result']($result);

		$result = $smcFunc['db_query']('',"
		SELECT
			COUNT(*) as total
		FROM {db_prefix}gallery_notifyupdates
		WHERE id_member = " . $user_info['id'] . " AND id_author = $memid
	");
		$row = $smcFunc['db_fetch_assoc']($result);
		$smcFunc['db_free_result']($result);


		if ($row['total'] == 0)
		{
			$context['page_title'] = $txt['gallery_txt_follow_user'];
			$context['gallery_watch_message'] = $txt['gallery_txt_msg_follow_user'];
		}
		else
		{
			$context['page_title'] = $txt['gallery_txt_unfollow_user'];
			$context['gallery_watch_message'] = $txt['gallery_txt_msg_nofollow_user'];
		}

		$context['gallery_watch_message'] = str_replace("%membername",	$memRow['real_name'], $context['gallery_watch_message']);


		if (isset($_REQUEST['id']) && !empty($_REQUEST['id']))
		{
			$id = (int) $_REQUEST['id'];
			$context['gallery_return_id'] = $id;
		}
		else
			$context['gallery_return_id'] = '';


		$context['sub_template'] = 'follow_member';

	}


}

function SendMemberWatchNotifications($authorID, $message)
{
	global $smcFunc, $sourcedir, $txt, $scripturl;

	if (empty($authorID))
		return;

	require_once($sourcedir . '/Subs-Post.php');

	// Get all the people to send notifications too
	$result = $smcFunc['db_query']('',"
	SELECT  n.id_author, mem.email_address, m.real_name
		FROM ({db_prefix}gallery_notifyupdates AS n, {db_prefix}members as m)
		LEFT JOIN  {db_prefix}members AS mem on (mem.id_member = n.id_member)
	WHERE m.id_member = n.id_author AND n.id_author = $authorID
	");
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
	   	   if (empty($row['email_address']))
                continue;

		// Send email notification
		sendmail($row['email_address'], $row['real_name'] . ' ' . $txt['gallery_txt_gallery_new_upload'],

		$row['real_name'] . $txt['gallery_txt_gallery_new_upload_message'] . $message . $txt['gallery_txt_gallery_new_upload_message2'] . $scripturl . '?action=gallery;sa=watchuser;author=' . $row['id_author'],null,'gallery');

	}
	$smcFunc['db_free_result']($result);


}

function MyWatchList()
{
	global $txt, $context, $smcFunc, $user_info;

	// Is the user allowed to view the gallery?
	isAllowedTo('smfgallery_view');

	is_not_guest();


 	$dbresult = $smcFunc['db_query']('',"
	SELECT
		u.id_author, m.real_name
	FROM {db_prefix}gallery_notifyupdates as u
	LEFT JOIN {db_prefix}members AS m ON (u.id_author = m.id_member)
	WHERE u.id_member =  " . $user_info['id']);
    $context['gallery_mywatchlist'] = array();
 	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
	   $context['gallery_mywatchlist'][] = $row;
	}

	$smcFunc['db_free_result']($dbresult);


	$context['page_title'] = $txt['gallery_txt_mywatchlist'];
	$context['sub_template'] = 'mywatchlist';

    $context['linktree'][] = array(
			'name' => $txt['gallery_txt_mywatchlist']
		);
}

function WhoWatchMe()
{
	global $txt, $context, $user_info, $smcFunc;

	// Is the user allowed to view the gallery?
	isAllowedTo('smfgallery_view');

	is_not_guest();


 	$dbresult = $smcFunc['db_query']('',"
	SELECT
		u.id_member, m.real_name
	FROM {db_prefix}gallery_notifyupdates as u
	LEFT JOIN {db_prefix}members AS m ON (u.id_member = m.id_member)
	WHERE u.id_author =  " . $user_info['id']);
    $context['gallery_whowwatchme'] = array();
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
	   $context['gallery_whowwatchme'][] = $row;
    }
    $smcFunc['db_free_result']($dbresult);

	$context['page_title'] = $txt['gallery_txt_who_watch_me'];
	$context['sub_template'] = 'whowatchme';

    $context['linktree'][] = array(
			'name' => $txt['gallery_txt_who_watch_me']
		);
}

function StoreGalleryLocation()
{
	global $scripturl;

	if (isset($_SERVER['QUERY_STRING']))
		$_SESSION['last_gallery_url'] = $scripturl . '?' . $_SERVER['QUERY_STRING'];
	else
		$_SESSION['last_gallery_url'] = $scripturl;
}

function CreateGalleryPrettyCategory()
{

	global $context;

		$finalArray = array();

		$parentList = array(0);
		$newParentList = array();
		$spacer = 0;
		for ($g = 0;$g < count($parentList); $g++)
		{
			$tmpLevelArray = array();
			for ($i = 0;$i < count($context['gallery_cat']);$i++)
			{
				if ($context['gallery_cat'][$i]['id_parent'] == $parentList[$g])
				{

					$newParentList[] = $context['gallery_cat'][$i]['id_cat'];
					$newParentList = array_unique($newParentList);
					$context['gallery_cat'][$i]['title'] = str_repeat('-', $spacer) .$context['gallery_cat'][$i]['title'];
					$tmpLevelArray[] = $context['gallery_cat'][$i];
				}
			}

			// Check Top Level ID_PARENT
			if ($parentList[$g] == 0)
			{
				$finalArray = $tmpLevelArray;
			}
			else
			{
				$tmpArray2 = array();
				for($j = 0;$j<count($finalArray);$j++)
				{
					$tmpArray2[] = $finalArray[$j];
					// Find Parent good Now we just insert the records that we found right after the parent
					if ($finalArray[$j]['id_cat'] == $parentList[$g])
					{
						for ($z = 0;$z < count($tmpLevelArray);$z++)
						{
							$tmpArray2[] = $tmpLevelArray[$z];
						}
					}
				}

				$finalArray = $tmpArray2;
			}
			$tmpLevelArray = array();


			if ($g == (count($parentList) -1) && !empty($newParentList))
			{

				$parentList = array();
				$parentList = $newParentList;
				$newParentList = array();
				$g=-1;
				$spacer++;
			}
			else if ($g == (count($parentList) -1) && empty($newParentList))
			{

			}


		}

		$context['gallery_cat'] = array();
		$context['gallery_cat'] = $finalArray;
}

function CreateUserGalleryPrettyCategory()
{
	global $context;
		$finalArray = array();

		$parentList = array(0);
		$newParentList = array();
		$spacer = 0;
		for ($g = 0;$g < count($parentList); $g++)
		{
			$tmpLevelArray = array();
			for ($i = 0;$i < count($context['gallery_cat']);$i++)
			{
				if ($context['gallery_cat'][$i]['id_parent'] == $parentList[$g])
				{

					$newParentList[] = $context['gallery_cat'][$i]['user_id_cat'];
					$newParentList = array_unique($newParentList);
					$context['gallery_cat'][$i]['title'] = str_repeat('-', $spacer) .$context['gallery_cat'][$i]['title'];
					$tmpLevelArray[] = $context['gallery_cat'][$i];
				}
			}

			// Check Top Level ID_PARENT
			if ($parentList[$g] == 0)
			{
				$finalArray = $tmpLevelArray;
			}
			else
			{
				$tmpArray2 = array();
				for($j = 0;$j<count($finalArray);$j++)
				{
					$tmpArray2[] = $finalArray[$j];
					// Find Parent good Now we just insert the records that we found right after the parent
					if ($finalArray[$j]['user_id_cat'] == $parentList[$g])
					{
						for ($z = 0;$z < count($tmpLevelArray);$z++)
						{
							$tmpArray2[] = $tmpLevelArray[$z];
						}
					}
				}

				$finalArray = $tmpArray2;
			}
			$tmpLevelArray = array();


			if ($g == (count($parentList) -1) && !empty($newParentList))
			{
				$parentList = array();
				$parentList = $newParentList;
				$newParentList = array();
				$g=-1;
				$spacer++;
			}
			else if ($g == (count($parentList) -1) && empty($newParentList))
			{

			}


		}

		$context['gallery_cat'] = array();
		$context['gallery_cat'] = $finalArray;
}


function Gallery_ShowTagCloud()
{
	global $txt, $scripturl, $context, $smcFunc, $gallerySettings;


	$smfgalleryTagCache = array();

	if (($smfgalleryTagCache = cache_get_data('smfgallery_tagcloud', 60)) == null)
	{
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			t.tag AS tag, l.ID_TAG, COUNT(l.ID_TAG) AS quantity
		  FROM {db_prefix}gallery_tags as t, {db_prefix}gallery_tags_log as l
		  WHERE t.ID_TAG = l.ID_TAG
		  GROUP BY l.ID_TAG
		  ORDER BY COUNT(l.ID_TAG) DESC, RAND() LIMIT " . $gallerySettings['gallery_set_cloud_tags_to_show']);

		while($row = $smcFunc['db_fetch_assoc']($dbresult))
		{
			$smfgalleryTagCache[] = $row;
		}
		$smcFunc['db_free_result']($dbresult);
		cache_put_data('smfgallery_tagcloud', $smfgalleryTagCache, 60);
	}


//Tag cloud from http://www.prism-perfect.net/archive/php-tag-cloud-tutorial/

		// here we loop through the results and put them into a simple array:
		// $tag['thing1'] = 12;
		// $tag['thing2'] = 25;
		// etc. so we can use all the nifty array functions
		// to calculate the font-size of each tag
		$tags = array();

		$tags2 = array();

		if (!empty($smfgalleryTagCache))
		foreach($smfgalleryTagCache as $row)
		{
		    $tags[$row['tag']] = $row['quantity'];
		    $tags2[$row['tag']] = $row['ID_TAG'];
		}

		if (count($tags2) > 0)
		{
			// change these font sizes if you will
			$max_size = $gallerySettings['gallery_set_cloud_max_font_size_precent']; // max font size in %
			$min_size = $gallerySettings['gallery_set_cloud_min_font_size_precent']; // min font size in %


			// get the largest and smallest array values
			$max_qty = max(array_values($tags));
			$min_qty = min(array_values($tags));

			// find the range of values
			$spread = $max_qty - $min_qty;
			if (0 == $spread)
			 { // we don't want to divide by zero
			    $spread = 1;
			}

			// determine the font-size increment
			// this is the increase per tag quantity (times used)
			$step = ($max_size - $min_size)/($spread);

			// loop through our tag array
			$context['gallery_poptags'] = '';
			$row_count = 0;
			foreach ($tags as $key => $value)
			{
				$row_count++;
			    // calculate CSS font-size
			    // find the $value in excess of $min_qty
			    // multiply by the font-size increment ($size)
			    // and add the $min_size set above
			    $size = $min_size + (($value - $min_qty) * $step);
			    // uncomment if you want sizes in whole %:
			    // $size = ceil($size);

			    // you'll need to put the link destination in place of the #
			    // (assuming your tag links to some sort of details page)
			    $context['gallery_poptags'] .= '<a href="' . $scripturl . '?action=gallery;sa=search2;key=' . urlencode($key) . '" style="font-size: '.$size.'%"';
			    // perhaps adjust this title attribute for the things that are tagged
			   $context['gallery_poptags'] .= ' title="'.$value.'  '.$key.'"';
			   $context['gallery_poptags'] .= '>'.$key.'</a> ';
			   if ($row_count > $gallerySettings['gallery_set_cloud_tags_per_row'])
			   {
			   	$context['gallery_poptags'] .= '<br />';
			   	$row_count =0;
			   }
			    // notice the space at the end of the link
			}
		}

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">', $txt['gallery_txt_tag_cloud'], '</h3>
</div>

	<table class="' . ($context['gallery21beta'] == false ? 'table_list' : 'table_grid') . '">

				<tr class="windowbg2">
					<td align="center">' .$context['gallery_poptags'] . '</td>
				</tr>
		</table><br />';

}

function UpdateGalleryKeywords($id_picture)
{
	global $smcFunc, $user_info;

	// Get the Keywords
	$result = $smcFunc['db_query']('', "select
		id_picture, keywords
	FROM {db_prefix}gallery_pic
	WHERE id_picture = $id_picture
	");
	$listingRow = $smcFunc['db_fetch_assoc']($result);

	if (empty($listingRow['keywords']))
		return;

	$keywords = explode(",",$listingRow['keywords']);

	$finalKeywords = array();
    $countRemoved = 0;
    $finalKeywordList = '';
	foreach($keywords as $mykey)
	{

       $mykey = $smcFunc['htmltrim']($mykey);

       if (strlen($mykey) <= 2)
       {
        $countRemoved++;
       }
       else
	       $finalKeywords[] = $mykey;
	}

    $finalKeywordList = implode(",",$finalKeywords);

	// Get Current Tags
	$currentKeywords = array();
	$dbresult = $smcFunc['db_query']('', "
	SELECT
		t.ID_TAG, t.tag
	FROM {db_prefix}gallery_tags as t, {db_prefix}gallery_tags_log  as l

	WHERE t.ID_TAG = l.ID_TAG AND  l.id_picture = $id_picture");
	while($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$currentKeywords[] = $row['tag'];
	}

	foreach($currentKeywords as $mykey)
	{
		// If the keyword  is not in the final keywords list
		if (!in_array($mykey, $finalKeywords))
		{
			// Delete it!
			$smcFunc['db_query']('', "
				DELETE l FROM ({db_prefix}gallery_tags_log as l, {db_prefix}gallery_tags as t)
				WHERE l.ID_TAG = t.ID_TAG AND l.id_picture = '$id_picture' AND t.tag = '$mykey'");
		}

	}


	// Now add keywords that are not in current keywords
	foreach($finalKeywords as $tag)
	{
			// Keyword found

			// If keyword is already in the list no reason to add it again
			if (in_array($tag, $currentKeywords))
				continue;

			$dbresult = $smcFunc['db_query']('', "
			SELECT
				ID_TAG
			FROM {db_prefix}gallery_tags
			WHERE tag = '$tag'");

			if ($smcFunc['db_num_rows']($dbresult) == 0)
			{
				// Insert into Tags table
				$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_tags
					(tag)
				VALUES ('$tag')");
				$ID_TAG = $smcFunc['db_insert_id']("{db_prefix}gallery_tags",'ID_TAG');
				// Insert into Tags log
				$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_tags_log
					(ID_TAG,id_picture, id_member)
				VALUES ($ID_TAG,$id_picture," . $user_info['id'] . ")");
			}
			else
			{
				$tagRow = $smcFunc['db_fetch_assoc']($dbresult);
				$ID_TAG = $tagRow['ID_TAG'];
				// Insert into Tags log
				$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_tags_log
					(ID_TAG,id_picture, id_member)
				VALUES ($ID_TAG,$id_picture," . $user_info['id'] . ")");
			}
		}

        if ($countRemoved  != 0)
        {
            $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic SET keywords = '$finalKeywordList'
	WHERE id_picture = $id_picture");
        }



}

function gallery_format_size($size, $round = 0)
{
    //Size must be bytes!
    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    for ($i=0; $size > 1024 && $i < count($sizes) - 1; $i++) $size /= 1024;
    return round($size,$round).$sizes[$i];
}

function gallery_getTinyUrl($url)
{
	 if (function_exists('file_get_contents') && ini_get('allow_url_fopen') == 1)
	 {
	 	$tinyurl = file_get_contents("http://tinyurl.com/api-create.php?url=".$url);
		 return $tinyurl;
	 }
	 else
	 	return $url;
}

function Gallery_UpdateLatestCategory($ID_CAT)
{
	global $smcFunc;

	if (empty($ID_CAT))
		return;

	$result = $smcFunc['db_query']('', "
	select
		 max(id_picture) as LastID, ID_CAT
	 from {db_prefix}gallery_pic
	WHERE approved = 1 AND ID_CAT = $ID_CAT group by id_cat");
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat
		SET LAST_id_picture = " . $row['LastID'] . "
		where ID_CAT = " . $ID_CAT);

	}

}

function GallerySaveNote()
{
	global $gallerySettings, $smcFunc, $context;
	isAllowedTo('smfgallery_view');
	is_not_guest();

	$context['template_layers'] = array();

	// Check if tagging is supported
	if (empty($gallerySettings['gallery_set_allow_photo_tagging']))
	{
		return;
	}

	ob_clean();

	if (!isset($_REQUEST['id']))
		return;
	if (!isset($_REQUEST['text']))
		return;

	$id = (int) $_REQUEST['id'];

	$pic = (int) $_REQUEST['pic'];

	$width = (int) $_REQUEST['width'];
	$height = (int) $_REQUEST['height'];
	$xpos = (int) $_REQUEST['left'];
	$ypos = (int) $_REQUEST['top'];
	$caption = $smcFunc['htmlspecialchars']($_REQUEST['text'],ENT_QUOTES);
	// New note
	if ($id == -1)
	$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_pic_tagging
			(id_picture, caption, xpos,ypos, width,height)
		VALUES ('$pic','$caption','$xpos','$ypos','$width','$height')");
	else
	{
		// We are updating a note
		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic_tagging
		SET id_picture = $pic, caption = '$caption',
		xpos = '$xpos', ypos = '$ypos', width = '$width',
		height = '$height'
		WHERE ID = $id
 ");

	}


	obExit(false);

}

function GalleryDeleteNote()
{
	global $gallerySettings, $smcFunc, $context;
	isAllowedTo('smfgallery_view');
	is_not_guest();

	$context['template_layers'] = array();

	// Check if tagging is supported
	if (empty($gallerySettings['gallery_set_allow_photo_tagging']))
	{
		return;
	}

	ob_clean();

	if (!isset($_REQUEST['id']))
		return;


	$id = (int) $_REQUEST['id'];
	$pic = (int) $_REQUEST['pic'];

	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_pic_tagging
		WHERE ID = $id AND id_picture = $pic");


	obExit(false);
}

function ShowTopGalleryBar($title = '&nbsp;')
{
	global $txt, $context;
		echo '


<div class="cat_bar">
		<h3 class="catbg centertext">
        ', $title, '
        </h3>
</div>';


	if ($context['gallery21beta'] == true)
	{
	    	echo template_button_strip($context['gallery']['buttons'], 'right');
	    echo '<br /><br />
		<br />';
	}
	else
	{
	echo '

					<table border="0" cellpadding="0" cellspacing="0" align="center" width="90%">
							<tr>
								<td align="right">
								', DoToolBarStrip($context['gallery']['buttons'], 'top'), '

							</td>
							</tr>
						</table>

	<br />';
	}
}

function CopyImageToCategory($id_picture, $USER_ID_CAT, $ID_CAT)
{
	global $smcFunc, $modSettings,  $scripturl, $extrafolder;

	$result = $smcFunc['db_query']('',
	"SELECT
		id_picture, id_member, title, description, filesize, height,  width, approved,  keywords,
		filename, thumbfilename, mediumfilename, videofile, mature, type, allowcomments, sendemail
	FROM {db_prefix}gallery_pic
	WHERE id_picture = $id_picture
	");
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	if ($modSettings['gallery_set_enable_multifolder'])
		CreateGalleryFolder();

	$t = time();
	$gallery_pic_id = 0;
	$extrafolder = '';

	if ($modSettings['gallery_set_enable_multifolder'])
			$extrafolder = $modSettings['gallery_folder_id'] . '/';

	$orginalFilename = $row['filename'];
	if ($modSettings['gallery_set_enable_multifolder'])
		{
			$tmp = explode('/',$row['filename']);

			if (!empty($tmp[1]))
			{
				$orginalFilename = $tmp[1];
			}
		}

	$sizes = @getimagesize($modSettings['gallery_path']  . $row['filename']);

	$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
	$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

	// Copy Image to Main Gallery
	if (!empty($ID_CAT))
	{
		// Filename Member Id + Day + Month + Year + 24 hour, Minute Seconds
		$filename = $row['id_member'] . '_' . date('d_m_y_g_i_s') . '_1.' . $extension;

		// Copy Files
		@copy($modSettings['gallery_path'] . $row['filename'], $modSettings['gallery_path'] . $extrafolder . $filename);
		@chmod($modSettings['gallery_path'] . $extrafolder . $filename, 0755);

		$thumbname = 'thumb_' . $filename;
		@copy($modSettings['gallery_path'] . $row['thumbfilename'], $modSettings['gallery_path'] . $extrafolder . $thumbname);
		@chmod($modSettings['gallery_path'] . $extrafolder . $thumbname, 0755);

		$mediumimage = 'medium_' . $filename;
		@copy($modSettings['gallery_path'] . $row['mediumfilename'], $modSettings['gallery_path'] . $extrafolder . $mediumimage );
		@chmod($modSettings['gallery_path'] . $extrafolder . $mediumimage , 0755);

		if (!empty($row['videofile']))
		{
			@copy($modSettings['gallery_path'] . $row['thumbfilename'], $modSettings['gallery_path'] . $extrafolder . $row['videofile']);
			@chmod($modSettings['gallery_path'] . $extrafolder . $row['videofile'], 0755);
		}



		$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_pic
							(ID_CAT, filesize,thumbfilename,filename, height, width,
							keywords, title, description,id_member,date,approved,allowcomments,
							sendemail,mediumfilename,mature)
						VALUES ($ID_CAT, " . $row['filesize']  . ",'" . $extrafolder .  $thumbname . "', '" . $extrafolder . $filename . "',
						$sizes[1], $sizes[0], '" . $row['keywords']  . "','" . $row['title']  . "', '" . $row['description']  . "', " . $row['id_member']  . ",
						$t," . $row['approved']  . ", " . $row['allowcomments']  . "," . $row['sendemail']  . ",'" . $extrafolder .  $mediumimage . "'," . $row['mature']  . ")");
		$gallery_pic_id = $smcFunc['db_insert_id']('{db_prefix}gallery_pic', 'id_picture');


		// Get EXIF Data
		ProcessEXIFData($extrafolder . $filename,$gallery_pic_id );



		if ($row['approved'] == 1)
 		{
 			UpdateMemberPictureTotals($row['id_member'] );
 			Gallery_UpdateLatestCategory($ID_CAT);

 		   SendMemberWatchNotifications($row['id_member'] , $scripturl . '?action=gallery;sa=view;id=' .  $gallery_pic_id );
 		}

 		UpdateCategoryTotals($ID_CAT);

 		UpdateUserFileSizeTable($row['id_member'] ,$row['filesize']);

 		UpdateGalleryKeywords($gallery_pic_id);

	}

	// Copy Image to Personal Gallery
	if (!empty($USER_ID_CAT))
	{
		$filename = $row['id_member'] . '_' . date('d_m_y_g_i_s') . '_2.' . $extension;

	// Copy Files
		@copy($modSettings['gallery_path'] . $row['filename'], $modSettings['gallery_path'] . $extrafolder . $filename);
		@chmod($modSettings['gallery_path'] . $extrafolder . $filename, 0755);

		$thumbname = 'thumb_' . $filename;
		@copy($modSettings['gallery_path'] . $row['thumbfilename'], $modSettings['gallery_path'] . $extrafolder . $thumbname);
		@chmod($modSettings['gallery_path'] . $extrafolder . $thumbname, 0755);

		$mediumimage = 'medium_' . $filename;
		@copy($modSettings['gallery_path'] . $row['mediumfilename'], $modSettings['gallery_path'] . $extrafolder . $mediumimage );
		@chmod($modSettings['gallery_path'] . $extrafolder . $mediumimage , 0755);

		if (!empty($row['videofile']))
		{
			@copy($modSettings['gallery_path'] . $row['thumbfilename'], $modSettings['gallery_path'] . $extrafolder . $row['videofile']);
			@chmod($modSettings['gallery_path'] . $extrafolder . $row['videofile'], 0755);
		}

		$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_pic
							(USER_ID_CAT, filesize,thumbfilename,filename, height, width,
							keywords, title, description,id_member,date,approved,allowcomments,
							sendemail,mediumfilename,mature)
						VALUES ($USER_ID_CAT, " . $row['filesize']  . ",'" . $extrafolder .  $thumbname . "', '" . $extrafolder . $filename . "',
						$sizes[1], $sizes[0], '" . $row['keywords']  . "','" . $row['title']  . "', '" . $row['description']  . "', " . $row['id_member']  . ",
						$t," . $row['approved']  . ", " . $row['allowcomments']  . "," . $row['sendemail']  . ",'" . $extrafolder .  $mediumimage . "'," . $row['mature']  . ")");
		$gallery_pic_id = $smcFunc['db_insert_id']('{db_prefix}gallery_pic', 'id_picture');


		// Get EXIF Data
		ProcessEXIFData($extrafolder . $filename,$gallery_pic_id );

		UpdateUserCategoryTotals($USER_ID_CAT);
		Gallery_UpdateUserLatestCategory($USER_ID_CAT);

		UpdateUserFileSizeTable($row['id_member'],$row['filesize']);

		UpdateGalleryKeywords($gallery_pic_id);
	}

	// Update the SMF Shop Points
	if (isset($modSettings['shopVersion']))
 				$smcFunc['db_query']('', "UPDATE {db_prefix}members
				 	SET money = money + " . $modSettings['gallery_shop_picadd'] . "
				 	WHERE id_member = " .  $row['id_member']  . "
				 	LIMIT 1");



}

function CopyImage()
{
	global $txt, $context, $smcFunc, $mbname, $user_info;

	isAllowedTo('smfgallery_add');

	$g_manage = allowedTo('smfgallery_manage');


	// Check if they own the picuture or are admin
	$id = (int) $_REQUEST['id'];

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_member, id_picture
		FROM
		 {db_prefix}gallery_pic
		WHERE id_picture = $id");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);

	if ($g_manage == false && $user_info['id'] != $row['id_member'])
		fatal_error($txt['gallery_perm_no_add'],false);

	$context['gallery_pic_id'] = $id;


	if ($context['user']['is_guest'])
		$groupid = -1;
	else
		$groupid =  $user_info['groups'][0];

		// User Gallery
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			u.id_member,u.USER_ID_CAT, u.title,u.roworder, m.real_name
		FROM
		 {db_prefix}gallery_usercat as u, {db_prefix}members as m
		WHERE u.id_member = m.id_member
		" . ($g_manage == false ? ' AND u.id_member = ' . $user_info['id'] : ' ') . "
		ORDER BY roworder ASC");



		$context['gallery_cat2'] = array();
		if ($smcFunc['db_num_rows']($dbresult) != 0)
		{
			while($row = $smcFunc['db_fetch_assoc']($dbresult))
				{
					$context['gallery_cat2'][] = array(
						'ID_CAT' => $row['USER_ID_CAT'],
						'title' => $row['title'],
						'roworder' => $row['roworder'],
						'real_name' => $row['real_name'],
					);
				}
		}
		$smcFunc['db_free_result']($dbresult);

		// Gallery Category
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.title, c.id_parent, p.view, p.addpic, c.locked
		FROM {db_prefix}gallery_cat as c
		LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.ID_GROUP = $groupid AND c.ID_CAT = p.ID_CAT)
		WHERE c.redirect = 0 ORDER BY c.title ASC");
		//DD Edit c.roworder ASC



		$context['gallery_cat'] = array();
		if ($smcFunc['db_num_rows']($dbresult) != 0)
		{
		 while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
			// Check if they have permission to add to this category.
				if ($row['view'] == '0' || $row['addpic'] == '0' )
					continue;

				// Skip category if it is locked
				if ($g_manage == false && $row['locked'] == 1)
					continue;


				$context['gallery_cat'][] = $row;
			}
		}

		$smcFunc['db_free_result']($dbresult);

		CreateGalleryPrettyCategory();


	$context['sub_template']  = 'copy_image';

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_copy_item'];

}

function CopyImage2()
{
	global $smcFunc, $txt, $user_info ;

	isAllowedTo('smfgallery_add');


	$g_manage = allowedTo('smfgallery_manage');
	// Check Permissions

	$id = (int) $_REQUEST['id'];


	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_member, id_picture
		FROM
		 {db_prefix}gallery_pic
		WHERE id_picture = $id");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);

	if ($g_manage == false && $user_info['id'] != $row['id_member'])
		fatal_error($txt['gallery_perm_no_add'],false);

	$usercat = (int) $_REQUEST['usercat'];

	$cat = (int) $_REQUEST['cat'];

	GetCatPermission($cat,'addpic');

	// Copy Images
	CopyImageToCategory($id,$usercat,$cat);

	// Redirect to MyImages
	redirectexit('action=gallery;sa=myimages;u=' . $user_info['id']);

}

function DoGalleryAdSellerPro()
{
	global $modSettings,  $smcFunc;

	// Check if adseller pro is installed

	if (!isset($modSettings['seller_show_advetise']))
		return;

	// Don't do anything if installed already

	if (!empty($modSettings['gallery_ad_seller']))
	{
		if ($modSettings['gallery_ad_seller'] == '3.0')
			return;
	}
	// Insert the Ad Seller Pro Locations for the Gallery

	// Top of category view
	$smcFunc['db_query']('', "INSERT IGNORE INTO {db_prefix}seller_ad_location (ID_LOCATION, title, description, disabled)
	VALUES ('101', 'SMF Gallery - Top of category View',  'This ad location shows an ad on the top of a category of a gallery' ,1)");

	// Bottom of category view
	$smcFunc['db_query']('', "INSERT IGNORE INTO {db_prefix}seller_ad_location (ID_LOCATION, title, description, disabled)
	VALUES ('102', 'SMF Gallery - Bottom of category view',  'This ad location shows an ad on the bottom of a category of a gallery' ,1)");


	// Top of picture
	$smcFunc['db_query']('', "INSERT IGNORE INTO {db_prefix}seller_ad_location (ID_LOCATION, title, description, disabled)
	VALUES ('103', 'SMF Gallery - Above Picture',  'This ad location shows above a picture in the gallery' ,1)");


	// Bottom of picture
	$smcFunc['db_query']('', "INSERT IGNORE INTO {db_prefix}seller_ad_location (ID_LOCATION, title, description, disabled)
	VALUES ('104', 'SMF Gallery - Below Picture',  'This ad location shows below a picture in the gallery' ,1)");



	// Mark the locations are installed
	updateSettings(
	array(
	'gallery_ad_seller' => '3.0',));
}

function AutoThumbNailGalleryIcon()
{
	global $modSettings, $smcFunc, $user_info, $sourcedir, $txt;

	$id = (int) $_REQUEST['id'];

	$g_manage = allowedTo('smfgallery_manage');

	require_once($sourcedir . '/Subs-Graphics.php');


	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_member, id_picture, thumbfilename, ID_CAT, USER_ID_CAT
		FROM
		 {db_prefix}gallery_pic
		WHERE id_picture = $id");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);

	if ($g_manage == false && empty($row['USER_ID_CAT']))
		return;

	// Check if picture exists
	if (empty($row['id_picture']))
		return;

	if (!empty($row['ID_CAT']))
	{
		$extension = substr(strrchr($row['thumbfilename'], '.'), 1);

		// If main category
		$filename = $row['ID_CAT'] . '.' . $extension;

			$sizes = @getimagesize($modSettings['gallery_path'] . $row['thumbfilename']);

			// No size, then it's probably not a valid pic.
			if ($sizes === false)
				fatal_error($txt['gallery_error_invalid_picture'],false);


			if ((!empty($modSettings['gallery_set_cat_width']) && $sizes[0] > $modSettings['gallery_set_cat_width']) || (!empty($modSettings['gallery_set_cat_height']) && $sizes[1] > $modSettings['gallery_set_cat_height']))
			{
				if (!empty($modSettings['gallery_resize_image']))
				{
					// Check to resize image?

					copy($modSettings['gallery_path'] . $row['thumbfilename'],$modSettings['gallery_path'] . 'catimgs/' . $filename );
					@chmod($modSettings['gallery_path'] . 'catimgs/' . $filename, 0644);
					DoCatImagResize($sizes,$modSettings['gallery_path'] . 'catimgs/' . $filename);
				}
				else
				{
					// Delete the temp file
					fatal_error($txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0],false);
				}
			}
			else
			{

				copy($modSettings['gallery_path'] . $row['thumbfilename'],$modSettings['gallery_path'] . 'catimgs/' . $filename );
				@chmod($modSettings['gallery_path'] . 'catimgs/' . $filename, 0644);
			}

		// Update the filename for the category
		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat
			SET filename = '$filename' WHERE ID_CAT = " . $row['ID_CAT']. " LIMIT 1");
	}

	if (!empty($row['USER_ID_CAT']))
	{

		$dbresult2 = $smcFunc['db_query']('', "
		SELECT
			id_member
		FROM
		 {db_prefix}gallery_usercat
		WHERE USER_ID_CAT  = " . $row['USER_ID_CAT']);
		$row2 = $smcFunc['db_fetch_assoc']($dbresult2);
		$smcFunc['db_free_result']($dbresult2);
		// Permission Checked
		if ($g_manage == false && $row2['id_member'] != $user_info['id'])
			return;


		$extension = substr(strrchr($row['thumbfilename'], '.'), 1);
		$filename = 'user_' . $row['USER_ID_CAT'] . '.' . $extension;


			$sizes = @getimagesize($modSettings['gallery_path'] . $row['thumbfilename']);

			// No size, then it's probably not a valid pic.
			if ($sizes === false)
				fatal_error($txt['gallery_error_invalid_picture'],false);



			if ((!empty($modSettings['gallery_set_cat_width']) && $sizes[0] > $modSettings['gallery_set_cat_width']) || (!empty($modSettings['gallery_set_cat_height']) && $sizes[1] > $modSettings['gallery_set_cat_height']))
			{
				if (!empty($modSettings['gallery_resize_image']))
				{
					// Check to resize image?

					copy($modSettings['gallery_path'] . $row['thumbfilename'],$modSettings['gallery_path'] . 'catimgs/' . $filename );
					@chmod($modSettings['gallery_path'] . 'catimgs/' . $filename, 0644);
					DoCatImagResize($sizes,$modSettings['gallery_path'] . 'catimgs/' . $filename);
				}
				else
				{
					// Delete the temp file
					fatal_error($txt['gallery_error_img_size_height'] . $sizes[1] . $txt['gallery_error_img_size_width'] . $sizes[0],false);
				}
			}
			else
			{

				copy($modSettings['gallery_path'] . $row['thumbfilename'],$modSettings['gallery_path'] . 'catimgs/' . $filename );
				@chmod($modSettings['gallery_path'] . 'catimgs/' . $filename, 0644);
			}

		// Update the filename for the category
		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_usercat
		SET filename = '$filename' WHERE user_id_cat = " . $row['USER_ID_CAT'] . " LIMIT 1");

	}

	redirectexit('action=gallery;sa=view;id=' . $id);

}

function MyFavorites()
{
	global $txt, $context, $mbname, $user_info, $smcFunc, $modSettings, $scripturl;

	is_not_guest();

	isAllowedTo('smfgallery_view');


 	// Get Total Pages
	$dbresult = $smcFunc['db_query']('', "
	SELECT COUNT(*) AS total
	FROM {db_prefix}gallery_favorites WHERE id_member = " . $user_info['id']);
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$recordtotal = $row['total'];
	$smcFunc['db_free_result']($dbresult);

	$context['start'] = (int) $_REQUEST['start'];
	$context['page_index'] = constructPageIndex($scripturl . '?action=gallery;sa=myfavorites' , $_REQUEST['start'], $recordtotal, $modSettings['gallery_set_images_per_page']);

	$dbresult = $smcFunc['db_query']('', "
	SELECT p.id_picture, p.commenttotal, p.filesize, p.thumbfilename,
	p.approved, p.views, p.id_member, m.real_name, mg.online_color, p.date, p.totallikes
	FROM ({db_prefix}gallery_pic as p, {db_prefix}gallery_favorites as f)
	LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
	LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
	WHERE f.id_picture = p.id_picture AND f.id_member = " . $user_info['id'] . "  ORDER BY f.id DESC
	LIMIT $context[start]," . $modSettings['gallery_set_images_per_page']);

    $context['gallery_myfavorites'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
	   $context['gallery_myfavorites'][] = $row;
    }

    $smcFunc['db_free_result']($dbresult);

	$context['sub_template']  = 'myfavorities';
	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_myfavorites'];

    $context['linktree'][] = array(
			'name' =>  $txt['gallery_txt_myfavorites']
		);

}

function AddFavorite()
{
	global $smcFunc, $user_info;

	is_not_guest();

	isAllowedTo('smfgallery_view');

	$id = (int) $_REQUEST['id'];

	$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_favorites
			(id_picture, id_member)
		VALUES ($id, " . $user_info['id'] . ")");


	redirectexit('action=gallery;sa=myfavorites');
}

function UnFavorite()
{
	global $smcFunc, $user_info;

	is_not_guest();

	isAllowedTo('smfgallery_view');

	$id = (int) $_REQUEST['id'];

	$smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_favorites WHERE id_picture = $id AND id_member = " . $user_info['id']);

	redirectexit('action=gallery;sa=myfavorites');
}

function DoCatImagResize($sizes,$destName)
{
	global $modSettings, $gd2;

	$default_formats = array(
		'1' => 'gif',
		'2' => 'jpeg',
		'3' => 'png',
		'6' => 'bmp',
		'15' => 'wbmp'
	);

	// Gif? That might mean trouble if gif support is not available.
	if ($sizes[2] == 1 && !function_exists('imagecreatefromgif') && function_exists('imagecreatefrompng'))
	{
		// Download it to the temporary file... use the special gif library... and save as png.
		if ($img = @gif_loadFile($destName) && gif_outputAsPng($img, $destName))
			$sizes[2] = 3;
	}
	// A known and supported format?
	if (isset($default_formats[$sizes[2]]) && function_exists('imagecreatefrom' . $default_formats[$sizes[2]]))
	{
		$imagecreatefrom = 'imagecreatefrom' . $default_formats[$sizes[2]];
		if ($src_img = @$imagecreatefrom($destName))
		{

			resizeImage($src_img, $destName, imagesx($src_img), imagesy($src_img), $modSettings['gallery_set_cat_width'], $modSettings['gallery_set_cat_height']);
		}
	}
}

if (!function_exists('json_encode'))
{
  function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}

function exif_get_float($value) {
  $pos = strpos($value, '/');
  if ($pos === false) return (float) $value;
  $a = (float) substr($value, 0, $pos);
  $b = (float) substr($value, $pos+1);
  return ($b == 0) ? ($a) : ($a / $b);
}

function exif_get_shutter($ShutterSpeedValue) {
  if (!isset($ShutterSpeedValue)) return false;
  $apex    = exif_get_float($ShutterSpeedValue);
  $shutter = pow(2, -$apex);
  if ($shutter == 0) return false;
  if ($shutter >= 1) return round($shutter) . 's';
  return '1/' . round(1 / $shutter) . 's';
}

function oldexif_get_fstop($ApertureValue) {
  if (!isset($ApertureValue)) return false;
  $apex  = exif_get_float($ApertureValue);

  $fstop = pow(2, $apex/2);
  if ($fstop == 0) return false;
  return 'f/' . round($fstop,1);
}

function exif_get_fstop($ApertureValue) {
  if (!isset($ApertureValue)) return false;

 // $fstop = exp(($ApertureValue*log(2))/2);
  //$fstop = round($fstop, 1);
  $apex  = exif_get_float($ApertureValue);

  $fstop = pow(2, $apex/2);
  if ($fstop == 0) return false;
  return   'f/' . round($fstop,1);

}

function exif_get_focal_length($FNumber,$FocalLength)
{
	 $Fnumber = explode("/", $FNumber);
	 if (empty($Fnumber[1]));
	 	return "n/a";

    $Fnumber = $Fnumber[0] / $Fnumber[1];
     $Focal = explode("/", $FocalLength);
     $Focal = $Focal[0] / $Focal[1];

     return round($Focal)."mm";
}

function CheckMaxUploadPerDay()
{
	global $gallerySettings, $smcFunc, $user_info, $txt;

	if (empty($gallerySettings['gallery_set_maxuploadperday']))
		return true;

	if (allowedTo('smfgallery_manage'))
		return true;

	// Find total uploads for the last 24 hours for the user
	$currenttime = time();

	$last24hourstime = $currenttime  -  (1* 24 * 60 * 60);


	$dbresult = $smcFunc['db_query']('', "
	SELECT
		id_picture
	FROM {db_prefix}gallery_pic
	WHERE id_member = " .$user_info['id'] . " AND date > $last24hourstime");

	$totalRow['total'] = $smcFunc['db_num_rows']($dbresult);

	if ($totalRow['total'] >= $gallerySettings['gallery_set_maxuploadperday'])
		fatal_error($txt['gallery_err_upload_day_limit'] .  $gallerySettings['gallery_set_maxuploadperday'], false);
	else
		return true;

}

function Gallery_AddRelatedPicture($pictureID, $title = '')
{
	global $smcFunc;

	if (empty($title))
		return;

   if (empty($pictureID))
        return;

	$smcFunc['db_query']('', "INSERT IGNORE INTO {db_prefix}gallery_title_cache
			(id_picture, title)
		VALUES ($pictureID, '$title')");


	$relatedPics = Gallery_FindRelated($title, true);

	if (empty($relatedPics))
		return false;


	$relatedPics = array_slice($relatedPics, 0, 10);

	$picsSQL = '';

	foreach ($relatedPics as $id)
		if ($pictureID != $id[0])
			$picsSQL .= '
		(' . min($pictureID, $id[0]) . ', ' . max($pictureID, $id[0]) . ', ' . $id[1] . '),';

	if (empty($picsSQL))
		return false;

	$smcFunc['db_query']('', "INSERT IGNORE INTO {db_prefix}gallery_related_pictures
			(id_picture_first, id_picture_second, score)
		VALUES" . substr($picsSQL, 0, -1));

}

function Gallery_FindRelated($title, $score = false)
{
	global $smcFunc;

	$result = $smcFunc['db_query']('', "SELECT id_picture, MATCH(title) AGAINST('$title') AS score
		FROM {db_prefix}gallery_title_cache
	WHERE MATCH(title) AGAINST('$title')
	ORDER BY MATCH(title) AGAINST('$title') DESC");

	$data = array();

	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		if ($score == false)
			$data[] = $row['id_picture'];
		else
			$data[] = array($row['id_picture'], $row['score']);
	}

	return $data;
}

function Gallery_BuildRelatedIndex()
{
	global $smcFunc, $txt, $context;

	isAllowedTo('smfgallery_manage');

	@ini_set('max_execution_time', '900');

	$context['start'] = empty($_REQUEST['start']) ? 50 : (int) $_REQUEST['start'];


	$request = $smcFunc['db_query']('', "
		SELECT
			COUNT(*)
		FROM {db_prefix}gallery_pic
		");

	list($totalProcess) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	// Initialize the variables.
	$increment = 50;
	if (empty($_REQUEST['start']))
	{
		$_REQUEST['start'] = 0;
		$smcFunc['db_query']('', "TRUNCATE {db_prefix}gallery_title_cache ");
		$smcFunc['db_query']('', "TRUNCATE {db_prefix}gallery_related_pictures");

	}


	$dbresult = $smcFunc['db_query']('', "
	SELECT
		title, id_picture
	FROM {db_prefix}gallery_pic
	LIMIT " . $_REQUEST['start'] . ","  . ($increment));
	$counter = 0;
	$gallery_pics = array();
	while ($row = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$gallery_pics[] = $row;
	}
	$smcFunc['db_free_result']($dbresult);

	foreach($gallery_pics as $row)
	{
		Gallery_AddRelatedPicture($row['id_picture'],addslashes($row['title']));
		$counter++;
	}

	$_REQUEST['start'] += $increment;

	$complete = 0;
	if ($_REQUEST['start'] < $totalProcess)
	{

		$context['continue_get_data'] = 'start=' . $_REQUEST['start'];
		$context['continue_percent'] = round(100 * $_REQUEST['start'] / $totalProcess);


	}
	else
		$complete = 1;


	if ($complete == 1)
		redirectexit('action=admin;area=gallery;sa=adminset');
	else
	{
		$context['sub_template']  = 'relatedindex';

		$context['page_title'] =  $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_rebuildindex'];

	}

}

function Gallery_ReturnRelatedPictures($pictureID = 0, $relatedList = '')
{
	global $smcFunc, $gallerySettings, $user_info;
	$data = array();


	$pics = array();
	$picList = '';

	if ($relatedList == '' && !empty($pictureID))
	{
		$request = $smcFunc['db_query']('', "
			SELECT IF(rp.id_picture_first = $pictureID, rp.id_picture_second, rp.id_picture_first) AS id_picture
			FROM {db_prefix}gallery_related_pictures as rp
				JOIN {db_prefix}gallery_pic AS p ON (p.id_picture = IF(rp.id_picture_first = $pictureID, rp.id_picture_second, rp.id_picture_first))
			WHERE (rp.id_picture_first = $pictureID OR rp.id_picture_second = $pictureID) AND p.approved = 1

			ORDER BY rp.score DESC
			LIMIT " .  $gallerySettings['gallery_set_relatedimagescount']);
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$pics[] = $row;

				if (!empty($picList))
					$picList .= ',';

				$picList .= $row['id_picture'];
			}
	}
	else
		$picList = $relatedList;


		if (!empty($picList))
		{
			$dbresult = $smcFunc['db_query']('', "
			SELECT
				p.id_picture, p.totalratings, p.rating, p.commenttotal, p.filesize, p.views, p.thumbfilename, p.title,
				p.id_member, m.real_name, p.date, p.description, p.mature, v.id_picture as unread, mg.online_color,
				p.totallikes
			FROM {db_prefix}gallery_pic as p
			LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
            LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
			LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $user_info['id'] . " AND v.user_id_cat = p.USER_ID_CAT)
			WHERE  p.approved = 1 AND p.id_picture IN (" . $picList . ") GROUP BY p.id_picture"
			);
			while ($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				$data[] = $row;
			}
		}



	return $data;
}

function Gallery_PostUpload()
{
	global $modSettings, $smcFunc, $context, $scripturl, $user_info, $txt, $gallerySettings;
	isAllowedTo('smfgallery_add');

	$g_manage = allowedTo('smfgallery_manage');

	$context['gallery_forumurl'] = base64_encode($_REQUEST['forumurl']);

	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']))
		@ob_start('ob_gzhandler');
	else
		ob_start();

		if ($context['user']['is_guest'])
			$groupid = -1;
		else
			$groupid =  $user_info['groups'][0];


		$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.id_cat, c.title, p.view, p.addpic, c.locked, c.id_parent
		FROM {db_prefix}gallery_cat AS c
		LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.id_group = $groupid AND c.id_cat = p.id_cat)
		WHERE c.redirect = 0 ORDER BY c.title ASC");


		$context['gallery_cat'] = array();
		 while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				// Check if they have permission to add to this category.
				if ($row['view'] == '0' || $row['addpic'] == '0' )
					continue;

				// Skip category if it is locked
				if ($g_manage == false && $row['locked'] == 1)
					continue;

				$context['gallery_cat'][] = $row;
			}
		$smcFunc['db_free_result']($dbresult);

		CreateGalleryPrettyCategory();


		$dbresult = $smcFunc['db_query']('', "
		SELECT
			user_id_cat, title, roworder, id_parent
		FROM {db_prefix}gallery_usercat
		WHERE id_member = " . $user_info['id'] . " ORDER BY title ASC");


		$context['gallery_user_cat'] = array();
		while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				// id_cat on purpose for Add Picture page
				$context['gallery_user_cat'][] = array(
					'id_cat' => $row['user_id_cat'],
					'title' => $row['title'],
					'roworder' => $row['roworder'],
					'id_parent' => $row['id_parent'],
				);
			}
		$smcFunc['db_free_result']($dbresult);


	// Register this form and get a sequence number in $context.
	checkSubmitOnce('register');

	echo '<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
    </head>
	<body>
	<form method="post" enctype="multipart/form-data" name="picform" id="picform" action="' . $scripturl . '?action=gallery;sa=add2" onsubmit="submitonce(this);">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="catbg">
	<td width="50%" colspan="2" align="center">
	<b>' . $txt['gallery_form_addpicture'] . '</b></td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_title'] . '</b>&nbsp;</td>
	<td><input type="text" size="50" name="title" /></td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_category'] . '</b>&nbsp;</td>
	<td><select name="cat" id="cat">
	<option value="0">',$txt['gallery_text_choose_cat'],'</option>
	';

	foreach ($context['gallery_cat'] as $i => $category)
		echo '<option value="' . $category['id_cat']  . '" '  .'>' . $category['title'] . '</option>';


	echo '</select>
	</td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_description'] . '</b>&nbsp;</td>
	<td>
	<textarea name="descript" rows="3" cols="55"></textarea>
	</td>
</tr>';

	if ($gallerySettings['gallery_set_require_keyword'] == true)
	{
		 echo '<tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_keywords'] . '</b>&nbsp;</td>
  	<td><input type="text" name="keywords" size="50" maxlength="100" /></td>
  </tr>';
	}

echo '
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_uploadpic'] . '</b>&nbsp;</td>

	<td><input type="file" size="48" name="picture" />';

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
  </tr>
	<tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
	<input type="hidden" name="popup" value="1" />
	<input type="hidden" name="forumurl" value="'. $context['gallery_forumurl'] . '" />
	<input type="submit" value="' . $txt['gallery_form_addpicture'] . '" name="submit" /><br />';


echo '
	</td>
  </tr>
</table>

		</form>';

$g_gallery = allowedTo('smfgallery_usergallery');
if ($g_gallery)
{
echo '
	<form method="post" enctype="multipart/form-data" name="picform" id="picform" action="' . $scripturl . '?action=gallery;sa=add2" onsubmit="submitonce(this);">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr class="catbg">
	<td width="50%" colspan="2" align="center">
	<b>' . $txt['gallery_user_title2']. '</b></td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_title'] . '</b>&nbsp;</td>
	<td><input type="text" size="50" name="title" /></td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_user_title2']  . '</b>&nbsp;</td>
	<td><select name="cat" id="cat">
	<option value="0">',$txt['gallery_text_choose_cat'],'</option>
	';

	foreach ($context['gallery_user_cat'] as $i => $category)
		echo '<option value="' . $category['id_cat']  . '" '  .'>' . $category['title'] . '</option>';


	echo '</select>
	</td>
  </tr>
  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_description'] . '</b>&nbsp;</td>
	<td>
	<textarea name="descript" rows="3" cols="55"></textarea>
	</td>
</tr>
';

	if ($gallerySettings['gallery_set_require_keyword'] == true)
	{
		 echo '<tr class="windowbg2">
  	<td align="right"><b>' . $txt['gallery_form_keywords'] . '</b>&nbsp;</td>
  	<td><input type="text" name="keywords" size="50" maxlength="100" /></td>
  </tr>';
	}

echo '

  <tr class="windowbg2">
	<td align="right"><b>' . $txt['gallery_form_uploadpic'] . '</b>&nbsp;</td>
	<td><input type="file" size="48" name="picture" />';

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
  </tr>
	<tr class="windowbg2">
	<td width="28%" colspan="2" align="center">
	<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
	<input type="hidden" name="userid" value="'. $user_info['id'] . '" />
	<input type="hidden" name="popup" value="1" />
	<input type="hidden" name="forumurl" value="'. $context['gallery_forumurl'] . '" />
	<input type="submit" value="' . $txt['gallery_form_addpicture'] . '" name="submit" /><br />';


echo '
	</td>
  </tr>
</table>

		</form>';
}

echo '

		</body></html>';



	obExit(false);
	die();

}

function Gallery_PostUpload2()
{
	global $modSettings, $txt, $smcFunc, $scripturl;
	isAllowedTo('smfgallery_add');

	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']))
		@ob_start('ob_gzhandler');
	else
		ob_start();
		$forumurl = base64_decode($_REQUEST['forumurl']);

		$id = (int) $_REQUEST['id'];

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			p.id_picture, p.thumbfilename, p.width, p.height, p.allowcomments, p.id_cat, p.keywords,
			p.commenttotal, p.filesize, p.filename, p.approved, p.views, p.title, p.id_member, p.date, m.real_name, p.description,
			p.totallikes
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}members AS m ON (p.id_member = m.id_member)
		WHERE id_picture = $id LIMIT 1");

	if ($smcFunc['db_affected_rows']()== 0)
		fatal_error($txt['gallery_error_no_pictureexist'],false);
	$row = $smcFunc['db_fetch_assoc']($dbresult);

	$picurl = $modSettings['gallery_url'] . $row['filename'];
	$picurl = urlencode($picurl);

	$pageurl = $scripturl . '?action=gallery;sa=view&id=' . $id;
	$pageurl = urlencode($pageurl);

echo '
<script language="javascript" type="text/javascript">
<!--
function redirect_code()
{
document.location.href = \'' . $forumurl. '&smfgallery_id=0&smfgallery_text=%0A%5Burl%3D' . $pageurl . '%5D%5Bimg%5D' . $picurl . '%5B%2Fimg%5D%5B%2Furl%5D%0A\';
}
document.write(\'' . $txt['gallery_redirecting']  . '\');
redirect_code();
//-->
</script>
<noscript>

</noscript>';

	obExit(false);
	die();
}

function Gallery_SelectCat()
{
	global $context, $txt, $mbname, $smcFunc, $user_info;

	$u = 0;
	$cat = 0;
	if (isset($_REQUEST['u']))
		$u = (int) $_REQUEST['u'];

	if (isset($_REQUEST['cat']))
		$cat = (int) $_REQUEST['cat'];

    $context['video_select']  = 0;
    if (isset($_REQUEST['video']))
        $context['video_select']  = 1;

	if (!empty($cat) && !empty($u))
	{
		// User Gallery
        if ($context['video_select'] == 0)
		  redirectexit('action=gallery;sa=add;cat=' . $cat . ';u=' . $u);
        else
            redirectexit('action=gallery;sa=addvideo;cat=' . $cat . ';u=' . $u);
	}
	else if (!empty($cat))
	{
		// Main Gallery
        if ($context['video_select'] == 0)
    		redirectexit('action=gallery;sa=add;cat=' . $cat);
        else
            redirectexit('action=gallery;sa=addvideo;cat=' . $cat);
	}
	else
	{
		// Show cat select
		if ($context['video_select'] == 0)
		  isAllowedTo('smfgallery_add');
        else
            isAllowedTo('smfgalleryvideo_add');

		CheckMaxUploadPerDay();


		$context['sub_template']  = 'selectcat';

		if ($context['video_select'] == 0)
		  $context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_addpicture'];
        else
            $context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_addvideo'];


		// User gallery

		$g_manage = allowedTo('smfgallery_manage');
		$g_gallery = allowedTo('smfgallery_usergallery');


		$context['usergallery_memid'] = 0;
		if ($g_gallery == true)
		{
			$context['usergallery_memid'] =  $user_info['id'];
			if (empty($u))
				$u = $context['usergallery_memid'] ;
			// Check permissions

			if (!$g_manage && ($user_info['id'] != $u || !$g_gallery))
			{
				fatal_error($txt['gallery_user_noperm'],false);
			}
			$dbresult = $smcFunc['db_query']('', "
			SELECT
				USER_ID_CAT, title, roworder, id_parent
			FROM {db_prefix}gallery_usercat
			WHERE id_member = $u ORDER BY title ASC");

			$context['gallery_cat'] = array();


			while($row = $smcFunc['db_fetch_assoc']($dbresult))
				{
					// ID_CAT on purpose for Add Picture page
					$context['gallery_cat'][] = array(
						'ID_CAT' => $row['USER_ID_CAT'],
						'id_cat' => $row['USER_ID_CAT'],
						'title' => $row['title'],
						'roworder' => $row['roworder'],
						'id_parent'  => $row['id_parent'],
					);
				}
			$smcFunc['db_free_result']($dbresult);

			CreateGalleryPrettyCategory();
			$context['gallery_user_cat'] = $context['gallery_cat'];
		}

		// Main Categories
		if ($context['user']['is_guest'])
			$groupid = -1;
		else
			$groupid =  $user_info['groups'][0];


		$dbresult = $smcFunc['db_query']('', "
		SELECT
			c.ID_CAT, c.title, p.view, p.addpic, p.addvideo,  c.locked, c.ID_PARENT
		FROM {db_prefix}gallery_cat AS c
		LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.ID_GROUP = $groupid AND c.ID_CAT = p.ID_CAT)
		WHERE c.redirect = 0 ORDER BY c.title ASC");


		$context['gallery_cat'] = array();
		 while($row = $smcFunc['db_fetch_assoc']($dbresult))
			{
				$row['id_cat'] = $row['ID_CAT'];
				$row['id_parent'] = $row['ID_PARENT'];
				// Check if they have permission to add to this category.
				if ($context['video_select'] == 0)
                {
    				if ($row['view'] == '0' || $row['addpic'] == '0' )
    					continue;
				}
                else
                {
                    if ($row['view'] == '0' || $row['addvideo'] == '0' )
    					continue;
                }

				$context['gallery_cat'][] = $row;
			}
		$smcFunc['db_free_result']($dbresult);

		CreateGalleryPrettyCategory();


	}
}

function Gallery_SelectCat2()
{
	$u = 0;
	$cat = 0;
	if (isset($_REQUEST['u']))
		$u = (int) $_REQUEST['u'];

	if (isset($_REQUEST['cat']))
		$cat = (int) $_REQUEST['cat'];

    $video = 0;
	if (isset($_REQUEST['video']))
		$video = (int) $_REQUEST['video'];

	$addmode = 'add';
	if (isset($_REQUEST['addmode']))
	{
		$addmode = htmlspecialchars(trim($_REQUEST['addmode']),ENT_QUOTES);
	}

	$userVar = 'cat';
	if ($addmode == 'bulk')
		$userVar = 'usercat';

	if (!empty($cat) && !empty($u))
	{
		// User Gallery
        if ($video == 0)
		   redirectexit('action=gallery;sa=' . $addmode . ';' . $userVar .'=' . $cat . ';u=' . $u);
        else
         redirectexit('action=gallery;sa=addvideo;cat=' . $cat . ';u=' . $u);
	}
	else if (!empty($cat))
	{
		// Main Gallery
        if ($video == 0)
		  redirectexit('action=gallery;sa=' . $addmode .';cat=' . $cat);
        else
         redirectexit('action=gallery;sa=addvideo;cat=' . $cat);
	}
	else
	{
	    if ($video == 0)
		  redirectexit('action=gallery;sa=' . $addmode);
        else
         redirectexit('action=gallery;sa=addvideo');
	}

}

function Gallery_UpdateUserLatestCategory($ID_CAT)
{
	global $smcFunc;

	if (empty($ID_CAT))
		return;

	$result = $smcFunc['db_query']('', "
	select
		 max(id_picture) as LastID, USER_ID_CAT
	 from {db_prefix}gallery_pic
	WHERE approved = 1 AND USER_ID_CAT = $ID_CAT group by USER_ID_CAT");
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_usercat
		SET LAST_id_picture = " . $row['LastID'] . "
		where USER_ID_CAT = " . $ID_CAT);

	}

}

function GalleryCheckBadgeAwards($memID = 0)
{
	global $sourcedir, $modSettings;

	if (empty($memID))
		return;

	if (!empty($modSettings['badgeawards_enable']))
	{

		require_once($sourcedir . '/badgeawards2.php');
		Badges_CheckMember($memID);
	}
}

function GalleryDownloadItem()
{
	global $txt, $smcFunc, $modSettings, $context;

	$id = (int) $_REQUEST['id'];

	isAllowedTo('smfgallery_view');


	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_picture, videofile, filename, id_cat, orginalfilename 
		FROM {db_prefix}gallery_pic
		WHERE id_picture = $id and approved = 1  LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	if ($smcFunc['db_num_rows']($dbresult) == 0)
		fatal_error($txt['gallery_error_no_pic_selected'],false);

	if (!empty($row['id_cat']))
	{
		GetCatPermission($row['id_cat'],'view');
        GetCatPermission($row['id_cat'],'viewimagedetail');
    }


	if (substr($row['videofile'],0,7) == 'http://' || substr($row['videofile'],0,8) == 'https://')
	{
		fatal_error($txt['gallery_error_no_pic_selected'],false);
	}

    $context['template_layers'] = array();

	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']))
		@ob_start('ob_gzhandler');
	else
		ob_start();

	if (empty($row['orginalfilename']))
		header('Content-Disposition: attachment; filename='.$row['filename']);
	else
		header('Content-Disposition: attachment; filename='.$row['orginalfilename']);

	if (empty($row['videofile']))
		echo file_get_contents($modSettings['gallery_path'] . $row['filename']);
	else
		echo file_get_contents($modSettings['gallery_path'] . $row['videofile']);

	obExit(false);

	die("");
}

// Send all the administrators a lovely email.
function Gallery_emailAdmins($subject, $body, $additional_recipients = array())
{
	global $smcFunc, $sourcedir;

    // Fix subject line/body
    $body = str_replace("&#039;","'",$body);
    $subject = str_replace("&#039;","'",$subject);

    // We certainly want this.
	require_once($sourcedir . '/Subs-Post.php');

	// Load all groups which are effectively admins.
	$request = $smcFunc['db_query']('', '
		SELECT id_group
		FROM {db_prefix}permissions
		WHERE permission = {string:admin}
			AND add_deny = {int:add_deny}
			AND id_group != {int:id_group}',
		array(
			'add_deny' => 1,
			'id_group' => 0,
			'admin' => 'smfgallery_manage',
		)
	);
	$groups = array(1);
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$groups[] = $row['id_group'];
	$smcFunc['db_free_result']($request);

	$request = $smcFunc['db_query']('', '
		SELECT id_member, member_name, real_name, lngfile, email_address
		FROM {db_prefix}members
		WHERE (id_group IN ({array_int:group_list}) OR FIND_IN_SET({raw:group_array_implode}, additional_groups) != 0)
		ORDER BY lngfile',
		array(
			'group_list' => $groups,
			'group_array_implode' => implode(', additional_groups) != 0 OR FIND_IN_SET(', $groups),
		)
	);
	$emails_sent = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		// Stick their particulars in the replacement data.

		// Then send the actual email.
		sendmail($row['email_address'], $subject, $body, null, 'gallery', false, 1);

		// Track who we emailed so we don't do it twice.
		$emails_sent[] = $row['email_address'];
	}
	$smcFunc['db_free_result']($request);

	// Any additional users we must email this to?
	if (!empty($additional_recipients))
		foreach ($additional_recipients as $recipient)
		{
			if (in_array($recipient['email'], $emails_sent))
				continue;

			// Send off the email.
			sendmail($recipient['email'], $subject, $body, null, 'gallery', false, 1);
		}
}



function Gallery_CheckForUserGallery()
{
    global $context, $options, $txt, $user_info, $gallerySettings, $smcFunc;

    if (empty($gallerySettings['gallery_set_createusercat']))
        return;

    if (isset($options['gallery_usercat_made']))
        return;

    if ($context['user']['is_guest'])
    {
        return;
    }
    else
    {
        // Create default category if there is no
        $dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) as total
		FROM {db_prefix}gallery_usercat
		WHERE id_member = " . $user_info['id']);
        $userRow = $smcFunc['db_fetch_assoc']($dbresult);

        if ($userRow['total'] == 0)
        {
            	$smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_usercat
			(title, description,roworder,image,ID_PARENT,id_member,sortby,orderby)
		  VALUES ('" . $txt['gallery_txt_userdefaultcategorytitle'] . "', '" . $txt['gallery_txt_userdefaultcategorydescription'] . "',0,'',0," . $user_info['id'] . ",'p.id_picture','DESC')");

        }

            $smcFunc['db_query']('', "
				REPLACE INTO {db_prefix}themes
					(id_member, ID_THEME, variable, value)
				VALUES
                (" . $user_info['id']  . ",0,'gallery_usercat_made',1)
                ");

    }
}

function Gallery_WhoFavorited()
{
   global $txt, $context, $smcFunc;

	// Is the user allowed to view the gallery?
	isAllowedTo('smfgallery_view');

	$id = (int) $_REQUEST['pic'];
    if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected'],false);

	$context['gallery_picture_id'] = $id;

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			title
		FROM {db_prefix}gallery_pic
		WHERE id_picture = $id");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	if ($smcFunc['db_num_rows']($dbresult) == 0)
		fatal_error($txt['gallery_error_no_pic_selected'],false);


    $context['gallery_whofavorited'] = array();
  	$dbresult = $smcFunc['db_query']('',"
	SELECT
		f.id_member, m.real_name
	FROM {db_prefix}gallery_favorites  as f,{db_prefix}members AS m
	WHERE f.id_member = m.id_member AND f.id_picture = " . $context['gallery_picture_id']);
	while($row2 = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_whofavorited'][] = $row2;
	}
    $smcFunc['db_free_result']($dbresult);


	$context['gallery_picture_title'] = $txt['gallery_txt_viewfavorites'] . ' - ' .  $row['title'];

	$context['page_title'] = $txt['gallery_txt_viewfavorites'] . ' - ' . $row['title'];
	$context['sub_template'] = 'whofavorited';
}

function Gallery_TwitterSettings()
{
    global $context, $mbname, $txt;
	isAllowedTo('smfgallery_manage');

	DoGalleryAdminTabs();

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_twitter'];

	$context['sub_template']  = 'twittersettings';
}

function Gallery_TwitterSettings2()
{
    isAllowedTo('smfgallery_manage');


	$gallery_consumer_key = htmlspecialchars($_REQUEST['gallery_consumer_key'],ENT_QUOTES);
	$gallery_consumer_secret = htmlspecialchars($_REQUEST['gallery_consumer_secret'],ENT_QUOTES);

	updateSettings(
	array(
	'gallery_consumer_key' => $gallery_consumer_key,
	'gallery_consumer_secret' => $gallery_consumer_secret,
	)

	);

	redirectexit('action=admin;area=gallery;sa=twitter');

}

function Gallery_TwitterSignIn()
{
	global $sourcedir, $boardurl, $txt, $modSettings;
	require_once($sourcedir . '/twitteroauth.php');

	/* Build TwitterOAuth object with client credentials. */
	$connection = new TwitterOAuth($modSettings['gallery_consumer_key'], $modSettings['gallery_consumer_secret']);

	/* Get temporary credentials. */
	$request_token = $connection->getRequestToken($boardurl . '/gallerytwittercallback.php');


	/* Save temporary credentials to session. */
	$token = $request_token['oauth_token'];

    /* If last connection failed don't display authorization link. */
    switch ($connection->http_code) {
      case 200:
        /* Build authorize URL and redirect user to Twitter. */
        updateSettings(array('gallery_oauth_token' => $token, 'gallery_oauth_token_secret' => $request_token['oauth_token_secret']));

        $url = $connection->getAuthorizeURL($token);
        header('Location: ' . $url);
        exit;
        break;
      default:
      	  updateSettings(array('gallery_oauth_token' => '', 'gallery_oauth_token_secret' => ''));

        /* Show notification if something went wrong. */
        die($txt['gallery_twitter_signon_error'] . " - " . $connection->http_code);
    }

}

function Gallery_CopyrightRemoval()
{
    global $context, $mbname, $txt;
	isAllowedTo('smfgallery_manage');

    if (isset($_REQUEST['save']))
    {

        $gallery_copyrightkey = $_REQUEST['gallery_copyrightkey'];

        updateSettings(
    	array(
    	'gallery_copyrightkey' => $gallery_copyrightkey,
    	)

    	);
    }


	DoGalleryAdminTabs();

	$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_txt_copyrightremoval'];

	$context['sub_template']  = 'gallerycopyright';
}

function Gallery_TweetItem($subject = '', $pictureID = 0, $picUrl = '')
{
	global $modSettings, $sourcedir, $scripturl, $smcFunc;

    $subject = str_replace("&#039;","'",$subject);

	if (empty($subject))
		return;

	if (empty($pictureID))
		return;

	if (empty($modSettings['gallery_oauth_token']))
		return;

	if (empty($modSettings['gallery_oauth_token_secret']))
		return;

	if (empty($modSettings['gallery_consumer_key']))
		return;

	if (empty($modSettings['gallery_consumer_secret']))
		return;


	$subject = stripslashes($subject);


	$url = $scripturl . "?action=gallery;sa=view;id=" . $pictureID;

	$hashtags = '';
	if (strlen($subject) < 100)
	{
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			keywords
		FROM {db_prefix}gallery_pic
		WHERE id_picture = ". $pictureID);
		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$delimeter = ' ';
		if (substr_count($row['keywords'],',') > 0)
			$delimeter = ',';

		$keywords = explode($delimeter,$row['keywords']);

		if (!empty($keywords))
		{
			foreach($keywords as $word)
			{
				$hashtags .= '#' . $word . ' ';
			}

		}


	 	$hashtags = trim($hashtags);

	 	if (!empty($hashtags))
	 		$hashtags = ' ' . $hashtags;
	}


	if (!empty($modSettings['gallery_oauth_token']) && !empty($modSettings['gallery_oauth_token_secret']))
	{

		if (class_exists('TwitterOAuth') == false)
			require_once($sourcedir . '/twitteroauth.php');
	try
	{
		/* Create a TwitterOauth object with consumer/user tokens. */
		$connection = new TwitterOAuth($modSettings['gallery_consumer_key'], $modSettings['gallery_consumer_secret'], $modSettings['gallery_oauth_token'], $modSettings['gallery_oauth_token_secret']);

		/* If method is set change API call made. Test is called by default. */
		$content = $connection->get('account/verify_credentials');
		#

		$connection->post('statuses/update', array('status' => $subject . " " . $url . $hashtags));

	}
	catch (Exception $e)
	{

	    echo $e->getMessage();
	    log_error("Tweet Gallery Post:" .$e->getMessage());
	}


	}
}

function GalleryCheckInfo()
{
    global $modSettings, $boardurl;

    if (isset($modSettings['gallery_copyrightkey']))
    {
        $m = 1;
        if (!empty($modSettings['gallery_copyrightkey']))
        {
            if ($modSettings['gallery_copyrightkey'] == sha1($m . '-' . $boardurl))
            {
                return false;
            }
            else
                return true;
        }
    }

    return true;
}

function Gallery_AddToActivityStream($type = '', $id = 0 , $comment = '', $memID = 0)
{
    global $sourcedir;

    if (file_exists($sourcedir . '/Stream.php'))
    {
        require_once($sourcedir . '/Stream.php');


        if ($type == 'galleryproadd')
        {
            if (function_exists("as_log_galleryproadd"))
            {
                as_log_galleryproadd($id,$memID,$comment);
            }
        }

        if ($type == 'gallerypro_comments')
        {
            if (function_exists("as_log_gallerypro_comments"))
            {
                as_log_gallerypro_comments($id,$memID,$comment);
            }
        }

    }
}

function ChangePictureOwner($id,$oldMemberID)
{
    global $smcFunc;

                    $pic_postername = str_replace('"','', $_REQUEST['pic_postername']);
					$pic_postername = str_replace("'",'', $pic_postername);
					$pic_postername = str_replace('\\','', $pic_postername);
					$pic_postername = $smcFunc['htmlspecialchars']($pic_postername, ENT_QUOTES);

					$memid = 0;

					$dbresult = $smcFunc['db_query']('', "
					SELECT
						real_name, id_member
					FROM {db_prefix}members
					WHERE real_name = '$pic_postername' OR member_name = '$pic_postername'  LIMIT 1");
					$row4 = $smcFunc['db_fetch_assoc']($dbresult);
					$smcFunc['db_free_result']($dbresult);

					if ($smcFunc['db_affected_rows']() != 0)
					{
						// Member found update the picture owner
						$memid = $row4['id_member'];
						$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
						SET id_member = $memid WHERE id_picture = $id LIMIT 1");

                        if (!empty($oldMemberID))
							 UpdateMemberPictureTotals($oldMemberID);

						UpdateMemberPictureTotals($memid);
					}
}

function Gallery_InsertSMFTags($keywords = '', $topic = 0)
{

		global $user_info, $modSettings, $smcFunc;

        if (empty($keywords))
            return;

        if (isset($modSettings['smftags_set_maxtags']) || empty($modSettings['smftags_set_maxtags']))
            return;


        if (empty($topic))
            return;

		// Get how many tags there have been for the topic
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) as total
		FROM {db_prefix}tags_log
		WHERE ID_TOPIC = " . $topic);
		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$totaltags = $row['total'];
		$smcFunc['db_free_result']($dbresult);

		// Check Tag restrictions
		$tags = explode(',',$smcFunc['htmlspecialchars']($keywords,ENT_QUOTES));

		if ($totaltags < $modSettings['smftags_set_maxtags'])
		{
			$tagcount = 0;
			foreach($tags as $tag)
			{

				$tag = trim($tag);
				$tag = strtolower($tag);

				if ($tagcount >= $modSettings['smftags_set_maxtags'])
					continue;


				if (empty($tag))
					continue;

				//Check min tag length
				if (strlen($tag) < $modSettings['smftags_set_mintaglength'])
					continue;
				//Check max tag length
				if (strlen($tag) > $modSettings['smftags_set_maxtaglength'])
					continue;

				//Insert The tag
				$dbresult = $smcFunc['db_query']('', "
				SELECT
					ID_TAG
				FROM {db_prefix}tags
				WHERE tag = '$tag'");

				if ($smcFunc['db_affected_rows']() == 0)
				{
					//Insert into Tags table
					$smcFunc['db_query']('', "INSERT INTO {db_prefix}tags
						(tag, approved)
					VALUES ('$tag',1)");
					$ID_TAG = $smcFunc['db_insert_id']("{db_prefix}tags",'ID_TAG');
					//Insert into Tags log
					$smcFunc['db_query']('', "INSERT INTO {db_prefix}tags_log
						(ID_TAG,ID_TOPIC, ID_MEMBER)
					VALUES ($ID_TAG,$topic,$user_info[id])");

					$tagcount++;
				}
				else
				{
					$row = $smcFunc['db_fetch_assoc']($dbresult);
					$ID_TAG = $row['ID_TAG'];
					$dbresult2= $smcFunc['db_query']('', "
					SELECT
						ID FROM {db_prefix}tags_log
					WHERE ID_TAG  =  $ID_TAG  AND ID_TOPIC = $topic");
					if ($smcFunc['db_affected_rows']() != 0)
					{
						continue;

					}
					$smcFunc['db_free_result']($dbresult2);
					//Insert into Tags log

					$smcFunc['db_query']('', "INSERT INTO {db_prefix}tags_log
						(ID_TAG,ID_TOPIC, ID_MEMBER)
					VALUES ($ID_TAG,$topic,$user_info[id])");
					$tagcount++;

				}
				$smcFunc['db_free_result']($dbresult);
			}
		}


	//End Tagging System

}

function Gallery_LogAction($action = '',$pictureID = 0, $commentID = 0)
{
    global $smcFunc, $user_info;

    if (empty($action))
        return;

    $ip = htmlspecialchars($_SERVER['REMOTE_ADDR'],ENT_QUOTES);

    $t = time();

    $smcFunc['db_query']('', "INSERT INTO {db_prefix}gallery_modlog
    (ID_PICTURE,ID_COMMENT,action,ID_MEMBER,ipaddress,logdate)
    VALUES
    ($pictureID,$commentID,'$action'," . $user_info['id'] . ",'$ip',$t)
    ");



}

function Gallery_ModerationLog()
{
    global $mbname, $txt, $context, $smcFunc, $scripturl;

	isAllowedTo('smfgallery_manage');

	DoGalleryAdminTabs();



	$context['start'] = (int) $_REQUEST['start'];

		// Get Total Pages
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) AS total
		FROM {db_prefix}gallery_modlog
		");
		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$total = $row['total'];
		$smcFunc['db_free_result']($dbresult);


    $dbresult = $smcFunc['db_query']('', "
		  	SELECT
		  		c.ID_COMMENT, c.ID_PICTURE, c.action, c.logdate, c.ipaddress, c.ID_MEMBER,
		  		m.real_name, l.title as itemtitle
				  FROM {db_prefix}gallery_modlog as c
		  	LEFT JOIN {db_prefix}members AS m ON (c.ID_MEMBER = m.ID_MEMBER)
			LEFT JOIN {db_prefix}gallery_pic AS l ON (c.ID_PICTURE = l.ID_PICTURE)
		ORDER BY c.ID_LOG DESC LIMIT $context[start],10");
    $context['gallery_mod_log_entries'] = array();
    while($row = $smcFunc['db_fetch_assoc']($dbresult))
    {
      $context['gallery_mod_log_entries'][] = $row;
    }

    $smcFunc['db_free_result']($dbresult);

    $context['page_index'] = constructPageIndex($scripturl . '?action=admin;sa=gallery;sa=modlog' , $_REQUEST['start'], $total, 10);


	$context['page_title'] =  $mbname . ' - ' . $txt['gallery_txt_moderationcenter'];

	$context['sub_template']  = 'modlog';


}

function Gallery_EmptyModLog()
{
   global $smcFunc;

    isAllowedTo('smfgallery_manage');


   $smcFunc['db_query']('', "DELETE FROM {db_prefix}gallery_modlog
    ");

   Gallery_LogAction('clearedmodlog',0,0);

   redirectexit('action=admin;area=gallery;sa=modlog');

}


function Gallery_SEOUrlCategory($id, $seourl = '', $urlparameters = '')
{
    global $smcFunc, $gallerySettings, $scripturl;

    if (empty($gallerySettings['gallery_set_useseourls']))
    {
        $url = $scripturl . '?action=gallery;cat=' . $id;
        return $url;
    }
    else
    {
        // We are using seo urls
        if (!empty($seourl))
        {

        }
        else
        {

        }
    }

}

function Gallery_SEOUrlUserCategory($id, $seourl = '', $urlparameters = '')
{
    global $smcFunc, $gallerySettings, $scripturl;

    if (empty($gallerySettings['gallery_set_useseourls']))
    {

    }
    else
    {
        // We are using seo urls
        if (!empty($seourl))
        {

        }
        else
        {

        }
    }
}

function Gallery_SEOUrlPicture($id, $seourl = '', $urlparameters = '')
{
    global $smcFunc, $gallerySettings, $scripturl;

    if (empty($gallerySettings['gallery_set_useseourls']))
    {
        $url = $scripturl . '?action=gallery;sa=view;id=' . $id;
        return $url;
    }
    else
    {
        // We are using seo urls
        if (!empty($seourl))
        {

        }
        else
        {

        }
    }



}

function GalleryCreateThumbnail($filename,$width,$height)
{
    global $sourcedir, $modSettings;
    require_once($sourcedir . '/Subs-Graphics.php');


	$default_formats = array(
		'1' => 'gif',
		'2' => 'jpeg',
		'3' => 'png',
		'6' => 'bmp',
		'15' => 'wbmp'
	);

    $sizes = @getimagesize($filename);



    if ($sizes[2] == 1)
    {
    	createThumbnail($filename,$width,$height);
    		 $modSettings['enableErrorLogging'] = 0;
             require_once($sourcedir . '/class.gifresize.php');
             $nGif = new GIF_eXG($filename,1);
             $nGif->resize($filename . '_thumb',$width,$height,1,1);
              $modSettings['enableErrorLogging'] = 1;
    }
    else if ($sizes[2] == 6)
	{

		$modSettings['gallery_ovveride_mime'] = 1;

		$destName = $filename . '_thumb.tmp';


		$success = resizeImageFile($filename, $destName, $width,$height, 6);

		// Okay, we're done with the temporary stuff.
		$destName = substr($destName, 0, -4);

		if ($success && @rename($destName . '.tmp', $destName))
			return true;
		else
		{
			@unlink($destName . '.tmp');
			@touch($destName);
			return false;
		}


	}
    else
        createThumbnail($filename,$width,$height);
}

function GetTotalByCATID($id_cat, $CatTotal = -1)
{
	global $smcFunc;

	// Check if we have to count the category
	if ($CatTotal != -1)
		return $CatTotal;

	$dbresult = $smcFunc['db_query']('', "
	SELECT
		total
	FROM {db_prefix}gallery_cat WHERE id_cat = $id_cat");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	$smcFunc['db_free_result']($dbresult);

	if ($row['total'] != -1)
		return $row['total'];
	else
	{
		$dbresult = $smcFunc['db_query']('', "
		SELECT
			COUNT(*) AS total
		FROM {db_prefix}gallery_pic
		WHERE id_cat = $id_cat AND approved = 1");
		$row = $smcFunc['db_fetch_assoc']($dbresult);
		$total = $row['total'];
		$smcFunc['db_free_result']($dbresult);

		// Update the count
		$dbresult = $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat SET total = $total WHERE id_cat = $id_cat LIMIT 1");

		// Return the total pictures
		return $total;
	}
}

function GetPictureTotals($id_cat,$CatTotal = -1)
{
	global $modSettings, $smcFunc, $subcats_linktree, $scripturl, $gallerySettings;

	$total = 0;

	$total += GetTotalByCATID($id_cat,$CatTotal);
	$subcats_linktree = '';
	$firstSub = 0;

	// Get the child categories to this category
	if ($modSettings['gallery_set_count_child'])
	{
		$dbresult3 = $smcFunc['db_query']('', "
		SELECT
			id_cat, total, title
		FROM {db_prefix}gallery_cat
		WHERE id_parent = $id_cat ORDER BY roworder ASC");
		while($row3 = $smcFunc['db_fetch_assoc']($dbresult3))
		{
			if ($firstSub == 1)
				$subcats_linktree .= ',&nbsp;';

			$firstSub = 1;

			$subcats_linktree .= '<a href="' . $scripturl . '?action=gallery;cat=' . $row3['id_cat'] . '">' . $row3['title'] . '</a>';

			if ($row3['total'] == -1)
			{
				$dbresult = $smcFunc['db_query']('', "SELECT COUNT(*) AS total FROM {db_prefix}gallery_pic WHERE id_cat = " . $row3['id_cat'] . " AND approved = 1");
				$row = $smcFunc['db_fetch_assoc']($dbresult);
				$total2 = $row['total'];
				$smcFunc['db_free_result']($dbresult);


				$dbresult = $smcFunc['db_query']('', "UPDATE {db_prefix}gallery_cat SET total = $total2 WHERE id_cat =  " . $row3['id_cat'] . " LIMIT 1");
			}
		}
		$smcFunc['db_free_result']($dbresult3);


		$dbresult3 = $smcFunc['db_query']('', "
		SELECT
			total, ID_CAT, ID_PARENT
		FROM {db_prefix}gallery_cat
		WHERE ID_PARENT <> 0");

		$childArray = array();
		while($row3 = $smcFunc['db_fetch_assoc']($dbresult3))
		{
			$childArray[] = $row3;
		}

		$total += Gallery_GetPictureTotalsByParent($id_cat,$childArray);

	}

	// Hide subcategory link display
	if ($gallerySettings['gallery_set_show_subcategory_links'])
	{
		$subcats_linktree = '';
	}

	return $total;
}


function Gallery_GetPictureTotalsByParent($ID_PARENT,$data)
{
	$total = 0;
	foreach($data as $row)
	{
		if ($row['ID_PARENT'] == $ID_PARENT)
		{
			$total += $row['total'];
			$total += Gallery_GetPictureTotalsByParent($row['ID_CAT'],$data);
		}
	}

	return $total;
}

function gallery_showadminbar()
{
	global $scripturl, $txt, $smcFunc, $context;

    if (function_exists("set_tld_regex"))
    {
    	$context['gallery21beta'] = true;
    }


	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		' . $txt['gallery_text_adminpanel'] . '
		</h3>
</div>
<table class="' . ($context['gallery21beta'] == false ? 'table_list' : 'table_grid') . '">
	<tr class="windowbg2 centertext">
		<td align="center"><a href="' . $scripturl . '?action=gallery;sa=addcat">' . $txt['gallery_text_addcategory'] . '</a>&nbsp;-&nbsp;<a href="' . $scripturl . '?action=admin;area=gallery;sa=adminset">' . $txt['gallery_text_settings'] . '</a>';

	if (allowedTo('manage_permissions'))
		echo '&nbsp;-&nbsp;<a href="', $scripturl, '?action=admin;area=permissions">', $txt['gallery_text_permissions'], '</a>';

	// Pictures waiting for approval
	$dbresult3 = $smcFunc['db_query']('', "SELECT COUNT(*) as totalpics FROM {db_prefix}gallery_pic WHERE approved = 0");
	$row2 = $smcFunc['db_fetch_assoc']($dbresult3);
	$totalpics = $row2['totalpics'];
	$smcFunc['db_free_result']($dbresult3);
	echo '<br />' . $txt['gallery_text_imgwaitapproval'] . '<b>' . $totalpics . '</b>&nbsp;&nbsp;<a href="' . $scripturl . '?action=admin;area=gallery;sa=approvelist">' . $txt['gallery_text_imgcheckapproval'] . '</a>';

	// Reported Pictures
	$dbresult4 = $smcFunc['db_query']('', "SELECT COUNT(*) as totalreport FROM {db_prefix}gallery_report");
	$row2 = $smcFunc['db_fetch_assoc']($dbresult4);
	$totalreport = $row2['totalreport'];
	$smcFunc['db_free_result']($dbresult4);
	echo '<br />' . $txt['gallery_text_imgreported'] . '<b>' . $totalreport . '</b>&nbsp;&nbsp;<a href="' . $scripturl . '?action=admin;area=gallery;sa=approvelist">' . $txt['gallery_text_imgcheckreported'] . '</a>';

	// Total Comments Rating for Approval
	$dbresult5 = $smcFunc['db_query']('', "SELECT COUNT(*) as totalcom FROM {db_prefix}gallery_comment WHERE approved = 0");
	$row2 = $smcFunc['db_fetch_assoc']($dbresult5);
	$totalcomments = $row2['totalcom'];
	$smcFunc['db_free_result']($dbresult5);
	echo '<br />' . $txt['gallery_text_comwaitapproval'] . '<b>' . $totalcomments . '</b>&nbsp;&nbsp;<a href="' . $scripturl . '?action=admin;area=gallery;sa=commentlist">' . $txt['gallery_text_comcheckapproval'] . '</a>';

	// Total reported Comments
	$dbresult6 = $smcFunc['db_query']('', "SELECT COUNT(*) as totalcreport FROM {db_prefix}gallery_creport");
	$row2 = $smcFunc['db_fetch_assoc']($dbresult6);
	$totalcomments = $row2['totalcreport'];
	$smcFunc['db_free_result']($dbresult6);
	echo '<br />' . $txt['gallery_text_comreported'] . '<b>' . $totalcomments . '</b>&nbsp;&nbsp;<a href="' . $scripturl . '?action=admin;area=gallery;sa=commentlist">' . $txt['gallery_text_comcheckreported'] . '</a>';

	echo '
		</td>
	</tr>
</table><br /><br />';
}

function Gallery_MainPageBlock($title, $type = 'recent')
{
	global $smcFunc, $scripturl, $settings, $txt, $modSettings, $user_info, $context, $gallerySettings;

    if (function_exists("set_tld_regex"))
    {
    	$context['gallery21beta'] = true;
    }


	if (!$context['user']['is_guest'])
		$groupsdata = implode(',',$user_info['groups']);
	else
		$groupsdata = -1;

	if (empty($gallerySettings['gallery_index_images_to_show']))
		$gallerySettings['gallery_index_images_to_show'] = 4;

	$maxrowlevel =  empty($modSettings['gallery_set_images_per_row']) ? 4 : $modSettings['gallery_set_images_per_row'];

	echo '
	<div class="cat_bar">
		<h3 class="catbg centertext">
		', $title;


	// Check what type it is
	$query = "
		SELECT
			p.id_picture, p.commenttotal, p.totalratings, p.rating, p.filesize, p.views,
			p.thumbfilename, p.title, p.id_member, m.real_name, p.date, p.description,
			p.mature, c.view, (p.rating / p.totalratings ) AS ratingaverage, v.id_picture as unread, mg.online_color,
			p.totallikes
		FROM {db_prefix}gallery_pic as p
		LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
		LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
		LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.id_group IN ($groupsdata) AND c.id_cat = p.id_cat)
		LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $context['user']['id'] . " AND v.user_id_cat = p.USER_ID_CAT)
		WHERE ((s.private = 0 OR s.private IS NULL) AND (s.password = '' OR s.password IS NULL) AND p.user_id_cat != 0 AND p.approved = 1) OR (p.approved = 1 AND p.user_id_cat = 0 AND (c.view IS NULL OR c.view = 1))
		GROUP by p.id_picture ORDER BY ";

	switch($type)
	{
		case 'viewed':
			$query .= "p.views DESC LIMIT " . $gallerySettings['gallery_index_images_to_show'];
			echo ' <a href="',$scripturl,'?action=gallery;sa=listall;type=views">',$txt['gallery_stats_listall'],'</a>';
		break;

		case 'mostcomments':
			$query .= "p.commenttotal DESC LIMIT " . $gallerySettings['gallery_index_images_to_show'];
			echo ' <a href="',$scripturl,'?action=gallery;sa=listall;type=comments">',$txt['gallery_stats_listall'],'</a>';
		break;

		case 'mostliked':
			$query .= "p.totallikes DESC LIMIT " . $gallerySettings['gallery_index_images_to_show'];
			echo ' <a href="',$scripturl,'?action=gallery;sa=listall;type=likes">',$txt['gallery_stats_listall'],'</a>';
		break;

		case 'toprated':
			$query .= "ratingaverage DESC, p.totalratings DESC LIMIT " . $gallerySettings['gallery_index_images_to_show'];
			echo ' <a href="',$scripturl,'?action=gallery;sa=listall;type=toprated">',$txt['gallery_stats_listall'],'</a>';
		break;

		case 'recentcomments':

$query = "
		SELECT
			max(com.ID_COMMENT) AS lastcomment, p.id_picture, p.commenttotal, p.totalratings, p.rating, p.filesize, p.views, p.thumbfilename, p.title,
			p.id_member, m.real_name, p.date, p.description, p.mature, c.view, v.id_picture as unread, mg.online_color, p.totallikes
		FROM ({db_prefix}gallery_pic as p, {db_prefix}gallery_comment as com)
		LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.id_member)
		LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
		LEFT JOIN {db_prefix}gallery_usersettings AS s ON (s.id_member = m.id_member)
		LEFT JOIN {db_prefix}gallery_catperm AS c ON (c.id_group IN ($groupsdata) AND c.id_cat = p.id_cat)
						LEFT JOIN {db_prefix}gallery_log_mark_view AS v ON (p.id_picture = v.id_picture AND v.id_member = " . $context['user']['id'] . " AND v.user_id_cat = p.USER_ID_CAT)
		WHERE com.id_picture = p.id_picture AND ( ((s.private = 0 OR s.private IS NULL) AND (s.password = '' OR s.password IS NULL) AND p.user_id_cat != 0 AND p.approved = 1) OR (p.approved = 1 AND p.user_id_cat = 0 AND (c.view IS NULL OR c.view = 1)))
		GROUP by p.id_picture ORDER BY ";

			$query .= "lastcomment DESC LIMIT " . $gallerySettings['gallery_index_images_to_show'];
			echo ' <a href="',$scripturl,'?action=gallery;sa=listall;type=recentcomments">',$txt['gallery_stats_listall'],'</a>';
		break;
		case 'random':
			$query .= " RAND() DESC LIMIT " . $gallerySettings['gallery_index_images_to_show'];
			echo ' <a href="',$scripturl,'?action=gallery;sa=listall;type=random">',$txt['gallery_stats_listall'],'</a>';
		break;

		default:
			$query .= "p.id_picture DESC LIMIT " . $gallerySettings['gallery_index_images_to_show'];
			echo ' <a href="',$scripturl,'?action=gallery;sa=listall;type=recent">',$txt['gallery_stats_listall'],'</a>';
		break;
	}

	echo '</h3>
</div>

	<table class="' . (!function_exists("set_tld_regex") ? 'table_list' : 'table_grid') . '">
		';

	$smfgalleryBlockCache = array();


		$dbresult = $smcFunc['db_query']('', $query);
		while($row = $smcFunc['db_fetch_assoc']($dbresult))
		{
			$smfgalleryBlockCache[] = $row;
		}
		$smcFunc['db_free_result']($dbresult);



	$rowlevel = 0;
	if (count($smfgalleryBlockCache) > 0)
	foreach($smfgalleryBlockCache as $row)
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
			'hidemanagementlinks' => 1,
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
		echo '</tr>';
	}

	echo '
		  </table><br />';

}

function Gallery_ShowSubCats($cat,$g_manage)
{
	global $txt, $smcFunc, $scripturl, $modSettings, $subcats_linktree, $user_info, $context, $gallerySettings;

	if ($context['user']['is_guest'])
		$groupid = -1;
	else
		$groupid =  $user_info['groups'][0];

	// Count all the categories
    $dbquery = $smcFunc['db_query']('', '
        SELECT c.id_cat, c.title
    FROM {db_prefix}gallery_cat AS c
    WHERE c.id_parent = '. $cat . (isset($_REQUEST['searchresult']) && isset($_REQUEST['searchcat']) && !empty($_REQUEST['searchcat']) ? ' AND c.title LIKE "%' . $_REQUEST['searchcat'] . '%"' : ''));
 
    $cats_total = $smcFunc['db_num_rows']($dbquery);
    $smcFunc['db_free_result']($dbquery);
 
    // Create the page index
    $context['page_index_top'] = constructPageIndex($scripturl . '?action=gallery;cat=' . $cat . (isset($_REQUEST['searchresult']) && isset($_REQUEST['searchcat']) && !empty($_REQUEST['searchcat']) ? ';searchresult;searchcat=' . $_REQUEST['searchcat'] : ''), $_GET['start'], $cats_total , 20);
 
    //List all the categories
    $dbresult = $smcFunc['db_query']('', '
        SELECT
            c.id_cat, c.title, p.view, c.roworder, c.description, c.image, c.filename, c.total,
            l.id_picture, l.title pictitle, l.date, m.id_member, m.real_name, c.redirect, mg.online_color
        FROM {db_prefix}gallery_cat AS c
            LEFT JOIN {db_prefix}gallery_catperm AS p ON (p.id_group = '. $groupid . ' AND c.id_cat = p.id_cat)
            LEFT JOIN {db_prefix}gallery_pic AS l ON (c.LAST_id_picture = l.id_picture)
            LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.id_member)
            LEFT JOIN {db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(m.ID_GROUP = 0, m.ID_POST_GROUP, m.ID_GROUP))
 
        WHERE c.id_parent = '.  $cat . (isset($_REQUEST['searchresult']) && isset($_REQUEST['searchcat']) && !empty($_REQUEST['searchcat']) ? ' AND c.title LIKE "%' . $_REQUEST['searchcat'] . '%"' : '') . '
        GROUP BY c.id_cat
        ORDER BY c.title ASC
        LIMIT '. $_GET['start'] . ', 20');
 
    If ($cats_total > 20 || isset($_REQUEST['searchresult'])) {
        echo '
        <div><form method="post" action="', $scripturl, '?action=gallery;cat=' . $cat. ';searchresult" class="floatright">
            <input type="text" name="searchcat" />
            <input type="submit" class="button_submit" value="', $txt['search'], '" />
        </form></div>
        <div>' . $context['page_index_top'] . '</div>';
    }
    if ($smcFunc['db_affected_rows']() != 0)
	{
		echo '<br /><table border="0" cellspacing="1" cellpadding="5" class="table_grid" style="margin-top: 1px;" align="center" width="100%">
				<thead>
			<tr class="catbg">
				<th scope="col" class="smalltext first_th">&nbsp;</th>
				<th scope="col" class="smalltext">' . $txt['gallery_text_galleryname'] . '</th>
				<th scope="col" class="smalltext" align="center">' . $txt['gallery_text_totalimages'] . '</th>
				';
			$num_cols =3;

		if (!empty($gallerySettings['gallery_set_show_cat_latest_pictures']))
		{
			echo '<th scope="col" class="smalltext" align="center">', $txt['gallery_latest_posts'], '</th>';
			$num_cols++;
		}

		if ($g_manage)
		{
			echo '
				<th scope="col" class="smalltext">' . $txt['gallery_text_reorder'] . '</th>
				<th scope="col" class="smalltext last_th">' . $txt['gallery_text_options'] . '</th>
				';
			$num_cols = $num_cols + 2;
		}

		echo '</tr>
		</thead>
		';

		while($row = $smcFunc['db_fetch_assoc']($dbresult))
		{
			$cat_url = '';

			// Check permission to show the gallery category
			if ($row['view'] == '0')
				continue;

			$totalpics = GetPictureTotals($row['id_cat'], $row['total']);
			$cat_url = $scripturl . '?action=gallery;cat=' . $row['id_cat'];

			echo '<tr>';

			if ($row['image'] == '' && $row['filename'] == '')
				echo '<td class="windowbg" width="10%"></td><td  class="windowbg2"><b><a href="' . $cat_url . '">' . parse_bbc($row['title']) . '</a></b>' .  ((!empty($gallerySettings['gallery_enable_rss']) && ($row['redirect'] == 0)) ? ' <a href="' . $scripturl . '?action=gallery;sa=rss;cat=' . $row['id_cat'] . '"><img src="' . $modSettings['gallery_url'] . '/rss.png" alt="rss" /></a>' : '') . '<br />' . parse_bbc($row['description']) . '</td>';
			else
			{
				if ($row['filename'] == '')
					echo '<td class="windowbg" width="10%"><a href="' . $cat_url . '"><img src="' . $row['image'] . '" alt="" /></a></td>';
				else
					echo '<td class="windowbg" width="10%"><a href="' . $cat_url . '"><img src="' . $modSettings['gallery_url'] . 'catimgs/' . $row['filename'] . '" alt="" /></a></td>';


				echo '<td class="windowbg2"><b><a href="' . $cat_url . '">' . parse_bbc($row['title']) . '</a></b>' . ((!empty($gallerySettings['gallery_enable_rss']) && ($row['redirect'] == 0)) ? ' <a href="' . $scripturl . '?action=gallery;sa=rss;cat=' . $row['id_cat'] . '"><img src="' . $modSettings['gallery_url'] . '/rss.png" alt="rss" /></a>' : '') . '<br />' . parse_bbc($row['description']) . '</td>';
			}

			// Show total pictures in the category
			echo '<td align="center"  valign="middle" class="windowbg">' . $totalpics . '</td>';


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
				echo '<td class="windowbg2"><a href="' . $scripturl . '?action=gallery;sa=catup;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_up'] . '</a>&nbsp;<a href="' . $scripturl . '?action=gallery;sa=catdown;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_down'] . '</a></td>
				<td class="windowbg"><a href="' . $scripturl . '?action=gallery;sa=editcat;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_edit'] . '</a>&nbsp;<a href="' . $scripturl . '?action=gallery;sa=deletecat;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_delete'] . '</a>
				<br /><br />
				<a href="' . $scripturl . '?action=gallery;sa=catperm;cat=' . $row['id_cat'] . '">[' . $txt['gallery_text_permissions'] . ']</a>
				<br />
				<a href="' . $scripturl . '?action=gallery;sa=import;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_importpics'] . '</a>
				<br />
				<a href="' . $scripturl . '?action=gallery;sa=regen;cat=' . $row['id_cat'] . '">' . $txt['gallery_text_regeneratethumbnails'] . '</a>
				</td>';
			}


			echo '</tr>';


			if ($subcats_linktree  != '')
				echo '
		<tr>
			<td colspan="',$num_cols,'" class="windowbg3">
				<span class="smalltext">',($subcats_linktree != '' ? '<b>' . $txt['gallery_sub_cats'] . '</b>' . $subcats_linktree : ''),'</span>
			</td>
		</tr>';
		}
		$smcFunc['db_free_result']($dbresult);
		echo '</table><br /><br />';
		If ($cats_total > 20) {
            echo '
            <div class="pagesection">
                <span>', $context['page_index_top'], '</span>
            </div>';
        }  
	}
}

function Gallery_GetMimeType($filename, $imageOnly = true)
{
	$extension = strtolower(substr(strrchr($filename, '.'), 1));

	if ($imageOnly  == true)
	{
		switch ($extension)
		{

			case 'jpeg';
				return 'image/jpeg';
				break;

			case 'jpg';
				return 'image/jpeg';
				break;

			case 'gif';
				return 'image/gif';
				break;

			case 'png';
				return 'image/png';
				break;

			case 'tiff';
				return 'image/tiff';
				break;

			case 'bmp';
				return 'image/bmp';
				break;

			default:
				return '';
		}
	}
	else
	{
		switch ($extension)
		{

			case 'jpeg';
				return 'image/jpeg';
				break;

			case 'jpg';
				return 'image/jpeg';
				break;

			case 'gif';
				return 'image/gif';
				break;

			case 'png';
				return 'image/png';
				break;

			case 'tiff';
				return 'image/tiff';
				break;

			case 'bmp';
				return 'image/bmp';
				break;

			default:
				return '';
		}
	}

}

function Gallery_ViewLikes()
{
	global $txt, $smcFunc, $context, $scripturl;

	isAllowedTo('smfgallery_view');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	$context['gallery_pic_id'] = $id;


	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_picture, videofile, filename, id_cat, title
		FROM {db_prefix}gallery_pic
		WHERE id_picture = $id and approved = 1  LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);
	if ($smcFunc['db_num_rows']($dbresult) == 0)
		fatal_error($txt['gallery_error_no_pic_selected'],false);

	if (!empty($row['id_cat']))
	{
		GetCatPermission($row['id_cat'],'view');
        GetCatPermission($row['id_cat'],'viewimagedetail');
    }


	$context['gallery_likes'] = '';

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			 l.ID_PICTURE, l.ID_LIKE, l.logdate, l.ID_MEMBER, m.real_name
		FROM {db_prefix}gallery_like as l, {db_prefix}members as m
		WHERE m.ID_MEMBER = l.ID_MEMBER AND l.ID_PICTURE = $id");
	while($rowLike = $smcFunc['db_fetch_assoc']($dbresult))
	{
		$context['gallery_likes'] .= ' <a href="' . $scripturl . '?action=profile;u=' . $rowLike['ID_MEMBER'] . '">'  . $rowLike['real_name'] . '</a>';
	}


	// Load the template
	$context['sub_template']  = 'viewlikes';
	// Set the page title
	$context['page_title'] = $txt['gallery_txt_view_likes'] . ' - ' . $row['title'];

}

function Gallery_Like()
{
	global $txt, $smcFunc, $txt, $user_info;

	is_not_guest();

	isAllowedTo('smfgallery_view');


	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_picture, videofile, filename, id_cat
		FROM {db_prefix}gallery_pic
		WHERE id_picture = $id and approved = 1 LIMIT 1");
	$row = $smcFunc['db_fetch_assoc']($dbresult);

	if ($smcFunc['db_num_rows']($dbresult) == 0)
		fatal_error($txt['gallery_error_no_pic_selected'],false);

	// Check if already liked
	$dbresult = $smcFunc['db_query']('', "
		SELECT
			count(*) as total
		FROM {db_prefix}gallery_like
		WHERE id_picture = $id and ID_MEMBER = " . $user_info['id']);
	$row = $smcFunc['db_fetch_assoc']($dbresult);

	if ($row['total'] > 0)
	{
		fatal_error($txt['gallery_err_alreadyliked'],false);
	}


	// Insert the like
	$t = time();
	$smcFunc['db_query']('', "
	INSERT INTO {db_prefix}gallery_like
	(ID_PICTURE,ID_MEMBER,logdate)
	VALUES
	($id," . $user_info['id'] . ",$t)");


	$smcFunc['db_query']('', "UPDATE {db_prefix}gallery_pic
		SET totallikes = totallikes + 1 WHERE ID_PICTURE = $id LIMIT 1");


	// Redirect back to the picture
	redirectexit('action=gallery;sa=view;id=' . $id);

}

function Gallery_MarkUnviewed()
{
	global $txt, $smcFunc, $user_info;

	is_not_guest();

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);


 	$smcFunc['db_query']('', "
				DELETE FROM {db_prefix}gallery_log_mark_view
				WHERE id_picture = $id AND id_member = " . $user_info['id']);


	redirectexit('action=gallery;sa=unviewed');
}

function Gallery_MarkAllViewed()
{
	global $smcFunc, $user_info, $settings;

	$t = time();



     $smcFunc['db_query']('', "
				REPLACE INTO {db_prefix}themes
					(id_member, ID_THEME, variable, value)
				VALUES
                (" . $user_info['id']  . "," . $settings['theme_id']. ",'gallery_markviewedtime','$t')
                ");

    cache_put_data('theme_settings-' . $settings['theme_id'], null, 90);

    redirectexit('action=gallery;sa=unviewed');
}


/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines http://www.simplemachines.org
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0.12
 *
 * Removed permissionchecks
 */
function gallery_removeMessage($message, $decreasePostCount = true)
{
	global $board, $sourcedir, $modSettings, $user_info, $smcFunc, $context;

	if (empty($message) || !is_numeric($message))
		return false;

	$request = $smcFunc['db_query']('', '
		SELECT
			m.id_member, m.icon, m.poster_time, m.subject,' . (empty($modSettings['search_custom_index_config']) ? '' : ' m.body,') . '
			m.approved, t.id_topic, t.id_first_msg, t.id_last_msg, t.num_replies, t.id_board,
			t.id_member_started AS id_member_poster,
			b.count_posts
		FROM {db_prefix}messages AS m
			INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
		WHERE m.id_msg = {int:id_msg}
		LIMIT 1',
		array(
			'id_msg' => $message,
		)
	);
	if ($smcFunc['db_num_rows']($request) == 0)
		return false;
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);



	// Close any moderation reports for this message.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_reported
		SET closed = {int:is_closed}
		WHERE id_msg = {int:id_msg}',
		array(
			'is_closed' => 1,
			'id_msg' => $message,
		)
	);
	if ($smcFunc['db_affected_rows']() != 0)
	{
		require_once($sourcedir . '/ModerationCenter.php');
		updateSettings(array('last_mod_report_action' => time()));
		recountOpenReports();
	}

	// Delete the *whole* topic, but only if the topic consists of one message.
	if ($row['id_first_msg'] == $message)
	{

		// ...if there is only one post.
		if (!empty($row['num_replies']))
			fatal_lang_error('delFirstPost', false);

		removeTopics($row['id_topic']);
		return true;
	}

	// Deleting a recycled message can not lower anyone's post count.
	if ($row['icon'] == 'recycled')
		$decreasePostCount = false;

	// This is the last post, update the last post on the board.
	if ($row['id_last_msg'] == $message)
	{
		// Find the last message, set it, and decrease the post count.
		$request = $smcFunc['db_query']('', '
			SELECT id_msg, id_member
			FROM {db_prefix}messages
			WHERE id_topic = {int:id_topic}
				AND id_msg != {int:id_msg}
			ORDER BY ' . ($modSettings['postmod_active'] ? 'approved DESC, ' : '') . 'id_msg DESC
			LIMIT 1',
			array(
				'id_topic' => $row['id_topic'],
				'id_msg' => $message,
			)
		);
		$row2 = $smcFunc['db_fetch_assoc']($request);
		$smcFunc['db_free_result']($request);

		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET
				id_last_msg = {int:id_last_msg},
				id_member_updated = {int:id_member_updated}' . (!$modSettings['postmod_active'] || $row['approved'] ? ',
				num_replies = CASE WHEN num_replies = {int:no_replies} THEN 0 ELSE num_replies - 1 END' : ',
				unapproved_posts = CASE WHEN unapproved_posts = {int:no_unapproved} THEN 0 ELSE unapproved_posts - 1 END') . '
			WHERE id_topic = {int:id_topic}',
			array(
				'id_last_msg' => $row2['id_msg'],
				'id_member_updated' => $row2['id_member'],
				'no_replies' => 0,
				'no_unapproved' => 0,
				'id_topic' => $row['id_topic'],
			)
		);
	}
	// Only decrease post counts.
	else
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET ' . ($row['approved'] ? '
				num_replies = CASE WHEN num_replies = {int:no_replies} THEN 0 ELSE num_replies - 1 END' : '
				unapproved_posts = CASE WHEN unapproved_posts = {int:no_unapproved} THEN 0 ELSE unapproved_posts - 1 END') . '
			WHERE id_topic = {int:id_topic}',
			array(
				'no_replies' => 0,
				'no_unapproved' => 0,
				'id_topic' => $row['id_topic'],
			)
		);

	// Default recycle to false.
	$recycle = false;

	// If recycle topics has been set, make a copy of this message in the recycle board.
	// Make sure we're not recycling messages that are already on the recycle board.
	if (!empty($modSettings['recycle_enable']) && $row['id_board'] != $modSettings['recycle_board'] && $row['icon'] != 'recycled')
	{
		// Check if the recycle board exists and if so get the read status.
		$request = $smcFunc['db_query']('', '
			SELECT (IFNULL(lb.id_msg, 0) >= b.id_msg_updated) AS is_seen, id_last_msg
			FROM {db_prefix}boards AS b
				LEFT JOIN {db_prefix}log_boards AS lb ON (lb.id_board = b.id_board AND lb.id_member = {int:current_member})
			WHERE b.id_board = {int:recycle_board}',
			array(
				'current_member' => $user_info['id'],
				'recycle_board' => $modSettings['recycle_board'],
			)
		);
		if ($smcFunc['db_num_rows']($request) == 0)
			fatal_lang_error('recycle_no_valid_board');
		list ($isRead, $last_board_msg) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		// Is there an existing topic in the recycle board to group this post with?
		$request = $smcFunc['db_query']('', '
			SELECT id_topic, id_first_msg, id_last_msg
			FROM {db_prefix}topics
			WHERE id_previous_topic = {int:id_previous_topic}
				AND id_board = {int:recycle_board}',
			array(
				'id_previous_topic' => $row['id_topic'],
				'recycle_board' => $modSettings['recycle_board'],
			)
		);
		list ($id_recycle_topic, $first_topic_msg, $last_topic_msg) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		// Insert a new topic in the recycle board if $id_recycle_topic is empty.
		if (empty($id_recycle_topic))
			$smcFunc['db_insert']('',
				'{db_prefix}topics',
				array(
					'id_board' => 'int', 'id_member_started' => 'int', 'id_member_updated' => 'int', 'id_first_msg' => 'int',
					'id_last_msg' => 'int', 'unapproved_posts' => 'int', 'approved' => 'int', 'id_previous_topic' => 'int',
				),
				array(
					$modSettings['recycle_board'], $row['id_member'], $row['id_member'], $message,
					$message, 0, 1, $row['id_topic'],
				),
				array('id_topic')
			);

		// Capture the ID of the new topic...
		$topicID = empty($id_recycle_topic) ? $smcFunc['db_insert_id']('{db_prefix}topics', 'id_topic') : $id_recycle_topic;

		// If the topic creation went successful, move the message.
		if ($topicID > 0)
		{
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}messages
				SET
					id_topic = {int:id_topic},
					id_board = {int:recycle_board},
					icon = {string:recycled},
					approved = {int:is_approved}
				WHERE id_msg = {int:id_msg}',
				array(
					'id_topic' => $topicID,
					'recycle_board' => $modSettings['recycle_board'],
					'id_msg' => $message,
					'recycled' => 'recycled',
					'is_approved' => 1,
				)
			);

			// Take any reported posts with us...
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}log_reported
				SET
					id_topic = {int:id_topic},
					id_board = {int:recycle_board}
				WHERE id_msg = {int:id_msg}',
				array(
					'id_topic' => $topicID,
					'recycle_board' => $modSettings['recycle_board'],
					'id_msg' => $message,
				)
			);

			// Mark recycled topic as read.
			if (!$user_info['is_guest'])
				$smcFunc['db_insert']('replace',
					'{db_prefix}log_topics',
					array('id_topic' => 'int', 'id_member' => 'int', 'id_msg' => 'int'),
					array($topicID, $user_info['id'], $modSettings['maxMsgID']),
					array('id_topic', 'id_member')
				);

			// Mark recycle board as seen, if it was marked as seen before.
			if (!empty($isRead) && !$user_info['is_guest'])
				$smcFunc['db_insert']('replace',
					'{db_prefix}log_boards',
					array('id_board' => 'int', 'id_member' => 'int', 'id_msg' => 'int'),
					array($modSettings['recycle_board'], $user_info['id'], $modSettings['maxMsgID']),
					array('id_board', 'id_member')
				);

			// Add one topic and post to the recycle bin board.
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}boards
				SET
					num_topics = num_topics + {int:num_topics_inc},
					num_posts = num_posts + 1' .
						($message > $last_board_msg ? ', id_last_msg = {int:id_merged_msg}' : '') . '
				WHERE id_board = {int:recycle_board}',
				array(
					'num_topics_inc' => empty($id_recycle_topic) ? 1 : 0,
					'recycle_board' => $modSettings['recycle_board'],
					'id_merged_msg' => $message,
				)
			);

			// Lets increase the num_replies, and the first/last message ID as appropriate.
			if (!empty($id_recycle_topic))
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}topics
					SET num_replies = num_replies + 1' .
						($message > $last_topic_msg ? ', id_last_msg = {int:id_merged_msg}' : '') .
						($message < $first_topic_msg ? ', id_first_msg = {int:id_merged_msg}' : '') . '
					WHERE id_topic = {int:id_recycle_topic}',
					array(
						'id_recycle_topic' => $id_recycle_topic,
						'id_merged_msg' => $message,
					)
				);

			// Make sure this message isn't getting deleted later on.
			$recycle = true;

			// Make sure we update the search subject index.
			updateStats('subject', $topicID, $row['subject']);
		}

		// If it wasn't approved don't keep it in the queue.
		if (!$row['approved'])
			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}approval_queue
				WHERE id_msg = {int:id_msg}
					AND id_attach = {int:id_attach}',
				array(
					'id_msg' => $message,
					'id_attach' => 0,
				)
			);
	}

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}boards
		SET ' . ($row['approved'] ? '
			num_posts = CASE WHEN num_posts = {int:no_posts} THEN 0 ELSE num_posts - 1 END' : '
			unapproved_posts = CASE WHEN unapproved_posts = {int:no_unapproved} THEN 0 ELSE unapproved_posts - 1 END') . '
		WHERE id_board = {int:id_board}',
		array(
			'no_posts' => 0,
			'no_unapproved' => 0,
			'id_board' => $row['id_board'],
		)
	);

	// If the poster was registered and the board this message was on incremented
	// the member's posts when it was posted, decrease his or her post count.
	if (!empty($row['id_member']) && $decreasePostCount && empty($row['count_posts']) && $row['approved'])
		updateMemberData($row['id_member'], array('posts' => '-'));

	// Only remove posts if they're not recycled.
	if (!$recycle)
	{
		// Remove the message!
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}messages
			WHERE id_msg = {int:id_msg}',
			array(
				'id_msg' => $message,
			)
		);

		if (!empty($modSettings['search_custom_index_config']))
		{
			$customIndexSettings = safe_unserialize($modSettings['search_custom_index_config']);
			$words = text2words($row['body'], $customIndexSettings['bytes_per_word'], true);
			if (!empty($words))
				$smcFunc['db_query']('', '
					DELETE FROM {db_prefix}log_search_words
					WHERE id_word IN ({array_int:word_list})
						AND id_msg = {int:id_msg}',
					array(
						'word_list' => $words,
						'id_msg' => $message,
					)
				);
		}

		// Delete attachment(s) if they exist.
		require_once($sourcedir . '/ManageAttachments.php');
		$attachmentQuery = array(
			'attachment_type' => 0,
			'id_msg' => $message,
		);
		removeAttachments($attachmentQuery);
	}

	// Update the pesky statistics.
	updateStats('message');
	updateStats('topic');
	updateSettings(array(
		'calendar_updated' => time(),
	));

	// And now to update the last message of each board we messed with.
	require_once($sourcedir . '/Subs-Post.php');
	if ($recycle)
		updateLastMessages(array($row['id_board'], $modSettings['recycle_board']));
	else
		updateLastMessages($row['id_board']);

	return false;
}

function Gallery_Pixie()
{
	global $txt, $smcFunc, $scripturl, $modSettings, $sourcedir, $context, $gd2, $user_info, $gallerySettings;

	isAllowedTo('smfgallery_edit');



	# Get JSON as a string
	$json_str = file_get_contents('php://input');
	# Get as an array
	$json_obj = json_decode($json_str, true);

	if (isset($json_obj["data"]) && isset($json_obj["id"]))
	{
		$obj = $json_obj["data"];
		$id = (int)$json_obj["id"];

		$dbresult = $smcFunc['db_query']('', "
		SELECT
			id_member, id_cat, user_id_cat, thumbfilename, mediumfilename,
			filename, filesize, ID_TOPIC
		FROM {db_prefix}gallery_pic
		WHERE id_picture = $id LIMIT 1");
		$row = $smcFunc['db_fetch_assoc']($dbresult);

		if (allowedTo('smfgallery_manage') || (allowedTo('smfgallery_edit') && $user_info['id'] == $row['id_member']))
		{
			//get extension
			$pos  = strpos($obj, ';');
			$extension = explode(':', substr($obj, 0, $pos))[1];
			$extension = explode('/', $extension)[1];

			//decode base64 image
			$exploded = explode(',', $obj, 2);
			$encoded = $exploded[1];
			$image = base64_decode($encoded);

			file_put_contents($modSettings['gallery_path'] . $row['filename'] . '.tmp',$image);

			$sizes = @getimagesize($modSettings['gallery_path'] . $row['filename']  . '.tmp');

			// No size, then it's probably not a valid pic.
			if ($sizes === false)
			{
				@unlink($modSettings['gallery_path'] . $row['filename']  . '.tmp');

				return;
			}
				$extensions = array(
					1 => 'gif',
					2 => 'jpeg',
					3 => 'png',
					5 => 'psd',
					6 => 'bmp',
					7 => 'tiff',
					8 => 'tiff',
					9 => 'jpeg',
					14 => 'iff',
					18 => 'webp',
					);
			$extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : 'bmp';
			$extrafolder = '';

			if ($modSettings['gallery_set_enable_multifolder'])
				$extrafolder = $modSettings['gallery_folder_id'] . '/';

			$filename = $user_info['id'] . '-' . date('dmyHis') . '.' . $extension;
			rename($modSettings['gallery_path'] . $row['filename']  . '.tmp',$modSettings['gallery_path'] .  $extrafolder . $filename);
			$filesize = filesize($modSettings['gallery_path'] .  $extrafolder . $filename);


			@unlink($modSettings['gallery_path'] . $row['thumbfilename']);
			@unlink($modSettings['gallery_path'] . $row['mediumfilename']);

			// Create thumbnail
			GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder . $filename,  $modSettings['gallery_thumb_width'],  $modSettings['gallery_thumb_height']);
			rename($modSettings['gallery_path'] . $extrafolder . $filename . '_thumb',  $modSettings['gallery_path'] . $extrafolder . 'thumb_' . $filename);
			$thumbname = 'thumb_' . $filename;
			@chmod($modSettings['gallery_path'] . $extrafolder .  'thumb_' . $filename, 0755);


			// Medium Image
			$mediumimage = '';

			if ($modSettings['gallery_make_medium'])
			{
				GalleryCreateThumbnail($modSettings['gallery_path'] . $extrafolder . $filename, $modSettings['gallery_medium_width'], $modSettings['gallery_medium_height']);
				rename($modSettings['gallery_path'] . $extrafolder . $filename . '_thumb', $modSettings['gallery_path'] . $extrafolder . 'medium_' . $filename);
				$mediumimage = 'medium_' . $filename;
				@chmod($modSettings['gallery_path'] . $extrafolder . 'medium_' . $filename, 0755);

				// Check for Watermark
				DoWaterMark($modSettings['gallery_path'] . $extrafolder . 'medium_' . $filename);
			}

				$smcFunc['db_query']('', "
			UPDATE {db_prefix}gallery_pic
			SET filesize = $filesize, filename = '" . $extrafolder . $filename . "', mediumfilename = '" . $extrafolder . $mediumimage . "', thumbfilename = '" . $extrafolder . $thumbname . "', height = $sizes[1], width = $sizes[0] WHERE id_picture = $id LIMIT 1");

			if ($row['ID_TOPIC'] != 0 && $row['id_cat'] != 0)
			{
				UpdateMessagePost($row['ID_TOPIC'], $id);
			}
			//ob_clean();
			 header('Content-Type: application/json');
			echo json_encode(array('message' => 'success', 'imgFilename' => $modSettings['gallery_path'] .  $extrafolder . $filename));
	//	obExit(false);
		die();

		}



	}
}
?>