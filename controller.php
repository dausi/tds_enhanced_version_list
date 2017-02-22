<?php
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

namespace Concrete\Package\TdsEnhancedVersionList;
use \AssetList;
use Package;
use SinglePage;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends Package {

	protected $pkgHandle = 'tds_enhanced_version_list';
	protected $appVersionRequired = '5.7.5.9';
	protected $pkgVersion = '0.9.0';

    public function getPackageName() {
		return t('Enhanced Version List');
	}

	public function getPackageDescription() {
		return t('List of all collection versions enhanced by table sorter and obsolete removal.');
	}

    public function on_start() {

    	$al = AssetList::getInstance();
    	$assets = [
			's' => 'css/style.css',
    		't' => 'css/jquery.tablesorter.css',
    		'g' => 'js/enhanced_version_list.js',
    		'j' => 'js/jquery.tablesorter.js',
    	];
    	$assetTypes = [
    		'c' => 'css',
    		'j' => 'javascript',
    	];
    	$assetGroups = [];
		foreach ($assets as $c => $asset)
		{
			$at = $assetTypes[substr($asset, 0, 1)];
			$al->register($at, 'tds_enhanced_version_list/'.$c, $asset, [], 'tds_enhanced_version_list');
			$assetGroups[] = [$at, 'tds_enhanced_version_list/'.$c];
		}
		$al->registerGroup('tds_enhanced_version_list', $assetGroups);
    }


    public function install() {
        /** @var $pkg \Concrete\Core\Package\Package() */
        $pkg = parent::install();

		//install single pages
        $single_page = SinglePage::add('/dashboard/sitemap/tds_enhanced_version_list', $pkg);
        if ($single_page) {
            $single_page->update(array('cName'=>t('Enhanced list of versions'), 'cDescription'=>t('List of all collection versions enhanced by table sorter and obsolete removal.')));
        }
	}

}
