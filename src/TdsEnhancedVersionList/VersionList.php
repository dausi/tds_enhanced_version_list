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

namespace Concrete\Package\TdsEnhancedVersionList\Src\TdsEnhancedVersionList;
use Concrete\Core\Search\ItemList\Database\AttributedItemList as DatabaseItemList;
use Page;
use Concrete\Core\Page\Collection\Version\Version;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Concrete\Core\Search\Pagination\Pagination;

class VersionList extends DatabaseItemList {

    /**
     * Columns in this array can be sorted via the request.
     * @var array
     */
    protected $autoSortColumns = array();

    public function isFulltextSearch() {
        return $this->isFulltextSearch;
    }

    protected function getAttributeKeyClassName() {
        return false;
    }

    public function createQuery() {
        $this->query->select('cv.cID, cv.cvID')
            ->from('CollectionVersions', 'cv');
    }

    /**
     * @param array $queryRow
     * @return Version
     */
    public function getResult($queryRow) {
        $page = Page::getByID($queryRow['cID']);
        $r = Version::get($page, $queryRow['cvID']);
        return $r;
    }

    /**
     * Gets the pagination object for the query.
     * @return Pagination
     */
    protected function createPaginationObject() {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->select('count(cv.cvID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);
        return $pagination;
    }

    /**
     * The total results of the query
     * @return int
     */
    public function getTotalResults() {
        $query = $this->deliverQueryObject();
        return $query->select('count(cv.cvID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    public function filterByPage($cID) {
        if ($cID > 0) {
            $this->query->andWhere('cv.cID = :cID');
            $this->query->setParameter('cID', $cID);
        }
    }

    public function filterByUser($uID) {
        if ($uID > 0) {
            $this->query->andWhere(
                $this->query->expr()->orX(
                    $this->query->expr()->eq('cv.cvAuthorUID', ':uID'),
                    $this->query->expr()->eq('cv.cvApproverUID', ':uID')
                )
            );
            $this->query->setParameter('uID', $uID);
        }
    }
    
    /**
     * Filter new and NOT approved versions
     */
    public function filterRecentNotApproved() {
        $this->query->andWhere('cv.cvIsApproved = :cvIsApproved');
        $this->query->andWhere('cv.cvID = (SELECT MAX(cv2.cvID) FROM CollectionVersions cv2 WHERE cv2.cID = cv.cID)');
        $this->query->setParameter('cvIsApproved', 0);
    }
}
