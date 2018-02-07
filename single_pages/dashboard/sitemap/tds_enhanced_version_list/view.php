<?php
use Zend\Http\Header\IfUnmodifiedSince;

/**
 * Enhanced Version List
 * Author: Thomas Dausner (aka dausi)
 * © 2017 (enhancements)
 * based on
 *
 * Global Version List
 * Author: Vladimir S. <guyasyou@gmail.com>
 * www.SiteCreate54.ru
 * © 2016
 */
defined('C5_EXECUTE') or die(_("Access Denied."));

/** @var Concrete\Core\Html\Service\Navigation $nh */
$nh = \Core::make('helper/navigation');
/** @var Concrete\Core\Utility\Service\Text $text */
$text = \Core::make('helper/text');
/** @var Concrete\Core\Form\Service\Form $form */
$form = \Core::make('helper/form');
/** @var \Concrete\Core\Form\Service\Widget\PageSelector $page_selector */
$page_selector = \Core::make('helper/form/page_selector');
/** @var \Concrete\Core\Form\Service\Widget\UserSelector $user_selector */
$user_selector = \Core::make('helper/form/user_selector');

?>
<div id="enhanced-version-list-search" class="row">
	<div class="col-xs-12">
		<h3><?=t('Search')?></h3>
		<form
			action="<?=URL::to('/dashboard/sitemap/tds_enhanced_version_list')?>"
			method="get">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
                        <?=$form->label('cID', t('Filter by page'))?>
                        <?=$page_selector->selectPage('cID')?>
                    </div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
                        <?=$form->label('uID', t('Filter by user'))?>
                        <?=$user_selector->selectUser('uID')?>
                    </div>
				</div>
			<div class="col-md-3">
			    <div class="form-group">
				<?=$form->checkbox('recentNotApproved', 1)?>
				<?=$form->label('recentNotApproved', t('Filter recent, but not approved versions'))?>
			    </div>
                	</div>
				<div class="col-md-3">
					<div class="form-group">
                        <?=$form->label('itemPerPage', t('Items per page'))?>
                        <?=$form->number('itemPerPage', 20)?>
                    </div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<a class="btn btn-success"
						href="<?=URL::to('/dashboard/sitemap/tds_enhanced_version_list')?>"><?=t('Clear form')?></a>
					<input class="btn btn-primary" type="submit" name="search"
						value="<?=t('Search')?>" />
				</div>
				<div class="col-md-8">
					<input class="btn btn-primary" id="scanObsolete" type="button"
						value="<?=t('Scan obsolete')?>" />
					<input class="btn btn-primary" id="clearChecked" type="button"
						value="<?=t('Clear checked')?>" />
					<input class="btn btn-danger" id="delChecked" type="button"
						value="<?=t('Delete checked')?>" />
				</div>
			</div>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
<?php

