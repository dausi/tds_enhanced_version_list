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

namespace Concrete\Package\TdsEnhancedVersionList\Controller\SinglePage\Dashboard\Sitemap;
use Concrete\Core\Http\RequestBase;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Package\TdsEnhancedVersionList\Src\TdsEnhancedVersionList\VersionList;

class TdsEnhancedVersionList extends DashboardPageController  {

    public function on_start() {
        parent::on_start();
        $this->requireAsset('tds_enhanced_version_list');
    }

    public function view() {
        $versionList = new VersionList();

        //Item per page
        $versionList->setItemsPerPage(RequestBase::request('itemPerPage', 20));

        //Filers
        $versionList->filterByPage(RequestBase::request('cID', 0));
        $versionList->filterByUser(RequestBase::request('uID', 0));

        $versionList->sortBy('cvDateCreated', 'DESC');

        $paginationObject = $versionList->getPagination();
        $items = $paginationObject->getCurrentPageResults();

        $this->set('items', $items);
        $this->set('versionList', $versionList);

        $this->requireAsset('tds_enhanced_version_list');
    }

}