/** @var array $items */
if (count($items)) {
	?>
        <table id="enhanced-version-list"
			class="table table-striped tablesorter">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th><b><?=t('Page name')?><i class="fa fa-sort"></i><i
							class="fa fa-sort-asc"></i><i class="fa fa-sort-desc"></i></b></th>
					<th><b><?=t('cID')?><i class="fa fa-sort"></i><i
							class="fa fa-sort-asc"></i><i class="fa fa-sort-desc"></i></b></th>
					<th><b><?=t('Version')?><i class="fa fa-sort"></i><i
							class="fa fa-sort-asc"></i><i class="fa fa-sort-desc"></i></b></th>
					<th><b><?=t('Create date')?><i class="fa fa-sort"></i><i
							class="fa fa-sort-asc"></i><i class="fa fa-sort-desc"></i></b></th>
					<th><b><?=t('Public date')?><i class="fa fa-sort"></i><i
							class="fa fa-sort-asc"></i><i class="fa fa-sort-desc"></i></b></th>
					<th><b><?=t('Author')?><i class="fa fa-sort"></i><i
							class="fa fa-sort-asc"></i><i class="fa fa-sort-desc"></i></b></th>
					<th><b><?=t('Approver')?><i class="fa fa-sort"></i><i
							class="fa fa-sort-asc"></i><i class="fa fa-sort-desc"></i></b></th>
					<th><b><?=t('Comment')?> <i
							class="cursor-help fa fa-question-circle" data-toggle="tooltip"
							data-placement="top"
							title="<?=t('Hover to read full comment')?>"></i><i
							class="fa fa-sort"></i><i class="fa fa-sort-asc"></i><i
							class="fa fa-sort-desc"></i></b></th>
				</tr>
			</thead>
			<tbody>
	<?php
	foreach ($items as $item) {
		/** @var \Concrete\Core\Page\Collection\Version\Version $item */
		/** @var \Concrete\Core\Page\Page $page */
		$page = Page::getByID($item->getCollectionID());
		$pageName = $page->getCollectionName();
		$pageURL = $nh->getCollectionURL($page);
		$pagePath = $page->getCollectionPath();

		$version = $item->getVersionID();

		$createdDate = $item->getVersionDateCreated();
		$publicDate = $item->cvDatePublic;

		$userNameAuthor = $item->getVersionAuthorUserName();
		$userURLAuthor = \URL::to('dashboard/users/search/view', $item->getVersionAuthorUserID());

		$userNameApprover = $item->getVersionApproverUserName();
		$userURLApprover = \URL::to('dashboard/users/search/view', $item->getVersionApproverUserID());

		$comment = $item->getVersionComments();

		$isApproved = $item->isApproved();
		
		//If recent version, but NOT approved
		$recentVersion = Concrete\Core\Page\Collection\Version\Version::get($page, 'RECENT');
		$notApprovedRecent = !$isApproved && ($recentVersion->getVersionID() == $item->getVersionID());
		
		if ($isApproved) {
		    $rowClass = 'success';
		} else if ($notApprovedRecent) {
		    $rowClass = 'danger';
		} else {
		    $rowClass = 'unapproved';
		}

		if ($version != '')
		{
	?>
                <tr
					class="<?=$rowClass?>">
					<td><input type="checkbox" /></td>
					<td class="data-approved" data-approved="<?=$isApproved?>"><?=$isApproved ? '<i class="fa fa-thumbs-up" title="'.t('Approved').'"></i>' : '<i class="fa fa-thumbs-o-down" title="'.t('Not approved').'"></i>'?></td>
					<td><?=$pageName?> (<a href="<?=$pageURL?>"
						target="_blank"><?=$pagePath?></a>)</td>
					<td class="data-collection"><?=$item->getCollectionID()?></td>
					<td class="data-version"><?=$version?></td>
					<td><?=$createdDate?></td>
					<td><?=$publicDate?></td>
					<td>
                    <?php  if ($userNameAuthor) {?>
                        <a href="<?=$userURLAuthor?>"
						target="_blank"><?=$userNameAuthor?></a>
					</td>
                    <?php  } else { echo 'N/A'; }?>
                    <td>
                    <?php  if ($userNameApprover) {?>
                        <a href="<?=$userURLApprover?>"
						target="_blank"><?=$userNameApprover?></a>
                    <?php  } else { echo 'N/A'; } ?>
                    </td>
					<td class="cursor-help" title="<?=$comment?>"
						data-toggle="tooltip" data-placement="left"><?=$text->shortenTextWord($comment, 30)?></td>
				</tr>
	<?php
		}
	}
	?>
			<tbody>

		</table>

		<div id="dialog-confirm" title="<?=t('Remove obsolete versions')?>" style="display: none;">
			<p class="lead"><?=t('Do you really want to remove obsolete versions?')?></p>
			<p id="announce-versions"></p>
			<p><?=t('Make a backup of the database, if you are not sure what happens here.')?></p>
			<p><?=t('There\'s no chance to restore any removed version!')?></p>
			<div id="progressbar"><div id="progress"><p></p></div></div>
		</div>
    <?php
	/** @var Concrete\Package\TdsEnhancedVersionList\Src\TdsEnhancedVersionList\VersionList $versionList */
	echo $versionList->getPagination()->renderView();
}
else {
	echo t('Versions not found');
}

$tokenParm = Core::make('helper/validation/token')->getParameter('Concrete\Controller\Panel\Page\Versions');

?>
    </div>
</div>
<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			$('#enhanced-version-list').tablesorter({
		        headers: {
		            0: { sorter: false }
		        }
			});

			var collections = [];
			$('#scanObsolete').click(function() {
				collections = [];
				$('tbody tr').each(function() {
					var cID = parseInt($('td.data-collection', this).text().trim());
					if (collections[cID] === undefined)
						collections[cID] = new Array;
					collections[cID].push({
						elem: this,
						approved: $('td.data-approved', this).data('approved'),
						version: parseInt($('td.data-version', this).text().trim())
					});
				});
				for (var cID in collections) {
					var props = collections[cID];
					var approved = 0;
					var latestVersion = 0;
					for (var i = 0; i < props.length; i++) {
						if (props[i].approved)
							approved = props[i].version;
						if (props[i].version > latestVersion)
							latestVersion = props[i].version;
					}
					for (var i = 0; i < props.length; i++) {
						if (!props[i].approved && (props[i].version < approved || props[i].version != latestVersion))
							$('input', props[i].elem).prop('checked', true);
						if (props[i].version > approved)
							$('td.data-approved i.fa', props[i].elem).attr('class', 'fa fa-exclamation-triangle');
					}

				}
			});

			$('#clearChecked').click(function() {
				$('tbody tr').each(function() {
					$('input', this).prop('checked', false);
				});
			});

			var collectionsToConsider = [];
			var versionsToDelete = [];
			var removeVersion = function(vToDelete) {
				for (var cidx = 0; cidx < collectionsToConsider.length; cidx++) {
					var cID = collectionsToConsider[cidx];
					var versions = versionsToDelete[cID];
					var jsonArray = [];
					for (var i = 0; i < versions.length; i++) {
						jsonArray.push({name: 'cvID[]', value: versions[i]});
					}
					versionsToDelete[cID] = jsonArray;
				}
				var cidx = 0;
				var vDeleted = 0;
				var url = CCM_APPLICATION_URL + '/index.php/ccm/system/panels/page/versions/delete?<?=$tokenParm?>';

				var sender = function() {
					$('#progressbar').show();
					var cID = collectionsToConsider[cidx];
					var ptext = '<?=t('Deleting %d version(s) of collection %d...')?>'.replace(/%d/, versionsToDelete[cID].length);
					ptext = ptext.replace(/%d/, cID);
					$('#progress p').text(ptext);
				    $('#progress').css({width: parseInt((vDeleted + versionsToDelete[cID].length) * 100 / vToDelete) + '%'});
					$('body, button').css('cursor', 'progress');
					$.ajax({
						type: 'post',
						dataType: 'json',
						data: versionsToDelete[cID],
						url: url + '&cID=' + cID,
						beforeSubmit: function() {
							jQuery.fn.dialog.showLoader();
						},
						error: function(r) {
							$('body, button').css('cursor', '');
							ConcreteAlert.dialog('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
							$("#dialog-confirm").dialog('close');
					  	},
						success: function(r) {
							vDeleted += versionsToDelete[cID].length;
							if (r.error) {
								$('body, button').css('cursor', '');
								ConcreteAlert.dialog('Error', '<div class="alert alert-danger">' + r.errors.join("<br>") + '</div>');
								$("#dialog-confirm").dialog('close');
							} else {
								if (++cidx < collectionsToConsider.length)
									sender();	// next collection
								else {
									ConcreteAlert.notify({
										message: vDeleted + ' versions of ' + (cidx - 1) + ' collections deleted.'
									});
									$('#enhanced-version-list-search form').submit();
									$('body, button').css('cursor', '');
									$("#dialog-confirm").dialog('close');
								}
							}
						}
					});
				}
				sender();	// initiate first AJAX transfer
			};
			$('#delChecked').click(function() {
				collectionsToConsider = [];
				versionsToDelete = [];
				var ncoll = 0;
				var nvers = 0;
				$('tbody tr').each(function() {
					if ($('input', this).prop('checked')) {
						var cID = $('td.data-collection', this).text().trim();
						var version =  $('td.data-version', this).text().trim();
						if (versionsToDelete[cID] === undefined) {
							versionsToDelete[cID] = [];
							collectionsToConsider.push(cID);
							ncoll++;
						}
						versionsToDelete[cID].push(version);
						nvers++;
					}
				});
				if ($(versionsToDelete).length) {
					var ptext = '<?=t('You are going to deleting %d version(s) of %d collections.')?>'.replace(/%d/, nvers);
					ptext = ptext.replace(/%d/, ncoll);
					$('#announce-versions').text(ptext);
					$('#progressbar').hide();
					$("#dialog-confirm").dialog({
						appendTo: '#enhanced-version-list-search',
						width: 500,
						height: 'auto',
						modal: true,
						buttons: [{
							text: '<?=t('Cancel') ?>',
							'class': 'btn btn-primary',
							click: function() {
								$(this).dialog('close');
							}
						},{
							text: '<?=t('Remove') ?>',
							'class': 'btn btn-danger',
							click: function() {
								removeVersion(nvers);
							}
						}]
					});
				}
			});
		});
	})(window.jQuery);
</script>
